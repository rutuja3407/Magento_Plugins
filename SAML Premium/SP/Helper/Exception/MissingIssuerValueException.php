<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingIssuerValueException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\115\x49\x53\x53\x49\x4e\x47\x5f\111\x53\123\125\105\122\137\x56\x41\x4c\125\105");
        $qk = 123;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\135\72\40{$this->message}\12";
    }
}
