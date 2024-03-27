<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Exception\PasswordMismatchException;
use MiniOrange\SP\Helper\Exception\OTPSendingFailedException;
class RegisterNewUserAction extends BaseAdminAction
{
    private $REQUEST;
    private $loginExistingUserAction;
    protected $sp;
    public function __construct(\Magento\Backend\App\Action\Context $Gc, \Magento\Framework\View\Result\PageFactory $VM, \MiniOrange\SP\Helper\SPUtility $Kx, \Magento\Framework\Message\ManagerInterface $c0, \Psr\Log\LoggerInterface $hI, \MiniOrange\SP\Controller\Actions\LoginExistingUserAction $iw, Sp $vT)
    {
        parent::__construct($Gc, $VM, $Kx, $c0, $hI, $vT);
        $this->loginExistingUserAction = $iw;
        $this->sp = $vT;
    }
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\x65\155\x61\151\x6c" => $this->REQUEST, "\x70\x61\x73\163\x77\157\x72\x64" => $this->REQUEST, "\x63\x6f\156\x66\151\x72\155\120\141\x73\163\167\x6f\162\144" => $this->REQUEST));
        $fx = $this->REQUEST["\145\155\x61\151\154"];
        $Ya = $this->REQUEST["\160\x61\163\x73\x77\x6f\162\x64"];
        $K9 = $this->REQUEST["\143\157\x6e\146\x69\x72\x6d\120\x61\x73\163\x77\x6f\x72\x64"];
        $GC = $this->REQUEST["\143\157\155\x70\x61\156\171\116\141\155\145"];
        $Aq = $this->REQUEST["\x66\151\x72\x73\164\x4e\x61\155\145"];
        $ko = $this->REQUEST["\x6c\141\163\164\x4e\x61\155\x65"];
        if (!(strcasecmp($K9, $Ya) != 0)) {
            goto pi;
        }
        throw new PasswordMismatchException();
        pi:
        $Up = $this->checkIfUserExists($fx);
        if (strcasecmp($Up["\163\164\x61\164\x75\x73"], "\103\125\123\x54\117\x4d\105\122\x5f\116\117\124\137\x46\117\125\116\x44") == 0) {
            goto I4;
        }
        $this->loginExistingUserAction->setRequestParam($this->REQUEST)->execute();
        goto z4;
        I4:
        $this->startVerificationProcess($Up, $fx, $GC, $Aq, $ko);
        z4:
    }
    private function checkIfUserExists($fx)
    {
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, $fx);
        $Ge = Curl::check_customer($fx);
        return json_decode((string) $Ge, true);
    }
    private function startVerificationProcess($Up, $fx, $GC, $Aq, $ko)
    {
        $Up = Curl::mo_send_otp_token(SPConstants::OTP_TYPE_EMAIL, $fx);
        $Up = json_decode((string) $Up, true);
        if (strcasecmp($Up["\x73\164\x61\x74\x75\x73"], "\123\x55\x43\103\105\x53\123") == 0) {
            goto Fd;
        }
        $this->handleOTPSendFailed();
        goto w8;
        Fd:
        $this->handleOTPSentSuccess($Up, $fx, $GC, $Aq, $ko);
        w8:
    }
    private function handleOTPSentSuccess($Up, $fx, $GC, $Aq, $ko)
    {
        $this->spUtility->setStoreConfig(SPConstants::TXT_ID, $Up["\164\170\111\144"]);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, $fx);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CNAME, $GC);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_FIRSTNAME, $Aq);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LASTNAME, $ko);
        $this->spUtility->setStoreConfig(SPConstants::OTP_TYPE, SPConstants::OTP_TYPE_EMAIL);
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_EMAIL);
        $this->messageManager->addSuccessMessage(SPMessages::parse("\105\115\x41\x49\114\137\117\x54\x50\x5f\123\x45\x4e\124", array("\145\x6d\x61\151\x6c" => $fx)));
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
