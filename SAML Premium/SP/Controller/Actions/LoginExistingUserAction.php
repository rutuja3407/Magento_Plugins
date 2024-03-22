<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Exception\AccountAlreadyExistsException;
use MiniOrange\SP\Helper\Exception\NotRegisteredException;
use MiniOrange\SP\Block\Sp;
class LoginExistingUserAction extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        if (!$this->spUtility->isTrialExpired()) {
            goto CI;
        }
        $this->spUtility->log_debug("\120\x72\157\x63\145\x73\x73\125\x73\145\x72\x41\143\164\151\157\x6e\72\x20\x65\170\x65\143\x75\x74\x65\40\x3a\40\x59\157\x75\162\x20\x64\x65\x6d\157\x20\x61\143\x63\x6f\165\x6e\x74\40\x68\x61\163\x20\145\x78\160\151\x72\145\x64\56");
        print_r("\x59\x6f\165\x72\x20\x44\x65\155\157\40\x61\143\143\x6f\x75\x6e\164\40\150\141\x73\40\x65\x78\x70\x69\162\x65\144\x2e\40\x50\154\x65\x61\163\x65\x20\x63\157\x6e\164\x61\x63\164\40\x6d\141\147\x65\x6e\x74\x6f\x73\x75\x70\x70\x6f\162\x74\100\x78\x65\143\x75\x72\151\146\171\56\143\157\x6d");
        exit;
        CI:
        $this->checkIfRequiredFieldsEmpty(array("\x65\155\141\151\x6c" => $this->REQUEST, "\x70\141\163\163\167\157\162\144" => $this->REQUEST, "\x73\x75\142\155\151\x74" => $this->REQUEST));
        $fx = $this->REQUEST["\145\155\x61\151\154"];
        $Ya = $this->REQUEST["\160\141\163\163\167\x6f\x72\144"];
        $XX = $this->REQUEST["\163\x75\142\x6d\x69\164"];
        if ($XX == "\x47\x6f\x20\x42\x61\143\153") {
            goto Bc;
        }
        $this->getCurrentCustomer($fx, $Ya);
        goto W5;
        Bc:
        $this->goBackToRegistrationPage();
        W5:
    }
    private function goBackToRegistrationPage()
    {
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_PHONE, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, '');
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, '');
    }
    private function getCurrentCustomer($fx, $Ya)
    {
        $Ge = Curl::get_customer_key($fx, $Ya);
        $rP = json_decode((string) $Ge, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            goto n7;
        }
        $this->spUtility->setStoreConfig("\x6d\x69\x6e\x69\157\x72\x61\156\147\145\x2f\163\141\x6d\154\163\x70\57\x72\x65\x67\151\163\164\x72\141\x74\x69\157\x6e\57\163\x74\141\x74\x75\x73", SPConstants::STATUS_VERIFY_LOGIN);
        throw new AccountAlreadyExistsException();
        goto xd;
        n7:
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, $fx);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, $rP["\151\144"]);
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, $rP["\141\x70\151\113\x65\171"]);
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, $rP["\x74\x6f\x6b\x65\x6e"]);
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_COMPLETE_LOGIN);
        $this->messageManager->addSuccessMessage(SPMessages::REG_SUCCESS);
        xd:
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
}
