<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\ObserverInterface;
use MiniOrange\SP\Helper\Exception\SAMLResponseException;
use MiniOrange\SP\Helper\Exception\InvalidSignatureInResponseException;
use MiniOrange\SP\Helper\SPMessages;
use Magento\Framework\Event\Observer;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Actions\AdminLoginAction;
use Magento\Framework\App\Request\Http;
use MiniOrange\SP\Controller\Actions\ReadLogoutRequestAction;
use Magento\Framework\App\RequestInterface;
use MiniOrange\SP\Controller\Actions\ShowTestResultsAction;
use Magento\Store\Model\StoreManagerInterface;
class RedirectToIDPObserver implements ObserverInterface
{
    private $requestParams = array("\x53\x41\115\x4c\122\x65\161\x75\145\163\164", "\x53\x41\x4d\114\122\x65\x73\160\x6f\x6e\163\145", "\x6f\x70\164\151\x6f\x6e");
    private $controllerActionPair = array("\x61\143\x63\x6f\165\156\164" => array("\x6c\x6f\x67\151\x6e", "\143\162\145\x61\x74\x65"), "\x61\x75\164\150" => array("\154\x6f\x67\151\156"));
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
    public function __construct(ManagerInterface $c0, LoggerInterface $hI, ReadResponseAction $Ei, SPUtility $Kx, AdminLoginAction $ZL, Http $MA, ReadLogoutRequestAction $fi, RequestInterface $nD, ShowTestResultsAction $p2, StoreManagerInterface $Wl)
    {
        $this->_storeManager = $Wl;
        $this->messageManager = $c0;
        $this->logger = $hI;
        $this->readResponseAction = $Ei;
        $this->spUtility = $Kx;
        $this->adminLoginAction = $ZL;
        $this->readLogoutRequestAction = $fi;
        $this->currentControllerName = $MA->getControllerName();
        $this->currentActionName = $MA->getActionName();
        $this->request = $nD;
        $this->testAction = $p2;
    }
    public function execute(Observer $ug)
    {
        $n6 = $this->getCurrentWebsite();
        $rJ = $this->getWebsiteIds();
        $gj = $this->spUtility->isBlank($rJ) ? array() : json_decode($rJ);
        if ($this->spUtility->isBlank($gj)) {
            goto Up2;
        }
        foreach ($gj as $zg => $XW) {
            if (!($n6 == $zg)) {
                goto a9r;
            }
            $PT = array_keys($this->request->getParams());
            $dz = array_intersect($PT, $this->requestParams);
            try {
                if (!$this->checkIfUserShouldBeRedirected()) {
                    goto l0a;
                }
                $qz = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
                $ug->getControllerAction()->getResponse()->setRedirect($this->spUtility->getSPInitiatedUrl() . $qz);
                l0a:
            } catch (\Exception $sS) {
                if (!$xi) {
                    goto JzI;
                }
                $this->testAction->setSamlException($sS)->setHasExceptionOccurred(TRUE)->execute();
                JzI:
                $this->messageManager->addErrorMessage($sS->getMessage());
                $this->logger->debug($sS->getMessage());
            }
            a9r:
            HKl:
        }
        Izb:
        Up2:
    }
    private function checkIfUserShouldBeRedirected()
    {
        if (!($this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT) != "\x31" || $this->spUtility->isUserLoggedIn())) {
            goto MJ5;
        }
        return FALSE;
        MJ5:
        if (!($this->spUtility->getStoreConfig(SPConstants::BACKDOOR) == "\61" && !empty($this->request->getParams()[SPConstants::SAML_SSO_FALSE]))) {
            goto Gf4;
        }
        return FALSE;
        Gf4:
        $cb = !empty($this->controllerActionPair[$this->currentControllerName]) ? $this->controllerActionPair[$this->currentControllerName] : NULL;
        return !is_null($cb) && is_array($cb) ? in_array($this->currentActionName, $cb) : FALSE;
    }
    public function getCurrentWebsite()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
    public function getWebsiteIds()
    {
        return $this->spUtility->getStoreConfig(SPConstants::WEBSITE_IDS);
    }
}
