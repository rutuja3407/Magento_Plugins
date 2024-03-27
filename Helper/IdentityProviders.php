<?php


namespace MiniOrange\SP\Helper;

use DOMElement;
use Exception;
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
    public function __construct(DOMElement $xa = NULL)
    {
        $this->idpName = '';
        $this->loginDetails = array();
        $this->logoutDetails = array();
        $this->signingCertificate = array();
        $this->encryptionCertificate = array();
        if (!$xa->hasAttribute("\145\x6e\x74\151\164\171\111\x44")) {
            goto ZN;
        }
        $this->entityID = $xa->getAttribute("\145\x6e\x74\x69\x74\x79\111\x44");
        ZN:
        if (!$xa->hasAttribute("\127\141\156\x74\101\x75\164\150\x6e\122\x65\161\165\x65\x73\164\163\123\x69\147\x6e\145\144")) {
            goto p9;
        }
        $this->signedRequest = $xa->getAttribute("\x57\x61\156\x74\x41\165\164\150\156\122\145\161\165\x65\x73\x74\163\x53\151\147\156\145\x64");
        p9:
        $sp = SAML2Utilities::xpQuery($xa, "\x2e\x2f\163\x61\x6d\154\137\155\145\x74\x61\x64\x61\x74\141\72\x49\x44\120\123\123\x4f\104\x65\x73\143\162\x69\x70\x74\157\x72");
        if (count($sp) > 1) {
            goto EB;
        }
        if (empty($sp)) {
            goto zx;
        }
        goto BA;
        EB:
        throw new Exception("\x4d\x6f\162\x65\40\164\150\x61\x6e\x20\x6f\156\145\x20\x3c\x49\x44\120\123\123\117\104\x65\x73\x63\162\x69\160\164\x6f\162\x3e\x20\x69\x6e\x20\x3c\x45\156\x74\x69\164\171\x44\145\x73\143\162\151\x70\x74\157\x72\x3e\56");
        goto BA;
        zx:
        throw new Exception("\x4d\151\163\x73\x69\x6e\147\x20\162\x65\x71\165\x69\162\x65\x64\40\74\x49\x44\120\123\123\x4f\104\x65\163\x63\x72\x69\x70\164\x6f\162\x3e\40\x69\x6e\40\74\x45\x6e\164\x69\x74\171\x44\x65\x73\143\x72\x69\160\x74\x6f\162\x3e\56");
        BA:
        $Ys = $sp[0];
        $BU = SAML2Utilities::xpQuery($xa, "\56\57\163\141\x6d\x6c\137\x6d\145\x74\x61\x64\x61\164\x61\x3a\x45\170\x74\x65\156\x73\151\x6f\x6e\x73");
        if (!$BU) {
            goto FD;
        }
        $this->parseInfo($Ys);
        FD:
        $this->parseSSOService($Ys);
        $this->parseSLOService($Ys);
        $this->parsex509Certificate($Ys);
    }
    private function parseInfo($xa)
    {
        $KJ = SAML2Utilities::xpQuery($xa, "\x2e\x2f\x6d\144\165\151\72\x55\111\111\x6e\x66\x6f\x2f\x6d\144\x75\151\72\104\151\x73\160\x6c\141\171\x4e\141\155\145");
        foreach ($KJ as $yt) {
            if (!($yt->hasAttribute("\170\x6d\154\x3a\154\141\156\x67") && $yt->getAttribute("\170\x6d\154\72\x6c\x61\156\x67") == "\145\156")) {
                goto dB;
            }
            $this->idpName = $yt->textContent;
            dB:
            zF:
        }
        Jw:
    }
    private function parseSSOService($xa)
    {
        $HQ = SAML2Utilities::xpQuery($xa, "\56\57\163\141\155\154\x5f\155\x65\164\141\144\141\164\x61\x3a\123\x69\156\147\154\145\x53\151\x67\x6e\x4f\x6e\x53\x65\162\x76\x69\x63\145");
        foreach ($HQ as $Fb) {
            $Mn = str_replace("\x75\162\156\72\157\x61\x73\x69\163\x3a\156\141\x6d\145\x73\x3a\164\143\72\123\101\x4d\114\72\62\56\x30\72\x62\x69\156\x64\151\x6e\x67\163\x3a", '', $Fb->getAttribute("\102\151\x6e\x64\x69\x6e\147"));
            $this->loginDetails = array_merge($this->loginDetails, array($Mn => $Fb->getAttribute("\114\x6f\x63\141\x74\x69\157\156")));
            vT:
        }
        P7:
    }
    private function parseSLOService($xa)
    {
        $IW = SAML2Utilities::xpQuery($xa, "\56\x2f\163\141\x6d\x6c\x5f\155\145\x74\x61\144\141\x74\141\x3a\x53\x69\156\x67\154\x65\114\157\147\x6f\x75\164\x53\145\162\166\151\x63\x65");
        foreach ($IW as $hb) {
            $Mn = str_replace("\165\162\x6e\72\157\x61\163\151\163\x3a\x6e\141\155\145\163\72\x74\x63\x3a\x53\x41\115\x4c\x3a\62\x2e\x30\x3a\142\x69\x6e\144\x69\x6e\147\x73\72", '', $hb->getAttribute("\102\x69\156\x64\x69\x6e\x67"));
            $this->logoutDetails = array_merge($this->logoutDetails, array($Mn => $hb->getAttribute("\x4c\x6f\143\141\164\x69\x6f\156")));
            el:
        }
        FE:
    }
    private function parsex509Certificate($xa)
    {
        foreach (SAML2Utilities::xpQuery($xa, "\56\57\163\141\x6d\154\137\155\x65\164\x61\144\x61\164\x61\72\113\x65\171\104\145\163\143\162\151\x70\164\157\x72") as $AE) {
            if ($AE->hasAttribute("\x75\x73\145")) {
                goto yM;
            }
            $this->parseSigningCertificate($AE);
            goto xg;
            yM:
            if ($AE->getAttribute("\165\163\x65") == "\x65\x6e\x63\x72\171\x70\x74\151\157\156") {
                goto Sf;
            }
            $this->parseSigningCertificate($AE);
            goto Dl;
            Sf:
            $this->parseEncryptionCertificate($AE);
            Dl:
            xg:
            G_:
        }
        kB:
    }
    private function parseEncryptionCertificate($xa)
    {
        $wl = SAML2Utilities::xpQuery($xa, "\x2e\x2f\x64\163\x3a\x4b\x65\x79\111\156\x66\x6f\57\x64\163\x3a\x58\65\60\71\x44\x61\x74\x61\x2f\144\x73\x3a\130\x35\x30\x39\x43\x65\162\x74\x69\x66\x69\x63\x61\x74\145");
        $YB = trim($wl[0]->textContent);
        $YB = str_replace(array("\15", "\xa", "\11", "\x20"), '', $YB);
        if (empty($wl)) {
            goto Bd;
        }
        array_push($this->encryptionCertificate, $YB);
        Bd:
    }
    private function parseSigningCertificate($xa)
    {
        $wl = SAML2Utilities::xpQuery($xa, "\56\57\x64\x73\72\113\x65\171\111\x6e\x66\157\x2f\x64\x73\72\x58\x35\x30\x39\104\141\x74\141\57\144\163\x3a\130\x35\x30\x39\103\x65\162\164\x69\146\x69\x63\141\x74\145");
        $YB = trim($wl[0]->textContent);
        $YB = str_replace(array("\15", "\12", "\x9", "\x20"), '', $YB);
        if (empty($wl)) {
            goto ml;
        }
        array_push($this->signingCertificate, SAML2Utilities::sanitize_certificate($YB));
        ml:
    }
    public function getIdpName()
    {
        return '';
    }
    public function getEntityID()
    {
        return $this->entityID;
    }
    public function getLoginURL($Mn)
    {
        return $this->loginDetails[$Mn];
    }
    public function getLogoutURL($Mn)
    {
        return $this->logoutDetails[$Mn];
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
