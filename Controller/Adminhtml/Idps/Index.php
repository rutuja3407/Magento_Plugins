<?php


namespace MiniOrange\SP\Controller\Adminhtml\Idps;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\Saml2\MetadataGenerator;
use MiniOrange\SP\Block\Sp;
class Index extends BaseAdminAction
{
    public function execute()
    {
        try {
            $this->checkIfValidPlugin();
            $As = $this->getRequest()->getParams();
            if (empty($As["\144\x65\x6c\145\x74\x65"])) {
                goto Ik;
            }
            $this->spUtility->deleteIDPApps((int) $As["\x69\x64"]);
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            Ik:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->logger->debug($sS->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\111\x44\x50\163"), __("\111\104\x50\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_IDPSETTINGS);
    }
}
