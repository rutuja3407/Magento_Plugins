<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class NotRegisteredException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\116\117\124\137\122\105\x47\137\x45\122\x52\x4f\122");
        $wI = 102;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\x5b{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
