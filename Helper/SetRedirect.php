<?php


namespace MiniOrange\SP\Helper;

class SetRedirect
{
    public function setRedirect($At, $Fx = null, $qI = null)
    {
        if (!($Fx !== null)) {
            goto wv;
        }
        $this->message = $Fx;
        wv:
        if (empty($qI)) {
            goto WQ;
        }
        $this->messageType = $qI;
        goto tr;
        WQ:
        if (!empty($GQ->messageType)) {
            goto Bt;
        }
        $this->messageType = "\155\x65\x73\x73\141\x67\145";
        Bt:
        tr:
        return $GQ;
    }
}
