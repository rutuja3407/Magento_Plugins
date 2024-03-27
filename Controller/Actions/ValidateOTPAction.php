<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Exception\AccountAlreadyExistsException;
use MiniOrange\SP\Helper\Exception\OTPRequiredException;
use MiniOrange\SP\Helper\Exception\OTPValidationFailedException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class ValidateOTPAction extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\163\165\142\155\151\x74" => $this->REQUEST));
        $uB = $this->REQUEST["\163\165\x62\155\151\x74"];
        $TZ = $this->spUtility->getStoreConfig(SPConstants::TXT_ID);
        $tH = $this->REQUEST["\157\164\160\x5f\164\157\x6b\145\x6e"];
        if ($uB == "\x42\141\143\153") {
            goto Hm;
        }
        $this->validateOTP($TZ, $tH);
        goto W5;
        Hm:
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
    private function validateOTP($x2, $aE)
    {
        if (!(empty($this->REQUEST["\157\x74\x70\x5f\x74\x6f\153\145\156"]) || $this->REQUEST["\157\x74\160\x5f\x74\157\153\145\156"] == '')) {
            goto Ie;
        }
        throw new OTPRequiredException();
        Ie:
        $bs = Curl::validate_otp_token($x2, $aE);
        $bs = json_decode((string) $bs, true);
        if (strcasecmp($bs["\x73\164\x61\x74\x75\x73"], "\123\x55\103\x43\x45\123\x53") == 0) {
            goto z6;
        }
        $this->handleOTPValidationFailed();
        goto lA;
        z6:
        $this->handleOTPValidationSuccess($bs);
        lA:
    }
    private function handleOTPValidationSuccess($bs)
    {
        $a9 = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_CNAME);
        $FO = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_FIRSTNAME);
        $Fo = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_LASTNAME);
        $EK = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $zw = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_PHONE);
        $bs = Curl::create_customer($EK, $a9, '', $zw, $FO, $Fo);
        $bs = json_decode((string) $bs, true);
        if (strcasecmp($bs["\x73\164\x61\164\x75\163"], "\x53\x55\103\103\105\x53\x53") == 0) {
            goto Dk;
        }
        if (!(strcasecmp($bs["\x73\164\x61\x74\x75\163"], "\103\125\x53\x54\x4f\115\x45\122\137\125\x53\105\x52\x4e\101\x4d\105\137\101\x4c\x52\x45\x41\104\131\137\105\130\x49\x53\124\123") == 0)) {
            goto xu;
        }
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        throw new AccountAlreadyExistsException();
        xu:
        goto Rl;
        Dk:
        $this->configureUserInMagento($bs);
        Rl:
    }
    private function configureUserInMagento($bs)
    {
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, $bs["\151\144"]);
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, $bs["\141\x70\151\113\145\171"]);
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, $bs["\164\x6f\x6b\145\x6e"]);
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
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
}
