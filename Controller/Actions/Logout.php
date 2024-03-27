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
use MiniOrange\SP\Helper\Saml2\SAML2Assertion;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class Logout extends Action implements CsrfAwareActionInterface
{
    protected $messageManager;
    protected $logger;
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
    protected $REQUEST;
    protected $POST;
    public function __construct(ManagerInterface $b_, LoggerInterface $kU, Context $gt, SPUtility $fR, AdminLoginAction $oS, Http $Q0, ReadLogoutRequestAction $Jn, RequestInterface $E1, StoreManagerInterface $VO, ShowTestResultsAction $Ip, ResultFactory $ps, PageFactory $Wl, FormKey $MW)
    {
        $this->messageManager = $b_;
        $this->logger = $kU;
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
        $this->getRequest()->setParam("\146\157\x72\x6d\x5f\x6b\x65\171", $this->formkey->getFormKey());
        $F_ = \Magento\Framework\App\ObjectManager::getInstance();
        $this->responseFactory = $F_->get("\x5c\x4d\x61\x67\x65\x6e\164\x6f\x5c\106\162\141\x6d\145\x77\157\x72\153\134\101\160\160\134\122\x65\163\160\x6f\156\163\145\x46\141\143\x74\157\162\x79");
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
        $this->spUtility->log_debug("\x49\156\40\154\157\x67\157\x75\164");
        $Te = $this->getRequest()->getParams();
        if (!($this->spUtility->getSessionData(SPConstants::IDP_NAME) == NULL || $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME) == NULL)) {
            goto PK;
        }
        $yO = $Te["\x53\x41\x4d\x4c\122\x65\163\x70\x6f\156\163\145"];
        $iY = base64_decode($Te["\123\101\x4d\114\x52\x65\163\160\157\156\163\145"]);
        $rT = new \DOMDocument();
        $rT->loadXML($iY);
        $Kh = $rT->firstChild;
        $wn = new SAML2Assertion($Kh, $this->spUtility);
        $wQ = $wn->getIssuer();
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\144\x70\x5f\145\x6e\x74\x69\x74\171\x5f\151\144"] === $wQ)) {
                goto o4;
            }
            $hR = $ub->getData();
            o4:
            hq:
        }
        lz:
        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $hR["\151\144\x70\x5f\156\x61\155\145"]);
        $this->spUtility->log_debug("\154\x6f\x67\x6f\165\164\72\x20\x45\170\x65\143\165\164\x65\x3a\143\165\x73\164\x6f\x6d\145\162\x5f\151\144\160\x20\x6e\141\x6d\145\40" . $hR["\x69\x64\x70\137\156\x61\x6d\145"]);
        $this->spUtility->setAdminSessionData(SPConstants::IDP_NAME, $hR["\151\144\160\137\x6e\x61\x6d\x65"]);
        $this->spUtility->log_debug("\x6c\157\x67\x6f\x75\164\72\40\x45\170\145\143\x75\x74\145\72\x61\x64\x6d\151\x6e\137\x69\144\x70\x20\156\x61\155\x65\40" . $hR["\x69\144\160\x5f\156\141\155\145"]);
        PK:
        $Ea = null;
        $Ea = $this->spUtility->checkIfFlowStartedFromBackend($Te["\122\x65\154\x61\x79\123\x74\141\164\x65"]);
        $ND = $Te["\122\x65\154\141\x79\123\x74\x61\x74\145"];
        $H_ = $Te["\122\x65\154\x61\171\123\164\x61\x74\x65"];
        if ($Ea) {
            goto kk;
        }
        $this->spUtility->setSessionData("\143\x75\163\x74\157\x6d\x65\x72\137\160\157\x73\x74\137\x6c\x6f\x67\157\165\164", NULL);
        $this->spUtility->log_debug("\x6c\x6f\147\x6f\x75\164\72\x20\x45\170\145\143\165\164\145\x3a\x63\x75\163\x74\x6f\x6d\x65\162");
        $KF = $ND . "\143\x75\163\x74\157\x6d\145\162\57\141\x63\143\157\165\x6e\164\57\x6c\157\x67\x6f\165\164";
        $this->spUtility->redirectURL($KF);
        exit;
        goto Qx;
        kk:
        $this->spUtility->setAdminSessionData("\141\x64\x6d\x69\x6e\137\160\x6f\163\x74\137\x6c\x6f\x67\157\x75\x74", NULL);
        $VN = $H_;
        $this->spUtility->setAdminSessionData("\141\x64\155\151\156\137\160\x6f\163\164\154\157\147\x6f\165\x74\137\141\x63\x74\x69\157\156", 1);
        $this->spUtility->log_debug("\154\x6f\x67\157\165\164\x3a\x20\x45\x78\145\143\x75\x74\x65\72\x61\144\x6d\151\x6e");
        $this->spUtility->log_debug("\154\157\x67\157\x75\164\x3a\x20\141\x64\155\151\x6e\x20\154\157\147\x6f\165\x74\40\x75\x72\x6c\x3a" . json_encode($VN));
        $this->spUtility->setAdminSessionData("\x61\x64\155\x69\x6e\137\x6c\157\x67\x6f\x75\x74\137\165\162\x6c", NULL);
        if ($VN != NULL) {
            goto fJ;
        }
        $this->spUtility->setSessionData("\143\165\163\x74\157\x6d\x65\162\137\160\x6f\x73\x74\137\154\x6f\147\x6f\x75\x74", NULL);
        $this->spUtility->redirectURL($VN);
        exit;
        goto YG;
        fJ:
        $this->spUtility->redirectURL($VN);
        exit;
        YG:
        Qx:
    }
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }
}
