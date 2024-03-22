<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\SAML2Assertion;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPUtility;
use DOMDocument;
use DOMElement;
class SAML2Response
{
    private $assertions;
    private $destination;
    private $certificates;
    private $signatureData;
    private $spUtility;
    private $statusCode;
    private $xml;
    public $ownerDocument;
    private $assertionNotBefore;
    private $assertionNotOnOrAfter;
    public function __construct(\DOMElement $BY = NULL, SPUtility $Kx)
    {
        $this->assertions = array();
        $this->certificates = array();
        $this->spUtility = $Kx;
        if (!($BY === NULL)) {
            goto KVr;
        }
        return;
        KVr:
        $GG = SAML2Utilities::validateElement($BY);
        if (!($GG !== FALSE)) {
            goto oIa;
        }
        $this->certificates = $GG["\x43\x65\162\164\151\146\151\143\141\x74\145\x73"];
        $this->signatureData = $GG;
        oIa:
        if (!$BY->hasAttribute("\x44\x65\163\164\151\156\x61\164\151\x6f\156")) {
            goto sDA;
        }
        $this->destination = $BY->getAttribute("\104\145\163\164\x69\x6e\141\x74\151\157\x6e");
        sDA:
        $w5 = $BY->firstChild;
        xg0:
        if (!($w5 !== NULL)) {
            goto uqW;
        }
        if (!($w5->namespaceURI !== "\x75\162\x6e\72\x6f\x61\163\x69\163\x3a\156\141\155\x65\163\72\164\143\72\x53\101\115\114\x3a\x32\56\60\x3a\141\x73\x73\x65\162\164\x69\157\156")) {
            goto xow;
        }
        goto zln;
        xow:
        if (!($w5->localName === "\101\x73\163\x65\x72\x74\x69\157\156" || $w5->localName === "\105\156\143\162\171\160\x74\145\144\x41\x73\x73\145\162\x74\x69\x6f\x6e")) {
            goto aGW;
        }
        $this->assertions[] = new SAML2Assertion($w5, $this->spUtility);
        $this->assertionNotOnOrAfter = current($this->assertions)->getNotOnOrAfter();
        $this->assertionNotBefore = current($this->assertions)->getNotBefore();
        aGW:
        zln:
        $w5 = $w5->nextSibling;
        goto xg0;
        uqW:
    }
    public function getAssertions()
    {
        return $this->assertions;
    }
    public function setAssertions(array $WM)
    {
        $this->assertions = $WM;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function getSignatureData()
    {
        return $this->signatureData;
    }
    public function getAssertionNotOnOrAfter()
    {
        return $this->assertionNotOnOrAfter;
    }
    public function getAssertionNotBefore()
    {
        return $this->assertionNotBefore;
    }
}
