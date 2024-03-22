<?php


namespace MiniOrange\SP\Block;

use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Framework\View\Element\Template\Context;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
class Sp extends \Magento\Framework\View\Element\Template
{
    public $spUtility;
    private $adminRoleModel;
    private $userGroupModel;
    private $userCollFactory;
    public function __construct(Context $Gc, SPUtility $Kx, \Magento\Authorization\Model\ResourceModel\Role\Collection $bY, \Magento\Customer\Model\ResourceModel\Group\Collection $o1, \Magento\Store\Model\ResourceModel\Website\CollectionFactory $DU, StoreRepositoryInterface $dW, StoreManagerInterface $Wl, StoreWebsiteRelationInterface $kq, \Magento\User\Model\ResourceModel\User\CollectionFactory $UL, array $F2 = array())
    {
        $this->spUtility = $Kx;
        $this->adminRoleModel = $bY;
        $this->userGroupModel = $o1;
        $this->_storeManager = $Wl;
        $this->storeRepository = $dW;
        $this->storeWebsiteRelation = $kq;
        $this->_websiteCollectionFactory = $DU;
        $this->userCollFactory = $UL;
        parent::__construct($Gc, $F2);
    }
    public function getWebsiteCollection()
    {
        $Lw = $this->_websiteCollectionFactory->create();
        return $Lw;
    }
    public function getMetadataUrl()
    {
        return $this->getBaseUrl() . "\155\x6f\x73\160\x73\x61\155\154\57\141\143\164\x69\x6f\x6e\x73\57\115\x65\164\141\144\141\164\141";
    }
    public function getStoreIds($lA)
    {
        $TW = [];
        try {
            $TW = $this->storeWebsiteRelation->getStoreByWebsiteId($lA);
        } catch (Exception $ax) {
            $this->logger->error($ax->getMessage());
        }
        return $TW;
    }
    public function getCurrentWebsite()
    {
        return $this->spUtility->getCurrentWebsiteId();
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
    public function showWebsite($lA)
    {
        return $this->spUtility->getStoreConfig($lA);
    }
    public function getStoreName($lA)
    {
        return $this->_storeManager->getStore($lA)->getName();
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
        return $this->spUtility->getAdminCssUrl("\x61\144\155\x69\x6e\x53\145\164\164\151\156\147\x73\x2e\x63\x73\163");
    }
    public function getAdminJSURL()
    {
        return $this->spUtility->getAdminJSUrl("\x61\x64\x6d\151\156\123\x65\164\164\x69\x6e\147\x73\x2e\152\x73");
    }
    public function getIntlTelInputJs()
    {
        return $this->spUtility->getAdminJSUrl("\151\x6e\x74\154\124\x65\x6c\x49\156\x70\165\x74\x2e\x6d\151\156\56\x6a\x73");
    }
    public function getX509Cert()
    {
        return $this->spUtility->getStoreConfig(SPConstants::X509CERT);
    }
    public function getTestUrl($rQ = NULL)
    {
        return $this->getSPInitiatedUrl(SPConstants::TEST_RELAYSTATE, $rQ);
    }
    public function getIssuerUrl()
    {
        return $this->spUtility->getIssuerUrl();
    }
    public function getBaseUrl()
    {
        return $this->spUtility->getBaseUrl();
    }
    public function getExtensionPageUrl($ie)
    {
        return $this->spUtility->getAdminUrl("\155\157\x73\160\163\x61\155\x6c\x2f" . $ie . "\57\151\x6e\x64\x65\170");
    }
    public function getCurrentActiveTab()
    {
        $ie = $this->getUrl("\52\x2f\52\57\x2a", ["\137\x63\x75\162\x72\x65\x6e\x74" => true, "\137\x75\163\x65\x5f\x72\145\167\x72\x69\164\145" => false]);
        $MH = strpos($ie, "\155\x6f\163\160\x73\141\x6d\x6c\57") + 9;
        $Pm = strpos($ie, "\57\x69\156\144\145\x78\57\x6b\x65\171");
        return substr($ie, $MH, $Pm - $MH);
    }
    public function getSPInitiatedUrl($qY = NULL, $rQ = NULL)
    {
        return $this->spUtility->getSPInitiatedUrl($qY, $rQ);
    }
    public function getAccountMatcher()
    {
        return $this->spUtility->getStoreConfig(SPConstants::MAP_MAP_BY);
    }
    public function getAllRoles()
    {
        $OC = $this->adminRoleModel->toOptionArray();
        return $OC;
    }
    public function getAllGroups()
    {
        return $this->userGroupModel->toOptionArray();
    }
    public function getDefaultRole()
    {
        $wR = $this->spUtility->getStoreConfig(SPConstants::MAP_DEFAULT_ROLE);
        return !$this->spUtility->isBlank($wR) ? $wR : SPConstants::DEFAULT_ROLE;
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
        $Xu = $this->spUtility->getStoreConfig(SPConstants::BUTTON_TEXT);
        $V6 = $this->spUtility->getStoreConfig(SPConstants::IDP_NAME);
        return !$this->spUtility->isBlank($Xu) ? $Xu : "\114\157\147\x69\156\40\x77\x69\164\x68\x20" . $V6;
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
        return $this->spUtility->getAdminCertResourceUrl("\x73\160\55\x63\x65\162\x74\x69\146\x69\x63\141\164\x65\56\x63\x72\164");
    }
    public function isVerified()
    {
        return $this->spUtility->mclv();
    }
    public function getLogoutUrl()
    {
        return $this->spUtility->getStoreConfig(SPConstants::SAML_SLO_URL);
    }
    public function getAdminLogoutUrl()
    {
        return $this->spUtility->getLogoutUrl();
    }
    public function getCustomerLoginUrl()
    {
        return $this->spUtility->getCustomerLoginUrl();
    }
    public function getAdminLoginUrl()
    {
        return $this->spUtility->getAdminLoginUrl();
    }
    public function isAutoRedirectEnabled($Mu)
    {
        $R5 = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($R5 == $Mu) {
            goto V9;
        }
        return 0;
        goto Z1;
        V9:
        return $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT);
        Z1:
    }
    public function isByBackDoorEnabled()
    {
        return $this->spUtility->getStoreConfig(SPConstants::BACKDOOR);
    }
    public function getTable()
    {
        $yn = $this->spUtility->getStoreConfig(SPConstants::MAP_TABLE);
        return !$this->spUtility->isBlank($yn) ? $yn : '';
    }
    public function getDisallowUnlistedUserRole()
    {
        $pk = $this->spUtility->getStoreConfig(SPConstants::UNLISTED_ROLE);
        return !$this->spUtility->isBlank($pk) ? $pk : '';
    }
    public function getDisallowUserCreationIfRoleNotMapped()
    {
        $kC = $this->spUtility->getStoreConfig(SPConstants::CREATEIFNOTMAP);
        return !$this->spUtility->isBlank($kC) ? $kC : '';
    }
    public function getRolesMapped()
    {
        $m9 = $this->spUtility->getStoreConfig(SPConstants::ROLES_MAPPED);
        return !$this->spUtility->isBlank($m9) ? json_decode($m9) : array();
    }
    public function getGroupsMapped()
    {
        $m9 = $this->spUtility->getStoreConfig(SPConstants::GROUPS_MAPPED);
        return !$this->spUtility->isBlank($m9) ? json_decode($m9) : array();
    }
    public function getLogoutAutoRedirectUrl()
    {
        return $this->spUtility->getStoreConfig(SPConstants::LOGOUT_AUTO_REDIRECT_URL);
    }
    public function isAllPageAutoRedirectEnabled($Mu)
    {
        $R5 = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($R5 == $Mu) {
            goto XI;
        }
        return 0;
        goto RN;
        XI:
        return $this->spUtility->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        RN:
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
        return $this->spUtility->getFrontendUrl(SPConstants::SAML_LOGIN_URL, array("\x72\145\154\141\x79\123\164\x61\x74\145" => ''));
    }
    public function chk_Premium()
    {
        return $this->spUtility->getStoreConfig(SPConstants::SAMLSP_LK);
    }
    public function getDays()
    {
        $zg = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $cp = AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::LICENSE_EXPIRY_DATE), $zg);
        $Jm = new \DateTime("\100{$cp}");
        $Wv = new \DateTime();
        $bh = $Wv->diff($Jm)->format("\45\x72\45\x61");
        return $bh;
    }
    public function isTrialExpired()
    {
        return $this->spUtility->isTrialExpired();
    }
    public function check_license_expiry_date()
    {
        return $this->spUtility->check_license_expiry_date();
    }
    public function check_license_plan($xW)
    {
        return $this->spUtility->check_license_plan($xW);
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
        $oL = $this->getCurrentWebsite();
        return $this->spUtility->getStoreConfig($oL);
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
