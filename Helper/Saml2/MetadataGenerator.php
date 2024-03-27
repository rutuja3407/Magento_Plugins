<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
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
    public function __construct($wz, $Hi, $Gi, $so, $u4, $xB, $LT, $Re, $R6)
    {
        $this->xml = new \DOMDocument("\x31\x2e\x30", "\165\x74\x66\55\70");
        $this->xml->preserveWhiteSpace = FALSE;
        $this->xml->formatOutput = TRUE;
        $this->issuer = $wz;
        $this->wantAssertionSigned = $Hi;
        $this->authnRequestSigned = $Gi;
        $this->x509Certificate = $so;
        $this->nameIDFormats = array("\x75\x72\x6e\x3a\x6f\x61\163\151\163\x3a\x6e\x61\155\x65\x73\72\164\x63\x3a\123\101\115\x4c\72\x31\x2e\x31\72\156\x61\155\145\x69\144\55\146\157\162\x6d\x61\x74\x3a\x65\155\141\x69\x6c\101\144\144\162\x65\x73\x73", "\x75\x72\x6e\x3a\x6f\x61\163\x69\x73\x3a\156\141\155\x65\x73\72\x74\143\72\x53\101\115\x4c\72\x31\56\61\x3a\156\141\155\x65\x69\144\55\x66\x6f\x72\155\x61\164\72\x75\x6e\163\160\145\143\x69\146\x69\x65\x64");
        $this->singleSignOnServiceURLs = array("\165\162\156\x3a\157\141\x73\x69\x73\72\x6e\x61\x6d\145\x73\x3a\164\143\x3a\x53\101\x4d\x4c\72\62\x2e\60\x3a\x62\x69\156\144\151\x6e\147\163\72\x48\x54\124\x50\55\x50\117\123\124" => $u4, "\x75\x72\156\72\x6f\x61\x73\151\163\72\x6e\141\x6d\x65\x73\x3a\164\143\72\x53\101\115\114\x3a\x32\56\60\x3a\142\151\156\x64\151\x6e\147\x73\x3a\x48\x54\x54\x50\55\x52\145\x64\151\x72\x65\x63\164" => $xB);
        $this->singleLogoutServiceURLs = array("\165\x72\x6e\x3a\157\x61\163\x69\163\x3a\156\x61\x6d\x65\163\x3a\164\143\72\123\101\x4d\114\72\62\x2e\x30\x3a\x62\151\x6e\144\x69\156\147\163\72\x48\x54\124\120\x2d\120\117\123\124" => $LT, "\165\x72\156\72\157\141\x73\151\x73\72\156\x61\x6d\145\163\72\164\x63\72\123\x41\x4d\x4c\x3a\62\x2e\x30\72\x62\151\x6e\x64\151\156\x67\163\72\110\124\x54\120\55\122\145\144\x69\162\145\x63\x74" => $Re);
        $this->acsUrl = $R6;
    }
    public function generateSPMetadata()
    {
        $eE = $this->createEntityDescriptorElement();
        $this->xml->appendChild($eE);
        $nx = $this->createSpDescriptorElement();
        $eE->appendChild($nx);
        $zg = $this->createKeyDescriptorElement("\163\151\147\156\151\x6e\147");
        $nx->appendChild($zg);
        $VU = $this->createKeyDescriptorElement("\145\156\x63\x72\171\160\164\x69\x6f\x6e");
        $nx->appendChild($VU);
        $fL = $this->createSLOUrls();
        foreach ($fL as $pS) {
            $nx->appendChild($pS);
            uRl:
        }
        oIi:
        $e9 = $this->createNameIdFormatElements();
        foreach ($e9 as $Db) {
            $nx->appendChild($Db);
            AGv:
        }
        oev:
        $nz = $this->createAcsUrlElement();
        $nx->appendChild($nz);
        $Xe = $this->xml->saveXML();
        return $Xe;
    }
    public function generateIdPMetadata()
    {
        $eE = $this->createEntityDescriptorElement();
        $this->xml->appendChild($eE);
        $bS = $this->createIdpDescriptorElement();
        $eE->appendChild($bS);
        $Zp = $this->createRoleDescriptorElement();
        $eE->appendChild($Zp);
        $zg = $this->createKeyDescriptorElement();
        $bS->appendChild($zg);
        $VU = $this->createKeyDescriptorElement();
        $Zp->appendChild($VU);
        $m6 = $this->createTokenTypesElement();
        $Zp->appendChild($m6);
        $K6 = $this->createPassiveRequestEndpoints();
        $Zp->appendChild($K6);
        $e9 = $this->createNameIdFormatElements();
        foreach ($e9 as $Db) {
            $bS->appendChild($Db);
            AqX:
        }
        eij:
        $Xj = $this->createSSOUrls();
        foreach ($Xj as $sP) {
            $bS->appendChild($sP);
            H2h:
        }
        rYQ:
        $fL = $this->createSLOUrls();
        foreach ($fL as $pS) {
            $bS->appendChild($pS);
            G2r:
        }
        yrA:
        $Xe = $this->xml->saveXML();
        return $Xe;
    }
    private function createEntityDescriptorElement()
    {
        $eE = $this->xml->createElementNS("\x75\162\156\x3a\x6f\141\163\151\x73\x3a\156\x61\x6d\145\163\72\164\x63\72\123\101\x4d\x4c\x3a\x32\56\60\x3a\x6d\x65\164\141\144\x61\164\x61", "\105\x6e\x74\151\x74\x79\x44\145\163\x63\x72\151\x70\164\157\x72");
        $eE->setAttribute("\x65\x6e\x74\151\164\171\x49\x44", $this->issuer);
        return $eE;
    }
    private function createIdpDescriptorElement()
    {
        $bS = $this->xml->createElementNS("\165\162\x6e\x3a\157\x61\x73\151\163\72\156\141\x6d\x65\x73\x3a\164\143\72\x53\x41\x4d\114\72\x32\x2e\60\x3a\x6d\145\164\x61\144\141\x74\141", "\x49\x44\120\123\123\117\x44\x65\x73\x63\162\151\x70\x74\157\x72");
        $bS->setAttribute("\x57\141\x6e\164\x41\165\x74\150\x6e\122\x65\161\165\x65\x73\164\163\x53\151\x67\x6e\145\x64", $this->wantAssertionSigned);
        $bS->setAttribute("\160\x72\x6f\164\157\x63\x6f\154\x53\165\x70\x70\157\162\x74\105\x6e\x75\x6d\145\162\141\164\x69\x6f\156", "\165\x72\156\x3a\x6f\x61\x73\151\163\72\x6e\x61\x6d\x65\163\x3a\164\143\x3a\x53\101\x4d\x4c\72\62\x2e\60\72\x70\162\x6f\x74\x6f\143\x6f\x6c");
        return $bS;
    }
    private function createAcsUrlElement()
    {
        $JZ = $this->xml->createElementNS("\x75\162\156\72\x6f\x61\163\x69\163\72\x6e\x61\155\145\163\x3a\x74\x63\x3a\123\101\115\114\x3a\62\x2e\60\x3a\155\x65\x74\x61\144\x61\x74\141", "\111\x44\120\x53\x53\117\104\x65\163\143\162\x69\160\x74\x6f\x72");
        $JZ->setAttribute("\102\151\x6e\144\x69\156\147", "\x75\162\x6e\x3a\157\x61\163\x69\163\72\156\x61\x6d\145\x73\x3a\x74\x63\x3a\x53\101\115\x4c\x3a\x32\x2e\60\72\x62\151\x6e\x64\151\x6e\x67\x73\x3a\x48\124\x54\x50\x2d\120\117\x53\x54");
        $JZ->setAttribute("\x4c\x6f\143\x61\164\x69\157\156", $this->acsUrl);
        $JZ->setAttribute("\x49\x6e\144\x65\170", "\x31");
        return $JZ;
    }
    private function createSPDescriptorElement()
    {
        $tg = $this->xml->createElementNS("\x75\x72\x6e\x3a\157\141\163\x69\163\x3a\x6e\x61\x6d\x65\163\x3a\x74\x63\72\123\x41\x4d\114\72\x32\56\60\72\155\x65\x74\141\x64\x61\x74\x61", "\123\120\x53\x53\117\x44\x65\x73\x63\x72\151\160\164\x6f\162");
        $tg->setAttribute("\x57\141\156\x74\101\165\164\150\156\122\145\161\x75\145\163\164\163\x53\x69\147\x6e\145\x64", $this->wantAssertionSigned);
        $tg->setAttribute("\101\165\x74\x68\156\x52\x65\x71\x75\x65\163\x74\163\123\151\147\156\x65\x64", $this->authnRequestSigned);
        $tg->setAttribute("\160\x72\157\164\157\x63\157\x6c\123\165\160\160\157\162\164\105\156\165\x6d\145\x72\141\164\x69\x6f\156", "\165\x72\x6e\x3a\x6f\141\x73\x69\x73\x3a\x6e\141\x6d\145\x73\72\x74\x63\x3a\x53\101\115\114\72\62\56\60\x3a\160\162\157\164\x6f\143\157\154");
        return $tg;
    }
    private function createKeyDescriptorElement($QU)
    {
        $zg = $this->xml->createElement("\x4b\x65\171\x44\x65\163\143\x72\x69\160\164\x6f\162");
        $zg->setAttribute("\x75\x73\x65", $QU);
        $Fp = $this->generateKeyInfo();
        $zg->appendChild($Fp);
        return $zg;
    }
    private function generateKeyInfo()
    {
        $Fp = $this->xml->createElementNS("\150\164\164\160\72\57\x2f\167\x77\x77\x2e\167\x33\x2e\157\162\x67\57\62\60\60\x30\x2f\60\x39\57\x78\x6d\x6c\x64\x73\151\147\43", "\144\x73\72\x4b\x65\x79\x49\x6e\x66\x6f");
        $fs = $this->xml->createElementNS("\x68\164\164\160\x3a\x2f\x2f\x77\x77\x77\56\167\63\56\157\162\147\x2f\x32\x30\60\60\57\60\71\x2f\170\x6d\154\144\163\151\x67\43", "\144\163\x3a\130\x35\x30\71\x44\x61\164\x61");
        $GQ = SAML2Utilities::desanitize_certificate($this->x509Certificate);
        $zD = $this->xml->createElementNS("\150\x74\x74\x70\72\x2f\57\x77\x77\167\56\167\x33\56\x6f\x72\147\57\62\60\60\60\x2f\60\71\x2f\x78\155\x6c\x64\163\151\x67\43", "\144\163\72\130\65\60\x39\x43\x65\x72\164\151\x66\x69\x63\x61\x74\x65", $GQ);
        $fs->appendChild($zD);
        $Fp->appendChild($fs);
        return $Fp;
    }
    private function createNameIdFormatElements()
    {
        $e9 = array();
        foreach ($this->nameIDFormats as $g6) {
            array_push($e9, $this->xml->createElementNS("\165\162\156\72\157\x61\x73\x69\x73\x3a\x6e\x61\155\x65\x73\72\x74\x63\x3a\x53\x41\115\x4c\x3a\x32\56\60\72\x6d\145\x74\x61\x64\x61\x74\x61", "\x4e\141\x6d\145\x49\x44\x46\157\162\155\x61\x74", $g6));
            hXP:
        }
        hnd:
        return $e9;
    }
    private function createSSOUrls()
    {
        $Xj = array();
        foreach ($this->singleSignOnServiceURLs as $Rp => $JY) {
            $hO = $this->xml->createElementNS("\x75\x72\156\x3a\x6f\141\163\151\x73\x3a\x6e\141\155\x65\x73\72\x74\143\x3a\x53\x41\115\x4c\72\62\x2e\x30\72\x6d\x65\x74\141\144\x61\x74\x61", "\x53\151\x6e\x67\x6c\x65\123\x69\x67\x6e\x4f\156\x53\x65\x72\x76\x69\x63\145");
            $hO->setAttribute("\x42\151\x6e\x64\151\x6e\x67", $Rp);
            $hO->setAttribute("\x4c\x6f\143\x61\x74\151\157\156", $JY);
            array_push($Xj, $hO);
            ZIt:
        }
        PTj:
        return $Xj;
    }
    private function createSLOUrls()
    {
        $fL = array();
        foreach ($this->singleLogoutServiceURLs as $Rp => $JY) {
            $pS = $this->xml->createElementNS("\165\x72\x6e\x3a\157\x61\163\151\163\x3a\x6e\x61\x6d\x65\163\72\x74\143\x3a\123\101\x4d\114\72\x32\56\x30\72\x6d\x65\164\x61\144\141\164\141", "\x53\x69\x6e\147\154\145\x4c\157\x67\157\x75\164\x53\145\162\x76\x69\x63\x65");
            $pS->setAttribute("\102\151\156\144\151\156\x67", $Rp);
            $pS->setAttribute("\114\x6f\x63\x61\164\151\x6f\x6e", $JY);
            array_push($fL, $pS);
            RhQ:
        }
        IyE:
        return $fL;
    }
    private function createRoleDescriptorElement()
    {
        $Zp = $this->xml->createElement("\122\x6f\154\145\x44\x65\x73\x63\162\151\160\164\x6f\162");
        $Zp->setAttributeNS("\150\164\164\160\x3a\x2f\x2f\167\x77\x77\x2e\x77\x33\56\157\162\147\x2f\x32\60\60\60\x2f\x78\x6d\x6c\x6e\x73\57", "\170\155\154\x6e\163\72\x78\163\x69", "\150\164\x74\x70\72\57\57\x77\x77\x77\x2e\167\63\x2e\x6f\x72\147\57\x32\x30\x30\61\x2f\x58\x4d\114\x53\x63\150\145\x6d\141\55\x69\156\x73\164\x61\156\x63\145");
        $Zp->setAttributeNS("\150\164\x74\160\x3a\57\57\167\167\167\x2e\x77\x33\x2e\157\162\147\57\x32\x30\x30\x30\x2f\x78\155\154\x6e\x73\x2f", "\170\x6d\154\156\x73\x3a\146\145\x64", "\x68\x74\x74\160\72\57\x2f\144\157\x63\x73\56\157\141\163\151\x73\55\x6f\160\x65\156\x2e\x6f\x72\x67\x2f\167\x73\x66\145\x64\57\x66\145\x64\x65\x72\x61\164\151\157\156\57\x32\60\x30\67\60\x36");
        $Zp->setAttribute("\x53\x65\x72\x76\151\x63\145\x44\151\x73\x70\154\141\171\x4e\141\155\x65", "\x6d\x69\156\151\x4f\162\x6e\x61\147\145\40\111\x6e\143");
        $Zp->setAttribute("\x78\x73\151\x3a\x74\171\160\x65", "\x66\x65\144\x3a\123\145\143\x75\x72\151\x74\x79\124\x6f\153\x65\x6e\123\x65\x72\x76\x69\x63\145\x54\171\160\x65");
        $Zp->setAttribute("\x70\162\x6f\x74\x6f\143\x6f\x6c\123\x75\x70\160\x6f\162\164\105\x6e\165\155\x65\x72\x61\x74\151\157\x6e", "\150\x74\164\x70\72\57\x2f\x64\x6f\x63\x73\56\157\141\163\x69\163\55\157\x70\145\x6e\x2e\x6f\x72\x67\57\167\x73\x2d\163\170\57\167\x73\x2d\x74\162\x75\163\x74\57\x32\60\x30\x35\x31\x32\x20\x68\164\164\160\x3a\x2f\x2f\x73\x63\x68\x65\x6d\x61\163\56\x78\x6d\154\163\157\141\160\x2e\157\x72\x67\57\167\x73\x2f\62\60\60\x35\x2f\x30\62\x2f\164\162\165\x73\164\x20\150\x74\164\x70\72\57\x2f\x64\157\143\163\x2e\157\141\x73\x69\x73\55\157\x70\145\156\x2e\157\162\x67\x2f\x77\163\x66\145\144\x2f\x66\145\144\145\162\141\x74\x69\157\x6e\x2f\62\x30\x30\67\x30\x36");
        return $Zp;
    }
    private function createTokenTypesElement()
    {
        $m6 = $this->xml->createElement("\x66\145\144\72\124\x6f\153\x65\156\124\x79\160\x65\x73\x4f\146\146\x65\162\x65\144");
        $JS = $this->xml->createElement("\x66\145\144\72\124\x6f\153\145\156\124\171\x70\x65");
        $JS->setAttribute("\x55\162\151", "\165\162\x6e\72\157\141\x73\151\x73\72\156\x61\x6d\145\x73\72\164\143\x3a\x53\101\x4d\114\x3a\61\x2e\60\x3a\141\x73\163\x65\x72\164\151\157\156");
        $m6->appendChild($JS);
        return $m6;
    }
    private function createPassiveRequestEndpoints()
    {
        $K6 = $this->xml->createElement("\146\145\x64\72\x50\x61\163\163\151\166\x65\x52\x65\x71\165\145\163\x74\157\162\x45\156\144\x70\157\151\x6e\164");
        $FC = $this->xml->createElementNS("\150\x74\x74\160\x3a\57\57\167\x77\167\x2e\x77\63\56\157\162\147\57\62\x30\60\x35\57\60\x38\x2f\141\144\144\162\x65\x73\163\x69\x6e\x67", "\x61\144\72\x45\156\144\160\157\x69\x6e\x74\x52\145\146\x65\162\x65\x6e\x63\x65");
        $FC->appendChild($this->xml->createElement("\101\144\144\162\x65\163\x73", $this->singleSignOnServiceURLs["\x75\x72\x6e\x3a\x6f\141\x73\x69\163\x3a\156\x61\155\145\x73\72\x74\143\72\123\x41\115\x4c\x3a\62\x2e\x30\72\x62\151\x6e\144\x69\156\x67\x73\x3a\x48\x54\124\x50\x2d\120\117\x53\124"]));
        $K6->appendChild($FC);
        return $K6;
    }
}
