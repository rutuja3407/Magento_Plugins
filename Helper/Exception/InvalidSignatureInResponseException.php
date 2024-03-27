<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\SPUtility;
class InvalidSignatureInResponseException extends SAMLResponseException
{
    private $pluginCert;
    private $certInResponse;
    public function __construct($MX, $pY, $BY)
    {
        $by = SPMessages::parse("\111\116\x56\x41\114\x49\104\x5f\122\x45\123\x50\x4f\116\x53\x45\x5f\x53\111\107\x4e\x41\x54\125\x52\105");
        $qk = 120;
        $this->pluginCert = $MX;
        $this->certInResponse = $pY;
        parent::__construct($by, $qk, $BY, TRUE);
    }
    public function __toString()
    {
        return __CLASS__ . "\x3a\x20\133{$this->code}\135\x3a\x20{$this->message}\xa";
    }
    public function getPluginCert()
    {
        return SPMessages::parse("\x46\x4f\122\x4d\101\x54\124\x45\x44\137\x43\x45\x52\124", array("\x63\x65\x72\x74" => $this->pluginCert));
    }
    public function getCertInResponse()
    {
        return SPMessages::parse("\x46\x4f\x52\x4d\101\124\x54\105\x44\137\x43\105\x52\x54", array("\x63\x65\x72\x74" => $this->certInResponse));
    }
}
