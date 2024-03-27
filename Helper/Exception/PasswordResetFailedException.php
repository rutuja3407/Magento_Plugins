<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class PasswordResetFailedException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\x45\x52\x52\x4f\122\x5f\117\x43\x43\125\x52\122\x45\104");
        $wI = 116;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\x5b{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
