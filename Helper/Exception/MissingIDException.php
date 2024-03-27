<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingIDException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\115\111\123\x53\x49\116\107\x5f\111\104\137\106\122\117\115\x5f\x52\x45\123\x50\x4f\x4e\x53\105");
        $wI = 125;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\x5b{$this->code}\135\72\x20{$this->message}\12";
    }
}
