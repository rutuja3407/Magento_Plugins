<?php


namespace MiniOrange\SP\Helper;

use DOMDocument;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Logger\Logger;
use MiniOrange\SP\Model\MiniorangeSamlIDPsFactory;
use Psr\Log\LoggerInterface;
class SPUtility extends Data
{
    public $_storeManager;
    public $customerRepository;
    public $resultFactory;
    public $messageManager;
    public $backendHelper;
    protected $adminSession;
    protected $customerSession;
    protected $authSession;
    protected $cacheTypeList;
    protected $cacheFrontendPool;
    protected $fileSystem;
    protected $reinitableConfig;
    protected $logger;
    protected $websiteModel;
    protected $websiteRepository;
    protected $resource;
    protected $userFactory;
    protected $responseFactory;
    protected $_logger;
    protected $_response;
    protected $productMetadata;
    protected $storeManager;
    public function __construct(ScopeConfigInterface $uc, UserFactory $na, CustomerFactory $kD, UrlInterface $kL, WriterInterface $z0, Repository $FG, \Magento\Backend\Helper\Data $NX, Url $Z2, \Magento\Backend\Model\Session $y6, \Magento\Customer\Model\Session $fD, \Magento\Backend\Model\Auth\Session $VX, TypeListInterface $df, Pool $mu, File $B1, LoggerInterface $kU, ReinitableConfigInterface $XG, StoreManagerInterface $VO, CustomerRepositoryInterface $oa, Website $O5, WebsiteRepositoryInterface $QL, ResourceConnection $dy, ResultFactory $ps, UserFactory $Do, \Magento\Backend\Helper\Data $Q2, ResponseFactory $Jv, ManagerInterface $b_, MiniorangeSamlIDPsFactory $S2, ResponseInterface $J_, ProductMetadataInterface $g8, DateTime $fO, Logger $Q9)
    {
        $this->adminSession = $y6;
        $this->customerSession = $fD;
        $this->authSession = $VX;
        $this->cacheTypeList = $df;
        $this->cacheFrontendPool = $mu;
        $this->fileSystem = $B1;
        $this->logger = $kU;
        $this->userFactory = $Do;
        $this->websiteRepository = $QL;
        $this->websiteModel = $O5;
        $this->_storeManager = $VO;
        $this->resultFactory = $ps;
        $this->customerRepository = $oa;
        $this->reinitableConfig = $XG;
        $this->resource = $dy;
        $this->messageManager = $b_;
        $this->backendHelper = $Q2;
        $this->responseFactory = $Jv;
        $this->_logger = $Q9;
        $this->_response = $J_;
        $this->productMetadata = $g8;
        parent::__construct($uc, $na, $kD, $kL, $z0, $FG, $NX, $Z2, $fO, $g8, $S2);
    }
    public function getHiddenPhone($zw)
    {
        $O0 = "\170\x78\170\x78\170\x78\170" . substr($zw, strlen($zw) - 3);
        return $O0;
    }
    public function isCurlInstalled()
    {
        if (in_array("\x63\x75\x72\154", get_loaded_extensions())) {
            goto EX;
        }
        return 0;
        goto z5;
        EX:
        return 1;
        z5:
    }
    public function validatePhoneNumber($zw)
    {
        if (!preg_match(MoIDPConstants::PATTERN_PHONE, $zw, $ob)) {
            goto Nz;
        }
        return TRUE;
        goto aC;
        Nz:
        return FALSE;
        aC:
    }
    public function getHiddenEmail($EK)
    {
        if (!(empty($EK) || trim($EK) === '')) {
            goto Ev;
        }
        return '';
        Ev:
        $gk = strlen($EK);
        $gZ = substr($EK, 0, 1);
        $Dq = strrpos($EK, "\100");
        $Fl = substr($EK, $Dq - 1, $gk);
        $nO = 1;
        Px:
        if (!($nO < $Dq)) {
            goto hJ;
        }
        $gZ = $gZ . "\170";
        FJ:
        $nO++;
        goto Px;
        hJ:
        $j3 = $gZ . $Fl;
        return $j3;
    }
    public function getAdminSessionData($On, $Vr = false)
    {
        return $this->adminSession->getData($On, $Vr);
    }
    public function getSessionData($On, $Vr = false)
    {
        return $this->customerSession->getData($On, $Vr);
    }
    public function unsetSessionData($On, $Vr = false)
    {
        return $this->customerSession->unsetData($On, $Vr);
    }
    public function setSessionDataForCurrentUser($On, $VP)
    {
        if ($this->customerSession->isLoggedIn()) {
            goto fQ;
        }
        if ($this->authSession->isLoggedIn()) {
            goto jk;
        }
        goto b3;
        fQ:
        $this->setSessionData($On, $VP);
        goto b3;
        jk:
        $this->setAdminSessionData($On, $VP);
        b3:
    }
    public function setSessionData($On, $VP)
    {
        return $this->customerSession->setData($On, $VP);
    }
    public function setAdminSessionData($On, $VP)
    {
        return $this->adminSession->setData($On, $VP);
    }
    public function isSPConfigured()
    {
        $ay = $this->getStoreConfig(SPConstants::IDP_NAME);
        return $this->isBlank($ay) ? FALSE : TRUE;
    }
    public function isBlank($VP)
    {
        if (!empty($VP)) {
            goto vI;
        }
        return TRUE;
        vI:
        return FALSE;
    }
    public function micr()
    {
        if (!$this->check_license_plan(4)) {
            goto jS;
        }
        return true;
        jS:
        $EK = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $On = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        return !$this->isBlank($EK) && !$this->isBlank($On) ? TRUE : FALSE;
    }
    public function check_license_plan($Gd)
    {
        return $this->get_license_plan() >= $Gd;
    }
    public function get_license_plan()
    {
        $N5 = SPConstants::LICENSE_PLAN;
        return $N5 === "\155\x61\147\x65\156\164\x6f\137\163\x61\x6d\154\137\164\x72\151\x61\x6c\137\160\x6c\141\156" ? 4 : ($N5 === "\x6d\x61\147\x65\156\x74\x6f\137\x73\141\x6d\154\x5f\145\156\x74\145\x72\160\x72\151\x73\x65\x5f\160\x6c\x61\x6e" ? 3 : ($N5 === "\155\x61\x67\145\156\164\157\137\x73\141\x6d\154\137\x70\162\145\155\151\165\x6d\x5f\x70\154\141\x6e" ? 2 : ($N5 === "\x6d\x61\147\145\x6e\x74\x6f\x5f\163\141\x6d\154\137\163\164\141\x6e\x64\x61\162\144\137\x70\x6c\x61\156" ? 1 : 0)));
    }
    public function isUserLoggedIn()
    {
        return $this->customerSession->isLoggedIn() || $this->authSession->isLoggedIn();
    }
    public function getCurrentAdminUser()
    {
        return $this->authSession->getUser();
    }
    public function getCurrentUser()
    {
        return $this->customerSession->getCustomer();
    }
    public function getCustomer($Gh)
    {
        return $this->customerRepository->getById($Gh);
    }
    public function desanitizeCert($d2)
    {
        return SAML2Utilities::desanitize_certificate($d2);
    }
    public function sanitizeCert($d2)
    {
        return SAML2Utilities::sanitize_certificate($d2);
    }
    public function getFileContents($SI)
    {
        return $this->fileSystem->fileGetContents($SI);
    }
    public function putFileContents($SI, $or)
    {
        $this->fileSystem->filePutContents($SI, $or);
    }
    public function getLogoutUrl()
    {
        if (!$this->customerSession->isLoggedIn()) {
            goto Ic;
        }
        return $this->getUrl("\143\x75\163\164\157\x6d\145\162\57\x61\x63\143\157\165\156\x74\57\x6c\157\x67\157\x75\x74");
        Ic:
        if (!$this->authSession->isLoggedIn()) {
            goto NR;
        }
        return $this->getAdminUrl("\141\x64\x6d\151\x6e\x68\x74\x6d\154\57\x61\165\x74\x68\57\154\x6f\147\157\165\x74");
        NR:
        return "\57";
    }
    public function getUrl($At, $Te = array())
    {
        return parent::getUrl($At, $Te);
    }
    public function getcustomerLogoutUrl()
    {
        return $this->getBaseUrl() . "\x63\x75\163\x74\157\x6d\x65\x72\x2f\x61\x63\143\x6f\x75\156\x74\x2f\x6c\x6f\147\157\x75\x74";
    }
    public function mclv()
    {
        if (!$this->check_license_plan(4)) {
            goto NZ;
        }
        return true;
        NZ:
        $Ai = $this->getStoreConfig(SPConstants::TOKEN);
        $zb = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::SAMLSP_CKL), $Ai);
        $rg = $this->getStoreConfig(SPConstants::SAMLSP_LK);
        return $zb == "\x74\162\165\x65" && !$this->isBlank($rg) ? TRUE : FALSE;
    }
    public function vml($wI)
    {
        $rY = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $UE = $this->getStoreConfig(SPConstants::API_KEY);
        $Qz = Curl::vml($rY, $UE, $wI, $this->getBaseUrl());
        return $Qz;
    }
    public function mius()
    {
        $rY = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $UE = $this->getStoreConfig(SPConstants::API_KEY);
        $Ai = $this->getStoreConfig(SPConstants::TOKEN);
        $wI = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::SAMLSP_LK), $Ai);
        $Qz = Curl::mius($rY, $UE, trim($wI));
        return $Qz;
    }
    public function getB2bStoreUrl()
    {
        $XC = $this->getStoreConfig(SPConstants::B2B_STORE_URL);
        return !$this->isBlank($XC) ? $XC : '';
    }
    public function getB2cStoreUrl()
    {
        $JV = $this->getStoreConfig(SPConstants::B2C_STORE_URL);
        return !$this->isBlank($JV) ? $JV : '';
    }
    public function getAutoCreateAdmin()
    {
        return $this->getStoreConfig(SPConstants::AUTO_CREATE_ADMIN);
    }
    public function getAutoCreateCustomer()
    {
        return $this->getStoreConfig(SPConstants::AUTO_CREATE_CUSTOMER);
    }
    public function getDisableB2C()
    {
        return $this->getStoreConfig(SPConstants::DISABLE_B2C);
    }
    public function isSLOConfigured()
    {
        $ty = $this->getStoreConfig(SPConstants::SAML_SLO_URL);
        return $this->isBlank($ty) ? FALSE : TRUE;
    }
    public function getAdminUserById($Gh)
    {
        $user = $this->userFactory->create()->load($Gh);
        return $user;
    }
    public function getBaseUrlFromUrl($CM)
    {
        $this->log_debug("\40\123\160\x55\164\x69\x6c\151\164\171\x3a\x20\x67\145\x74\x42\x61\x73\145\x55\162\154\x46\162\x6f\x6d\x55\x72\154\x28\51\x20\146\157\x72\72" . $CM);
        $ZB = $this->_storeManager->getStores();
        foreach ($ZB as $vH) {
            $At = $vH->getBaseUrl();
            $Tj = strpos($CM, $At);
            if (!($Tj !== false)) {
                goto pb;
            }
            $this->log_debug("\123\160\x55\164\151\154\x69\164\x79\x20\x3a\40\147\145\164\x42\141\x73\145\x55\162\x6c\106\x72\157\155\x55\162\154\50\x29\56\x20" . $At);
            return $At;
            pb:
            D1:
        }
        He:
        $CM = parse_url($CM, PHP_URL_HOST);
        $this->log_debug("\x53\160\125\x74\151\154\x69\x74\x79\72\40\147\145\164\102\x61\163\145\125\x72\x6c\x46\x72\x6f\x6d\125\x72\154\x28\x29\72" . $CM . "\57");
        return $CM;
    }
    public function log_debug($Fx = '', $u2 = null)
    {
        if (is_object($Fx)) {
            goto xX;
        }
        $this->customlog("\115\x4f\x20\x53\x41\x4d\x4c\40\x3a\x20" . $Fx);
        goto hS;
        xX:
        $this->customlog("\x4d\117\x20\123\x41\115\114\40\x20\72\40" . print_r($u2, true));
        hS:
        if (!($u2 != null)) {
            goto G3;
        }
        $this->customlog("\115\117\40\123\x41\x4d\114\40\x3a\x20" . var_export($u2, true));
        G3:
    }
    public function customlog($Op)
    {
        $this->isLogEnable() ? $this->_logger->debug($Op) : NULL;
    }
    public function isLogEnable()
    {
        return $this->getStoreConfig(SPConstants::ENABLE_DEBUG_LOG);
    }
    public function getStoreById($Gh)
    {
        return $this->_storeManager->getStore($Gh);
    }
    public function getWebsiteCode()
    {
        return $this->_storeManager->getWebsite()->getCode();
    }
    public function getWebsiteName($Ed)
    {
        $yG = $this->_websiteModel->load($Ed, "\x77\x65\142\163\x69\x74\x65\x5f\151\x64");
        $Lw = $yG->getData();
        return $Lw[0]["\x6e\x61\155\145"];
    }
    public function getWebsiteById($Gh)
    {
        return $this->websiteRepository->getById($Gh);
    }
    public function insertRowInTable($O7, $or)
    {
        $this->resource->getConnection()->insertMultiple($O7, $or);
    }
    public function updateColumnInTable($O7, $iH, $QD, $OY, $Gu)
    {
        $this->log_debug("\165\x70\x64\141\164\x65\103\157\154\165\155\x6e\111\x6e\124\141\x62\154\145" . $iH);
        $oD = $this->resource->getConnection();
        if (!($oD->tableColumnExists($O7, $iH) === false)) {
            goto Ti;
        }
        $oD->addColumn($O7, $iH, array("\x74\171\x70\x65" => "\164\x65\x78\164", "\156\x75\x6c\154\141\x62\x6c\x65" => false, "\154\145\x6e\147\164\x68" => 255, "\141\146\164\145\162" => null, "\143\157\x6d\x6d\x65\x6e\164" => $iH));
        Ti:
        $this->resource->getConnection()->update($O7, [$iH => $QD], [$OY . "\x20\75\x20\x3f" => $Gu]);
    }
    public function updateRowInTable($O7, $uf, $OY, $Gu)
    {
        $this->log_debug("\x75\160\144\141\164\x65\122\x6f\x77\111\x6e\x54\x61\x62\154\x65");
        $this->resource->getConnection()->update($O7, $uf, [$OY . "\40\75\x20\77" => $Gu]);
    }
    public function getValueFromTableSQL($O7, $wH, $OY, $Gu)
    {
        $oD = $this->resource->getConnection();
        $Q8 = "\123\x45\114\x45\103\124\x20" . $wH . "\x20\106\122\x4f\x4d\x20" . $O7 . "\x20\127\x48\x45\x52\x45\x20" . $OY . "\40\75\40" . $Gu;
        $this->log_debug("\x53\121\x4c\x3a\x20" . $Q8);
        $bs = $oD->fetchOne($Q8);
        $this->log_debug("\162\145\163\x75\154\x74\x20\x73\x71\x6c\x3a\40" . $bs);
        return $bs;
    }
    public function checkIfFlowStartedFromBackend($Nf)
    {
        $x7 = $this->helperBackend->getAreaFrontName();
        if (!str_contains($Nf, $x7)) {
            goto OF;
        }
        $this->log_debug("\x63\x68\x65\143\153\x49\146\x46\x6c\157\167\123\164\141\162\164\x65\x64\x46\162\157\x6d\102\141\x63\153\145\156\144\x3a\x20\164\x72\165\x65");
        return true;
        OF:
        $this->log_debug("\x63\x68\x65\143\153\111\x66\106\x6c\157\167\x53\x74\141\162\164\145\x64\x46\x72\157\155\x42\x61\143\153\145\x6e\x64\x3a\40\x66\141\154\163\145");
        return false;
    }
    public function getadmincode()
    {
        $x7 = $this->helperBackend->getAreaFrontName();
        return $x7;
    }
    public function handle_upload_metadata($SI, $At, $Te)
    {
        if (!(!empty($SI) || !empty($At))) {
            goto ev;
        }
        if (!empty($SI["\x74\x6d\160\137\156\x61\155\145"])) {
            goto vR;
        }
        $At = filter_var($At, FILTER_SANITIZE_URL);
        $B8 = array("\163\163\x6c" => array("\166\x65\x72\x69\146\171\x5f\160\x65\145\162" => false, "\x76\x65\162\151\x66\x79\137\160\145\145\162\137\x6e\141\155\145" => false));
        if (empty($At)) {
            goto Nr;
        }
        $SI = file_get_contents($At, false, stream_context_create($B8));
        goto ob;
        Nr:
        return;
        ob:
        goto jQ;
        vR:
        $SI = file_get_contents($SI["\164\x6d\160\x5f\x6e\x61\155\x65"]);
        jQ:
        $this->upload_metadata($SI, $Te);
        ev:
    }
    public function upload_metadata($SI, $Te)
    {
        $rT = new DOMDocument();
        $rT->loadXML($SI);
        restore_error_handler();
        $dI = $rT->firstChild;
        if (!empty($dI)) {
            goto qJ;
        }
        return;
        goto Ch;
        qJ:
        $BF = new IDPMetadataReader($rT);
        $pZ = $BF->getIdentityProviders();
        if (!empty($pZ)) {
            goto k3;
        }
        return;
        k3:
        foreach ($pZ as $On => $bf) {
            $z1 = $bf->getLoginURL("\x48\124\x54\x50\x2d\122\x65\x64\x69\162\x65\x63\164");
            $Lr = $bf->getEntityID();
            $rO = $bf->getSigningCertificate();
            $sD = "\x73\x61\155\x6c";
            $fT = array("\x73\x61\x6d\154\111\163\x73\x75\145\x72" => !empty($Lr) ? $Lr : 0, "\x73\x73\x6f\x75\162\x6c" => !empty($z1) ? $z1 : 0, "\154\157\147\151\156\102\151\x6e\144\x69\x6e\147\124\171\x70\x65" => "\110\164\164\160\122\145\x64\x69\x72\x65\143\164", "\x63\x65\x72\x74\151\x66\151\x63\x61\x74\x65" => !empty($rO) ? $rO[0] : 0);
            $this->generic_update_query($sD, $fT, $Te);
            goto Gq;
            Al:
        }
        Gq:
        return;
        Ch:
    }
    public function generic_update_query($sD, $fT, $Te)
    {
        $this->log_debug("\x49\x6e\40\x67\145\x6e\145\x72\x69\143\x5f\165\160\x64\141\164\x65\137\161\165\145\x72\x79\x20\x66\x75\156\143\x74\x69\157\x6e");
        $yf = json_encode($fT);
        if (!empty($Te["\163\141\155\x6c\x5f\x69\x64\145\156\164\x69\x74\x79\x5f\156\141\x6d\145"])) {
            goto e7;
        }
        $Te["\x73\x61\155\x6c\137\151\x64\145\x6e\164\151\164\x79\137\156\x61\155\x65"] = $Te["\x73\145\154\145\x63\164\x65\x64\x5f\160\x72\x6f\x76\151\x64\145\x72"];
        e7:
        $gu = trim($Te["\163\x61\155\154\x5f\151\x64\145\x6e\164\x69\164\171\137\156\x61\155\x65"]);
        $yG = $this->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\x64\160\137\x6e\141\155\x65"] === $gu)) {
                goto LQ;
            }
            $hR = $ub->getData();
            LQ:
            aH:
        }
        jZ:
        foreach ($fT as $On => $VP) {
            if (!($On == "\163\141\x6d\154\111\163\163\165\145\x72")) {
                goto MJ;
            }
            $FW = trim($VP);
            MJ:
            if (!($On == "\x73\163\x6f\x75\x72\x6c")) {
                goto kF;
            }
            $I_ = trim($VP);
            kF:
            if (!($On == "\154\x6f\147\x69\x6e\102\x69\x6e\x64\x69\x6e\147\x54\171\x70\145")) {
                goto lj;
            }
            $CI = trim($VP);
            lj:
            if (!($On == "\143\x65\x72\x74\151\x66\x69\143\x61\164\145")) {
                goto Lk;
            }
            $zS = SAML2Utilities::sanitize_certificate(trim($VP));
            Lk:
            rj:
        }
        iI:
        $tb = !empty($Te["\x73\x61\x6d\x6c\x5f\154\157\x67\x6f\165\164\x5f\165\162\154"]) ? trim($Te["\163\141\155\154\137\x6c\157\x67\x6f\x75\x74\x5f\165\162\x6c"]) : '';
        $fF = "\x48\164\x74\x70\122\x65\144\151\x72\145\x63\x74";
        $LT = !empty($Te["\163\141\x6d\154\137\162\x65\163\x70\157\156\x73\145\137\x73\151\x67\156\x65\x64"]) && $Te["\163\141\155\x6c\x5f\x72\x65\x73\x70\x6f\x6e\163\x65\x5f\163\x69\147\x6e\x65\x64"] == "\131\145\x73" ? 1 : 0;
        $GK = !empty($Te["\163\x61\155\154\x5f\x61\163\x73\x65\x72\164\x69\157\156\137\163\x69\x67\x6e\x65\x64"]) && $Te["\163\x61\x6d\154\x5f\x61\x73\163\x65\162\x74\151\x6f\x6e\x5f\x73\151\x67\x6e\145\144"] == "\x59\x65\163" ? 1 : 0;
        $y4 = !empty($hR["\x73\150\157\x77\137\x61\x64\155\x69\156\x5f\x6c\151\156\x6b"]) && $hR["\x73\150\157\x77\x5f\x61\x64\x6d\151\x6e\137\154\151\156\x6b"] == true ? 1 : 0;
        $t8 = !empty($hR["\x73\150\157\167\x5f\143\165\163\x74\157\155\145\162\137\x6c\151\x6e\153"]) && $hR["\x73\150\157\167\x5f\x63\x75\x73\164\x6f\155\145\x72\137\x6c\x69\156\x6b"] == true ? 1 : 0;
        $Ab = !empty($hR["\141\x75\x74\x6f\x5f\x63\162\145\x61\x74\145\137\x61\x64\x6d\151\x6e\137\x75\x73\x65\162\x73"]) && $hR["\141\x75\x74\x6f\137\143\x72\145\x61\164\145\x5f\141\x64\x6d\x69\x6e\137\165\x73\x65\162\163"] == true ? 1 : 0;
        $Cx = !empty($hR["\141\x75\x74\x6f\x5f\143\x72\x65\x61\164\x65\x5f\x63\165\163\164\x6f\x6d\x65\162\163"]) && $hR["\141\x75\164\157\137\x63\x72\145\x61\164\145\137\x63\x75\163\164\x6f\x6d\145\x72\x73"] == true ? 1 : 0;
        $kY = !empty($hR["\x64\151\x73\141\142\154\145\137\142\62\143"]) && $hR["\144\151\x73\x61\142\x6c\x65\x5f\142\x32\143"] == true ? 1 : 0;
        $ni = !empty($hR["\x66\x6f\x72\143\x65\x5f\141\x75\x74\x68\x65\x6e\164\151\x63\141\x74\x69\x6f\156\137\x77\151\164\x68\137\x69\144\x70"]) && $hR["\146\x6f\x72\143\x65\137\x61\x75\164\x68\145\x6e\164\x69\143\x61\x74\x69\x6f\156\137\167\151\164\x68\x5f\151\x64\160"] == true ? 1 : 0;
        $j2 = !empty($hR["\141\x75\164\157\137\162\x65\144\x69\x72\x65\x63\x74\x5f\164\x6f\137\151\144\160"]) && $hR["\x61\165\x74\x6f\x5f\162\x65\x64\151\162\x65\x63\x74\x5f\x74\x6f\137\x69\144\x70"] == true ? 1 : 0;
        $zL = !empty($hR["\x6c\151\x6e\x6b\137\x74\157\x5f\x69\156\151\164\151\x61\x74\145\137\163\x73\x6f"]) && $hR["\x6c\151\156\153\137\x74\157\x5f\x69\x6e\x69\164\x69\141\x74\145\x5f\x73\x73\157"] == true ? 1 : 0;
        $Ql = !empty($hR["\165\x70\144\141\x74\x65\137\141\x74\164\x72\x69\142\x75\x74\145\163\137\x6f\156\137\154\x6f\x67\x69\x6e"]) ? $hR["\165\x70\x64\x61\164\x65\x5f\141\164\x74\x72\151\x62\x75\164\145\163\137\x6f\156\x5f\154\x6f\147\x69\x6e"] : "\165\x6e\143\x68\145\143\x6b\145\144";
        $Hr = !empty($hR["\143\162\145\x61\164\145\137\x6d\x61\x67\x65\x6e\x74\x6f\x5f\x61\143\143\x6f\165\156\164\137\x62\171"]) ? $hR["\x63\x72\145\x61\x74\145\x5f\155\141\147\x65\x6e\164\x6f\137\x61\143\x63\x6f\x75\x6e\x74\137\142\171"] : '';
        $Qx = !empty($hR["\145\155\141\x69\154\137\141\x74\164\x72\x69\x62\165\164\x65"]) ? $hR["\x65\x6d\x61\x69\154\x5f\141\164\164\162\151\x62\x75\164\x65"] : '';
        $pf = !empty($hR["\165\x73\x65\162\156\141\155\x65\137\x61\x74\x74\162\x69\x62\165\x74\x65"]) ? $hR["\165\163\145\162\x6e\x61\x6d\145\137\x61\x74\164\x72\x69\142\165\x74\x65"] : '';
        $Z3 = !empty($hR["\146\x69\162\163\x74\156\x61\x6d\x65\137\x61\164\164\162\x69\x62\x75\x74\x65"]) ? $hR["\x66\x69\x72\x73\x74\156\141\155\145\137\x61\164\164\162\x69\142\165\164\145"] : '';
        $ph = !empty($hR["\154\141\x73\x74\x6e\x61\x6d\x65\x5f\x61\164\164\162\x69\142\x75\164\145"]) ? $hR["\154\x61\163\164\156\141\x6d\145\137\141\164\x74\x72\x69\x62\x75\x74\145"] : '';
        $qS = !empty($hR["\147\x72\157\x75\160\137\141\x74\164\162\x69\x62\x75\164\145"]) ? $hR["\147\162\157\x75\x70\x5f\x61\164\164\162\x69\x62\x75\x74\145"] : '';
        $pY = !empty($hR["\142\x69\154\x6c\151\x6e\x67\x5f\x63\x69\x74\171\x5f\x61\164\x74\162\x69\x62\x75\x74\x65"]) ? $hR["\x62\x69\154\x6c\x69\156\x67\137\143\151\x74\171\137\x61\164\x74\x72\x69\x62\x75\x74\x65"] : '';
        $mP = !empty($hR["\142\151\x6c\154\x69\x6e\147\137\163\164\141\164\145\x5f\141\164\164\162\x69\142\165\164\x65"]) ? $hR["\x62\x69\154\x6c\151\x6e\x67\x5f\163\164\x61\x74\x65\137\x61\164\x74\162\x69\x62\165\164\145"] : '';
        $gr = !empty($hR["\x62\x69\x6c\x6c\151\156\x67\x5f\x63\x6f\165\x6e\164\162\171\x5f\x61\164\164\162\151\x62\x75\164\145"]) ? $hR["\x62\x69\x6c\154\151\156\147\x5f\x63\157\x75\156\x74\x72\x79\137\x61\164\x74\x72\x69\x62\165\x74\x65"] : '';
        $lF = !empty($hR["\x62\151\154\154\x69\156\x67\x5f\x61\144\x64\162\x65\x73\163\137\141\x74\164\x72\151\142\165\164\145"]) ? $hR["\142\x69\x6c\154\x69\x6e\147\137\141\x64\144\x72\145\163\x73\x5f\141\164\164\162\151\142\165\164\x65"] : '';
        $M8 = !empty($hR["\142\151\x6c\x6c\151\x6e\147\137\x70\x68\x6f\156\x65\x5f\141\x74\x74\x72\x69\142\165\164\x65"]) ? $hR["\142\x69\x6c\154\x69\156\147\x5f\160\x68\x6f\156\145\137\141\x74\164\162\151\x62\165\164\x65"] : '';
        $h5 = !empty($hR["\142\x69\x6c\x6c\x69\156\147\x5f\x7a\151\x70\x5f\x61\164\x74\162\151\x62\x75\x74\145"]) ? $hR["\x62\151\x6c\x6c\151\156\x67\137\x7a\x69\x70\x5f\141\164\x74\162\x69\142\165\164\145"] : '';
        $se = !empty($hR["\163\x68\151\x70\x70\151\156\x67\137\143\151\x74\171\x5f\141\x74\x74\162\151\x62\x75\x74\x65"]) ? $hR["\x73\x68\x69\160\x70\151\156\147\137\143\151\x74\171\137\141\164\x74\162\151\x62\165\164\x65"] : '';
        $zd = !empty($hR["\x73\x68\x69\x70\x70\x69\156\x67\x5f\163\164\141\x74\x65\137\141\164\164\x72\151\142\x75\x74\x65"]) ? $hR["\163\150\151\x70\160\151\x6e\x67\x5f\163\164\x61\x74\145\137\141\x74\x74\162\x69\x62\165\x74\x65"] : '';
        $il = !empty($hR["\x73\x68\x69\x70\x70\x69\x6e\x67\x5f\x63\157\165\156\164\x72\x79\137\141\164\164\162\151\x62\x75\x74\145"]) ? $hR["\x73\x68\151\160\160\151\x6e\x67\x5f\x63\157\165\x6e\x74\162\x79\137\141\164\164\x72\x69\142\x75\x74\x65"] : '';
        $We = !empty($hR["\x73\150\151\x70\x70\151\156\147\137\x61\x64\x64\162\x65\x73\163\137\141\x74\x74\x72\151\x62\x75\164\145"]) ? $hR["\x73\150\151\x70\x70\151\x6e\x67\137\141\144\144\162\x65\163\163\137\x61\x74\164\162\151\x62\x75\x74\145"] : '';
        $Qn = !empty($hR["\163\x68\151\x70\160\x69\156\x67\x5f\x70\x68\x6f\x6e\x65\x5f\x61\x74\164\x72\x69\142\165\164\x65"]) ? $hR["\163\x68\x69\160\x70\x69\156\x67\x5f\x70\x68\157\x6e\145\137\x61\164\164\x72\x69\x62\x75\x74\145"] : '';
        $Pt = !empty($hR["\163\x68\151\160\160\151\x6e\147\137\x7a\151\160\x5f\x61\x74\x74\x72\x69\142\x75\164\x65"]) ? $hR["\x73\x68\x69\x70\160\151\156\x67\x5f\172\151\x70\x5f\141\x74\x74\x72\x69\142\165\164\x65"] : '';
        $Dx = !empty($hR["\x62\62\142\137\141\164\x74\162\151\x62\x75\x74\x65"]) ? $hR["\142\62\x62\137\x61\164\164\162\151\x62\165\164\x65"] : '';
        $C5 = !empty($hR["\x63\165\x73\164\x6f\155\137\164\141\x62\154\145\x6e\x61\x6d\x65"]) ? $hR["\x63\x75\x73\x74\x6f\155\137\164\x61\142\154\145\x6e\141\155\x65"] : '';
        $V9 = !empty($hR["\x63\165\x73\x74\157\x6d\137\x61\x74\164\162\151\x62\x75\164\x65\x73"]) ? $hR["\x63\x75\x73\164\x6f\x6d\137\x61\164\x74\x72\x69\x62\x75\164\145\163"] : '';
        $WD = !empty($hR["\144\x6f\x5f\x6e\157\x74\x5f\x61\165\x74\x6f\x63\162\x65\141\164\145\x5f\x69\x66\137\162\157\x6c\145\x73\x5f\x6e\157\x74\137\155\x61\160\160\145\144"]) && $hR["\x64\157\137\156\x6f\x74\137\x61\165\164\157\x63\162\x65\141\164\145\137\x69\x66\137\162\157\154\x65\163\x5f\156\157\164\x5f\155\141\160\160\145\x64"] == true ? 1 : 0;
        $VJ = !empty($hR["\165\160\144\141\164\145\137\142\x61\x63\153\145\156\x64\137\162\157\x6c\145\x73\137\157\x6e\x5f\163\163\157"]) && $hR["\x75\160\x64\x61\164\x65\x5f\x62\x61\143\x6b\x65\x6e\144\137\162\x6f\x6c\145\x73\x5f\x6f\156\x5f\163\163\157"] == true ? 1 : 0;
        $mk = !empty($hR["\x75\160\x64\141\x74\x65\x5f\146\x72\157\x6e\164\145\x6e\144\137\147\162\x6f\165\160\163\x5f\157\156\137\x73\x73\x6f"]) && $hR["\165\x70\x64\141\164\145\x5f\146\x72\x6f\156\x74\x65\156\x64\137\x67\162\157\165\160\163\137\157\156\137\x73\x73\157"] == true ? 1 : 0;
        $YO = !empty($hR["\x64\x65\x66\141\165\x6c\164\x5f\x67\162\x6f\165\x70"]) ? $hR["\144\x65\x66\x61\165\x6c\164\137\x67\x72\x6f\x75\160"] : '';
        $Le = !empty($hR["\144\x65\146\141\x75\x6c\x74\137\x72\x6f\154\145"]) ? $hR["\x64\x65\146\x61\x75\154\x74\x5f\x72\157\154\145"] : '';
        $Qu = !empty($hR["\x67\162\157\x75\x70\163\x5f\x6d\x61\160\x70\145\x64"]) ? $hR["\147\162\x6f\165\x70\163\x5f\x6d\141\160\x70\145\144"] : '';
        $by = !empty($hR["\x72\157\x6c\145\x73\137\155\x61\x70\160\x65\x64"]) ? $hR["\x72\x6f\154\x65\163\137\x6d\x61\160\x70\x65\144"] : '';
        $bw = !empty($hR["\x73\x61\155\154\137\154\157\147\157\x75\x74\x5f\162\145\x64\151\162\x65\x63\x74\x5f\165\162\x6c"]) ? $hR["\163\x61\155\x6c\x5f\154\157\x67\157\x75\164\x5f\x72\x65\x64\151\162\x65\x63\164\x5f\165\x72\x6c"] : '';
        $Yx = !empty($hR["\163\141\x6d\x6c\x5f\145\x6e\141\142\154\145\x5f\x62\151\x6c\x6c\x69\x6e\x67\x61\x6e\x64\163\150\x69\160\160\x69\156\x67"]) ? $hR["\163\x61\155\x6c\x5f\145\x6e\x61\142\154\145\137\142\151\x6c\x6c\151\x6e\x67\141\x6e\144\163\x68\151\x70\160\151\x6e\147"] : "\x6e\157\x6e\145";
        $lN = !empty($hR["\x73\x61\155\x6c\137\x73\x61\x6d\x65\141\163\142\151\154\x6c\151\156\x67"]) ? $hR["\x73\141\155\x6c\137\163\x61\155\145\141\x73\142\151\154\154\x69\156\147"] : "\156\157\156\x65";
        $GE = !empty($hR["\x6d\157\x5f\163\141\155\154\137\150\145\x61\144\x6c\x65\x73\x73\137\163\x73\x6f"]) && $hR["\x6d\x6f\x5f\x73\141\x6d\154\x5f\x68\x65\x61\x64\154\x65\x73\163\137\x73\x73\157"] == true ? 1 : 0;
        $Q7 = !empty($hR["\x6d\157\137\163\x61\155\x6c\137\146\162\x6f\x6e\164\x65\156\x64\137\x70\x6f\x73\164\x5f\165\x72\x6c"]) ? $hR["\155\157\x5f\163\141\155\154\x5f\146\x72\157\x6e\x74\145\156\144\137\x70\157\x73\164\x5f\x75\x72\x6c"] : '';
        if (!is_null($hR)) {
            goto wP;
        }
        $this->checkIdpLimit();
        goto kO;
        wP:
        $this->deleteIDPApps((int) $hR["\x69\144"]);
        kO:
        if (empty($gu)) {
            goto DY;
        }
        $this->setIDPApps($gu, $FW, $I_, $CI, $tb, $fF, $zS, $LT, $GK, $y4, $t8, $Ab, $Cx, $kY, $ni, $j2, $zL, $Ql, $Hr, $Qx, $pf, $Z3, $ph, $qS, $pY, $mP, $gr, $lF, $M8, $h5, $se, $zd, $il, $We, $Qn, $Pt, $Dx, $C5, $V9, $WD, $VJ, $mk, $YO, $Le, $Qu, $by, $bw, $Yx, $lN, $GE, $Q7);
        DY:
        $this->setStoreConfig(SPConstants::IDP_NAME, $gu);
        $this->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $gu);
        $this->reinitConfig();
    }
    public function checkIdpLimit()
    {
        $ov = $this->getcounttable();
        if ($this->check_license_plan(4)) {
            goto vo;
        }
        if ($this->check_license_plan(3)) {
            goto Ov;
        }
        $H5 = 1;
        goto bE;
        vo:
        $H5 = 2;
        goto bE;
        Ov:
        $H5 = $this->getWebsiteLimit();
        bE:
        if ($ov < $H5) {
            goto Rm;
        }
        $this->messageManager->addErrorMessage(__("\124\157\40\x63\x6f\156\146\151\147\165\162\145\40\x6d\x6f\162\145\40\x49\144\x65\x6e\164\x69\x74\171\40\x50\x72\x6f\x76\151\x64\x65\x72\x73\x20\50\111\104\120\163\51\54\x20\160\x6c\145\x61\163\145\40\165\x70\x67\162\141\144\145\40\x74\x6f\x20\164\150\145\x20\x4d\165\x6c\x74\151\x2d\111\104\x50\x20\160\x6c\x61\156\x2e"));
        $this->responseFactory->create()->setRedirect($this->getAdminUrl("\x6d\x6f\163\160\x73\x61\155\154\x2f\x69\144\x70\x73\57\x69\156\144\x65\170"))->sendResponse();
        exit;
        Rm:
    }
    public function getWebsiteLimit()
    {
        return AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::WEBSITES_LIMIT), SPConstants::DEFAULT_TOKEN_VALUE);
    }
    public function reinitConfig()
    {
        $this->reinitableConfig->reinit();
    }
    public function isAllPageAutoRedirectEnabled($Yh)
    {
        $P6 = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($P6 == $Yh) {
            goto UH;
        }
        return 0;
        goto JT;
        UH:
        return $this->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        JT:
    }
    public function isAutoRedirectEnabled($Yh)
    {
        $P6 = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($P6 == $Yh) {
            goto JH;
        }
        return 0;
        goto wr;
        JH:
        return $this->getStoreConfig(SPConstants::AUTO_REDIRECT);
        wr:
    }
    public function result($At)
    {
        $this->responseFactory->create()->setRedirect($At)->sendResponse();
        exit;
    }
    public function defaultlog($Op)
    {
        $this->logger->debug($Op);
    }
    public function isCustomLogExist()
    {
        if ($this->fileSystem->isExists("\56\x2e\x2f\166\x61\162\57\x6c\x6f\x67\x2f\155\157\137\x73\x61\x6d\x6c\56\x6c\157\x67")) {
            goto wC;
        }
        if ($this->fileSystem->isExists("\166\141\x72\x2f\x6c\157\x67\x2f\x6d\x6f\x5f\163\x61\155\x6c\x2e\x6c\x6f\147")) {
            goto II;
        }
        goto BU;
        wC:
        return 1;
        goto BU;
        II:
        return 1;
        BU:
        return 0;
    }
    public function deleteCustomLogFile()
    {
        if ($this->fileSystem->isExists("\x2e\x2e\x2f\166\141\x72\x2f\154\x6f\x67\57\155\157\137\163\141\x6d\154\x2e\x6c\157\147")) {
            goto Od;
        }
        if ($this->fileSystem->isExists("\x76\x61\x72\x2f\x6c\x6f\x67\57\155\157\137\163\141\x6d\x6c\56\154\x6f\147")) {
            goto mv;
        }
        goto PZ;
        Od:
        $this->fileSystem->deleteFile("\x2e\56\x2f\166\141\x72\57\x6c\157\147\57\155\157\x5f\163\141\x6d\x6c\x2e\154\x6f\x67");
        goto PZ;
        mv:
        $this->fileSystem->deleteFile("\166\x61\x72\x2f\154\157\x67\x2f\x6d\x6f\x5f\163\x61\x6d\x6c\x2e\x6c\157\147");
        PZ:
    }
    public function update_status($wI)
    {
        $rY = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $UE = $this->getStoreConfig(SPConstants::API_KEY);
        $Qz = Curl::update_status($rY, $UE, $wI, $this->getBaseUrl());
        return $Qz;
    }
    public function errorPageRedirection($zR)
    {
        $this->messageManager->addErrorMessage($zR);
        $PJ = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $PJ->setUrl($this->urlInterface->getUrl("\x6e\157\x72\x6f\x75\x74\x65"));
        return $PJ;
    }
    public function redirectURL($At)
    {
        return $this->_response->setRedirect($At)->sendResponse();
    }
    public function check_license_expiry_date()
    {
        $On = $this->getStoreConfig(SPConstants::TOKEN);
        $fq = $this->getStoreConfig(SPConstants::LICENSE_EXPIRY_DATE);
        $XF = null;
        if ($fq == null) {
            goto XC;
        }
        $XF = AESEncryption::decrypt_data($fq, $On);
        goto Wc;
        XC:
        $Qz = json_decode(self::ccl(), true);
        $XF = array_key_exists("\x6c\151\x63\x65\156\163\x65\x45\x78\160\x69\162\x79", $Qz) ? strtotime($Qz["\x6c\151\143\145\x6e\x73\x65\x45\x78\160\151\x72\x79"]) === false ? null : strtotime($Qz["\x6c\x69\x63\x65\156\163\x65\105\170\160\x69\x72\x79"]) : null;
        if ($this->isBlank($XF)) {
            goto nP;
        }
        $this->setStoreConfig(SPConstants::LICENSE_EXPIRY_DATE, AESEncryption::encrypt_data($XF, $On));
        nP:
        Wc:
        $lG = new \DateTime("\100{$XF}");
        $l4 = new \DateTime();
        $zU = $l4->diff($lG)->format("\x25\x72\x25\141");
        if (!($zU <= 30)) {
            goto py;
        }
        $this->flushCache();
        $eP = $this->getStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT);
        if ($zU > 7) {
            goto J3;
        }
        if ($zU <= 7 && $zU > 0) {
            goto D7;
        }
        if ($zU <= -5) {
            goto VX;
        }
        goto WC;
        J3:
        if (!($eP == null)) {
            goto x_;
        }
        $this->license_expiry_notification_before_expiry($zU);
        $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, $zU);
        x_:
        goto WC;
        D7:
        if (!($eP == null || $eP > 7)) {
            goto Hh;
        }
        $this->license_expiry_notification_before_expiry($zU);
        $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, $zU);
        Hh:
        goto WC;
        VX:
        if (!($eP == null || $eP >= 0)) {
            goto C9;
        }
        $this->license_expiry_notification_after_expiry();
        $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, 0);
        C9:
        WC:
        py:
        return $zU;
    }
    public function ccl()
    {
        $rY = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $UE = $this->getStoreConfig(SPConstants::API_KEY);
        $Qz = Curl::ccl($rY, $UE);
        return $Qz;
    }
    public function flushCache()
    {
        $uy = array("\144\x62\x5f\144\144\154");
        foreach ($uy as $qI) {
            $this->cacheTypeList->cleanType($qI);
            Qj:
        }
        Xf:
        foreach ($this->cacheFrontendPool as $Gv) {
            $Gv->getBackend()->clean();
            B2:
        }
        AW:
    }
    public function license_expiry_notification_before_expiry($cm)
    {
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $XV = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $Qz = "\x48\x65\154\x6c\x6f\54\x3c\142\162\x3e\x3c\142\x72\x3e\124\x68\x69\x73\x20\145\155\x61\x69\x6c\40\151\163\40\164\157\40\156\157\164\x69\x66\x79\x20\x79\157\x75\x20\164\x68\x61\164\x20\171\157\x75\162\40\x31\40\171\145\x61\162\x20\x6c\151\143\x65\156\x73\145\x20\146\157\x72\x20\115\x61\147\145\156\x74\157\40\123\101\115\x4c\x20\123\x53\x4f\40\x50\154\x75\147\151\x6e\40\x77\x69\x6c\x6c\40\x65\x78\x70\x69\162\x65\xd\12\40\x20\x20\x20\x20\x20\x20\40\40\x20\x20\x20\x20\x20\x20\x20\40\40\40\40\40\40\40\40\x20\40\167\151\164\150\x69\x6e\x20" . $cm . "\x20\x64\x61\171\x73\x2e\74\142\x72\x3e\74\142\162\76\xd\12\40\x20\40\x20\40\x20\x20\40\40\40\40\40\40\40\40\40\x20\x20\x20\x20\x20\x20\x20\x20\40\x20\x3c\142\x72\76\x3c\142\162\x3e\103\157\156\164\141\x63\x74\x20\165\163\40\141\x74\x20\x3c\x61\x20\150\x72\x65\x66\75\47\155\141\x69\x6c\164\157\72\x6d\141\147\x65\x6e\164\157\163\x75\x70\160\157\x72\x74\100\170\x65\143\165\162\x69\x66\171\x2e\143\157\155\x27\x3e\155\141\147\x65\156\164\157\x73\x75\160\x70\157\162\164\100\x78\145\x63\x75\x72\151\146\171\x2e\143\157\x6d\74\x2f\141\x3e\40\x69\156\x20\157\162\144\x65\x72\40\164\157\40\162\x65\156\145\167\40\x79\x6f\x75\x72\40\154\x69\143\145\x6e\x73\x65\x2e\x3c\x62\162\76\x3c\x62\x72\x3e\124\150\141\156\x6b\163\54\74\x62\x72\x3e\155\x69\156\151\x4f\x72\x61\x6e\x67\x65";
        $fH = "\x4c\151\143\x65\156\163\145\40\105\x78\160\151\162\171\x20\x2d\40\x4d\x61\147\x65\156\x74\x6f\x20\x53\101\x4d\x4c\x20\123\x53\117\x20\120\x6c\x75\x67\x69\x6e";
        CURL::notify($rY, $UE, $XV, $Qz, $fH);
    }
    public function license_expiry_notification_after_expiry()
    {
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $XV = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $Qz = "\110\x65\x6c\x6c\x6f\x2c\x3c\142\162\x3e\74\x62\162\x3e\131\157\x75\x72\x20\61\x20\171\x65\141\x72\40\154\151\x63\x65\156\x73\x65\x20\x66\x6f\162\x20\115\x61\147\145\x6e\x74\x6f\40\x53\x41\115\114\x20\x53\123\x4f\x20\120\x6c\165\x67\x69\x6e\40\x68\x61\163\40\x65\x78\x70\x69\x72\x65\x64\56\74\142\x72\76\74\142\x72\x3e\15\xa\11\x9\11\11\11\x9\x43\x6f\156\164\x61\143\164\x20\165\x73\x20\x61\164\x20\x3c\141\x20\x68\162\x65\146\75\x27\x6d\x61\x69\x6c\x74\x6f\72\x6d\141\147\145\156\x74\157\x73\x75\x70\160\157\162\x74\100\170\145\x63\x75\x72\151\x66\x79\56\x63\x6f\x6d\47\x3e\x6d\x61\147\145\x6e\164\x6f\x73\165\160\x70\x6f\162\164\100\170\x65\x63\165\162\151\x66\171\56\x63\157\155\x3c\57\141\76\x20\151\x6e\40\x6f\x72\x64\x65\162\x20\164\x6f\40\162\145\156\x65\x77\40\171\x6f\x75\162\40\x6c\151\143\145\x6e\163\x65\x2e\x3c\x62\162\x3e\74\x62\162\76\x54\150\x61\x6e\x6b\x73\x2c\74\142\162\76\x6d\151\156\151\x4f\x72\x61\x6e\147\x65";
        $fH = "\x4c\151\x63\x65\x6e\x73\x65\40\x45\170\x70\151\x72\x65\x64\40\55\40\x4d\141\x67\x65\x6e\x74\x6f\x20\123\101\115\114\40\x53\x53\117\40\x50\x6c\x75\x67\151\x6e\x20";
        CURL::notify($rY, $UE, $XV, $Qz, $fH);
    }
    public function getMagnetoVersion()
    {
        return $this->productMetadata->getVersion();
    }
    public function update_customer_id_in_customer_visitor($Mi)
    {
        $this->log_debug("\x55\160\x64\x61\x74\x69\156\x67\40\x63\x75\x73\x74\x6f\155\x65\x72\137\x76\151\163\151\x74\157\x72\x20\164\x61\x62\154\x65");
        $oD = $this->resource->getConnection();
        $d8 = $oD->select()->from("\x63\x75\x73\164\157\155\x65\162\x5f\166\x69\163\x69\164\x6f\x72", "\103\x4f\125\116\x54\50\x2a\51");
        $uu = $oD->fetchOne($d8);
        $this->resource->getConnection()->update("\143\165\x73\164\157\155\x65\x72\137\166\151\163\151\x74\x6f\162", ["\143\x75\x73\x74\157\155\x65\x72\x5f\151\144" => $Mi], ["\x76\x69\163\x69\x74\x6f\x72\137\151\144\x20\75\40\x3f" => $uu]);
    }
    public function checkIfRelayStateIsMatchingAnySite($Nf)
    {
        $ZB = $this->_storeManager->getWebsites();
        foreach ($ZB as $Kt) {
            $VQ = $Kt->getGroups();
            foreach ($VQ as $m1) {
                $kk = $m1->getStores();
                foreach ($kk as $vH) {
                    $this->log_debug("\x53\164\157\162\x65\143\157\x64\145\x3a\x20" . $vH->getCode());
                    $this->log_debug("\x53\x74\x6f\x72\x65\111\144\72\x20" . $vH->getId());
                    $this->log_debug("\127\x65\142\163\x69\x74\145\x49\144\x3a\x20" . $vH->getWebsiteId());
                    $this->log_debug("\127\145\x62\x73\151\x74\145\116\x61\155\x65\72\x20" . $vH->getWebsite()->getName());
                    $this->log_debug("\55\55\55\x2d\x2d\55\x2d\x2d\55\55\x2d\x2d\55\x2d\x2d\55\x2d\x2d\55\x2d\55\x2d\x2d\55\x2d\x2d\x2d\55\x2d\55\55\55\x2d\x2d\55\55\x2d\x2d\x2d\x2d\55\x2d\55\55\x2d\x2d\55\x2d\x2d\55\55\x2d\55\x2d\x2d");
                    if (!($vH->getWebsiteId() == $Nf)) {
                        goto SQ;
                    }
                    $this->log_debug("\127\x65\142\x73\x69\x74\145\40\x49\144\40\155\x61\164\x63\150\x65\144\x20\x77\x69\164\x68\40\162\x65\x6c\141\x79\x53\x74\141\164\145\40\55\x20" . $vH->getWebsiteId());
                    return $vH->getBaseUrl();
                    SQ:
                    cB:
                }
                eq:
                Q5:
            }
            qH:
            Cf:
        }
        wi:
        return false;
    }
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }
    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
}
