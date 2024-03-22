<?php


namespace MiniOrange\SP\Helper\Exception;

class JSErrorException extends \Exception
{
    public function __construct($by)
    {
        $by = $by;
        $qk = 103;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\x5d\72\40{$this->message}\xa";
    }
}
