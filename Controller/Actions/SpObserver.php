<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;

/**
 * This is our main Observer class. Observer class are used as a callback
 * function for all of our events and hooks. This particular observer
 * class is being used to check if a SAML request or response was made
 * to the website. If so then read and process it. Every Observer class
 * needs to implement ObserverInterface.
 */
class SpObserver extends Action implements CsrfAwareActionInterface
{
    protected $messageManager;
    protected $logger;
    protected $readResponseAction;
    protected $spUtility;
    protected $adminLoginAction;
    protected $testAction;
    protected $storeManager;
    protected $currentControllerName;
    protected $currentActionName;
    protected $readLogoutRequestAction;
    protected $requestInterface;
    protected $request;
    protected $formkey;
    protected $_pageFactory;
    protected $acsUrl;
    protected $repostSAMLResponseRequest;
    protected $repostSAMLResponsePostData;
    protected $responseFactory;
    protected $baseRelayState;
    private $requestParams = array(
        'SAMLRequest',
        'SAMLResponse',
        'option'
    );
    private $controllerActionPair = array(
        'account' => array('login', 'create'),
        'auth' => array('login'),
    );

    public function __construct(
        ManagerInterface        $messageManager,
        LoggerInterface         $logger,
        Context                 $context,
        ReadResponseAction      $readResponseAction,
        SPUtility               $spUtility,
        AdminLoginAction        $adminLoginAction,
        Http                    $httpRequest,
        ReadLogoutRequestAction $readLogoutRequestAction,
        RequestInterface        $request,
        StoreManagerInterface   $storeManager,
        ShowTestResultsAction   $testAction,
        ResultFactory           $resultFactory,
        PageFactory             $pageFactory,
        FormKey                 $formkey)
    {
        //You can use dependency injection to get any class this observer may need.
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
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->_pageFactory = $pageFactory;

        parent::__construct($context);
        $this->formkey = $formkey;
        $this->getRequest()->setParam('form_key', $this->formkey->getFormKey());
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->responseFactory = $objectManager->get('\Magento\Framework\App\ResponseFactory');

    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * This function is called as soon as the observer class is initialized.
     * Checks if the request parameter has any of the configured request
     * parameters and handles any exception that the system might throw.
     *
     * @param $observer
     */
    public function execute()
    {
        $this->spUtility->log_debug(" inside spObserver : execute: ");
        $keys = array_keys($this->request->getParams());
        $operation = array_intersect($keys, $this->requestParams);
        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("spobserver: ", $idp_name);
        $params = $this->getRequest()->getParams();

        $params = $this->request->getParams(); // get params
        $postData = $this->request->getPost(); // get only post params
        $isTest = true;
        $this->baseRelayState = !empty($params['RelayState']) ? $params['RelayState'] : '';
        $this->baseRelayState = !empty($this->baseRelayState) ? parse_url($this->baseRelayState, PHP_URL_HOST) : '';
        $this->spUtility->log_debug("execute: count-operation: " . count($operation));
        // request has values then it takes priority over others
        if (count($operation) > 0) {
            $this->_route_data(array_values($operation)[0], $params, $postData);
        }
        $this->spUtility->log_debug("SPObserver: execute: stop flow before this. " . $this->baseRelayState);


    }

    /**
     * Route the request data to appropriate functions for processing.
     * Check for any kind of Exception that may occur during processing
     * of form post data. Call the appropriate action.
     *
     * @param $op refers to operation to perform
     * @param $params
     * @param $postData
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    private function _route_data($op, $params, $postData)
    {
        $this->spUtility->log_debug(" _route_data: operation " . $op);
        switch ($op) {
            case $this->requestParams[0]:
                {
                    $this->readLogoutRequestAction->setRequestParam($params)->setPostParam($postData)->execute();
                }
                break;

            case $this->requestParams[1]:
                {
                    if ($params['RelayState'] == SPConstants::TEST_RELAYSTATE) {
                        $this->readResponseAction->setRequestParam($params)->setPostParam($postData)->execute();
                    }
                    $this->checkForMultipleStoreAndProceedAccordingly($params, $postData);
                }
                break;

            case $this->requestParams[2]:
                {
                    if ($params['option'] == SPConstants::LOGIN_ADMIN_OPT)
                        $this->adminLoginAction->execute();
                }
                break;
        }
    }

    private function checkForMultipleStoreAndProceedAccordingly($params, $postData)
    {
        $this->spUtility->log_debug(" inside spObserver.checkForMultipleStoreAndProceedAccordingly(): ");

        if ($this->storeManager->hasSingleStore()) {
            $this->spUtility->log_debug(" checkForMultipleStoreAndProceedAccordingly: Single Store ");
            $this->readResponseAction->setRequestParam($params)
                ->setPostParam($postData)->execute();
        } else {
            $this->spUtility->log_debug(" checkForMultipleStoreAndProceedAccordingly: multistore");

            $get_admin_base_url = $this->spUtility->getAdminBaseUrl();
            $this->spUtility->log_debug(" admin_base_url - " . $get_admin_base_url);

            $currentUrl = $this->spUtility->getCurrentUrl();
            $baseCurrentUrl = parse_url($currentUrl, PHP_URL_HOST);;
            $this->spUtility->log_debug(" currentUrl - " . $currentUrl);
            $this->spUtility->log_debug(" baseCurrentUrl - " . $baseCurrentUrl);

            $this->setParams($params);
            $this->setPostData($postData);

            $samlResponse = $this->repostSAMLResponseRequest['SAMLResponse'];
            $relayState = array_key_exists('RelayState', $this->repostSAMLResponseRequest) ? $this->repostSAMLResponseRequest['RelayState'] : '/';
            $this->spUtility->log_debug(" checkForMultipleStoreAndProceedAccording : relayState - " . $relayState);

            if ($this->spUtility->isBlank($relayState) || $relayState == "/") {
                $this->spUtility->log_debug("checkForMultipleStoreAndProceedAccording : relayState is Blank- ");
                $b2bStoreUrl = $this->spUtility->getStoreConfig(SPConstants::B2B_STORE_URL);
                $baseUrl = "";
                if (!$this->spUtility->isBlank($b2bStoreUrl)) {
                    $this->spUtility->log_debug("checkForMultipleStoreAndProceedAccording : B2b url is set - ");
                    $baseUrl = $this->spUtility->getBaseUrlFromUrl($b2bStoreUrl);
                }
                if ($this->spUtility->isBlank($baseUrl)) {
                    $baseUrl = $this->storeManager->getDefaultStoreView()->getBaseUrl();
                }
                $url = $baseUrl . SPConstants::SUFFIX_SPOBSERVER;
                $relayState = $baseUrl . SPConstants::SUFFIX_ACCOUNT_LOGIN;
                $this->spUtility->log_debug("checkForMultipleStoreAndProceedAccordingly:New RelayState  = " . $relayState);
                $this->repostSAMLResponse($samlResponse, $relayState, $url);
                return;
            }

            $this->baseRelayState = $this->baseRelayState = parse_url($relayState, PHP_URL_HOST);

            if ($this->spUtility->checkIfFlowStartedFromBackend($relayState)) {
                $this->spUtility->log_debug(" checkForMultipleStoreAndProceedAccordingly: admin_url: processing response on: " . $relayState);
                $this->readResponseAction->setRequestParam($params)->setPostParam($postData)->execute();
            } else {
                $this->spUtility->log_debug(" checkForMultipleStoreAndProceedAccordingly: No admin_url in relaystate");
                $isCurrentSameAsRelayState = strpos($relayState, $baseCurrentUrl);
                $storeCode = $this->storeManager->getStore()->getCode();
                $isCurrentStoreSameAsRelayState = false;
                $relayState = str_replace("/index.php", "", $relayState);
                $urlInfo = parse_url($relayState);
                $path = trim($urlInfo['path'], '/');
                $pathParts = explode('/', $path);

                if (count($pathParts) > 0 && $pathParts[0] === $storeCode) {
                    // The first segment of the path matches the store code
                    $isCurrentStoreSameAsRelayState = true;
                } elseif (count($pathParts) > 0) {
                    $storeCodeOfRelayState = $pathParts[0];
                }
                $storeCodeOfRelayState = $storeCodeOfRelayState ?? 1;
                $relayStatestore = $this->storeManager->getStore($storeCodeOfRelayState);
                $relayStateWebsiteId = $relayStatestore->getWebsiteId();
                $this->spUtility->log_debug("WebsiteId of relaystate: " . $relayStateWebsiteId);

                if ($isCurrentSameAsRelayState !== false && $isCurrentStoreSameAsRelayState !== false) {
                    $this->spUtility->log_debug("CurrentUrl same as RelayState. Processing Response.. - " . $currentUrl);
                    $this->readResponseAction->setRequestParam($params)->setPostParam($postData)->execute();
                } else {
                    $this->spUtility->log_debug("CurrentUrl not same as RelayState: CurrentUrl: " . $currentUrl);
                    $this->spUtility->log_debug("CurrentUrl not same as RelayState: RelayState: " . $relayState);

                    $checkIfRelayStateIsMatchingAnySite = $this->spUtility->checkIfRelayStateIsMatchingAnySite($relayStateWebsiteId);

                    if ($checkIfRelayStateIsMatchingAnySite) {
                        $url = $checkIfRelayStateIsMatchingAnySite;
                        $url = $url . SPConstants::SUFFIX_SPOBSERVER;
                        $this->spUtility->log_debug(" checkForMultipleStoreAndProceedAccordingly: posting response on - " . $url);
                        $this->repostSAMLResponse($samlResponse, $relayState, $url);
                        return;
                    }
                }
            }
        }
    }

    private function setParams($request)
    {
        $this->repostSAMLResponseRequest = $request;
        return $this;
    }

    private function setPostData($post)
    {
        $this->repostSAMLResponsePostData = $post;
        return $this;
    }

    private function repostSAMLResponse($samlResponse, $sendRelayState, $ssoUrl)
    {
        $this->spUtility->log_debug(" Re-posting SAMLResponse to ssoUrl - " . $ssoUrl);

        print_r("

                <html>
                    <head>
                        <script src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
                    </head>
                    <body>
                        <form action=\"" . $ssoUrl . "\" method=\"post\" id=\"saml-request-form\" style=\"display:none;\">
                            <input type=\"hidden\" name=\"SAMLResponse\" value=\"" . $samlResponse . "\" />
                            <input type=\"hidden\" name=\"RelayState\" value=\"" . $sendRelayState . "\" />
                        </form>
                        <p>Please wait we are processing your request..</p>

                        <script type=\"text/javascript\">
                                $(function(){document.forms['saml-request-form'].submit();});
                        </script>
                    </body>
                </html>");
        $this->spUtility->log_debug(" Reposted SAMLResponse successfully.");
    }

    /**
     * This function checks if user needs to be redirected to the
     * registered IDP with AUthnRequest. First check if admin has
     * enabled autoRedirect. Then check if user is landing on one of the
     * admin or customer login pages. If both of those are true
     * then return TRUE other return FALSE.
     */
    private function checkIfUserShouldBeRedirected()
    {
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
