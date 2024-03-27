<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidAudienceException extends SAMLResponseException
{
    public function __construct($vS, $iX, $xa)
    {
        $qx = SPMessages::parse("\x49\x4e\126\101\x4c\111\x44\137\101\x55\x44\111\x45\116\x43\x45", array("\x65\x78\160\x65\143\x74" => $vS, "\146\x6f\165\x6e\x64" => $iX));
        $wI = 108;
        parent::__construct($qx, $wI, $xa, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\x5b{$this->code}\135\72\40{$this->message}\xa";
    }
}
