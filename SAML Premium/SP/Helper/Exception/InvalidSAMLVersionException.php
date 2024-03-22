<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidSAMLVersionException extends SAMLResponseException
{
    public function __construct($BY)
    {
        $by = SPMessages::parse("\111\116\x56\101\114\x49\x44\137\x53\x41\115\x4c\x5f\x56\105\x52\x53\x49\117\116");
        $qk = 118;
        parent::__construct($by, $qk, $BY, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\x5b{$this->code}\135\x3a\x20{$this->message}\12";
    }
}
