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
    private $requestParams = array("\x53\101\115\x4c\x52\x65\161\165\145\163\x74", "\x53\x41\x4d\x4c\x52\x65\163\160\x6f\156\163\145", "\157\160\164\151\157\156");
    private $controllerActionPair = array("\x61\143\x63\157\x75\156\164" => array("\154\x6f\147\x69\x6e", "\143\x72\145\141\x74\x65"), "\x61\165\x74\150" => array("\x6c\157\147\x69\x6e"));
    public function __construct(ManagerInterface $b_, LoggerInterface $kU, Context $gt, ReadResponseAction $kn, SPUtility $fR, AdminLoginAction $oS, Http $Q0, ReadLogoutRequestAction $Jn, RequestInterface $E1, StoreManagerInterface $VO, ShowTestResultsAction $Ip, ResultFactory $ps, PageFactory $Wl, FormKey $MW)
    {
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
        $this->storeManager = $VO;
        $this->resultFactory = $ps;
        $this->_pageFactory = $Wl;
        parent::__construct($gt);
        $this->formkey = $MW;
        $this->getRequest()->setParam("\x66\157\x72\x6d\x5f\x6b\x65\171", $this->formkey->getFormKey());
        $F_ = \Magento\Framework\App\ObjectManager::getInstance();
        $this->responseFactory = $F_->get("\134\x4d\x61\x67\145\156\x74\x6f\x5c\x46\x72\x61\x6d\145\x77\x6f\x72\153\x5c\101\x70\x70\x5c\x52\145\x73\x70\157\156\x73\145\x46\141\x63\164\157\162\171");
    }
    public function createCsrfValidationException(RequestInterface $E1) : ?InvalidRequestException
    {
        return null;
    }
    public function validateForCsrf(RequestInterface $E1) : ?bool
    {
        return true;
    }
    public function execute()
    {
        $this->spUtility->log_debug("\40\x69\156\x73\x69\x64\145\40\163\x70\117\142\x73\145\162\x76\x65\x72\x20\x3a\40\145\x78\x65\143\165\164\145\x3a\x20");
        $pb = array_keys($this->request->getParams());
        $F6 = array_intersect($pb, $this->requestParams);
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\163\x70\157\142\163\145\162\166\x65\x72\x3a\x20", $rq);
        $Te = $this->getRequest()->getParams();
        $Te = $this->request->getParams();
        $wr = $this->request->getPost();
        $a8 = true;
        $this->baseRelayState = !empty($Te["\x52\145\x6c\141\171\x53\x74\141\164\x65"]) ? $Te["\122\145\154\141\171\x53\164\141\x74\x65"] : '';
        $this->baseRelayState = !empty($this->baseRelayState) ? parse_url($this->baseRelayState, PHP_URL_HOST) : '';
        $this->spUtility->log_debug("\x65\170\x65\x63\165\164\145\x3a\40\143\x6f\x75\x6e\x74\55\x6f\x70\x65\162\x61\164\151\x6f\x6e\x3a\x20" . count($F6));
        if (!(count($F6) > 0)) {
            goto Qo;
        }
        $this->_route_data(array_values($F6)[0], $Te, $wr);
        Qo:
        $this->spUtility->log_debug("\x53\x50\x4f\x62\163\145\x72\x76\145\x72\x3a\40\145\170\x65\x63\x75\164\x65\x3a\40\x73\x74\x6f\x70\x20\146\154\157\167\x20\142\x65\146\x6f\162\145\x20\x74\x68\x69\x73\x2e\40" . $this->baseRelayState);
    }
    private function _route_data($a2, $Te, $wr)
    {
        $this->spUtility->log_debug("\x20\x5f\x72\157\x75\x74\x65\137\x64\141\164\x61\x3a\40\x6f\160\x65\x72\141\x74\x69\x6f\156\40" . $a2);
        switch ($a2) {
            case $this->requestParams[0]:
                $this->readLogoutRequestAction->setRequestParam($Te)->setPostParam($wr)->execute();
                goto kE;
            case $this->requestParams[1]:
                if (!($Te["\x52\x65\x6c\141\171\x53\x74\141\164\x65"] == SPConstants::TEST_RELAYSTATE)) {
                    goto Vj;
                }
                $this->readResponseAction->setRequestParam($Te)->setPostParam($wr)->execute();
                Vj:
                $this->checkForMultipleStoreAndProceedAccordingly($Te, $wr);
                goto kE;
            case $this->requestParams[2]:
                if (!($Te["\x6f\160\x74\x69\x6f\x6e"] == SPConstants::LOGIN_ADMIN_OPT)) {
                    goto Yn;
                }
                $this->adminLoginAction->execute();
                Yn:
                goto kE;
        }
        mc:
        kE:
    }
    private function checkForMultipleStoreAndProceedAccordingly($Te, $wr)
    {
        $this->spUtility->log_debug("\x20\151\x6e\x73\x69\144\x65\x20\x73\x70\x4f\142\x73\145\x72\x76\145\x72\56\143\150\x65\143\153\106\157\162\115\x75\x6c\x74\x69\160\154\x65\123\x74\157\x72\x65\101\x6e\144\x50\x72\x6f\143\x65\x65\x64\x41\143\x63\x6f\x72\x64\x69\156\x67\154\171\50\x29\x3a\40");
        if ($this->storeManager->hasSingleStore()) {
            goto hV;
        }
        $this->spUtility->log_debug("\x20\x63\x68\x65\143\153\106\157\162\115\165\154\x74\151\160\154\x65\x53\164\x6f\162\x65\101\156\144\120\x72\157\x63\145\x65\144\x41\x63\143\157\x72\144\x69\156\147\154\171\72\40\x6d\x75\154\x74\151\x73\164\157\162\x65");
        $yi = $this->spUtility->getAdminBaseUrl();
        $this->spUtility->log_debug("\40\x61\144\155\151\x6e\137\142\141\163\145\x5f\165\x72\x6c\40\55\x20" . $yi);
        $RZ = $this->spUtility->getCurrentUrl();
        $EB = parse_url($RZ, PHP_URL_HOST);
        $this->spUtility->log_debug("\40\x63\x75\x72\x72\145\x6e\x74\125\x72\154\40\55\40" . $RZ);
        $this->spUtility->log_debug("\40\x62\x61\x73\145\x43\165\162\x72\x65\156\164\125\x72\x6c\x20\55\x20" . $EB);
        $this->setParams($Te);
        $this->setPostData($wr);
        $yO = $this->repostSAMLResponseRequest["\123\101\x4d\114\122\x65\x73\160\x6f\156\x73\145"];
        $Nf = array_key_exists("\122\145\x6c\x61\x79\x53\164\141\164\x65", $this->repostSAMLResponseRequest) ? $this->repostSAMLResponseRequest["\x52\x65\x6c\x61\x79\123\164\x61\x74\x65"] : "\x2f";
        $this->spUtility->log_debug("\40\143\150\145\x63\153\106\157\162\115\165\x6c\x74\151\x70\154\145\x53\x74\157\162\145\x41\156\x64\x50\162\x6f\143\145\145\x64\101\x63\143\x6f\162\x64\x69\x6e\x67\40\72\40\x72\145\x6c\141\171\x53\x74\141\164\x65\40\55\40" . $Nf);
        if (!($this->spUtility->isBlank($Nf) || $Nf == "\57")) {
            goto Cx;
        }
        $this->spUtility->log_debug("\x63\150\145\x63\x6b\x46\157\x72\x4d\x75\154\x74\151\x70\154\145\x53\x74\157\162\x65\101\x6e\144\120\x72\157\x63\145\145\144\101\x63\143\157\x72\144\x69\x6e\147\40\72\40\162\145\154\141\171\123\x74\141\x74\x65\40\151\163\x20\102\154\x61\x6e\x6b\x2d\40");
        $XC = $this->spUtility->getStoreConfig(SPConstants::B2B_STORE_URL);
        $sA = '';
        if ($this->spUtility->isBlank($XC)) {
            goto Ey;
        }
        $this->spUtility->log_debug("\x63\150\x65\x63\153\106\x6f\162\x4d\165\x6c\164\151\x70\x6c\145\123\x74\x6f\162\x65\101\x6e\144\x50\x72\157\143\x65\x65\x64\101\x63\143\157\x72\144\151\x6e\147\x20\x3a\x20\102\x32\142\x20\165\x72\154\40\151\x73\x20\x73\145\x74\40\x2d\40");
        $sA = $this->spUtility->getBaseUrlFromUrl($XC);
        Ey:
        if (!$this->spUtility->isBlank($sA)) {
            goto j1;
        }
        $sA = $this->storeManager->getDefaultStoreView()->getBaseUrl();
        j1:
        $At = $sA . SPConstants::SUFFIX_SPOBSERVER;
        $Nf = $sA . SPConstants::SUFFIX_ACCOUNT_LOGIN;
        $this->spUtility->log_debug("\143\150\145\x63\x6b\x46\157\162\x4d\x75\x6c\164\x69\160\x6c\145\123\164\x6f\x72\x65\x41\156\x64\x50\162\x6f\143\145\145\x64\101\x63\143\x6f\x72\144\151\x6e\147\x6c\x79\x3a\x4e\x65\x77\x20\x52\x65\154\141\171\x53\164\x61\164\x65\40\40\x3d\x20" . $Nf);
        $this->repostSAMLResponse($yO, $Nf, $At);
        return;
        Cx:
        $this->baseRelayState = $this->baseRelayState = parse_url($Nf, PHP_URL_HOST);
        if ($this->spUtility->checkIfFlowStartedFromBackend($Nf)) {
            goto y0;
        }
        $this->spUtility->log_debug("\x20\x63\x68\145\143\153\106\x6f\x72\x4d\x75\x6c\164\x69\160\x6c\145\123\164\x6f\x72\145\x41\x6e\144\x50\162\157\143\x65\x65\144\x41\x63\x63\x6f\162\144\151\156\x67\154\x79\x3a\40\x4e\157\x20\x61\x64\155\151\x6e\137\165\x72\154\40\151\156\40\162\x65\154\x61\x79\x73\x74\141\164\x65");
        $hY = strpos($Nf, $EB);
        $sI = $this->storeManager->getStore()->getCode();
        $vK = false;
        $Nf = str_replace("\57\x69\x6e\x64\x65\170\56\160\150\160", '', $Nf);
        $UZ = parse_url($Nf);
        $Wg = trim($UZ["\x70\141\x74\x68"], "\x2f");
        $Ho = explode("\x2f", $Wg);
        if (count($Ho) > 0 && $Ho[0] === $sI) {
            goto eG;
        }
        if (count($Ho) > 0) {
            goto j3;
        }
        goto I2;
        eG:
        $vK = true;
        goto I2;
        j3:
        $xY = $Ho[0];
        I2:
        $xY = $xY ?? 1;
        $VU = $this->storeManager->getStore($xY);
        $iI = $VU->getWebsiteId();
        $this->spUtility->log_debug("\x57\145\x62\163\151\164\145\x49\x64\x20\x6f\146\x20\162\145\x6c\141\171\163\164\x61\164\145\72\x20" . $iI);
        if ($hY !== false && $vK !== false) {
            goto gN;
        }
        $this->spUtility->log_debug("\103\x75\162\x72\x65\156\164\x55\162\x6c\x20\156\157\164\40\x73\141\155\x65\x20\x61\x73\40\122\145\x6c\141\171\123\x74\141\x74\145\x3a\x20\103\165\162\x72\x65\156\164\125\x72\154\72\40" . $RZ);
        $this->spUtility->log_debug("\103\165\162\x72\x65\x6e\164\x55\162\154\40\x6e\157\164\40\x73\x61\155\x65\x20\141\x73\x20\122\145\x6c\x61\x79\123\x74\141\164\x65\x3a\40\x52\x65\x6c\141\x79\123\x74\141\164\x65\72\x20" . $Nf);
        $BN = $this->spUtility->checkIfRelayStateIsMatchingAnySite($iI);
        if (!$BN) {
            goto MC;
        }
        $At = $BN;
        $At = $At . SPConstants::SUFFIX_SPOBSERVER;
        $this->spUtility->log_debug("\x20\x63\x68\x65\143\153\x46\x6f\x72\115\x75\x6c\164\151\160\154\x65\123\x74\x6f\x72\x65\x41\156\x64\x50\162\157\x63\145\145\144\x41\143\x63\x6f\x72\144\x69\156\x67\154\171\72\x20\160\157\x73\x74\151\156\147\x20\x72\x65\163\x70\x6f\156\x73\145\40\x6f\x6e\40\x2d\x20" . $At);
        $this->repostSAMLResponse($yO, $Nf, $At);
        return;
        MC:
        goto CK;
        gN:
        $this->spUtility->log_debug("\103\165\162\162\x65\x6e\164\125\x72\x6c\40\163\141\x6d\x65\x20\x61\x73\x20\x52\145\x6c\x61\171\x53\164\141\x74\145\x2e\40\x50\x72\x6f\x63\x65\163\x73\151\156\147\x20\122\145\163\x70\x6f\x6e\x73\x65\56\x2e\x20\x2d\x20" . $RZ);
        $this->readResponseAction->setRequestParam($Te)->setPostParam($wr)->execute();
        CK:
        goto V1;
        y0:
        $this->spUtility->log_debug("\40\x63\x68\145\x63\153\106\x6f\x72\115\x75\154\164\151\160\154\x65\123\164\157\162\x65\101\156\x64\x50\162\x6f\x63\x65\145\x64\101\x63\x63\x6f\162\x64\x69\x6e\x67\x6c\x79\x3a\x20\x61\x64\155\x69\156\137\165\x72\x6c\x3a\40\160\162\157\x63\x65\163\163\x69\156\147\x20\x72\145\163\160\x6f\x6e\x73\145\40\x6f\x6e\72\40" . $Nf);
        $this->readResponseAction->setRequestParam($Te)->setPostParam($wr)->execute();
        V1:
        goto WE;
        hV:
        $this->spUtility->log_debug("\x20\x63\x68\145\x63\x6b\x46\x6f\x72\x4d\x75\x6c\x74\151\160\x6c\x65\x53\x74\157\x72\145\x41\156\x64\120\x72\x6f\143\145\x65\144\101\x63\143\x6f\x72\144\x69\x6e\x67\154\x79\x3a\x20\123\x69\156\x67\154\145\40\x53\x74\x6f\x72\x65\x20");
        $this->readResponseAction->setRequestParam($Te)->setPostParam($wr)->execute();
        WE:
    }
    private function setParams($E1)
    {
        $this->repostSAMLResponseRequest = $E1;
        return $this;
    }
    private function setPostData($post)
    {
        $this->repostSAMLResponsePostData = $post;
        return $this;
    }
    private function repostSAMLResponse($yO, $eA, $s9)
    {
        $this->spUtility->log_debug("\x20\122\145\x2d\x70\x6f\x73\x74\151\x6e\147\x20\123\x41\x4d\114\122\145\163\x70\x6f\156\163\x65\40\x74\x6f\x20\163\163\157\x55\x72\154\40\55\40" . $s9);
        print_r("\xd\xa\xd\xa\40\40\40\x20\40\40\x20\x20\x20\40\40\x20\x20\x20\x20\40\74\x68\164\x6d\x6c\76\15\xa\x20\x20\x20\x20\x20\40\x20\x20\x20\x20\x20\40\40\x20\40\x20\x20\40\40\40\74\150\x65\x61\144\76\15\12\x20\40\x20\40\x20\x20\x20\x20\40\40\40\x20\x20\x20\40\x20\40\40\x20\x20\x20\40\x20\x20\74\163\x63\x72\151\x70\x74\x20\x73\162\143\x3d\x27\x68\164\164\160\163\72\x2f\57\x63\x6f\x64\x65\x2e\x6a\161\165\x65\x72\x79\56\x63\157\155\57\152\x71\165\x65\162\x79\55\x31\x2e\x31\x31\x2e\63\56\155\x69\156\56\152\x73\47\x3e\x3c\57\x73\x63\162\x69\x70\164\76\xd\12\x20\x20\40\x20\40\40\40\40\x20\40\40\x20\40\40\40\x20\x20\x20\x20\40\74\x2f\150\x65\x61\x64\x3e\xd\xa\40\40\40\x20\x20\x20\x20\40\40\x20\x20\40\x20\40\40\x20\x20\40\x20\x20\74\142\x6f\144\171\x3e\xd\12\x20\x20\x20\x20\40\40\x20\40\40\40\40\40\40\40\40\40\x20\40\x20\40\x20\40\40\40\74\146\157\162\155\40\141\x63\x74\151\157\156\75\x22" . $s9 . "\x22\40\155\x65\164\150\x6f\x64\x3d\42\x70\157\163\x74\42\x20\x69\144\75\42\x73\141\x6d\154\x2d\x72\145\x71\165\145\x73\x74\x2d\x66\157\x72\155\42\x20\163\x74\x79\154\x65\75\x22\144\151\x73\x70\x6c\x61\171\x3a\x6e\157\156\145\73\42\76\xd\xa\40\40\x20\x20\40\40\x20\x20\x20\40\40\x20\40\x20\x20\x20\40\x20\x20\40\40\x20\x20\40\x20\40\x20\x20\74\151\156\160\x75\164\x20\164\171\x70\145\x3d\42\x68\151\144\x64\145\x6e\x22\x20\156\141\155\x65\x3d\x22\123\x41\115\114\x52\145\x73\160\x6f\156\x73\145\42\x20\166\141\154\165\145\x3d\x22" . $yO . "\x22\40\57\76\15\12\x20\40\x20\x20\x20\x20\40\40\40\40\40\40\x20\x20\40\40\40\x20\x20\40\x20\40\x20\40\x20\40\x20\40\74\151\156\x70\165\x74\40\164\171\160\x65\x3d\42\150\151\x64\144\x65\156\x22\x20\x6e\x61\x6d\145\x3d\x22\122\x65\x6c\x61\171\x53\164\141\x74\x65\x22\40\x76\141\154\165\x65\75\x22" . $eA . "\x22\x20\x2f\76\xd\12\40\40\40\40\40\x20\x20\40\x20\40\40\x20\x20\x20\x20\x20\x20\40\x20\40\40\x20\x20\x20\x3c\57\146\157\162\155\76\15\xa\40\40\x20\x20\40\40\x20\x20\x20\40\40\x20\x20\x20\40\x20\x20\x20\x20\x20\40\x20\40\40\74\160\x3e\120\154\x65\141\163\145\40\167\141\x69\164\40\167\x65\x20\141\162\x65\x20\x70\x72\157\143\x65\163\x73\151\156\147\40\x79\x6f\x75\x72\40\x72\x65\x71\x75\x65\x73\164\56\56\x3c\x2f\x70\x3e\xd\12\15\12\40\x20\x20\40\x20\40\x20\x20\x20\x20\x20\x20\40\40\40\40\x20\x20\x20\40\x20\40\x20\x20\x3c\x73\143\162\x69\x70\x74\40\164\x79\x70\x65\75\x22\164\x65\170\x74\57\x6a\141\166\x61\x73\143\x72\x69\x70\164\42\76\15\12\x20\x20\x20\x20\x20\40\40\40\40\40\40\40\x20\x20\x20\x20\x20\x20\40\x20\x20\40\40\40\40\x20\x20\40\x20\x20\40\40\44\50\146\x75\156\x63\164\x69\157\156\50\51\173\144\x6f\x63\x75\x6d\x65\156\164\x2e\x66\157\162\x6d\163\133\47\163\141\155\154\55\162\145\x71\x75\145\163\164\x2d\x66\x6f\162\x6d\47\x5d\56\x73\165\x62\155\151\164\x28\x29\x3b\175\51\x3b\xd\xa\40\x20\40\x20\40\x20\x20\x20\x20\x20\x20\40\40\40\x20\40\40\40\40\x20\40\40\40\x20\74\57\x73\x63\x72\x69\x70\x74\x3e\xd\xa\40\40\40\x20\40\40\x20\x20\x20\x20\40\40\40\x20\40\x20\x20\x20\x20\40\x3c\57\142\x6f\x64\x79\x3e\15\xa\40\40\40\40\40\x20\40\40\x20\40\x20\40\40\x20\40\40\x3c\57\x68\x74\155\154\x3e");
        $this->spUtility->log_debug("\x20\122\145\x70\x6f\x73\164\x65\144\40\x53\x41\x4d\114\x52\x65\163\160\x6f\156\163\145\x20\x73\165\x63\143\145\x73\163\x66\165\154\x6c\171\56");
    }
    private function checkIfUserShouldBeRedirected()
    {
        if (!($this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT) != "\x31" || $this->spUtility->isUserLoggedIn())) {
            goto Uq;
        }
        return FALSE;
        Uq:
        if (!($this->spUtility->getStoreConfig(SPConstants::BACKDOOR) == "\61" && !empty($this->request->getParams()[SPConstants::SAML_SSO_FALSE]))) {
            goto VT;
        }
        return FALSE;
        VT:
        $JS = !empty($this->controllerActionPair[$this->currentControllerName]) ? $this->controllerActionPair[$this->currentControllerName] : NULL;
        return !is_null($JS) && is_array($JS) ? in_array($this->currentActionName, $JS) : FALSE;
    }
}
