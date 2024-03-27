<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingNameIdException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\115\x49\x53\123\x49\116\107\x5f\x4e\x41\x4d\105\111\104");
        $wI = 126;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\135\x3a\40{$this->message}\xa";
    }
}
