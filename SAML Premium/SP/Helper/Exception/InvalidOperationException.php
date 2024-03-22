<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidOperationException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\111\x4e\126\x41\x4c\x49\x44\137\x4f\x50");
        $qk = 105;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\x3a\40{$this->message}\xa";
    }
}
