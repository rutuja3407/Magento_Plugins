<?php


namespace MiniOrange\SP\Helper;

use DOMElement;
use DOMNode;
use DOMDocument;
use Exception;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecEnc;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityDSig;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
class IdentityProviders
{
    private $idpName;
    private $entityID;
    private $loginDetails;
    private $logoutDetails;
    private $signingCertificate;
    private $encryptionCertificate;
    private $signedRequest;
    public function __construct(DOMElement $BY = NULL)
    {
        $this->idpName = '';
        $this->loginDetails = array();
        $this->logoutDetails = array();
        $this->signingCertificate = array();
        $this->encryptionCertificate = array();
        if (!$BY->hasAttribute("\x65\x6e\x74\151\164\171\x49\x44")) {
            goto gh;
        }
        $this->entityID = $BY->getAttribute("\x65\x6e\x74\x69\164\171\111\104");
        gh:
        if (!$BY->hasAttribute("\x57\x61\x6e\164\x41\165\164\150\156\x52\x65\x71\165\145\163\x74\163\123\x69\x67\x6e\145\x64")) {
            goto Dm;
        }
        $this->signedRequest = $BY->getAttribute("\127\x61\156\x74\101\x75\164\150\156\x52\x65\x71\x75\145\163\164\x73\x53\151\x67\156\x65\144");
        Dm:
        $op = SAML2Utilities::xpQuery($BY, "\56\57\x73\141\155\154\x5f\155\x65\x74\141\x64\x61\164\141\x3a\x49\104\x50\123\x53\117\104\145\163\143\x72\151\160\x74\x6f\x72");
        if (count($op) > 1) {
            goto wr;
        }
        if (empty($op)) {
            goto HV;
        }
        goto EH;
        wr:
        throw new Exception("\x4d\157\162\x65\x20\x74\x68\141\156\x20\x6f\156\145\40\x3c\x49\x44\x50\x53\123\x4f\104\145\163\x63\162\x69\160\164\157\x72\x3e\40\x69\x6e\40\74\x45\156\x74\x69\164\171\104\145\x73\143\x72\151\x70\164\x6f\x72\76\56");
        goto EH;
        HV:
        throw new Exception("\115\x69\163\163\x69\156\x67\40\x72\x65\161\x75\x69\x72\x65\x64\x20\x3c\x49\104\x50\x53\123\x4f\x44\x65\x73\x63\162\x69\x70\x74\x6f\162\x3e\40\x69\156\40\74\x45\x6e\164\x69\164\171\104\145\163\x63\x72\x69\160\x74\157\x72\76\56");
        EH:
        $dH = $op[0];
        $lv = SAML2Utilities::xpQuery($BY, "\x2e\x2f\x73\x61\x6d\x6c\x5f\155\x65\x74\141\144\141\x74\141\x3a\105\170\x74\x65\156\163\151\157\156\x73");
        if (!$lv) {
            goto yb;
        }
        $this->parseInfo($dH);
        yb:
        $this->parseSSOService($dH);
        $this->parseSLOService($dH);
        $this->parsex509Certificate($dH);
    }
    private function parseInfo($BY)
    {
        $ad = SAML2Utilities::xpQuery($BY, "\56\x2f\x6d\x64\x75\x69\x3a\125\x49\111\x6e\146\x6f\x2f\x6d\x64\x75\x69\x3a\x44\x69\163\160\154\141\x79\x4e\x61\155\145");
        foreach ($ad as $rb) {
            if (!($rb->hasAttribute("\x78\x6d\154\72\x6c\x61\x6e\147") && $rb->getAttribute("\170\x6d\x6c\72\154\x61\156\147") == "\145\156")) {
                goto ll;
            }
            $this->idpName = $rb->textContent;
            ll:
            y3:
        }
        QJ:
    }
    private function parseSSOService($BY)
    {
        $hY = SAML2Utilities::xpQuery($BY, "\56\x2f\x73\x61\x6d\x6c\137\155\x65\x74\x61\144\x61\164\x61\x3a\123\x69\x6e\147\154\145\x53\151\x67\156\x4f\x6e\123\145\x72\166\x69\x63\145");
        foreach ($hY as $e7) {
            $Rp = str_replace("\x75\162\156\72\157\x61\x73\151\x73\x3a\x6e\x61\155\145\x73\72\x74\x63\x3a\x53\101\115\x4c\x3a\x32\56\x30\x3a\142\x69\x6e\x64\x69\156\147\163\72", '', $e7->getAttribute("\x42\151\156\x64\151\x6e\x67"));
            $this->loginDetails = array_merge($this->loginDetails, array($Rp => $e7->getAttribute("\x4c\x6f\143\141\164\x69\157\x6e")));
            yW:
        }
        iA:
    }
    private function parseSLOService($BY)
    {
        $dP = SAML2Utilities::xpQuery($BY, "\56\57\x73\x61\155\154\x5f\x6d\x65\x74\141\x64\x61\x74\x61\x3a\x53\x69\156\147\x6c\x65\114\157\147\x6f\x75\164\x53\145\162\x76\151\143\x65");
        foreach ($dP as $cg) {
            $Rp = str_replace("\165\162\x6e\x3a\157\141\163\151\x73\72\x6e\141\155\145\x73\72\x74\143\x3a\x53\x41\115\x4c\72\x32\x2e\x30\x3a\x62\x69\156\x64\x69\x6e\147\163\x3a", '', $cg->getAttribute("\x42\x69\x6e\144\151\x6e\x67"));
            $this->logoutDetails = array_merge($this->logoutDetails, array($Rp => $cg->getAttribute("\x4c\157\x63\x61\164\x69\x6f\156")));
            UM:
        }
        cl:
    }
    private function parsex509Certificate($BY)
    {
        foreach (SAML2Utilities::xpQuery($BY, "\x2e\57\163\141\155\154\137\x6d\145\x74\141\144\141\x74\141\72\x4b\145\x79\x44\145\163\x63\x72\x69\160\164\157\x72") as $NH) {
            if ($NH->hasAttribute("\x75\x73\x65")) {
                goto Nm;
            }
            $this->parseSigningCertificate($NH);
            goto a2;
            Nm:
            if ($NH->getAttribute("\x75\163\145") == "\x65\x6e\x63\162\x79\160\x74\x69\x6f\156") {
                goto VU;
            }
            $this->parseSigningCertificate($NH);
            goto GT;
            VU:
            $this->parseEncryptionCertificate($NH);
            GT:
            a2:
            xL:
        }
        wQ:
    }
    private function parseSigningCertificate($BY)
    {
        $wm = SAML2Utilities::xpQuery($BY, "\56\57\144\163\72\113\145\171\x49\156\146\157\x2f\144\x73\72\x58\x35\60\x39\104\141\164\x61\x2f\x64\163\x3a\130\x35\x30\71\103\x65\162\x74\151\146\x69\143\x61\x74\145");
        $MS = trim($wm[0]->textContent);
        $MS = str_replace(array("\xd", "\12", "\11", "\x20"), '', $MS);
        if (empty($wm)) {
            goto xo;
        }
        array_push($this->signingCertificate, SAML2Utilities::sanitize_certificate($MS));
        xo:
    }
    private function parseEncryptionCertificate($BY)
    {
        $wm = SAML2Utilities::xpQuery($BY, "\x2e\57\x64\x73\72\113\x65\171\111\156\x66\157\57\144\x73\x3a\130\65\x30\x39\x44\x61\x74\x61\x2f\144\163\x3a\x58\x35\x30\x39\103\x65\162\164\x69\x66\x69\x63\x61\164\145");
        $MS = trim($wm[0]->textContent);
        $MS = str_replace(array("\xd", "\12", "\x9", "\x20"), '', $MS);
        if (empty($wm)) {
            goto t2;
        }
        array_push($this->encryptionCertificate, $MS);
        t2:
    }
    public function getIdpName()
    {
        return '';
    }
    public function getEntityID()
    {
        return $this->entityID;
    }
    public function getLoginURL($Rp)
    {
        return $this->loginDetails[$Rp];
    }
    public function getLogoutURL($Rp)
    {
        return $this->logoutDetails[$Rp];
    }
    public function getLoginDetails()
    {
        return $this->loginDetails;
    }
    public function getLogoutDetails()
    {
        return $this->logoutDetails;
    }
    public function getSigningCertificate()
    {
        return $this->signingCertificate;
    }
    public function getEncryptionCertificate()
    {
        return $this->encryptionCertificate[0];
    }
    public function isRequestSigned()
    {
        return $this->signedRequest;
    }
}
