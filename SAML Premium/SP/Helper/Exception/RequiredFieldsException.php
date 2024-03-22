<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class RequiredFieldsException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x52\x45\x51\125\x49\x52\x45\104\137\x46\111\x45\x4c\104\123");
        $qk = 104;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
