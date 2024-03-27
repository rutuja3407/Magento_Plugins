<?php

namespace MiniOrange\SP\Controller\Adminhtml\Multisitesettings;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;

/**
 * This class handles the action for endpoint: mospsaml/multisitesettings/Index
 * Extends the \Magento\Backend\App\Action for Admin Actions which
 * inturn extends the \Magento\Framework\App\Action\Action class necessary
 * for each Controller class
 */
class Index extends BaseAdminAction implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * The first function to be called when a Controller class is invoked.
     * Usually, has all our controller logic. Returns a view/page/template
     * to be shown to the users.
     * This function gets and prepares all our upgrade /license page.
     * It's called when you visis the moasaml/upgrade/Index
     * URL. It prepares all the values required on the license upgrade
     * page in the backend and returns the block to be displayed.
     *
     * @return \Magento\Framework\View\Result\Page
     */

    public function execute()
    {
        if ($this->spUtility->check_license_plan(4)) {
            $this->spUtility->setStoreConfig(SPConstants::WEBSITES_LIMIT, AESEncryption::encrypt_data(2, SPConstants::DEFAULT_TOKEN_VALUE));
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
            $this->checkIfValidPlugin(); //check if user has registered himself
            $params = $this->getRequest()->getParams();
            if ($this->isFormOptionBeingSaved($params)) {

                $websiteCount = $params['website_count'];
                $count = $params['check_count'];
                $websites = null;
                $websiteLimit = $this->spUtility->getWebsiteLimit();
                if ($count <= $websiteLimit) {

                    foreach ($params as $key => $value) {
                        if ($value == 'true') {
                            $websites[$key] = $value;
                        }
                    }
                    $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
                    $this->processValuesAndSaveData($count, $websites, $params, $websiteCount);
                } else {
                    $this->messageManager->addErrorMessage(SPMessages::WEBSITE_ERROR);
                    $this->clearData();
                }
                $this->spUtility->flushCache();
                $this->spUtility->reinitConfig();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->debug($e->getMessage());
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $resultPage->addBreadcrumb(__('Multisite Settings'), __('Multisite Settings'));
        $resultPage->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $resultPage;
    }

    private function processValuesAndSaveData($count, $websites, $params, $websiteCount)
    {
        foreach ($this->sp->getWebsiteCollection() as $website) {
            $key = $website->getId();
            $this->spUtility->setStoreConfig($key, 0);
        }
        if (empty($websites) || $count == 0) {
            $this->clearData();
        } else
            foreach ($websites as $key => $value) {
                foreach ($this->sp->getWebsiteCollection() as $website) {
                    $id = $website->getId();

                    if ($key == $id) {
                        $this->spUtility->log_debug("matched: " . $id);
                        $this->spUtility->setStoreConfig($key, 1);
                        break;
                    } else {
                        $this->spUtility->setStoreConfig($key, 0);
                    }
                }
            }
        $this->spUtility->setStoreConfig(SPConstants::WEBSITE_COUNT, $count);
        $this->spUtility->setStoreConfig(SPConstants::WEBSITE_IDS, json_encode($websites));
    }

    public function clearData()
    {
        foreach ($this->sp->getWebsiteCollection() as $website) {
            $key = $website->getId();
            $this->spUtility->setStoreConfig($key, 0);
            $this->spUtility->setStoreConfig(SPConstants::WEBSITE_IDS,null);
        }
    }

    /**
     * Is the user allowed to view the Identity Provider settings.
     * This is based on the ACL set by the admin in the backend.
     * Works in conjugation with acl.xml
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_MULTISITE);
    }
}
