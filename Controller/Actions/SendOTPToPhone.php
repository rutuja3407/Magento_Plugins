<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Exception\OTPSendingFailedException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class SendOTPToPhone extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\160\x68\157\156\145" => $this->REQUEST));
        $zw = $this->REQUEST["\160\x68\x6f\156\x65"];
        $this->startVerificationProcess('', $zw);
    }
    private function startVerificationProcess($bs, $zw)
    {
        $bs = Curl::mo_send_otp_token(SPConstants::OTP_TYPE_PHONE, '', $zw);
        $bs = json_decode((string) $bs, true);
        if (strcasecmp($bs["\163\x74\x61\164\x75\x73"], "\123\x55\103\103\x45\123\123") == 0) {
            goto hZ;
        }
        $this->handleOTPSendFailed();
        goto nb;
        hZ:
        $this->handleOTPSentSuccess($bs, $zw);
        nb:
    }
    private function handleOTPSentSuccess($bs, $zw)
    {
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, $bs["\164\170\111\x64"]);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_PHONE, $zw);
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, SPConstants::OTP_TYPE_PHONE);
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        $this->messageManager->addSuccessMessage(SPMessages::parse("\120\110\x4f\116\x45\137\x4f\x54\120\137\123\x45\116\124", array("\160\x68\157\156\x65" => $zw)));
    }
    private function handleOTPSendFailed()
    {
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        throw new OTPSendingFailedException();
    }
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
}
