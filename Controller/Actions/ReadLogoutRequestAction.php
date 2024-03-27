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
        $KJ = $this->REQUEST["\123\101\x4d\114\x52\x65\161\x75\145\163\x74"];
        $qY = !empty($this->REQUEST["\x52\x65\x6c\141\171\123\164\141\164\x65"]) ? $this->REQUEST["\122\145\x6c\141\x79\123\x74\141\x74\x65"] : '';
        $KJ = base64_decode((string) $KJ);
        if (!empty($this->POST["\x53\101\115\x4c\122\145\161\x75\x65\163\164"])) {
            goto fi;
        }
        $KJ = gzinflate($KJ);
        fi:
        $L5 = new \DOMDocument();
        $L5->loadXML($KJ);
        $v8 = $L5->firstChild;
        if (!($v8->localName == "\x4c\x6f\x67\x6f\165\x74\x52\x65\161\165\145\x73\x74")) {
            goto MF;
        }
        $Xd = new LogoutRequest($v8);
        return $this->logoutUser($Xd, $qY);
        MF:
    }
    private function logoutUser($Xd, $qY)
    {
        $this->spUtility->setSessionData(SPConstants::SEND_RESPONSE, TRUE);
        $this->spUtility->setAdminSessionData(SPConstants::SEND_RESPONSE, TRUE);
        $this->spUtility->setSessionData(SPConstants::LOGOUT_REQUEST_ID, $Xd->getId());
        $this->spUtility->setAdminSessionData(SPConstants::LOGOUT_REQUEST_ID, $Xd->getId());
        return $this->resultRedirectFactory->create()->setUrl($this->spUtility->getLogoutUrl());
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }
}
