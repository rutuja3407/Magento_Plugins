<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidDestinationException extends SAMLResponseException
{
    public function __construct($Uc, $X0, $xa)
    {
        $qx = SPMessages::parse("\x49\116\126\101\x4c\x49\104\137\x44\105\x53\124\111\x4e\101\x54\111\x4f\x4e", array("\144\x65\163\x74\151\x6e\x61\x74\x69\157\156" => $Uc, "\x63\165\x72\x72\145\156\164\x75\162\x6c" => $X0));
        $wI = 108;
        parent::__construct($qx, $wI, $xa, FALSE);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\x5b{$this->code}\x5d\72\40{$this->message}\12";
    }
}
