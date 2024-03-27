<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidIssuerException extends SAMLResponseException
{
    public function __construct($vY, $bd, $BY)
    {
        $by = SPMessages::parse("\111\116\126\x41\x4c\111\x44\x5f\111\x53\123\x55\x45\122", array("\x65\x78\x70\145\143\164" => $vY, "\146\x6f\x75\156\x64" => $bd));
        $qk = 101;
        parent::__construct($by, $qk, $BY, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
