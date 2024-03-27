<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class PasswordMismatchException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\120\101\123\x53\x5f\115\x49\123\x4d\101\x54\103\x48");
        $wI = 122;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\x5d\72\40{$this->message}\12";
    }
}
