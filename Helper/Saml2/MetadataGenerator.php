<?php


namespace MiniOrange\SP\Helper\Saml2;

class MetadataGenerator
{
    private $xml;
    private $issuer;
    private $samlLoginURL;
    private $wantAssertionSigned;
    private $x509Certificate;
    private $nameIDFormats;
    private $singleSignOnServiceURLs;
    private $singleLogoutServiceURLs;
    private $acsUrl;
    private $authnRequestSigned;
    public function __construct($wQ, $Yo, $Oi, $E5, $y8, $tG, $Ct, $vl, $X9)
    {
        $this->xml = new \DOMDocument("\61\56\60", "\x75\x74\146\55\70");
        $this->xml->preserveWhiteSpace = FALSE;
        $this->xml->formatOutput = TRUE;
        $this->issuer = $wQ;
        $this->wantAssertionSigned = $Yo;
        $this->authnRequestSigned = $Oi;
        $this->x509Certificate = $E5;
        $this->nameIDFormats = array("\165\x72\156\x3a\x6f\141\x73\151\x73\72\x6e\141\x6d\x65\163\72\164\143\x3a\x53\x41\115\114\72\x31\x2e\61\72\x6e\x61\x6d\145\x69\x64\x2d\x66\x6f\x72\155\141\164\x3a\145\155\141\x69\154\x41\x64\144\x72\x65\x73\x73", "\165\162\x6e\72\x6f\141\x73\x69\163\x3a\156\141\x6d\145\x73\72\x74\x63\x3a\123\101\115\114\x3a\61\56\x31\x3a\x6e\x61\x6d\x65\151\x64\x2d\146\157\x72\x6d\141\164\x3a\165\x6e\x73\160\x65\x63\151\146\151\x65\144");
        $this->singleSignOnServiceURLs = array("\165\x72\x6e\72\x6f\141\163\x69\163\x3a\156\x61\x6d\145\163\72\x74\143\x3a\x53\101\115\x4c\x3a\62\x2e\60\x3a\142\151\x6e\144\x69\156\147\163\72\110\124\x54\x50\x2d\120\x4f\x53\124" => $y8, "\x75\162\x6e\x3a\x6f\x61\163\151\x73\x3a\x6e\141\x6d\145\163\x3a\164\x63\x3a\x53\x41\x4d\114\x3a\62\x2e\x30\x3a\142\151\x6e\144\x69\x6e\147\x73\72\110\x54\124\x50\x2d\122\145\x64\151\162\145\143\x74" => $tG);
        $this->singleLogoutServiceURLs = array("\165\x72\156\x3a\x6f\x61\163\x69\163\x3a\156\x61\155\x65\163\72\x74\x63\x3a\123\x41\x4d\x4c\x3a\x32\x2e\x30\x3a\142\x69\156\144\x69\156\147\x73\72\110\124\x54\x50\x2d\120\117\x53\124" => $Ct, "\x75\162\156\x3a\x6f\x61\x73\x69\x73\x3a\156\x61\155\x65\163\x3a\164\143\x3a\123\101\x4d\114\72\x32\x2e\60\72\142\x69\x6e\144\x69\x6e\x67\163\x3a\110\x54\x54\x50\x2d\x52\145\x64\151\162\145\x63\x74" => $vl);
        $this->acsUrl = $X9;
    }
    public function generateSPMetadata()
    {
        $wO = $this->createEntityDescriptorElement();
        $this->xml->appendChild($wO);
        $rk = $this->createSpDescriptorElement();
        $wO->appendChild($rk);
        $On = $this->createKeyDescriptorElement("\x73\x69\x67\x6e\151\x6e\x67");
        $rk->appendChild($On);
        $Jp = $this->createKeyDescriptorElement("\145\156\143\162\x79\x70\x74\151\157\156");
        $rk->appendChild($Jp);
        $OF = $this->createSLOUrls();
        foreach ($OF as $NN) {
            $rk->appendChild($NN);
            IY:
        }
        xL:
        $ve = $this->createNameIdFormatElements();
        foreach ($ve as $oi) {
            $rk->appendChild($oi);
            eP:
        }
        CE:
        $Rm = $this->createAcsUrlElement();
        $rk->appendChild($Rm);
        $BF = $this->xml->saveXML();
        return $BF;
    }
    private function createEntityDescriptorElement()
    {
        $wO = $this->xml->createElementNS("\165\x72\x6e\72\x6f\x61\x73\x69\x73\x3a\156\x61\155\145\163\72\x74\x63\x3a\123\x41\x4d\x4c\72\x32\56\60\x3a\x6d\x65\x74\x61\144\x61\164\x61", "\x45\156\164\x69\x74\171\x44\x65\163\x63\x72\151\x70\164\x6f\162");
        $wO->setAttribute("\145\156\x74\x69\x74\x79\111\104", $this->issuer);
        return $wO;
    }
    private function createSPDescriptorElement()
    {
        $Eo = $this->xml->createElementNS("\165\x72\x6e\72\x6f\141\163\x69\x73\72\x6e\x61\x6d\145\163\x3a\164\x63\72\x53\101\x4d\114\x3a\x32\56\60\x3a\x6d\145\164\141\144\141\164\x61", "\x53\120\123\123\x4f\104\x65\x73\x63\x72\x69\x70\164\157\x72");
        $Eo->setAttribute("\x57\141\x6e\x74\x41\x75\164\x68\x6e\122\x65\161\165\145\x73\164\x73\123\151\147\156\x65\144", $this->wantAssertionSigned);
        $Eo->setAttribute("\x41\165\x74\150\156\x52\x65\161\x75\x65\x73\x74\163\x53\x69\x67\x6e\145\x64", $this->authnRequestSigned);
        $Eo->setAttribute("\x70\x72\157\164\157\x63\157\154\123\x75\x70\160\157\x72\x74\x45\x6e\165\155\145\x72\x61\164\x69\x6f\x6e", "\x75\162\x6e\x3a\x6f\141\x73\151\x73\x3a\156\x61\155\145\x73\72\x74\143\x3a\123\101\115\x4c\x3a\62\x2e\x30\x3a\160\x72\157\164\157\x63\x6f\154");
        return $Eo;
    }
    private function createKeyDescriptorElement($um)
    {
        $On = $this->xml->createElement("\113\x65\x79\x44\145\163\x63\x72\x69\x70\x74\157\162");
        $On->setAttribute("\165\x73\145", $um);
        $He = $this->generateKeyInfo();
        $On->appendChild($He);
        return $On;
    }
    private function generateKeyInfo()
    {
        $He = $this->xml->createElementNS("\x68\x74\x74\160\72\x2f\57\x77\x77\167\x2e\x77\63\56\x6f\x72\x67\x2f\x32\x30\60\x30\57\60\71\57\x78\155\x6c\144\163\151\x67\43", "\x64\x73\72\x4b\145\171\x49\156\x66\x6f");
        $RR = $this->xml->createElementNS("\x68\x74\x74\160\x3a\x2f\x2f\x77\167\x77\56\167\63\56\157\x72\x67\57\x32\x30\x30\60\x2f\x30\71\x2f\x78\x6d\154\x64\163\151\147\x23", "\x64\x73\72\130\x35\60\71\104\141\x74\x61");
        $xJ = SAML2Utilities::desanitize_certificate($this->x509Certificate);
        $Aa = $this->xml->createElementNS("\x68\x74\x74\160\x3a\x2f\x2f\x77\x77\167\x2e\167\x33\56\157\x72\147\x2f\x32\x30\60\x30\57\x30\x39\x2f\x78\155\x6c\x64\x73\x69\147\x23", "\x64\x73\x3a\130\x35\60\71\103\145\x72\164\x69\146\x69\143\x61\x74\145", $xJ);
        $RR->appendChild($Aa);
        $He->appendChild($RR);
        return $He;
    }
    private function createSLOUrls()
    {
        $OF = array();
        foreach ($this->singleLogoutServiceURLs as $Mn => $At) {
            $NN = $this->xml->createElementNS("\x75\162\x6e\x3a\157\x61\x73\x69\x73\x3a\156\141\x6d\x65\163\72\164\x63\x3a\x53\101\115\x4c\x3a\x32\56\x30\72\155\145\x74\141\144\141\164\x61", "\x53\151\x6e\x67\154\145\x4c\157\147\x6f\x75\164\x53\x65\x72\166\x69\x63\145");
            $NN->setAttribute("\102\x69\156\144\151\x6e\x67", $Mn);
            $NN->setAttribute("\114\x6f\143\x61\164\151\x6f\x6e", $At);
            array_push($OF, $NN);
            NJ:
        }
        GJ:
        return $OF;
    }
    private function createNameIdFormatElements()
    {
        $ve = array();
        foreach ($this->nameIDFormats as $no) {
            array_push($ve, $this->xml->createElementNS("\x75\x72\x6e\x3a\157\x61\163\x69\x73\72\x6e\141\155\145\x73\72\164\143\72\x53\101\x4d\114\x3a\62\x2e\x30\x3a\x6d\x65\164\141\x64\141\x74\141", "\x4e\141\155\145\111\x44\106\157\x72\x6d\x61\164", $no));
            aD:
        }
        U3:
        return $ve;
    }
    private function createAcsUrlElement()
    {
        $le = $this->xml->createElementNS("\x75\x72\156\72\157\x61\x73\151\x73\x3a\156\x61\x6d\x65\x73\x3a\x74\143\x3a\x53\x41\x4d\x4c\72\62\x2e\x30\72\155\145\164\141\144\141\x74\x61", "\111\104\120\123\123\117\104\x65\x73\x63\x72\x69\160\164\x6f\x72");
        $le->setAttribute("\102\151\156\x64\151\x6e\x67", "\x75\x72\156\x3a\157\x61\163\x69\x73\72\x6e\141\x6d\x65\163\x3a\x74\x63\72\x53\x41\115\114\x3a\x32\x2e\60\72\x62\x69\156\x64\x69\156\x67\x73\x3a\110\x54\x54\x50\55\120\x4f\123\124");
        $le->setAttribute("\114\x6f\143\x61\164\x69\x6f\156", $this->acsUrl);
        $le->setAttribute("\x49\156\144\x65\170", "\x31");
        return $le;
    }
    public function generateIdPMetadata()
    {
        $wO = $this->createEntityDescriptorElement();
        $this->xml->appendChild($wO);
        $bN = $this->createIdpDescriptorElement();
        $wO->appendChild($bN);
        $l8 = $this->createRoleDescriptorElement();
        $wO->appendChild($l8);
        $On = $this->createKeyDescriptorElement();
        $bN->appendChild($On);
        $Jp = $this->createKeyDescriptorElement();
        $l8->appendChild($Jp);
        $Rh = $this->createTokenTypesElement();
        $l8->appendChild($Rh);
        $gX = $this->createPassiveRequestEndpoints();
        $l8->appendChild($gX);
        $ve = $this->createNameIdFormatElements();
        foreach ($ve as $oi) {
            $bN->appendChild($oi);
            Sl:
        }
        Qt:
        $W7 = $this->createSSOUrls();
        foreach ($W7 as $f_) {
            $bN->appendChild($f_);
            C1:
        }
        jz:
        $OF = $this->createSLOUrls();
        foreach ($OF as $NN) {
            $bN->appendChild($NN);
            Ip:
        }
        fz:
        $BF = $this->xml->saveXML();
        return $BF;
    }
    private function createIdpDescriptorElement()
    {
        $bN = $this->xml->createElementNS("\165\x72\x6e\x3a\x6f\x61\x73\151\163\x3a\x6e\141\155\145\x73\72\x74\143\x3a\123\x41\115\x4c\72\x32\x2e\x30\72\155\x65\x74\141\x64\x61\164\x61", "\111\x44\x50\x53\123\117\104\145\x73\143\162\151\160\x74\x6f\162");
        $bN->setAttribute("\x57\141\x6e\164\x41\x75\x74\150\x6e\122\x65\x71\165\x65\163\164\163\123\x69\x67\156\145\x64", $this->wantAssertionSigned);
        $bN->setAttribute("\160\162\157\x74\157\x63\157\154\123\x75\160\160\157\x72\x74\x45\x6e\165\155\x65\162\x61\164\x69\157\x6e", "\x75\162\x6e\x3a\x6f\x61\x73\x69\x73\x3a\x6e\141\x6d\145\x73\x3a\164\x63\72\123\x41\x4d\x4c\x3a\x32\56\60\72\160\162\157\164\x6f\143\157\x6c");
        return $bN;
    }
    private function createRoleDescriptorElement()
    {
        $l8 = $this->xml->createElement("\122\157\x6c\x65\104\x65\x73\143\x72\151\x70\x74\157\162");
        $l8->setAttributeNS("\150\164\164\160\x3a\57\x2f\x77\x77\x77\56\167\63\56\x6f\162\x67\x2f\62\x30\x30\60\x2f\x78\155\154\x6e\163\x2f", "\x78\155\154\x6e\163\x3a\170\163\151", "\150\164\164\x70\x3a\57\57\x77\x77\x77\x2e\x77\63\x2e\157\x72\x67\x2f\x32\60\x30\x31\57\x58\115\114\x53\143\x68\x65\155\141\x2d\151\x6e\163\164\x61\156\x63\145");
        $l8->setAttributeNS("\x68\x74\x74\160\72\x2f\x2f\x77\167\x77\56\x77\63\56\157\162\x67\57\62\x30\60\60\x2f\170\x6d\154\156\x73\x2f", "\170\x6d\154\156\163\72\146\x65\x64", "\150\x74\x74\160\x3a\x2f\57\144\157\143\x73\56\157\x61\163\x69\x73\55\157\x70\145\x6e\x2e\157\x72\x67\57\167\163\x66\x65\144\57\146\x65\x64\145\162\x61\164\x69\x6f\156\x2f\x32\60\x30\67\60\x36");
        $l8->setAttribute("\x53\x65\x72\166\x69\143\145\x44\x69\163\160\154\x61\171\x4e\141\x6d\x65", "\155\x69\156\x69\117\x72\156\x61\147\x65\x20\111\x6e\143");
        $l8->setAttribute("\x78\x73\151\x3a\164\x79\160\x65", "\x66\145\x64\72\123\145\143\165\162\x69\x74\x79\x54\x6f\x6b\145\x6e\123\x65\162\x76\151\143\x65\x54\171\x70\x65");
        $l8->setAttribute("\160\162\x6f\164\157\143\157\x6c\x53\x75\160\x70\x6f\x72\x74\105\x6e\165\155\145\x72\x61\x74\151\x6f\x6e", "\150\x74\164\160\72\x2f\57\144\x6f\143\x73\x2e\x6f\x61\x73\151\x73\x2d\157\160\x65\156\x2e\x6f\x72\x67\x2f\x77\x73\x2d\163\x78\57\167\x73\55\164\162\165\x73\164\57\x32\60\60\65\x31\x32\x20\x68\164\164\160\x3a\x2f\57\x73\143\150\x65\x6d\x61\x73\x2e\x78\155\154\x73\157\141\160\x2e\x6f\x72\x67\57\x77\163\x2f\x32\x30\60\65\57\x30\x32\57\164\x72\165\163\x74\x20\x68\164\164\x70\x3a\57\x2f\144\x6f\143\163\x2e\157\x61\163\x69\163\x2d\157\160\145\156\56\x6f\162\x67\x2f\167\163\146\145\144\x2f\146\145\x64\145\162\141\x74\x69\157\x6e\57\62\60\60\x37\60\x36");
        return $l8;
    }
    private function createTokenTypesElement()
    {
        $Rh = $this->xml->createElement("\146\x65\144\x3a\124\157\x6b\x65\156\124\171\160\145\x73\x4f\x66\146\x65\162\145\144");
        $fv = $this->xml->createElement("\146\145\x64\72\124\x6f\153\145\156\124\x79\x70\145");
        $fv->setAttribute("\x55\162\x69", "\165\x72\x6e\x3a\x6f\x61\163\151\x73\x3a\156\141\x6d\145\163\x3a\x74\143\x3a\123\101\x4d\x4c\x3a\61\56\60\x3a\141\x73\163\x65\x72\x74\151\x6f\x6e");
        $Rh->appendChild($fv);
        return $Rh;
    }
    private function createPassiveRequestEndpoints()
    {
        $gX = $this->xml->createElement("\146\145\x64\x3a\120\141\x73\x73\x69\x76\145\x52\145\x71\x75\x65\x73\164\x6f\x72\x45\156\144\160\x6f\151\x6e\164");
        $MB = $this->xml->createElementNS("\150\164\x74\x70\x3a\57\x2f\167\167\167\56\167\63\56\x6f\162\x67\57\62\x30\x30\x35\x2f\60\x38\x2f\141\144\144\x72\145\163\163\151\x6e\147", "\x61\144\x3a\105\x6e\x64\160\157\151\156\164\x52\x65\x66\x65\x72\145\156\x63\x65");
        $MB->appendChild($this->xml->createElement("\x41\x64\144\162\x65\x73\163", $this->singleSignOnServiceURLs["\x75\x72\x6e\72\x6f\x61\163\151\163\x3a\156\x61\x6d\x65\163\72\x74\x63\x3a\123\x41\x4d\x4c\72\x32\56\60\x3a\142\x69\156\x64\x69\x6e\147\x73\72\x48\x54\x54\120\x2d\120\x4f\123\x54"]));
        $gX->appendChild($MB);
        return $gX;
    }
    private function createSSOUrls()
    {
        $W7 = array();
        foreach ($this->singleSignOnServiceURLs as $Mn => $At) {
            $L8 = $this->xml->createElementNS("\x75\x72\x6e\72\x6f\x61\x73\151\x73\x3a\x6e\141\x6d\145\x73\72\x74\x63\72\123\x41\115\114\72\62\x2e\x30\x3a\x6d\x65\164\x61\x64\141\x74\x61", "\123\x69\x6e\x67\154\145\x53\x69\x67\x6e\117\156\x53\145\x72\x76\151\143\x65");
            $L8->setAttribute("\102\151\x6e\x64\x69\x6e\147", $Mn);
            $L8->setAttribute("\114\157\143\x61\164\151\x6f\156", $At);
            array_push($W7, $L8);
            Rt:
        }
        x2:
        return $W7;
    }
}
