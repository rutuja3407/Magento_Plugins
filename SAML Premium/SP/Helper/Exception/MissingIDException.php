<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingIDException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\x4d\111\x53\x53\111\x4e\x47\x5f\x49\104\137\x46\x52\117\x4d\137\x52\x45\123\120\x4f\116\x53\105");
        $qk = 125;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\x5d\x3a\x20{$this->message}\xa";
    }
}
