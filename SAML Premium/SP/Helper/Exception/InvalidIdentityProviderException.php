<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidIdentityProviderException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x49\116\126\x41\114\x49\104\x5f\x49\x44\120");
        $qk = 119;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\135\x3a\40{$this->message}\12";
    }
}
