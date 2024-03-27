<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingAttributesException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x4d\x49\x53\x53\x49\x4e\x47\x5f\x41\124\124\122\x49\x42\x55\x54\105\123\x5f\105\x58\103\105\x50\x54\111\x4f\116");
        $qk = 125;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
