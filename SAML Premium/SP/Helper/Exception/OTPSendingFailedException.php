<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class OTPSendingFailedException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x45\122\x52\117\122\x5f\123\x45\116\104\x49\116\x47\x5f\x4f\124\120");
        $qk = 115;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\x5b{$this->code}\135\x3a\40{$this->message}\xa";
    }
}
