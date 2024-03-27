<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class RequiredFieldsException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\122\105\x51\125\x49\122\x45\104\x5f\106\111\105\114\104\123");
        $wI = 104;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
