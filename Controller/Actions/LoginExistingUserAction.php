<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Exception\AccountAlreadyExistsException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class LoginExistingUserAction extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        if (!$this->spUtility->isTrialExpired()) {
            goto y1;
        }
        $this->spUtility->log_debug("\120\162\x6f\143\x65\163\163\125\x73\x65\162\x41\x63\164\151\x6f\156\72\40\145\x78\x65\143\165\164\145\x20\72\40\x59\157\165\162\40\x64\145\155\157\x20\141\x63\143\x6f\165\156\164\x20\x68\x61\x73\40\145\170\x70\151\x72\x65\x64\56");
        print_r("\x59\x6f\x75\162\40\x44\145\x6d\157\40\x61\x63\x63\x6f\x75\156\x74\40\x68\x61\x73\40\x65\x78\160\151\x72\x65\144\x2e\x20\x50\154\x65\141\x73\145\40\x63\x6f\x6e\164\141\x63\x74\x20\155\x61\x67\x65\156\x74\x6f\163\165\160\160\157\x72\x74\x40\x78\x65\x63\165\x72\x69\146\171\56\143\157\x6d");
        exit;
        y1:
        $this->checkIfRequiredFieldsEmpty(array("\145\x6d\x61\151\154" => $this->REQUEST, "\160\141\x73\x73\167\x6f\x72\144" => $this->REQUEST, "\163\165\142\155\x69\x74" => $this->REQUEST));
        $EK = $this->REQUEST["\145\155\x61\x69\x6c"];
        $XM = $this->REQUEST["\160\x61\163\163\167\x6f\x72\x64"];
        $uB = $this->REQUEST["\163\x75\x62\x6d\x69\x74"];
        if ($uB == "\107\157\x20\x42\141\x63\x6b") {
            goto kx;
        }
        $this->getCurrentCustomer($EK, $XM);
        goto Jc;
        kx:
        $this->goBackToRegistrationPage();
        Jc:
    }
    private function goBackToRegistrationPage()
    {
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_PHONE, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, '');
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, '');
    }
    private function getCurrentCustomer($EK, $XM)
    {
        $Qz = Curl::get_customer_key($EK, $XM);
        $rY = json_decode((string) $Qz, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            goto dc;
        }
        $this->spUtility->setStoreConfig("\155\151\156\151\157\x72\141\x6e\x67\145\57\x73\141\x6d\154\163\x70\x2f\x72\145\x67\x69\163\x74\162\141\164\151\157\x6e\57\x73\x74\141\164\165\163", SPConstants::STATUS_VERIFY_LOGIN);
        throw new AccountAlreadyExistsException();
        goto Da;
        dc:
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, $EK);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, $rY["\151\x64"]);
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, $rY["\x61\x70\151\x4b\145\171"]);
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, $rY["\164\x6f\x6b\145\x6e"]);
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_COMPLETE_LOGIN);
        $this->messageManager->addSuccessMessage(SPMessages::REG_SUCCESS);
        Da:
    }
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
}
