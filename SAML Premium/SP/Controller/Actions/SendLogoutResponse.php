<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\LogoutResponse;
use MiniOrange\SP\Helper\SPConstants;
class SendLogoutResponse extends BaseAction
{
    private $isAdmin;
    private $userId;
    private $requestId;
    public function execute()
    {
        $this->checkIfValidPlugin();
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x6c\x6f\x67\157\165\x74\162\x65\161\x75\145\163\x74\x3a\40", $rQ);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\x64\x70\137\x6e\141\x6d\x65"] === $rQ)) {
                goto LG;
            }
            $ft = $fR->getData();
            LG:
            XW:
        }
        nx:
        if (!(!$this->spUtility->isSPConfigured() || !$ft["\x73\x61\x6d\x6c\137\154\157\147\157\x75\x74\137\165\x72\154"])) {
            goto oz;
        }
        return;
        oz:
        $XS = $this->spUtility->getLogoutUrl();
        $CR = $ft["\163\141\x6d\x6c\x5f\x6c\157\147\x6f\165\164\137\142\151\x6e\x64\151\156\147"];
        $p_ = $this->isAdmin ? $this->spUtility->getAdminBaseUrl() : $this->spUtility->getBaseUrl();
        $wz = $this->spUtility->getIssuerUrl();
        $TR = (new LogoutResponse($this->requestId, $wz, $XS, $CR))->build();
        if (empty($CR) || $CR == SPConstants::HTTP_REDIRECT) {
            goto lE;
        }
        $this->sendHTTPPostResponse($TR, $p_, $XS);
        goto cc;
        lE:
        return $this->sendHTTPRedirectResponse($TR, $p_, $XS);
        cc:
    }
    public function setRequestId($lA)
    {
        $this->requestId = $lA;
        return $this;
    }
}
