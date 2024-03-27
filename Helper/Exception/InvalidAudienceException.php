<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidAudienceException extends SAMLResponseException
{
    public function __construct($vY, $bd, $BY)
    {
        $by = SPMessages::parse("\111\116\x56\x41\x4c\111\104\137\x41\125\x44\111\105\116\x43\x45", array("\x65\170\x70\x65\x63\164" => $vY, "\146\157\165\156\144" => $bd));
        $qk = 108;
        parent::__construct($by, $qk, $BY, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\135\72\x20{$this->message}\xa";
    }
}
