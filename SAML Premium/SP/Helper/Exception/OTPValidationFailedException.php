<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class OTPValidationFailedException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x49\116\x56\x41\x4c\111\x44\137\117\124\120");
        $qk = 114;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\135\72\40{$this->message}\12";
    }
}
