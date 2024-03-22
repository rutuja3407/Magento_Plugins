<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class PasswordMismatchException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x50\101\123\123\137\x4d\111\123\x4d\x41\124\103\x48");
        $qk = 122;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\72\x20{$this->message}\xa";
    }
}
