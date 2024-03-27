<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class OTPRequiredException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\122\105\121\x55\x49\x52\105\104\x5f\117\x54\x50");
        $qk = 113;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
