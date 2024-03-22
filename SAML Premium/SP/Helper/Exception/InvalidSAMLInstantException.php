<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidSAMLInstantException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\111\116\x56\x41\x4c\x49\104\137\x49\x4e\123\x54\101\116\x54");
        $qk = 117;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\135\72\x20{$this->message}\12";
    }
}
