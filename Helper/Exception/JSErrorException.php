<?php


namespace MiniOrange\SP\Helper\Exception;

class JSErrorException extends \Exception
{
    public function __construct($qx)
    {
        $qx = $qx;
        $wI = 103;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\135\72\40{$this->message}\xa";
    }
}
