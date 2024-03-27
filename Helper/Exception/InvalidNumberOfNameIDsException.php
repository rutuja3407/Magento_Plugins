<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidNumberOfNameIDsException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\111\116\x56\x41\x4c\x49\104\x5f\116\117\137\x4f\106\x5f\x4e\101\x4d\105\111\x44\123");
        $wI = 124;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\x5d\x3a\x20{$this->message}\xa";
    }
}
