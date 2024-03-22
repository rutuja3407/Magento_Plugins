<?php


namespace MiniOrange\SP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\User\Model\UserFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Url;
use MiniOrange\SP\Model\MiniorangeSamlIDPsFactory;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
class Data extends AbstractHelper
{
    protected $scopeConfig;
    protected $adminFactory;
    protected $customerFactory;
    protected $urlInterface;
    protected $configWriter;
    protected $assetRepo;
    protected $helperBackend;
    protected $frontendUrl;
    protected $miniorangeSamlIDPsFactory;
    protected $dateTime;
    public function __construct(ScopeConfigInterface $PS, UserFactory $Zg, CustomerFactory $j8, UrlInterface $WU, WriterInterface $UX, Repository $Tq, \Magento\Backend\Helper\Data $A3, Url $vs, DateTime $rE, ProductMetadataInterface $a2, MiniorangeSamlIDPsFactory $OP)
    {
        $this->scopeConfig = $PS;
        $this->adminFactory = $Zg;
        $this->customerFactory = $j8;
        $this->urlInterface = $WU;
        $this->configWriter = $UX;
        $this->assetRepo = $Tq;
        $this->helperBackend = $A3;
        $this->frontendUrl = $vs;
        $this->dateTime = $rE;
        $this->miniorangeSamlIDPsFactory = $OP;
    }
    public function getMiniOrangeUrl()
    {
        return SPConstants::HOSTNAME;
    }
    public function getAcsUrl()
    {
        $JY = "\x6d\157\163\160\163\141\x6d\x6c\x2f\x61\143\164\x69\157\156\x73\57\163\160\117\142\163\x65\162\x76\x65\162";
        return $this->getBaseUrl() . $JY;
    }
    public function getssourl()
    {
        $JY = "\x6d\x6f\163\x70\x73\x61\155\x6c\57\x61\143\164\x69\157\156\x73\x2f\114\157\x67\x6f\165\x74";
        return $this->getBaseUrl() . $JY;
    }
    public function getStoreConfig($Zf)
    {
        $B9 = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue("\x6d\x69\x6e\x69\x6f\162\141\x6e\147\x65\57\x73\x61\x6d\x6c\x73\160\x2f" . $Zf, $B9);
    }
    public function setStoreConfig($Zf, $Yk)
    {
        $this->configWriter->save("\x6d\151\x6e\x69\157\162\x61\x6e\147\x65\57\x73\x61\155\154\x73\160\57" . $Zf, $Yk);
    }
    public function saveConfig($JY, $Yk, $lA, $zc)
    {
        $zc ? $this->saveAdminStoreConfig($JY, $Yk, $lA) : $this->saveCustomerStoreConfig($JY, $Yk, $lA);
    }
    public function getAdminStoreConfig($Zf, $lA)
    {
        return $this->adminFactory->create()->load($lA)->getData($Zf);
    }
    private function saveAdminStoreConfig($JY, $Yk, $lA)
    {
        $F2 = array($JY => $Yk);
        $tH = $this->adminFactory->create()->load($lA)->addData($F2);
        $tH->setId($lA)->save();
    }
    public function getCustomerStoreConfig($Zf, $lA)
    {
        return $this->customerFactory->create()->load($lA)->getData($Zf);
    }
    private function saveCustomerStoreConfig($JY, $Yk, $lA)
    {
        $F2 = array($JY => $Yk);
        $tH = $this->customerFactory->create()->load($lA)->addData($F2);
        $tH->setId($lA)->save();
    }
    public function getBaseUrl()
    {
        return $this->urlInterface->getBaseUrl();
    }
    public function getCurrentUrl()
    {
        return $this->urlInterface->getCurrentUrl();
    }
    public function getUrl($JY, $As = array())
    {
        return $this->urlInterface->getUrl($JY, array("\137\161\165\x65\162\171" => $As));
    }
    public function getFrontendUrl($JY, $As = array())
    {
        return $this->frontendUrl->getUrl($JY, array("\137\x71\165\145\x72\171" => $As));
    }
    public function getIssuerUrl()
    {
        return $this->getBaseUrl() . SPConstants::SUFFIX_ISSUER_URL_PATH;
    }
    public function getImageUrl($jd)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_IMAGES . $jd);
    }
    public function getAdminCssUrl($Aa)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_CSS . $Aa, array("\x61\162\x65\x61" => "\141\x64\x6d\151\x6e\x68\164\x6d\154"));
    }
    public function getAdminJSUrl($p3)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_JS . $p3, array("\141\x72\x65\x61" => "\x61\x64\155\151\156\150\164\155\x6c"));
    }
    public function getMetadataFilePath()
    {
        return $this->assetRepo->createAsset(SPConstants::MODULE_DIR . SPConstants::MODULE_METADATA, array("\141\x72\145\141" => "\141\x64\155\151\x6e\150\164\155\154"))->getSourceFile();
    }
    public function getResourcePath($zg)
    {
        return $this->assetRepo->createAsset(SPConstants::MODULE_DIR . SPConstants::MODULE_CERTS . $zg, array("\141\162\x65\x61" => "\x61\x64\x6d\x69\x6e\x68\164\x6d\x6c"))->getSourceFile();
    }
    public function getAdminBaseUrl()
    {
        return $this->helperBackend->getHomePageUrl();
    }
    public function getAdminUrl($JY, $As = array())
    {
        return $this->helperBackend->getUrl($JY, array("\x5f\161\x75\145\x72\x79" => $As));
    }
    public function getSPInitiatedUrl($qY = NULL, $rQ = NULL)
    {
        $qY = is_null($qY) ? $this->getCurrentUrl() : $qY;
        return $this->getFrontendUrl(SPConstants::SUFFIX_SAML_LOGIN_URL, array("\x72\x65\x6c\x61\171\123\164\141\164\x65" => $qY)) . "\46\151\144\160\137\156\x61\155\145\x3d" . $rQ;
    }
    public function getAdminCertResourceUrl($zg)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_CERTS . $zg, array("\x61\162\145\141" => "\x61\144\x6d\151\x6e\x68\x74\x6d\x6c"));
    }
    public function isAllPageAutoRedirectEnabled($Mu)
    {
        $R5 = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($R5 == $Mu) {
            goto EZ;
        }
        return 0;
        goto Qx;
        EZ:
        return $this->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        Qx:
    }
    public function setIDPApps($Lk, $xb, $ln, $dq, $WY, $B3, $w_, $a5, $f4, $o9, $vh, $w8, $Gd, $h0, $M1, $M3, $qr, $S8, $qx, $cE, $x_, $Fw, $tV, $Wr, $T5, $ab, $TC, $Au, $hX, $Sr, $J2, $mG, $iz, $mq, $Qb, $gE, $lL, $Rr, $Rv, $i3, $AF, $Jg, $Vc, $LQ, $CL, $sG, $Uo, $wj, $fE)
    {
        $tH = $this->miniorangeSamlIDPsFactory->create();
        $tH->addData(["\x69\x64\160\137\x6e\141\155\x65" => $Lk, "\151\x64\x70\137\145\156\164\x69\x74\171\137\x69\x64" => $xb, "\x73\x61\155\x6c\137\154\x6f\x67\x69\x6e\137\x75\162\154" => $ln, "\x73\x61\x6d\x6c\137\x6c\x6f\147\151\x6e\x5f\x62\x69\156\x64\x69\156\147" => $dq, "\163\141\x6d\x6c\137\154\x6f\x67\x6f\165\164\x5f\x75\x72\x6c" => $WY, "\163\141\x6d\154\137\154\157\147\157\x75\x74\x5f\x62\x69\156\x64\151\156\147" => $B3, "\x78\x35\x30\x39\x5f\143\x65\162\164\151\146\x69\x63\x61\164\145" => $w_, "\x72\x65\163\x70\157\156\x73\145\x5f\163\x69\147\x6e\x65\x64" => $a5, "\141\163\163\145\x72\x74\x69\x6f\x6e\137\163\151\147\156\x65\x64" => $f4, "\163\150\x6f\167\x5f\x61\x64\x6d\x69\156\x5f\x6c\151\x6e\153" => $o9, "\163\150\x6f\167\x5f\x63\x75\163\164\157\x6d\145\x72\x5f\154\x69\156\153" => $vh, "\x61\x75\x74\x6f\x5f\143\162\x65\x61\164\145\137\141\x64\x6d\151\156\137\165\163\145\x72\163" => $w8, "\x61\x75\x74\x6f\x5f\x63\x72\145\141\x74\145\x5f\143\x75\x73\164\x6f\155\x65\x72\163" => $Gd, "\x64\x69\x73\141\x62\x6c\145\137\x62\62\x63" => $h0, "\146\x6f\x72\x63\145\x5f\x61\165\164\x68\145\x6e\x74\x69\143\141\164\x69\x6f\156\137\x77\x69\164\150\x5f\x69\x64\160" => $M1, "\141\x75\x74\x6f\x5f\162\145\144\151\162\145\143\x74\137\164\x6f\137\151\x64\x70" => $M3, "\154\151\x6e\x6b\137\164\x6f\x5f\151\x6e\151\164\151\x61\164\x65\137\163\x73\157" => $qr, "\x75\x70\x64\141\164\x65\x5f\141\x74\164\x72\151\x62\165\x74\x65\163\x5f\x6f\x6e\x5f\154\x6f\147\151\156" => $S8, "\143\162\x65\141\164\145\x5f\155\x61\147\145\156\164\x6f\x5f\x61\x63\143\x6f\x75\156\164\137\x62\x79" => $qx, "\145\155\x61\151\x6c\x5f\x61\164\x74\x72\151\142\165\x74\145" => $cE, "\165\163\145\162\156\141\155\145\137\x61\x74\164\162\151\142\x75\164\x65" => $x_, "\x66\x69\162\163\164\x6e\x61\x6d\x65\x5f\141\164\x74\162\x69\142\165\x74\x65" => $Fw, "\x6c\x61\x73\164\156\x61\x6d\145\137\x61\x74\x74\x72\x69\142\x75\164\145" => $tV, "\x67\162\157\x75\160\x5f\x61\x74\164\x72\x69\x62\165\164\x65" => $Wr, "\142\151\154\154\151\156\x67\x5f\x63\x69\164\x79\137\x61\x74\x74\162\x69\x62\x75\x74\x65" => $T5, "\x62\151\154\154\151\x6e\147\x5f\x73\x74\141\x74\x65\137\x61\x74\x74\x72\x69\x62\x75\x74\x65" => $ab, "\142\x69\154\154\151\156\x67\x5f\143\157\165\x6e\164\162\x79\137\141\164\x74\162\x69\142\165\x74\145" => $TC, "\x62\151\154\x6c\x69\x6e\147\x5f\x61\144\144\x72\x65\163\x73\137\x61\x74\x74\x72\x69\x62\165\164\145" => $Au, "\142\151\154\154\151\x6e\x67\137\x70\x68\x6f\156\145\137\x61\x74\164\162\x69\x62\165\164\145" => $hX, "\x62\x69\x6c\x6c\x69\156\x67\137\172\x69\x70\137\x61\164\164\x72\151\x62\x75\x74\x65" => $Sr, "\163\150\x69\x70\160\151\156\147\x5f\143\151\164\x79\137\x61\164\x74\162\151\142\165\164\145" => $J2, "\163\x68\x69\160\x70\151\156\147\137\163\164\141\x74\x65\137\141\x74\x74\x72\x69\142\x75\164\145" => $mG, "\163\x68\x69\x70\160\151\x6e\147\137\143\x6f\x75\156\164\162\x79\x5f\141\x74\x74\162\x69\x62\x75\164\145" => $iz, "\163\150\151\160\x70\151\x6e\147\x5f\x61\x64\x64\x72\x65\x73\163\x5f\141\x74\164\162\151\142\165\164\145" => $mq, "\163\x68\x69\160\x70\151\156\147\137\160\x68\157\x6e\145\137\x61\x74\x74\x72\x69\x62\x75\x74\145" => $Qb, "\163\150\x69\160\x70\151\x6e\147\137\x7a\151\160\137\x61\164\164\162\151\142\x75\164\145" => $gE, "\142\62\x62\137\141\x74\164\x72\151\x62\165\x74\x65" => $lL, "\x63\165\163\x74\157\x6d\x5f\x74\x61\x62\154\x65\156\141\155\x65" => $Rr, "\143\x75\x73\x74\157\155\137\x61\164\x74\x72\x69\142\x75\164\145\x73" => $Rv, "\x64\x6f\x5f\x6e\x6f\164\137\x61\165\164\x6f\x63\162\145\141\164\145\137\x69\x66\137\x72\157\154\x65\x73\137\156\x6f\164\x5f\x6d\x61\160\160\x65\144" => $i3, "\165\160\x64\x61\164\x65\x5f\x62\141\143\153\x65\156\x64\137\162\157\154\x65\x73\x5f\x6f\x6e\137\x73\163\x6f" => $AF, "\x75\160\x64\x61\x74\x65\x5f\146\162\x6f\156\x74\145\x6e\144\x5f\x67\162\x6f\x75\x70\x73\137\157\x6e\137\x73\163\157" => $Jg, "\x64\145\x66\x61\165\154\x74\x5f\147\162\x6f\x75\x70" => $Vc, "\144\145\x66\141\165\154\x74\137\x72\157\x6c\x65" => $LQ, "\147\x72\x6f\165\160\163\137\155\141\160\160\145\x64" => $CL, "\x72\157\154\x65\163\137\x6d\x61\x70\160\x65\x64" => $sG, "\163\141\155\x6c\x5f\x6c\157\x67\x6f\x75\164\x5f\162\145\x64\151\162\x65\143\x74\x5f\165\162\154" => $Uo, "\x73\x61\x6d\x6c\137\145\156\x61\142\x6c\145\x5f\x62\x69\154\154\151\x6e\x67\141\x6e\144\x73\150\x69\160\160\x69\156\x67" => $wj, "\x73\x61\155\154\x5f\x73\141\x6d\x65\x61\163\142\x69\154\154\x69\156\x67" => $fE]);
        $tH->save();
    }
    public function getIDPApps()
    {
        $tH = $this->miniorangeSamlIDPsFactory->create();
        $Lw = $tH->getCollection();
        return $Lw;
    }
    public function getcounttable()
    {
        $Lw = $this->getIDPApps();
        $xG = 0;
        foreach ($Lw as $fR) {
            $ft = $fR->getData();
            $xG++;
            r9:
        }
        ct:
        return $xG;
    }
    public function deleteIDPApps($lA)
    {
        $tH = $this->miniorangeSamlIDPsFactory->create();
        $tH->load($lA);
        $tH->delete();
    }
    public function isTrialExpired()
    {
        if (!$this->check_license_plan(4)) {
            goto vF;
        }
        $vl = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::INSTALLATION_DATE), SPConstants::DEFAULT_TOKEN_VALUE);
        if (!$vl) {
            goto GI;
        }
        $TX = date("\131\x2d\x6d\x2d\144\40\110\72\x69\72\163", strtotime($vl . "\40\x2b\x20" . 7 . "\40\x64\x61\171\163"));
        $gD = $this->dateTime->gmtDate("\131\x2d\x6d\x2d\144\40\110\x3a\151\x3a\163");
        if (!($gD > $TX)) {
            goto PO;
        }
        $cN = null;
        $Aq = null;
        if (!$this->getCurrentAdminUser()) {
            goto af;
        }
        $MG = $this->getCurrentAdminUser()->getData();
        $cN = $MG["\145\155\141\x69\x6c"];
        $Aq = $MG["\x66\x69\162\x73\164\x6e\141\x6d\145"];
        af:
        $Lu = $this->getBaseUrl();
        $zG = $this->getMagnetoVersion();
        $D7 = array($Aq, $zG, $Lu);
        $bc = $this->getStoreConfig(SPConstants::SEND_EXPIRED_EMAIL);
        if (!($bc == null)) {
            goto lS;
        }
        $this->setStoreConfig(SPConstants::SEND_EXPIRED_EMAIL, 1);
        Curl::submit_to_magento_team($cN, "\x54\122\x49\101\x4c\40\x45\x58\x50\x49\122\x45\x44", $D7);
        $this->flushCache();
        lS:
        return true;
        PO:
        goto xR;
        GI:
        $gD = $this->dateTime->gmtDate("\x59\x2d\x6d\55\144\40\x48\72\x69\72\x73");
        $this->setStoreConfig(SPConstants::INSTALLATION_DATE, AESEncryption::encrypt_data($gD, SPConstants::DEFAULT_TOKEN_VALUE));
        xR:
        return false;
        vF:
        return false;
    }
    public function extendTrial()
    {
        if (!$this->check_license_plan(4)) {
            goto Fb;
        }
        $MG = $this->getCurrentAdminUser()->getData();
        $cN = $MG["\145\x6d\x61\151\x6c"];
        $Aq = $MG["\x66\151\162\x73\x74\x6e\x61\155\x65"];
        $Lu = $this->getBaseUrl();
        $zG = $this->getMagnetoVersion();
        $t9 = $this->getStoreConfig(SPConstants::IS_TRIAL_EXTENDED);
        if (!$t9) {
            goto De;
        }
        $D7 = array($Aq, $zG, $Lu);
        Curl::submit_to_magento_team($cN, "\124\122\x49\101\x4c\x20\x45\130\124\x45\116\104\40\x52\105\121\x55\105\123\124\40\106\101\x49\x4c\105\104\40\50\x41\114\122\x45\x41\x44\131\x20\x45\130\x54\x45\116\x44\x45\104\x29", $D7);
        $this->messageManager->addErrorMessage(SPMessages::EXTEND_TRIAL_LIMIT_REACHED);
        goto fR;
        De:
        $this->setStoreConfig(SPConstants::IS_TRIAL_EXTENDED, true);
        $gD = $this->dateTime->gmtDate("\131\x2d\x6d\x2d\x64\x20\x48\72\151\x3a\163");
        $this->setStoreConfig(SPConstants::INSTALLATION_DATE, AESEncryption::encrypt_data($gD, SPConstants::DEFAULT_TOKEN_VALUE));
        $D7 = array($Aq, $zG, $Lu);
        Curl::submit_to_magento_team($cN, "\x54\x52\x49\x41\114\x20\105\130\x54\105\116\104\105\x44\x20\x46\x4f\x52\x20\67\40\104\101\131\x53", $D7);
        $this->messageManager->addSuccessMessage(SPMessages::TRIAL_EXTENDED);
        fR:
        Fb:
    }
    public function getRemainingUsersCount()
    {
        if (!is_null($this->getStoreConfig(SPConstants::MAGENTO_COUNTER))) {
            goto ZG;
        }
        $this->setStoreConfig(SPConstants::MAGENTO_COUNTER, AESEncryption::encrypt_data(0, SPConstants::DEFAULT_TOKEN_VALUE));
        ZG:
        $Vn = (int) AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::MAGENTO_COUNTER), SPConstants::DEFAULT_TOKEN_VALUE);
        $Kh = (int) AESEncryption::decrypt_data("\132\110\x6f\x3d", SPConstants::DEFAULT_TOKEN_VALUE);
        if (!($Vn > $Kh)) {
            goto b1;
        }
        $Lu = $this->getBaseUrl();
        $zG = $this->getMagnetoVersion();
        $D7 = array($zG, $Lu);
        Curl::submit_to_magento_team('', "\125\x73\x65\x72\x20\114\x69\x6d\151\164\40\x65\170\143\x65\x65\x64\145\x64", $D7);
        b1:
        return $Vn > $Kh;
    }
}
