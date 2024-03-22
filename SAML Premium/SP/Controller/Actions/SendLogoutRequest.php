<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\LogoutRequest;
use MiniOrange\SP\Helper\SPConstants;
class SendLogoutRequest extends BaseAction
{
    private $isAdmin;
    private $userId;
    private $nameId;
    private $sessionIndex;
    protected $relay;
    public function execute()
    {
        $this->spUtility->log_debug("\x49\156\x20\x53\x65\156\144\x4c\157\147\157\x75\164\122\145\x71\x75\x65\x73\x74\x2e\160\x68\160");
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        if ($rQ) {
            goto yI;
        }
        $rQ = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
        yI:
        $this->spUtility->log_debug("\x6c\157\x67\157\165\x74\162\x65\x71\165\145\x73\x74\x3a\40", $rQ);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\x69\144\x70\x5f\x6e\141\x6d\145"] === $rQ)) {
                goto qe;
            }
            $ft = $fR->getData();
            qe:
            Un:
        }
        ls:
        if (!(!$this->spUtility->isSPConfigured() || !$ft["\x73\141\x6d\154\x5f\154\x6f\147\157\165\x74\137\165\x72\x6c"])) {
            goto gZ;
        }
        return;
        gZ:
        $XS = $ft["\163\141\x6d\154\137\x6c\157\147\x6f\x75\164\x5f\165\162\154"];
        $CR = $ft["\x73\141\x6d\x6c\x5f\154\x6f\147\x6f\165\x74\x5f\142\151\156\x64\x69\156\147"];
        $Q5 = $this->nameId;
        $SK = $this->isAdmin ? $this->spUtility->getAdminStoreConfig(SPConstants::SESSION_INDEX, $this->userId) : $this->spUtility->getCustomerStoreConfig(SPConstants::SESSION_INDEX, $this->userId);
        $p_ = $this->isAdmin ? $this->relay : $this->spUtility->getBaseUrl();
        $wz = $this->spUtility->getIssuerUrl();
        $this->spUtility->saveConfig(SPConstants::NAME_ID, '', $this->userId, $this->isAdmin);
        $this->spUtility->saveConfig(SPConstants::SESSION_INDEX, '', $this->userId, $this->isAdmin);
        $this->spUtility->log_debug("\111\x6e\40\123\x65\156\144\114\x6f\147\157\165\164\122\x65\161\165\x65\x73\x74\72\40\142\x65\146\157\162\145\40\154\x6f\147\x6f\165\164\122\145\161\x75\x65\163\x74\x3a\x20");
        $Xd = (new LogoutRequest($this->spUtility))->setIssuer($wz)->setDestination($XS)->setNameId($Q5)->setSessionIndexes($SK)->setBindingType($CR)->build();
        $this->spUtility->log_debug("\111\x6e\x20\123\145\x6e\144\x4c\x6f\147\x6f\x75\164\122\x65\x71\165\x65\x73\164\72\x20\154\157\x67\157\165\x74\122\x65\x71\x75\x65\163\164\x3a\40", $Xd);
        if (empty($CR) || $CR == SPConstants::HTTP_REDIRECT) {
            goto Fr;
        }
        $this->spUtility->log_debug("\111\156\x20\x53\145\156\144\x4c\x6f\x67\x6f\165\164\x52\145\161\165\x65\163\164\72\40\154\157\x67\x6f\165\164\x52\x65\x71\x75\x65\163\x74\72\x20\x50\117\123\x54\x5f\x52\105\104\111\122\105\x43\124");
        $this->sendHTTPPostRequest($Xd, $p_, $XS);
        goto IZ;
        Fr:
        $this->spUtility->log_debug("\111\x6e\x20\x53\x65\156\144\x4c\157\147\x6f\x75\164\x52\x65\161\165\x65\x73\x74\72\x20\x6c\x6f\x67\157\x75\164\122\x65\161\165\145\x73\164\x3a\x20\x48\x54\124\120\137\x52\x45\x44\x49\x52\105\x43\x54");
        return $this->sendHTTPRedirectRequest($Xd, $p_, $XS);
        IZ:
    }
    public function setIsAdmin($tZ)
    {
        $this->isAdmin = $tZ;
        return $this;
    }
    public function setUserId($l0)
    {
        $this->userId = $l0;
        return $this;
    }
    public function setNameId($Q5)
    {
        $this->nameId = $Q5;
        return $this;
    }
    public function setrelay($xF)
    {
        $this->relay = $xF;
        return $this;
    }
}
