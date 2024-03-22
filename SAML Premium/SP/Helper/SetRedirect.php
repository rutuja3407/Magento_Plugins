<?php


namespace MiniOrange\SP\Helper;

use DOMElement;
use DOMNode;
use DOMDocument;
use Exception;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecEnc;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityDSig;
class SetRedirect
{
    public function setRedirect($JY, $ZX = null, $Nv = null)
    {
        if (!($ZX !== null)) {
            goto PpN;
        }
        $this->message = $ZX;
        PpN:
        if (empty($Nv)) {
            goto fqh;
        }
        $this->messageType = $Nv;
        goto EZt;
        fqh:
        if (!empty($Vq->messageType)) {
            goto stS;
        }
        $this->messageType = "\x6d\145\163\163\x61\x67\x65";
        stS:
        EZt:
        return $Vq;
    }
}
