<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class PasswordResetFailedException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\105\122\122\x4f\x52\137\x4f\103\103\x55\x52\x52\x45\104");
        $qk = 116;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\135\x3a\x20{$this->message}\12";
    }
}
