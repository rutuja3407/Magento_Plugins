<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidPhoneException extends \Exception
{
    public function __construct($Rs)
    {
        $by = SPMessages::parse("\x45\x52\122\x4f\122\x5f\120\x48\117\116\x45\137\106\117\122\x4d\101\124", array("\x70\x68\157\x6e\x65" => $Rs));
        $qk = 112;
        parent::__construct($by, $qk, NULL);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\x5b{$this->code}\135\x3a\x20{$this->message}\xa";
    }
}
