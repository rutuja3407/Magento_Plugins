<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class OTPValidationFailedException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\111\x4e\x56\101\x4c\x49\x44\137\117\124\x50");
        $wI = 114;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\133{$this->code}\x5d\72\x20{$this->message}\xa";
    }
}
