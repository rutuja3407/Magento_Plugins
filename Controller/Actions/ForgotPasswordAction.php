<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class ForgotPasswordAction extends BaseAdminAction
{
    protected $sp;
    private $REQUEST;
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\145\155\141\151\x6c" => $this->REQUEST));
        $EK = $this->REQUEST["\x65\x6d\141\x69\x6c"];
        $rY = $this->spUtility->getStoreConfig(SPConstants::SAMLSP_KEY);
        $UE = $this->spUtility->getStoreConfig(SPConstants::API_KEY);
        $Qz = json_decode((string) Curl::forgot_password($EK, $rY, $UE), true);
        if (strcasecmp($Qz["\x73\164\x61\164\165\163"], "\x53\x55\x43\103\105\x53\123") == 0) {
            goto nx;
        }
        $this->messageManager->addErrorMessage(SPMessages::PASS_RESET_ERROR);
        goto Cw;
        nx:
        $this->messageManager->addSuccessMessage(SPMessages::PASS_RESET);
        Cw:
    }
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
}
