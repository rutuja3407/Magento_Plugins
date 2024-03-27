<?php

namespace MiniOrange\SP\Controller\Adminhtml\Rolesettings;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;

/**
 * This class handles the action for endpoint: mospsaml/attrsettings/Index
 * Extends the \Magento\Backend\App\Action for Admin Actions which
 * inturn extends the \Magento\Framework\App\Action\Action class necessary
 * for each Controller class
 */
class Index extends BaseAdminAction
{

    protected $sp;
    private $adminRoleModel;
    private $userGroupModel;
    private $attributeModel;
    private $samlResponse;
    private $params;
    private $adminUserModel;

    public function __construct(
        Context                                                    $context,
        PageFactory                                                $resultPageFactory,
        SPUtility                                                  $spUtility,
        ManagerInterface                                           $messageManager,
        LoggerInterface                                            $logger,
        \Magento\Authorization\Model\ResourceModel\Role\Collection $adminRoleModel,
        \Magento\Customer\Model\ResourceModel\Attribute\Collection $attributeModel,
        \Magento\Customer\Model\ResourceModel\Group\Collection     $userGroupModel,
        Sp                                                         $sp)
    {
        //You can use dependency injection to get any class this observer may need.
        parent::__construct($context, $resultPageFactory, $spUtility, $messageManager, $logger, $sp);
        $this->adminRoleModel = $adminRoleModel;
        $this->userGroupModel = $userGroupModel;
        $this->attributeModel = $attributeModel;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->sp = $sp;
    }

    /**
     * The first function to be called when a Controller class is invoked.
     * Usually, has all our controller logic. Returns a view/page/template
     * to be shown to the users.
     *
     * This function gets and prepares all our SP config data from the
     * database. It's called when you visis the moasaml/attrsettings/Index
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
            if ($this->isFormOptionBeingSaved($params)) // check if form options are being saved
            {

                $this->processValuesAndSaveData($params);
                $this->spUtility->flushCache();
                $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
                $this->spUtility->reinitConfig();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->spUtility->log_debug($e->getMessage());
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $resultPage->addBreadcrumb(__('ROLE Settings'), __('ROLE Settings'));
        $resultPage->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $resultPage;
    }


    /**
     * Process Values being submitted and save data in the database.
     * @param $param
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
            $mo_idp_do_not_autocreate_if_roles_not_mapped = !empty($params['mo_saml_dont_create_user_if_role_not_mapped']) ? "checked" : "unchecked";
            $mo_idp_update_backend_roles_on_sso = !empty($params['saml_am_update_roles']) ? "checked" : "unchecked";
            $mo_idp_update_frontend_groups_on_sso = !empty($params['saml_am_update_frontend_roles']) ? "checked" : "unchecked";
            $mo_idp_default_group = !empty($params['saml_am_default_group']) ? trim($params['saml_am_default_group']) : '';
            $mo_idp_default_role = !empty($params['saml_am_default_role']) ? trim($params['saml_am_default_role']) : '';
            $mo_idp_groups_mapped = json_encode($this->processCustomerRoleMapping($params));
            $mo_idp_roles_mapped = json_encode($this->processAdminRoleMapping($params));
            $mo_saml_logout_redirect_url = !empty($idpDetails['saml_logout_redirect_url']) ? $idpDetails['saml_logout_redirect_url'] : '';
            $billinandshippingchcekbox = !empty($idpDetails['saml_enable_billingandshipping']) ? $idpDetails['saml_enable_billingandshipping'] : 'none';
            $sameasbilling = !empty($idpDetails['saml_sameasbilling']) ? $idpDetails['saml_sameasbilling'] : 'none';
            $mo_saml_headless_sso = !empty($idpDetails['mo_saml_headless_sso']) && $idpDetails['mo_saml_headless_sso'] == true ? 1 : 0;
            $mo_saml_frontend_post_url = !empty($idpDetails['mo_saml_frontend_post_url']) ? $idpDetails['mo_saml_frontend_post_url'] : '';

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

            $saml_am_default_role = trim($params['saml_am_default_role']);
            $saml_am_default_group = trim($params['saml_am_default_group']);


            /* ===================================================================================================
                                THE LINES OF CODE BELOW ARE PREMIUM PLUGIN SPECIFIC
               ===================================================================================================
            */

            $saml_am_dont_allow_unlisted_user_role
                = !empty($params['saml_am_dont_allow_unlisted_user_role']) ? "checked" : "unChecked";
            $mo_saml_dont_create_user_if_role_not_mapped
                = !empty($params['mo_saml_dont_create_user_if_role_not_mapped']) ? "checked" : "unchecked";
            $admin_role_mapping = $this->processAdminRoleMapping($params);
            $customer_role_mapping = $this->processCustomerRoleMapping($params);

            $saml_am_update_roles
                = !empty($params['saml_am_update_roles']) ? "checked" : "unchecked";
            $saml_am_update_frontend_roles
                = !empty($params['saml_am_update_frontend_roles']) ? "checked" : "unchecked";


        }

    }

    /**
     * Read and process the Groups saved by the
     * admin.
     * @param $params
     * @return array
     */
    private function processCustomerRoleMapping($params)
    {
        $customer_role_mapping = array();
        $groups = $this->userGroupModel->toOptionArray();
        foreach ($groups as $group) {
            $attr = 'saml_am_group_attr_values_' . $group['value'];
            if (!empty($params[$attr]))
                $customer_role_mapping[$group['value']] = $params[$attr];
        }
        return $customer_role_mapping;
    }

    /**
     * Read and process the Roles saved by the
     * admin.
     * @param $params
     * @return array
     */
    private function processAdminRoleMapping($params)
    {
        $admin_role_mapping = array();
        $roles = $this->adminRoleModel->toOptionArray();
        foreach ($roles as $role) {
            $attr = 'saml_am_admin_attr_values_' . $role['value'];
            if (!empty($params[$attr]))
                $admin_role_mapping[$role['value']] = $params[$attr];
        }
        return $admin_role_mapping;
    }


}
