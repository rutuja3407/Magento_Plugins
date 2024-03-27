<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidNumberOfNameIDsException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x49\116\126\x41\114\111\x44\x5f\x4e\x4f\137\117\x46\x5f\x4e\101\x4d\x45\111\104\123");
        $qk = 124;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\133{$this->code}\x5d\x3a\x20{$this->message}\xa";
    }
}
