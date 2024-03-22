<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\SPUtility;
class InvalidSamlStatusCodeException extends SAMLResponseException
{
    public function __construct($AY, $BY)
    {
        $by = SPMessages::parse("\x49\116\x56\101\114\x49\104\x5f\x49\116\123\x54\x41\x4e\124", array("\163\164\x61\x74\165\163\x63\x6f\144\x65" => $AY));
        $qk = 117;
        parent::__construct($by, $qk, $BY, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\x5d\x3a\40{$this->message}\12";
    }
}
