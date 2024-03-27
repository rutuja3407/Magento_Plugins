<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class AccountAlreadyExistsException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\x41\103\103\x4f\125\116\x54\137\105\x58\111\x53\124\123");
        $wI = 108;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\135\x3a\x20{$this->message}\xa";
    }
}
