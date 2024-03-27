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

/**
 * This class contains functions to get and set the required data
 * from Magento database or session table/file or generate some
 * necessary values to be used in our module.
 */
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

    public function __construct(
        ScopeConfigInterface         $scopeConfig,
        UserFactory                  $adminFactory,
        CustomerFactory              $customerFactory,
        UrlInterface                 $urlInterface,
        WriterInterface              $configWriter,
        Repository                   $assetRepo,
        \Magento\Backend\Helper\Data $helperBackend,
        Url                          $frontendUrl,
        DateTime                     $dateTime,
        ProductMetadataInterface     $productMetadata,
        MiniorangeSamlIDPsFactory    $miniorangeSamlIDPsFactory)
    {
        $this->scopeConfig = $scopeConfig;
        $this->adminFactory = $adminFactory;
        $this->customerFactory = $customerFactory;
        $this->urlInterface = $urlInterface;
        $this->configWriter = $configWriter;
        $this->assetRepo = $assetRepo;
        $this->helperBackend = $helperBackend;
        $this->frontendUrl = $frontendUrl;
        $this->dateTime = $dateTime;
        $this->miniorangeSamlIDPsFactory = $miniorangeSamlIDPsFactory;

    }


    /**
     * Get base url of miniorange
     */
    public function getMiniOrangeUrl()
    {
        return SPConstants::HOSTNAME;
    }

    public function getAcsUrl()
    {
        $url = "mospsaml/actions/spObserver";
        return $this->getBaseUrlWithoutStoreCode() . $url;
    }

    /**
     * Function to get the sites Base URL.
     */
    public function getBaseUrl()
    {
        return $this->urlInterface->getBaseUrl();
    }

    public function getssourl()
    {
        $url = "mospsaml/actions/Logout";
        return $this->getBaseUrlWithoutStoreCode() . $url;
    }

    /**
     * This function is used to save user attributes to the
     * database and save it. Mostly used in the SSO flow to
     * update user attributes. Decides which user to update.
     *
     * @param $url
     * @param $value
     * @param $id
     * @param $admin
     * @throws \Exception
     */
    public function saveConfig($url, $value, $id, $admin)
    {
        $admin ? $this->saveAdminStoreConfig($url, $value, $id) : $this->saveCustomerStoreConfig($url, $value, $id);
    }

    /**
     * This function is used to save admin attributes to the
     * database and save it. Mostly used in the SSO flow to
     * update user attributes.
     *
     * @param $url
     * @param $value
     * @param $id
     * @throws \Exception
     */
    private function saveAdminStoreConfig($url, $value, $id)
    {
        $data = array($url => $value);
        $model = $this->adminFactory->create()->load($id)->addData($data);
        $model->setId($id)->save();
    }

    /**
     * This function is used to save customer attributes to the
     * database and save it. Mostly used in the SSO flow to
     * update user attributes.
     *
     * @param $url
     * @param $value
     * @param $id
     * @throws \Exception
     */
    private function saveCustomerStoreConfig($url, $value, $id)
    {
        $data = array($url => $value);
        $model = $this->customerFactory->create()->load($id)->addData($data);
        $model->setId($id)->save();
    }

    /**
     * Function to extract information stored in the admin user table.
     *
     * @param $config
     * @param $id
     * @return mixed
     */
    public function getAdminStoreConfig($config, $id)
    {
        return $this->adminFactory->create()->load($id)->getData($config);


    }

    /**
     * Function to extract information stored in the customer user table.
     *
     * @param $config
     * @param $id
     */
    public function getCustomerStoreConfig($config, $id)
    {
        return $this->customerFactory->create()->load($id)->getData($config);
    }

    /**
     * Function to get the sites Issuer URL.
     */
    public function getIssuerUrl()
    {
        return $this->getBaseUrlWithoutStoreCode(). SPConstants::SUFFIX_ISSUER_URL_PATH;
    }

     /**
     * Function to get secure base url without store code.
     */
    public function getBaseUrlWithoutStoreCode(){
        return $this->scopeConfig->getValue("web/secure/base_url");
    }

    /**
     * Function to get the Image URL of our module.
     *
     * @param $image
     */
    public function getImageUrl($image)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_IMAGES . $image);
    }

    /**
     * Function to get the url based on where the user is.
     *
     * @param $url
     */
    public function getUrl($url, $params = array())
    {
        return $this->urlInterface->getUrl($url, array('_query' => $params));
    }

    /**
     * Get Admin CSS URL
     */
    public function getAdminCssUrl($css)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_CSS . $css, array('area' => 'adminhtml'));
    }

    /**
     * Get Admin JS URL
     */
    public function getAdminJSUrl($js)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_JS . $js, array('area' => 'adminhtml'));
    }

    /**
     * Get Admin Metadata File Path
     */
    public function getMetadataFilePath()
    {
        return $this->assetRepo->createAsset(SPConstants::MODULE_DIR . SPConstants::MODULE_METADATA, array('area' => 'adminhtml'))
            ->getSourceFile();
    }

    /**
     * Function to get the resource as a path instead of the URL.
     *
     * @param $key
     */
    public function getResourcePath($key)
    {
        return $this->assetRepo
            ->createAsset(SPConstants::MODULE_DIR . SPConstants::MODULE_CERTS . $key, array('area' => 'adminhtml'))
            ->getSourceFile();
    }

    /**
     * Get admin Base url for the site.
     */
    public function getAdminBaseUrl()
    {
        return $this->helperBackend->getHomePageUrl();
    }

    /**
     * Get the Admin url for the site based on the path passed,
     * Append the query parameters to the URL if necessary.
     *
     * @param $url
     * @param $params
     */
    public function getAdminUrl($url, $params = array())
    {
        return $this->helperBackend->getUrl($url, array('_query' => $params));
    }

    /**
     * Get the SP InitiatedURL
     *
     * @param $relayState
     */
    public function getSPInitiatedUrl($relayState = NULL, $idp_name = NULL)
    {
        $relayState = is_null($relayState) ? $this->getCurrentUrl() : $relayState;
        return $this->getFrontendUrl(SPConstants::SUFFIX_SAML_LOGIN_URL,
                array("relayState" => $relayState)) . "&idp_name=" . $idp_name;
    }

    /**
     * Function get the current url the user is on.
     */
    public function getCurrentUrl()
    {
        return $this->urlInterface->getCurrentUrl();
    }

    /**
     * Function to get the sites frontend url.
     *
     * @param $url
     */
    public function getFrontendUrl($url, $params = array())
    {
        return $this->frontendUrl->getUrl($url, array('_query' => $params));
    }

    /**
     * Get Admin Cert Download URL
     */
    public function getAdminCertResourceUrl($key)
    {
        return $this->assetRepo->getUrl(SPConstants::MODULE_DIR . SPConstants::MODULE_CERTS . $key, array('area' => 'adminhtml'));
    }

    public function isAllPageAutoRedirectEnabled($default_provider)
    {
        $auto_redirect_app = $this->getStoreConfig(SPConstants::AUTO_REDIRECT_APP);
        if ($auto_redirect_app == $default_provider) {
            return $this->getStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT);
        } else {
            return 0;
        }

    }

    /*===========================================================================================
						THESE ARE PREMIUM PLUGIN SPECIFIC FUNCTIONS
    =============================================================================================*/

    /**
     * Function to extract data stored in the store config table.
     *
     * @param $config
     */
    public function getStoreConfig($config)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('miniorange/samlsp/' . $config, $storeScope);
    }

    public function setIDPApps($mo_idp_app_name,
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
                               $mo_saml_frontend_post_url)
    {
        $model = $this->miniorangeSamlIDPsFactory->create();
        $model->addData([
            "idp_name" => $mo_idp_app_name,
            "idp_entity_id" => $mo_idp_entity_id,
            "saml_login_url" => $mo_idp_saml_login_url,
            "saml_login_binding" => $mo_idp_saml_login_binding,
            "saml_logout_url" => $mo_idp_saml_logout_url,
            "saml_logout_binding" => $mo_idp_saml_logout_binding,
            "x509_certificate" => $mo_idp_x509_certificate,
            "response_signed" => $mo_idp_response_signed,
            "assertion_signed" => $mo_idp_assertion_signed,
            "show_admin_link" => $mo_idp_show_admin_link,
            "show_customer_link" => $mo_idp_show_customer_link,
            "auto_create_admin_users" => $mo_idp_auto_create_admin_users,
            "auto_create_customers" => $mo_idp_auto_create_customers,
            "disable_b2c" => $mo_idp_disable_b2c,
            "force_authentication_with_idp" => $mo_idp_force_authentication_with_idp,
            "auto_redirect_to_idp" => $mo_idp_auto_redirect_to_idp,
            "link_to_initiate_sso" => $mo_idp_link_to_initiate_sso,
            "update_attributes_on_login" => $mo_idp_update_attributes_on_login,
            "create_magento_account_by" => $mo_idp_create_magento_account_by,
            "email_attribute" => $mo_idp_email_attribute,
            "username_attribute" => $mo_idp_username_attribute,
            "firstname_attribute" => $mo_idp_firstname_attribute,
            "lastname_attribute" => $mo_idp_lastname_attribute,
            "group_attribute" => $mo_idp_group_attribute,
            "billing_city_attribute" => $mo_idp_billing_city_attribute,
            "billing_state_attribute" => $mo_idp_billing_state_attribute,
            "billing_country_attribute" => $mo_idp_billing_country_attribute,
            "billing_address_attribute" => $mo_idp_billing_address_attribute,
            "billing_phone_attribute" => $mo_idp_billing_phone_attribute,
            "billing_zip_attribute" => $mo_idp_billing_zip_attribute,
            "shipping_city_attribute" => $mo_idp_shipping_city_attribute,
            "shipping_state_attribute" => $mo_idp_shipping_state_attribute,
            "shipping_country_attribute" => $mo_idp_shipping_country_attribute,
            "shipping_address_attribute" => $mo_idp_shipping_address_attribute,
            "shipping_phone_attribute" => $mo_idp_shipping_phone_attribute,
            "shipping_zip_attribute" => $mo_idp_shipping_zip_attribute,
            "b2b_attribute" => $mo_idp_b2b_attribute,
            "custom_tablename" => $mo_idp_custom_tablename,
            "custom_attributes" => $mo_idp_custom_attributes,
            "do_not_autocreate_if_roles_not_mapped" => $mo_idp_do_not_autocreate_if_roles_not_mapped,
            "update_backend_roles_on_sso" => $mo_idp_update_backend_roles_on_sso,
            "update_frontend_groups_on_sso" => $mo_idp_update_frontend_groups_on_sso,
            "default_group" => $mo_idp_default_group,
            "default_role" => $mo_idp_default_role,
            "groups_mapped" => $mo_idp_groups_mapped,
            "roles_mapped" => $mo_idp_roles_mapped,
            "saml_logout_redirect_url" => $mo_saml_logout_redirect_url,
            "saml_enable_billingandshipping" => $billinandshippingchcekbox,
            "saml_sameasbilling" => $sameasbilling,
            "mo_saml_headless_sso" => $mo_saml_headless_sso,
            "mo_saml_frontend_post_url" => $mo_saml_frontend_post_url
        ]);
        $model->save();
    }

    public function getcounttable()
    {
        $collection = $this->getIDPApps();
        $count = 0;
        foreach ($collection as $item) {
            $idpDetails = $item->getData();
            $count++;
        }
        return $count;
    }

    /**
     * Get All the entry from the miniorange_saml_idps Table
     */
    public function getIDPApps()
    {
        $model = $this->miniorangeSamlIDPsFactory->create();
        $collection = $model->getCollection();
        return $collection;
    }

    /**
     * Delete the entry in the miniorange_saml_idps Table
     *
     * @param $id
     */
    public function deleteIDPApps($id)
    {
        $model = $this->miniorangeSamlIDPsFactory->create();
        $model->load($id);
        $model->delete();
    }

    /**
     * Check if the trial has expired or not.
     */

    public function isTrialExpired()
    {
        if ($this->check_license_plan(4)) {

            $executionDate = AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::INSTALLATION_DATE), SPConstants::DEFAULT_TOKEN_VALUE);

            if (!$executionDate) {
                // Set the current date if the execution date is not already set
                $currentDate = $this->dateTime->gmtDate('Y-m-d H:i:s');
                $this->setStoreConfig(SPConstants::INSTALLATION_DATE, AESEncryption::encrypt_data($currentDate, SPConstants::DEFAULT_TOKEN_VALUE));
            } else {
                // Check if the execution date exceeds the maximum allowed days
                $expirationDate = date('Y-m-d H:i:s', strtotime($executionDate . ' + ' . 7 . ' days'));
                $currentDate = $this->dateTime->gmtDate('Y-m-d H:i:s');

                if ($currentDate > $expirationDate) {
                    $userEmail = null;
                    $firstName = null;
                    if ($this->getCurrentAdminUser()) {

                        $currentAdminUser = $this->getCurrentAdminUser()->getData();
                        $userEmail = $currentAdminUser['email'];
                        $firstName = $currentAdminUser['firstname'];

                    }
                    $site = $this->getBaseUrl();
                    $magentoVersion = $this->getMagnetoVersion();
                    $values = array($firstName, $magentoVersion, $site);

                    $send_email = $this->getStoreConfig(SPConstants::SEND_EXPIRED_EMAIL);
                    if ($send_email == null) {
                        $this->setStoreConfig(SPConstants::SEND_EXPIRED_EMAIL, 1);
                        Curl::submit_to_magento_team($userEmail, "TRIAL EXPIRED", $values);
                        $this->flushCache();
                    }

                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * Function to store data stored in the store config table.
     *
     * @param $config
     * @param $value
     */
    public function setStoreConfig($config, $value)
    {
        $this->configWriter->save('miniorange/samlsp/' . $config, $value);
    }

    //Function to extend trial

    public function extendTrial()
    {
        if ($this->check_license_plan(4)) {
            $currentAdminUser = $this->getCurrentAdminUser()->getData();
            $userEmail = $currentAdminUser['email'];
            $firstName = $currentAdminUser['firstname'];
            $site = $this->getBaseUrl();
            $magentoVersion = $this->getMagnetoVersion();
            $alreadyTrialExtended = $this->getStoreConfig(SPConstants::IS_TRIAL_EXTENDED);
            if (!$alreadyTrialExtended) {
                $this->setStoreConfig(SPConstants::IS_TRIAL_EXTENDED, true);
                $currentDate = $this->dateTime->gmtDate('Y-m-d H:i:s');
                $this->setStoreConfig(SPConstants::INSTALLATION_DATE, AESEncryption::encrypt_data($currentDate, SPConstants::DEFAULT_TOKEN_VALUE));
                $values = array($firstName, $magentoVersion, $site);
                Curl::submit_to_magento_team($userEmail, "TRIAL EXTENDED FOR 7 DAYS", $values);
                $this->messageManager->addSuccessMessage(SPMessages::TRIAL_EXTENDED);
            } else {
                $values = array($firstName, $magentoVersion, $site);
                Curl::submit_to_magento_team($userEmail, "TRIAL EXTEND REQUEST FAILED (ALREADY EXTENDED)", $values);
                $this->messageManager->addErrorMessage(SPMessages::EXTEND_TRIAL_LIMIT_REACHED);
            }
        }
    }

    //Function to get remaining user count
    public function getRemainingUsersCount()
    {

        if (is_null($this->getStoreConfig(SPConstants::MAGENTO_COUNTER))) {
            $this->setStoreConfig(SPConstants::MAGENTO_COUNTER, AESEncryption::encrypt_data(0, SPConstants::DEFAULT_TOKEN_VALUE));
        }

        $donotCreateUsers = (int)AESEncryption::decrypt_data($this->getStoreConfig(SPConstants::MAGENTO_COUNTER), SPConstants::DEFAULT_TOKEN_VALUE);

        $maxLimit = (int)AESEncryption::decrypt_data("ZHo=", SPConstants::DEFAULT_TOKEN_VALUE);

        if ($donotCreateUsers > $maxLimit) {

            $site = $this->getBaseUrl();
            $magentoVersion = $this->getMagnetoVersion();
            $values = array($magentoVersion, $site);
            Curl::submit_to_magento_team('', "User Limit exceeded", $values);
        }

        return $donotCreateUsers > $maxLimit;
    }
}
