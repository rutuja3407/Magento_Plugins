<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class MissingAttributesException extends \Exception
{
    public function __construct()
    {
        $qx = SPMessages::parse("\115\111\x53\123\111\116\x47\137\101\x54\124\x52\x49\x42\x55\x54\x45\x53\137\105\x58\103\105\x50\x54\111\x4f\116");
        $wI = 125;
        parent::__construct($qx, $wI, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\x5b{$this->code}\x5d\72\x20{$this->message}\xa";
    }
}
