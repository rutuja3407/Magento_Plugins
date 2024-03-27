<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidDestinationException extends SAMLResponseException
{
    public function __construct($XS, $K5, $BY)
    {
        $by = SPMessages::parse("\x49\x4e\x56\x41\114\111\x44\137\x44\x45\x53\x54\111\x4e\101\x54\x49\117\116", array("\x64\145\163\164\151\x6e\x61\164\151\157\x6e" => $XS, "\x63\x75\162\162\x65\156\x74\x75\162\154" => $K5));
        $qk = 108;
        parent::__construct($by, $qk, $BY, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\x20\133{$this->code}\x5d\72\x20{$this->message}\12";
    }
}
