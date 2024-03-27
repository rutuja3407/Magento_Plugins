<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class SupportQueryRequiredFieldsException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x52\105\121\125\111\x52\105\104\x5f\121\125\x45\122\131\x5f\x46\x49\105\114\x44\x53");
        $qk = 109;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\x5d\72\x20{$this->message}\xa";
    }
}
