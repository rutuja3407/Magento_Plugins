<?php


namespace MiniOrange\SP\Helper;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Model\MiniorangeSamlIDPsFactory;
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
    public function __construct(ScopeConfigInterface $uc, UserFactory $na, CustomerFactory $kD, UrlInterface $kL, WriterInterface $z0, Repository $FG, \Magento\Backend\Helper\Data $NX, Url $Z2, DateTime $fO, ProductMetadataInterface $g8, MiniorangeSamlIDPsFactory $S2)
    {
        $this->scopeConfig = $uc;
        $this->adminFactory = $na;
        $this->customerFactory = $kD;
        $this->urlInterface = $kL;
        $this->configWriter = $z0;
        $this->assetRepo = $FG;
        $this->helperBackend = $NX;
        $this->frontendUrl = $Z2;
        $this->dateTime = $fO;
        $this->miniorangeSamlIDPsFactory = $S2;
    }
    public function getMiniOrangeUrl()
    {
        return SPConstants::HOSTNAME;
    }
    public function getAcsUrl()
    {
        $At = "\155\157\x73\x70\163\x61\155\154\57\141\x63\164\x69\x6f\x6e\163\57\163\x70\117\x62\x73\x65\162\166\145\x72";
        return $this->getBaseUrlWithoutStoreCode() . $At;
    }
    public function getBaseUrl()
    {
        return $this->urlInterface->getBaseUrl();
    }
    public function getssourl()
    {
        $At = "\x6d\157\163\x70\163\x61\155\x6c\57\141\x63\x74\151\x6f\156\x73\57\x4c\x6f\147\157\x75\x74";
        return $this->getBaseUrlWithoutStoreCode() . $At;
    }
    public function saveConfig($At, $VP, $Gh, $Ea)
    {
        $Ea ? $this->saveAdminStoreConfig($At, $VP, $Gh) : $this->saveCustomerStoreConfig($At, $VP, $Gh);
    }
    private function saveAdminStoreConfig($At, $VP, $Gh)
    {
        $or = array($At => $VP);
        $vn = $this->adminFactory->create()->load($Gh)->addData($or);
        $vn->setId($Gh)->save();
    }
    private function saveCustomerStoreConfig($At, $VP, $Gh)
    {
        $or = array($At => $VP);
        $vn = $this->customerFactory->create()->load($Gh)->addData($or);
        $vn->setId($Gh)->save();
    }
    public function getAdminStoreConfig($uR, $Gh)
    {
        return $this->adminFactory->create()->load($Gh)->getData($uR);
    }
    public function getCustomerStoreConfig($uR, $Gh)
    {
        return $this->customerFactory->create()->load($Gh)->getData($uR);
    }
    public function getIssuerUrl()
    {
        return $this->getBaseUrlWithoutStoreCode() . SPConstants::SUFFIX_ISSUER_URL_PATH;
    }
    public function getBaseUrlWithoutStoreCode()
    {
        return $this->scopeConfig->getValue("\167\145\x62\x2f\163\145\x63\165\162\x65\57\x62\x61\x73\145\x5f\165\x72\x6c");
    }
    public function getImageUrl($B9)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_IMAGES . $B9);
    }
    public function getUrl($At, $Te = array())
    {
        return $this->urlInterface->getUrl($At, array("\x5f\161\x75\145\162\x79" => $Te));
    }
    public function getAdminCssUrl($zc)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_CSS . $zc, array("\141\x72\145\x61" => "\141\x64\155\x69\156\150\x74\x6d\154"));
    }
    public function getAdminJSUrl($C2)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_JS . $C2, array("\141\x72\x65\x61" => "\x61\144\155\x69\156\x68\164\155\154"));
    }
    public function getMetadataFilePath()
    {
        return $this->assetRepo->createAsset(SPConstants::MODULE_DIR . SPConstants::MODULE_METADATA, array("\141\162\x65\141" => "\141\x64\155\151\156\150\x74\x6d\154"))->getSourceFile();
    }
    public function getResourcePath($On)
    {
        return $this->assetRepo->createAsset(SPConstants::MODULE_DIR . SPConstants::MODULE_CERTS . $On, array("\x61\x72\x65\141" => "\141\x64\x6d\151\156\x68\x74\x6d\154"))->getSourceFile();
    }
    public function getAdminBaseUrl()
    {
        return $this->helperBackend->getHomePageUrl();
    }
    public function getAdminUrl($At, $Te = array())
    {
        return $this->helperBackend->getUrl($At, array("\137\161\165\x65\x72\x79" => $Te));
    }
    public function getSPInitiatedUrl($Nf = NULL, $rq = NULL)
    {
        $Nf = is_null($Nf) ? $this->getCurrentUrl() : $Nf;
        return $this->getFrontendUrl(SPConstants::SUFFIX_SAML_LOGIN_URL, array("\x72\145\x6c\x61\x79\x53\164\x61\164\x65" => $Nf)) . "\46\x69\x64\160\137\x6e\141\155\x65\75" . $rq;
    }
    public function getCurrentUrl()
    {
        return $this->urlInterface->getCurrentUrl();
    }
    public function getFrontendUrl($At, $Te = array())
    {
        return $this->frontendUrl->getUrl($At, array("\x5f\x71\x75\145\x72\171" => $Te));
    }
    public function getAdminCertResourceUrl($On)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_CERTS . $On, array("\x61\x72\145\141" => "\141\144\x6d\151\156\x68\164\155\x6c"));
    }
    public function isAllPageAutoRedirectEnabled($Yh)
    {
        $P6 = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($P6 == $Yh) {
            goto Yp;
        }
        return 0;
        goto rr;
        Yp:
        return $this->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        rr:
    }
    public function getStoreConfig($uR)
    {
        $y5 = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue("\155\151\x6e\x69\x6f\162\141\x6e\x67\145\57\x73\141\x6d\154\x73\x70\57" . $uR, $y5);
    }
    public function setIDPApps($gu, $FW, $I_, $CI, $tb, $fF, $zS, $LT, $GK, $y4, $t8, $Ab, $Cx, $kY, $ni, $j2, $zL, $Ql, $Hr, $Qx, $pf, $Z3, $ph, $qS, $pY, $mP, $gr, $lF, $M8, $h5, $se, $zd, $il, $We, $Qn, $Pt, $Dx, $C5, $V9, $WD, $VJ, $mk, $YO, $Le, $Qu, $by, $bw, $Yx, $lN, $GE, $Q7)
    {
        $vn = $this->miniorangeSamlIDPsFactory->create();
        $vn->addData(["\151\144\160\137\x6e\141\155\145" => $gu, "\x69\144\160\x5f\x65\x6e\x74\151\x74\171\137\151\x64" => $FW, "\x73\141\x6d\154\137\154\x6f\147\151\x6e\137\x75\x72\x6c" => $I_, "\163\x61\x6d\154\x5f\154\157\x67\x69\x6e\x5f\x62\x69\x6e\144\151\x6e\147" => $CI, "\163\x61\155\154\x5f\x6c\157\147\x6f\x75\164\137\x75\162\x6c" => $tb, "\163\x61\155\154\x5f\154\157\147\x6f\x75\x74\x5f\x62\x69\x6e\x64\x69\x6e\x67" => $fF, "\170\65\x30\x39\137\x63\145\x72\x74\151\x66\151\x63\141\164\x65" => $zS, "\162\x65\x73\160\157\x6e\163\145\137\x73\151\x67\x6e\145\144" => $LT, "\x61\163\163\x65\162\164\151\157\x6e\137\163\x69\x67\156\x65\x64" => $GK, "\x73\150\x6f\x77\137\x61\144\155\151\x6e\137\x6c\x69\156\x6b" => $y4, "\x73\150\157\167\137\x63\x75\163\x74\157\155\145\x72\x5f\x6c\151\x6e\153" => $t8, "\x61\x75\x74\157\x5f\x63\162\x65\141\164\x65\137\x61\x64\155\151\156\x5f\x75\x73\x65\x72\163" => $Ab, "\141\x75\164\x6f\137\143\x72\145\x61\164\x65\137\143\x75\163\x74\x6f\155\145\162\163" => $Cx, "\x64\x69\x73\x61\142\x6c\145\137\142\x32\143" => $kY, "\146\157\162\143\145\137\141\165\164\150\145\x6e\164\151\143\141\164\x69\157\156\137\167\151\164\150\x5f\151\144\160" => $ni, "\x61\165\x74\157\x5f\x72\145\x64\x69\162\x65\x63\164\137\x74\x6f\137\x69\x64\160" => $j2, "\154\151\156\x6b\x5f\x74\x6f\137\x69\156\151\x74\151\x61\164\x65\x5f\x73\163\x6f" => $zL, "\x75\160\144\141\164\145\137\141\164\x74\x72\x69\x62\165\164\145\163\137\x6f\x6e\137\154\x6f\147\151\x6e" => $Ql, "\143\162\x65\141\164\145\137\x6d\x61\147\x65\156\x74\x6f\137\141\143\x63\157\165\x6e\164\x5f\x62\x79" => $Hr, "\145\x6d\141\151\154\137\x61\164\x74\x72\x69\x62\165\x74\145" => $Qx, "\x75\x73\x65\162\156\x61\x6d\x65\137\141\x74\164\x72\151\142\x75\x74\x65" => $pf, "\146\x69\162\163\164\x6e\141\155\x65\x5f\141\164\164\162\x69\142\165\164\145" => $Z3, "\x6c\x61\x73\164\x6e\141\155\145\x5f\x61\x74\164\162\151\142\165\x74\145" => $ph, "\147\162\157\165\x70\x5f\141\164\164\162\151\x62\x75\x74\x65" => $qS, "\x62\151\x6c\154\x69\x6e\x67\x5f\x63\x69\x74\171\x5f\x61\164\x74\162\151\142\165\x74\x65" => $pY, "\142\x69\154\154\151\156\147\137\163\x74\x61\x74\x65\x5f\x61\164\x74\162\x69\x62\165\164\x65" => $mP, "\x62\x69\154\x6c\x69\156\147\137\143\x6f\165\x6e\x74\x72\171\137\141\x74\164\x72\x69\x62\x75\164\145" => $gr, "\x62\151\154\x6c\x69\x6e\147\137\x61\x64\144\x72\x65\163\163\x5f\141\x74\x74\162\x69\142\x75\164\145" => $lF, "\x62\x69\154\154\151\x6e\147\137\160\x68\x6f\156\145\x5f\141\x74\164\162\x69\142\165\x74\145" => $M8, "\142\151\154\154\x69\x6e\147\x5f\x7a\x69\160\x5f\x61\164\164\162\151\142\x75\164\x65" => $h5, "\x73\x68\151\x70\160\151\156\147\137\143\151\164\x79\137\x61\164\x74\x72\x69\142\165\164\x65" => $se, "\x73\x68\151\160\x70\x69\x6e\x67\x5f\163\x74\x61\x74\x65\x5f\x61\164\x74\x72\x69\142\165\x74\145" => $zd, "\163\x68\x69\160\160\x69\x6e\x67\137\143\157\165\156\x74\162\x79\137\x61\164\x74\x72\x69\142\x75\x74\145" => $il, "\163\x68\151\x70\160\151\156\147\137\x61\144\x64\162\145\163\x73\x5f\141\164\x74\162\151\x62\165\x74\145" => $We, "\x73\x68\151\x70\160\151\x6e\147\x5f\x70\x68\157\x6e\x65\x5f\x61\x74\x74\x72\x69\x62\165\x74\x65" => $Qn, "\x73\x68\151\x70\x70\x69\x6e\147\x5f\x7a\x69\160\137\x61\164\x74\162\x69\x62\165\164\x65" => $Pt, "\x62\x32\142\x5f\x61\164\164\162\151\x62\x75\164\145" => $Dx, "\143\x75\163\x74\x6f\155\x5f\x74\141\142\x6c\145\x6e\x61\155\x65" => $C5, "\143\x75\163\164\x6f\155\x5f\141\164\x74\162\x69\x62\165\x74\x65\163" => $V9, "\x64\157\x5f\x6e\157\164\x5f\x61\165\x74\x6f\143\162\x65\x61\x74\145\x5f\151\x66\x5f\162\x6f\154\x65\163\x5f\x6e\x6f\x74\x5f\155\141\x70\x70\145\144" => $WD, "\165\160\144\x61\164\x65\137\x62\x61\x63\x6b\x65\156\144\x5f\x72\157\154\x65\163\137\x6f\x6e\x5f\x73\x73\157" => $VJ, "\x75\x70\x64\x61\x74\145\137\146\x72\157\156\x74\145\x6e\x64\x5f\x67\x72\x6f\165\160\x73\137\157\156\x5f\163\x73\157" => $mk, "\x64\x65\x66\141\x75\x6c\164\x5f\147\162\x6f\x75\x70" => $YO, "\144\145\x66\x61\165\154\164\137\162\x6f\x6c\x65" => $Le, "\147\x72\157\x75\x70\163\137\x6d\141\x70\x70\x65\x64" => $Qu, "\162\157\x6c\145\163\x5f\x6d\141\160\x70\x65\x64" => $by, "\163\141\x6d\x6c\137\x6c\x6f\x67\157\165\164\x5f\162\x65\x64\151\x72\145\x63\x74\x5f\165\162\154" => $bw, "\163\x61\x6d\154\137\145\156\141\x62\x6c\x65\137\142\151\154\x6c\x69\x6e\147\x61\x6e\144\163\150\x69\x70\x70\x69\x6e\147" => $Yx, "\x73\141\155\x6c\x5f\163\x61\155\145\141\163\142\151\x6c\154\x69\x6e\x67" => $lN, "\x6d\157\x5f\163\x61\x6d\154\137\150\145\141\144\154\145\163\x73\x5f\x73\163\157" => $GE, "\x6d\x6f\137\163\x61\x6d\154\x5f\146\x72\157\x6e\164\x65\x6e\144\137\160\x6f\163\x74\137\165\x72\x6c" => $Q7]);
        $vn->save();
    }
    public function getcounttable()
    {
        $yG = $this->getIDPApps();
        $ov = 0;
        foreach ($yG as $ub) {
            $hR = $ub->getData();
            $ov++;
            az:
        }
        K0:
        return $ov;
    }
    public function getIDPApps()
    {
        $vn = $this->miniorangeSamlIDPsFactory->create();
        $yG = $vn->getCollection();
        return $yG;
    }
    public function deleteIDPApps($Gh)
    {
        $vn = $this->miniorangeSamlIDPsFactory->create();
        $vn->load($Gh);
        $vn->delete();
    }
    public function isTrialExpired()
    {
        if (!$this->check_license_plan(4)) {
            goto QD;
        }
        $h3 = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::INSTALLATION_DATE), SPConstants::DEFAULT_TOKEN_VALUE);
        if (!$h3) {
            goto Dd;
        }
        $mf = date("\x59\x2d\155\x2d\144\40\x48\x3a\151\x3a\163", strtotime($h3 . "\40\53\x20" . 7 . "\x20\x64\x61\171\x73"));
        $ew = $this->dateTime->gmtDate("\131\55\155\x2d\x64\40\x48\x3a\x69\x3a\163");
        if (!($ew > $mf)) {
            goto C_;
        }
        $ii = null;
        $FO = null;
        if (!$this->getCurrentAdminUser()) {
            goto BJ;
        }
        $fU = $this->getCurrentAdminUser()->getData();
        $ii = $fU["\x65\155\x61\151\154"];
        $FO = $fU["\x66\x69\x72\x73\164\x6e\x61\155\145"];
        BJ:
        $kz = $this->getBaseUrl();
        $RH = $this->getMagnetoVersion();
        $jT = array($FO, $RH, $kz);
        $Az = $this->getStoreConfig(SPConstants::SEND_EXPIRED_EMAIL);
        if (!($Az == null)) {
            goto hK;
        }
        $this->setStoreConfig(SPConstants::SEND_EXPIRED_EMAIL, 1);
        Curl::submit_to_magento_team($ii, "\x54\122\x49\101\114\40\x45\x58\x50\x49\x52\105\x44", $jT);
        $this->flushCache();
        hK:
        return true;
        C_:
        goto ET;
        Dd:
        $ew = $this->dateTime->gmtDate("\x59\x2d\155\x2d\144\x20\x48\72\x69\x3a\163");
        $this->setStoreConfig(SPConstants::INSTALLATION_DATE, AESEncryption::encrypt_data($ew, SPConstants::DEFAULT_TOKEN_VALUE));
        ET:
        return false;
        QD:
        return false;
    }
    public function setStoreConfig($uR, $VP)
    {
        $this->configWriter->save("\x6d\151\x6e\x69\x6f\x72\141\x6e\x67\x65\57\163\141\x6d\x6c\x73\160\x2f" . $uR, $VP);
    }
    public function extendTrial()
    {
        if (!$this->check_license_plan(4)) {
            goto dg;
        }
        $fU = $this->getCurrentAdminUser()->getData();
        $ii = $fU["\x65\x6d\x61\x69\154"];
        $FO = $fU["\146\151\x72\x73\x74\x6e\141\x6d\x65"];
        $kz = $this->getBaseUrl();
        $RH = $this->getMagnetoVersion();
        $fr = $this->getStoreConfig(SPConstants::IS_TRIAL_EXTENDED);
        if (!$fr) {
            goto MG;
        }
        $jT = array($FO, $RH, $kz);
        Curl::submit_to_magento_team($ii, "\x54\x52\x49\101\114\x20\x45\130\x54\x45\116\x44\40\122\105\x51\x55\105\x53\x54\x20\106\x41\111\x4c\105\104\x20\x28\101\114\122\x45\x41\x44\131\x20\105\130\x54\x45\x4e\x44\105\104\x29", $jT);
        $this->messageManager->addErrorMessage(SPMessages::EXTEND_TRIAL_LIMIT_REACHED);
        goto K1;
        MG:
        $this->setStoreConfig(SPConstants::IS_TRIAL_EXTENDED, true);
        $ew = $this->dateTime->gmtDate("\131\x2d\155\55\x64\40\x48\x3a\x69\72\x73");
        $this->setStoreConfig(SPConstants::INSTALLATION_DATE, AESEncryption::encrypt_data($ew, SPConstants::DEFAULT_TOKEN_VALUE));
        $jT = array($FO, $RH, $kz);
        Curl::submit_to_magento_team($ii, "\x54\x52\x49\x41\x4c\40\105\130\124\x45\116\x44\105\104\40\106\117\122\x20\67\x20\x44\101\x59\x53", $jT);
        $this->messageManager->addSuccessMessage(SPMessages::TRIAL_EXTENDED);
        K1:
        dg:
    }
    public function getRemainingUsersCount()
    {
        if (!is_null($this->getStoreConfig(SPConstants::MAGENTO_COUNTER))) {
            goto fm;
        }
        $this->setStoreConfig(SPConstants::MAGENTO_COUNTER, AESEncryption::encrypt_data(0, SPConstants::DEFAULT_TOKEN_VALUE));
        fm:
        $fc = (int) AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::MAGENTO_COUNTER), SPConstants::DEFAULT_TOKEN_VALUE);
        $sY = (int) AESEncryption::decrypt_data("\x5a\x48\x6f\x3d", SPConstants::DEFAULT_TOKEN_VALUE);
        if (!($fc > $sY)) {
            goto e9;
        }
        $kz = $this->getBaseUrl();
        $RH = $this->getMagnetoVersion();
        $jT = array($RH, $kz);
        Curl::submit_to_magento_team('', "\x55\x73\145\162\x20\x4c\x69\155\151\x74\x20\145\170\143\145\x65\144\145\144", $jT);
        e9:
        return $fc > $sY;
    }
}
