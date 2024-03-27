<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class PasswordStrengthException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\111\116\x56\101\114\x49\104\x5f\x50\101\x53\123\137\123\124\122\105\116\x47\x54\x48");
        $qk = 110;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\135\72\x20{$this->message}\12";
    }
}
