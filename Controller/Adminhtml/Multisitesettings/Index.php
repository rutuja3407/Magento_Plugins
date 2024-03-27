<?php


namespace MiniOrange\SP\Controller\Adminhtml\Multisitesettings;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Helper\Curl;
class Index extends BaseAdminAction implements HttpGetActionInterface, HttpPostActionInterface
{
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto HR;
        }
        $this->spUtility->setStoreConfig(SPConstants::WEBSITES_LIMIT, AESEncryption::encrypt_data(2, SPConstants::DEFAULT_TOKEN_VALUE));
        $bc = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($bc == NULL)) {
            goto kz;
        }
        $MG = $this->spUtility->getCurrentAdminUser()->getData();
        $zG = $this->spUtility->getMagnetoVersion();
        $cN = $MG["\x65\155\x61\x69\x6c"];
        $Aq = $MG["\x66\151\x72\163\164\x6e\141\x6d\145"];
        $ko = $MG["\x6c\141\x73\164\x6e\141\x6d\x65"];
        $Lu = $this->spUtility->getBaseUrl();
        $D7 = array($Aq, $ko, $zG, $Lu);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($cN, "\111\x6e\x73\164\x61\x6c\154\x65\144\40\x53\x75\x63\143\145\163\163\x66\x75\154\154\x79\55\x41\x63\143\157\165\156\164\40\124\141\x62", $D7);
        $this->spUtility->flushCache();
        kz:
        HR:
        try {
            $this->checkIfValidPlugin();
            $As = $this->getRequest()->getParams();
            if (!$this->isFormOptionBeingSaved($As)) {
                goto sz;
            }
            $wc = $As["\167\x65\x62\x73\151\164\x65\x5f\x63\x6f\x75\x6e\164"];
            $xG = $As["\x63\x68\145\143\x6b\137\x63\x6f\165\156\164"];
            $RT = null;
            $Zm = $this->spUtility->getWebsiteLimit();
            if ($xG <= $Zm) {
                goto Y4;
            }
            $this->messageManager->addErrorMessage(SPMessages::WEBSITE_ERROR);
            $this->clearData();
            goto Bn;
            Y4:
            foreach ($As as $zg => $Yk) {
                if (!($Yk == "\x74\x72\x75\145")) {
                    goto w9;
                }
                $RT[$zg] = $Yk;
                w9:
                h9:
            }
            sW:
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            $this->processValuesAndSaveData($xG, $RT, $As, $wc);
            Bn:
            $this->spUtility->flushCache();
            $this->spUtility->reinitConfig();
            sz:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->logger->debug($sS->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\115\165\154\164\x69\x73\151\x74\x65\x20\x53\145\164\164\151\x6e\147\x73"), __("\x4d\x75\x6c\x74\151\163\x69\164\x65\x20\x53\x65\164\164\151\x6e\x67\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    public function clearData()
    {
        foreach ($this->sp->getWebsiteCollection() as $XW) {
            $zg = $XW->getId();
            $this->spUtility->setStoreConfig($zg, 0);
            mQ:
        }
        uF:
    }
    private function processValuesAndSaveData($xG, $RT, $As, $wc)
    {
        foreach ($this->sp->getWebsiteCollection() as $XW) {
            $zg = $XW->getId();
            $this->spUtility->setStoreConfig($zg, 0);
            yq:
        }
        O6:
        if (empty($RT) || $xG == 0) {
            goto dG;
        }
        foreach ($RT as $zg => $Yk) {
            foreach ($this->sp->getWebsiteCollection() as $XW) {
                $lA = $XW->getId();
                if ($zg == $lA) {
                    goto V0;
                }
                $this->spUtility->setStoreConfig($zg, 0);
                goto up;
                V0:
                $this->spUtility->log_debug("\x6d\x61\x74\x63\x68\x65\144\72\40" . $lA);
                $this->spUtility->setStoreConfig($zg, 1);
                goto j_;
                up:
                yv:
            }
            j_:
            ki:
        }
        rc:
        goto hl;
        dG:
        $this->clearData();
        hl:
        $this->spUtility->setStoreConfig(SPConstants::WEBSITE_COUNT, $xG);
        $this->spUtility->setStoreConfig(SPConstants::WEBSITE_IDS, json_encode($RT));
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_MULTISITE);
    }
}
