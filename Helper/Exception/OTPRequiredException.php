<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class OTPRequiredException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\122\105\121\125\x49\x52\x45\104\x5f\x4f\124\x50");
        $wI = 113;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\x5b{$this->code}\x5d\72\40{$this->message}\12";
    }
}
