<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class InvalidSignatureInResponseException extends SAMLResponseException
{
    private $pluginCert;
    private $certInResponse;
    public function __construct($Cg, $iC, $xa)
    {
        $qx = SPMessages::parse("\111\116\126\101\x4c\x49\x44\137\x52\105\123\x50\117\x4e\123\105\x5f\123\x49\x47\x4e\101\x54\125\122\x45");
        $wI = 120;
        $this->pluginCert = $Cg;
        $this->certInResponse = $iC;
        parent::__construct($qx, $wI, $xa, TRUE);
    }
    public function __toString()
    {
        return __CLASS__ . "\72\40\133{$this->code}\135\72\40{$this->message}\xa";
    }
    public function getPluginCert()
    {
        return SPMessages::parse("\x46\x4f\122\x4d\x41\x54\x54\105\104\x5f\103\x45\x52\124", array("\143\145\x72\x74" => $this->pluginCert));
    }
    public function getCertInResponse()
    {
        return SPMessages::parse("\106\x4f\x52\115\x41\x54\124\105\x44\x5f\103\x45\122\124", array("\x63\145\x72\x74" => $this->certInResponse));
    }
}
