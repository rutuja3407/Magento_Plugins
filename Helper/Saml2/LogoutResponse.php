<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
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
    public function __construct($gH, $wz, $XS, $CR)
    {
        $this->xml = new \DOMDocument("\x31\x2e\x30", "\165\164\146\55\x38");
        $this->issuer = $wz;
        $this->destination = $XS;
        $this->inResponseTo = $gH;
        $this->bindingType = $CR;
    }
    private function generateResponse()
    {
        $nw = $this->createLogoutResponseElement();
        $this->xml->appendChild($nw);
        $wz = $this->buildIssuer();
        $nw->appendChild($wz);
        $RK = $this->buildStatus();
        $nw->appendChild($RK);
        $n0 = $this->xml->saveXML();
        return $n0;
    }
    public function build()
    {
        $Yb = $this->generateResponse();
        if (!(empty($this->bindingType) || $this->bindingType == SPConstants::HTTP_REDIRECT)) {
            goto Cl5;
        }
        $zy = gzdeflate($Yb);
        $k1 = base64_encode($zy);
        $Og = urlencode($k1);
        $Yb = $Og;
        Cl5:
        return $Yb;
    }
    protected function createLogoutResponseElement()
    {
        $nw = $this->xml->createElementNS("\165\162\x6e\72\x6f\141\x73\x69\163\x3a\x6e\141\x6d\145\x73\72\164\x63\72\123\101\x4d\x4c\x3a\x32\56\60\x3a\160\x72\157\164\x6f\143\x6f\154", "\x73\141\x6d\154\160\x3a\114\157\x67\x6f\x75\164\x52\145\163\x70\157\x6e\163\145");
        $nw->setAttribute("\111\104", $this->generateUniqueID(40));
        $nw->setAttribute("\x56\x65\x72\163\x69\x6f\156", "\62\56\60");
        $nw->setAttribute("\111\163\x73\x75\145\111\x6e\163\x74\x61\156\164", str_replace("\53\x30\x30\x3a\x30\60", "\x5a", gmdate("\143", time())));
        $nw->setAttribute("\104\x65\x73\x74\x69\x6e\x61\x74\x69\157\156", $this->destination);
        $nw->setAttribute("\x49\x6e\122\x65\163\160\157\x6e\163\145\x54\x6f", $this->inResponseTo);
        return $nw;
    }
    protected function buildIssuer()
    {
        return $this->xml->createElementNS("\165\162\156\x3a\157\x61\163\151\x73\72\x6e\x61\155\145\x73\x3a\x74\143\72\x53\101\115\x4c\x3a\x32\56\x30\72\x61\x73\163\145\162\164\x69\157\156", "\x73\x61\155\154\72\111\163\163\x75\145\x72", $this->issuer);
    }
    protected function buildStatus()
    {
        $cG = $this->xml->createElementNS("\x75\162\x6e\x3a\157\141\163\x69\x73\72\x6e\141\x6d\x65\163\72\164\x63\72\x53\101\x4d\114\72\x32\56\60\x3a\160\162\x6f\164\157\143\157\154", "\x73\141\x6d\154\160\x3a\x53\164\141\x74\x75\x73");
        $cG->appendChild($this->createStatusCode());
        return $cG;
    }
    protected function createStatusCode()
    {
        $AY = $this->xml->createElementNS("\x75\x72\156\x3a\x6f\141\x73\151\x73\72\x6e\141\x6d\145\x73\72\x74\x63\x3a\123\x41\115\x4c\x3a\62\56\x30\72\160\162\157\164\157\x63\x6f\x6c", "\x73\x61\155\x6c\160\72\123\164\141\164\x75\163\103\x6f\x64\x65");
        $AY->setAttribute("\x56\x61\154\x75\x65", "\165\x72\x6e\72\157\x61\163\x69\x73\x3a\x6e\x61\155\x65\x73\x3a\164\143\72\123\x41\115\x4c\x3a\62\x2e\x30\72\163\x74\x61\164\165\163\72\123\x75\143\x63\x65\163\x73");
        return $AY;
    }
    protected function generateUniqueID($vS)
    {
        return SAML2Utilities::generateRandomAlphanumericValue($vS);
    }
}
