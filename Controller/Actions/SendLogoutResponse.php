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
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x6c\x6f\147\x6f\165\x74\162\x65\x71\x75\x65\x73\x74\x3a\40", $rq);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\144\160\x5f\156\x61\x6d\145"] === $rq)) {
                goto yk;
            }
            $hR = $ub->getData();
            yk:
            Pq:
        }
        qi:
        if (!(!$this->spUtility->isSPConfigured() || !$hR["\x73\141\x6d\x6c\x5f\x6c\x6f\147\x6f\165\x74\x5f\x75\x72\154"])) {
            goto S5;
        }
        return;
        S5:
        $Uc = $this->spUtility->getLogoutUrl();
        $H2 = $hR["\x73\x61\x6d\x6c\137\x6c\157\x67\x6f\165\x74\137\x62\x69\156\x64\x69\156\147"];
        $eA = $this->isAdmin ? $this->spUtility->getAdminBaseUrl() : $this->spUtility->getBaseUrl();
        $wQ = $this->spUtility->getIssuerUrl();
        $kK = (new LogoutResponse($this->requestId, $wQ, $Uc, $H2))->build();
        if (empty($H2) || $H2 == SPConstants::HTTP_REDIRECT) {
            goto mZ;
        }
        $this->sendHTTPPostResponse($kK, $eA, $Uc);
        goto av;
        mZ:
        return $this->sendHTTPRedirectResponse($kK, $eA, $Uc);
        av:
    }
    public function setRequestId($Gh)
    {
        $this->requestId = $Gh;
        return $this;
    }
}
