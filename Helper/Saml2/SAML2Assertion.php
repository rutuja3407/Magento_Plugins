<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Exception\InvalidSAMLVersionException;
use MiniOrange\SP\Helper\Exception\MissingIDException;
use MiniOrange\SP\Helper\Exception\MissingIssuerValueException;
use MiniOrange\SP\Helper\Exception\InvalidNumberOfNameIDsException;
use MiniOrange\SP\Helper\Exception\MissingNameIdException;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Helper\SPConstants;
use Exception;
use DOMElement;
use DOMText;
class SAML2Assertion
{
    private $id;
    private $issueInstant;
    private $issuer;
    private $nameId;
    private $encryptedNameId;
    private $encryptedAttribute;
    private $encryptionKey;
    private $notBefore;
    private $notOnOrAfter;
    private $validAudiences;
    private $sessionNotOnOrAfter;
    private $sessionIndex;
    private $authnInstant;
    private $authnContextClassRef;
    private $authnContextDecl;
    private $authnContextDeclRef;
    private $AuthenticatingAuthority;
    private $attributes;
    private $nameFormat;
    private $signatureKey;
    private $certificates;
    private $signatureData;
    private $requiredEncAttributes;
    private $SubjectConfirmation;
    private $spUtility;
    protected $wasSignedAtConstruction = FALSE;
    public $key;
    public function __construct(\DOMElement $BY = NULL, SPUtility $Kx)
    {
        $this->id = SAML2Utilities::generateId();
        $this->issueInstant = SAML2Utilities::generateTimestamp();
        $this->issuer = '';
        $this->authnInstant = SAML2Utilities::generateTimestamp();
        $this->attributes = array();
        $this->nameFormat = "\165\x72\156\x3a\x6f\x61\x73\x69\163\x3a\x6e\141\x6d\145\163\x3a\164\143\x3a\123\x41\x4d\114\72\x31\x2e\x31\x3a\156\x61\155\x65\151\x64\x2d\x66\157\x72\x6d\x61\x74\72\165\156\163\160\x65\143\x69\x66\x69\145\x64";
        $this->certificates = array();
        $this->AuthenticatingAuthority = array();
        $this->SubjectConfirmation = array();
        $this->spUtility = $Kx;
        if (!($BY === NULL)) {
            goto gAS;
        }
        return;
        gAS:
        if (!($BY->localName === "\105\x6e\x63\x72\171\160\x74\x65\144\101\163\163\x65\162\164\151\157\x6e")) {
            goto xvt;
        }
        $F2 = SAML2Utilities::xpQuery($BY, "\x2e\57\170\x65\156\x63\x3a\105\x6e\x63\x72\x79\x70\x74\x65\x64\x44\141\164\141");
        $eB = SAML2Utilities::xpQuery($BY, "\x2e\57\170\x65\156\x63\72\x45\x6e\x63\162\x79\160\x74\145\144\104\x61\x74\141\x2f\144\163\72\113\145\171\111\x6e\x66\x6f\57\170\x65\156\x63\x3a\x45\x6e\143\162\x79\160\x74\x65\x64\x4b\145\x79");
        $bs = '';
        if (empty($eB)) {
            goto nSk;
        }
        $bs = $eB[0]->firstChild->getAttribute("\x41\154\147\x6f\162\x69\164\x68\x6d");
        goto QnG;
        nSk:
        $eB = SAML2Utilities::xpQuery($BY, "\56\x2f\170\145\156\143\x3a\105\156\x63\x72\x79\x70\x74\x65\x64\113\x65\x79\x2f\x78\x65\156\x63\x3a\105\x6e\143\162\x79\160\164\151\x6f\156\x4d\145\x74\x68\157\144");
        $bs = $eB[0]->getAttribute("\x41\x6c\147\x6f\162\151\x74\150\x6d");
        QnG:
        $s4 = SAML2Utilities::getEncryptionAlgorithm($bs);
        if (count($F2) === 0) {
            goto SvG;
        }
        if (count($F2) > 1) {
            goto hfm;
        }
        goto QZA;
        SvG:
        print_r("\115\151\x73\163\151\x6e\147\40\x65\156\x63\x72\171\x70\x74\145\x64\40\144\x61\x74\x61\x20\x69\x6e\40\x3c\x73\x61\x6d\x6c\72\105\156\x63\x72\x79\x70\x74\145\x64\x41\x73\x73\145\x72\x74\x69\x6f\x6e\76\x2e");
        exit;
        goto QZA;
        hfm:
        print_r("\x4d\x6f\x72\x65\40\164\150\x61\156\x20\157\x6e\145\x20\x65\156\143\x72\171\160\x74\145\144\x20\144\141\164\141\x20\145\154\x65\155\145\156\164\40\x69\x6e\40\x3c\163\141\155\x6c\x3a\105\x6e\x63\162\x79\x70\164\x65\x64\101\163\x73\x65\162\x74\x69\x6f\x6e\x3e\56");
        exit;
        QZA:
        $fT = array("\164\x79\160\x65" => "\x70\162\151\x76\x61\x74\x65");
        $zg = new XMLSecurityKey($s4, $fT);
        $ZW = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));
        $zg->loadKey($ZW);
        $KA = array();
        $BY = SAML2Utilities::decryptElement($F2[0], $zg, $KA);
        xvt:
        if ($BY->hasAttribute("\x49\x44")) {
            goto LnJ;
        }
        print_r("\115\151\163\163\151\156\147\x20\111\x44\x20\141\x74\164\x72\x69\x62\x75\x74\145\40\157\156\x20\x53\101\x4d\x4c\x20\x61\x73\163\x65\x72\x74\151\x6f\156\56");
        exit;
        LnJ:
        $this->id = $BY->getAttribute("\111\104");
        if (!($BY->getAttribute("\126\x65\162\163\151\157\x6e") !== "\62\56\60")) {
            goto EgZ;
        }
        print_r("\125\156\x73\165\160\x70\x6f\162\164\145\x64\40\166\145\162\163\151\x6f\156\x3a\x20" . $BY->getAttribute("\126\x65\162\x73\x69\157\156"));
        exit;
        EgZ:
        $this->issueInstant = SAML2Utilities::xsDateTimeToTimestamp($BY->getAttribute("\x49\x73\x73\165\x65\x49\156\x73\x74\141\x6e\x74"));
        $wz = SAML2Utilities::xpQuery($BY, "\x2e\x2f\x73\141\155\x6c\137\141\163\163\x65\162\164\x69\x6f\x6e\x3a\111\x73\163\x75\145\x72");
        if (!empty($wz)) {
            goto Uj2;
        }
        print_r("\115\x69\163\163\151\156\x67\x20\x3c\163\x61\155\154\x3a\111\163\163\x75\x65\x72\x3e\x20\151\156\40\x61\x73\163\x65\x72\x74\x69\x6f\156\56");
        exit;
        Uj2:
        $this->issuer = trim($wz[0]->textContent);
        $this->parseConditions($BY);
        $this->parseAuthnStatement($BY);
        $this->parseAttributes($BY);
        $this->parseEncryptedAttributes($BY);
        $this->parseSignature($BY);
        $this->parseSubject($BY);
    }
    private function parseSubject(DOMElement $BY)
    {
        $XP = SAML2Utilities::xpQuery($BY, "\56\57\x73\x61\x6d\x6c\x5f\x61\163\163\x65\162\x74\x69\x6f\156\72\x53\165\x62\x6a\145\x63\x74");
        if (empty($XP)) {
            goto jek;
        }
        if (count($XP) > 1) {
            goto Tcb;
        }
        goto c7s;
        jek:
        return;
        goto c7s;
        Tcb:
        print_r("\x4d\x6f\162\x65\40\x74\150\x61\156\40\157\156\145\x20\x3c\x73\141\x6d\154\72\x53\x75\x62\152\x65\x63\x74\76\x20\x69\x6e\40\74\x73\141\155\x6c\72\101\163\163\x65\162\x74\151\157\x6e\x3e\x2e");
        exit;
        c7s:
        $XP = $XP[0];
        $Q5 = SAML2Utilities::xpQuery($XP, "\x2e\57\163\141\155\154\137\141\x73\x73\145\x72\164\x69\157\x6e\x3a\x4e\x61\155\x65\111\104\40\x7c\x20\56\x2f\163\141\x6d\x6c\137\141\x73\163\x65\162\164\x69\157\156\72\x45\156\143\162\171\x70\x74\145\144\x49\104\x2f\170\x65\x6e\x63\72\105\x6e\x63\x72\171\x70\164\x65\x64\104\141\164\141");
        if (empty($Q5)) {
            goto psu;
        }
        if (count($Q5) > 1) {
            goto Y3R;
        }
        goto YB4;
        psu:
        print_r("\115\x69\x73\x73\x69\x6e\x67\40\x3c\x73\141\x6d\x6c\72\116\x61\155\x65\111\x44\76\x20\157\162\40\x3c\x73\x61\x6d\154\72\105\156\143\162\x79\x70\x74\145\x64\111\x44\x3e\40\151\156\40\74\163\x61\x6d\154\x3a\123\165\x62\x6a\x65\x63\x74\76\x2e");
        exit;
        goto YB4;
        Y3R:
        print_r("\x4d\x6f\x72\x65\x20\164\x68\141\x6e\x20\157\x6e\145\40\x3c\x73\x61\155\x6c\x3a\116\x61\x6d\145\111\104\76\40\157\x72\x20\x3c\163\x61\x6d\x6c\72\105\x6e\143\x72\x79\160\164\145\x64\x44\x3e\40\x69\x6e\40\x3c\x73\141\x6d\x6c\x3a\x53\165\x62\152\x65\x63\x74\76\x2e");
        exit;
        YB4:
        $Q5 = $Q5[0];
        if ($Q5->localName === "\105\156\143\162\171\x70\x74\145\144\x44\141\164\x61") {
            goto V6s;
        }
        $this->nameId = SAML2Utilities::parseNameId($Q5);
        goto LKK;
        V6s:
        $this->encryptedNameId = $Q5;
        LKK:
    }
    private function parseConditions(DOMElement $BY)
    {
        $Ey = SAML2Utilities::xpQuery($BY, "\56\x2f\163\x61\x6d\x6c\137\x61\163\x73\x65\x72\x74\151\x6f\156\x3a\103\x6f\x6e\144\x69\x74\151\157\x6e\163");
        if (empty($Ey)) {
            goto lA9;
        }
        if (count($Ey) > 1) {
            goto QqT;
        }
        goto zuI;
        lA9:
        return;
        goto zuI;
        QqT:
        print_r("\115\157\x72\x65\x20\164\150\141\156\40\157\156\145\x20\x3c\163\x61\155\154\72\103\157\x6e\x64\151\x74\x69\157\156\x73\x3e\40\x69\156\x20\74\x73\x61\155\x6c\x3a\x41\163\163\145\x72\x74\151\x6f\156\x3e\56");
        exit;
        zuI:
        $Ey = $Ey[0];
        if (!$Ey->hasAttribute("\116\157\164\x42\x65\x66\157\162\x65")) {
            goto tKB;
        }
        $TB = SAML2Utilities::xsDateTimeToTimestamp($Ey->getAttribute("\116\x6f\x74\102\x65\x66\x6f\x72\x65"));
        if (!($this->notBefore === NULL || $this->notBefore < $TB)) {
            goto ibK;
        }
        $this->notBefore = $TB;
        ibK:
        tKB:
        if (!$Ey->hasAttribute("\116\x6f\164\x4f\156\117\162\x41\146\164\x65\x72")) {
            goto oQ3;
        }
        $pz = SAML2Utilities::xsDateTimeToTimestamp($Ey->getAttribute("\x4e\157\x74\x4f\156\117\162\x41\x66\x74\x65\162"));
        if (!($this->notOnOrAfter === NULL || $this->notOnOrAfter > $pz)) {
            goto f9w;
        }
        $this->notOnOrAfter = $pz;
        f9w:
        oQ3:
        $w5 = $Ey->firstChild;
        Sug:
        if (!($w5 !== NULL)) {
            goto sz6;
        }
        if (!$w5 instanceof DOMText) {
            goto Xd1;
        }
        goto fUY;
        Xd1:
        if (!($w5->namespaceURI !== "\x75\x72\x6e\x3a\157\141\x73\x69\163\72\156\x61\155\x65\163\x3a\x74\x63\x3a\x53\x41\115\114\x3a\62\56\60\72\x61\x73\163\x65\x72\x74\151\x6f\x6e")) {
            goto Yx1;
        }
        print_r("\125\x6e\153\x6e\157\x77\156\40\x6e\x61\155\145\163\160\x61\143\x65\40\x6f\146\40\x63\x6f\x6e\x64\x69\x74\151\157\x6e\72\40" . var_export($w5->namespaceURI, TRUE));
        exit;
        Yx1:
        switch ($w5->localName) {
            case "\x41\165\x64\151\145\x6e\143\x65\122\x65\x73\164\x72\151\143\x74\x69\x6f\x6e":
                $zT = SAML2Utilities::extractStrings($w5, "\165\162\x6e\72\x6f\x61\163\x69\163\x3a\156\141\155\x65\163\x3a\x74\143\x3a\x53\101\x4d\x4c\72\62\x2e\60\x3a\x61\163\163\145\x72\164\x69\157\156", "\101\165\x64\151\x65\156\x63\145");
                if ($this->validAudiences === NULL) {
                    goto I51;
                }
                $this->validAudiences = array_intersect($this->validAudiences, $zT);
                goto t4r;
                I51:
                $this->validAudiences = $zT;
                t4r:
                goto nKh;
            case "\x4f\156\x65\x54\x69\x6d\145\125\163\x65":
                goto nKh;
            case "\x50\x72\157\170\171\122\145\x73\164\x72\151\143\164\151\x6f\156":
                goto nKh;
            default:
                print_r("\x55\156\x6b\156\x6f\x77\156\x20\x63\x6f\x6e\x64\151\x74\x69\157\x6e\72\40" . var_export($w5->localName, TRUE));
                exit;
        }
        Hwq:
        nKh:
        fUY:
        $w5 = $w5->nextSibling;
        goto Sug;
        sz6:
    }
    private function parseAuthnStatement(DOMElement $BY)
    {
        $LV = SAML2Utilities::xpQuery($BY, "\x2e\57\163\141\155\154\137\141\x73\x73\145\162\x74\x69\x6f\156\x3a\x41\x75\x74\x68\x6e\123\164\141\x74\145\155\145\156\x74");
        if (empty($LV)) {
            goto cUV;
        }
        if (count($LV) > 1) {
            goto FDq;
        }
        goto QHc;
        cUV:
        $this->authnInstant = NULL;
        return;
        goto QHc;
        FDq:
        print_r("\115\x6f\162\145\40\164\150\141\x74\x20\x6f\156\x65\40\74\163\141\x6d\x6c\72\101\x75\164\150\x6e\123\164\141\x74\x65\x6d\145\156\x74\76\40\x69\156\40\74\163\141\x6d\x6c\72\x41\163\163\x65\162\164\x69\x6f\156\76\x20\156\157\x74\x20\x73\165\160\x70\x6f\x72\164\x65\144\56");
        exit;
        QHc:
        $bg = $LV[0];
        if ($bg->hasAttribute("\x41\x75\164\150\x6e\x49\x6e\x73\x74\141\x6e\164")) {
            goto nA3;
        }
        print_r("\115\151\x73\x73\x69\156\x67\40\162\145\x71\x75\151\162\x65\144\40\x41\x75\x74\x68\156\111\x6e\163\164\x61\x6e\x74\x20\x61\x74\164\x72\x69\142\x75\x74\x65\x20\157\156\x20\74\163\x61\155\x6c\72\x41\165\164\150\156\123\x74\141\164\145\155\x65\x6e\164\x3e\x2e");
        exit;
        nA3:
        $this->authnInstant = SAML2Utilities::xsDateTimeToTimestamp($bg->getAttribute("\101\x75\x74\x68\x6e\111\x6e\163\x74\x61\156\164"));
        if (!$bg->hasAttribute("\x53\x65\163\163\151\x6f\x6e\116\x6f\164\x4f\x6e\117\162\101\146\x74\x65\x72")) {
            goto qNc;
        }
        $this->sessionNotOnOrAfter = SAML2Utilities::xsDateTimeToTimestamp($bg->getAttribute("\x53\x65\x73\163\x69\157\156\116\157\x74\117\x6e\117\162\x41\146\164\145\162"));
        qNc:
        if (!$bg->hasAttribute("\123\x65\163\163\151\x6f\156\111\x6e\x64\x65\170")) {
            goto NYL;
        }
        $this->sessionIndex = $bg->getAttribute("\x53\x65\163\163\151\157\156\111\156\144\x65\x78");
        NYL:
        $this->parseAuthnContext($bg);
    }
    private function parseAuthnContext(DOMElement $bZ)
    {
        $oe = SAML2Utilities::xpQuery($bZ, "\56\57\x73\141\155\154\x5f\x61\x73\x73\145\x72\x74\151\x6f\x6e\72\101\x75\164\150\156\x43\157\x6e\164\x65\x78\164");
        if (count($oe) > 1) {
            goto sIO;
        }
        if (empty($oe)) {
            goto KF1;
        }
        goto NLM;
        sIO:
        print_r("\115\x6f\162\145\40\x74\150\x61\x6e\x20\157\x6e\145\40\74\x73\141\155\x6c\72\x41\x75\164\x68\156\103\x6f\156\x74\x65\x78\164\x3e\x20\x69\156\40\x3c\163\141\x6d\x6c\72\101\x75\x74\x68\156\123\164\141\x74\x65\x6d\145\156\164\x3e\x2e");
        exit;
        goto NLM;
        KF1:
        print_r("\x4d\151\163\x73\x69\x6e\147\40\162\145\x71\x75\151\x72\145\144\x20\x3c\163\x61\155\x6c\72\101\x75\164\x68\x6e\x43\157\156\164\x65\x78\164\76\x20\151\156\x20\74\163\x61\155\154\x3a\101\165\x74\150\156\x53\164\141\164\145\x6d\x65\156\x74\x3e\56");
        exit;
        NLM:
        $HM = $oe[0];
        $pg = SAML2Utilities::xpQuery($HM, "\56\57\163\x61\x6d\x6c\x5f\141\163\163\x65\162\164\151\x6f\x6e\x3a\101\165\164\x68\x6e\x43\x6f\156\x74\x65\x78\x74\104\145\x63\154\x52\x65\146");
        if (count($pg) > 1) {
            goto SxP;
        }
        if (count($pg) === 1) {
            goto rMM;
        }
        goto GCI;
        SxP:
        print_r("\115\157\162\x65\40\x74\150\141\x6e\40\157\x6e\x65\x20\74\x73\141\155\154\72\x41\x75\x74\150\x6e\103\157\x6e\x74\x65\170\164\x44\145\143\x6c\122\145\x66\76\x20\146\157\x75\156\x64\77");
        exit;
        goto GCI;
        rMM:
        $this->setAuthnContextDeclRef(trim($pg[0]->textContent));
        GCI:
        $A5 = SAML2Utilities::xpQuery($HM, "\x2e\x2f\x73\141\155\154\x5f\141\x73\163\145\162\x74\151\157\x6e\x3a\101\x75\x74\x68\x6e\103\x6f\x6e\x74\145\170\164\104\145\143\154");
        if (count($A5) > 1) {
            goto Li9;
        }
        if (count($A5) === 1) {
            goto xzF;
        }
        goto eId;
        Li9:
        print_r("\x4d\x6f\162\x65\40\164\150\x61\x6e\x20\x6f\156\145\x20\74\163\x61\x6d\x6c\72\101\165\x74\150\x6e\103\x6f\x6e\164\x65\x78\164\104\x65\143\154\76\x20\146\157\x75\x6e\x64\77");
        exit;
        goto eId;
        xzF:
        $this->setAuthnContextDecl(new SAML2_XML_Chunk($A5[0]));
        eId:
        $jY = SAML2Utilities::xpQuery($HM, "\x2e\x2f\x73\141\x6d\x6c\137\141\x73\163\145\162\164\151\157\156\72\101\x75\164\150\x6e\x43\x6f\x6e\x74\145\170\164\x43\x6c\141\163\163\x52\145\146");
        if (count($jY) > 1) {
            goto E1E;
        }
        if (count($jY) === 1) {
            goto HjF;
        }
        goto Wqt;
        E1E:
        print_r("\x4d\157\x72\x65\x20\164\150\x61\156\40\157\156\x65\40\74\163\141\155\x6c\72\101\x75\x74\x68\156\103\x6f\x6e\164\x65\170\x74\x43\x6c\141\163\163\x52\145\146\x3e\x20\151\x6e\x20\x3c\163\x61\x6d\x6c\x3a\101\x75\164\150\156\x43\x6f\156\x74\x65\170\164\x3e\x2e");
        exit;
        goto Wqt;
        HjF:
        $this->setAuthnContextClassRef(trim($jY[0]->textContent));
        Wqt:
        if (!(empty($this->authnContextClassRef) && empty($this->authnContextDecl) && empty($this->authnContextDeclRef))) {
            goto OAv;
        }
        print_r("\x4d\151\x73\163\x69\156\147\x20\145\x69\164\150\x65\x72\x20\x3c\163\141\155\154\72\101\165\x74\x68\156\103\x6f\x6e\x74\x65\170\164\103\154\x61\163\163\122\145\x66\x3e\40\x6f\162\40\74\163\x61\x6d\x6c\72\x41\165\164\x68\x6e\103\x6f\x6e\x74\x65\170\164\x44\145\143\x6c\x52\145\x66\x3e\40\157\162\x20\74\163\141\x6d\154\x3a\x41\x75\x74\150\x6e\103\157\156\x74\145\x78\164\x44\145\143\154\76");
        exit;
        OAv:
        $this->AuthenticatingAuthority = SAML2Utilities::extractStrings($HM, "\x75\x72\156\x3a\157\141\x73\x69\x73\72\156\x61\155\x65\163\72\x74\143\72\x53\101\x4d\114\72\62\x2e\x30\x3a\x61\x73\x73\145\162\164\151\x6f\156", "\101\x75\x74\150\145\x6e\x74\x69\143\141\164\x69\x6e\147\x41\x75\x74\x68\157\x72\x69\164\x79");
    }
    private function parseAttributes(DOMElement $BY)
    {
        $PN = TRUE;
        $cS = SAML2Utilities::xpQuery($BY, "\56\x2f\x73\x61\x6d\x6c\x5f\141\163\x73\145\162\164\x69\x6f\x6e\72\x41\164\x74\x72\x69\x62\165\x74\145\x53\164\141\164\145\155\x65\x6e\164\57\x73\141\x6d\x6c\x5f\141\x73\x73\x65\162\164\x69\x6f\156\x3a\x41\164\164\162\x69\142\x75\164\x65");
        foreach ($cS as $e2) {
            if ($e2->hasAttribute("\x4e\141\x6d\145")) {
                goto FsP;
            }
            print_r("\115\151\x73\163\x69\x6e\147\x20\156\141\x6d\x65\x20\157\156\40\74\163\x61\155\154\72\101\164\164\x72\x69\x62\x75\x74\x65\x3e\x20\145\x6c\x65\155\145\x6e\x74\x2e");
            exit;
            FsP:
            $rb = $e2->getAttribute("\x4e\x61\x6d\145");
            if ($e2->hasAttribute("\116\141\155\145\x46\157\x72\155\141\164")) {
                goto dhJ;
            }
            $YF = "\165\x72\x6e\x3a\157\141\x73\151\163\x3a\156\141\x6d\145\x73\72\x74\x63\x3a\x53\x41\x4d\x4c\x3a\61\x2e\x31\x3a\x6e\x61\155\x65\151\144\x2d\x66\x6f\162\x6d\x61\x74\x3a\x75\156\163\x70\145\x63\151\146\151\x65\x64";
            goto tSW;
            dhJ:
            $YF = $e2->getAttribute("\116\141\x6d\145\x46\x6f\x72\155\141\164");
            tSW:
            if ($PN) {
                goto EDV;
            }
            if (!($this->nameFormat !== $YF)) {
                goto AzL;
            }
            $this->nameFormat = "\165\x72\x6e\72\157\x61\x73\151\x73\x3a\156\141\155\x65\x73\72\x74\143\72\123\101\x4d\x4c\72\61\56\61\x3a\x6e\x61\x6d\145\x69\144\55\146\157\x72\x6d\141\x74\72\x75\156\163\x70\x65\143\x69\x66\151\x65\144";
            AzL:
            goto us_;
            EDV:
            $this->nameFormat = $YF;
            $PN = FALSE;
            us_:
            if (array_key_exists($rb, $this->attributes)) {
                goto zm_;
            }
            $this->attributes[$rb] = array();
            zm_:
            $D7 = SAML2Utilities::xpQuery($e2, "\56\x2f\x73\x61\x6d\154\x5f\141\x73\163\x65\162\164\x69\x6f\156\72\101\x74\x74\x72\x69\x62\165\164\x65\126\x61\x6c\165\x65");
            foreach ($D7 as $Yk) {
                $this->attributes[$rb][] = trim($Yk->textContent);
                ESI:
            }
            lBy:
            Mg1:
        }
        xnv:
    }
    private function parseEncryptedAttributes(DOMElement $BY)
    {
        $this->encryptedAttribute = SAML2Utilities::xpQuery($BY, "\56\57\x73\x61\155\x6c\x5f\x61\x73\x73\x65\162\x74\151\x6f\156\72\101\164\164\162\151\142\x75\164\145\123\x74\141\x74\x65\155\145\156\x74\x2f\163\x61\x6d\x6c\x5f\141\163\163\145\x72\x74\x69\157\156\72\105\x6e\143\162\x79\160\x74\x65\144\x41\164\164\x72\x69\142\165\164\x65");
    }
    private function parseSignature(DOMElement $BY)
    {
        $GG = SAML2Utilities::validateElement($BY);
        if (!($GG !== FALSE)) {
            goto KDb;
        }
        $this->wasSignedAtConstruction = TRUE;
        $this->certificates = $GG["\103\145\162\x74\151\x66\151\143\141\164\145\x73"];
        $this->signatureData = $GG;
        KDb:
    }
    public function validate(XMLSecurityKey $zg)
    {
        if (!($this->signatureData === NULL)) {
            goto m9o;
        }
        return FALSE;
        m9o:
        SAML2Utilities::validateSignature($this->signatureData, $zg);
        return TRUE;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($lA)
    {
        $this->id = $lA;
    }
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }
    public function setIssueInstant($KQ)
    {
        $this->issueInstant = $KQ;
    }
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($wz)
    {
        $this->issuer = $wz;
    }
    public function getNameId()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto X51;
        }
        print_r("\x41\x74\164\145\155\160\164\145\x64\x20\x74\x6f\40\162\145\x74\162\x69\145\166\x65\x20\x65\156\x63\x72\171\x70\x74\x65\x64\40\116\x61\155\x65\111\x44\x20\167\151\164\150\x6f\165\164\x20\x64\145\x63\x72\x79\160\164\151\x6e\147\40\x69\164\40\146\x69\162\x73\164\x2e");
        exit;
        X51:
        return $this->nameId;
    }
    public function setNameId($Q5)
    {
        $this->nameId = $Q5;
    }
    public function isNameIdEncrypted()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto vZc;
        }
        return TRUE;
        vZc:
        return FALSE;
    }
    public function encryptNameId(XMLSecurityKey $zg)
    {
        $Dy = new DOMDocument();
        $N7 = $Dy->createElement("\x72\x6f\x6f\x74");
        $Dy->appendChild($N7);
        SAML2Utilities::addNameId($N7, $this->nameId);
        $Q5 = $N7->firstChild;
        SAML2Utilities::getContainer()->debugMessage($Q5, "\x65\156\143\x72\x79\160\x74");
        $Xb = new XMLSecEnc();
        $Xb->setNode($Q5);
        $Xb->type = XMLSecEnc::Element;
        $tb = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
        $tb->generateSessionKey();
        $Xb->encryptKey($zg, $tb);
        $this->encryptedNameId = $Xb->encryptNode($tb);
        $this->nameId = NULL;
    }
    public function decryptNameId(XMLSecurityKey $zg, array $KA = array())
    {
        if (!($this->encryptedNameId === NULL)) {
            goto Joh;
        }
        return;
        Joh:
        $Q5 = SAML2Utilities::decryptElement($this->encryptedNameId, $zg, $KA);
        SAML2Utilities::getContainer()->debugMessage($Q5, "\x64\145\x63\162\171\160\164");
        $this->nameId = SAML2Utilities::parseNameId($Q5);
        $this->encryptedNameId = NULL;
    }
    public function decryptAttributes(XMLSecurityKey $zg, array $KA = array())
    {
        if (!($this->encryptedAttribute === NULL)) {
            goto PPz;
        }
        return;
        PPz:
        $PN = TRUE;
        $cS = $this->encryptedAttribute;
        foreach ($cS as $Dq) {
            $e2 = SAML2Utilities::decryptElement($Dq->getElementsByTagName("\105\x6e\143\x72\x79\160\x74\145\144\x44\x61\x74\141")->item(0), $zg, $KA);
            if ($e2->hasAttribute("\116\141\155\x65")) {
                goto Pc2;
            }
            print_r("\x4d\151\163\163\x69\156\147\40\156\141\155\x65\40\x6f\156\40\x3c\163\x61\x6d\154\x3a\101\164\x74\x72\151\142\165\x74\145\76\x20\145\154\145\x6d\x65\156\x74\56");
            exit;
            Pc2:
            $rb = $e2->getAttribute("\x4e\141\x6d\145");
            if ($e2->hasAttribute("\116\141\x6d\x65\x46\157\x72\x6d\x61\x74")) {
                goto sAg;
            }
            $YF = "\165\x72\156\x3a\157\141\163\151\163\x3a\x6e\141\155\145\163\72\164\x63\72\123\101\115\114\x3a\62\56\60\72\141\164\x74\162\x6e\x61\155\145\55\x66\x6f\162\155\x61\164\x3a\x75\x6e\x73\160\145\x63\x69\146\151\145\144";
            goto YCk;
            sAg:
            $YF = $e2->getAttribute("\x4e\141\155\x65\106\x6f\162\155\141\164");
            YCk:
            if ($PN) {
                goto mIa;
            }
            if (!($this->nameFormat !== $YF)) {
                goto aVP;
            }
            $this->nameFormat = "\165\162\x6e\x3a\x6f\x61\x73\x69\x73\x3a\156\x61\x6d\145\163\72\164\143\72\x53\101\x4d\114\72\x32\x2e\x30\72\x61\x74\x74\162\x6e\x61\x6d\145\55\146\157\x72\155\141\164\x3a\165\156\163\160\145\x63\151\x66\151\145\144";
            aVP:
            goto NJM;
            mIa:
            $this->nameFormat = $YF;
            $PN = FALSE;
            NJM:
            if (array_key_exists($rb, $this->attributes)) {
                goto MUx;
            }
            $this->attributes[$rb] = array();
            MUx:
            $D7 = SAML2Utilities::xpQuery($e2, "\x2e\x2f\x73\141\x6d\154\x5f\141\x73\x73\x65\162\164\x69\157\x6e\x3a\101\164\x74\162\151\142\165\x74\145\x56\x61\x6c\x75\145");
            foreach ($D7 as $Yk) {
                $this->attributes[$rb][] = trim($Yk->textContent);
                br2:
            }
            xeo:
            xT2:
        }
        p26:
    }
    public function getNotBefore()
    {
        return $this->notBefore;
    }
    public function setNotBefore($TB)
    {
        $this->notBefore = $TB;
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($pz)
    {
        $this->notOnOrAfter = $pz;
    }
    public function setEncryptedAttributes($Gf)
    {
        $this->requiredEncAttributes = $Gf;
    }
    public function getValidAudiences()
    {
        return $this->validAudiences;
    }
    public function setValidAudiences(array $Ku = NULL)
    {
        $this->validAudiences = $Ku;
    }
    public function getAuthnInstant()
    {
        return $this->authnInstant;
    }
    public function setAuthnInstant($ig)
    {
        $this->authnInstant = $ig;
    }
    public function getSessionNotOnOrAfter()
    {
        return $this->sessionNotOnOrAfter;
    }
    public function setSessionNotOnOrAfter($fw)
    {
        $this->sessionNotOnOrAfter = $fw;
    }
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }
    public function setSessionIndex($SK)
    {
        $this->sessionIndex = $SK;
    }
    public function getAuthnContext()
    {
        if (empty($this->authnContextClassRef)) {
            goto lu7;
        }
        return $this->authnContextClassRef;
        lu7:
        if (empty($this->authnContextDeclRef)) {
            goto BaT;
        }
        return $this->authnContextDeclRef;
        BaT:
        return NULL;
    }
    public function setAuthnContext($dR)
    {
        $this->setAuthnContextClassRef($dR);
    }
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }
    public function setAuthnContextClassRef($KD)
    {
        $this->authnContextClassRef = $KD;
    }
    public function setAuthnContextDecl(SAML2_XML_Chunk $tA)
    {
        if (empty($this->authnContextDeclRef)) {
            goto fHD;
        }
        print_r("\101\165\164\150\156\x43\157\156\164\145\170\x74\104\x65\143\x6c\122\x65\146\40\x69\x73\x20\x61\x6c\x72\145\x61\x64\x79\x20\162\x65\147\151\x73\x74\145\162\x65\x64\41\x20\x4d\141\x79\x20\x6f\x6e\154\171\40\x68\141\x76\145\x20\x65\x69\x74\x68\145\x72\40\x61\40\x44\145\x63\x6c\x20\157\162\40\x61\x20\x44\145\x63\x6c\x52\x65\146\x2c\x20\x6e\x6f\x74\40\x62\157\164\x68\x21");
        exit;
        fHD:
        $this->authnContextDecl = $tA;
    }
    public function getAuthnContextDecl()
    {
        return $this->authnContextDecl;
    }
    public function setAuthnContextDeclRef($vb)
    {
        if (empty($this->authnContextDecl)) {
            goto pKp;
        }
        print_r("\x41\x75\x74\x68\x6e\103\157\156\164\145\170\164\104\145\143\154\x20\x69\163\40\x61\154\x72\x65\x61\x64\171\40\162\145\x67\x69\x73\164\145\x72\145\144\x21\40\115\x61\171\x20\157\156\x6c\x79\40\150\x61\166\145\40\x65\151\x74\x68\x65\162\x20\141\x20\104\x65\x63\154\x20\x6f\x72\40\141\40\104\x65\x63\x6c\122\145\146\54\40\x6e\157\164\40\142\157\164\x68\x21");
        exit;
        pKp:
        $this->authnContextDeclRef = $vb;
    }
    public function getAuthnContextDeclRef()
    {
        return $this->authnContextDeclRef;
    }
    public function getAuthenticatingAuthority()
    {
        return $this->AuthenticatingAuthority;
    }
    public function setAuthenticatingAuthority($ud)
    {
        $this->AuthenticatingAuthority = $ud;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes(array $cS)
    {
        $this->attributes = $cS;
    }
    public function getAttributeNameFormat()
    {
        return $this->nameFormat;
    }
    public function setAttributeNameFormat($YF)
    {
        $this->nameFormat = $YF;
    }
    public function getSubjectConfirmation()
    {
        return $this->SubjectConfirmation;
    }
    public function setSubjectConfirmation(array $f7)
    {
        $this->SubjectConfirmation = $f7;
    }
    public function getSignatureKey()
    {
        return $this->signatureKey;
    }
    public function getSignatureData()
    {
        return $this->signatureData;
    }
    public function setSignatureKey(XMLsecurityKey $r2 = NULL)
    {
        $this->signatureKey = $r2;
    }
    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }
    public function setEncryptionKey(XMLSecurityKey $eJ = NULL)
    {
        $this->encryptionKey = $eJ;
    }
    public function setCertificates(array $fp)
    {
        $this->certificates = $fp;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function getWasSignedAtConstruction()
    {
        return $this->wasSignedAtConstruction;
    }
    public function toXML(DOMNode $wn = NULL)
    {
        if ($wn === NULL) {
            goto nON;
        }
        $L5 = $wn->ownerDocument;
        goto N6c;
        nON:
        $L5 = new DOMDocument();
        $wn = $L5;
        N6c:
        $N7 = $L5->createElementNS("\x75\x72\156\72\157\141\163\x69\163\72\156\x61\x6d\145\x73\72\x74\143\72\123\101\115\x4c\x3a\x32\x2e\60\x3a\141\163\163\x65\x72\164\x69\157\156", "\163\141\x6d\154\x3a" . "\x41\163\x73\145\x72\x74\x69\x6f\x6e");
        $wn->appendChild($N7);
        $N7->setAttributeNS("\x75\162\156\x3a\x6f\x61\163\151\x73\72\156\141\x6d\145\163\72\164\x63\72\x53\101\115\x4c\72\x32\56\x30\72\x70\162\157\164\157\x63\157\154", "\163\141\x6d\154\x70\72\164\155\160", "\x74\155\160");
        $N7->removeAttributeNS("\x75\x72\x6e\72\157\x61\163\x69\163\72\156\141\x6d\x65\x73\72\x74\143\x3a\123\x41\x4d\x4c\x3a\62\56\60\x3a\x70\x72\x6f\x74\x6f\x63\157\154", "\x74\x6d\160");
        $N7->setAttributeNS("\x68\164\164\160\72\57\57\167\167\167\56\x77\x33\x2e\157\162\147\57\x32\60\x30\x31\57\x58\115\x4c\x53\143\x68\145\x6d\141\55\x69\156\163\x74\141\x6e\x63\x65", "\170\163\x69\72\164\x6d\160", "\164\x6d\160");
        $N7->removeAttributeNS("\x68\x74\164\160\72\57\x2f\x77\x77\x77\x2e\x77\63\x2e\x6f\162\147\57\x32\60\x30\x31\x2f\130\x4d\114\x53\143\150\145\155\x61\x2d\151\156\x73\x74\x61\x6e\x63\x65", "\164\x6d\160");
        $N7->setAttributeNS("\150\x74\x74\160\72\57\x2f\167\x77\167\x2e\167\63\x2e\157\162\147\57\62\60\x30\x31\57\130\x4d\x4c\x53\x63\x68\145\x6d\x61", "\x78\163\72\x74\155\x70", "\x74\x6d\x70");
        $N7->removeAttributeNS("\150\x74\164\160\72\57\57\x77\x77\x77\x2e\167\x33\56\x6f\x72\147\57\62\60\60\61\57\130\115\x4c\x53\143\x68\145\x6d\141", "\x74\155\160");
        $N7->setAttribute("\111\104", $this->id);
        $N7->setAttribute("\126\145\162\x73\151\x6f\x6e", "\62\x2e\60");
        $N7->setAttribute("\111\x73\163\165\x65\111\156\x73\x74\x61\x6e\x74", gmdate("\131\55\x6d\55\x64\134\x54\x48\x3a\151\x3a\x73\x5c\x5a", $this->issueInstant));
        $wz = SAML2Utilities::addString($N7, "\x75\162\156\x3a\x6f\x61\163\151\163\72\x6e\x61\x6d\x65\x73\72\x74\x63\72\x53\101\115\x4c\x3a\x32\56\60\x3a\141\163\x73\145\162\164\x69\x6f\156", "\x73\141\x6d\154\x3a\x49\163\163\165\x65\x72", $this->issuer);
        $this->addSubject($N7);
        $this->addConditions($N7);
        $this->addAuthnStatement($N7);
        if ($this->requiredEncAttributes == FALSE) {
            goto TP7;
        }
        $this->addEncryptedAttributeStatement($N7);
        goto gpq;
        TP7:
        $this->addAttributeStatement($N7);
        gpq:
        if (!($this->signatureKey !== NULL)) {
            goto uNk;
        }
        SAML2Utilities::insertSignature($this->signatureKey, $this->certificates, $N7, $wz->nextSibling);
        uNk:
        return $N7;
    }
    private function addSubject(DOMElement $N7)
    {
        if (!($this->nameId === NULL && $this->encryptedNameId === NULL)) {
            goto yDS;
        }
        return;
        yDS:
        $XP = $N7->ownerDocument->createElementNS("\165\x72\x6e\72\x6f\x61\163\x69\163\72\156\141\155\145\163\x3a\x74\143\x3a\x53\101\115\x4c\x3a\62\x2e\x30\x3a\141\163\163\x65\162\164\x69\157\x6e", "\163\141\x6d\x6c\72\x53\x75\x62\152\145\143\164");
        $N7->appendChild($XP);
        if ($this->encryptedNameId === NULL) {
            goto fUC;
        }
        $Eo = $XP->ownerDocument->createElementNS("\x75\x72\156\x3a\157\141\163\x69\163\x3a\x6e\x61\155\x65\x73\x3a\x74\x63\x3a\x53\x41\115\x4c\x3a\x32\x2e\x30\72\141\163\x73\x65\x72\164\151\157\x6e", "\163\x61\155\154\72" . "\x45\x6e\143\x72\x79\x70\164\x65\144\x49\104");
        $XP->appendChild($Eo);
        $Eo->appendChild($XP->ownerDocument->importNode($this->encryptedNameId, TRUE));
        goto wfE;
        fUC:
        SAML2Utilities::addNameId($XP, $this->nameId);
        wfE:
        foreach ($this->SubjectConfirmation as $L_) {
            $L_->toXML($XP);
            Pmw:
        }
        toN:
    }
    private function addConditions(DOMElement $N7)
    {
        $L5 = $N7->ownerDocument;
        $Ey = $L5->createElementNS("\x75\x72\x6e\x3a\157\141\x73\x69\x73\72\156\x61\x6d\145\163\72\164\x63\x3a\x53\101\x4d\x4c\x3a\62\56\60\x3a\141\x73\x73\x65\x72\164\x69\157\x6e", "\163\x61\155\154\72\x43\157\156\x64\x69\x74\151\157\x6e\163");
        $N7->appendChild($Ey);
        if (!($this->notBefore !== NULL)) {
            goto mO0;
        }
        $Ey->setAttribute("\x4e\157\x74\102\145\x66\x6f\x72\145", gmdate("\x59\55\x6d\55\144\134\x54\x48\72\x69\x3a\163\x5c\132", $this->notBefore));
        mO0:
        if (!($this->notOnOrAfter !== NULL)) {
            goto gTD;
        }
        $Ey->setAttribute("\116\157\x74\x4f\156\117\162\x41\146\x74\x65\x72", gmdate("\131\55\x6d\x2d\144\x5c\x54\110\x3a\x69\72\163\x5c\x5a", $this->notOnOrAfter));
        gTD:
        if (!($this->validAudiences !== NULL)) {
            goto vwd;
        }
        $qO = $L5->createElementNS("\x75\x72\x6e\72\x6f\141\163\151\x73\x3a\156\x61\x6d\x65\x73\x3a\x74\x63\x3a\123\x41\x4d\114\x3a\x32\x2e\60\72\141\x73\x73\145\x72\164\x69\x6f\x6e", "\x73\141\x6d\x6c\72\x41\x75\144\151\145\156\143\x65\122\145\163\164\162\x69\143\x74\151\157\156");
        $Ey->appendChild($qO);
        SAML2Utilities::addStrings($qO, "\165\x72\156\x3a\157\x61\163\151\163\72\x6e\x61\x6d\x65\163\72\x74\x63\72\123\101\115\x4c\72\62\x2e\x30\72\141\163\163\x65\162\164\151\x6f\x6e", "\x73\x61\x6d\x6c\x3a\x41\165\x64\x69\145\156\x63\x65", FALSE, $this->validAudiences);
        vwd:
    }
    private function addAuthnStatement(DOMElement $N7)
    {
        if (!($this->authnInstant === NULL || $this->authnContextClassRef === NULL && $this->authnContextDecl === NULL && $this->authnContextDeclRef === NULL)) {
            goto O1E;
        }
        return;
        O1E:
        $L5 = $N7->ownerDocument;
        $bZ = $L5->createElementNS("\165\162\156\72\157\141\x73\151\163\72\156\141\x6d\x65\x73\72\164\x63\x3a\123\101\x4d\114\x3a\62\56\60\72\x61\163\163\145\x72\164\151\x6f\x6e", "\163\141\x6d\x6c\x3a\x41\165\164\x68\x6e\123\x74\x61\x74\145\x6d\x65\156\x74");
        $N7->appendChild($bZ);
        $bZ->setAttribute("\x41\x75\164\150\x6e\x49\156\163\164\x61\x6e\x74", gmdate("\x59\55\155\55\x64\134\x54\110\x3a\x69\x3a\163\134\132", $this->authnInstant));
        if (!($this->sessionNotOnOrAfter !== NULL)) {
            goto k1Q;
        }
        $bZ->setAttribute("\123\x65\163\163\151\157\x6e\x4e\x6f\164\117\x6e\117\162\101\146\164\145\x72", gmdate("\x59\55\x6d\x2d\x64\x5c\124\x48\x3a\151\x3a\x73\134\x5a", $this->sessionNotOnOrAfter));
        k1Q:
        if (!($this->sessionIndex !== NULL)) {
            goto sB3;
        }
        $bZ->setAttribute("\123\x65\x73\163\151\157\x6e\111\156\144\145\170", $this->sessionIndex);
        sB3:
        $HM = $L5->createElementNS("\x75\x72\x6e\x3a\157\x61\163\x69\x73\72\x6e\x61\155\x65\x73\72\164\143\x3a\123\101\x4d\x4c\x3a\x32\56\x30\x3a\x61\163\163\145\162\164\151\x6f\x6e", "\163\x61\155\x6c\72\x41\165\x74\150\156\103\x6f\156\164\x65\170\x74");
        $bZ->appendChild($HM);
        if (empty($this->authnContextClassRef)) {
            goto Grk;
        }
        SAML2Utilities::addString($HM, "\x75\x72\156\x3a\157\x61\163\x69\163\x3a\x6e\141\x6d\x65\x73\x3a\x74\143\72\123\101\x4d\x4c\x3a\x32\56\60\x3a\x61\163\x73\x65\162\164\151\157\156", "\x73\x61\155\x6c\x3a\x41\x75\164\150\156\x43\x6f\156\164\x65\x78\164\x43\154\x61\163\x73\x52\145\x66", $this->authnContextClassRef);
        Grk:
        if (empty($this->authnContextDecl)) {
            goto Rp9;
        }
        $this->authnContextDecl->toXML($HM);
        Rp9:
        if (empty($this->authnContextDeclRef)) {
            goto j37;
        }
        SAML2Utilities::addString($HM, "\x75\162\x6e\72\157\x61\x73\x69\x73\72\x6e\x61\155\x65\163\x3a\x74\143\72\123\x41\115\114\x3a\62\x2e\x30\72\x61\x73\163\x65\162\164\x69\157\x6e", "\x73\x61\x6d\x6c\72\x41\x75\x74\x68\x6e\103\157\156\x74\x65\170\x74\104\145\x63\x6c\x52\145\x66", $this->authnContextDeclRef);
        j37:
        SAML2Utilities::addStrings($HM, "\x75\162\156\72\x6f\141\163\x69\x73\x3a\x6e\141\155\x65\163\x3a\164\143\72\123\101\x4d\x4c\x3a\62\56\x30\x3a\x61\x73\x73\x65\162\164\x69\157\x6e", "\163\x61\155\154\72\x41\x75\164\x68\x65\x6e\164\151\143\x61\164\151\156\x67\x41\x75\164\150\x6f\x72\151\164\171", FALSE, $this->AuthenticatingAuthority);
    }
    private function addAttributeStatement(DOMElement $N7)
    {
        if (!empty($this->attributes)) {
            goto E_a;
        }
        return;
        E_a:
        $L5 = $N7->ownerDocument;
        $hi = $L5->createElementNS("\x75\x72\x6e\x3a\157\141\163\x69\x73\x3a\156\141\155\x65\x73\72\164\x63\72\x53\x41\x4d\114\72\62\x2e\60\72\141\x73\163\x65\x72\x74\151\157\x6e", "\x73\141\155\154\72\x41\164\x74\162\x69\142\x75\x74\145\123\x74\x61\164\x65\x6d\x65\156\x74");
        $N7->appendChild($hi);
        foreach ($this->attributes as $rb => $D7) {
            $e2 = $L5->createElementNS("\x75\162\156\x3a\157\141\x73\151\x73\x3a\156\x61\x6d\x65\x73\x3a\x74\143\72\x53\x41\115\114\72\62\x2e\x30\72\141\x73\x73\x65\x72\x74\151\x6f\x6e", "\x73\141\x6d\154\x3a\101\164\x74\162\x69\142\x75\164\x65");
            $hi->appendChild($e2);
            $e2->setAttribute("\x4e\x61\155\x65", $rb);
            if (!($this->nameFormat !== "\x75\x72\156\72\x6f\141\x73\x69\x73\x3a\156\x61\x6d\145\163\72\164\143\x3a\x53\101\x4d\114\72\x32\x2e\x30\72\141\164\x74\x72\156\x61\x6d\145\x2d\146\157\x72\155\x61\164\x3a\x75\156\163\x70\145\x63\151\146\151\145\144")) {
                goto GKn;
            }
            $e2->setAttribute("\116\x61\x6d\145\x46\x6f\x72\155\141\x74", $this->nameFormat);
            GKn:
            foreach ($D7 as $Yk) {
                if (is_string($Yk)) {
                    goto UpD;
                }
                if (is_int($Yk)) {
                    goto BIJ;
                }
                $Nv = NULL;
                goto hUI;
                UpD:
                $Nv = "\x78\x73\x3a\x73\x74\x72\x69\156\x67";
                goto hUI;
                BIJ:
                $Nv = "\x78\163\x3a\151\156\x74\145\x67\x65\x72";
                hUI:
                $ru = $L5->createElementNS("\x75\x72\156\x3a\157\141\x73\151\163\72\156\x61\x6d\x65\x73\72\164\143\x3a\x53\101\x4d\114\x3a\x32\56\x30\72\x61\163\163\145\x72\164\151\x6f\156", "\x73\x61\155\x6c\72\x41\x74\164\162\151\x62\165\x74\x65\x56\x61\x6c\165\x65");
                $e2->appendChild($ru);
                if (!($Nv !== NULL)) {
                    goto oB9;
                }
                $ru->setAttributeNS("\150\x74\164\x70\x3a\x2f\57\167\x77\x77\x2e\x77\63\56\157\x72\147\57\62\x30\60\x31\x2f\130\115\114\123\x63\x68\145\x6d\x61\x2d\x69\156\x73\164\x61\x6e\x63\x65", "\x78\163\x69\72\164\171\160\x65", $Nv);
                oB9:
                if (!is_null($Yk)) {
                    goto H02;
                }
                $ru->setAttributeNS("\x68\x74\x74\160\72\x2f\x2f\167\x77\167\x2e\x77\x33\56\157\x72\x67\x2f\62\x30\x30\61\57\x58\115\x4c\x53\143\x68\145\155\141\x2d\x69\x6e\163\x74\141\x6e\x63\145", "\170\x73\x69\72\x6e\151\x6c", "\164\x72\165\x65");
                H02:
                if ($Yk instanceof DOMNodeList) {
                    goto LBU;
                }
                $ru->appendChild($L5->createTextNode($Yk));
                goto UMk;
                LBU:
                $X5 = 0;
                IQ1:
                if (!($X5 < $Yk->length)) {
                    goto mlP;
                }
                $w5 = $L5->importNode($Yk->item($X5), TRUE);
                $ru->appendChild($w5);
                HDc:
                $X5++;
                goto IQ1;
                mlP:
                UMk:
                Y91:
            }
            E2A:
            lv2:
        }
        Ttv:
    }
    private function addEncryptedAttributeStatement(DOMElement $N7)
    {
        if (!($this->requiredEncAttributes == FALSE)) {
            goto xab;
        }
        return;
        xab:
        $L5 = $N7->ownerDocument;
        $hi = $L5->createElementNS("\x75\162\x6e\72\157\141\163\x69\163\72\156\141\155\145\163\72\164\143\72\123\101\115\x4c\72\62\x2e\x30\72\x61\163\163\145\x72\x74\x69\x6f\x6e", "\163\141\155\154\72\101\x74\x74\162\151\x62\165\x74\145\x53\164\x61\x74\x65\155\145\156\164");
        $N7->appendChild($hi);
        foreach ($this->attributes as $rb => $D7) {
            $tc = new DOMDocument();
            $e2 = $tc->createElementNS("\165\x72\156\72\x6f\141\x73\151\x73\72\156\x61\155\x65\x73\x3a\x74\x63\x3a\x53\x41\115\x4c\72\x32\56\60\72\141\163\x73\145\x72\x74\x69\x6f\156", "\x73\141\x6d\154\72\101\x74\164\162\x69\142\165\x74\x65");
            $e2->setAttribute("\116\141\155\145", $rb);
            $tc->appendChild($e2);
            if (!($this->nameFormat !== "\165\x72\156\72\157\x61\163\x69\163\72\156\x61\x6d\145\x73\72\x74\x63\72\x53\x41\x4d\114\x3a\x32\56\60\72\x61\x74\x74\x72\x6e\141\155\x65\x2d\146\x6f\162\155\141\x74\72\165\156\163\x70\145\x63\151\146\151\145\144")) {
                goto rOU;
            }
            $e2->setAttribute("\116\x61\155\x65\x46\x6f\x72\155\x61\x74", $this->nameFormat);
            rOU:
            foreach ($D7 as $Yk) {
                if (is_string($Yk)) {
                    goto BhO;
                }
                if (is_int($Yk)) {
                    goto zhG;
                }
                $Nv = NULL;
                goto nDh;
                BhO:
                $Nv = "\x78\163\72\x73\x74\162\151\156\x67";
                goto nDh;
                zhG:
                $Nv = "\x78\163\72\x69\x6e\x74\145\x67\x65\162";
                nDh:
                $ru = $tc->createElementNS("\x75\x72\x6e\72\157\141\163\151\x73\72\156\x61\155\145\x73\72\164\x63\72\x53\101\115\x4c\x3a\x32\56\60\x3a\141\x73\163\145\x72\x74\151\157\x6e", "\x73\141\x6d\154\72\x41\164\x74\162\x69\x62\x75\x74\145\x56\x61\154\x75\x65");
                $e2->appendChild($ru);
                if (!($Nv !== NULL)) {
                    goto EoS;
                }
                $ru->setAttributeNS("\x68\x74\164\160\72\x2f\57\x77\x77\x77\x2e\167\63\x2e\x6f\x72\x67\x2f\62\x30\60\x31\57\130\x4d\x4c\x53\143\150\x65\155\x61\x2d\151\x6e\163\x74\141\x6e\143\x65", "\x78\163\151\x3a\164\x79\160\145", $Nv);
                EoS:
                if ($Yk instanceof DOMNodeList) {
                    goto B3h;
                }
                $ru->appendChild($tc->createTextNode($Yk));
                goto Hr_;
                B3h:
                $X5 = 0;
                S3l:
                if (!($X5 < $Yk->length)) {
                    goto Fns;
                }
                $w5 = $tc->importNode($Yk->item($X5), TRUE);
                $ru->appendChild($w5);
                CPH:
                $X5++;
                goto S3l;
                Fns:
                Hr_:
                KHI:
            }
            Am4:
            $s8 = new XMLSecEnc();
            $s8->setNode($tc->documentElement);
            $s8->type = "\150\x74\x74\x70\x3a\x2f\57\167\167\167\x2e\167\63\56\x6f\x72\x67\x2f\x32\x30\x30\x31\57\x30\x34\x2f\x78\x6d\x6c\x65\x6e\x63\x23\105\x6c\145\155\x65\x6e\164";
            $tb = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
            $tb->generateSessionKey();
            $s8->encryptKey($this->encryptionKey, $tb);
            $xJ = $s8->encryptNode($tb);
            $rx = $L5->createElementNS("\165\x72\x6e\72\x6f\x61\163\151\163\x3a\156\x61\155\145\163\72\x74\x63\x3a\x53\x41\x4d\x4c\72\x32\x2e\60\72\141\163\163\x65\162\x74\151\157\x6e", "\x73\x61\x6d\154\x3a\x45\x6e\x63\162\x79\x70\x74\145\x64\101\x74\x74\162\151\x62\165\x74\x65");
            $hi->appendChild($rx);
            $UM = $L5->importNode($xJ, TRUE);
            $rx->appendChild($UM);
            zVp:
        }
        Qnj:
    }
}
