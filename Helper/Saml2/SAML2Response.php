<?php


namespace MiniOrange\SP\Helper\Saml2;

use DOMElement;
use MiniOrange\SP\Helper\SPUtility;
class SAML2Response
{
    public $ownerDocument;
    private $assertions;
    private $destination;
    private $certificates;
    private $signatureData;
    private $spUtility;
    private $statusCode;
    private $xml;
    private $assertionNotBefore;
    private $assertionNotOnOrAfter;
    public function __construct(\DOMElement $xa = NULL, SPUtility $fR)
    {
        $this->assertions = array();
        $this->certificates = array();
        $this->spUtility = $fR;
        if (!($xa === NULL)) {
            goto hA;
        }
        return;
        hA:
        $Ud = SAML2Utilities::validateElement($xa);
        if (!($Ud !== FALSE)) {
            goto VE;
        }
        $this->certificates = $Ud["\103\x65\162\x74\151\146\x69\x63\x61\x74\145\x73"];
        $this->signatureData = $Ud;
        VE:
        if (!$xa->hasAttribute("\104\145\163\x74\151\156\x61\164\151\x6f\x6e")) {
            goto CT;
        }
        $this->destination = $xa->getAttribute("\x44\x65\x73\164\151\x6e\x61\164\151\x6f\156");
        CT:
        $zN = $xa->firstChild;
        Nh:
        if (!($zN !== NULL)) {
            goto fq;
        }
        if (!($zN->namespaceURI !== "\x75\162\x6e\x3a\x6f\x61\163\151\163\x3a\x6e\141\x6d\x65\x73\72\164\143\x3a\123\x41\x4d\x4c\72\x32\56\x30\72\141\x73\x73\x65\x72\164\x69\157\156")) {
            goto pD;
        }
        goto Fh;
        pD:
        if (!($zN->localName === "\x41\x73\163\145\x72\x74\x69\x6f\x6e" || $zN->localName === "\x45\x6e\143\x72\171\x70\164\145\x64\x41\163\x73\x65\162\164\151\x6f\x6e")) {
            goto Rx;
        }
        $this->assertions[] = new SAML2Assertion($zN, $this->spUtility);
        $this->assertionNotOnOrAfter = current($this->assertions)->getNotOnOrAfter();
        $this->assertionNotBefore = current($this->assertions)->getNotBefore();
        Rx:
        Fh:
        $zN = $zN->nextSibling;
        goto Nh;
        fq:
    }
    public function getAssertions()
    {
        return $this->assertions;
    }
    public function setAssertions(array $tE)
    {
        $this->assertions = $tE;
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
