<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidPhoneException extends \Exception
{
    public function __construct($zw)
    {
        $qx = SPMessages::parse("\x45\x52\122\x4f\122\x5f\120\x48\117\116\105\137\x46\x4f\x52\x4d\x41\x54", array("\160\x68\x6f\156\x65" => $zw));
        $wI = 112;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\x5b{$this->code}\x5d\72\x20{$this->message}\xa";
    }
}
