<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class OTPSendingFailedException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\x45\122\x52\117\122\x5f\123\x45\x4e\x44\111\x4e\107\137\117\x54\120");
        $wI = 115;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\x5b{$this->code}\135\72\40{$this->message}\xa";
    }
}
