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

/**
 * This class is used to denote our admin block for all our
 * backend templates. This class has certain commmon
 * functions which can be called from our admin template pages.
 */
class Sp extends \Magento\Framework\View\Element\Template
{

    public $spUtility;
    private $adminRoleModel;
    private $userGroupModel;

    /**
     * @var UserCollectionFactory
     */
    private $userCollFactory;

    public function __construct(
        Context                                                      $context,
        SPUtility                                                    $spUtility,
        \Magento\Authorization\Model\ResourceModel\Role\Collection   $adminRoleModel,
        \Magento\Customer\Model\ResourceModel\Group\Collection       $userGroupModel,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        StoreRepositoryInterface                                     $storeRepository,
        StoreManagerInterface                                        $storeManager,
        StoreWebsiteRelationInterface                                $storeWebsiteRelation,
        \Magento\User\Model\ResourceModel\User\CollectionFactory     $userCollFactory,
        array                                                        $data = [])
    {
        $this->spUtility = $spUtility;
        $this->adminRoleModel = $adminRoleModel;
        $this->userGroupModel = $userGroupModel;

        $this->_storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
        $this->storeWebsiteRelation = $storeWebsiteRelation;
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
        $this->userCollFactory = $userCollFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve websites collection of system
     *
     * @return Website Collection
     */
    public function getWebsiteCollection()
    {
        $collection = $this->_websiteCollectionFactory->create();
        return $collection;
    }

    public function getMetadataUrl()
    {
        return $this->getBaseUrl() . 'mospsaml/actions/Metadata';
    }

    /**
     * Get/Create Base URL of the site
     */
    public function getBaseUrl()
    {
        return $this->spUtility->getBaseUrl();
    }

    public function getStoreIds($id)
    {
        $storeId = [];
        try {
            $storeId = $this->storeWebsiteRelation->getStoreByWebsiteId($id);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $storeId;
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

    public function showWebsite($id)
    {
        return $this->spUtility->getStoreConfig($id);
    }

    /**
     * To get names of the stores.
     */

    public function getStoreName($id)
    {
        return $this->_storeManager->getStore($id)->getName();
    }


    /**
     * This function retrieves the miniOrange customer Email
     * from the database. To be used on our template pages.
     */
    public function getCustomerEmail()
    {
        return $this->spUtility->getStoreConfig(SPConstants::SAMLSP_EMAIL);
    }


    /**
     * This function retrieves the miniOrange customer key from the
     * database. To be used on our template pages.
     */
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

    /**
     * This function retrieves the miniOrange API key from the database.
     * To be used on our template pages.
     */
    public function getApiKey()
    {
        return $this->spUtility->getStoreConfig(SPConstants::API_KEY);
    }


    /**
     * This function retrieves the token key from the database.
     * To be used on our template pages.
     */
    public function getToken()
    {
        return $this->spUtility->getStoreConfig(SPConstants::TOKEN);
    }

    /**
     * This function checks if the SP has been configured or not.
     */
    public function isSPConfigured()
    {
        return $this->spUtility->isSPConfigured();
    }

    /**
     * This function gets the admin CSS URL to be appended to the
     * admin dashboard screen.
     */
    public function getAdminCssURL()
    {
        return $this->spUtility->getAdminCssUrl('adminSettings.css');
    }

    /**
     * This function gets the IntelTelInput JS URL to be appended
     * to admin pages to show country code dropdown on phone number
     * fields.
     */
    public function getIntlTelInputJs()
    {
        return $this->spUtility->getAdminJSUrl('intlTelInput.min.js');
    }

    /**
     * This function gets the admin JS URL to be appended to the
     * admin dashboard pages for plugin functionality
     */
    public function getAdminJSURL()
    {
        return $this->spUtility->getAdminJSUrl('adminSettings.js');
    }

    /**
     * This function fetches the X509 cert saved by the admin for the IDP
     * in the plugin settings.
     */
    public function getX509Cert()
    {
        return $this->spUtility->getStoreConfig(SPConstants::X509CERT);
    }


    /**
     * This function fetches/creates the TEST Configuration URL of the
     * Plugin.
     */
    public function getTestUrl($idp_name = NULL)
    {
        return $this->getSPInitiatedUrl(SPConstants::TEST_RELAYSTATE, $idp_name);
    }

    /**
     * Create/Get the SP initiated URL for the site.
     */
    public function getSPInitiatedUrl($relayState = NULL, $idp_name = NULL)
    {
        return $this->spUtility->getSPInitiatedUrl($relayState, $idp_name);
    }

    /**
     * Get/Create Issuer URL of the site
     */
    public function getIssuerUrl()
    {
        return $this->spUtility->getIssuerUrl();
    }

    /**
     * Create the URL for one of the SAML SP plugin
     * sections to be shown as link on any of the
     * template files.
     */
    public function getExtensionPageUrl($page)
    {
        return $this->spUtility->getAdminUrl('mospsaml/' . $page . '/index');
    }


    /**
     * Reads the Tab and retrieves the current active tab
     * if any.
     */
    public function getCurrentActiveTab()
    {
        $page = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => false]);
        $start = strpos($page, 'mospsaml/') + 9;
        $end = strpos($page, '/index/key');
        return substr($page, $start, $end - $start);
    }

    /**
     * This fetches the setting saved by the admin which decides if the
     * account should be mapped to username or email in Magento.
     */
    public function getAccountMatcher()
    {
        return $this->spUtility->getStoreConfig(SPConstants::MAP_MAP_BY);
    }

    /**
     * Get all admin roles set by the admin on his site.
     */
    public function getAllRoles()
    {
        $rolesCollection = $this->adminRoleModel->addFieldToFilter('role_type', 'G');
        // Convert the filtered collection to an options array
        $rolesOptionsArray = $rolesCollection->toOptionArray();
        return $rolesOptionsArray;
    }

    /**
     * Get all customer groups set by the admin on his site.
     */
    public function getAllGroups()
    {
        return $this->userGroupModel->toOptionArray();
    }

    /**
     * Get the default role to be set for the user if it
     * doesn't match any of the group mappings
     */
    public function getDefaultRole()
    {
        $defaultRole = $this->spUtility->getStoreConfig(SPConstants::MAP_DEFAULT_ROLE);
        return !$this->spUtility->isBlank($defaultRole) ? $defaultRole : SPConstants::DEFAULT_ROLE;
    }

    /**
     * This fetches the registration status in the plugin.
     * Used to detect at what stage is the user at for
     * registration with miniOrange
     */
    public function getRegistrationStatus()
    {
        return $this->spUtility->getStoreConfig(SPConstants::REG_STATUS);
    }

    /**
     * Get the Current Admin user from session
     */
    public function getCurrentAdminUser()
    {
        return $this->spUtility->getCurrentAdminUser();
    }

    /**
     * Fetches/Creates the text of the button to be shown
     * for SP inititated login from the admin / customer
     * login pages.
     */
    public function getSSOButtonText()
    {
        $buttonText = $this->spUtility->getStoreConfig(SPConstants::BUTTON_TEXT);
        $idpName = $this->spUtility->getStoreConfig(SPConstants::IDP_NAME);
        return !$this->spUtility->isBlank($buttonText) ? $buttonText : 'Login with ' . $idpName;
    }

    /**
     * Get base url of miniorange
     */
    public function getMiniOrangeUrl()
    {
        return $this->spUtility->getMiniOrangeUrl();
    }

    /**
     * This function checks if the user has completed the registration
     * and verification process. Returns TRUE or FALSE.
     */
    public function isEnabled()
    {
        return $this->spUtility->micr()
            && $this->spUtility->mclv();
    }


    /* ===================================================================================================
                THE FUNCTIONS BELOW ARE PREMIUM PLUGIN SPECIFIC AND DIFFER IN THE FREE VERSION
       ===================================================================================================
     */

    /**
     * Get the SP Cert URL
     */
    public function getPublicCert()
    {
        return $this->spUtility->getAdminCertResourceUrl('sp-certificate.crt');
    }

    /*===========================================================================================
						THESE ARE PREMIUM PLUGIN SPECIFIC FUNCTIONS
    =============================================================================================*/

    /**
     * Just check and return if the user has verified his
     * license key to activate the plugin. Mostly used
     * on the account page to show the verify license key
     * screen.
     */
    public function isVerified()
    {
        return $this->spUtility->mclv();
    }

    /**
     * Is auto redirect enabled by the admin
     */
    public function isAutoRedirectEnabled($default_provider)
    {
        $auto_redirect_app = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($auto_redirect_app == $default_provider) {
            return $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT);
        } else {
            return 0;
        }

    }

    /**
     * Is Back Door bypass URL enabled by the admin
     */
    public function isByBackDoorEnabled()
    {
        return $this->spUtility->getStoreConfig(SPConstants::BACKDOOR);
    }

    /**
     * method to get table
     */
    public function getTable()
    {
        $amTable = $this->spUtility->getStoreConfig(SPConstants::MAP_TABLE);
        return !$this->spUtility->isBlank($amTable) ? $amTable : '';
    }

    /**
     * This fetches the setting saved by the admin which doesn't allow
     * roles to be assigned to unlisted users.
     */
    public function getDisallowUnlistedUserRole()
    {
        $disallowUnlistedRole = $this->spUtility->getStoreConfig(SPConstants::UNLISTED_ROLE);
        return !$this->spUtility->isBlank($disallowUnlistedRole) ? $disallowUnlistedRole : '';
    }

    /**
     * This fetches the setting saved by the admin which doesn't allow
     * users to be created if roles are not mapped based on the admin settings.
     */
    public function getDisallowUserCreationIfRoleNotMapped()
    {
        $disallowUserCreationIfRoleNotMapped = $this->spUtility->getStoreConfig(SPConstants::CREATEIFNOTMAP);
        return !$this->spUtility->isBlank($disallowUserCreationIfRoleNotMapped) ? $disallowUserCreationIfRoleNotMapped : '';
    }

    /**
     * This fetches the setting saved by the admin which maps the
     * attributes in the SAML response to the Magento Admin Roles.
     *
     * @return array
     */
    public function getRolesMapped()
    {
        $rolesMapped = $this->spUtility->getStoreConfig(SPConstants::ROLES_MAPPED);
        return !$this->spUtility->isBlank($rolesMapped) ? json_decode($rolesMapped) : array();
    }

    /**
     * This fetches the setting saved by the admin which maps the
     * attributes in the SAML response to the Magento Customer Groups.
     */
    public function getGroupsMapped()
    {
        $rolesMapped = $this->spUtility->getStoreConfig(SPConstants::GROUPS_MAPPED);
        return !$this->spUtility->isBlank($rolesMapped) ? json_decode($rolesMapped) : array();
    }

    /**
     * This function fetches the Logout Autoredirect URL
     */
    public function getLogoutAutoRedirectUrl()
    {
        return $this->spUtility->getStoreConfig(SPConstants::LOGOUT_AUTO_REDIRECT_URL);
    }

    public function isAllPageAutoRedirectEnabled($default_provider)
    {
        $auto_redirect_app = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($auto_redirect_app == $default_provider) {
            return $this->spUtility->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        } else {
            return 0;
        }
    }

    /**
     * This function fetches the appname to which the autoredirect is enabled
     */
    public function getAutoRedirect_AppName()
    {
        return $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
    }

    /**
     * This function checks if the AllPageAutoredirect has been enabled
     */
    public function isAllPageAutoRedirectEnabled_action()
    {

        return $this->spUtility->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);

    }

    /**
     * This function is used to get the generic SSO URL
     */
    public function getSSOButtonURL()
    {
        return $this->spUtility->getFrontendUrl(SPConstants::SAML_LOGIN_URL,
            array("relayState" => ''));
    }

    public function chk_Premium()
    {
        return $this->spUtility->getStoreConfig(SPConstants::SAMLSP_LK);
    }

    //This function checks if license key is present

    /**
     * get license remaining days to show in the navbar
     */
    public function getDays()
    {
        $key = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $date = AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::LICENSE_EXPIRY_DATE), $key);
        $expiryDate = new \DateTime("@$date");
        $now = new \DateTime();
        $daysTillExpiry = $now->diff($expiryDate)->format("%r%a");
        return $daysTillExpiry;
    }

    /**
     * This function checks if the license has been expired
     */
    public function isTrialExpired()
    {
        return $this->spUtility->isTrialExpired();
    }

    /**
     * This function checks the license expiry date
     */
    public function check_license_expiry_date()
    {
        return $this->spUtility->check_license_expiry_date();
    }

    public function check_license_plan($lvl)
    {
        return $this->spUtility->check_license_plan($lvl);
    }

    /**
     * To get the configuration of Identity Provider
     */
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
        $website_id = $this->getCurrentWebsite();
        return $this->spUtility->getStoreConfig($website_id);
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

    //Function to check if autoredirect is enabled
    public function isAdminAutoRedirectEnabled($default_provider)
    {   $auto_redirect_app = $this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if($auto_redirect_app==$default_provider){
            return $this->spUtility->getStoreConfig(SPConstants::ADMIN_AUTO_REDIRECT);
        }else{
            return 0;
        }

    }

    //Function to get Admin Backdoor URL
    public function getAdminBackdoorUrl()
    {
        $adminBaseUrl = $this->spUtility->getAdminBaseUrl();
        $adminBaseUrl = rtrim($adminBaseUrl,'/');
        return $adminBaseUrl."?backdoor=true";
    }

}
