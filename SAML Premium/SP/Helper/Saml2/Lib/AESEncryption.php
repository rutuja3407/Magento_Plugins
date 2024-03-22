<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

class AESEncryption
{
    public static function encrypt_data($WV, $TV)
    {
        $Up = '';
        $X5 = 0;
        q4:
        if (!($X5 < strlen($WV))) {
            goto Hw;
        }
        $Pa = substr($WV, $X5, 1);
        $Ls = substr($TV, $X5 % strlen($TV) - 1, 1);
        $Pa = chr(ord($Pa) + ord($Ls));
        $Up .= $Pa;
        kD:
        $X5++;
        goto q4;
        Hw:
        return base64_encode($Up);
    }
    public static function decrypt_data($WV, $TV)
    {
        $Up = '';
        $WV = base64_decode((string) $WV);
        $X5 = 0;
        Gv:
        if (!($X5 < strlen($WV))) {
            goto st;
        }
        $Pa = substr($WV, $X5, 1);
        $Ls = substr($TV, $X5 % strlen($TV) - 1, 1);
        $Pa = chr(ord($Pa) - ord($Ls));
        $Up .= $Pa;
        JY:
        $X5++;
        goto Gv;
        st:
        return $Up;
    }
}
