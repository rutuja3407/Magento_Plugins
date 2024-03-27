<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingIssuerValueException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\x4d\x49\x53\x53\111\x4e\107\x5f\111\123\123\x55\105\x52\x5f\x56\101\114\x55\105");
        $wI = 123;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\135\72\x20{$this->message}\xa";
    }
}
