<?php


namespace MiniOrange\SP\Controller\Adminhtml\Idps;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class Index extends BaseAdminAction
{
    public function execute()
    {
        try {
            $this->checkIfValidPlugin();
            $Te = $this->getRequest()->getParams();
            if (empty($Te["\x64\145\154\x65\164\145"])) {
                goto j_;
            }
            $this->spUtility->deleteIDPApps((int) $Te["\x69\x64"]);
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            j_:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->logger->debug($IR->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\111\104\120\163"), __("\x49\104\x50\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_IDPSETTINGS);
    }
}
