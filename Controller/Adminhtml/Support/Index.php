<?php


namespace MiniOrange\SP\Controller\Adminhtml\Support;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class Index extends BaseAdminAction
{
    public function execute()
    {
        try {
            $Te = $this->getRequest()->getParams();
            if (!$this->isFormOptionBeingSaved($Te)) {
                goto Gj;
            }
            $this->checkIfSupportQueryFieldsEmpty(array("\x65\x6d\141\151\154" => $Te, "\x71\165\x65\x72\x79" => $Te));
            $EK = $Te["\x65\155\x61\x69\x6c"];
            $zw = $Te["\160\150\x6f\x6e\x65"];
            $NB = $Te["\161\x75\x65\162\x79"];
            $a9 = $this->spUtility->getBaseUrl();
            Curl::submit_contact_us($EK, $zw, $NB, $a9);
            $this->messageManager->addSuccessMessage(SPMessages::QUERY_SENT);
            Gj:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->logger->debug($IR->getMessage());
        }
        $PJ = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $PJ->setUrl($this->_redirect->getRefererUrl());
        return $PJ;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_SUPPORT);
    }
}
