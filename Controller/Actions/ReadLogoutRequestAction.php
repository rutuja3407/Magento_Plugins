<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\LogoutRequest;
use MiniOrange\SP\Helper\SPConstants;
class ReadLogoutRequestAction extends BaseAction
{
    private $REQUEST;
    private $POST;
    public function execute()
    {
        $this->checkIfValidPlugin();
        $ML = $this->REQUEST["\123\101\x4d\x4c\122\145\x71\165\145\163\x74"];
        $Nf = !empty($this->REQUEST["\122\145\154\x61\x79\123\164\x61\x74\145"]) ? $this->REQUEST["\122\x65\x6c\141\171\x53\x74\x61\164\x65"] : '';
        $ML = base64_decode((string) $ML);
        if (!empty($this->POST["\x53\x41\115\114\122\145\x71\x75\x65\163\164"])) {
            goto Dg;
        }
        $ML = gzinflate($ML);
        Dg:
        $rT = new \DOMDocument();
        $rT->loadXML($ML);
        $DJ = $rT->firstChild;
        if (!($DJ->localName == "\114\157\x67\157\165\x74\122\145\161\165\x65\163\164")) {
            goto gs;
        }
        $Uh = new LogoutRequest($DJ);
        return $this->logoutUser($Uh, $Nf);
        gs:
    }
    private function logoutUser($Uh, $Nf)
    {
        $this->spUtility->setSessionData(SPConstants::SEND_RESPONSE, TRUE);
        $this->spUtility->setAdminSessionData(SPConstants::SEND_RESPONSE, TRUE);
        $this->spUtility->setSessionData(SPConstants::LOGOUT_REQUEST_ID, $Uh->getId());
        $this->spUtility->setAdminSessionData(SPConstants::LOGOUT_REQUEST_ID, $Uh->getId());
        return $this->resultRedirectFactory->create()->setUrl($this->spUtility->getLogoutUrl());
    }
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }
}
