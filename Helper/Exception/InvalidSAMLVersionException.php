<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidSAMLVersionException extends SAMLResponseException
{
    public function __construct($xa)
    {
        $qx = SPMessages::parse("\x49\x4e\126\101\x4c\111\104\137\123\101\115\114\x5f\126\x45\x52\123\x49\117\x4e");
        $wI = 118;
        parent::__construct($qx, $wI, $xa, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\x5b{$this->code}\135\72\40{$this->message}\12";
    }
}
