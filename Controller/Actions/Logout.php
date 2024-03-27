<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\App\Action\Action;
use MiniOrange\SP\Helper\Exception\SAMLResponseException;
use MiniOrange\SP\Helper\Exception\InvalidSignatureInResponseException;
use MiniOrange\SP\Helper\SPMessages;
use Magento\Framework\Event\Observer;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
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
    public function __construct(ManagerInterface $c0, LoggerInterface $hI, Context $Gc, SPUtility $Kx, AdminLoginAction $ZL, Http $MA, ReadLogoutRequestAction $fi, RequestInterface $nD, StoreManagerInterface $Wl, ShowTestResultsAction $p2, ResultFactory $UZ, PageFactory $lh, FormKey $ca)
    {
        $this->messageManager = $c0;
        $this->logger = $hI;
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
        $this->getRequest()->setParam("\x66\157\162\x6d\x5f\153\145\171", $this->formkey->getFormKey());
        $c5 = \Magento\Framework\App\ObjectManager::getInstance();
        $this->responseFactory = $c5->get("\x5c\115\141\147\x65\156\x74\x6f\134\106\x72\x61\x6d\x65\167\x6f\x72\153\x5c\101\x70\x70\x5c\x52\145\163\x70\x6f\156\163\x65\106\141\x63\164\157\162\x79");
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
        $this->spUtility->log_debug("\x49\x6e\x20\154\x6f\147\157\165\x74");
        $As = $this->getRequest()->getParams();
        if (!($this->spUtility->getSessionData(SPConstants::IDP_NAME) == NULL || $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME) == NULL)) {
            goto Qb;
        }
        $CN = $As["\x53\x41\x4d\x4c\x52\145\163\x70\157\156\163\145"];
        $aM = base64_decode($As["\x53\x41\115\x4c\x52\x65\163\x70\157\156\x73\145"]);
        $L5 = new \DOMDocument();
        $L5->loadXML($aM);
        $nc = $L5->firstChild;
        $Rx = new SAML2Assertion($nc, $this->spUtility);
        $wz = $Rx->getIssuer();
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\x64\160\137\x65\x6e\164\x69\x74\171\137\151\144"] === $wz)) {
                goto lx;
            }
            $ft = $fR->getData();
            lx:
            v7:
        }
        DB:
        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $ft["\151\144\x70\137\156\141\155\145"]);
        $this->spUtility->log_debug("\x6c\x6f\x67\x6f\165\164\x3a\40\105\170\145\143\x75\x74\145\x3a\143\165\163\x74\157\x6d\x65\162\x5f\151\x64\160\40\156\x61\155\145\40" . $ft["\x69\x64\x70\137\156\x61\155\x65"]);
        $this->spUtility->setAdminSessionData(SPConstants::IDP_NAME, $ft["\x69\144\160\x5f\x6e\x61\x6d\145"]);
        $this->spUtility->log_debug("\154\157\x67\x6f\165\164\72\x20\x45\170\x65\x63\165\164\x65\x3a\x61\x64\155\151\x6e\137\151\144\160\40\x6e\x61\155\x65\x20" . $ft["\151\144\160\137\x6e\x61\x6d\145"]);
        Qb:
        $zc = null;
        $zc = $this->spUtility->checkIfFlowStartedFromBackend($As["\122\x65\x6c\x61\x79\x53\x74\141\x74\x65"]);
        $Rh = $As["\122\x65\x6c\x61\x79\123\164\x61\164\145"];
        $AL = $As["\122\x65\x6c\141\171\123\164\x61\164\145"];
        if ($zc) {
            goto xa;
        }
        $this->spUtility->setSessionData("\x63\x75\163\164\x6f\x6d\145\162\x5f\160\157\x73\164\x5f\x6c\x6f\147\157\x75\x74", NULL);
        $this->spUtility->log_debug("\x6c\x6f\x67\x6f\165\164\x3a\x20\105\170\x65\x63\165\164\x65\x3a\143\165\x73\164\x6f\155\145\162");
        $Cs = $Rh . "\x63\x75\x73\164\x6f\155\145\162\57\141\x63\x63\x6f\x75\156\164\57\x6c\x6f\x67\x6f\x75\x74";
        $this->spUtility->redirectURL($Cs);
        exit;
        goto T2;
        xa:
        $this->spUtility->setAdminSessionData("\141\x64\155\151\x6e\x5f\160\157\x73\164\137\154\x6f\147\x6f\165\x74", NULL);
        $Ty = $AL;
        $this->spUtility->setAdminSessionData("\141\144\x6d\151\x6e\x5f\x70\x6f\163\164\x6c\157\x67\157\165\x74\x5f\141\143\164\151\157\156", 1);
        $this->spUtility->log_debug("\x6c\x6f\147\157\165\x74\72\40\x45\x78\x65\x63\165\x74\x65\72\x61\144\x6d\x69\x6e");
        $this->spUtility->log_debug("\154\157\x67\x6f\165\x74\72\40\141\x64\155\x69\x6e\40\154\x6f\x67\157\165\x74\x20\165\x72\x6c\72" . json_encode($Ty));
        $this->spUtility->setAdminSessionData("\x61\144\155\151\156\137\154\157\x67\157\x75\164\137\x75\x72\x6c", NULL);
        if ($Ty != NULL) {
            goto a_;
        }
        $this->spUtility->setSessionData("\x63\x75\163\164\157\x6d\145\x72\137\160\x6f\x73\164\x5f\154\x6f\147\157\165\164", NULL);
        $this->spUtility->redirectURL($Ty);
        exit;
        goto Z3;
        a_:
        $this->spUtility->redirectURL($Ty);
        exit;
        Z3:
        T2:
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }
}
