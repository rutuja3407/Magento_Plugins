<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidIdentityProviderException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\111\x4e\126\x41\x4c\x49\x44\x5f\x49\x44\x50");
        $wI = 119;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\135\x3a\x20{$this->message}\12";
    }
}
