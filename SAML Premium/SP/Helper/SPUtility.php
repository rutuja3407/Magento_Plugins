<?php


namespace MiniOrange\SP\Helper;

use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Data;
use MiniOrange\SP\Helper\Exception\InvalidOperationException;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use DOMDocument;
use MiniOrange\SP\Helper\IdentityProviders;
use MiniOrange\SP\Helper\IDPMetadataReader;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\User\Model\UserFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Url;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\Website;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use MiniOrange\SP\Model\MiniorangeSamlIDPsFactory;
use Magento\Framework\App\ResponseInterface;
use MiniOrange\SP\Logger\Logger;
class SPUtility extends Data
{
    protected $adminSession;
    protected $customerSession;
    protected $authSession;
    protected $cacheTypeList;
    protected $cacheFrontendPool;
    protected $fileSystem;
    protected $reinitableConfig;
    protected $logger;
    public $_storeManager;
    protected $websiteModel;
    protected $websiteRepository;
    protected $resource;
    protected $userFactory;
    public $customerRepository;
    public $resultFactory;
    public $messageManager;
    public $backendHelper;
    protected $responseFactory;
    protected $_logger;
    protected $_response;
    protected $productMetadata;
    protected $storeManager;
    public function __construct(ScopeConfigInterface $PS, UserFactory $Zg, CustomerFactory $j8, UrlInterface $WU, WriterInterface $UX, Repository $Tq, \Magento\Backend\Helper\Data $A3, Url $vs, \Magento\Backend\Model\Session $hL, \Magento\Customer\Model\Session $FK, \Magento\Backend\Model\Auth\Session $Yi, TypeListInterface $vZ, Pool $jw, File $PC, LoggerInterface $hI, ReinitableConfigInterface $GK, StoreManagerInterface $Wl, CustomerRepositoryInterface $kS, Website $c4, WebsiteRepositoryInterface $Qx, ResourceConnection $nQ, ResultFactory $UZ, UserFactory $t2, \Magento\Backend\Helper\Data $M8, ResponseFactory $XF, ManagerInterface $c0, MiniorangeSamlIDPsFactory $OP, ResponseInterface $qm, ProductMetadataInterface $a2, DateTime $rE, Logger $XD)
    {
        $this->adminSession = $hL;
        $this->customerSession = $FK;
        $this->authSession = $Yi;
        $this->cacheTypeList = $vZ;
        $this->cacheFrontendPool = $jw;
        $this->fileSystem = $PC;
        $this->logger = $hI;
        $this->userFactory = $t2;
        $this->websiteRepository = $Qx;
        $this->websiteModel = $c4;
        $this->_storeManager = $Wl;
        $this->resultFactory = $UZ;
        $this->customerRepository = $kS;
        $this->reinitableConfig = $GK;
        $this->resource = $nQ;
        $this->messageManager = $c0;
        $this->backendHelper = $M8;
        $this->responseFactory = $XF;
        $this->_logger = $XD;
        $this->_response = $qm;
        $this->productMetadata = $a2;
        parent::__construct($PS, $Zg, $j8, $WU, $UX, $Tq, $A3, $vs, $rE, $a2, $OP);
    }
    public function getHiddenPhone($Rs)
    {
        $BK = "\170\170\x78\170\x78\x78\170" . substr($Rs, strlen($Rs) - 3);
        return $BK;
    }
    public function isBlank($Yk)
    {
        if (!empty($Yk)) {
            goto ohP;
        }
        return TRUE;
        ohP:
        return FALSE;
    }
    public function isCurlInstalled()
    {
        if (in_array("\143\x75\162\154", get_loaded_extensions())) {
            goto p73;
        }
        return 0;
        goto z5j;
        p73:
        return 1;
        z5j:
    }
    public function validatePhoneNumber($Rs)
    {
        if (!preg_match(MoIDPConstants::PATTERN_PHONE, $Rs, $he)) {
            goto x1b;
        }
        return TRUE;
        goto Ueu;
        x1b:
        return FALSE;
        Ueu:
    }
    public function getHiddenEmail($fx)
    {
        if (!(empty($fx) || trim($fx) === '')) {
            goto Tai;
        }
        return '';
        Tai:
        $O1 = strlen($fx);
        $N4 = substr($fx, 0, 1);
        $xC = strrpos($fx, "\x40");
        $xa = substr($fx, $xC - 1, $O1);
        $X5 = 1;
        g_V:
        if (!($X5 < $xC)) {
            goto xWf;
        }
        $N4 = $N4 . "\170";
        qcP:
        $X5++;
        goto g_V;
        xWf:
        $w9 = $N4 . $xa;
        return $w9;
    }
    public function setAdminSessionData($zg, $Yk)
    {
        return $this->adminSession->setData($zg, $Yk);
    }
    public function getAdminSessionData($zg, $at = false)
    {
        return $this->adminSession->getData($zg, $at);
    }
    public function setSessionData($zg, $Yk)
    {
        return $this->customerSession->setData($zg, $Yk);
    }
    public function getSessionData($zg, $at = false)
    {
        return $this->customerSession->getData($zg, $at);
    }
    public function unsetSessionData($zg, $at = false)
    {
        return $this->customerSession->unsetData($zg, $at);
    }
    public function setSessionDataForCurrentUser($zg, $Yk)
    {
        if ($this->customerSession->isLoggedIn()) {
            goto xjS;
        }
        if ($this->authSession->isLoggedIn()) {
            goto zzN;
        }
        goto mWX;
        xjS:
        $this->setSessionData($zg, $Yk);
        goto mWX;
        zzN:
        $this->setAdminSessionData($zg, $Yk);
        mWX:
    }
    public function isSPConfigured()
    {
        $Sl = $this->getStoreConfig(SPConstants::IDP_NAME);
        return $this->isBlank($Sl) ? FALSE : TRUE;
    }
    public function micr()
    {
        if (!$this->check_license_plan(4)) {
            goto Ep4;
        }
        return true;
        Ep4:
        $fx = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $zg = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        return !$this->isBlank($fx) && !$this->isBlank($zg) ? TRUE : FALSE;
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
    public function getAdminLoginUrl()
    {
        return $this->getAdminUrl("\x61\x64\155\x69\156\x68\x74\155\x6c\57\x61\165\x74\x68\x2f\x6c\157\147\x69\x6e");
    }
    public function getCustomerLoginUrl()
    {
        return $this->getFrontendUrl("\x63\x75\163\x74\157\x6d\x65\x72\x2f\141\x63\x63\157\165\156\164\x2f\x6c\x6f\147\151\156");
    }
    public function desanitizeCert($hj)
    {
        return SAML2Utilities::desanitize_certificate($hj);
    }
    public function sanitizeCert($hj)
    {
        return SAML2Utilities::sanitize_certificate($hj);
    }
    public function flushCache()
    {
        $I6 = array("\x64\142\137\144\144\x6c");
        foreach ($I6 as $Nv) {
            $this->cacheTypeList->cleanType($Nv);
            Nm1:
        }
        aZ8:
        foreach ($this->cacheFrontendPool as $H3) {
            $H3->getBackend()->clean();
            V0x:
        }
        WTP:
    }
    public function getFileContents($Gb)
    {
        return $this->fileSystem->fileGetContents($Gb);
    }
    public function putFileContents($Gb, $F2)
    {
        $this->fileSystem->filePutContents($Gb, $F2);
    }
    public function getLogoutUrl()
    {
        if (!$this->customerSession->isLoggedIn()) {
            goto q5Q;
        }
        return $this->getUrl("\x63\165\163\164\157\155\145\162\x2f\x61\x63\x63\x6f\165\156\x74\x2f\154\157\x67\x6f\165\164");
        q5Q:
        if (!$this->authSession->isLoggedIn()) {
            goto fsZ;
        }
        return $this->getAdminUrl("\141\144\x6d\151\x6e\x68\x74\155\x6c\x2f\141\x75\164\150\x2f\x6c\157\147\157\x75\164");
        fsZ:
        return "\x2f";
    }
    public function getcustomerLogoutUrl()
    {
        return $this->getBaseUrl() . "\143\x75\x73\164\157\155\x65\162\57\141\x63\143\157\165\156\164\x2f\x6c\157\147\157\x75\164";
    }
    public function reinitConfig()
    {
        $this->reinitableConfig->reinit();
    }
    public function mclv()
    {
        if (!$this->check_license_plan(4)) {
            goto JdD;
        }
        return true;
        JdD:
        $Av = $this->getStoreConfig(SPConstants::TOKEN);
        $A0 = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::SAMLSP_CKL), $Av);
        $r1 = $this->getStoreConfig(SPConstants::SAMLSP_LK);
        return $A0 == "\x74\x72\x75\x65" && !$this->isBlank($r1) ? TRUE : FALSE;
    }
    public function ccl()
    {
        $rP = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $Io = $this->getStoreConfig(SPConstants::API_KEY);
        $Ge = Curl::ccl($rP, $Io);
        return $Ge;
    }
    public function vml($qk)
    {
        $rP = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $Io = $this->getStoreConfig(SPConstants::API_KEY);
        $Ge = Curl::vml($rP, $Io, $qk, $this->getBaseUrl());
        return $Ge;
    }
    public function mius()
    {
        $rP = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $Io = $this->getStoreConfig(SPConstants::API_KEY);
        $Av = $this->getStoreConfig(SPConstants::TOKEN);
        $qk = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::SAMLSP_LK), $Av);
        $Ge = Curl::mius($rP, $Io, trim($qk));
        return $Ge;
    }
    public function getB2bStoreUrl()
    {
        $c7 = $this->getStoreConfig(SPConstants::B2B_STORE_URL);
        return !$this->isBlank($c7) ? $c7 : '';
    }
    public function getB2cStoreUrl()
    {
        $hU = $this->getStoreConfig(SPConstants::B2C_STORE_URL);
        return !$this->isBlank($hU) ? $hU : '';
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
        $G7 = $this->getStoreConfig(SPConstants::SAML_SLO_URL);
        return $this->isBlank($G7) ? FALSE : TRUE;
    }
    public function getCustomer($lA)
    {
        return $this->customerRepository->getById($lA);
    }
    public function getAdminUserById($lA)
    {
        $user = $this->userFactory->create()->load($lA);
        return $user;
    }
    public function getBaseUrlFromUrl($Y1)
    {
        $this->log_debug("\40\x53\160\125\164\151\x6c\x69\x74\x79\x3a\40\x67\145\164\x42\x61\x73\x65\x55\162\154\106\x72\x6f\155\125\162\x6c\50\51\40\x66\157\x72\72" . $Y1);
        $RT = $this->_storeManager->getStores();
        foreach ($RT as $SR) {
            $JY = $SR->getBaseUrl();
            $v1 = strpos($Y1, $JY);
            if (!($v1 !== false)) {
                goto j3r;
            }
            $this->log_debug("\123\x70\125\164\151\x6c\x69\x74\171\40\x3a\x20\x67\145\164\102\x61\x73\145\125\x72\154\106\x72\157\155\x55\162\x6c\x28\x29\x2e\40" . $JY);
            return $JY;
            j3r:
            SVg:
        }
        Los:
        $Y1 = parse_url($Y1, PHP_URL_HOST);
        $this->log_debug("\123\x70\x55\164\151\154\151\164\x79\x3a\x20\147\145\164\102\141\163\145\125\x72\154\x46\x72\x6f\155\125\162\x6c\50\51\72" . $Y1 . "\57");
        return $Y1;
    }
    public function getStoreById($lA)
    {
        return $this->_storeManager->getStore($lA);
    }
    public function getWebsiteCode()
    {
        return $this->_storeManager->getWebsite()->getCode();
    }
    public function getWebsiteName($iM)
    {
        $Lw = $this->_websiteModel->load($iM, "\167\x65\x62\163\x69\x74\145\x5f\x69\x64");
        $u1 = $Lw->getData();
        return $u1[0]["\x6e\x61\155\145"];
    }
    public function getWebsiteById($lA)
    {
        return $this->websiteRepository->getById($lA);
    }
    public function getUrl($JY, $As = array())
    {
        return parent::getUrl($JY, $As);
    }
    public function insertRowInTable($ag, $F2)
    {
        $this->resource->getConnection()->insertMultiple($ag, $F2);
    }
    public function updateColumnInTable($ag, $GM, $sj, $ZS, $xl)
    {
        $this->log_debug("\x75\x70\x64\x61\x74\145\103\x6f\154\x75\155\156\111\x6e\124\x61\x62\154\145" . $GM);
        $Os = $this->resource->getConnection();
        if (!($Os->tableColumnExists($ag, $GM) === false)) {
            goto itx;
        }
        $Os->addColumn($ag, $GM, array("\x74\171\160\x65" => "\x74\x65\170\164", "\x6e\x75\x6c\154\141\142\154\145" => false, "\x6c\145\156\x67\x74\150" => 255, "\141\146\x74\145\x72" => null, "\x63\157\x6d\155\145\x6e\164" => $GM));
        itx:
        $this->resource->getConnection()->update($ag, [$GM => $sj], [$ZS . "\x20\75\x20\77" => $xl]);
    }
    public function updateRowInTable($ag, $mI, $ZS, $xl)
    {
        $this->log_debug("\165\x70\x64\x61\164\145\x52\157\x77\111\156\124\141\142\154\145");
        $this->resource->getConnection()->update($ag, $mI, [$ZS . "\x20\75\40\x3f" => $xl]);
    }
    public function getValueFromTableSQL($ag, $df, $ZS, $xl)
    {
        $Os = $this->resource->getConnection();
        $nq = "\x53\x45\114\x45\x43\124\40" . $df . "\40\106\122\117\115\40" . $ag . "\40\127\110\x45\122\x45\x20" . $ZS . "\40\x3d\x20" . $xl;
        $this->log_debug("\123\x51\x4c\72\x20" . $nq);
        $Up = $Os->fetchOne($nq);
        $this->log_debug("\162\x65\x73\x75\154\x74\40\163\x71\x6c\x3a\40" . $Up);
        return $Up;
    }
    public function checkIfFlowStartedFromBackend($qY)
    {
        $zo = $this->helperBackend->getAreaFrontName();
        if (!str_contains($qY, $zo)) {
            goto H_t;
        }
        $this->log_debug("\x63\150\145\x63\x6b\x49\x66\x46\x6c\x6f\167\x53\x74\x61\x72\x74\x65\144\x46\162\157\x6d\x42\x61\x63\x6b\145\x6e\x64\72\40\164\x72\x75\x65");
        return true;
        H_t:
        $this->log_debug("\x63\x68\145\143\153\111\x66\x46\154\157\167\x53\164\x61\162\x74\x65\144\106\x72\157\155\102\141\143\x6b\x65\x6e\144\x3a\x20\x66\x61\154\163\x65");
        return false;
    }
    public function getadmincode()
    {
        $zo = $this->helperBackend->getAreaFrontName();
        return $zo;
    }
    public function handle_upload_metadata($Gb, $JY, $As)
    {
        if (!(!empty($Gb) || !empty($JY))) {
            goto kzh;
        }
        if (!empty($Gb["\164\155\x70\137\156\x61\155\145"])) {
            goto MjL;
        }
        $JY = filter_var($JY, FILTER_SANITIZE_URL);
        $I7 = array("\163\163\x6c" => array("\x76\145\x72\151\146\171\x5f\160\x65\x65\162" => false, "\x76\145\162\151\146\171\x5f\160\145\145\x72\x5f\156\141\x6d\x65" => false));
        if (empty($JY)) {
            goto iqS;
        }
        $Gb = file_get_contents($JY, false, stream_context_create($I7));
        goto PZj;
        iqS:
        return;
        PZj:
        goto BE6;
        MjL:
        $Gb = file_get_contents($Gb["\164\x6d\x70\137\156\141\x6d\145"]);
        BE6:
        $this->upload_metadata($Gb, $As);
        kzh:
    }
    public function upload_metadata($Gb, $As)
    {
        $L5 = new DOMDocument();
        $L5->loadXML($Gb);
        restore_error_handler();
        $Wn = $L5->firstChild;
        if (!empty($Wn)) {
            goto NyB;
        }
        return;
        goto BJD;
        NyB:
        $Xe = new IDPMetadataReader($L5);
        $l2 = $Xe->getIdentityProviders();
        if (!empty($l2)) {
            goto qB4;
        }
        return;
        qB4:
        foreach ($l2 as $zg => $iF) {
            $dS = $iF->getLoginURL("\x48\124\x54\x50\55\x52\145\x64\x69\162\x65\143\164");
            $zZ = $iF->getEntityID();
            $Af = $iF->getSigningCertificate();
            $Xg = "\x73\141\x6d\x6c";
            $Zd = array("\x73\x61\155\154\x49\163\163\x75\x65\162" => !empty($zZ) ? $zZ : 0, "\163\163\157\165\162\x6c" => !empty($dS) ? $dS : 0, "\154\157\147\151\x6e\102\151\156\144\x69\156\147\124\171\x70\145" => "\x48\164\x74\x70\x52\145\144\151\162\145\143\164", "\143\145\x72\x74\x69\146\x69\143\141\x74\x65" => !empty($Af) ? $Af[0] : 0);
            $this->generic_update_query($Xg, $Zd, $As);
            goto C9p;
            L3k:
        }
        C9p:
        return;
        BJD:
    }
    public function generic_update_query($Xg, $Zd, $As)
    {
        $this->log_debug("\x49\x6e\40\147\145\156\x65\162\151\x63\x5f\165\160\x64\x61\x74\145\137\161\x75\x65\162\x79\40\146\165\x6e\143\x74\151\157\x6e");
        $O5 = json_encode($Zd);
        if (!empty($As["\163\141\155\154\x5f\x69\144\x65\156\x74\151\x74\171\x5f\156\141\x6d\145"])) {
            goto JPk;
        }
        $As["\x73\141\x6d\x6c\137\151\144\x65\x6e\x74\151\x74\x79\137\156\x61\155\x65"] = $As["\163\145\154\145\143\164\x65\144\137\x70\162\x6f\x76\151\x64\x65\x72"];
        JPk:
        $Lk = trim($As["\x73\141\155\154\x5f\151\144\145\156\x74\x69\x74\x79\137\x6e\141\155\x65"]);
        $Lw = $this->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\x69\144\160\137\156\x61\155\x65"] === $Lk)) {
                goto F2Y;
            }
            $ft = $fR->getData();
            F2Y:
            Iul:
        }
        Sho:
        foreach ($Zd as $zg => $Yk) {
            if (!($zg == "\163\141\155\x6c\x49\x73\163\x75\145\162")) {
                goto z71;
            }
            $xb = trim($Yk);
            z71:
            if (!($zg == "\163\163\157\x75\x72\154")) {
                goto zk7;
            }
            $ln = trim($Yk);
            zk7:
            if (!($zg == "\x6c\157\147\151\156\x42\151\x6e\144\151\156\147\x54\171\x70\145")) {
                goto P6r;
            }
            $dq = trim($Yk);
            P6r:
            if (!($zg == "\143\145\162\164\x69\x66\151\x63\x61\164\145")) {
                goto iuD;
            }
            $w_ = SAML2Utilities::sanitize_certificate(trim($Yk));
            iuD:
            PeY:
        }
        bdo:
        $WY = !empty($As["\x73\141\155\154\137\x6c\x6f\x67\157\165\x74\137\x75\162\154"]) ? trim($As["\163\141\x6d\154\137\154\157\147\x6f\165\x74\137\x75\162\x6c"]) : '';
        $B3 = "\110\164\164\x70\x52\x65\x64\x69\162\145\143\164";
        $a5 = !empty($As["\x73\141\x6d\x6c\x5f\162\145\x73\160\x6f\x6e\163\145\137\163\x69\147\156\x65\x64"]) && $As["\163\x61\155\x6c\137\162\x65\x73\x70\x6f\156\x73\145\137\163\x69\147\156\145\144"] == "\131\145\x73" ? 1 : 0;
        $f4 = !empty($As["\x73\141\x6d\154\x5f\141\163\163\x65\162\164\x69\x6f\156\x5f\163\x69\147\x6e\145\144"]) && $As["\x73\141\155\154\137\141\x73\x73\145\162\x74\x69\157\156\137\x73\x69\x67\156\x65\144"] == "\x59\145\163" ? 1 : 0;
        $o9 = !empty($ft["\x73\x68\x6f\x77\x5f\x61\x64\x6d\x69\x6e\137\154\151\x6e\x6b"]) && $ft["\163\x68\157\x77\137\x61\x64\x6d\151\x6e\x5f\154\x69\156\153"] == true ? 1 : 0;
        $vh = !empty($ft["\163\150\x6f\x77\x5f\143\x75\163\x74\157\155\145\162\x5f\x6c\151\x6e\x6b"]) && $ft["\163\150\x6f\x77\137\x63\x75\163\x74\x6f\x6d\x65\x72\x5f\154\151\x6e\x6b"] == true ? 1 : 0;
        $w8 = !empty($ft["\141\x75\164\x6f\x5f\143\162\x65\141\x74\145\137\x61\x64\155\x69\156\137\165\163\x65\162\x73"]) && $ft["\141\165\164\157\137\143\162\145\x61\x74\145\137\141\144\155\151\x6e\x5f\x75\163\x65\x72\163"] == true ? 1 : 0;
        $Gd = !empty($ft["\141\165\164\x6f\137\143\x72\145\x61\164\145\x5f\143\165\163\164\157\x6d\145\x72\163"]) && $ft["\141\165\164\x6f\x5f\143\x72\145\x61\164\x65\x5f\143\x75\163\164\157\155\x65\162\163"] == true ? 1 : 0;
        $h0 = !empty($ft["\x64\x69\163\x61\x62\154\145\137\x62\62\x63"]) && $ft["\144\x69\163\141\x62\154\145\x5f\142\x32\143"] == true ? 1 : 0;
        $M1 = !empty($ft["\146\157\x72\x63\145\x5f\141\x75\x74\x68\145\156\x74\x69\x63\x61\164\151\x6f\x6e\137\167\151\x74\150\x5f\151\144\x70"]) && $ft["\146\157\x72\143\x65\137\141\165\164\150\145\x6e\164\x69\143\x61\164\x69\157\156\x5f\167\151\164\x68\x5f\x69\144\x70"] == true ? 1 : 0;
        $M3 = !empty($ft["\141\x75\x74\x6f\x5f\x72\145\144\x69\162\x65\x63\164\137\164\157\137\151\144\x70"]) && $ft["\x61\165\x74\x6f\137\162\145\x64\151\162\x65\143\164\137\x74\x6f\x5f\x69\x64\x70"] == true ? 1 : 0;
        $qr = !empty($ft["\x6c\x69\156\x6b\x5f\164\x6f\x5f\151\156\x69\164\x69\x61\x74\145\x5f\x73\163\157"]) && $ft["\x6c\x69\156\153\x5f\164\x6f\x5f\x69\156\151\164\151\x61\164\145\x5f\163\x73\x6f"] == true ? 1 : 0;
        $S8 = !empty($ft["\x75\160\x64\x61\x74\145\x5f\x61\x74\164\x72\151\142\x75\164\x65\x73\x5f\157\156\x5f\154\157\147\151\x6e"]) ? $ft["\165\x70\x64\141\164\145\137\141\164\164\x72\151\x62\165\x74\145\x73\x5f\x6f\x6e\x5f\154\x6f\x67\x69\x6e"] : "\x75\x6e\x63\x68\x65\x63\x6b\145\x64";
        $qx = !empty($ft["\x63\x72\x65\x61\x74\x65\137\x6d\x61\147\145\x6e\x74\157\137\x61\x63\x63\157\x75\156\164\137\142\x79"]) ? $ft["\143\162\x65\x61\164\145\x5f\155\x61\x67\x65\156\164\157\137\x61\143\x63\x6f\165\x6e\164\x5f\142\x79"] : '';
        $cE = !empty($ft["\x65\x6d\x61\x69\x6c\137\141\164\164\162\151\142\165\164\x65"]) ? $ft["\145\x6d\x61\151\x6c\137\x61\164\x74\x72\151\142\165\164\145"] : '';
        $x_ = !empty($ft["\x75\163\x65\162\x6e\x61\x6d\x65\x5f\141\164\x74\162\151\x62\165\x74\x65"]) ? $ft["\x75\163\145\162\156\141\155\x65\x5f\x61\164\x74\162\x69\x62\165\164\145"] : '';
        $Fw = !empty($ft["\x66\151\x72\x73\164\156\x61\x6d\145\137\x61\164\x74\162\151\142\165\164\145"]) ? $ft["\x66\x69\162\x73\x74\x6e\141\x6d\145\x5f\x61\x74\x74\x72\151\142\x75\164\x65"] : '';
        $tV = !empty($ft["\154\x61\x73\x74\x6e\141\155\x65\137\141\164\x74\162\x69\x62\165\164\145"]) ? $ft["\154\x61\x73\x74\x6e\x61\155\145\x5f\x61\164\164\x72\x69\x62\165\x74\145"] : '';
        $Wr = !empty($ft["\x67\162\157\165\x70\x5f\141\x74\x74\x72\151\x62\x75\x74\x65"]) ? $ft["\147\x72\157\165\160\x5f\141\164\x74\x72\x69\x62\x75\164\x65"] : '';
        $T5 = !empty($ft["\142\151\x6c\154\151\x6e\147\137\143\151\164\x79\137\x61\164\164\162\151\x62\165\164\x65"]) ? $ft["\142\x69\154\x6c\x69\156\147\x5f\143\x69\x74\171\x5f\x61\164\x74\162\x69\142\x75\164\145"] : '';
        $ab = !empty($ft["\x62\x69\x6c\154\x69\x6e\x67\x5f\163\x74\141\x74\x65\137\141\164\x74\162\151\142\x75\x74\145"]) ? $ft["\142\151\154\154\x69\156\x67\137\163\x74\x61\164\145\x5f\141\164\x74\162\x69\x62\x75\x74\x65"] : '';
        $TC = !empty($ft["\x62\x69\154\x6c\x69\156\x67\137\x63\x6f\165\x6e\164\162\x79\x5f\141\x74\x74\162\151\142\x75\x74\x65"]) ? $ft["\142\151\x6c\154\x69\156\147\x5f\x63\x6f\165\x6e\164\x72\x79\x5f\141\x74\164\x72\x69\142\x75\164\x65"] : '';
        $Au = !empty($ft["\x62\151\154\154\x69\x6e\x67\137\x61\x64\144\162\x65\x73\163\137\141\x74\x74\x72\x69\142\165\164\x65"]) ? $ft["\142\x69\x6c\154\151\x6e\147\x5f\141\144\x64\x72\x65\163\163\x5f\141\x74\x74\x72\151\x62\165\164\x65"] : '';
        $hX = !empty($ft["\142\x69\154\154\151\x6e\147\x5f\x70\x68\x6f\156\x65\137\141\164\164\x72\151\142\x75\x74\x65"]) ? $ft["\142\151\x6c\154\x69\x6e\x67\137\x70\x68\x6f\x6e\x65\x5f\x61\164\x74\162\151\x62\x75\x74\145"] : '';
        $Sr = !empty($ft["\142\151\x6c\154\151\156\x67\137\172\x69\x70\x5f\x61\164\x74\x72\151\x62\x75\x74\145"]) ? $ft["\x62\x69\154\x6c\x69\156\x67\x5f\x7a\x69\160\x5f\x61\x74\164\x72\151\x62\x75\x74\145"] : '';
        $J2 = !empty($ft["\163\150\151\160\x70\151\156\x67\137\x63\x69\x74\x79\137\141\x74\164\162\151\142\165\164\145"]) ? $ft["\163\x68\151\x70\160\x69\x6e\x67\137\143\151\164\171\137\141\164\x74\x72\151\x62\165\164\x65"] : '';
        $mG = !empty($ft["\163\150\151\160\160\x69\x6e\x67\137\163\x74\x61\x74\145\137\x61\x74\x74\x72\151\x62\165\x74\145"]) ? $ft["\x73\x68\151\x70\x70\151\x6e\147\137\x73\164\x61\164\145\137\141\164\164\162\x69\142\165\164\x65"] : '';
        $iz = !empty($ft["\x73\x68\x69\x70\160\x69\156\147\137\143\x6f\165\156\x74\x72\x79\x5f\141\164\164\x72\x69\x62\x75\x74\145"]) ? $ft["\x73\150\x69\x70\160\151\156\147\137\x63\x6f\165\x6e\164\162\x79\137\x61\x74\164\162\x69\x62\165\164\x65"] : '';
        $mq = !empty($ft["\163\150\151\160\x70\x69\156\147\137\x61\144\144\162\145\x73\163\x5f\x61\164\x74\x72\151\x62\165\164\145"]) ? $ft["\x73\150\x69\160\x70\151\156\147\137\x61\144\x64\162\x65\163\x73\x5f\x61\x74\164\162\151\x62\165\164\145"] : '';
        $Qb = !empty($ft["\163\x68\151\160\160\151\156\147\x5f\x70\x68\x6f\156\145\x5f\x61\164\x74\x72\x69\142\x75\164\145"]) ? $ft["\163\x68\151\x70\x70\151\x6e\147\x5f\160\x68\x6f\x6e\145\x5f\141\x74\164\x72\151\x62\165\164\145"] : '';
        $gE = !empty($ft["\x73\150\x69\160\x70\151\x6e\147\x5f\x7a\x69\x70\137\141\164\164\x72\151\142\x75\x74\145"]) ? $ft["\163\150\x69\160\160\151\x6e\147\137\x7a\x69\160\137\141\x74\x74\x72\151\x62\x75\164\x65"] : '';
        $lL = !empty($ft["\142\62\142\137\x61\164\x74\x72\x69\x62\x75\x74\145"]) ? $ft["\142\x32\x62\x5f\x61\164\164\x72\x69\142\x75\164\145"] : '';
        $Rr = !empty($ft["\x63\x75\x73\x74\157\155\x5f\x74\141\142\x6c\145\x6e\x61\x6d\145"]) ? $ft["\143\x75\x73\164\x6f\x6d\x5f\x74\141\142\x6c\x65\x6e\141\155\x65"] : '';
        $Rv = !empty($ft["\143\165\x73\x74\x6f\x6d\x5f\x61\x74\x74\x72\x69\x62\x75\x74\145\163"]) ? $ft["\143\165\x73\x74\x6f\155\x5f\x61\164\164\x72\151\142\x75\164\x65\x73"] : '';
        $i3 = !empty($ft["\144\x6f\137\x6e\x6f\164\137\141\165\164\157\143\x72\145\141\164\x65\x5f\151\x66\x5f\162\157\154\145\x73\x5f\156\157\164\x5f\155\141\x70\x70\145\144"]) && $ft["\144\157\137\156\157\x74\x5f\141\x75\164\157\143\162\x65\x61\164\145\137\x69\146\x5f\x72\x6f\154\145\163\137\156\157\164\x5f\x6d\x61\x70\160\x65\144"] == true ? 1 : 0;
        $AF = !empty($ft["\165\160\144\x61\x74\x65\137\x62\x61\143\x6b\x65\x6e\144\x5f\162\x6f\x6c\145\163\137\x6f\156\x5f\x73\x73\157"]) && $ft["\x75\160\x64\141\x74\145\137\x62\141\x63\x6b\x65\156\x64\137\x72\157\154\x65\163\137\x6f\156\x5f\163\x73\157"] == true ? 1 : 0;
        $Jg = !empty($ft["\165\x70\x64\141\164\x65\x5f\x66\162\x6f\x6e\x74\145\156\x64\137\147\x72\157\x75\160\x73\137\x6f\x6e\x5f\x73\163\x6f"]) && $ft["\165\x70\144\141\164\145\137\x66\x72\x6f\156\x74\145\x6e\144\137\147\162\157\165\x70\x73\x5f\x6f\156\137\163\x73\x6f"] == true ? 1 : 0;
        $Vc = !empty($ft["\x64\x65\146\x61\165\154\x74\x5f\147\x72\x6f\x75\160"]) ? $ft["\144\145\x66\x61\x75\x6c\x74\x5f\147\x72\157\165\x70"] : '';
        $LQ = !empty($ft["\x64\145\x66\141\165\154\164\137\162\x6f\154\145"]) ? $ft["\144\x65\146\141\x75\x6c\x74\137\x72\x6f\154\145"] : '';
        $CL = !empty($ft["\x67\x72\x6f\x75\160\163\x5f\155\x61\160\160\x65\x64"]) ? $ft["\x67\162\x6f\x75\160\x73\x5f\155\x61\160\160\145\x64"] : '';
        $sG = !empty($ft["\x72\x6f\154\x65\163\137\155\x61\160\x70\x65\144"]) ? $ft["\162\157\x6c\x65\x73\137\x6d\x61\x70\x70\145\x64"] : '';
        $Uo = !empty($ft["\x73\x61\155\154\137\154\x6f\147\x6f\x75\164\137\162\145\144\151\162\145\143\164\x5f\x75\x72\x6c"]) ? $ft["\x73\141\x6d\x6c\137\154\x6f\147\x6f\165\164\x5f\162\145\144\151\x72\145\143\164\x5f\x75\162\154"] : '';
        $wj = !empty($ft["\x73\141\x6d\154\x5f\x65\156\x61\x62\154\x65\x5f\x62\151\154\154\x69\156\147\x61\156\144\163\x68\x69\160\160\x69\156\147"]) ? $ft["\x73\x61\155\x6c\137\145\x6e\141\142\154\x65\137\142\151\x6c\154\x69\x6e\147\141\156\144\x73\x68\151\160\x70\x69\x6e\x67"] : "\x6e\157\156\x65";
        $fE = !empty($ft["\x73\x61\155\154\137\163\141\x6d\145\141\163\x62\151\x6c\154\x69\x6e\147"]) ? $ft["\163\141\155\x6c\137\x73\x61\155\145\x61\163\x62\x69\x6c\154\151\x6e\147"] : "\x6e\157\x6e\145";
        if (!is_null($ft)) {
            goto GAX;
        }
        $this->checkIdpLimit();
        goto Y3v;
        GAX:
        $this->deleteIDPApps((int) $ft["\151\x64"]);
        Y3v:
        if (empty($Lk)) {
            goto UHw;
        }
        $this->setIDPApps($Lk, $xb, $ln, $dq, $WY, $B3, $w_, $a5, $f4, $o9, $vh, $w8, $Gd, $h0, $M1, $M3, $qr, $S8, $qx, $cE, $x_, $Fw, $tV, $Wr, $T5, $ab, $TC, $Au, $hX, $Sr, $J2, $mG, $iz, $mq, $Qb, $gE, $lL, $Rr, $Rv, $i3, $AF, $Jg, $Vc, $LQ, $CL, $sG, $Uo, $wj, $fE);
        UHw:
        $this->setStoreConfig(SPConstants::IDP_NAME, $Lk);
        $this->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $Lk);
        $this->reinitConfig();
    }
    public function isAllPageAutoRedirectEnabled($Mu)
    {
        $R5 = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($R5 == $Mu) {
            goto Fs2;
        }
        return 0;
        goto QZr;
        Fs2:
        return $this->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        QZr:
    }
    public function isAutoRedirectEnabled($Mu)
    {
        $R5 = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($R5 == $Mu) {
            goto N2Z;
        }
        return 0;
        goto ZUQ;
        N2Z:
        return $this->getStoreConfig(SPConstants::AUTO_REDIRECT);
        ZUQ:
    }
    public function result($JY)
    {
        $this->responseFactory->create()->setRedirect($JY)->sendResponse();
        exit;
    }
    public function check_license_plan($xW)
    {
        return $this->get_license_plan() >= $xW;
    }
    public function get_license_plan()
    {
        $wW = SPConstants::LICENSE_PLAN;
        return $wW === "\155\141\x67\145\x6e\164\x6f\137\x73\x61\155\154\137\164\162\151\x61\x6c\137\x70\154\x61\156" ? 4 : ($wW === "\x6d\141\x67\145\x6e\164\157\x5f\163\141\155\154\x5f\x65\156\164\x65\x72\160\x72\151\163\145\137\x70\x6c\141\x6e" ? 3 : ($wW === "\155\x61\x67\145\x6e\164\157\137\x73\x61\155\154\137\x70\162\x65\155\151\165\x6d\137\160\154\x61\156" ? 2 : ($wW === "\155\x61\147\x65\x6e\164\x6f\x5f\163\141\155\154\137\163\164\141\156\x64\x61\x72\144\x5f\x70\x6c\x61\156" ? 1 : 0)));
    }
    public function customlog($vg)
    {
        $this->isLogEnable() ? $this->_logger->debug($vg) : NULL;
    }
    public function defaultlog($vg)
    {
        $this->logger->debug($vg);
    }
    public function isCustomLogExist()
    {
        if ($this->fileSystem->isExists("\56\56\57\166\x61\x72\x2f\x6c\157\147\57\x6d\x6f\x5f\163\x61\x6d\154\x2e\x6c\157\147")) {
            goto VAz;
        }
        if ($this->fileSystem->isExists("\x76\x61\162\57\154\157\147\57\155\157\x5f\163\141\x6d\x6c\x2e\154\x6f\x67")) {
            goto HF7;
        }
        goto Nhj;
        VAz:
        return 1;
        goto Nhj;
        HF7:
        return 1;
        Nhj:
        return 0;
    }
    public function deleteCustomLogFile()
    {
        if ($this->fileSystem->isExists("\x2e\x2e\x2f\x76\141\x72\57\154\157\147\x2f\x6d\157\x5f\163\x61\155\x6c\x2e\x6c\x6f\x67")) {
            goto sn2;
        }
        if ($this->fileSystem->isExists("\x76\x61\162\57\x6c\157\x67\x2f\x6d\157\x5f\x73\x61\155\154\x2e\154\157\x67")) {
            goto Qg3;
        }
        goto tjB;
        sn2:
        $this->fileSystem->deleteFile("\x2e\56\x2f\166\x61\x72\57\154\157\147\57\155\157\137\x73\141\x6d\154\56\x6c\157\147");
        goto tjB;
        Qg3:
        $this->fileSystem->deleteFile("\x76\141\162\x2f\x6c\157\x67\x2f\155\x6f\137\x73\141\155\x6c\x2e\x6c\x6f\x67");
        tjB:
    }
    public function isLogEnable()
    {
        return $this->getStoreConfig(SPConstants::ENABLE_DEBUG_LOG);
    }
    public function log_debug($ZX = '', $oY = null)
    {
        if (is_object($ZX)) {
            goto MA0;
        }
        $this->customlog("\115\117\x20\123\101\115\x4c\x20\x3a\x20" . $ZX);
        goto Ged;
        MA0:
        $this->customlog("\x4d\117\40\123\101\x4d\x4c\x20\40\72\x20" . print_r($oY, true));
        Ged:
        if (!($oY != null)) {
            goto N2z;
        }
        $this->customlog("\x4d\x4f\x20\123\101\115\x4c\x20\72\40" . var_export($oY, true));
        N2z:
    }
    public function update_status($qk)
    {
        $rP = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $Io = $this->getStoreConfig(SPConstants::API_KEY);
        $Ge = Curl::update_status($rP, $Io, $qk, $this->getBaseUrl());
        return $Ge;
    }
    public function errorPageRedirection($YM)
    {
        $this->messageManager->addErrorMessage($YM);
        $Y0 = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $Y0->setUrl($this->urlInterface->getUrl("\156\157\162\157\165\x74\x65"));
        return $Y0;
    }
    public function redirectURL($JY)
    {
        return $this->_response->setRedirect($JY)->sendResponse();
    }
    public function check_license_expiry_date()
    {
        $zg = $this->getStoreConfig(SPConstants::TOKEN);
        $cp = $this->getStoreConfig(SPConstants::LICENSE_EXPIRY_DATE);
        $uR = null;
        if ($cp == null) {
            goto xKS;
        }
        $uR = AESEncryption::decrypt_data($cp, $zg);
        goto GkE;
        xKS:
        $Ge = json_decode(self::ccl(), true);
        $uR = array_key_exists("\154\x69\143\x65\x6e\x73\145\x45\170\160\151\x72\x79", $Ge) ? strtotime($Ge["\154\x69\143\145\156\163\145\105\x78\160\151\x72\171"]) === false ? null : strtotime($Ge["\x6c\151\x63\x65\x6e\163\145\x45\170\x70\x69\162\x79"]) : null;
        if ($this->isBlank($uR)) {
            goto ejK;
        }
        $this->setStoreConfig(SPConstants::LICENSE_EXPIRY_DATE, AESEncryption::encrypt_data($uR, $zg));
        ejK:
        GkE:
        $Jm = new \DateTime("\100{$uR}");
        $Wv = new \DateTime();
        $bh = $Wv->diff($Jm)->format("\45\x72\45\141");
        if (!($bh <= 30)) {
            goto gh8;
        }
        $this->flushCache();
        $RZ = $this->getStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT);
        if ($bh > 7) {
            goto kw0;
        }
        if ($bh <= 7 && $bh > 0) {
            goto Umi;
        }
        if ($bh <= -5) {
            goto aYD;
        }
        goto cpJ;
        kw0:
        if (!($RZ == null)) {
            goto EVd;
        }
        $this->license_expiry_notification_before_expiry($bh);
        $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, $bh);
        EVd:
        goto cpJ;
        Umi:
        if (!($RZ == null || $RZ > 7)) {
            goto Aev;
        }
        $this->license_expiry_notification_before_expiry($bh);
        $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, $bh);
        Aev:
        goto cpJ;
        aYD:
        if (!($RZ == null || $RZ >= 0)) {
            goto Ibb;
        }
        $this->license_expiry_notification_after_expiry();
        $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, 0);
        Ibb:
        cpJ:
        gh8:
        return $bh;
    }
    public function license_expiry_notification_before_expiry($oM)
    {
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $SF = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $Ge = "\110\x65\154\x6c\x6f\54\x3c\x62\x72\76\74\x62\162\x3e\x54\150\x69\163\40\145\x6d\x61\x69\x6c\x20\x69\x73\x20\164\x6f\x20\x6e\x6f\164\x69\146\x79\x20\x79\x6f\x75\x20\x74\x68\141\x74\x20\171\x6f\x75\x72\x20\61\x20\x79\145\141\162\40\154\x69\x63\x65\156\x73\145\40\146\x6f\x72\40\x4d\141\147\145\156\164\157\40\x53\x41\115\x4c\x20\123\123\117\40\x50\154\165\x67\151\156\x20\x77\151\154\x6c\x20\x65\170\x70\151\162\145\xd\12\x20\x20\x20\x20\x20\x20\40\x20\40\40\40\40\40\x20\40\x20\40\x20\x20\40\x20\x20\40\x20\x20\x20\167\x69\x74\x68\x69\156\40" . $oM . "\40\144\141\x79\163\x2e\x3c\x62\x72\x3e\x3c\x62\x72\76\xd\12\40\x20\x20\40\x20\40\x20\40\40\x20\x20\40\x20\x20\x20\x20\40\40\x20\x20\40\x20\x20\40\x20\x20\x3c\142\162\76\x3c\142\162\x3e\x43\157\x6e\x74\x61\x63\x74\40\165\163\40\x61\164\40\74\141\40\150\162\x65\146\75\x27\x6d\141\x69\154\164\157\x3a\155\x61\x67\145\x6e\x74\157\163\x75\x70\160\157\162\164\100\170\x65\143\165\162\151\146\x79\56\143\x6f\x6d\47\x3e\x6d\141\x67\145\156\164\157\163\x75\160\160\x6f\x72\164\x40\170\145\143\165\162\151\x66\171\x2e\143\157\x6d\74\57\141\x3e\x20\151\x6e\40\x6f\x72\144\x65\162\x20\x74\157\x20\162\x65\156\145\167\40\x79\157\165\x72\x20\x6c\151\x63\145\156\163\x65\56\x3c\142\x72\x3e\x3c\142\162\76\x54\150\x61\156\153\163\54\x3c\x62\162\76\155\x69\x6e\151\x4f\162\141\156\x67\x65";
        $XP = "\114\151\x63\x65\156\163\x65\40\x45\x78\160\151\x72\171\40\55\x20\x4d\141\x67\x65\156\x74\x6f\40\123\101\x4d\114\40\x53\x53\x4f\40\x50\x6c\165\x67\151\x6e";
        CURL::notify($rP, $Io, $SF, $Ge, $XP);
    }
    public function license_expiry_notification_after_expiry()
    {
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $SF = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $Ge = "\110\x65\x6c\154\157\x2c\x3c\x62\162\76\x3c\142\x72\x3e\131\157\x75\x72\x20\x31\40\171\145\x61\162\40\x6c\x69\143\x65\156\x73\x65\40\146\x6f\x72\x20\x4d\x61\x67\145\156\164\157\x20\x53\101\x4d\114\x20\x53\x53\x4f\40\120\154\x75\147\x69\156\40\x68\141\x73\40\x65\170\x70\151\x72\x65\144\56\74\142\162\x3e\74\142\162\76\xd\xa\x9\x9\11\11\11\x9\103\x6f\156\x74\x61\x63\x74\40\x75\x73\40\141\x74\x20\x3c\x61\x20\150\x72\145\146\x3d\x27\x6d\141\x69\154\164\157\x3a\x6d\141\x67\145\156\164\157\x73\x75\x70\160\x6f\x72\164\x40\170\x65\143\165\162\x69\146\171\x2e\143\157\155\x27\x3e\155\x61\x67\x65\156\x74\x6f\163\x75\x70\x70\157\x72\x74\x40\x78\145\x63\165\x72\x69\146\171\x2e\143\157\x6d\x3c\57\x61\76\x20\151\x6e\40\x6f\x72\x64\145\x72\x20\164\x6f\40\162\145\x6e\x65\x77\40\171\x6f\165\x72\40\154\151\x63\145\x6e\163\x65\x2e\74\142\162\x3e\74\142\x72\76\124\150\141\x6e\153\x73\54\x3c\142\162\x3e\155\x69\156\151\117\x72\141\156\147\145";
        $XP = "\x4c\x69\x63\145\x6e\x73\x65\x20\105\170\x70\x69\x72\x65\x64\x20\55\x20\115\141\147\145\156\x74\157\x20\123\x41\115\114\x20\x53\123\117\40\120\x6c\165\x67\151\156\x20";
        CURL::notify($rP, $Io, $SF, $Ge, $XP);
    }
    public function getMagnetoVersion()
    {
        return $this->productMetadata->getVersion();
    }
    public function update_customer_id_in_customer_visitor($w3)
    {
        $this->log_debug("\x55\x70\144\141\x74\151\156\147\x20\143\x75\163\164\157\x6d\x65\162\x5f\x76\x69\x73\x69\164\157\x72\x20\x74\x61\142\x6c\x65");
        $Os = $this->resource->getConnection();
        $oo = $Os->select()->from("\143\165\163\164\x6f\x6d\145\162\137\166\x69\x73\151\164\157\162", "\103\117\125\116\124\x28\52\x29");
        $E3 = $Os->fetchOne($oo);
        $this->resource->getConnection()->update("\143\x75\163\164\x6f\155\x65\x72\x5f\166\151\163\x69\164\157\x72", ["\x63\165\x73\164\x6f\x6d\145\162\137\x69\144" => $w3], ["\166\151\163\151\164\157\x72\x5f\x69\144\x20\x3d\x20\77" => $E3]);
    }
    public function getWebsiteLimit()
    {
        return AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::WEBSITES_LIMIT), SPConstants::DEFAULT_TOKEN_VALUE);
    }
    public function checkIdpLimit()
    {
        $xG = $this->getcounttable();
        if ($this->check_license_plan(4)) {
            goto n1P;
        }
        if ($this->check_license_plan(3)) {
            goto FMX;
        }
        $Dp = 1;
        goto WAR;
        n1P:
        $Dp = 2;
        goto WAR;
        FMX:
        $Dp = $this->getWebsiteLimit();
        WAR:
        if ($xG < $Dp) {
            goto VWi;
        }
        $this->messageManager->addErrorMessage(__("\124\157\40\143\157\156\146\151\147\165\x72\145\40\155\157\162\x65\x20\111\x64\145\156\164\151\x74\171\x20\x50\x72\157\166\151\x64\145\x72\163\x20\50\111\104\120\163\x29\x2c\40\x70\x6c\145\x61\163\145\x20\x75\x70\x67\x72\141\144\x65\x20\x74\157\40\x74\150\x65\x20\x4d\165\154\164\x69\x2d\111\104\120\40\160\x6c\141\x6e\x2e"));
        $this->responseFactory->create()->setRedirect($this->getAdminUrl("\x6d\157\163\160\163\141\x6d\154\x2f\x69\x64\x70\x73\x2f\x69\x6e\x64\x65\170"))->sendResponse();
        exit;
        VWi:
    }
    public function checkIfRelayStateIsMatchingAnySite($qY)
    {
        $RT = $this->_storeManager->getWebsites();
        foreach ($RT as $XW) {
            $g8 = $XW->getGroups();
            foreach ($g8 as $Jp) {
                $wM = $Jp->getStores();
                foreach ($wM as $SR) {
                    $this->log_debug("\123\164\157\x72\145\x63\x6f\144\145\72\40" . $SR->getCode());
                    $this->log_debug("\123\x74\x6f\x72\x65\111\144\72\40" . $SR->getId());
                    $this->log_debug("\x57\x65\142\163\x69\x74\145\111\x64\72\x20" . $SR->getWebsiteId());
                    $this->log_debug("\x57\x65\142\x73\151\164\x65\x4e\141\155\x65\72\x20" . $SR->getWebsite()->getName());
                    $this->log_debug("\55\x2d\55\x2d\x2d\x2d\x2d\55\55\55\55\x2d\x2d\55\55\x2d\55\55\x2d\x2d\55\55\55\55\x2d\55\x2d\55\x2d\x2d\55\x2d\55\55\55\x2d\55\x2d\55\55\55\x2d\55\55\55\55\55\55\55\x2d\x2d\55\x2d\55\x2d");
                    if (!($SR->getWebsiteId() == $qY)) {
                        goto C6a;
                    }
                    $this->log_debug("\x57\145\142\163\x69\x74\145\x20\x49\x64\x20\155\x61\x74\143\x68\145\144\x20\167\x69\x74\150\40\162\x65\154\141\x79\123\164\141\x74\145\x20\55\x20" . $SR->getWebsiteId());
                    return $SR->getBaseUrl();
                    C6a:
                    tHd:
                }
                pOM:
                xNR:
            }
            nPV:
            Gp_:
        }
        bc2:
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
