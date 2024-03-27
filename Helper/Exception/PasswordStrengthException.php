<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class PasswordStrengthException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\x49\x4e\126\x41\x4c\x49\x44\x5f\120\101\x53\x53\137\123\x54\x52\105\x4e\107\x54\x48");
        $wI = 110;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\133{$this->code}\135\72\40{$this->message}\12";
    }
}
