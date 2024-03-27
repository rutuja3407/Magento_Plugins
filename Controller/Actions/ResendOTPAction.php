<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Exception\OTPSendingFailedException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class ResendOTPAction extends BaseAdminAction
{
    private $REQUEST;
    public function execute()
    {
        $oo = $this->spUtility->getStoreConfig(SPConstants::OTP_TYPE);
        $EK = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_EMAIL);
        $zw = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_PHONE);
        $this->startVerificationProcess($zw, $EK, $oo);
    }
    private function startVerificationProcess($zw, $EK, $oo)
    {
        $bs = Curl::mo_send_otp_token($oo, $EK, $zw);
        $bs = json_decode((string) $bs, true);
        if (strcasecmp($bs["\163\164\x61\x74\165\x73"], "\123\x55\103\103\x45\123\123") == 0) {
            goto gF;
        }
        $this->handleOTPSendFailed();
        goto Uj;
        gF:
        $this->handleOTPSentSuccess($bs, $zw, $EK, $oo);
        Uj:
    }
    private function handleOTPSentSuccess($bs, $zw, $EK, $oo)
    {
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, $bs["\x74\x78\111\144"]);
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, $oo);
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        $qx = $oo == SPConstants::OTP_TYPE_PHONE ? SPMessages::parse("\x50\110\x4f\116\x45\137\117\124\x50\x5f\x53\x45\x4e\124", array("\160\150\157\156\145" => $zw)) : SPMessages::parse("\105\115\x41\111\114\x5f\117\124\120\x5f\x53\105\116\x54", array("\x65\x6d\x61\151\x6c" => $EK));
        $this->messageManager->addSuccessMessage($qx);
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
