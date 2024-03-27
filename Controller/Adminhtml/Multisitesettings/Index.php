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
class Index extends BaseAdminAction implements HttpGetActionInterface, HttpPostActionInterface
{
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto O4;
        }
        $this->spUtility->setStoreConfig(SPConstants::WEBSITES_LIMIT, AESEncryption::encrypt_data(2, SPConstants::DEFAULT_TOKEN_VALUE));
        $Az = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($Az == NULL)) {
            goto uv;
        }
        $fU = $this->spUtility->getCurrentAdminUser()->getData();
        $RH = $this->spUtility->getMagnetoVersion();
        $ii = $fU["\x65\x6d\141\x69\x6c"];
        $FO = $fU["\x66\x69\162\163\164\156\141\x6d\x65"];
        $Fo = $fU["\x6c\x61\163\x74\x6e\x61\155\x65"];
        $kz = $this->spUtility->getBaseUrl();
        $jT = array($FO, $Fo, $RH, $kz);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($ii, "\111\156\163\x74\141\x6c\x6c\145\144\40\123\x75\x63\143\x65\x73\163\146\x75\x6c\154\171\55\x41\143\x63\x6f\165\156\x74\40\124\x61\x62", $jT);
        $this->spUtility->flushCache();
        uv:
        O4:
        try {
            $this->checkIfValidPlugin();
            $Te = $this->getRequest()->getParams();
            if (!$this->isFormOptionBeingSaved($Te)) {
                goto PN;
            }
            $A6 = $Te["\167\x65\x62\x73\151\x74\145\137\x63\x6f\x75\156\x74"];
            $ov = $Te["\x63\x68\145\143\x6b\137\x63\157\165\156\164"];
            $ZB = null;
            $lx = $this->spUtility->getWebsiteLimit();
            if ($ov <= $lx) {
                goto wM;
            }
            $this->messageManager->addErrorMessage(SPMessages::WEBSITE_ERROR);
            $this->clearData();
            goto fM;
            wM:
            foreach ($Te as $On => $VP) {
                if (!($VP == "\164\162\x75\145")) {
                    goto Eq;
                }
                $ZB[$On] = $VP;
                Eq:
                zB:
            }
            Pz:
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            $this->processValuesAndSaveData($ov, $ZB, $Te, $A6);
            fM:
            $this->spUtility->flushCache();
            $this->spUtility->reinitConfig();
            PN:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->logger->debug($IR->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\115\x75\x6c\x74\x69\163\151\164\x65\40\123\x65\x74\x74\x69\156\147\163"), __("\115\x75\x6c\164\151\x73\x69\x74\x65\x20\123\x65\x74\x74\x69\x6e\x67\x73"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function processValuesAndSaveData($ov, $ZB, $Te, $A6)
    {
        foreach ($this->sp->getWebsiteCollection() as $Kt) {
            $On = $Kt->getId();
            $this->spUtility->setStoreConfig($On, 0);
            Rc:
        }
        oZ:
        if (empty($ZB) || $ov == 0) {
            goto zV;
        }
        foreach ($ZB as $On => $VP) {
            foreach ($this->sp->getWebsiteCollection() as $Kt) {
                $Gh = $Kt->getId();
                if ($On == $Gh) {
                    goto zN;
                }
                $this->spUtility->setStoreConfig($On, 0);
                goto to;
                zN:
                $this->spUtility->log_debug("\155\141\x74\143\150\x65\x64\72\40" . $Gh);
                $this->spUtility->setStoreConfig($On, 1);
                goto AT;
                to:
                cj:
            }
            AT:
            hs:
        }
        bv:
        goto GP;
        zV:
        $this->clearData();
        GP:
        $this->spUtility->setStoreConfig(SPConstants::WEBSITE_COUNT, $ov);
        $this->spUtility->setStoreConfig(SPConstants::WEBSITE_IDS, json_encode($ZB));
    }
    public function clearData()
    {
        foreach ($this->sp->getWebsiteCollection() as $Kt) {
            $On = $Kt->getId();
            $this->spUtility->setStoreConfig($On, 0);
            $this->spUtility->setStoreConfig(SPConstants::WEBSITE_IDS, null);
            u1:
        }
        os:
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_MULTISITE);
    }
}
