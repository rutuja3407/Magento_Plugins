<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Exception\AccountAlreadyExistsException;
use MiniOrange\SP\Helper\Exception\OTPValidationFailedException;
use MiniOrange\SP\Helper\Exception\RequiredFieldsException;
use MiniOrange\SP\Helper\Exception\OTPRequiredException;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
class ValidateOTPAction extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\163\165\142\x6d\x69\164" => $this->REQUEST));
        $XX = $this->REQUEST["\x73\165\x62\x6d\151\164"];
        $pe = $this->spUtility->getStoreConfig(SPConstants::TXT_ID);
        $MT = $this->REQUEST["\x6f\x74\160\x5f\x74\x6f\153\145\156"];
        if ($XX == "\x42\x61\x63\x6b") {
            goto rI;
        }
        $this->validateOTP($pe, $MT);
        goto Ev;
        rI:
        $this->goBackToRegistrationPage();
        Ev:
    }
    private function goBackToRegistrationPage()
    {
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_PHONE, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, '');
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, '');
    }
    private function validateOTP($v0, $y7)
    {
        if (!(empty($this->REQUEST["\157\164\160\137\164\x6f\x6b\145\156"]) || $this->REQUEST["\157\x74\x70\137\x74\x6f\x6b\145\x6e"] == '')) {
            goto Qt;
        }
        throw new OTPRequiredException();
        Qt:
        $Up = Curl::validate_otp_token($v0, $y7);
        $Up = json_decode((string) $Up, true);
        if (strcasecmp($Up["\x73\x74\x61\164\165\163"], "\123\125\x43\103\x45\x53\x53") == 0) {
            goto nZ;
        }
        $this->handleOTPValidationFailed();
        goto dF;
        nZ:
        $this->handleOTPValidationSuccess($Up);
        dF:
    }
    private function handleOTPValidationSuccess($Up)
    {
        $GC = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_CNAME);
        $Aq = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_FIRSTNAME);
        $ko = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_LASTNAME);
        $fx = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $Rs = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_PHONE);
        $Up = Curl::create_customer($fx, $GC, '', $Rs, $Aq, $ko);
        $Up = json_decode((string) $Up, true);
        if (strcasecmp($Up["\163\164\141\164\165\163"], "\123\x55\x43\x43\x45\x53\x53") == 0) {
            goto Ij;
        }
        if (!(strcasecmp($Up["\x73\164\x61\164\x75\x73"], "\103\x55\123\x54\117\x4d\105\122\x5f\x55\123\x45\122\x4e\101\x4d\x45\x5f\101\114\x52\x45\x41\104\131\137\x45\130\111\123\124\x53") == 0)) {
            goto wW;
        }
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        throw new AccountAlreadyExistsException();
        wW:
        goto E9;
        Ij:
        $this->configureUserInMagento($Up);
        E9:
    }
    private function configureUserInMagento($Up)
    {
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, $Up["\151\x64"]);
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, $Up["\x61\160\x69\113\x65\171"]);
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, $Up["\x74\157\x6b\x65\x6e"]);
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, '');
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_COMPLETE_LOGIN);
        $this->messageManager->addSuccessMessage(SPMessages::REG_SUCCESS);
    }
    private function handleOTPValidationFailed()
    {
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        throw new OTPValidationFailedException();
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
}
