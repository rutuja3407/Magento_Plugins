<?php


namespace MiniOrange\SP\Helper\Saml2;

class LogoutResponse
{
    private $xml;
    private $id;
    private $version;
    private $destination;
    private $inResponseTo;
    private $issuer;
    private $status;
    private $bindingType;
    public function __construct($tx, $wQ, $Uc, $H2)
    {
        $this->xml = new \DOMDocument("\61\56\60", "\x75\164\x66\x2d\x38");
        $this->issuer = $wQ;
        $this->destination = $Uc;
        $this->inResponseTo = $tx;
        $this->bindingType = $H2;
    }
    public function build()
    {
        $S5 = $this->generateResponse();
        if (!(empty($this->bindingType) || $this->bindingType == SPConstants::HTTP_REDIRECT)) {
            goto sT;
        }
        $a1 = gzdeflate($S5);
        $CN = base64_encode($a1);
        $AB = urlencode($CN);
        $S5 = $AB;
        sT:
        return $S5;
    }
    private function generateResponse()
    {
        $QW = $this->createLogoutResponseElement();
        $this->xml->appendChild($QW);
        $wQ = $this->buildIssuer();
        $QW->appendChild($wQ);
        $kj = $this->buildStatus();
        $QW->appendChild($kj);
        $rK = $this->xml->saveXML();
        return $rK;
    }
    protected function createLogoutResponseElement()
    {
        $QW = $this->xml->createElementNS("\x75\x72\x6e\72\157\x61\163\151\163\x3a\156\141\x6d\x65\163\x3a\164\x63\72\123\x41\x4d\x4c\x3a\x32\x2e\x30\x3a\160\x72\157\x74\x6f\143\157\x6c", "\x73\x61\x6d\x6c\x70\x3a\x4c\x6f\x67\157\165\164\x52\x65\163\x70\x6f\156\163\145");
        $QW->setAttribute("\x49\x44", $this->generateUniqueID(40));
        $QW->setAttribute("\x56\x65\x72\163\x69\157\156", "\x32\56\60");
        $QW->setAttribute("\111\x73\163\x75\145\111\x6e\x73\x74\141\x6e\164", str_replace("\x2b\x30\x30\x3a\x30\x30", "\132", gmdate("\x63", time())));
        $QW->setAttribute("\104\145\x73\x74\151\156\141\164\x69\x6f\x6e", $this->destination);
        $QW->setAttribute("\x49\x6e\x52\145\163\160\157\156\x73\145\x54\x6f", $this->inResponseTo);
        return $QW;
    }
    protected function generateUniqueID($E4)
    {
        return SAML2Utilities::generateRandomAlphanumericValue($E4);
    }
    protected function buildIssuer()
    {
        return $this->xml->createElementNS("\165\162\156\x3a\157\x61\163\x69\x73\72\x6e\141\x6d\x65\x73\72\x74\x63\x3a\123\x41\115\x4c\72\x32\x2e\60\x3a\141\x73\163\145\162\x74\151\157\x6e", "\163\141\155\x6c\x3a\111\163\x73\x75\x65\x72", $this->issuer);
    }
    protected function buildStatus()
    {
        $s1 = $this->xml->createElementNS("\165\162\x6e\72\157\x61\163\151\163\72\x6e\x61\x6d\x65\x73\72\x74\143\x3a\123\x41\115\114\x3a\62\56\60\x3a\x70\x72\x6f\x74\157\143\157\x6c", "\x73\x61\x6d\154\x70\72\x53\164\x61\x74\x75\x73");
        $s1->appendChild($this->createStatusCode());
        return $s1;
    }
    protected function createStatusCode()
    {
        $pj = $this->xml->createElementNS("\x75\x72\156\72\157\x61\x73\x69\x73\x3a\x6e\141\x6d\x65\163\72\164\x63\72\123\x41\x4d\114\x3a\x32\56\60\72\x70\x72\x6f\x74\157\x63\x6f\154", "\x73\x61\155\x6c\160\72\x53\x74\141\164\x75\x73\x43\x6f\x64\145");
        $pj->setAttribute("\126\x61\154\165\145", "\165\x72\156\x3a\157\x61\163\151\163\72\156\141\155\145\x73\x3a\x74\x63\72\123\101\115\114\x3a\x32\x2e\60\72\163\164\x61\x74\x75\163\72\x53\165\143\143\x65\x73\x73");
        return $pj;
    }
}
