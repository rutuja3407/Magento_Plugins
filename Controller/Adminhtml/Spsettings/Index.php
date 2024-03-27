<?php

namespace MiniOrange\SP\Controller\Adminhtml\Spsettings;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
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
 * This class handles the action for endpoint: mospsaml/spsettings/Index
 * Extends the \Magento\Backend\App\Action for Admin Actions which
 * inturn extends the \Magento\Framework\App\Action\Action class necessary
 * for each Controller class
 */
class Index extends BaseAdminAction
{
    protected $sp;
    protected $responseFactory;
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    /**
     * The first function to be called when a Controller class is invoked.
     * Usually, has all our controller logic. Returns a view/page/template
     * to be shown to the users.
     *
     * This function gets and prepares all our SP config data from the
     * database. It's called when you visis the moasaml/spsettings/Index
     * URL. It prepares all the values required on the SP setting
     * page in the backend and returns the block to be displayed.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */

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
        AttributeSetFactory                                        $attributeSetFactory,
        ResponseFactory                                            $responseFactory
    )
    {
        //You can use dependency injection to get any class this observer may need.
        parent::__construct($context, $resultPageFactory, $spUtility, $messageManager, $logger, $sp);
        $this->adminRoleModel = $adminRoleModel;
        $this->sp = $sp;
        $this->userGroupModel = $userGroupModel;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->responseFactory = $responseFactory;

    }

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
            if (!empty($params['add'])) {
                $this->spUtility->checkIdpLimit();
            }
            if ($this->isFormOptionBeingSaved($params)) // check if form options are being saved
            {
                if (!$this->spUtility->check_license_plan(3) && !$this->spUtility->check_license_plan(4)) {
                    $collection = $this->spUtility->getIDPApps();
                    foreach ($collection as $item) {
                        if ($item->getData()["idp_name"] !== $params['saml_identity_name']) {
                            $idpDetails = $item->getData();
                            $this->idpDetails = $idpDetails;
                            $this->spUtility->deleteIDPApps((int)$idpDetails['id']);
                        }
                    }
                }
                // check if required values have been submitted
                if ($params['option'] == 'saveIDPSettings') {
                    if (empty($params['saml_identity_name']))
                        $params['saml_identity_name'] = $params['selected_provider'];

                    $this->checkIfRequiredFieldsEmpty(array('saml_identity_name' => $params, 'saml_issuer' => $params,
                        'saml_login_url' => $params, 'saml_x509_certificate' => $params));
                    $this->processValuesAndSaveData($params);

                    if ($this->spUtility->check_license_plan(3)) {
                        $idps = $this->spUtility->getAdminUrl('mospsaml/idps/index');
                        header('Location:' . $idps);
                        exit;
                    }
                    $this->spUtility->flushCache();
                    $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
                } else if ($params['option'] == 'upload_metadata_file') {
                    $folder = 'idpMetadata/';
                    $metadata_file = 'metadata_file';
                    $file = $this->getRequest()->getFiles($metadata_file);
                    $url = $params['upload_url'];
                    if (!empty($params['saml_identity_name']) || !empty($params['selected_provider']) && (!$this->spUtility->isBlank($file['tmp_name']) || !$this->spUtility->isBlank($url))) {
                        $matches = array();
                        $provider = !empty($params['saml_identity_name']) ? $params['saml_identity_name'] : $params['selected_provider'];
                        $regex = preg_match('/[\'^£$%&*()}{@#~?> <>,|=+¬-]/', $provider);
                        if (!$regex) {
                            $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $provider);
                            $this->spUtility->handle_upload_metadata($file, $url, $params);
                            $this->spUtility->reinitConfig();
                            $this->spUtility->flushCache();
                            $this->getMessageManager()->addSuccessMessage(SPMessages::SETTINGS_SAVED);
                        } else {
                            $this->getMessageManager()->addErrorMessage('Special characters are not allowed in the Identity Provider Name!');
                        }
                    } elseif (empty($params['saml_identity_name']) && !empty($params['selected_provider'])) {
                        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $params['selected_provider']);
                        $this->spUtility->flushCache();
                    } elseif (empty($params['saml_identity_name']) || ($this->spUtility->isBlank($file['tmp_name']) && $this->spUtility->isBlank($url))) {
                        $this->getMessageManager()->addErrorMessage('No Metadata IDP Name/File/URL Provided.');
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->debug($e->getMessage());
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $resultPage->addBreadcrumb(__('SP Settings'), __('SP Settings'));
        $resultPage->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $resultPage;
    }


    /**
     * Process Values being submitted and save data in the database.
     */
    private function processValuesAndSaveData($params)
    {

        if (!empty($params['option']) && $params['option'] != 'goBack') {

            $mo_idp_app_name = trim($params['saml_identity_name']);
            $collection = $this->spUtility->getIDPApps();
            $idpDetails = null;
            foreach ($collection as $item) {
                if ($item->getData()["idp_name"] === $mo_idp_app_name) {
                    $idpDetails = $item->getData();
                }
            }
        }

        $mo_idp_entity_id = !empty($params['saml_issuer']) ? trim($params['saml_issuer']) : '';
        $mo_idp_saml_login_url = !empty($params['saml_login_url']) ? trim($params['saml_login_url']) : '';
        $mo_idp_saml_login_binding = !empty($params['saml_login_binding_type']) ? $params['saml_login_binding_type'] : '';
        $mo_idp_saml_logout_url = !empty($params['saml_logout_url']) ? trim($params['saml_logout_url']) : '';
        $mo_idp_saml_logout_binding = !empty($params['saml_logout_binding_type']) ? $params['saml_logout_binding_type'] : '';
        $mo_idp_x509_certificate = !empty($params['saml_x509_certificate']) ? SAML2Utilities::sanitize_certificate($params['saml_x509_certificate']) : '';
        $mo_idp_response_signed = !empty($params['saml_response_signed']) && $params['saml_response_signed'] == 'Yes' ? 1 : 0;
        $mo_idp_assertion_signed = !empty($params['saml_assertion_signed']) && $params['saml_assertion_signed'] == 'Yes' ? 1 : 0;

        if($this->spUtility->check_license_plan(2) || $this->spUtility->check_license_plan(1))
        {
            $this->spUtility->setStoreConfig(SPConstants::AUTO_REDIRECT_APP, $mo_idp_app_name);
        }
        else
        {
            $this->idpDetails = $idpDetails;
        }
        $mo_idp_show_admin_link = !empty($this->idpDetails['show_admin_link']) && $this->idpDetails['show_admin_link'] == true ? 1 : 0;
        $mo_idp_show_customer_link = !empty($this->idpDetails['show_customer_link']) && $this->idpDetails['show_customer_link'] == true ? 1 : 0;
        $mo_idp_auto_create_admin_users = !empty($this->idpDetails['auto_create_admin_users']) && $this->idpDetails['auto_create_admin_users'] == true ? 1 : 0;
        $mo_idp_auto_create_customers = !empty($this->idpDetails['auto_create_customers']) && $this->idpDetails['auto_create_customers'] == true ? 1 : 0;
        $mo_idp_disable_b2c = !empty($this->idpDetails['disable_b2c']) && $this->idpDetails['disable_b2c'] == true ? 1 : 0;
        $mo_idp_force_authentication_with_idp = !empty($this->idpDetails['force_authentication_with_idp']) && $this->idpDetails['force_authentication_with_idp'] == true ? 1 : 0;
        $mo_idp_auto_redirect_to_idp = !empty($this->idpDetails['auto_redirect_to_idp']) && $this->idpDetails['auto_redirect_to_idp'] == true ? 1 : 0;
        $mo_idp_link_to_initiate_sso = !empty($this->idpDetails['link_to_initiate_sso']) && $this->idpDetails['link_to_initiate_sso'] == true ? 1 : 0;
        $mo_idp_update_attributes_on_login = !empty($this->idpDetails['update_attributes_on_login']) ? $this->idpDetails['update_attributes_on_login'] : 'unchecked';
        $mo_idp_create_magento_account_by = !empty($this->idpDetails['create_magento_account_by']) ? $this->idpDetails['create_magento_account_by'] : '';
        $mo_idp_email_attribute = !empty($this->idpDetails['email_attribute']) ? $this->idpDetails['email_attribute'] : '';
        $mo_idp_username_attribute = !empty($this->idpDetails['username_attribute']) ? $this->idpDetails['username_attribute'] : '';
        $mo_idp_firstname_attribute = !empty($this->idpDetails['firstname_attribute']) ? $this->idpDetails['firstname_attribute'] : '';
        $mo_idp_lastname_attribute = !empty($this->idpDetails['lastname_attribute']) ? $this->idpDetails['lastname_attribute'] : '';
        $mo_idp_group_attribute = !empty($this->idpDetails['group_attribute']) ? $this->idpDetails['group_attribute'] : '';
        $mo_idp_billing_city_attribute = !empty($this->idpDetails['billing_city_attribute']) ? $this->idpDetails['billing_city_attribute'] : '';
        $mo_idp_billing_state_attribute = !empty($this->idpDetails['billing_state_attribute']) ? $this->idpDetails['billing_state_attribute'] : '';
        $mo_idp_billing_country_attribute = !empty($this->idpDetails['billing_country_attribute']) ? $this->idpDetails['billing_country_attribute'] : '';
        $mo_idp_billing_address_attribute = !empty($this->idpDetails['billing_address_attribute']) ? $this->idpDetails['billing_address_attribute'] : '';
        $mo_idp_billing_phone_attribute = !empty($this->idpDetails['billing_phone_attribute']) ? $this->idpDetails['billing_phone_attribute'] : '';
        $mo_idp_billing_zip_attribute = !empty($this->idpDetails['billing_zip_attribute']) ? $this->idpDetails['billing_zip_attribute'] : '';
        $mo_idp_shipping_city_attribute = !empty($this->idpDetails['shipping_city_attribute']) ? $this->idpDetails['shipping_city_attribute'] : '';
        $mo_idp_shipping_state_attribute = !empty($this->idpDetails['shipping_state_attribute']) ? $this->idpDetails['shipping_state_attribute'] : '';
        $mo_idp_shipping_country_attribute = !empty($this->idpDetails['shipping_country_attribute']) ? $this->idpDetails['shipping_country_attribute'] : '';
        $mo_idp_shipping_address_attribute = !empty($this->idpDetails['shipping_address_attribute']) ? $this->idpDetails['shipping_address_attribute'] : '';
        $mo_idp_shipping_phone_attribute = !empty($this->idpDetails['shipping_phone_attribute']) ? $this->idpDetails['shipping_phone_attribute'] : '';
        $mo_idp_shipping_zip_attribute = !empty($this->idpDetails['shipping_zip_attribute']) ? $this->idpDetails['shipping_zip_attribute'] : '';
        $mo_idp_b2b_attribute = !empty($this->idpDetails['b2b_attribute']) ? $this->idpDetails['b2b_attribute'] : '';
        $mo_idp_custom_tablename = !empty($this->idpDetails['custom_tablename']) ? $this->idpDetails['custom_tablename'] : '';
        $mo_idp_custom_attributes = !empty($this->idpDetails['custom_attributes']) ? $this->idpDetails['custom_attributes'] : '';
        $mo_idp_do_not_autocreate_if_roles_not_mapped = !empty($this->idpDetails['do_not_autocreate_if_roles_not_mapped']) ? $this->idpDetails['do_not_autocreate_if_roles_not_mapped'] : 'unchecked';
        $mo_idp_update_backend_roles_on_sso = !empty($this->idpDetails['update_backend_roles_on_sso']) ? $this->idpDetails['update_backend_roles_on_sso'] : 'unchecked';
        $mo_idp_update_frontend_groups_on_sso = !empty($this->idpDetails['update_frontend_groups_on_sso']) ? $this->idpDetails['update_frontend_groups_on_sso'] : 'unchecked';
        $mo_idp_default_group = !empty($this->idpDetails['default_group']) ? $this->idpDetails['default_group'] : '';
        $mo_idp_default_role = !empty($this->idpDetails['default_role']) ? $this->idpDetails['default_role'] : '';
        $mo_idp_groups_mapped = !empty($this->idpDetails['groups_mapped']) ? $this->idpDetails['groups_mapped'] : '';
        $mo_idp_roles_mapped = !empty($this->idpDetails['roles_mapped']) ? $this->idpDetails['roles_mapped'] : '';
        $mo_saml_logout_redirect_url = !empty($this->idpDetails['saml_logout_redirect_url']) ? $this->idpDetails['saml_logout_redirect_url'] : '';
        $billinandshippingchcekbox = !empty($this->idpDetails['saml_enable_billingandshipping']) ? $this->idpDetails['saml_enable_billingandshipping'] : 'none';
        $sameasbilling = !empty($this->idpDetails['saml_sameasbilling']) ? $this->idpDetails['saml_sameasbilling'] : 'none';

        $mo_saml_headless_sso = !empty($this->idpDetails['mo_saml_headless_sso']) && $this->idpDetails['mo_saml_headless_sso'] == true ? 1 : 0;
        $mo_saml_frontend_post_url = !empty($this->idpDetails['mo_saml_frontend_post_url']) ? $this->idpDetails['mo_saml_frontend_post_url'] : '';

        if (!is_null($idpDetails)) {
            $this->spUtility->deleteIDPApps((int)$idpDetails['id']);
        } else {
            $this->spUtility->checkIdpLimit();
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
        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $mo_idp_app_name);
        $this->spUtility->setStoreConfig(SPConstants::IDP_NAME, $mo_idp_app_name);
        $this->spUtility->reinitConfig();
    }


    /**
     * Is the user allowed to view the Service Provider settings.
     * This is based on the ACL set by the admin in the backend.
     * Works in conjugation with acl.xml
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_SPSETTINGS);
    }
}
