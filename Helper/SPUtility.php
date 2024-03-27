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

/**
 * This class contains some common Utility functions
 * which can be called from anywhere in the module. This is
 * mostly used in the action classes to get any utility
 * function or data from the database.
 */
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

    public function __construct(
        ScopeConfigInterface                $scopeConfig,
        UserFactory                         $adminFactory,
        CustomerFactory                     $customerFactory,
        UrlInterface                        $urlInterface,
        WriterInterface                     $configWriter,
        Repository                          $assetRepo,
        \Magento\Backend\Helper\Data        $helperBackend,
        Url                                 $frontendUrl,
        \Magento\Backend\Model\Session      $adminSession,
        \Magento\Customer\Model\Session     $customerSession,
        \Magento\Backend\Model\Auth\Session $authSession,
        TypeListInterface                   $cacheTypeList,
        Pool                                $cacheFrontendPool,
        File                                $fileSystem,
        LoggerInterface                     $logger,
        ReinitableConfigInterface           $reinitableConfig,
        StoreManagerInterface               $storeManager,
        CustomerRepositoryInterface         $customerRepository,
        Website                             $websiteModel,
        WebsiteRepositoryInterface          $websiteRepository,
        ResourceConnection                  $resource,
        ResultFactory                       $resultFactory,
        UserFactory                         $userFactory,
        \Magento\Backend\Helper\Data        $backendHelper,
        ResponseFactory                     $responseFactory,
        ManagerInterface                    $messageManager,
        MiniorangeSamlIDPsFactory           $miniorangeSamlIDPsFactory,
        ResponseInterface                   $response,
        ProductMetadataInterface            $productMetadata,
        DateTime                            $dateTime,
        Logger                              $logger2
    )
    {
        $this->adminSession = $adminSession;
        $this->customerSession = $customerSession;
        $this->authSession = $authSession;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->fileSystem = $fileSystem;
        $this->logger = $logger;
        $this->userFactory = $userFactory;
        $this->websiteRepository = $websiteRepository;
        $this->websiteModel = $websiteModel;
        $this->_storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->customerRepository = $customerRepository;
        $this->reinitableConfig = $reinitableConfig;
        $this->resource = $resource;
        $this->messageManager = $messageManager;
        $this->backendHelper = $backendHelper;
        $this->responseFactory = $responseFactory;
        $this->_logger = $logger2;
        $this->_response = $response;
        $this->productMetadata = $productMetadata;
        parent::__construct($scopeConfig, $adminFactory, $customerFactory, $urlInterface,
            $configWriter, $assetRepo, $helperBackend, $frontendUrl, $dateTime, $productMetadata, $miniorangeSamlIDPsFactory);
    }

    /**
     * This function returns phone number as a obfuscated
     * string which can be used to show as a message to the user.
     *
     * @param $phone references the phone number.
     */
    public function getHiddenPhone($phone)
    {
        $hidden_phone = 'xxxxxxx' . substr($phone, strlen($phone) - 3);
        return $hidden_phone;
    }

    /**
     * This function checks if cURL has been installed
     * or enabled on the site.
     *
     * @return True or False
     */
    public function isCurlInstalled()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return 1;
        } else
            return 0;
    }

    /**
     * This function checks if the phone number is in the correct format or not.
     *
     * @param $phone refers to the phone number entered
     */
    public function validatePhoneNumber($phone)
    {
        if (!preg_match(MoIDPConstants::PATTERN_PHONE, $phone, $matches))
            return FALSE;
        else
            return TRUE;
    }

    /**
     * This function is used to obfuscate and return
     * the email in question.
     *
     * @param $email refers to the email id to be obfuscated
     * @return obfuscated email id.
     */
    public function getHiddenEmail($email)
    {
        if (empty($email) || trim($email) === '')
            return "";

        $emailsize = strlen($email);
        $partialemail = substr($email, 0, 1);
        $temp = strrpos($email, "@");
        $endemail = substr($email, $temp - 1, $emailsize);
        for ($i = 1; $i < $temp; $i++)
            $partialemail = $partialemail . 'x';

        $hiddenemail = $partialemail . $endemail;

        return $hiddenemail;
    }

    /**
     * get Admin Session data based of on the key
     *
     * @param $key
     * @param $remove
     */
    public function getAdminSessionData($key, $remove = false)
    {
        return $this->adminSession->getData($key, $remove);
    }

    /**
     * Get customer Session data based off on the key
     *
     * @param $key
     * @param $remove
     */
    public function getSessionData($key, $remove = false)
    {
        return $this->customerSession->getData($key, $remove);
    }

    /**
     * unset customer Session Data
     *
     * @param $key
     * @param $remove
     */
    public function unsetSessionData($key, $remove = false)
    {
        return $this->customerSession->unsetData($key, $remove);
    }

    /**
     * Set Session data for logged in user based on if he/she
     * is in the backend of frontend. Call this function only if
     * you are not sure where the user is logged in at.
     *
     * @param $key
     * @param $value
     */
    public function setSessionDataForCurrentUser($key, $value)
    {
        if ($this->customerSession->isLoggedIn())
            $this->setSessionData($key, $value);
        elseif ($this->authSession->isLoggedIn())
            $this->setAdminSessionData($key, $value);
    }

    /**
     * set customer Session Data
     *
     * @param $key
     * @param $value
     */
    public function setSessionData($key, $value)
    {
        return $this->customerSession->setData($key, $value);
    }

    /**
     * set Admin Session Data
     *
     * @param $key
     * @param $value
     */
    public function setAdminSessionData($key, $value)
    {
        return $this->adminSession->setData($key, $value);
    }

    /**
     * Check if the admin has configured the plugin with
     * the Identity Provier. Returns true or false
     */
    public function isSPConfigured()
    {
        $loginUrl = $this->getStoreConfig(SPConstants::IDP_NAME);
        return $this->isBlank($loginUrl) ? FALSE : TRUE;
    }

    /**
     * This function checks if a value is set or
     * empty. Returns true if value is empty
     *
     * @param $value references the variable passed.
     * @return True or False
     */
    public function isBlank($value)
    {
        if (empty($value)) return TRUE;
        return FALSE;
    }

    /**
     * This function is used to check if customer has completed
     * the registration process. Returns TRUE or FALSE. Checks
     * for the email and customerkey in the database are set
     * or not.
     */
    public function micr()
    {
        if ($this->check_license_plan(4))
            return true;

        $email = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $key = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        return !$this->isBlank($email) && !$this->isBlank($key) ? TRUE : FALSE;
    }

    public function check_license_plan($lvl)
    {
        return ($this->get_license_plan() >= $lvl);
    }

    public function get_license_plan()
    {
        $version = SPConstants::LICENSE_PLAN;
        return $version === 'magento_saml_trial_plan' ? 4 : ($version === 'magento_saml_enterprise_plan' ? 3 : ($version === 'magento_saml_premium_plan' ? 2 : ($version === 'magento_saml_standard_plan' ? 1 : 0)));
    }

    /**
     * Check if there's an active session of the user
     * for the frontend or the backend. Returns TRUE
     * or FALSE
     */
    public function isUserLoggedIn()
    {
        return $this->customerSession->isLoggedIn()
            || $this->authSession->isLoggedIn();
    }

    /**
     * Get the Current Admin User who is logged in
     */
    public function getCurrentAdminUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * Get the Current Admin User who is logged in
     */
    public function getCurrentUser()
    {
        return $this->customerSession->getCustomer();
    }

    public function getCustomer($id)
    {
        return $this->customerRepository->getById($id);
    }

    /**
     * Desanitize the cert
     */
    public function desanitizeCert($cert)
    {
        return SAML2Utilities::desanitize_certificate($cert);
    }

    /**
     * Sanitize the cert
     */
    public function sanitizeCert($cert)
    {
        return SAML2Utilities::sanitize_certificate($cert);
    }

    /**
     * Get data in the file specified by the path
     */
    public function getFileContents($file)
    {
        return $this->fileSystem->fileGetContents($file);
    }

    /**
     * Put data in the file specified by the path
     */
    public function putFileContents($file, $data)
    {
        $this->fileSystem->filePutContents($file, $data);
    }

    /**
     * Get the Current User's logout url
     */
    public function getLogoutUrl()
    {
        if ($this->customerSession->isLoggedIn()) return $this->getUrl('customer/account/logout');
        if ($this->authSession->isLoggedIn()) return $this->getAdminUrl('adminhtml/auth/logout');
        return '/';
    }

    /*===========================================================================================
                        THESE ARE PREMIUM PLUGIN SPECIFIC FUNCTIONS
    =============================================================================================*/

    /**
     * Function to get the url based on where the user is.
     *
     * @param $url
     * @return
     */
    public function getUrl($url, $params = array())
    {
        return parent::getUrl($url, $params);
    }

    public function getcustomerLogoutUrl()
    {
        return $this->getBaseUrl() . 'customer/account/logout';
    }

    /**
     * This function is used to check if customer has completed
     * the registration process. Returns TRUE or FALSE. Checks
     * for the email and customerkey in the database are set
     * or not. Then checks if license key has been verified.
     */
    public function mclv()
    {
        if ($this->check_license_plan(4))
            return true;

        $token = $this->getStoreConfig(SPConstants::TOKEN);
        $isVerified = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::SAMLSP_CKL), $token);
        $licenseKey = $this->getStoreConfig(SPConstants::SAMLSP_LK);
        return $isVerified == "true" && !$this->isBlank($licenseKey) ? TRUE : FALSE;
    }

    /**
     * This function is used to validate the license key entered by
     * the user by calling the vml cURL function.
     *
     * @return JSONEncoded response
     */
    public function vml($code)
    {
        $customerKey = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $apiKey = $this->getStoreConfig(SPConstants::API_KEY);
        $content = Curl::vml($customerKey, $apiKey, $code, $this->getBaseUrl());
        return $content;
    }

    /*===========================================================================================
                        THESE ARE PREMIUM PLUGIN SPECIFIC FUNCTIONS
    =============================================================================================*/

    /**
     * This function is updates the status of the licenseKey
     * on the server by calling the mius cURL function.
     *
     * @return JSONEncoded response
     */
    public function mius()
    {
        $customerKey = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $apiKey = $this->getStoreConfig(SPConstants::API_KEY);
        $token = $this->getStoreConfig(SPConstants::TOKEN);
        $code = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::SAMLSP_LK), $token);
        $content = Curl::mius($customerKey, $apiKey, trim($code));
        return $content;
    }

    public function getB2bStoreUrl()
    {
        $b2bStoreUrl = $this->getStoreConfig(SPConstants::B2B_STORE_URL);
        return !$this->isBlank($b2bStoreUrl) ? $b2bStoreUrl : '';
    }

    public function getB2cStoreUrl()
    {
        $b2cStoreUrl = $this->getStoreConfig(SPConstants::B2C_STORE_URL);
        return !$this->isBlank($b2cStoreUrl) ? $b2cStoreUrl : '';
    }

    /**
     * Is the option to auto create Admin while SSO enabled
     * by the admin.
     */
    public function getAutoCreateAdmin()
    {
        return $this->getStoreConfig(SPConstants::AUTO_CREATE_ADMIN);
    }

    /**
     * Is the option to auto create Customer while SSO
     * by the admin.
     */
    public function getAutoCreateCustomer()
    {
        return $this->getStoreConfig(SPConstants::AUTO_CREATE_CUSTOMER);
    }

    /**
     * if B2c flow is disabled
     */
    public function getDisableB2C()
    {
        return $this->getStoreConfig(SPConstants::DISABLE_B2C);
    }


    /* Get customer object by id */

    /**
     * Check if the admin has configured the plugin with
     * SLO settings. Returns true or false
     */
    public function isSLOConfigured()
    {
        $logoutUrl = $this->getStoreConfig(SPConstants::SAML_SLO_URL);
        return $this->isBlank($logoutUrl) ? FALSE : TRUE;
    }

    public function getAdminUserById($id)
    {
        $user = $this->userFactory->create()->load($id);
//      $user->getStoredData();
        return $user;
    }

    /**
     *** Magento Store/Website specific Methods
     * @param $givenUrl
     * @return
     */
    public function getBaseUrlFromUrl($givenUrl)
    {
        $this->log_debug(" SpUtility: getBaseUrlFromUrl() for:" . $givenUrl);
        $websites = $this->_storeManager->getStores();
        foreach ($websites as $store) {
            $url = $store->getBaseUrl();
            $pos = strpos($givenUrl, $url);
            if ($pos !== false) {
                ;
                $this->log_debug("SpUtility : getBaseUrlFromUrl(). " . $url);
                return $url;
            }
        }
        $givenUrl = parse_url($givenUrl, PHP_URL_HOST);
        $this->log_debug("SpUtility: getBaseUrlFromUrl():" . $givenUrl . '/');
        return $givenUrl;
    }

    //Get Store Object by store_id

    /**
     *Common Log Method .. Accessible in all classes through
     **/
    public function log_debug($msg = "", $obj = null)
    {

        if (is_object($msg)) {
            $this->customlog("MO SAML  : " . print_r($obj, true));
        } else {
            $this->customlog("MO SAML : " . $msg);

        }

        if ($obj != null) {
            $this->customlog("MO SAML : " . var_export($obj, true));


        }
    }

    /**
     * This function print custom log in var/log/mo_saml.log file.
     */
    public function customlog($txt)
    {

        $this->isLogEnable() ? $this->_logger->debug($txt) : NULL;
    }

    //get websiteName By website_id

    public function isLogEnable()
    {
        return $this->getStoreConfig(SPConstants::ENABLE_DEBUG_LOG);
    }

    //get website Object By website_id

    public function getStoreById($id)
    {
        return $this->_storeManager->getStore($id);
    }

    public function getWebsiteCode()
    {
        return $this->_storeManager->getWebsite()->getCode();
    }


    /**
     ****DATABASE Querying Methods
     * @param $table
     * @param $data
     */

    //Insert a row in any table
    public function getWebsiteName($websiteId)
    {
        $collection = $this->_websiteModel->load($websiteId, 'website_id');
        $websiteData = $collection->getData();
        return $websiteData[0]['name'];
    }

    public function getWebsiteById($id)
    {
        return $this->websiteRepository->getById($id);
    }


    //Update a set of values of a row in any table


    public function insertRowInTable($table, $data)
    {
        $this->resource->getConnection()->insertMultiple($table, $data);
    }

    /**
     * Update a set of values of a row in any table
     **/
    public function updateColumnInTable($table, $colName, $colValue, $idKey, $idValue)
    {
        $this->log_debug("updateColumnInTable" . $colName);
        $connection = $this->resource->getConnection();
        if ($connection->tableColumnExists($table, $colName) === false) {
            $connection->addColumn($table, $colName, array(
                'type' => 'text',
                'nullable' => false,
                'length' => 255,
                'after' => null, // column name to insert new column after
                'comment' => $colName
            ));

        }
        $this->resource->getConnection()->update(
            $table, [$colName => $colValue],
            [$idKey . " = ?" => $idValue]
        );
    }

    //check if flow started from Admin Url
    //It just checks if relayState contains admin base url

    public function updateRowInTable($table, $valArray, $idKey, $idValue)
    {
        $this->log_debug("updateRowInTable");
        $this->resource->getConnection()->update(
            $table, $valArray, [$idKey . " = ?" => $idValue]
        );
    }

    /**
     * Get value of any column from a table.
     * @param $table
     * @param $col
     * @param $idKey
     * @param $idValue
     * @return
     */
    public function getValueFromTableSQL($table, $col, $idKey, $idValue)
    {
        $connection = $this->resource->getConnection();
        //Select Data from table
        $sqlQuery = "SELECT " . $col . " FROM " . $table . " WHERE " . $idKey . " = " . $idValue;
        $this->log_debug("SQL: " . $sqlQuery);
        $result = $connection->fetchOne($sqlQuery);
        $this->log_debug("result sql: " . $result);
        return $result;
    }

    public function checkIfFlowStartedFromBackend($relayState)
    {
        $admin_path = $this->helperBackend->getAreaFrontName();
        if (str_contains($relayState, $admin_path)) {
            $this->log_debug("checkIfFlowStartedFromBackend: true");
            return true;
        }
        $this->log_debug("checkIfFlowStartedFromBackend: false");
        return false;
    }

    public function getadmincode()
    {
        $admin_path = $this->helperBackend->getAreaFrontName();
        return $admin_path;
    }

    public function handle_upload_metadata($file, $url, $params)
    {
        if (!empty($file) || !empty($url)) {
            if (!empty($file['tmp_name'])) {
                $file = file_get_contents($file['tmp_name']);
            } else {
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $arrContextOptions = array(
                    "ssl" => array(
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ),
                );
                if (empty($url)) {
                    return;
                } else {
                    $file = file_get_contents($url, false, stream_context_create($arrContextOptions));
                }
            }
            $this->upload_metadata($file, $params);
        }
    }

    public function upload_metadata($file, $params)
    {
        $document = new DOMDocument();
        $document->loadXML($file);
        restore_error_handler();
        $first_child = $document->firstChild;
        if (!empty($first_child)) {
            $metadata = new IDPMetadataReader($document);
            $identity_providers = $metadata->getIdentityProviders();
            if (empty($identity_providers)) {
                return;
            }
            foreach ($identity_providers as $key => $idp) {
                $saml_login_url = $idp->getLoginURL('HTTP-Redirect');
                $saml_issuer = $idp->getEntityID();
                $saml_x509_certificate = $idp->getSigningCertificate();
                $database_name = 'saml';
                $updatefieldsarray = array(
                    'samlIssuer' => !empty($saml_issuer) ? $saml_issuer : 0,
                    'ssourl' => !empty($saml_login_url) ? $saml_login_url : 0,
                    'loginBindingType' => 'HttpRedirect',
                    'certificate' => !empty($saml_x509_certificate) ? $saml_x509_certificate[0] : 0,
                );

                $this->generic_update_query($database_name, $updatefieldsarray, $params);
                break;
            }
            return;
        } else {

            return;
        }
    }

    public function generic_update_query($database_name, $updatefieldsarray, $params)
    {
        $this->log_debug("In generic_update_query function");
        $idp_obj = json_encode($updatefieldsarray);

        if (empty($params['saml_identity_name']))
            $params['saml_identity_name'] = $params['selected_provider'];

        $mo_idp_app_name = trim($params['saml_identity_name']);
        $collection = $this->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $mo_idp_app_name) {
                $idpDetails = $item->getData();
            }
        }
        foreach ($updatefieldsarray as $key => $value) {
            if ($key == 'samlIssuer')
                $mo_idp_entity_id = trim($value);
            if ($key == 'ssourl')
                $mo_idp_saml_login_url = trim($value);
            if ($key == 'loginBindingType')
                $mo_idp_saml_login_binding = trim($value);
            if ($key == 'certificate')
                $mo_idp_x509_certificate = SAML2Utilities::sanitize_certificate(trim($value));
        }

        $mo_idp_saml_logout_url = !empty($params['saml_logout_url']) ? trim($params['saml_logout_url']) : '';
        $mo_idp_saml_logout_binding = 'HttpRedirect';
        $mo_idp_response_signed = !empty($params['saml_response_signed']) && $params['saml_response_signed'] == 'Yes' ? 1 : 0;
        $mo_idp_assertion_signed = !empty($params['saml_assertion_signed']) && $params['saml_assertion_signed'] == 'Yes' ? 1 : 0;
        $mo_idp_show_admin_link = !empty($idpDetails['show_admin_link']) && $idpDetails['show_admin_link'] == true ? 1 : 0;
        $mo_idp_show_customer_link = !empty($idpDetails['show_customer_link']) && $idpDetails['show_customer_link'] == true ? 1 : 0;
        $mo_idp_auto_create_admin_users = !empty($idpDetails['auto_create_admin_users']) && $idpDetails['auto_create_admin_users'] == true ? 1 : 0;
        $mo_idp_auto_create_customers = !empty($idpDetails['auto_create_customers']) && $idpDetails['auto_create_customers'] == true ? 1 : 0;
        $mo_idp_disable_b2c = !empty($idpDetails['disable_b2c']) && $idpDetails['disable_b2c'] == true ? 1 : 0;
        $mo_idp_force_authentication_with_idp = !empty($idpDetails['force_authentication_with_idp']) && $idpDetails['force_authentication_with_idp'] == true ? 1 : 0;
        $mo_idp_auto_redirect_to_idp = !empty($idpDetails['auto_redirect_to_idp']) && $idpDetails['auto_redirect_to_idp'] == true ? 1 : 0;
        $mo_idp_link_to_initiate_sso = !empty($idpDetails['link_to_initiate_sso']) && $idpDetails['link_to_initiate_sso'] == true ? 1 : 0;
        $mo_idp_update_attributes_on_login = !empty($idpDetails['update_attributes_on_login']) ? $idpDetails['update_attributes_on_login'] : 'unchecked';
        $mo_idp_create_magento_account_by = !empty($idpDetails['create_magento_account_by']) ? $idpDetails['create_magento_account_by'] : '';
        $mo_idp_email_attribute = !empty($idpDetails['email_attribute']) ? $idpDetails['email_attribute'] : '';
        $mo_idp_username_attribute = !empty($idpDetails['username_attribute']) ? $idpDetails['username_attribute'] : '';
        $mo_idp_firstname_attribute = !empty($idpDetails['firstname_attribute']) ? $idpDetails['firstname_attribute'] : '';
        $mo_idp_lastname_attribute = !empty($idpDetails['lastname_attribute']) ? $idpDetails['lastname_attribute'] : '';
        $mo_idp_group_attribute = !empty($idpDetails['group_attribute']) ? $idpDetails['group_attribute'] : '';
        $mo_idp_billing_city_attribute = !empty($idpDetails['billing_city_attribute']) ? $idpDetails['billing_city_attribute'] : '';
        $mo_idp_billing_state_attribute = !empty($idpDetails['billing_state_attribute']) ? $idpDetails['billing_state_attribute'] : '';
        $mo_idp_billing_country_attribute = !empty($idpDetails['billing_country_attribute']) ? $idpDetails['billing_country_attribute'] : '';
        $mo_idp_billing_address_attribute = !empty($idpDetails['billing_address_attribute']) ? $idpDetails['billing_address_attribute'] : '';
        $mo_idp_billing_phone_attribute = !empty($idpDetails['billing_phone_attribute']) ? $idpDetails['billing_phone_attribute'] : '';
        $mo_idp_billing_zip_attribute = !empty($idpDetails['billing_zip_attribute']) ? $idpDetails['billing_zip_attribute'] : '';
        $mo_idp_shipping_city_attribute = !empty($idpDetails['shipping_city_attribute']) ? $idpDetails['shipping_city_attribute'] : '';
        $mo_idp_shipping_state_attribute = !empty($idpDetails['shipping_state_attribute']) ? $idpDetails['shipping_state_attribute'] : '';
        $mo_idp_shipping_country_attribute = !empty($idpDetails['shipping_country_attribute']) ? $idpDetails['shipping_country_attribute'] : '';
        $mo_idp_shipping_address_attribute = !empty($idpDetails['shipping_address_attribute']) ? $idpDetails['shipping_address_attribute'] : '';
        $mo_idp_shipping_phone_attribute = !empty($idpDetails['shipping_phone_attribute']) ? $idpDetails['shipping_phone_attribute'] : '';
        $mo_idp_shipping_zip_attribute = !empty($idpDetails['shipping_zip_attribute']) ? $idpDetails['shipping_zip_attribute'] : '';
        $mo_idp_b2b_attribute = !empty($idpDetails['b2b_attribute']) ? $idpDetails['b2b_attribute'] : '';
        $mo_idp_custom_tablename = !empty($idpDetails['custom_tablename']) ? $idpDetails['custom_tablename'] : '';
        $mo_idp_custom_attributes = !empty($idpDetails['custom_attributes']) ? $idpDetails['custom_attributes'] : '';
        $mo_idp_do_not_autocreate_if_roles_not_mapped = !empty($idpDetails['do_not_autocreate_if_roles_not_mapped']) && $idpDetails['do_not_autocreate_if_roles_not_mapped'] == true ? 1 : 0;
        $mo_idp_update_backend_roles_on_sso = !empty($idpDetails['update_backend_roles_on_sso']) && $idpDetails['update_backend_roles_on_sso'] == true ? 1 : 0;
        $mo_idp_update_frontend_groups_on_sso = !empty($idpDetails['update_frontend_groups_on_sso']) && $idpDetails['update_frontend_groups_on_sso'] == true ? 1 : 0;
        $mo_idp_default_group = !empty($idpDetails['default_group']) ? $idpDetails['default_group'] : '';
        $mo_idp_default_role = !empty($idpDetails['default_role']) ? $idpDetails['default_role'] : '';
        $mo_idp_groups_mapped = !empty($idpDetails['groups_mapped']) ? $idpDetails['groups_mapped'] : '';
        $mo_idp_roles_mapped = !empty($idpDetails['roles_mapped']) ? $idpDetails['roles_mapped'] : '';
        $mo_saml_logout_redirect_url = !empty($idpDetails['saml_logout_redirect_url']) ? $idpDetails['saml_logout_redirect_url'] : '';
        $billinandshippingchcekbox = !empty($idpDetails['saml_enable_billingandshipping']) ? $idpDetails['saml_enable_billingandshipping'] : 'none';
        $sameasbilling = !empty($idpDetails['saml_sameasbilling']) ? $idpDetails['saml_sameasbilling'] : 'none';
        $mo_saml_headless_sso = !empty($idpDetails['mo_saml_headless_sso']) && $idpDetails['mo_saml_headless_sso'] == true ? 1 : 0;
        $mo_saml_frontend_post_url = !empty($idpDetails['mo_saml_frontend_post_url']) ? $idpDetails['mo_saml_frontend_post_url'] : '';

        if (!is_null($idpDetails)) {
            $this->deleteIDPApps((int)$idpDetails['id']);
        } else {
            $this->checkIdpLimit();
        }
        if (!empty($mo_idp_app_name))
            $this->setIDPApps(
                $mo_idp_app_name,
                $mo_idp_entity_id,
                $mo_idp_saml_login_url,
                $mo_idp_saml_login_binding,
                $mo_idp_saml_logout_url,
                $mo_idp_saml_logout_binding,
                $mo_idp_x509_certificate,
                $mo_idp_response_signed,
                $mo_idp_assertion_signed,
                $mo_idp_show_admin_link,
                $mo_idp_show_customer_link,
                $mo_idp_auto_create_admin_users,
                $mo_idp_auto_create_customers,
                $mo_idp_disable_b2c,
                $mo_idp_force_authentication_with_idp,
                $mo_idp_auto_redirect_to_idp,
                $mo_idp_link_to_initiate_sso,
                $mo_idp_update_attributes_on_login,
                $mo_idp_create_magento_account_by,
                $mo_idp_email_attribute,
                $mo_idp_username_attribute,
                $mo_idp_firstname_attribute,
                $mo_idp_lastname_attribute,
                $mo_idp_group_attribute,
                $mo_idp_billing_city_attribute,
                $mo_idp_billing_state_attribute,
                $mo_idp_billing_country_attribute,
                $mo_idp_billing_address_attribute,
                $mo_idp_billing_phone_attribute,
                $mo_idp_billing_zip_attribute,
                $mo_idp_shipping_city_attribute,
                $mo_idp_shipping_state_attribute,
                $mo_idp_shipping_country_attribute,
                $mo_idp_shipping_address_attribute,
                $mo_idp_shipping_phone_attribute,
                $mo_idp_shipping_zip_attribute,
                $mo_idp_b2b_attribute,
                $mo_idp_custom_tablename,
                $mo_idp_custom_attributes,
                $mo_idp_do_not_autocreate_if_roles_not_mapped,
                $mo_idp_update_backend_roles_on_sso,
                $mo_idp_update_frontend_groups_on_sso,
                $mo_idp_default_group,
                $mo_idp_default_role,
                $mo_idp_groups_mapped,
                $mo_idp_roles_mapped,
                $mo_saml_logout_redirect_url,
                $billinandshippingchcekbox,
                $sameasbilling,
                $mo_saml_headless_sso,
                $mo_saml_frontend_post_url);
        $this->setStoreConfig(SPConstants::IDP_NAME, $mo_idp_app_name);
        $this->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $mo_idp_app_name);
        $this->reinitConfig();
    }

//change name result to redirectURL()

    public function checkIdpLimit()
    {
        $count = $this->getcounttable();
        if ($this->check_license_plan(4)) {
            $idpLimit = 2;
        } elseif ($this->check_license_plan(3)) {
            $idpLimit = $this->getWebsiteLimit();
        } else {
            $idpLimit = 1;
        }
        if (!($count < $idpLimit)) {
            $this->messageManager->addErrorMessage(__("To configure more Identity Providers (IDPs), please upgrade to the Multi-IDP plan."));
            $this->responseFactory->create()->setRedirect($this->getAdminUrl('mospsaml/idps/index'))->sendResponse();
            exit;
        }
    }

    public function getWebsiteLimit()
    {
        return AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::WEBSITES_LIMIT), SPConstants::DEFAULT_TOKEN_VALUE);
    }

    public function reinitConfig()
    {
        $this->reinitableConfig->reinit();
    }

    //CUSTOM LOG FILE OPERATION

    public function isAllPageAutoRedirectEnabled($default_provider)
    {
        $auto_redirect_app = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($auto_redirect_app == $default_provider) {
            return $this->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        } else {
            return 0;
        }

    }

    public function isAutoRedirectEnabled($default_provider)
    {
        $auto_redirect_app = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($auto_redirect_app == $default_provider) {
            return $this->getStoreConfig(SPConstants::AUTO_REDIRECT);
        } else {
            return 0;
        }

    }

    public function result($url)
    {

        $this->responseFactory->create()->setRedirect($url)->sendResponse();
        exit;
    }

    public function defaultlog($txt)
    {
        $this->logger->debug($txt);
    }

    /**
     * This function check whether any custom log file exist or not.
     */
    public function isCustomLogExist()
    {
        if ($this->fileSystem->isExists("../var/log/mo_saml.log")) {
            return 1;
        } elseif ($this->fileSystem->isExists("var/log/mo_saml.log")) {
            return 1;
        }
        return 0;
    }

    public function deleteCustomLogFile()
    {
        if ($this->fileSystem->isExists("../var/log/mo_saml.log")) {
            $this->fileSystem->deleteFile("../var/log/mo_saml.log");
        } elseif ($this->fileSystem->isExists("var/log/mo_saml.log")) {
            $this->fileSystem->deleteFile("var/log/mo_saml.log");
        }
    }

    public function update_status($code)
    {

        $customerKey = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $apiKey = $this->getStoreConfig(SPConstants::API_KEY);
        $content = Curl::update_status($customerKey, $apiKey, $code, $this->getBaseUrl());

        return $content;
    }

    public function errorPageRedirection($errorMessage)
    {

        // Add an error message using the message manager
        $this->messageManager->addErrorMessage($errorMessage);

        // Create the result redirect
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->urlInterface->getUrl('noroute'));

        return $resultRedirect;
    }

    public function redirectURL($url)
    {
        return $this->_response->setRedirect($url)->sendResponse();
    }

    //This function checks license expiry date and selects appropriate notifications to be sent.

    public function check_license_expiry_date()
    {
        $key = $this->getStoreConfig(SPConstants::TOKEN);
        $date = $this->getStoreConfig(SPConstants::LICENSE_EXPIRY_DATE);
        $expiry = null;

        if ($date == null) {
            $content = json_decode(self::ccl(), true);
            $expiry = array_key_exists('licenseExpiry', $content) ? (strtotime($content['licenseExpiry']) === false ? null : strtotime($content['licenseExpiry'])) : null;
            if (!$this->isBlank($expiry)) {
                $this->setStoreConfig(SPConstants::LICENSE_EXPIRY_DATE, AESEncryption::encrypt_data($expiry, $key));
            }
        } else {
            $expiry = AESEncryption::decrypt_data($date, $key);
        }
        $expiryDate = new \DateTime("@$expiry");
        $now = new \DateTime();
        $daysTillExpiry = $now->diff($expiryDate)->format("%r%a");
        if ($daysTillExpiry <= 30) {
            $this->flushCache();
            $licenseAlert = $this->getStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT);
            if ($daysTillExpiry > 7) {
                if ($licenseAlert == null) {
                    $this->license_expiry_notification_before_expiry($daysTillExpiry);
                    $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, $daysTillExpiry);
                }
            } elseif ($daysTillExpiry <= 7 && $daysTillExpiry > 0) {
                if ($licenseAlert == null || $licenseAlert > 7) {
                    $this->license_expiry_notification_before_expiry($daysTillExpiry);
                    $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, $daysTillExpiry);
                }
            } elseif ($daysTillExpiry <= -5) {
                if ($licenseAlert == null || $licenseAlert >= 0) {
                    $this->license_expiry_notification_after_expiry();
                    $this->setStoreConfig(SPConstants::SAMLSP_LICENSE_ALERT_SENT, 0);
                }
            }
        }
        return $daysTillExpiry;
    }

    /**
     * This function is used to get the user license associated
     * with IDP plugin from the server by calling the ccl cURL
     * function.
     *
     * @return JSONEncoded response
     */
    public function ccl()
    {
        $customerKey = $this->getStoreConfig(SPConstants::SAMLSP_KEY);
        $apiKey = $this->getStoreConfig(SPConstants::API_KEY);
        $content = Curl::ccl($customerKey, $apiKey);
        return $content;
    }

    /**
     * Flush Magento Cache. This has been added to make
     * sure the admin/user has a smooth experience and
     * doesn't have to flush his cache over and over again
     * to see his changes.
     */
    public function flushCache()
    {
        $types = array('db_ddl'); // we just need to clear the database cache
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    /**
     * This function is used to send an email notification to the user
     * letting him know that their one year license is going to over within @param $days days and they need
     * to renew their license. Calls the notify cURL call to send notifications
     */
    public function license_expiry_notification_before_expiry($days)
    {
        $customerKey = SPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey = SPConstants::DEFAULT_API_KEY;
        $toEmail = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $content = "Hello,<br><br>This email is to notify you that your 1 year license for Magento SAML SSO Plugin will expire
                          within " . $days . " days.<br><br>
                          <br><br>Contact us at <a href='mailto:magentosupport@xecurify.com'>magentosupport@xecurify.com</a> in order to renew your license.<br><br>Thanks,<br>miniOrange";
        $subject = 'License Expiry - Magento SAML SSO Plugin';
        CURL::notify($customerKey, $apiKey, $toEmail, $content, $subject);
    }

    /**
     * This function is used to send an email notification to the user
     * letting him know that their one year license is over and the plugin
     * has been deactivated on their site because they didn't renew within
     * the 5 days grace period.
     */
    public function license_expiry_notification_after_expiry()
    {
        $customerKey = SPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey = SPConstants::DEFAULT_API_KEY;
        $toEmail = $this->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $content = "Hello,<br><br>Your 1 year license for Magento SAML SSO Plugin has expired.<br><br>
						Contact us at <a href='mailto:magentosupport@xecurify.com'>magentosupport@xecurify.com</a> in order to renew your license.<br><br>Thanks,<br>miniOrange";
        $subject = 'License Expired - Magento SAML SSO Plugin ';
        CURL::notify($customerKey, $apiKey, $toEmail, $content, $subject);
    }

    //Function to check Website limit for the current plan

    /** Get the Magento Version */
    public function getMagnetoVersion()
    {
        return $this->productMetadata->getVersion();
    }

    //Function to check number of IDPs limit

    public function update_customer_id_in_customer_visitor($customerid)
    {

        $this->log_debug("Updating customer_visitor table");

        $connection = $this->resource->getConnection();
        // Perform a select count query
        $select = $connection->select()->from('customer_visitor', 'COUNT(*)');

        // Execute the query and fetch the count
        $rowCount = $connection->fetchOne($select);

        $this->resource->getConnection()->update(
            'customer_visitor', ['customer_id' => $customerid],
            ["visitor_id = ?" => $rowCount]
        );

    }

    //Function to check if the RelayState URL matches any Magento Site

    public function checkIfRelayStateIsMatchingAnySite($relayState)
    {
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            // Get all groups within the website
            $storeGroups = $website->getGroups();
            foreach ($storeGroups as $storeGroup) {
                // Get all stores within the store group
                $stores = $storeGroup->getStores();

                foreach ($stores as $store) {
                    // Do something with the store information
                    $this->log_debug("Storecode: " . $store->getCode());
                    $this->log_debug("StoreId: " . $store->getId());
                    $this->log_debug("WebsiteId: " . $store->getWebsiteId());
                    $this->log_debug("WebsiteName: " . $store->getWebsite()->getName());
                    $this->log_debug("-------------------------------------------------------");

                    if ($store->getWebsiteId() == $relayState) {
                        $this->log_debug("Website Id matched with relayState - " . $store->getWebsiteId());
                        return $store->getBaseUrl();
                    }
                }
            }
        }
        return false;
    }

    //Function to fetch current store
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }

    //Function to fetch Current Website Id
    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

}
