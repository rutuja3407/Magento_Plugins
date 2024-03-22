<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class RegistrationRequiredFieldsException extends \Exception
{
    public function __construct()
    {
        $by = SPMessages::parse("\122\105\121\x55\x49\122\x45\104\137\x52\105\x47\111\123\x54\x52\101\124\111\117\116\137\106\111\105\114\x44\123");
        $qk = 111;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\135\72\x20{$this->message}\xa";
    }
}
