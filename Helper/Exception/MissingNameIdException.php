<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingNameIdException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x4d\x49\x53\123\111\116\x47\137\116\x41\x4d\105\111\104");
        $qk = 126;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\x3a\x20{$this->message}\12";
    }
}
