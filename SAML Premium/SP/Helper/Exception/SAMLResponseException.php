<?php


namespace MiniOrange\SP\Helper\Exception;

use MiniOrange\SP\Helper\SPMessages;
class SAMLResponseException extends \Exception
{
    private $samlResponse;
    private $isCertError;
    public function __construct($by, $qk, $BY, $XA)
    {
        $this->xml = $BY;
        $this->isCertError = $XA;
        parent::__construct($by, $qk, NULL);
    }
    public function getSamlResponse()
    {
        return SPMessages::parse("\x53\x41\x4d\114\137\x52\105\123\x50\117\x4e\x53\x45", array("\170\x6d\x6c" => $this->parseXML($this->xml)));
    }
    public function isCertError()
    {
        return $this->isCertError;
    }
    public static function parseXML($BY)
    {
        $mc = new \DOMDocument();
        $mc->preserveWhiteSpace = TRUE;
        $mc->formatOutput = TRUE;
        $mc->loadXML($BY->ownerDocument->saveXML($BY));
        return htmlentities($mc->saveXml());
    }
}
