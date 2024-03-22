<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecEnc;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use DOMElement;
class XPath
{
    const ALPHANUMERIC = "\x5c\167\134\144";
    const NUMERIC = "\x5c\x64";
    const LETTERS = "\x5c\x77";
    const EXTENDED_ALPHANUMERIC = "\x5c\x77\x5c\x64\134\163\134\55\x5f\72\134\56";
    const SINGLE_QUOTE = "\x27";
    const DOUBLE_QUOTE = "\42";
    const ALL_QUOTES = "\x5b\x27\42\135";
    public static function filterAttrValue($Yk, $EU = self::ALL_QUOTES)
    {
        return preg_replace("\x23" . $EU . "\x23", '', $Yk);
    }
    public static function filterAttrName($rb, $M2 = self::EXTENDED_ALPHANUMERIC)
    {
        return preg_replace("\x23\133\136" . $M2 . "\135\x23", '', $rb);
    }
}
