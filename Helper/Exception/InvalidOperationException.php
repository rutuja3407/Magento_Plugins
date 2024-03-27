<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidOperationException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\x49\x4e\x56\x41\x4c\111\x44\x5f\x4f\x50");
        $wI = 105;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
