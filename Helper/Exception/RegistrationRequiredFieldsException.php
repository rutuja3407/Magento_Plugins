<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class RegistrationRequiredFieldsException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\122\x45\x51\125\x49\x52\105\x44\x5f\x52\105\107\x49\123\x54\122\101\x54\x49\117\116\x5f\106\111\x45\x4c\x44\x53");
        $wI = 111;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\133{$this->code}\135\x3a\40{$this->message}\xa";
    }
}
