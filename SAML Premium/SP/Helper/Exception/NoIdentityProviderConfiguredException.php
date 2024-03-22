<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class NoIdentityProviderConfiguredException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x4e\x4f\x5f\111\104\120\137\x43\x4f\116\x46\111\x47");
        $qk = 101;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\x3a\40{$this->message}\xa";
    }
}
