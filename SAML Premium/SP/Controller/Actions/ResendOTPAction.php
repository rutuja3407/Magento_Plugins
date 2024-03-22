<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Exception\OTPSendingFailedException;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
class ResendOTPAction extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        $C3 = $this->spUtility->getStoreConfig(SPConstants::OTP_TYPE);
        $fx = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $Rs = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_PHONE);
        $this->startVerificationProcess($Rs, $fx, $C3);
    }
    private function startVerificationProcess($Rs, $fx, $C3)
    {
        $Up = Curl::mo_send_otp_token($C3, $fx, $Rs);
        $Up = json_decode((string) $Up, true);
        if (strcasecmp($Up["\x73\x74\141\x74\165\163"], "\x53\125\103\x43\x45\x53\123") == 0) {
            goto nS;
        }
        $this->handleOTPSendFailed();
        goto ns;
        nS:
        $this->handleOTPSentSuccess($Up, $Rs, $fx, $C3);
        ns:
    }
    private function handleOTPSentSuccess($Up, $Rs, $fx, $C3)
    {
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, $Up["\x74\170\111\144"]);
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, $C3);
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        $by = $C3 == SPConstants::OTP_TYPE_PHONE ? SPMessages::parse("\120\x48\117\116\105\137\x4f\x54\x50\x5f\123\105\116\124", array("\160\150\x6f\x6e\145" => $Rs)) : SPMessages::parse("\x45\115\x41\111\x4c\137\117\124\120\137\x53\x45\x4e\x54", array("\145\x6d\141\151\154" => $fx));
        $this->messageManager->addSuccessMessage($by);
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
