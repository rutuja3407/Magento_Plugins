<?php


namespace MiniOrange\SP\Helper;

class SPZendUtility
{
    public static function base64Decode($Yk)
    {
        $Xk = base64_decode((string) $Yk);
        return $Xk;
    }
    public static function gzDeflate($Ge)
    {
        $Ob = ["\x6c\145\166\x65\x6c" => 9, "\155\157\144\x65" => "\144\145\146\154\x61\164\145", "\x61\162\x63\150\x69\166\145" => null];
        $lc = new \Zend_Filter_Compress_Gz($Ob);
        return $lc->compress($Ge);
    }
    public static function gzInflate($Ge)
    {
        $Ob = ["\x6c\145\166\145\154" => 9, "\155\157\144\145" => "\x64\x65\x66\154\x61\x74\x65", "\x61\x72\143\150\151\x76\x65" => null];
        $lc = new \Zend_Filter_Compress_Gz($Ob);
        return $lc->decompress($Ge);
    }
    public static function getRandomASCIIString($v1)
    {
        $B8 = ["\357\xbf\xbd", "\40", "\41", "\x22", "\43", "\44", "\x25", "\x26", "\x27", "\x28", "\x29", "\x2a", "\x2b", "\x2c", "\x2d", "\56", "\x2f", "\60", "\x31", "\x32", "\x33", "\64", "\x35", "\66", "\x37", "\70", "\71", "\72", "\x3b", "\74", "\x3d", "\76", "\x3f", "\100", "\x41", "\102", "\103", "\x44", "\x45", "\x46", "\107", "\x48", "\111", "\x4a", "\113", "\x4c", "\x4d", "\116", "\x4f", "\120", "\x51", "\122", "\x53", "\124", "\x55", "\126", "\127", "\130", "\131", "\x5a", "\133", "\134", "\135", "\136", "\137", "\140", "\141", "\142", "\x63", "\x64", "\x65", "\x66", "\x67", "\x68", "\151", "\152", "\153", "\x6c", "\x6d", "\x6e", "\x6f", "\x70", "\x71", "\162", "\x73", "\x74", "\x75", "\166", "\167", "\x78", "\171", "\172", "\173", "\174", "\175", "\176", "\x7f"];
        $Y3 = sizeof($B8);
        if (!($v1 < 0)) {
            goto jEc;
        }
        $v1 += $Y3 - 1;
        jEc:
        $v1 %= $Y3;
        return $B8[$v1];
    }
    public static function encryptMcrypt($Yk)
    {
        $WZ = new \Zend_Filter_Encrypt_Mcrypt(["\x63\157\x6d\160\162\145\163\x73\x69\x6f\156" => '']);
        return $WZ->encrypt($Yk);
    }
    public static function decryptMcrypt($Yk)
    {
        $WZ = new \Zend_Filter_Encrypt_Mcrypt(["\x63\x6f\155\160\x72\145\163\163\151\157\156" => '']);
        return $WZ->decrypt($Yk);
    }
}
