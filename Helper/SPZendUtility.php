<?php


namespace MiniOrange\SP\Helper;

class SPZendUtility
{
    public static function base64Decode($VP)
    {
        $FV = base64_decode((string) $VP);
        return $FV;
    }
    public static function gzDeflate($Qz)
    {
        $F3 = ["\x6c\145\x76\x65\x6c" => 9, "\x6d\x6f\144\x65" => "\144\145\x66\x6c\141\164\145", "\x61\162\143\150\151\x76\145" => null];
        $jb = new \Zend_Filter_Compress_Gz($F3);
        return $jb->compress($Qz);
    }
    public static function gzInflate($Qz)
    {
        $F3 = ["\x6c\145\x76\145\154" => 9, "\x6d\157\144\x65" => "\x64\145\x66\x6c\141\164\x65", "\141\x72\x63\x68\x69\166\x65" => null];
        $jb = new \Zend_Filter_Compress_Gz($F3);
        return $jb->decompress($Qz);
    }
    public static function getRandomASCIIString($Tj)
    {
        $L2 = ["\357\xbf\xbd", "\40", "\41", "\42", "\x23", "\x24", "\x25", "\x26", "\47", "\x28", "\x29", "\52", "\x2b", "\54", "\55", "\x2e", "\57", "\x30", "\61", "\x32", "\x33", "\64", "\x35", "\x36", "\67", "\x38", "\71", "\x3a", "\x3b", "\74", "\x3d", "\x3e", "\77", "\100", "\x41", "\102", "\103", "\x44", "\105", "\x46", "\x47", "\110", "\111", "\x4a", "\113", "\114", "\x4d", "\116", "\x4f", "\120", "\x51", "\122", "\123", "\124", "\x55", "\x56", "\x57", "\130", "\x59", "\132", "\x5b", "\x5c", "\135", "\x5e", "\x5f", "\x60", "\x61", "\x62", "\x63", "\144", "\x65", "\x66", "\147", "\150", "\151", "\x6a", "\x6b", "\x6c", "\155", "\156", "\157", "\x70", "\161", "\162", "\x73", "\164", "\x75", "\166", "\x77", "\170", "\171", "\x7a", "\173", "\174", "\175", "\x7e", "\177"];
        $LK = sizeof($L2);
        if (!($Tj < 0)) {
            goto aL;
        }
        $Tj += $LK - 1;
        aL:
        $Tj %= $LK;
        return $L2[$Tj];
    }
    public static function encryptMcrypt($VP)
    {
        $Zm = new \Zend_Filter_Encrypt_Mcrypt(["\143\x6f\155\160\x72\x65\163\163\151\157\156" => '']);
        return $Zm->encrypt($VP);
    }
    public static function decryptMcrypt($VP)
    {
        $Zm = new \Zend_Filter_Encrypt_Mcrypt(["\x63\157\155\160\162\145\x73\x73\x69\157\x6e" => '']);
        return $Zm->decrypt($VP);
    }
}
