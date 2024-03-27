<?php


namespace MiniOrange\SP\Controller\Adminhtml\Upgrade;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\SPConstants;
class Index extends BaseAdminAction
{
    public function execute()
    {
        try {
            $this->checkIfValidPlugin();
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->logger->debug($IR->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\x55\160\147\x72\x61\144\145\40\120\x6c\141\x6e\163"), __("\125\x70\x67\x72\141\x64\145\40\120\154\141\x6e\x73"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_UPGRADE);
    }
}
