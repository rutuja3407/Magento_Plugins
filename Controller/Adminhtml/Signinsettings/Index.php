<?php

namespace MiniOrange\SP\Controller\Adminhtml\Signinsettings;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;

/**
 * This class handles the action for endpoint: mospsaml/signinsettings/Index
 * Extends the \Magento\Backend\App\Action for Admin Actions which
 * inturn extends the \Magento\Framework\App\Action\Action class necessary
 * for each Controller class
 */
class Index extends BaseAdminAction
{
    protected $fileFactory;
    protected $_storeManager;
    private $userGroupModel;

    public function __construct(
        Context               $context,
        PageFactory           $resultPageFactory,
        SPUtility             $spUtility,
        ManagerInterface      $messageManager,
        LoggerInterface       $logger,
        Collection            $userGroupModel,
        FileFactory           $fileFactory,
        StoreManagerInterface $storeManager,
        Sp                    $sp
    )
    {
        //You can use dependency injection to get any class this observer may need.
        parent::__construct($context, $resultPageFactory, $spUtility, $messageManager, $logger, $sp);
        $this->_storeManager = $storeManager;
        $this->fileFactory = $fileFactory;
        $this->userGroupModel = $userGroupModel;
    }

    /**
     * The first function to be called when a Controller class is invoked.
     * Usually, has all our controller logic. Returns a view/page/template
     * to be shown to the users.
     *
     * This function gets and prepares all our SP config data from the
     * database. It's called when you visis the moasaml/signinsettings/Index
     * URL. It prepares all the values required on the SP setting
     * page in the backend and returns the block to be displayed.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($this->spUtility->check_license_plan(4)) {
            $send_email = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
            if ($send_email == NULL) {
                $currentAdminUser = $this->spUtility->getCurrentAdminUser()->getData();
                $magentoVersion = $this->spUtility->getMagnetoVersion();
                $userEmail = $currentAdminUser['email'];
                $firstName = $currentAdminUser['firstname'];
                $lastName = $currentAdminUser['lastname'];
                $site = $this->spUtility->getBaseUrl();
                $values = array($firstName, $lastName, $magentoVersion, $site);
                $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
                Curl::submit_to_magento_team($userEmail, 'Installed Successfully-Account Tab', $values);
                $this->spUtility->flushCache();
            }
        }

        try {
            $params = $this->getRequest()->getParams(); //get params
            $this->checkIfValidPlugin(); //check if user has registered himself
            // check if form options are being saved
            if ($this->isFormOptionBeingSaved($params)) {
                if (!empty($params['option']) && ($params['option'] == 'saveSingInSettings' || $params['option'] == 'saveProvider')) {
                    $this->processValuesAndSaveData($params);
                    $this->spUtility->flushCache();
                    $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
                    $this->spUtility->reinitConfig();
                } elseif (!empty($params['option']) && $params['option'] == 'enable_debug_log') {

                    $debug_log_on = !empty($params['debug_log_on']) ? 1 : 0;
                    $log_file_time = time();
                    $this->spUtility->setStoreConfig(SPConstants::ENABLE_DEBUG_LOG, $debug_log_on);
                    $this->spUtility->flushCache();
                    $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
                    $this->spUtility->reinitConfig();
                    if ($debug_log_on == '1') {
                        $this->spUtility->setStoreConfig(SPConstants::LOG_FILE_TIME, $log_file_time);
                    } elseif ($debug_log_on == '0' && $this->spUtility->isCustomLogExist()) {
                        $this->spUtility->setStoreConfig(SPConstants::LOG_FILE_TIME, NULL);
                        $this->spUtility->deleteCustomLogFile();
                    }
                } elseif ($params['option'] == 'clear_download_logs') {
                    if (!empty($params['download_logs'])) {
                        $fileName = "mo_saml.log"; // add your file name here
                        if ($fileName) {
                            $filePath = '../var/log/' . $fileName;
                            $content['type'] = 'filename';// type has to be "filename"
                            $content['value'] = $filePath; // path where file place
                            $content['rm'] = 0; // if you add 1 then it will be delete from server after being download, otherwise add 0.
                            if ($this->spUtility->isLogEnable()) {
                                $mo_idp_app_name = trim($params['mo_identity_provider']);
                                $collection = $this->spUtility->getidpApps();
                                $idpDetails = null;
                                foreach ($collection as $item) {
                                    if ($item->getData()["idp_name"] === $mo_idp_app_name) {
                                        $idpDetails = $item->getData();
                                    }
                                }
                                $this->customerConfigurationSettings($idpDetails, $mo_idp_app_name);
                            }

                            if (($this->spUtility->isCustomLogExist()) && $this->spUtility->isLogEnable()) {
                                return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
                            } else {
                                $this->messageManager->addErrorMessage('Please Enable Debug Log Setting First');

                            }
                        } else {
                            $this->messageManager->addErrorMessage('Something went wrong');

                        }
                    } elseif (!empty($params['clear_logs'])) {
                        if ($this->spUtility->isCustomLogExist()) {
                            $this->spUtility->setStoreConfig(SPConstants::LOG_FILE_TIME, NULL);
                            $this->spUtility->deleteCustomLogFile();
                            $this->messageManager->addSuccessMessage('Logs Cleared Successfully');
                        } else {
                            $this->messageManager->addSuccessMessage('Logs Have Already Been Removed');
                        }

                    }


                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->debug($e->getMessage());
        }
        // generate page
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $resultPage->addBreadcrumb(__('Sign In Settings'), __('Sign In Settings'));
        $resultPage->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $resultPage;
    }

    /**
     * Process Values being submitted and save data in the database.
     */
    private function processValuesAndSaveData($params)
    {
        if (!empty($params['option']) && $params['option'] == 'saveProvider') {
            $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $params['mo_identity_provider']);
        } else {
            $mo_idp_app_name = trim($params['mo_identity_provider']);
            $collection = $this->spUtility->getidpApps();
            $idpDetails = null;
            foreach ($collection as $item) {
                if ($item->getData()["idp_name"] === $mo_idp_app_name) {
                    $idpDetails = $item->getData();
                }
            }
            $mo_idp_entity_id = !empty($idpDetails['idp_entity_id']) ? $idpDetails['idp_entity_id'] : '';
            $mo_idp_saml_login_url = !empty($idpDetails['saml_login_url']) ? $idpDetails['saml_login_url'] : '';
            $mo_idp_saml_login_binding = !empty($idpDetails['saml_login_binding']) ? $idpDetails['saml_login_binding'] : '';
            $mo_idp_saml_logout_url = !empty($idpDetails['saml_logout_url']) ? $idpDetails['saml_logout_url'] : '';
            $mo_idp_saml_logout_binding = !empty($idpDetails['saml_logout_binding']) ? $idpDetails['saml_logout_binding'] : '';
            $mo_idp_x509_certificate = !empty($idpDetails['x509_certificate']) ? SAML2Utilities::sanitize_certificate($idpDetails['x509_certificate']) : '';
            $mo_idp_response_signed = !empty($idpDetails['response_signed']) ? $idpDetails['response_signed'] : 0;
            $mo_idp_assertion_signed = !empty($idpDetails['assertion_signed']) ? $idpDetails['assertion_signed'] : 0;
            $mo_idp_show_admin_link = !empty($params['mo_saml_show_admin_link']) && $params['mo_saml_show_admin_link'] == true ? 1 : 0;
            $mo_idp_show_customer_link = !empty($params['mo_saml_show_customer_link']) && $params['mo_saml_show_customer_link'] == true ? 1 : 0;
            $mo_idp_auto_create_admin_users = !empty($params['mo_saml_auto_create_admin']) && $params['mo_saml_auto_create_admin'] == true ? 1 : 0;
            $mo_idp_auto_create_customers = !empty($params['mo_saml_auto_create_customer']) && $params['mo_saml_auto_create_customer'] == true ? 1 : 0;
            $mo_idp_admin_autoredirect =  !empty($params['mo_saml_enable_admin_login_redirect']) ? $params['mo_saml_enable_admin_login_redirect'] : 0;
            $mo_idp_disable_b2c = !empty($params['mo_saml_disable_b2c']) && $params['mo_saml_disable_b2c'] == true ? 1 : 0;
            $mo_idp_force_authentication_with_idp = !empty($params['mo_saml_force_authentication']) && $params['mo_saml_force_authentication'] == true ? 1 : 0;
            $mo_idp_auto_redirect_to_idp = !empty($params['mo_saml_enable_login_redirect']) ? $params['mo_saml_enable_login_redirect'] : 0;
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
            $mo_idp_do_not_autocreate_if_roles_not_mapped = !empty($idpDetails['do_not_autocreate_if_roles_not_mapped']) ? $idpDetails['do_not_autocreate_if_roles_not_mapped'] : 'unchecked';
            $mo_idp_update_backend_roles_on_sso = !empty($idpDetails['update_backend_roles_on_sso']) ? $idpDetails['update_backend_roles_on_sso'] : 'unchecked';
            $mo_idp_update_frontend_groups_on_sso = !empty($idpDetails['update_frontend_groups_on_sso']) ? $idpDetails['update_frontend_groups_on_sso'] : 'unchecked';
            $mo_idp_default_group = !empty($idpDetails['default_group']) ? $idpDetails['default_group'] : '';
            $mo_idp_default_role = !empty($idpDetails['default_role']) ? $idpDetails['default_role'] : '';
            $mo_idp_groups_mapped = !empty($idpDetails['groups_mapped']) ? $idpDetails['groups_mapped'] : '';
            $mo_idp_roles_mapped = !empty($idpDetails['roles_mapped']) ? $idpDetails['roles_mapped'] : '';
            $mo_saml_logout_redirect_url = !empty($params['mo_saml_logout_redirect_url']) ? $params['mo_saml_logout_redirect_url'] : '';
            $billinandshippingchcekbox = !empty($idpDetails['saml_enable_billingandshipping']) ? $idpDetails['saml_enable_billingandshipping'] : 'none';
            $sameasbilling = !empty($idpDetails['saml_sameasbilling']) ? $idpDetails['saml_sameasbilling'] : 'none';
            $mo_saml_headless_sso = !empty($params['mo_saml_headless_sso']) && $params['mo_saml_headless_sso'] == true ? 1 : 0;
            $mo_saml_frontend_post_url = !empty($params['mo_saml_frontend_post_url']) ? $params['mo_saml_frontend_post_url'] : '';
            if (!is_null($idpDetails)) {
                $this->spUtility->deleteIDPApps((int)$idpDetails['id']);
            }

            $this->spUtility->setIDPApps(
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


            $mo_saml_enable_all_page_login_redirect = !empty($params['mo_saml_enable_all_page_login_redirect']) ? 1 : 0;
            $mo_saml_enable_login_redirect = !empty($params['mo_saml_enable_login_redirect']) ? 1 : 0;
            $change_setting_same_app = ($this->spUtility->getStoreConfig(SPConstants::AUTO_REDIRECT_APP) == $mo_idp_app_name) ? 1 : 0;

            if ($mo_saml_enable_login_redirect || $mo_saml_enable_login_redirect || $change_setting_same_app || $mo_idp_admin_autoredirect) {
                $this->spUtility->setStoreConfig(SPConstants::AUTO_REDIRECT_APP, $mo_idp_app_name);
                $this->spUtility->setStoreConfig(SPConstants::AUTO_REDIRECT, $mo_saml_enable_login_redirect);
                $this->spUtility->setStoreConfig(SPConstants::ADMIN_AUTO_REDIRECT, $mo_idp_admin_autoredirect);
                $this->spUtility->setStoreConfig(SPConstants::ALL_PAGE_AUTO_REDIRECT, $mo_saml_enable_all_page_login_redirect);

            }

        }

        $this->spUtility->reinitConfig();
    }

    private function customerConfigurationSettings($idpDetails, $mo_idp_app_name)
    {
        $this->spUtility->customlog("///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////");
        $this->spUtility->customlog("///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////");

        $magento_version = $this->spUtility->getMagnetoVersion();
        $php_version = phpversion();

        $mo_idp_entity_id = !empty($idpDetails['idp_entity_id']) ? $idpDetails['idp_entity_id'] : '';
        $mo_idp_saml_login_url = !empty($idpDetails['saml_login_url']) ? $idpDetails['saml_login_url'] : '';
        $mo_idp_saml_login_binding = !empty($idpDetails['saml_login_binding']) ? $idpDetails['saml_login_binding'] : '';
        $mo_idp_saml_logout_url = !empty($idpDetails['saml_logout_url']) ? $idpDetails['saml_logout_url'] : '';
        $mo_idp_saml_logout_binding = !empty($idpDetails['saml_logout_binding']) ? $idpDetails['saml_logout_binding'] : '';
        $mo_idp_x509_certificate = !empty($idpDetails['x509_certificate']) ? SAML2Utilities::sanitize_certificate($idpDetails['x509_certificate']) : '';
        $mo_idp_response_signed = !empty($idpDetails['response_signed']) ? $idpDetails['response_signed'] : 0;
        $mo_idp_assertion_signed = !empty($idpDetails['assertion_signed']) ? $idpDetails['assertion_signed'] : 0;
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
        $mo_idp_do_not_autocreate_if_roles_not_mapped = !empty($idpDetails['do_not_autocreate_if_roles_not_mapped']) ? $idpDetails['do_not_autocreate_if_roles_not_mapped'] : 'unchecked';
        $mo_idp_update_backend_roles_on_sso = !empty($idpDetails['update_backend_roles_on_sso']) ? $idpDetails['update_backend_roles_on_sso'] : 'unchecked';
        $mo_idp_update_frontend_groups_on_sso = !empty($idpDetails['update_frontend_groups_on_sso']) ? $idpDetails['update_frontend_groups_on_sso'] : 'unchecked';
        $mo_idp_default_group = !empty($idpDetails['default_group']) ? $idpDetails['default_group'] : '';
        $mo_idp_default_role = !empty($idpDetails['default_role']) ? $idpDetails['default_role'] : '';
        $mo_idp_groups_mapped = !empty($idpDetails['groups_mapped']) ? $idpDetails['groups_mapped'] : '';
        $mo_idp_roles_mapped = !empty($idpDetails['roles_mapped']) ? $idpDetails['roles_mapped'] : '';
        $mo_saml_logout_redirect_url = !empty($params['mo_saml_logout_redirect_url']) ? $params['mo_saml_logout_redirect_url'] : '';
        $mo_saml_headless_sso = !empty($idpDetails['mo_saml_headless_sso']) && $idpDetails['mo_saml_headless_sso'] == true ? 1 : 0;
        $mo_saml_frontend_post_url = !empty($idpDetails['mo_saml_frontend_post_url']) ? $idpDetails['mo_saml_frontend_post_url'] : '';


        $this->spUtility->customlog("Plugin: SAML Premium : " . SPConstants::VERSION);
        $this->spUtility->customlog("Plugin: Magento version : " . $magento_version . " ; Php version: " . $php_version);
        $this->spUtility->customlog("SAML SP Settings:.......................................................");
        $this->spUtility->customlog("Appname: " . $mo_idp_app_name);
        $this->spUtility->customlog("SAML_SSO_URL: " . $mo_idp_saml_login_url);
        $this->spUtility->customlog("SAML_SLO_URL: " . $mo_idp_saml_logout_url);
        $this->spUtility->customlog("Login Binding Type: " . $mo_idp_saml_login_binding);
        $this->spUtility->customlog("Logout Binding Type: " . $mo_idp_saml_logout_url);
        $this->spUtility->customlog("X.509 Certificate : " . $mo_idp_x509_certificate);
        $this->spUtility->customlog("IdP Entity ID or Issuer : " . $mo_idp_entity_id);
        $this->spUtility->customlog("Response Signed: " . $mo_idp_response_signed);
        $this->spUtility->customlog("Assertion Signed: " . $mo_idp_assertion_signed);
        $this->spUtility->customlog("Sign in Setting:.......................................................");
        $this->spUtility->customlog("Show_customer_link: " . $mo_idp_show_customer_link);
        $this->spUtility->customlog("Show Link for admin: " . $mo_idp_show_admin_link);
        $this->spUtility->customlog("AUTO_CREATE_ADMIN: " . $mo_idp_auto_create_admin_users);
        $this->spUtility->customlog("AUTO_CREATE_CUSTOMER: " . $mo_idp_auto_create_customers);
        $this->spUtility->customlog("Attribute Mapping:.......................................................");
        $this->spUtility->customlog("Update attribute: " . $mo_idp_custom_attributes);
        $this->spUtility->customlog("Attribute mapping Username: " . $mo_idp_username_attribute);
        $this->spUtility->customlog("Attribute mapping email: " . $mo_idp_email_attribute);
        $this->spUtility->customlog("Attribute mapping groups: " . $mo_idp_group_attribute);
        $this->spUtility->customlog("Role Mapping Mapping:.......................................................");
        $this->spUtility->customlog("Default Admin Role: " . $mo_idp_default_role);
        $this->spUtility->customlog("Default Customer Role: " . $mo_idp_default_group);
        $this->spUtility->customlog("Do not create user for role not mapped: " . $mo_idp_do_not_autocreate_if_roles_not_mapped);
        $this->spUtility->customlog("Enable update admin role: " . $mo_idp_update_backend_roles_on_sso);
        $this->spUtility->customlog("Enable update customer role: " . $mo_idp_update_frontend_groups_on_sso);
        $this->spUtility->customlog("///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////");
        $this->spUtility->customlog("///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////");

    }

    /**
     * Is the user allowed to view the Sign in Settings.
     * This is based on the ACL set by the admin in the backend.
     * Works in conjugation with acl.xml
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_SIGNIN);
    }
}
