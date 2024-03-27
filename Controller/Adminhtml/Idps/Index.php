<?php

namespace MiniOrange\SP\Controller\Adminhtml\Idps;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;

/**
 * This class handles the action for endpoint: mospsaml/idpsettings/Index
 * Extends the \Magento\Backend\App\Action for Admin Actions which
 * inturn extends the \Magento\Framework\App\Action\Action class necessary
 * for each Controller class
 */
class Index extends BaseAdminAction
{

    /**
     * The first function to be called when a Controller class is invoked.
     * Usually, has all our controller logic. Returns a view/page/template
     * to be shown to the users.
     *
     * This function gets and prepares all our SP config data from the
     * database. It's called when you visis the moasaml/idpsettings/Index
     * URL. It prepares all the values required on the SP setting
     * page in the backend and returns the block to be displayed.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $this->checkIfValidPlugin(); //check if user has registered himself
            $params = $this->getRequest()->getParams();
            if (!empty($params['delete'])) {
                $this->spUtility->deleteIDPApps((int)$params['id']);
                $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->debug($e->getMessage());
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $resultPage->addBreadcrumb(__('IDPs'), __('IDPs'));
        $resultPage->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $resultPage;
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
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_IDPSETTINGS);
    }
}
