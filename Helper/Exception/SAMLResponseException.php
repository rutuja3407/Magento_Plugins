<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class SAMLResponseException extends \Exception
{
    private $samlResponse;
    private $isCertError;
    public function __construct($qx, $wI, $xa, $Ic)
    {
        $this->xml = $xa;
        $this->isCertError = $Ic;
        parent::__construct($qx, $wI, NULL);
    }
    public function getSamlResponse()
    {
        return SPMessages::parse("\123\101\x4d\114\x5f\122\x45\123\120\x4f\116\123\x45", array("\x78\x6d\154" => $this->parseXML($this->xml)));
    }
    public static function parseXML($xa)
    {
        $uE = new \DOMDocument();
        $uE->preserveWhiteSpace = TRUE;
        $uE->formatOutput = TRUE;
        $uE->loadXML($xa->ownerDocument->saveXML($xa));
        return htmlentities($uE->saveXml());
    }
    public function isCertError()
    {
        return $this->isCertError;
    }
}
