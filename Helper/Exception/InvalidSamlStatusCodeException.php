<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidSamlStatusCodeException extends SAMLResponseException
{
    public function __construct($pj, $xa)
    {
        $qx = SPMessages::parse("\111\116\x56\x41\114\111\x44\137\111\116\123\x54\x41\x4e\124", array("\163\164\141\x74\165\x73\143\157\144\x65" => $pj));
        $wI = 117;
        parent::__construct($qx, $wI, $xa, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\135\x3a\x20{$this->message}\xa";
    }
}
