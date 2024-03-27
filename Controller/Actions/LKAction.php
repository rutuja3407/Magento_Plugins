<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
class LKAction extends BaseAdminAction
{
    private $REQUEST;
    public function removeAccount()
    {
        $this->spUtility->defaultlog("\111\156\x20\x4c\113\101\143\164\151\x6f\x6e");
        if ($this->spUtility->micr() && $this->spUtility->mclv()) {
            goto j9;
        }
        if (!$this->spUtility->micr()) {
            goto HZ;
        }
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        HZ:
        goto JU;
        j9:
        $Ai = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $wI = AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::SAMLSP_LK), $Ai);
        $Qz = json_decode($this->spUtility->update_status($wI), true);
        $this->spUtility->mius();
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::API_KEY, '');
        $this->spUtility->setStoreConfig(SPConstants::TOKEN, '');
        $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        JU:
    }
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array("\x6c\153" => $this->REQUEST));
        $m8 = $this->REQUEST["\x6c\x6b"];
        $bs = json_decode((string) $this->spUtility->ccl(), true);
        $this->spUtility->defaultlog("\143\143\x6c\40\x72\145\163\160\x6f\x6e\163\x65\72\x20" . print_r($bs, true));
        $XF = array_key_exists("\x6c\151\143\145\156\163\145\x45\x78\x70\x69\x72\x79", $bs) ? strtotime($bs["\154\x69\143\145\156\163\145\x45\170\x70\x69\x72\x79"]) === false ? null : strtotime($bs["\154\151\x63\145\156\x73\145\105\x78\x70\x69\x72\x79"]) : null;
        $this->spUtility->log_debug("\x72\x65\163\x70\x6f\156\x73\145\40\146\162\157\x6d\x20\143\x63\x6c\x3a\x20", print_r($bs, true));
        if (!empty($bs["\x6e\157\117\x66\x53\165\142\x53\151\x74\x65\x73"])) {
            goto aa;
        }
        if ($this->spUtility->check_license_plan(3)) {
            goto ws;
        }
        if ($this->spUtility->check_license_plan(2)) {
            goto Bo;
        }
        $Iq = 1;
        goto mA;
        ws:
        $Iq = 2;
        goto mA;
        Bo:
        $Iq = 1;
        mA:
        goto A8;
        aa:
        $Iq = $bs["\156\157\x4f\146\123\165\142\x53\x69\164\145\163"];
        A8:
        $this->spUtility->defaultlog("\116\165\x6d\x62\145\162\x20\x6f\146\x20\163\x75\x62\163\151\164\x65\163\40\141\x6c\x6c\157\x77\145\144\x3a\40" . $Iq);
        $this->spUtility->setStoreConfig(SPConstants::WEBSITES_LIMIT, AESEncryption::encrypt_data($Iq, SPConstants::DEFAULT_TOKEN_VALUE));
        switch ($bs["\x73\x74\141\164\x75\x73"]) {
            case "\123\125\x43\103\105\x53\x53":
                $this->_vlk_success($m8, $XF);
                goto lf;
            default:
                $this->_vlk_fail();
                goto lf;
        }
        iC:
        lf:
    }
    public function _vlk_success($wI, $XF)
    {
        $Qz = json_decode((string) $this->spUtility->vml(trim($wI)), true);
        if (!(!is_array($Qz) || empty($Qz["\x73\x74\x61\x74\165\x73"]) || empty($Qz["\163\164\141\164\165\163"]))) {
            goto f0;
        }
        $this->messageManager->addErrorMessage(SPMessages::ENTERED_INVALID_KEY);
        return;
        f0:
        if (strcasecmp($Qz["\163\x74\141\164\165\163"], "\123\x55\103\x43\105\x53\123") == 0) {
            goto Vz;
        }
        if (strcasecmp($Qz["\x73\164\141\164\x75\163"], "\x46\101\111\x4c\x45\104") == 0) {
            goto gr;
        }
        $this->messageManager->addErrorMessage(SPMessages::ERROR_OCCURRED);
        goto uV;
        gr:
        if (strcasecmp($Qz["\x6d\x65\x73\163\141\147\145"], "\103\x6f\144\x65\x20\x68\x61\x73\40\x45\170\x70\151\x72\145\x64") == 0) {
            goto h7;
        }
        $this->messageManager->addErrorMessage(SPMessages::ENTERED_INVALID_KEY);
        goto ND;
        h7:
        $this->messageManager->addErrorMessage(SPMessages::LICENSE_KEY_IN_USE);
        ND:
        uV:
        goto c0;
        Vz:
        $On = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, AESEncryption::encrypt_data($wI, $On));
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, AESEncryption::encrypt_data("\x74\x72\x75\145", $On));
        $this->spUtility->setStoreConfig(SPConstants::LICENSE_EXPIRY_DATE, AESEncryption::encrypt_data($XF, $On));
        $this->messageManager->addSuccessMessage(SPMessages::LICENSE_VERIFIED);
        c0:
    }
    public function _vlk_fail()
    {
        $On = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, AESEncryption::encrypt_data("\x66\x61\x6c\163\145", $On));
        $this->messageManager->addErrorMessage(SPMessages::NOT_UPGRADED_YET);
    }
}
