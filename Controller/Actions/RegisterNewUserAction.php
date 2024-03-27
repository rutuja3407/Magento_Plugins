<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Exception\OTPSendingFailedException;
use MiniOrange\SP\Helper\Exception\PasswordMismatchException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class RegisterNewUserAction extends BaseAdminAction
{
    protected $sp;
    private $REQUEST;
    private $loginExistingUserAction;
    public function __construct(\Magento\Backend\App\Action\Context $gt, \Magento\Framework\View\Result\PageFactory $Jq, \MiniOrange\SP\Helper\SPUtility $fR, \Magento\Framework\Message\ManagerInterface $b_, \Psr\Log\LoggerInterface $kU, \MiniOrange\SP\Controller\Actions\LoginExistingUserAction $AS, Sp $ou)
    {
        parent::__construct($gt, $Jq, $fR, $b_, $kU, $ou);
        $this->loginExistingUserAction = $AS;
        $this->sp = $ou;
    }
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\x65\x6d\141\x69\x6c" => $this->REQUEST, "\160\x61\163\x73\x77\x6f\x72\144" => $this->REQUEST, "\143\x6f\156\146\151\162\x6d\x50\x61\x73\x73\167\157\162\x64" => $this->REQUEST));
        $EK = $this->REQUEST["\x65\155\x61\151\x6c"];
        $XM = $this->REQUEST["\160\x61\x73\x73\x77\157\162\144"];
        $LW = $this->REQUEST["\x63\x6f\x6e\x66\x69\162\155\x50\141\x73\163\167\157\x72\x64"];
        $a9 = $this->REQUEST["\x63\x6f\x6d\x70\x61\x6e\x79\x4e\141\155\x65"];
        $FO = $this->REQUEST["\146\151\x72\163\x74\x4e\x61\x6d\x65"];
        $Fo = $this->REQUEST["\154\x61\163\x74\x4e\x61\x6d\145"];
        if (!(strcasecmp($LW, $XM) != 0)) {
            goto M_;
        }
        throw new PasswordMismatchException();
        M_:
        $bs = $this->checkIfUserExists($EK);
        if (strcasecmp($bs["\163\x74\141\x74\x75\x73"], "\x43\125\x53\x54\117\115\105\122\137\116\117\124\x5f\x46\117\125\116\x44") == 0) {
            goto M1;
        }
        $this->loginExistingUserAction->setRequestParam($this->REQUEST)->execute();
        goto xT;
        M1:
        $this->startVerificationProcess($bs, $EK, $a9, $FO, $Fo);
        xT:
    }
    private function checkIfUserExists($EK)
    {
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, $EK);
        $Qz = Curl::check_customer($EK);
        return json_decode((string) $Qz, true);
    }
    private function startVerificationProcess($bs, $EK, $a9, $FO, $Fo)
    {
        $bs = Curl::mo_send_otp_token(SPConstants::OTP_TYPE_EMAIL, $EK);
        $bs = json_decode((string) $bs, true);
        if (strcasecmp($bs["\x73\164\x61\164\165\163"], "\123\125\103\103\105\x53\x53") == 0) {
            goto BF;
        }
        $this->handleOTPSendFailed();
        goto om;
        BF:
        $this->handleOTPSentSuccess($bs, $EK, $a9, $FO, $Fo);
        om:
    }
    private function handleOTPSentSuccess($bs, $EK, $a9, $FO, $Fo)
    {
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, $bs["\x74\x78\x49\144"]);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, $EK);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CNAME, $a9);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_FIRSTNAME, $FO);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LASTNAME, $Fo);
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, SPConstants::OTP_TYPE_EMAIL);
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        $this->messageManager->addSuccessMessage(SPMessages::parse("\105\x4d\x41\111\x4c\x5f\117\x54\120\137\123\x45\116\124", array("\145\155\141\x69\154" => $EK)));
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
