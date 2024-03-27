<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidIssuerException extends SAMLResponseException
{
    public function __construct($vS, $iX, $xa)
    {
        $qx = SPMessages::parse("\x49\x4e\126\101\x4c\x49\104\137\111\x53\123\125\105\x52", array("\145\170\x70\145\143\164" => $vS, "\146\157\165\156\144" => $iX));
        $wI = 101;
        parent::__construct($qx, $wI, $xa, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\x5d\72\40{$this->message}\xa";
    }
}
