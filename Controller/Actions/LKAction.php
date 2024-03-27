<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Block\Sp;
class LKAction extends BaseAdminAction
{
    private $REQUEST;
    public function removeAccount()
    {
        $this->spUtility->defaultlog("\x49\156\40\x4c\113\101\x63\164\x69\157\156");
        if ($this->spUtility->micr() && $this->spUtility->mclv()) {
            goto b4;
        }
        if (!$this->spUtility->micr()) {
            goto P0;
        }
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        P0:
        goto mI;
        b4:
        $Av = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $qk = AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::SAMLSP_LK), $Av);
        $Ge = json_decode($this->spUtility->update_status($qk), true);
        $this->spUtility->mius();
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        mI:
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\x6c\x6b" => $this->REQUEST));
        $dv = $this->REQUEST["\154\x6b"];
        $Up = json_decode((string) $this->spUtility->ccl(), true);
        $this->spUtility->defaultlog("\x63\x63\x6c\x20\162\145\x73\160\x6f\x6e\x73\x65\x3a\40" . print_r($Up, true));
        $uR = array_key_exists("\x6c\151\x63\145\x6e\163\x65\105\x78\x70\151\x72\x79", $Up) ? strtotime($Up["\x6c\151\x63\145\156\163\145\x45\x78\x70\x69\x72\x79"]) === false ? null : strtotime($Up["\x6c\x69\143\x65\156\163\145\x45\x78\160\x69\x72\x79"]) : null;
        $this->spUtility->log_debug("\162\145\x73\x70\x6f\x6e\163\x65\x20\146\162\157\x6d\x20\143\x63\x6c\72\x20", print_r($Up, true));
        if (!empty($Up["\x6e\x6f\117\x66\123\165\142\123\151\x74\145\x73"])) {
            goto La;
        }
        if ($this->spUtility->check_license_plan(3)) {
            goto zg;
        }
        if ($this->spUtility->check_license_plan(2)) {
            goto ps;
        }
        $Rk = 1;
        goto Os;
        zg:
        $Rk = 2;
        goto Os;
        ps:
        $Rk = 1;
        Os:
        goto lA;
        La:
        $Rk = $Up["\x6e\x6f\117\146\123\165\x62\x53\151\164\x65\163"];
        lA:
        $this->spUtility->defaultlog("\116\x75\x6d\x62\145\x72\40\157\146\40\x73\165\142\163\x69\164\145\163\40\141\x6c\154\157\167\x65\x64\x3a\40" . $Rk);
        $this->spUtility->setStoreConfig(SPConstants::WEBSITES_LIMIT, AESEncryption::encrypt_data($Rk, SPConstants::DEFAULT_TOKEN_VALUE));
        switch ($Up["\163\x74\x61\164\165\x73"]) {
            case "\123\x55\x43\x43\x45\123\x53":
                $this->_vlk_success($dv, $uR);
                goto lQ;
            default:
                $this->_vlk_fail();
                goto lQ;
        }
        pR:
        lQ:
    }
    public function _vlk_success($qk, $uR)
    {
        $Ge = json_decode((string) $this->spUtility->vml(trim($qk)), true);
        if (!(!is_array($Ge) || empty($Ge["\x73\164\x61\164\x75\x73"]) || empty($Ge["\x73\x74\x61\164\165\163"]))) {
            goto LA;
        }
        $this->messageManager->addErrorMessage(SPMessages::ENTERED_INVALID_KEY);
        return;
        LA:
        if (strcasecmp($Ge["\x73\x74\x61\x74\165\163"], "\x53\125\x43\x43\105\123\x53") == 0) {
            goto fu;
        }
        if (strcasecmp($Ge["\163\x74\141\x74\165\163"], "\106\x41\111\x4c\105\x44") == 0) {
            goto sI;
        }
        $this->messageManager->addErrorMessage(SPMessages::ERROR_OCCURRED);
        goto vU;
        sI:
        if (strcasecmp($Ge["\155\x65\x73\x73\141\147\x65"], "\x43\157\x64\x65\40\x68\x61\x73\40\x45\170\160\151\x72\x65\x64") == 0) {
            goto sb;
        }
        $this->messageManager->addErrorMessage(SPMessages::ENTERED_INVALID_KEY);
        goto Fa;
        sb:
        $this->messageManager->addErrorMessage(SPMessages::LICENSE_KEY_IN_USE);
        Fa:
        vU:
        goto rE;
        fu:
        $zg = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, AESEncryption::encrypt_data($qk, $zg));
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, AESEncryption::encrypt_data("\164\x72\165\x65", $zg));
        $this->spUtility->setStoreConfig(SPConstants::LICENSE_EXPIRY_DATE, AESEncryption::encrypt_data($uR, $zg));
        $this->messageManager->addSuccessMessage(SPMessages::LICENSE_VERIFIED);
        rE:
    }
    public function _vlk_fail()
    {
        $zg = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, AESEncryption::encrypt_data("\x66\141\154\163\145", $zg));
        $this->messageManager->addErrorMessage(SPMessages::NOT_UPGRADED_YET);
    }
}
