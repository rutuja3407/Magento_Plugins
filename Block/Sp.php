<?php


namespace MiniOrange\SP\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
class Sp extends \Magento\Framework\View\Element\Template
{
    public $spUtility;
    private $adminRoleModel;
    private $userGroupModel;
    private $userCollFactory;
    public function __construct(Context $gt, SPUtility $fR, \Magento\Authorization\Model\ResourceModel\Role\Collection $sP, \Magento\Customer\Model\ResourceModel\Group\Collection $i6, \Magento\Store\Model\ResourceModel\Website\CollectionFactory $HM, StoreRepositoryInterface $qc, StoreManagerInterface $VO, StoreWebsiteRelationInterface $zY, \Magento\User\Model\ResourceModel\User\CollectionFactory $VM, array $or = array())
    {
        $this->spUtility = $fR;
        $this->adminRoleModel = $sP;
        $this->userGroupModel = $i6;
        $this->_storeManager = $VO;
        $this->storeRepository = $qc;
        $this->storeWebsiteRelation = $zY;
        $this->_websiteCollectionFactory = $HM;
        $this->userCollFactory = $VM;
        parent::__construct($gt, $or);
    }
    public function getWebsiteCollection()
    {
        $yG = $this->_websiteCollectionFactory->create();
        return $yG;
    }
    public function getMetadataUrl()
    {
        return $this->getBaseUrl() . "\155\x6f\163\160\163\141\155\x6c\x2f\x61\143\164\151\157\x6e\x73\57\115\145\164\141\x64\x61\x74\141";
    }
    public function getBaseUrl()
    {
        return $this->spUtility->getBaseUrl();
    }
    public function getStoreIds($Gh)
    {
        $Jk = [];
        try {
            $Jk = $this->storeWebsiteRelation->getStoreByWebsiteId($Gh);
        } catch (Exception $cA) {
            $this->logger->error($cA->getMessage());
        }
        return $Jk;
    }
    public function getWebsiteIds()
    {
        return $this->spUtility->getStoreConfig(SPConstants::WEBSITE_IDS);
    }
    public function getWebsiteCount()
    {
        return $this->spUtility->getStoreConfig(SPConstants::WEBSITE_COUNT);
    }
    public function getWebsiteLimit()
    {
        return $this->spUtility->getWebsiteLimit();
    }
    public function showWebsite($Gh)
    {
        return $this->spUtility->getStoreConfig($Gh);
    }
    public function getStoreName($Gh)
    {
        return $this->_storeManager->getStore($Gh)->getName();
    }
    public function getCustomerEmail()
    {
        return $this->spUtility->getStoreConfig(SPConstants::SAMLSP_EMAIL);
    }
    public function getCustomerKey()
    {
        return $this->spUtility->getStoreConfig(SPConstants::SAMLSP_KEY);
    }
    public function getAcsUrl()
    {
        return $this->spUtility->getAcsUrl();
    }
    public function getssourl()
    {
        return $this->spUtility->getssourl();
    }
    public function getApiKey()
    {
        return $this->spUtility->getStoreConfig(SPConstants::API_KEY);
    }
    public function getToken()
    {
        return $this->spUtility->getStoreConfig(SPConstants::TOKEN);
    }
    public function isSPConfigured()
    {
        return $this->spUtility->isSPConfigured();
    }
    public function getAdminCssURL()
    {
        return $this->spUtility->getAdminCssUrl("\141\x64\155\x69\x6e\x53\145\x74\164\151\156\x67\x73\x2e\143\x73\x73");
    }
    public function getIntlTelInputJs()
    {
        return $this->spUtility->getAdminJSUrl("\151\x6e\x74\x6c\124\x65\154\x49\x6e\160\165\x74\56\155\151\x6e\56\152\x73");
    }
    public function getAdminJSURL()
    {
        return $this->spUtility->getAdminJSUrl("\x61\x64\x6d\151\x6e\x53\x65\164\x74\151\x6e\x67\163\x2e\x6a\163");
    }
    public function getX509Cert()
    {
        return $this->spUtility->getStoreConfig(SPConstants::X509CERT);
    }
    public function getTestUrl($rq = NULL)
    {
        return $this->getSPInitiatedUrl(SPConstants::TEST_RELAYSTATE, $rq);
    }
    public function getSPInitiatedUrl($Nf = NULL, $rq = NULL)
    {
        return $this->spUtility->getSPInitiatedUrl($Nf, $rq);
    }
    public function getIssuerUrl()
    {
        return $this->spUtility->getIssuerUrl();
    }
    public function getExtensionPageUrl($rU)
    {
        return $this->spUtility->getAdminUrl("\x6d\x6f\x73\160\x73\x61\155\154\x2f" . $rU . "\57\x69\x6e\x64\x65\x78");
    }
    public function getCurrentActiveTab()
    {
        $rU = $this->getUrl("\x2a\x2f\x2a\x2f\52", ["\x5f\143\165\162\x72\145\x6e\164" => true, "\137\165\x73\145\x5f\x72\x65\167\162\x69\164\145" => false]);
        $eu = strpos($rU, "\x6d\x6f\x73\x70\x73\x61\x6d\154\x2f") + 9;
        $YL = strpos($rU, "\57\x69\156\x64\145\x78\x2f\153\145\x79");
        return substr($rU, $eu, $YL - $eu);
    }
    public function getAccountMatcher()
    {
        return $this->spUtility->getStoreConfig(SPConstants::MAP_MAP_BY);
    }
    public function getAllRoles()
    {
        $ke = $this->adminRoleModel->addFieldToFilter("\x72\x6f\x6c\x65\x5f\164\171\x70\x65", "\107");
        $A7 = $ke->toOptionArray();
        return $A7;
    }
    public function getAllGroups()
    {
        return $this->userGroupModel->toOptionArray();
    }
    public function getDefaultRole()
    {
        $YU = $this->spUtility->getStoreConfig(SPConstants::MAP_DEFAULT_ROLE);
        return !$this->spUtility->isBlank($YU) ? $YU : SPConstants::DEFAULT_ROLE;
    }
    public function getRegistrationStatus()
    {
        return $this->spUtility->getStoreConfig(SPConstants::REG_STATUS);
    }
    public function getCurrentAdminUser()
    {
        return $this->spUtility->getCurrentAdminUser();
    }
    public function getSSOButtonText()
    {
        $WB = $this->spUtility->getStoreConfig(SPConstants::BUTTON_TEXT);
        $v4 = $this->spUtility->getStoreConfig(SPConstants::IDP_NAME);
        return !$this->spUtility->isBlank($WB) ? $WB : "\114\x6f\147\x69\x6e\40\x77\151\x74\150\40" . $v4;
    }
    public function getMiniOrangeUrl()
    {
        return $this->spUtility->getMiniOrangeUrl();
    }
    public function isEnabled()
    {
        return $this->spUtility->micr() && $this->spUtility->mclv();
    }
    public function getPublicCert()
    {
        return $this->spUtility->getAdminCertResourceUrl("\x73\x70\x2d\x63\x65\x72\x74\151\146\151\143\141\x74\145\x2e\143\162\x74");
    }
    public function isVerified()
    {
        return $this->spUtility->mclv();
    }
    public function isAutoRedirectEnabled($Yh)
    {
        $P6 = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($P6 == $Yh) {
            goto r8;
        }
        return 0;
        goto BM;
        r8:
        return $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT);
        BM:
    }
    public function isByBackDoorEnabled()
    {
        return $this->spUtility->getStoreConfig(SPConstants::BACKDOOR);
    }
    public function getTable()
    {
        $ao = $this->spUtility->getStoreConfig(SPConstants::MAP_TABLE);
        return !$this->spUtility->isBlank($ao) ? $ao : '';
    }
    public function getDisallowUnlistedUserRole()
    {
        $fC = $this->spUtility->getStoreConfig(SPConstants::UNLISTED_ROLE);
        return !$this->spUtility->isBlank($fC) ? $fC : '';
    }
    public function getDisallowUserCreationIfRoleNotMapped()
    {
        $zn = $this->spUtility->getStoreConfig(SPConstants::CREATEIFNOTMAP);
        return !$this->spUtility->isBlank($zn) ? $zn : '';
    }
    public function getRolesMapped()
    {
        $w5 = $this->spUtility->getStoreConfig(SPConstants::ROLES_MAPPED);
        return !$this->spUtility->isBlank($w5) ? json_decode($w5) : array();
    }
    public function getGroupsMapped()
    {
        $w5 = $this->spUtility->getStoreConfig(SPConstants::GROUPS_MAPPED);
        return !$this->spUtility->isBlank($w5) ? json_decode($w5) : array();
    }
    public function getLogoutAutoRedirectUrl()
    {
        return $this->spUtility->getStoreConfig(SPConstants::LOGOUT_AUTO_REDIRECT_URL);
    }
    public function isAllPageAutoRedirectEnabled($Yh)
    {
        $P6 = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($P6 == $Yh) {
            goto qv;
        }
        return 0;
        goto gG;
        qv:
        return $this->spUtility->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        gG:
    }
    public function getAutoRedirect_AppName()
    {
        return $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
    }
    public function isAllPageAutoRedirectEnabled_action()
    {
        return $this->spUtility->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
    }
    public function getSSOButtonURL()
    {
        return $this->spUtility->getFrontendUrl(SPConstants::SAML_LOGIN_URL, array("\162\145\154\141\171\123\x74\141\x74\x65" => ''));
    }
    public function chk_Premium()
    {
        return $this->spUtility->getStoreConfig(SPConstants::SAMLSP_LK);
    }
    public function getDays()
    {
        $On = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $fq = AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::LICENSE_EXPIRY_DATE), $On);
        $lG = new \DateTime("\x40{$fq}");
        $l4 = new \DateTime();
        $zU = $l4->diff($lG)->format("\45\x72\x25\141");
        return $zU;
    }
    public function isTrialExpired()
    {
        return $this->spUtility->isTrialExpired();
    }
    public function check_license_expiry_date()
    {
        return $this->spUtility->check_license_expiry_date();
    }
    public function check_license_plan($Gd)
    {
        return $this->spUtility->check_license_plan($Gd);
    }
    public function getAllIdpConfiguration()
    {
        return $this->spUtility->getIDPApps();
    }
    public function getProvider()
    {
        return $this->spUtility->getStoreConfig(SPConstants::DEFAULT_PROVIDER);
    }
    public function isSiteEnable()
    {
        $IX = $this->getCurrentWebsite();
        return $this->spUtility->getStoreConfig($IX);
    }
    public function getCurrentWebsite()
    {
        return $this->spUtility->getCurrentWebsiteId();
    }
    public function isDebugLogEnable()
    {
        return $this->spUtility->getStoreConfig(SPConstants::ENABLE_DEBUG_LOG);
    }
    public function getCurrentVersion()
    {
        return SPConstants::VERSION;
    }
}
