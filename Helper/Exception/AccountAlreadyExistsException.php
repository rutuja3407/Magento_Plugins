<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class AccountAlreadyExistsException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x41\x43\103\117\x55\116\x54\x5f\105\130\111\123\124\x53");
        $qk = 108;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\40\133{$this->code}\135\72\x20{$this->message}\xa";
    }
}
