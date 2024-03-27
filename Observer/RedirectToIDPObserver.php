<?php

namespace MiniOrange\SP\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Controller\Actions\AdminLoginAction;
use MiniOrange\SP\Controller\Actions\ReadLogoutRequestAction;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Controller\Actions\ShowTestResultsAction;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
use \Magento\Framework\UrlInterface;

class RedirectToIDPObserver implements ObserverInterface
{

    private $requestParams = array(
        'SAMLRequest',
        'SAMLResponse',
        'option'
    );

    private $controllerActionPair = array(
        'account' => array('login', 'create'),
        'auth' => array('login'),
    );

    private $adminControllerActionPair = array (
		'index' => array('login','index'),
		'auth' => array('login'),
    );

    private $messageManager;
    private $logger;
    private $readResponseAction;
    private $spUtility;
    private $adminLoginAction;
    private $testAction;

    private $currentControllerName;
    private $currentActionName;
    private $readLogoutRequestAction;
    private $requestInterface;
    private $request;
    private $storeManager;
    private $_storeManager;
    private $urlBuilder;
    protected $customerSession;

    public function __construct(
        ManagerInterface        $messageManager,
        LoggerInterface         $logger,
        ReadResponseAction      $readResponseAction,
        SPUtility               $spUtility,
        AdminLoginAction        $adminLoginAction,
        Http                    $httpRequest,
        ReadLogoutRequestAction $readLogoutRequestAction,
        RequestInterface        $request,
        ShowTestResultsAction   $testAction,
        StoreManagerInterface   $storeManager,
        UrlInterface $urlBuilder,
        \Magento\Customer\Model\Session $customerSession)
    {
        //You can use dependency injection to get any class this observer may need.
        $this->_storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->readResponseAction = $readResponseAction;
        $this->spUtility = $spUtility;
        $this->adminLoginAction = $adminLoginAction;
        $this->readLogoutRequestAction = $readLogoutRequestAction;
        $this->currentControllerName = $httpRequest->getControllerName();
        $this->currentActionName = $httpRequest->getActionName();
        $this->request = $request;
        $this->testAction = $testAction;
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
    }

    /**
     * This function is called as soon as the observer class is initialized.
     * Checks if the request parameter has any of the configured request
     * parameters and handles any exception that the system might throw.
     *
     * @param $observer
     */
    public function execute(Observer $observer)
    {
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $currentWebsiteId = $this->getCurrentWebsite();
        $websiteIds = $this->getWebsiteIds();
        $selectedWebsites = $this->spUtility->isBlank($websiteIds) ? array() : json_decode($websiteIds);
        if (!$this->spUtility->isBlank($selectedWebsites))
            foreach ($selectedWebsites as $key => $website) {
                if ($currentWebsiteId == $key) {
                    $keys = array_keys($this->request->getParams());
                    $operation = array_intersect($keys, $this->requestParams);
                    try {
                        if ($this->checkIfUserShouldBeRedirected($currentUrl)) {        
                            //redirecting to the loginrequest controller
                            $this->spUtility->log_debug("RedirectToIDPObserver : checkIfUserShouldBeRedirected is true: backdoor not enabled");
                            $autoRedirect_appName = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
                            $observer->getControllerAction()->getResponse()
                                ->setRedirect($this->spUtility->getSPInitiatedUrl() . $autoRedirect_appName);
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                        $this->logger->debug($e->getMessage());
                    }
                }
            }
    }

    public function getCurrentWebsite()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    public function getWebsiteIds()
    {
        return $this->spUtility->getStoreConfig(SPConstants::WEBSITE_IDS);
    }

    /**
     * This function checks if user needs to be redirected to the
     * registered IDP with AUthnRequest. First check if admin has
     * enabled autoRedirect. Then check if user is landing on one of the
     * admin or customer login pages. If both of those are true
     * then return TRUE other return FALSE.
     */
    private function checkIfUserShouldBeRedirected($currentUrl)
    {
        // return false if auto redirect is not enabled
        $isFlowStartedFromBackend = $this->spUtility->checkIfFlowStartedFromBackend($currentUrl);
        if($isFlowStartedFromBackend)
        {
            $this->spUtility->log_debug("RedirectToIDPObserver : Flow started from backend");
            $adminAutoRedirect = $this->spUtility->getStoreConfig(SPConstants::ADMIN_AUTO_REDIRECT);

            $this->spUtility->log_debug("RedirectToIDPObserver : is Admin Autoredirect enabled? ",$adminAutoRedirect);
            if ($this->spUtility->isUserLoggedIn()) return FALSE;
            // check if backdoor is enabled and samlsso=false
            $params = $this->request->getParams();
            $backdoor = isset($params['backdoor']) ? true : false;
            $adminAction = array_key_exists($this->currentControllerName,$this->adminControllerActionPair)
            ? $this->adminControllerActionPair[$this->currentControllerName] : array();
            $backdoorInSession = $this->customerSession->getData('backdoor');
            $backdoorInSession = isset($backdoorInSession) ? $backdoorInSession : 'false';
            if($isFlowStartedFromBackend && !isset($params['backdoor']) && $backdoorInSession != 'true')
            {
                $this->spUtility->log_debug("SP Observer: From admin page:");
                return $adminAutoRedirect == true ? in_array($this->currentActionName,$adminAction) : false;
            }
            else
            {
                $backdoorInSession = $this->customerSession->getData('backdoor');
                if($backdoorInSession == 'true')
                {
                    $this->customerSession->setData('backdoor', 'false');
                }
                else
                {
                    $this->customerSession->setData('backdoor', 'true');
                }

                $backdoorInSession = $this->customerSession->getData('backdoor');
                $this->spUtility->log_debug("SP Observer:Flow started from backdoor Url: updated backdoorInSession: ",$backdoorInSession);
            }
            return false;
        }
        else
        {
            $this->spUtility->log_debug("RedirectToIDPObserver : Flow started from Frontend");
            // return false if auto redirect is not enabled
            if ($this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT) != "1"
            || $this->spUtility->isUserLoggedIn()) return FALSE;
            // check if backdoor is enabled and samlsso=false
            if ($this->spUtility->getStoreConfig(SPConstants::BACKDOOR) == "1"
                && !empty($this->request->getParams()[SPConstants::SAML_SSO_FALSE])) return FALSE;
            // now check if user is landing on one of the login pages
            $action = !empty($this->controllerActionPair[$this->currentControllerName])
                ? $this->controllerActionPair[$this->currentControllerName] : NULL;
            return !is_null($action) && is_array($action) ? in_array($this->currentActionName, $action) : FALSE;
        }
    }

}
