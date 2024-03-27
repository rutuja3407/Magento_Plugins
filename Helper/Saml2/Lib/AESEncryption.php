<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

class AESEncryption
{
    public static function encrypt_data($Ee, $q4)
    {
        $bs = '';
        $nO = 0;
        LD:
        if (!($nO < strlen($Ee))) {
            goto GI;
        }
        $qP = substr($Ee, $nO, 1);
        $qF = substr($q4, $nO % strlen($q4) - 1, 1);
        $qP = chr(ord($qP) + ord($qF));
        $bs .= $qP;
        Fz:
        $nO++;
        goto LD;
        GI:
        return base64_encode($bs);
    }
    public static function decrypt_data($Ee, $q4)
    {
        $bs = '';
        $Ee = base64_decode((string) $Ee);
        $nO = 0;
        GX:
        if (!($nO < strlen($Ee))) {
            goto TW;
        }
        $qP = substr($Ee, $nO, 1);
        $qF = substr($q4, $nO % strlen($q4) - 1, 1);
        $qP = chr(ord($qP) - ord($qF));
        $bs .= $qP;
        pl:
        $nO++;
        goto GX;
        TW:
        return $bs;
    }
}
