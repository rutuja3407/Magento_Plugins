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
class RedirectToIDPObserver implements ObserverInterface
{
    private $requestParams = array("\123\x41\115\114\122\145\161\x75\145\163\x74", "\x53\101\x4d\114\x52\x65\x73\x70\157\x6e\163\x65", "\157\x70\164\151\157\156");
    private $controllerActionPair = array("\x61\143\143\157\165\156\x74" => array("\154\x6f\x67\151\156", "\x63\162\x65\x61\x74\x65"), "\141\x75\x74\150" => array("\154\157\147\x69\156"));
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
    public function __construct(ManagerInterface $b_, LoggerInterface $kU, ReadResponseAction $kn, SPUtility $fR, AdminLoginAction $oS, Http $Q0, ReadLogoutRequestAction $Jn, RequestInterface $E1, ShowTestResultsAction $Ip, StoreManagerInterface $VO)
    {
        $this->_storeManager = $VO;
        $this->messageManager = $b_;
        $this->logger = $kU;
        $this->readResponseAction = $kn;
        $this->spUtility = $fR;
        $this->adminLoginAction = $oS;
        $this->readLogoutRequestAction = $Jn;
        $this->currentControllerName = $Q0->getControllerName();
        $this->currentActionName = $Q0->getActionName();
        $this->request = $E1;
        $this->testAction = $Ip;
    }
    public function execute(Observer $UJ)
    {
        $u9 = $this->getCurrentWebsite();
        $hq = $this->getWebsiteIds();
        $L_ = $this->spUtility->isBlank($hq) ? array() : json_decode($hq);
        if ($this->spUtility->isBlank($L_)) {
            goto aZ;
        }
        foreach ($L_ as $On => $Kt) {
            if (!($u9 == $On)) {
                goto lE;
            }
            $pb = array_keys($this->request->getParams());
            $F6 = array_intersect($pb, $this->requestParams);
            try {
                if (!$this->checkIfUserShouldBeRedirected()) {
                    goto AU;
                }
                $ZJ = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
                $UJ->getControllerAction()->getResponse()->setRedirect($this->spUtility->getSPInitiatedUrl() . $ZJ);
                AU:
            } catch (\Exception $IR) {
                $this->messageManager->addErrorMessage($IR->getMessage());
                $this->logger->debug($IR->getMessage());
            }
            lE:
            CF:
        }
        ol:
        aZ:
    }
    public function getCurrentWebsite()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
    public function getWebsiteIds()
    {
        return $this->spUtility->getStoreConfig(SPConstants::WEBSITE_IDS);
    }
    private function checkIfUserShouldBeRedirected()
    {
        if (!($this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT) != "\x31" || $this->spUtility->isUserLoggedIn())) {
            goto Ww;
        }
        return FALSE;
        Ww:
        if (!($this->spUtility->getStoreConfig(SPConstants::BACKDOOR) == "\x31" && !empty($this->request->getParams()[SPConstants::SAML_SSO_FALSE]))) {
            goto C7;
        }
        return FALSE;
        C7:
        $JS = !empty($this->controllerActionPair[$this->currentControllerName]) ? $this->controllerActionPair[$this->currentControllerName] : NULL;
        return !is_null($JS) && is_array($JS) ? in_array($this->currentActionName, $JS) : FALSE;
    }
}
