<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class NoIdentityProviderConfiguredException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\116\x4f\x5f\x49\x44\120\x5f\103\x4f\116\106\x49\x47");
        $wI = 101;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\135\x3a\x20{$this->message}\12";
    }
}
