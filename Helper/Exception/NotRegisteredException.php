<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class NotRegisteredException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x4e\117\x54\137\x52\x45\107\137\105\122\x52\x4f\x52");
        $qk = 102;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\133{$this->code}\135\72\40{$this->message}\xa";
    }
}
