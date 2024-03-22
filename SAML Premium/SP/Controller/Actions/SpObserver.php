<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\App\Action\Action;
use MiniOrange\SP\Helper\Exception\SAMLResponseException;
use MiniOrange\SP\Helper\Exception\InvalidSignatureInResponseException;
use MiniOrange\SP\Helper\SPMessages;
use Magento\Framework\Event\Observer;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use MiniOrange\SP\Helper\Saml2\SAML2Assertion;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Actions\AdminLoginAction;
use Magento\Framework\App\Request\Http;
use MiniOrange\SP\Controller\Actions\ReadLogoutRequestAction;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Controller\Actions\ShowTestResultsAction;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Data\Form\FormKey;
class SpObserver extends Action implements CsrfAwareActionInterface
{
    private $requestParams = array("\x53\101\115\114\x52\x65\161\165\x65\163\x74", "\x53\x41\x4d\114\122\145\x73\x70\x6f\x6e\x73\x65", "\x6f\x70\164\x69\x6f\x6e");
    private $controllerActionPair = array("\141\x63\x63\x6f\165\x6e\x74" => array("\x6c\x6f\x67\x69\x6e", "\143\162\145\x61\164\145"), "\x61\x75\164\150" => array("\154\157\x67\x69\156"));
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
    public function __construct(ManagerInterface $c0, LoggerInterface $hI, Context $Gc, ReadResponseAction $Ei, SPUtility $Kx, AdminLoginAction $ZL, Http $MA, ReadLogoutRequestAction $fi, RequestInterface $nD, StoreManagerInterface $Wl, ShowTestResultsAction $p2, ResultFactory $UZ, PageFactory $lh, FormKey $ca)
    {
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
        $this->storeManager = $Wl;
        $this->resultFactory = $UZ;
        $this->_pageFactory = $lh;
        parent::__construct($Gc);
        $this->formkey = $ca;
        $this->getRequest()->setParam("\146\157\x72\155\x5f\x6b\145\x79", $this->formkey->getFormKey());
        $c5 = \Magento\Framework\App\ObjectManager::getInstance();
        $this->responseFactory = $c5->get("\134\115\141\147\x65\156\164\157\x5c\x46\x72\x61\155\x65\x77\157\162\153\x5c\101\x70\160\134\122\x65\163\x70\x6f\156\163\x65\106\x61\143\x74\x6f\162\x79");
    }
    public function createCsrfValidationException(RequestInterface $nD) : ?InvalidRequestException
    {
        return null;
    }
    public function validateForCsrf(RequestInterface $nD) : ?bool
    {
        return true;
    }
    public function execute()
    {
        $this->spUtility->log_debug("\40\x69\156\163\x69\144\x65\x20\x73\x70\x4f\142\x73\145\x72\166\x65\162\x20\72\40\x65\170\x65\143\x75\x74\145\72\40");
        $PT = array_keys($this->request->getParams());
        $dz = array_intersect($PT, $this->requestParams);
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\163\160\157\142\163\x65\162\166\x65\x72\72\x20", $rQ);
        $As = $this->getRequest()->getParams();
        $As = $this->request->getParams();
        $FB = $this->request->getPost();
        $xi = true;
        $this->baseRelayState = !empty($As["\122\145\154\x61\171\123\164\141\164\x65"]) ? $As["\122\145\x6c\141\x79\x53\164\141\164\145"] : '';
        $this->baseRelayState = !empty($this->baseRelayState) ? parse_url($this->baseRelayState, PHP_URL_HOST) : '';
        $this->spUtility->log_debug("\x65\170\145\x63\165\164\145\72\x20\x63\x6f\x75\156\x74\55\157\160\x65\162\141\164\151\157\156\x3a\x20" . count($dz));
        if (!(count($dz) > 0)) {
            goto Ax;
        }
        $this->_route_data(array_values($dz)[0], $As, $FB);
        Ax:
        $this->spUtility->log_debug("\123\120\117\x62\163\x65\162\166\x65\x72\x3a\40\x65\x78\x65\143\165\164\145\x3a\x20\x73\164\157\x70\40\146\x6c\x6f\167\x20\x62\145\x66\157\x72\145\40\164\x68\x69\163\56\x20" . $this->baseRelayState);
    }
    private function checkIfUserShouldBeRedirected()
    {
        if (!($this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT) != "\x31" || $this->spUtility->isUserLoggedIn())) {
            goto xg;
        }
        return FALSE;
        xg:
        if (!($this->spUtility->getStoreConfig(SPConstants::BACKDOOR) == "\61" && !empty($this->request->getParams()[SPConstants::SAML_SSO_FALSE]))) {
            goto SF;
        }
        return FALSE;
        SF:
        $cb = !empty($this->controllerActionPair[$this->currentControllerName]) ? $this->controllerActionPair[$this->currentControllerName] : NULL;
        return !is_null($cb) && is_array($cb) ? in_array($this->currentActionName, $cb) : FALSE;
    }
    private function _route_data($NR, $As, $FB)
    {
        $this->spUtility->log_debug("\40\137\x72\157\165\164\x65\x5f\144\x61\164\141\x3a\40\x6f\160\x65\x72\141\164\x69\157\x6e\40" . $NR);
        switch ($NR) {
            case $this->requestParams[0]:
                $this->readLogoutRequestAction->setRequestParam($As)->setPostParam($FB)->execute();
                goto Av;
            case $this->requestParams[1]:
                if (!($As["\x52\145\154\141\x79\x53\x74\x61\x74\x65"] == SPConstants::TEST_RELAYSTATE)) {
                    goto cn;
                }
                $this->readResponseAction->setRequestParam($As)->setPostParam($FB)->execute();
                cn:
                $this->checkForMultipleStoreAndProceedAccordingly($As, $FB);
                goto Av;
            case $this->requestParams[2]:
                if (!($As["\x6f\x70\164\151\157\156"] == SPConstants::LOGIN_ADMIN_OPT)) {
                    goto NK;
                }
                $this->adminLoginAction->execute();
                NK:
                goto Av;
        }
        nU:
        Av:
    }
    private function setParams($nD)
    {
        $this->repostSAMLResponseRequest = $nD;
        return $this;
    }
    private function setPostData($post)
    {
        $this->repostSAMLResponsePostData = $post;
        return $this;
    }
    private function checkForMultipleStoreAndProceedAccordingly($As, $FB)
    {
        $this->spUtility->log_debug("\x20\x69\x6e\x73\x69\144\145\x20\163\160\117\x62\163\145\x72\x76\145\162\56\143\150\x65\x63\153\106\157\162\x4d\165\154\x74\151\x70\154\145\123\x74\x6f\x72\x65\x41\156\x64\x50\162\x6f\x63\x65\145\144\x41\x63\143\x6f\162\x64\151\156\147\x6c\x79\50\x29\72\x20");
        if ($this->storeManager->hasSingleStore()) {
            goto aP;
        }
        $this->spUtility->log_debug("\x20\x63\150\145\143\x6b\106\157\x72\x4d\x75\154\x74\151\x70\154\145\123\164\x6f\162\145\x41\156\x64\x50\162\x6f\x63\145\x65\144\101\143\143\157\162\x64\x69\156\x67\154\x79\72\40\155\165\x6c\164\151\x73\x74\x6f\x72\145");
        $hs = $this->spUtility->getAdminBaseUrl();
        $this->spUtility->log_debug("\x20\141\144\155\x69\156\137\x62\141\x73\145\137\165\162\154\x20\55\x20" . $hs);
        $DR = $this->spUtility->getCurrentUrl();
        $kp = parse_url($DR, PHP_URL_HOST);
        $this->spUtility->log_debug("\40\143\x75\162\x72\145\156\164\x55\x72\x6c\x20\x2d\x20" . $DR);
        $this->spUtility->log_debug("\40\142\141\x73\x65\103\x75\162\162\145\x6e\164\x55\162\154\x20\55\x20" . $kp);
        $this->setParams($As);
        $this->setPostData($FB);
        $CN = $this->repostSAMLResponseRequest["\x53\x41\x4d\114\x52\x65\x73\160\x6f\x6e\163\145"];
        $qY = array_key_exists("\x52\x65\x6c\x61\x79\123\164\141\x74\145", $this->repostSAMLResponseRequest) ? $this->repostSAMLResponseRequest["\122\x65\x6c\x61\171\123\x74\141\x74\x65"] : "\57";
        $this->spUtility->log_debug("\40\143\150\x65\x63\x6b\106\157\162\x4d\165\x6c\164\x69\160\154\x65\x53\x74\x6f\162\x65\x41\x6e\x64\x50\x72\157\143\x65\x65\x64\101\x63\143\x6f\162\144\x69\156\147\40\x3a\40\x72\145\154\x61\x79\123\164\x61\x74\145\x20\55\x20" . $qY);
        if (!($this->spUtility->isBlank($qY) || $qY == "\57")) {
            goto t9;
        }
        $this->spUtility->log_debug("\143\x68\x65\x63\x6b\106\157\x72\x4d\x75\x6c\x74\x69\160\x6c\145\x53\164\157\x72\x65\101\x6e\144\120\x72\157\143\x65\x65\144\x41\x63\x63\157\x72\x64\x69\156\147\40\72\x20\162\x65\154\141\x79\123\164\141\x74\x65\40\151\x73\x20\102\154\x61\156\153\x2d\x20");
        $c7 = $this->spUtility->getStoreConfig(SPConstants::B2B_STORE_URL);
        $LJ = '';
        if ($this->spUtility->isBlank($c7)) {
            goto qk;
        }
        $this->spUtility->log_debug("\x63\150\x65\x63\x6b\106\157\162\115\x75\x6c\x74\x69\160\154\x65\123\164\157\162\145\101\156\144\120\x72\157\x63\x65\x65\x64\x41\x63\143\157\162\x64\151\x6e\x67\40\x3a\40\x42\x32\x62\x20\165\x72\x6c\40\x69\163\40\163\145\164\40\x2d\40");
        $LJ = $this->spUtility->getBaseUrlFromUrl($c7);
        qk:
        if (!$this->spUtility->isBlank($LJ)) {
            goto rY;
        }
        $LJ = $this->storeManager->getDefaultStoreView()->getBaseUrl();
        rY:
        $JY = $LJ . SPConstants::SUFFIX_SPOBSERVER;
        $qY = $LJ . SPConstants::SUFFIX_ACCOUNT_LOGIN;
        $this->spUtility->log_debug("\x63\150\145\x63\153\106\x6f\162\x4d\165\x6c\164\x69\x70\x6c\x65\x53\x74\157\162\145\x41\x6e\144\x50\162\x6f\x63\145\145\144\x41\x63\x63\157\162\144\151\156\147\154\171\x3a\x4e\x65\167\40\122\x65\154\x61\x79\123\x74\141\164\x65\40\x20\75\x20" . $qY);
        $this->repostSAMLResponse($CN, $qY, $JY);
        return;
        t9:
        $this->baseRelayState = $this->baseRelayState = parse_url($qY, PHP_URL_HOST);
        if ($this->spUtility->checkIfFlowStartedFromBackend($qY)) {
            goto Xb;
        }
        $this->spUtility->log_debug("\x20\143\150\145\143\153\106\157\162\x4d\x75\154\164\151\160\154\145\123\x74\157\x72\145\x41\156\x64\120\x72\x6f\x63\145\145\x64\x41\x63\143\157\x72\x64\x69\156\147\154\x79\72\40\x4e\x6f\x20\x61\x64\155\151\x6e\137\165\162\154\40\x69\x6e\40\x72\x65\154\x61\171\x73\x74\x61\164\145");
        $t_ = strpos($qY, $kp);
        $dg = $this->storeManager->getStore()->getCode();
        $nZ = false;
        $qY = str_replace("\57\x69\x6e\x64\145\170\x2e\x70\x68\x70", '', $qY);
        $Ub = parse_url($qY);
        $W1 = trim($Ub["\x70\x61\x74\x68"], "\57");
        $r5 = explode("\57", $W1);
        if (count($r5) > 0 && $r5[0] === $dg) {
            goto jp;
        }
        if (count($r5) > 0) {
            goto vi;
        }
        goto OQ;
        jp:
        $nZ = true;
        goto OQ;
        vi:
        $zt = $r5[0];
        OQ:
        $zt = $zt ?? 1;
        $PV = $this->storeManager->getStore($zt);
        $Fq = $PV->getWebsiteId();
        $this->spUtility->log_debug("\127\x65\142\163\151\164\x65\111\144\40\x6f\x66\40\x72\145\x6c\141\x79\163\164\141\164\x65\72\40" . $Fq);
        if ($t_ !== false && $nZ !== false) {
            goto Hv;
        }
        $this->spUtility->log_debug("\103\165\162\x72\x65\156\x74\x55\x72\x6c\x20\x6e\x6f\164\x20\163\141\x6d\145\40\141\163\40\x52\145\154\x61\x79\123\x74\141\164\145\x3a\40\103\x75\x72\x72\145\156\x74\125\162\x6c\72\40" . $DR);
        $this->spUtility->log_debug("\103\x75\x72\162\x65\156\x74\125\162\154\x20\156\157\x74\40\x73\141\155\x65\x20\141\x73\40\x52\x65\154\141\x79\x53\164\x61\x74\x65\x3a\x20\x52\145\154\x61\171\123\164\x61\164\x65\72\40" . $qY);
        $vf = $this->spUtility->checkIfRelayStateIsMatchingAnySite($Fq);
        if (!$vf) {
            goto cO;
        }
        $JY = $vf;
        $JY = $JY . SPConstants::SUFFIX_SPOBSERVER;
        $this->spUtility->log_debug("\x20\143\150\145\143\153\106\x6f\162\115\x75\x6c\x74\x69\160\x6c\x65\123\164\x6f\162\x65\101\x6e\144\120\162\x6f\x63\145\x65\144\x41\x63\x63\x6f\x72\144\151\156\x67\x6c\171\x3a\40\x70\x6f\x73\x74\x69\x6e\147\x20\x72\145\x73\160\x6f\x6e\x73\x65\x20\157\x6e\40\55\40" . $JY);
        $this->repostSAMLResponse($CN, $qY, $JY);
        return;
        cO:
        goto Gy;
        Hv:
        $this->spUtility->log_debug("\103\x75\162\x72\145\x6e\x74\125\162\x6c\40\163\141\x6d\145\x20\x61\163\x20\x52\145\x6c\141\x79\123\164\141\164\145\56\x20\x50\x72\x6f\x63\145\x73\x73\x69\156\147\x20\122\145\163\160\x6f\x6e\x73\145\56\x2e\x20\x2d\40" . $DR);
        $this->readResponseAction->setRequestParam($As)->setPostParam($FB)->execute();
        Gy:
        goto sQ;
        Xb:
        $this->spUtility->log_debug("\40\143\150\x65\x63\153\x46\x6f\x72\115\x75\x6c\x74\x69\160\154\x65\123\x74\x6f\162\145\101\x6e\144\x50\x72\157\x63\x65\145\x64\101\143\143\x6f\162\144\151\x6e\x67\154\171\x3a\x20\x61\x64\155\151\156\x5f\x75\x72\154\72\x20\x70\162\157\x63\145\163\x73\x69\x6e\147\x20\162\x65\163\160\157\x6e\x73\x65\40\157\156\72\x20" . $qY);
        $this->readResponseAction->setRequestParam($As)->setPostParam($FB)->execute();
        sQ:
        goto qa;
        aP:
        $this->spUtility->log_debug("\x20\x63\x68\x65\x63\153\106\157\162\115\x75\154\164\x69\160\154\x65\123\x74\x6f\x72\x65\101\x6e\144\120\162\x6f\143\x65\x65\144\x41\143\x63\157\162\x64\x69\156\x67\154\171\72\40\x53\x69\x6e\x67\x6c\x65\40\123\164\157\x72\x65\40");
        $this->readResponseAction->setRequestParam($As)->setPostParam($FB)->execute();
        qa:
    }
    private function repostSAMLResponse($CN, $p_, $Iy)
    {
        $this->spUtility->log_debug("\40\122\x65\x2d\160\x6f\x73\164\x69\156\x67\40\123\x41\115\x4c\x52\145\163\160\157\x6e\x73\x65\40\164\157\40\163\x73\x6f\x55\x72\154\x20\x2d\40" . $Iy);
        print_r("\xd\12\40\x20\40\x20\40\x20\40\x20\40\x20\x20\40\x20\x20\40\40\xd\12\x20\40\40\x20\40\40\x20\40\x20\40\40\40\x20\40\x20\40\74\150\164\155\x6c\x3e\15\xa\x20\40\40\x20\40\x20\40\x20\40\40\40\40\x20\40\40\40\40\40\x20\40\x3c\150\145\141\144\76\15\xa\x20\x20\x20\40\40\40\40\40\40\x20\x20\40\x20\40\x20\40\x20\40\x20\40\x20\x20\x20\x20\74\x73\x63\162\x69\x70\x74\x20\x73\x72\x63\x3d\47\150\164\x74\160\163\x3a\57\x2f\143\157\x64\145\56\x6a\161\x75\145\162\171\56\143\x6f\x6d\57\152\x71\x75\145\162\171\x2d\x31\56\x31\x31\x2e\x33\x2e\x6d\151\x6e\x2e\x6a\x73\47\76\x3c\57\163\x63\x72\151\160\x74\76\xd\xa\40\40\40\x20\x20\x20\x20\40\x20\x20\40\40\40\40\x20\40\40\x20\40\x20\x3c\x2f\150\x65\x61\144\76\15\12\x20\40\40\40\40\40\x20\40\x20\x20\40\40\x20\40\x20\x20\x20\40\40\x20\x3c\142\157\144\x79\76\15\xa\40\x20\x20\40\40\40\x20\40\x20\x20\x20\40\40\x20\40\40\40\40\40\x20\x20\x20\x20\40\x3c\x66\157\x72\155\40\x61\143\x74\151\x6f\x6e\x3d\x22" . $Iy . "\x22\40\x6d\x65\164\x68\x6f\x64\75\x22\x70\x6f\163\164\x22\x20\x69\x64\x3d\42\x73\141\155\x6c\x2d\162\x65\x71\x75\145\x73\164\55\x66\157\x72\155\42\40\x73\x74\171\x6c\x65\x3d\x22\144\x69\163\x70\154\x61\x79\x3a\x6e\157\156\x65\73\42\76\xd\12\40\40\x20\x20\40\40\x20\x20\x20\x20\40\40\x20\x20\x20\40\x20\x20\40\x20\x20\40\40\x20\40\x20\x20\x20\74\151\156\160\165\164\40\164\171\160\145\x3d\42\150\x69\144\144\145\x6e\x22\40\156\141\x6d\145\x3d\x22\123\101\115\114\122\145\x73\x70\x6f\156\163\x65\42\40\x76\x61\154\165\145\x3d\42" . $CN . "\42\40\x2f\x3e\xd\xa\40\x20\x20\x20\x20\40\x20\40\40\x20\40\40\x20\x20\40\40\x20\x20\x20\x20\x20\40\40\x20\x20\x20\x20\40\x3c\151\x6e\160\x75\164\x20\164\x79\160\145\x3d\42\150\x69\144\x64\x65\156\42\x20\x6e\141\155\145\x3d\42\x52\x65\154\141\171\123\164\141\164\145\x22\40\166\x61\154\165\x65\75\x22" . $p_ . "\x22\40\x2f\x3e\xd\xa\40\40\40\40\x20\x20\x20\40\x20\x20\x20\x20\x20\40\40\x20\40\x20\x20\x20\x20\x20\40\40\x3c\57\x66\x6f\x72\155\76\15\12\x20\40\x20\40\x20\x20\40\x20\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\40\40\x20\40\40\74\160\x3e\x50\154\x65\141\163\x65\x20\x77\141\x69\164\x20\167\x65\40\x61\x72\x65\x20\160\x72\157\x63\x65\163\163\x69\x6e\x67\40\171\157\x75\x72\40\x72\145\x71\165\x65\x73\164\x2e\56\74\57\160\x3e\xd\12\x20\x20\40\x20\x20\40\x20\40\40\x20\x20\x20\x20\40\x20\40\x20\40\40\40\40\x20\40\x20\15\12\40\40\40\x20\x20\40\x20\x20\x20\x20\40\x20\x20\40\x20\x20\40\40\40\40\x20\40\40\x20\x3c\x73\x63\162\x69\x70\164\40\x74\x79\160\x65\75\x22\x74\x65\170\164\57\152\141\166\141\163\143\162\151\x70\x74\x22\76\xd\12\x20\40\x20\x20\40\40\x20\x20\40\40\40\40\40\x20\x20\40\40\40\40\40\x20\40\40\40\x20\40\40\40\x20\x20\40\x20\44\50\x66\x75\156\143\x74\x69\157\x6e\50\x29\173\x64\157\143\165\x6d\x65\x6e\164\56\146\157\x72\155\x73\x5b\47\x73\x61\x6d\154\x2d\162\x65\x71\165\145\x73\x74\55\x66\x6f\162\155\x27\135\56\163\x75\x62\155\151\x74\50\x29\73\x7d\x29\73\15\xa\x20\40\x20\40\40\40\x20\x20\40\x20\40\x20\40\x20\40\40\x20\40\x20\x20\40\40\40\x20\74\x2f\163\x63\162\151\x70\164\76\15\xa\40\x20\40\x20\x20\40\40\x20\x20\x20\x20\40\40\x20\x20\40\x20\x20\40\x20\74\57\142\157\x64\171\x3e\xd\12\x20\x20\40\x20\x20\40\40\40\x20\40\x20\40\x20\x20\x20\x20\74\x2f\x68\x74\x6d\154\76");
        $this->spUtility->log_debug("\40\x52\145\x70\x6f\x73\x74\145\144\40\x53\101\115\x4c\x52\x65\x73\160\x6f\x6e\163\145\x20\163\165\143\x63\x65\163\x73\146\x75\x6c\154\x79\56");
    }
}
