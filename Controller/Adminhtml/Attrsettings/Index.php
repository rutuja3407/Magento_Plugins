<?php

namespace MiniOrange\SP\Controller\Adminhtml\Attrsettings;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
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
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    private $adminRoleModel;
    private $userGroupModel;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */

    public function __construct(
        Context                                                    $context,
        PageFactory                                                $resultPageFactory,
        SPUtility                                                  $spUtility,
        ManagerInterface                                           $messageManager,
        LoggerInterface                                            $logger,
        \Magento\Authorization\Model\ResourceModel\Role\Collection $adminRoleModel,
        \Magento\Customer\Model\ResourceModel\Group\Collection     $userGroupModel,
        Sp                                                         $sp,
        ModuleDataSetupInterface                                   $moduleDataSetup,
        CustomerSetupFactory                                       $customerSetupFactory,
        AttributeSetFactory                                        $attributeSetFactory)
    {
        //You can use dependency injection to get any class this observer may need.
        parent::__construct($context, $resultPageFactory, $spUtility, $messageManager, $logger, $sp);
        $this->adminRoleModel = $adminRoleModel;
        $this->sp = $sp;
        $this->userGroupModel = $userGroupModel;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
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
                if (!empty($params['option']) && $params['option'] != 'saveProvider') {
                    $this->checkIfRequiredFieldsEmpty(['saml_am_username' => $params, 'saml_am_account_matcher' => $params]);
                }
                $this->processValuesAndSaveData($params);
                $this->spUtility->flushCache();
                $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
                $this->spUtility->reinitConfig();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->debug($e->getMessage());
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $resultPage->addBreadcrumb(__('ATTR Settings'), __('ATTR Settings'));
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
            $defaultAttributes = array("form_key", "mo_identity_provider", "saml_am_sameasbilling", "saml_am_billingandshipping", "saml_am_update_attribute", "this_attribute", "saml_am_first_name", "saml_am_last_name", "saml_am_account_matcher", "saml_am_username", "saml_am_email", "saml_am_group_name", "saml_am_city", "saml_am_address", "0", "saml_am_company_id", "saml_am_city_billing", "saml_am_address_billing", "saml_am_phone_billing", "saml_am_state_billing", "saml_am_zipcode_billing", "saml_am_country_billing", "saml_am_city_shipping", "saml_am_address_shipping", "saml_am_phone_shipping", "saml_am_state_shipping", "saml_am_zipcode_shipping", "saml_am_country_shipping", "saml_am_update_attribue", "option", "saml_am_company", "saml_am_table", "key", "submit");
            $tempCustomAttrObject = json_encode($params, true);
            $tempCustomAttrObjectDecoded = json_decode($tempCustomAttrObject, true);
            $this->spUtility->log_debug("Default and Custom Attributes Array: ", $tempCustomAttrObjectDecoded);
            $tempCustom = $tempCustomAttrObjectDecoded;
            $this->spUtility->log_debug("let's unset default attr", $tempCustom);
            foreach ($defaultAttributes as $value) {
                unset($tempCustom[$value]);
            }
            $tempCustomAttrObjectEncoded = json_encode($tempCustom, true);
            $this->spUtility->log_debug("after unseting the value", $tempCustomAttrObjectEncoded);
            $this->spUtility->log_debug("save custom attributes");

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
            $mo_idp_update_attributes_on_login = !empty($params['saml_am_update_attribute']) ? "checked" : "unchecked";
            $mo_idp_create_magento_account_by = !empty($params['saml_am_account_matcher']) ? $params['saml_am_account_matcher'] : '';
            $mo_idp_email_attribute = !empty($params['saml_am_email']) ? trim($params['saml_am_email']) : '';
            $mo_idp_username_attribute = !empty($params['saml_am_username']) ? trim($params['saml_am_username']) : '';
            $mo_idp_firstname_attribute = !empty($params['saml_am_first_name']) ? trim($params['saml_am_first_name']) : '';
            $mo_idp_lastname_attribute = !empty($params['saml_am_last_name']) ? trim($params['saml_am_last_name']) : '';
            $mo_idp_group_attribute = !empty($params['saml_am_group_name']) ? trim($params['saml_am_group_name']) : '';
            $mo_idp_billing_city_attribute = !empty($params['saml_am_city_billing']) ? trim($params['saml_am_city_billing']) : '';
            $mo_idp_billing_state_attribute = !empty($params['saml_am_state_billing']) ? trim($params['saml_am_state_billing']) : '';
            $mo_idp_billing_country_attribute = !empty($params['saml_am_country_billing']) ? trim($params['saml_am_country_billing']) : '';
            $mo_idp_billing_address_attribute = !empty($params['saml_am_address_billing']) ? trim($params['saml_am_address_billing']) : '';
            $mo_idp_billing_phone_attribute = !empty($params['saml_am_phone_billing']) ? trim($params['saml_am_phone_billing']) : '';
            $mo_idp_billing_zip_attribute = !empty($params['saml_am_zipcode_billing']) ? trim($params['saml_am_zipcode_billing']) : '';
            $mo_idp_shipping_city_attribute = !empty($params['saml_am_city_shipping']) ? trim($params['saml_am_city_shipping']) : '';
            $mo_idp_shipping_state_attribute = !empty($params['saml_am_state_shipping']) ? trim($params['saml_am_state_shipping']) : '';
            $mo_idp_shipping_country_attribute = !empty($params['saml_am_country_shipping']) ? trim($params['saml_am_country_shipping']) : '';
            $mo_idp_shipping_address_attribute = !empty($params['saml_am_address_shipping']) ? trim($params['saml_am_address_shipping']) : '';
            $mo_idp_shipping_phone_attribute = !empty($params['saml_am_phone_shipping']) ? trim($params['saml_am_phone_shipping']) : '';
            $mo_idp_shipping_zip_attribute = !empty($params['saml_am_zipcode_shipping']) ? trim($params['saml_am_zipcode_shipping']) : '';
            $mo_idp_b2b_attribute = !empty($params['saml_am_company_id']) ? trim($params['saml_am_company_id']) : '';
            $mo_idp_custom_tablename = !empty($params['saml_am_table']) ? trim($params['saml_am_table']) : '';
            $mo_idp_custom_attributes = !empty($tempCustomAttrObjectEncoded) ? $tempCustomAttrObjectEncoded : '';
            $mo_idp_do_not_autocreate_if_roles_not_mapped = !empty($idpDetails['do_not_autocreate_if_roles_not_mapped']) ? $idpDetails['do_not_autocreate_if_roles_not_mapped'] : 'unchecked';
            $mo_idp_update_backend_roles_on_sso = !empty($idpDetails['update_backend_roles_on_sso']) ? $idpDetails['update_backend_roles_on_sso'] : 'unchecked';
            $mo_idp_update_frontend_groups_on_sso = !empty($idpDetails['update_frontend_groups_on_sso']) ? $idpDetails['update_frontend_groups_on_sso'] : 'unchecked';
            $mo_idp_default_group = !empty($idpDetails['default_group']) ? $idpDetails['default_group'] : '';
            $mo_idp_default_role = !empty($idpDetails['default_role']) ? $idpDetails['default_role'] : '';
            $mo_idp_groups_mapped = !empty($idpDetails['groups_mapped']) ? $idpDetails['groups_mapped'] : '';
            $mo_idp_roles_mapped = !empty($idpDetails['roles_mapped']) ? $idpDetails['roles_mapped'] : '';
            $mo_saml_logout_redirect_url = !empty($idpDetails['saml_logout_redirect_url']) ? $idpDetails['saml_logout_redirect_url'] : '';

            $billinandshippingchcekbox = !empty($params['saml_am_billingandshipping']) ? trim($params['saml_am_billingandshipping']) : 'none';
            $sameasbilling = !empty($params['saml_am_sameasbilling']) ? trim($params['saml_am_sameasbilling']) : 'none';
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
        }

    }


    /* ===================================================================================================
                           THE FUNCTION BELOW ARE PREMIUM PLUGIN SPECIFIC
       ===================================================================================================
   */

    public function remove($attributeCode)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, $attributeCode);

    }

    /**
     * Is the user allowed to view the Attribute Mapping settings.
     * This is based on the ACL set by the admin in the backend.
     * Works in conjugation with acl.xml
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_ATTR);
    }
}
