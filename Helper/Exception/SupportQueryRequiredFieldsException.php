<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class SupportQueryRequiredFieldsException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\x52\x45\x51\x55\x49\x52\105\104\137\121\x55\x45\122\x59\x5f\x46\x49\x45\x4c\104\123");
        $wI = 109;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\135\72\x20{$this->message}\12";
    }
}
