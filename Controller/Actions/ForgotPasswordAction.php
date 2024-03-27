<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\Exception\AccountAlreadyExistsException;
use MiniOrange\SP\Helper\Exception\NotRegisteredException;
class ForgotPasswordAction extends BaseAdminAction
{
    private $REQUEST;
    protected $sp;
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\145\x6d\141\151\x6c" => $this->REQUEST));
        $fx = $this->REQUEST["\x65\155\x61\x69\154"];
        $rP = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_KEY);
        $Io = $this->spUtility->getStoreConfig(SPConstants::API_KEY);
        $Ge = json_decode((string) Curl::forgot_password($fx, $rP, $Io), true);
        if (strcasecmp($Ge["\163\x74\141\164\165\x73"], "\123\x55\103\x43\x45\x53\x53") == 0) {
            goto K4;
        }
        $this->messageManager->addErrorMessage(SPMessages::PASS_RESET_ERROR);
        goto A_;
        K4:
        $this->messageManager->addSuccessMessage(SPMessages::PASS_RESET);
        A_:
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
}
