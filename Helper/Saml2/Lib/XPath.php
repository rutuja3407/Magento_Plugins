<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

class XPath
{
    const ALPHANUMERIC = "\x5c\x77\134\144";
    const NUMERIC = "\x5c\144";
    const LETTERS = "\x5c\167";
    const EXTENDED_ALPHANUMERIC = "\x5c\x77\134\144\x5c\163\x5c\55\x5f\x3a\134\x2e";
    const SINGLE_QUOTE = "\47";
    const DOUBLE_QUOTE = "\x22";
    const ALL_QUOTES = "\x5b\x27\42\135";
    public static function filterAttrValue($VP, $HS = self::ALL_QUOTES)
    {
        return preg_replace("\x23" . $HS . "\43", '', $VP);
    }
    public static function filterAttrName($yt, $Vq = self::EXTENDED_ALPHANUMERIC)
    {
        return preg_replace("\43\133\136" . $Vq . "\x5d\43", '', $yt);
    }
}
