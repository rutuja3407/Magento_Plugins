<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\LogoutRequest;
use MiniOrange\SP\Helper\SPConstants;
class SendLogoutRequest extends BaseAction
{
    protected $relay;
    private $isAdmin;
    private $userId;
    private $nameId;
    private $sessionIndex;
    public function execute()
    {
        $this->spUtility->log_debug("\x49\156\40\123\145\156\x64\114\x6f\x67\x6f\x75\x74\x52\145\x71\165\145\x73\164\56\x70\x68\x70");
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        if ($rq) {
            goto MZ;
        }
        $rq = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
        MZ:
        $this->spUtility->log_debug("\154\x6f\x67\x6f\165\164\162\x65\161\165\x65\x73\164\72\40", $rq);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\x64\x70\x5f\156\x61\x6d\145"] === $rq)) {
                goto Nl;
            }
            $hR = $ub->getData();
            Nl:
            qe:
        }
        LX:
        if (!(!$this->spUtility->isSPConfigured() || !$hR["\163\141\x6d\x6c\x5f\x6c\x6f\x67\157\x75\164\x5f\x75\x72\x6c"])) {
            goto gh;
        }
        return;
        gh:
        $Uc = $hR["\163\x61\x6d\154\137\154\157\147\157\165\164\x5f\x75\162\154"];
        $H2 = $hR["\x73\141\x6d\x6c\137\x6c\157\x67\x6f\x75\164\137\142\151\156\144\x69\156\147"];
        $Au = $this->nameId;
        $lr = $this->isAdmin ? $this->spUtility->getAdminStoreConfig(SPConstants::SESSION_INDEX, $this->userId) : $this->spUtility->getCustomerStoreConfig(SPConstants::SESSION_INDEX, $this->userId);
        $eA = $this->isAdmin ? $this->relay : $this->spUtility->getBaseUrl();
        $wQ = $this->spUtility->getIssuerUrl();
        $this->spUtility->saveConfig(SPConstants::NAME_ID, '', $this->userId, $this->isAdmin);
        $this->spUtility->saveConfig(SPConstants::SESSION_INDEX, '', $this->userId, $this->isAdmin);
        $this->spUtility->log_debug("\x49\156\40\x53\x65\156\x64\x4c\x6f\147\x6f\x75\x74\x52\145\x71\165\145\x73\x74\x3a\x20\x62\145\x66\157\162\145\40\x6c\x6f\147\x6f\165\164\122\x65\161\x75\145\163\164\x3a\40");
        $Uh = (new LogoutRequest($this->spUtility))->setIssuer($wQ)->setDestination($Uc)->setNameId($Au)->setSessionIndexes($lr)->setBindingType($H2)->build();
        $this->spUtility->log_debug("\111\156\40\x53\145\156\144\114\157\147\157\165\x74\x52\145\161\x75\145\163\x74\72\40\x6c\157\x67\157\x75\164\x52\145\x71\165\145\x73\x74\x3a\40", $Uh);
        if (empty($H2) || $H2 == SPConstants::HTTP_REDIRECT) {
            goto PU;
        }
        $this->spUtility->log_debug("\x49\156\x20\123\x65\156\x64\x4c\157\147\157\165\x74\122\145\161\x75\145\163\164\x3a\x20\x6c\x6f\x67\157\x75\x74\122\x65\x71\165\x65\x73\164\x3a\x20\x50\117\123\x54\137\122\105\104\x49\x52\x45\103\x54");
        $this->sendHTTPPostRequest($Uh, $eA, $Uc);
        goto p3;
        PU:
        $this->spUtility->log_debug("\x49\x6e\40\123\145\156\x64\x4c\x6f\x67\157\x75\164\122\x65\x71\165\145\x73\164\72\40\x6c\157\147\x6f\165\164\122\x65\161\165\x65\163\x74\72\x20\x48\x54\x54\x50\137\122\x45\104\x49\122\x45\103\x54");
        return $this->sendHTTPRedirectRequest($Uh, $eA, $Uc);
        p3:
    }
    public function setNameId($Au)
    {
        $this->nameId = $Au;
        return $this;
    }
    public function setIsAdmin($Sq)
    {
        $this->isAdmin = $Sq;
        return $this;
    }
    public function setUserId($U1)
    {
        $this->userId = $U1;
        return $this;
    }
    public function setrelay($uj)
    {
        $this->relay = $uj;
        return $this;
    }
}
