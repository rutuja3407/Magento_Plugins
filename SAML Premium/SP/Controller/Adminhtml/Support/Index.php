<?php


namespace MiniOrange\SP\Controller\Adminhtml\Support;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Block\Sp;
use Magento\Framework\Controller\ResultFactory;
class Index extends BaseAdminAction
{
    public function execute()
    {
        try {
            $As = $this->getRequest()->getParams();
            if (!$this->isFormOptionBeingSaved($As)) {
                goto vC;
            }
            $this->checkIfSupportQueryFieldsEmpty(array("\x65\155\x61\x69\154" => $As, "\x71\x75\x65\162\x79" => $As));
            $fx = $As["\145\x6d\x61\151\x6c"];
            $Rs = $As["\160\x68\x6f\156\145"];
            $dU = $As["\x71\x75\145\162\x79"];
            $GC = $this->spUtility->getBaseUrl();
            Curl::submit_contact_us($fx, $Rs, $dU, $GC);
            $this->messageManager->addSuccessMessage(SPMessages::QUERY_SENT);
            vC:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->logger->debug($sS->getMessage());
        }
        $Y0 = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $Y0->setUrl($this->_redirect->getRefererUrl());
        return $Y0;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_SUPPORT);
    }
}
