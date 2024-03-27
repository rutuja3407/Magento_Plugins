<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Exception\OTPSendingFailedException;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
class SendOTPToPhone extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\x70\150\157\x6e\x65" => $this->REQUEST));
        $Rs = $this->REQUEST["\x70\x68\157\x6e\x65"];
        $this->startVerificationProcess('', $Rs);
    }
    private function startVerificationProcess($Up, $Rs)
    {
        $Up = Curl::mo_send_otp_token(SPConstants::OTP_TYPE_PHONE, '', $Rs);
        $Up = json_decode((string) $Up, true);
        if (strcasecmp($Up["\x73\x74\141\x74\x75\x73"], "\x53\x55\x43\103\x45\x53\x53") == 0) {
            goto fx;
        }
        $this->handleOTPSendFailed();
        goto Kj;
        fx:
        $this->handleOTPSentSuccess($Up, $Rs);
        Kj:
    }
    private function handleOTPSentSuccess($Up, $Rs)
    {
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, $Up["\164\170\x49\x64"]);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_PHONE, $Rs);
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, SPConstants::OTP_TYPE_PHONE);
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        $this->messageManager->addSuccessMessage(SPMessages::parse("\120\x48\x4f\116\105\x5f\x4f\x54\x50\x5f\123\105\x4e\x54", array("\x70\x68\157\x6e\x65" => $Rs)));
    }
    private function handleOTPSendFailed()
    {
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        throw new OTPSendingFailedException();
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
}
