<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidSAMLInstantException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\111\x4e\126\101\x4c\x49\104\137\x49\x4e\123\x54\101\116\124");
        $wI = 117;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\135\72\x20{$this->message}\xa";
    }
}
