<?php


namespace MiniOrange\SP\Helper\Saml2;

use DOMElement;
use DOMText;
use Exception;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityKey;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
class SAML2Assertion
{
    public $key;
    protected $wasSignedAtConstruction = FALSE;
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
    public function __construct(\DOMElement $xa = NULL, SPUtility $fR)
    {
        $this->id = SAML2Utilities::generateId();
        $this->issueInstant = SAML2Utilities::generateTimestamp();
        $this->issuer = '';
        $this->authnInstant = SAML2Utilities::generateTimestamp();
        $this->attributes = array();
        $this->nameFormat = "\165\x72\156\72\x6f\141\x73\151\163\x3a\x6e\141\155\145\163\72\164\143\72\x53\101\x4d\x4c\x3a\x31\56\61\72\156\x61\x6d\x65\151\x64\x2d\x66\x6f\162\155\141\164\x3a\165\x6e\x73\160\145\x63\151\146\x69\145\144";
        $this->certificates = array();
        $this->AuthenticatingAuthority = array();
        $this->SubjectConfirmation = array();
        $this->spUtility = $fR;
        if (!($xa === NULL)) {
            goto ZB;
        }
        return;
        ZB:
        if (!($xa->localName === "\105\x6e\x63\x72\x79\160\164\145\x64\101\x73\163\x65\162\x74\x69\157\x6e")) {
            goto wn;
        }
        $or = SAML2Utilities::xpQuery($xa, "\56\x2f\170\145\156\x63\72\105\x6e\x63\162\x79\160\x74\145\144\x44\x61\x74\141");
        $y_ = SAML2Utilities::xpQuery($xa, "\x2e\x2f\170\145\156\x63\72\x45\156\143\162\171\160\x74\x65\144\x44\141\164\x61\x2f\144\x73\x3a\113\145\171\111\156\146\157\57\x78\x65\156\x63\x3a\105\x6e\x63\x72\x79\x70\x74\145\x64\x4b\145\171");
        $vh = '';
        if (empty($y_)) {
            goto xV;
        }
        $vh = $y_[0]->firstChild->getAttribute("\101\x6c\x67\157\x72\x69\164\150\x6d");
        goto p8;
        xV:
        $y_ = SAML2Utilities::xpQuery($xa, "\x2e\57\170\x65\156\143\72\105\156\x63\162\x79\160\x74\145\144\113\x65\171\x2f\x78\145\156\143\x3a\105\156\x63\162\x79\x70\164\151\157\156\x4d\x65\164\x68\157\x64");
        $vh = $y_[0]->getAttribute("\x41\x6c\147\x6f\x72\151\164\150\155");
        p8:
        $Vl = SAML2Utilities::getEncryptionAlgorithm($vh);
        if (count($or) === 0) {
            goto WW;
        }
        if (count($or) > 1) {
            goto Z6;
        }
        goto iA;
        WW:
        print_r("\115\x69\x73\x73\151\x6e\147\x20\x65\x6e\x63\162\x79\160\164\x65\144\40\144\141\x74\x61\x20\x69\156\x20\74\163\141\x6d\x6c\72\x45\156\x63\162\x79\x70\164\x65\x64\x41\163\x73\x65\162\164\x69\x6f\156\76\x2e");
        exit;
        goto iA;
        Z6:
        print_r("\115\157\162\145\x20\x74\150\x61\x6e\40\x6f\x6e\x65\40\x65\x6e\143\162\x79\160\164\x65\144\40\x64\x61\164\x61\40\145\154\x65\x6d\x65\x6e\x74\x20\151\156\40\x3c\163\x61\155\154\x3a\105\156\143\162\171\x70\x74\x65\x64\x41\x73\x73\x65\162\164\x69\x6f\156\76\56");
        exit;
        iA:
        $Cv = array("\x74\x79\160\145" => "\160\x72\151\x76\x61\164\145");
        $On = new XMLSecurityKey($Vl, $Cv);
        $up = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));
        $On->loadKey($up);
        $cz = array();
        $xa = SAML2Utilities::decryptElement($or[0], $On, $cz);
        wn:
        if ($xa->hasAttribute("\x49\104")) {
            goto P2;
        }
        print_r("\115\x69\163\163\x69\156\147\40\x49\104\x20\x61\164\x74\x72\x69\142\x75\x74\145\x20\x6f\x6e\40\123\101\x4d\x4c\x20\141\x73\x73\145\162\164\151\x6f\x6e\56");
        exit;
        P2:
        $this->id = $xa->getAttribute("\x49\104");
        if (!($xa->getAttribute("\x56\x65\x72\163\x69\157\x6e") !== "\62\56\x30")) {
            goto h1;
        }
        print_r("\125\x6e\x73\165\x70\160\x6f\x72\x74\145\144\40\x76\145\162\x73\x69\157\156\72\x20" . $xa->getAttribute("\126\145\x72\x73\151\x6f\156"));
        exit;
        h1:
        $this->issueInstant = SAML2Utilities::xsDateTimeToTimestamp($xa->getAttribute("\111\163\163\x75\x65\x49\x6e\163\164\x61\156\164"));
        $wQ = SAML2Utilities::xpQuery($xa, "\x2e\57\x73\x61\155\x6c\137\x61\163\163\x65\162\x74\151\x6f\x6e\x3a\x49\x73\163\x75\x65\x72");
        if (!empty($wQ)) {
            goto XA;
        }
        print_r("\115\151\x73\x73\x69\156\147\40\74\163\141\x6d\154\72\111\x73\x73\x75\x65\x72\76\x20\151\156\40\141\x73\x73\x65\162\164\151\157\x6e\x2e");
        exit;
        XA:
        $this->issuer = trim($wQ[0]->textContent);
        $this->parseConditions($xa);
        $this->parseAuthnStatement($xa);
        $this->parseAttributes($xa);
        $this->parseEncryptedAttributes($xa);
        $this->parseSignature($xa);
        $this->parseSubject($xa);
    }
    private function parseConditions(DOMElement $xa)
    {
        $lL = SAML2Utilities::xpQuery($xa, "\x2e\x2f\x73\x61\x6d\154\137\141\163\x73\145\x72\164\151\157\156\x3a\103\157\x6e\144\x69\x74\x69\x6f\156\x73");
        if (empty($lL)) {
            goto f7;
        }
        if (count($lL) > 1) {
            goto CG;
        }
        goto DL;
        f7:
        return;
        goto DL;
        CG:
        print_r("\x4d\157\x72\145\x20\x74\150\141\x6e\x20\157\156\145\x20\74\x73\141\x6d\x6c\72\103\x6f\156\x64\151\x74\151\157\x6e\x73\76\x20\x69\156\x20\74\163\x61\x6d\154\x3a\101\x73\163\x65\x72\x74\x69\157\x6e\x3e\56");
        exit;
        DL:
        $lL = $lL[0];
        if (!$lL->hasAttribute("\116\x6f\164\102\145\146\x6f\162\145")) {
            goto yv;
        }
        $Qq = SAML2Utilities::xsDateTimeToTimestamp($lL->getAttribute("\x4e\x6f\x74\102\x65\x66\157\162\x65"));
        if (!($this->notBefore === NULL || $this->notBefore < $Qq)) {
            goto I1;
        }
        $this->notBefore = $Qq;
        I1:
        yv:
        if (!$lL->hasAttribute("\x4e\x6f\164\x4f\156\x4f\162\x41\x66\x74\145\162")) {
            goto Fo;
        }
        $Pc = SAML2Utilities::xsDateTimeToTimestamp($lL->getAttribute("\x4e\157\x74\x4f\x6e\x4f\162\x41\146\164\x65\x72"));
        if (!($this->notOnOrAfter === NULL || $this->notOnOrAfter > $Pc)) {
            goto Rr;
        }
        $this->notOnOrAfter = $Pc;
        Rr:
        Fo:
        $zN = $lL->firstChild;
        Be:
        if (!($zN !== NULL)) {
            goto Mq;
        }
        if (!$zN instanceof DOMText) {
            goto HB;
        }
        goto zq;
        HB:
        if (!($zN->namespaceURI !== "\x75\162\156\x3a\157\141\x73\x69\x73\72\156\x61\x6d\x65\163\x3a\x74\143\72\x53\101\115\114\72\x32\56\x30\x3a\141\163\163\145\162\164\151\x6f\156")) {
            goto YA;
        }
        print_r("\125\x6e\x6b\156\x6f\167\156\40\x6e\141\155\x65\x73\160\141\143\x65\40\157\x66\40\143\157\x6e\x64\151\164\151\x6f\x6e\x3a\40" . var_export($zN->namespaceURI, TRUE));
        exit;
        YA:
        switch ($zN->localName) {
            case "\x41\x75\x64\151\x65\156\x63\145\122\x65\163\164\x72\151\143\x74\151\x6f\156":
                $Ly = SAML2Utilities::extractStrings($zN, "\165\x72\x6e\x3a\157\x61\163\151\163\72\156\x61\x6d\x65\x73\72\164\143\x3a\123\x41\x4d\114\72\62\56\60\72\x61\163\163\145\x72\164\x69\157\156", "\x41\165\144\151\x65\x6e\143\145");
                if ($this->validAudiences === NULL) {
                    goto lX;
                }
                $this->validAudiences = array_intersect($this->validAudiences, $Ly);
                goto br;
                lX:
                $this->validAudiences = $Ly;
                br:
                goto f1;
            case "\117\156\x65\124\x69\x6d\145\x55\x73\145":
                goto f1;
            case "\x50\162\157\170\171\122\145\163\164\x72\x69\x63\164\x69\x6f\x6e":
                goto f1;
            default:
                print_r("\125\x6e\x6b\x6e\157\x77\156\x20\143\x6f\156\144\x69\x74\151\x6f\x6e\x3a\x20" . var_export($zN->localName, TRUE));
                exit;
        }
        yB:
        f1:
        zq:
        $zN = $zN->nextSibling;
        goto Be;
        Mq:
    }
    private function parseAuthnStatement(DOMElement $xa)
    {
        $TC = SAML2Utilities::xpQuery($xa, "\56\x2f\163\x61\155\x6c\x5f\x61\x73\163\145\x72\164\151\x6f\x6e\x3a\101\165\164\x68\x6e\123\164\x61\x74\145\155\x65\156\x74");
        if (empty($TC)) {
            goto Vi;
        }
        if (count($TC) > 1) {
            goto jO;
        }
        goto UG;
        Vi:
        $this->authnInstant = NULL;
        return;
        goto UG;
        jO:
        print_r("\115\x6f\x72\x65\40\x74\150\x61\x74\40\x6f\x6e\145\40\x3c\x73\x61\155\x6c\72\101\x75\x74\x68\156\123\x74\x61\x74\x65\x6d\x65\x6e\164\76\40\151\x6e\x20\74\x73\x61\155\154\72\101\163\x73\145\x72\x74\151\157\x6e\x3e\x20\x6e\157\164\40\x73\x75\x70\160\157\x72\x74\x65\144\x2e");
        exit;
        UG:
        $ih = $TC[0];
        if ($ih->hasAttribute("\x41\x75\x74\150\x6e\x49\x6e\x73\164\x61\x6e\x74")) {
            goto Rv;
        }
        print_r("\x4d\151\163\163\151\156\147\40\x72\x65\x71\165\151\x72\145\x64\x20\101\165\x74\150\x6e\111\156\163\x74\141\x6e\164\40\141\164\164\x72\x69\x62\x75\x74\x65\x20\x6f\156\40\x3c\x73\141\155\154\x3a\101\x75\x74\150\x6e\123\164\x61\164\x65\x6d\x65\x6e\x74\x3e\56");
        exit;
        Rv:
        $this->authnInstant = SAML2Utilities::xsDateTimeToTimestamp($ih->getAttribute("\101\165\x74\150\x6e\x49\156\x73\164\141\x6e\164"));
        if (!$ih->hasAttribute("\x53\x65\163\x73\151\x6f\156\x4e\157\164\117\x6e\x4f\162\x41\146\x74\x65\162")) {
            goto G1;
        }
        $this->sessionNotOnOrAfter = SAML2Utilities::xsDateTimeToTimestamp($ih->getAttribute("\123\145\163\163\x69\x6f\x6e\116\x6f\x74\x4f\156\117\x72\101\x66\164\x65\x72"));
        G1:
        if (!$ih->hasAttribute("\123\145\x73\x73\x69\157\x6e\111\156\144\x65\x78")) {
            goto Mh;
        }
        $this->sessionIndex = $ih->getAttribute("\x53\x65\163\163\151\157\156\x49\156\144\145\170");
        Mh:
        $this->parseAuthnContext($ih);
    }
    private function parseAuthnContext(DOMElement $h8)
    {
        $mR = SAML2Utilities::xpQuery($h8, "\56\x2f\x73\x61\x6d\x6c\137\141\163\x73\145\162\x74\151\157\x6e\72\101\165\x74\x68\x6e\103\157\156\x74\145\x78\x74");
        if (count($mR) > 1) {
            goto sk;
        }
        if (empty($mR)) {
            goto Di;
        }
        goto ho;
        sk:
        print_r("\x4d\157\162\145\x20\164\x68\141\156\x20\x6f\x6e\145\40\x3c\x73\x61\155\x6c\x3a\x41\165\x74\x68\156\103\x6f\156\164\x65\170\164\x3e\40\x69\156\x20\74\x73\x61\155\x6c\72\101\165\164\x68\x6e\x53\x74\x61\164\145\x6d\145\x6e\164\76\56");
        exit;
        goto ho;
        Di:
        print_r("\115\151\163\x73\x69\156\x67\40\x72\145\161\165\151\x72\x65\144\x20\74\x73\141\x6d\154\x3a\101\x75\x74\x68\156\x43\x6f\x6e\164\145\x78\164\76\40\151\156\x20\x3c\163\x61\155\154\x3a\101\165\164\x68\x6e\x53\x74\x61\x74\x65\155\x65\x6e\164\x3e\56");
        exit;
        ho:
        $zV = $mR[0];
        $vy = SAML2Utilities::xpQuery($zV, "\x2e\57\x73\x61\x6d\154\137\141\x73\x73\x65\x72\164\151\157\x6e\x3a\101\165\x74\150\x6e\103\157\156\x74\145\170\x74\104\x65\143\x6c\x52\145\146");
        if (count($vy) > 1) {
            goto af;
        }
        if (count($vy) === 1) {
            goto O6;
        }
        goto Jh;
        af:
        print_r("\x4d\157\162\x65\40\164\x68\x61\156\40\x6f\156\145\40\x3c\163\141\155\x6c\72\101\x75\164\150\x6e\103\x6f\156\164\x65\170\x74\104\145\x63\x6c\x52\x65\146\x3e\x20\146\x6f\165\x6e\144\77");
        exit;
        goto Jh;
        O6:
        $this->setAuthnContextDeclRef(trim($vy[0]->textContent));
        Jh:
        $ei = SAML2Utilities::xpQuery($zV, "\56\x2f\x73\141\155\154\137\x61\163\x73\145\162\x74\151\x6f\x6e\x3a\x41\165\164\x68\x6e\x43\x6f\156\x74\x65\x78\164\x44\145\x63\154");
        if (count($ei) > 1) {
            goto Id;
        }
        if (count($ei) === 1) {
            goto cf;
        }
        goto iE;
        Id:
        print_r("\115\157\162\145\x20\164\150\x61\156\40\x6f\156\x65\x20\x3c\163\x61\155\154\72\101\165\x74\150\156\x43\x6f\x6e\164\x65\x78\x74\x44\145\143\154\76\x20\x66\x6f\165\x6e\x64\x3f");
        exit;
        goto iE;
        cf:
        $this->setAuthnContextDecl(new SAML2_XML_Chunk($ei[0]));
        iE:
        $jL = SAML2Utilities::xpQuery($zV, "\56\x2f\163\141\x6d\x6c\137\x61\163\x73\145\162\164\x69\157\x6e\x3a\x41\165\x74\x68\x6e\103\x6f\156\164\x65\x78\164\x43\154\x61\163\163\x52\145\146");
        if (count($jL) > 1) {
            goto SA;
        }
        if (count($jL) === 1) {
            goto W9;
        }
        goto Se;
        SA:
        print_r("\x4d\157\162\145\40\x74\x68\x61\x6e\x20\x6f\156\x65\40\x3c\163\x61\x6d\x6c\72\101\x75\164\150\x6e\x43\x6f\x6e\x74\145\x78\164\103\x6c\x61\163\x73\x52\x65\146\76\x20\151\x6e\x20\74\x73\141\155\x6c\72\x41\165\x74\150\x6e\103\157\156\164\145\170\164\x3e\x2e");
        exit;
        goto Se;
        W9:
        $this->setAuthnContextClassRef(trim($jL[0]->textContent));
        Se:
        if (!(empty($this->authnContextClassRef) && empty($this->authnContextDecl) && empty($this->authnContextDeclRef))) {
            goto Pu;
        }
        print_r("\x4d\151\x73\x73\151\156\x67\40\145\x69\164\150\x65\x72\40\x3c\163\141\x6d\x6c\x3a\101\165\x74\150\156\x43\x6f\x6e\x74\145\x78\164\103\x6c\141\163\163\122\x65\146\76\40\x6f\162\40\74\163\141\155\154\x3a\101\x75\x74\x68\156\x43\x6f\x6e\164\145\170\164\104\x65\x63\154\122\145\146\x3e\40\157\162\40\x3c\x73\x61\155\154\x3a\101\165\x74\x68\156\103\x6f\x6e\x74\145\170\164\104\x65\143\x6c\76");
        exit;
        Pu:
        $this->AuthenticatingAuthority = SAML2Utilities::extractStrings($zV, "\165\162\x6e\x3a\157\141\163\151\163\72\x6e\141\x6d\x65\x73\72\164\143\x3a\x53\101\x4d\114\x3a\62\56\60\72\141\163\x73\145\x72\x74\151\x6f\156", "\101\165\164\150\x65\x6e\164\x69\x63\141\164\151\156\147\101\165\164\x68\x6f\162\x69\164\x79");
    }
    private function parseAttributes(DOMElement $xa)
    {
        $bO = TRUE;
        $Gg = SAML2Utilities::xpQuery($xa, "\x2e\57\x73\141\155\x6c\x5f\141\163\x73\x65\162\164\151\157\156\72\x41\x74\x74\x72\151\x62\x75\x74\x65\x53\x74\x61\164\145\155\145\x6e\164\57\163\x61\x6d\154\x5f\141\x73\x73\x65\x72\164\151\157\156\x3a\x41\x74\x74\x72\x69\142\x75\x74\x65");
        foreach ($Gg as $cX) {
            if ($cX->hasAttribute("\116\141\155\145")) {
                goto RU;
            }
            print_r("\115\x69\163\163\x69\x6e\x67\x20\x6e\141\155\x65\40\x6f\x6e\x20\74\163\141\x6d\154\72\x41\x74\164\x72\x69\142\x75\x74\x65\76\40\x65\x6c\x65\x6d\x65\156\164\56");
            exit;
            RU:
            $yt = $cX->getAttribute("\116\x61\155\x65");
            if ($cX->hasAttribute("\116\141\155\145\x46\157\x72\x6d\x61\164")) {
                goto Hn;
            }
            $U4 = "\165\x72\x6e\x3a\157\141\x73\x69\163\72\x6e\141\x6d\x65\163\x3a\164\x63\x3a\123\101\115\x4c\x3a\x31\x2e\61\72\x6e\x61\x6d\145\151\144\55\x66\x6f\162\155\x61\x74\x3a\x75\x6e\163\x70\x65\143\x69\146\151\x65\144";
            goto pi;
            Hn:
            $U4 = $cX->getAttribute("\116\141\155\x65\106\x6f\162\155\x61\x74");
            pi:
            if ($bO) {
                goto lQ;
            }
            if (!($this->nameFormat !== $U4)) {
                goto Vf;
            }
            $this->nameFormat = "\x75\162\x6e\x3a\x6f\x61\163\151\x73\72\156\x61\155\145\x73\x3a\164\x63\72\123\101\115\x4c\x3a\61\x2e\61\x3a\156\141\x6d\x65\151\x64\55\146\x6f\x72\x6d\x61\x74\72\165\x6e\x73\x70\x65\x63\x69\146\x69\145\144";
            Vf:
            goto Qy;
            lQ:
            $this->nameFormat = $U4;
            $bO = FALSE;
            Qy:
            if (array_key_exists($yt, $this->attributes)) {
                goto Yl;
            }
            $this->attributes[$yt] = array();
            Yl:
            $jT = SAML2Utilities::xpQuery($cX, "\56\x2f\163\141\155\x6c\x5f\141\163\163\145\x72\164\151\x6f\x6e\72\101\164\x74\x72\151\142\x75\164\x65\126\x61\x6c\x75\x65");
            foreach ($jT as $VP) {
                $this->attributes[$yt][] = trim($VP->textContent);
                JQ:
            }
            hf:
            fc:
        }
        PF:
    }
    private function parseEncryptedAttributes(DOMElement $xa)
    {
        $this->encryptedAttribute = SAML2Utilities::xpQuery($xa, "\x2e\x2f\x73\141\x6d\x6c\137\x61\x73\x73\145\162\x74\x69\x6f\156\x3a\x41\x74\x74\162\151\142\x75\x74\145\x53\x74\141\164\145\155\x65\156\164\x2f\163\x61\x6d\x6c\x5f\x61\163\x73\145\x72\x74\x69\157\x6e\x3a\105\x6e\x63\162\x79\x70\x74\145\x64\101\164\x74\x72\x69\142\x75\164\x65");
    }
    private function parseSignature(DOMElement $xa)
    {
        $Ud = SAML2Utilities::validateElement($xa);
        if (!($Ud !== FALSE)) {
            goto hH;
        }
        $this->wasSignedAtConstruction = TRUE;
        $this->certificates = $Ud["\103\x65\x72\x74\151\146\x69\x63\x61\x74\x65\163"];
        $this->signatureData = $Ud;
        hH:
    }
    private function parseSubject(DOMElement $xa)
    {
        $fH = SAML2Utilities::xpQuery($xa, "\x2e\57\163\141\155\154\137\x61\163\x73\x65\x72\x74\x69\157\x6e\x3a\123\x75\x62\152\x65\143\164");
        if (empty($fH)) {
            goto XZ;
        }
        if (count($fH) > 1) {
            goto vp;
        }
        goto Y2;
        XZ:
        return;
        goto Y2;
        vp:
        print_r("\115\157\x72\x65\x20\x74\x68\x61\156\x20\157\156\145\40\74\x73\x61\155\x6c\x3a\x53\165\142\152\145\143\164\76\40\x69\x6e\40\x3c\x73\x61\155\154\x3a\101\x73\163\x65\162\164\x69\x6f\x6e\x3e\56");
        exit;
        Y2:
        $fH = $fH[0];
        $Au = SAML2Utilities::xpQuery($fH, "\56\x2f\163\x61\x6d\x6c\137\x61\163\163\x65\x72\x74\x69\157\156\72\116\x61\x6d\x65\111\x44\40\x7c\40\56\x2f\x73\141\x6d\154\137\141\x73\x73\x65\x72\x74\151\x6f\x6e\72\105\x6e\x63\162\x79\x70\164\x65\144\x49\104\57\x78\x65\156\x63\x3a\x45\x6e\x63\162\171\x70\164\145\x64\104\x61\x74\141");
        if (empty($Au)) {
            goto w0;
        }
        if (count($Au) > 1) {
            goto dn;
        }
        goto OL;
        w0:
        print_r("\115\151\x73\163\x69\156\147\40\x3c\163\141\155\x6c\x3a\x4e\141\x6d\145\111\x44\x3e\40\157\x72\40\74\163\141\x6d\x6c\x3a\x45\x6e\x63\162\x79\x70\164\145\144\x49\x44\x3e\40\151\x6e\x20\x3c\163\x61\155\154\x3a\x53\x75\142\152\145\x63\x74\76\56");
        exit;
        goto OL;
        dn:
        print_r("\x4d\157\162\x65\40\x74\150\141\156\x20\x6f\x6e\145\40\74\x73\141\155\x6c\72\116\x61\x6d\145\111\104\x3e\x20\x6f\162\x20\74\x73\141\155\154\72\105\156\143\162\x79\160\x74\145\x64\104\76\x20\151\x6e\x20\x3c\x73\141\x6d\x6c\x3a\123\165\142\152\145\x63\x74\x3e\x2e");
        exit;
        OL:
        $Au = $Au[0];
        if ($Au->localName === "\x45\x6e\143\x72\171\x70\x74\145\x64\104\141\164\141") {
            goto Xd;
        }
        $this->nameId = SAML2Utilities::parseNameId($Au);
        goto yO;
        Xd:
        $this->encryptedNameId = $Au;
        yO:
    }
    public function validate(XMLSecurityKey $On)
    {
        if (!($this->signatureData === NULL)) {
            goto EV;
        }
        return FALSE;
        EV:
        SAML2Utilities::validateSignature($this->signatureData, $On);
        return TRUE;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($Gh)
    {
        $this->id = $Gh;
    }
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }
    public function setIssueInstant($Tf)
    {
        $this->issueInstant = $Tf;
    }
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($wQ)
    {
        $this->issuer = $wQ;
    }
    public function getNameId()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto kY;
        }
        print_r("\x41\x74\164\x65\x6d\x70\x74\145\x64\40\164\x6f\x20\x72\x65\x74\x72\x69\145\166\145\40\x65\156\143\x72\171\x70\x74\x65\144\x20\116\141\x6d\x65\111\104\x20\167\x69\164\x68\x6f\x75\164\40\144\x65\x63\162\x79\160\164\151\x6e\x67\40\151\164\x20\146\151\x72\x73\x74\x2e");
        exit;
        kY:
        return $this->nameId;
    }
    public function setNameId($Au)
    {
        $this->nameId = $Au;
    }
    public function isNameIdEncrypted()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto pf;
        }
        return TRUE;
        pf:
        return FALSE;
    }
    public function encryptNameId(XMLSecurityKey $On)
    {
        $st = new DOMDocument();
        $OC = $st->createElement("\x72\x6f\157\x74");
        $st->appendChild($OC);
        SAML2Utilities::addNameId($OC, $this->nameId);
        $Au = $OC->firstChild;
        SAML2Utilities::getContainer()->debugMessage($Au, "\x65\156\143\162\171\160\x74");
        $nj = new XMLSecEnc();
        $nj->setNode($Au);
        $nj->type = XMLSecEnc::Element;
        $ep = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
        $ep->generateSessionKey();
        $nj->encryptKey($On, $ep);
        $this->encryptedNameId = $nj->encryptNode($ep);
        $this->nameId = NULL;
    }
    public function decryptNameId(XMLSecurityKey $On, array $cz = array())
    {
        if (!($this->encryptedNameId === NULL)) {
            goto fa;
        }
        return;
        fa:
        $Au = SAML2Utilities::decryptElement($this->encryptedNameId, $On, $cz);
        SAML2Utilities::getContainer()->debugMessage($Au, "\x64\x65\x63\x72\x79\x70\164");
        $this->nameId = SAML2Utilities::parseNameId($Au);
        $this->encryptedNameId = NULL;
    }
    public function decryptAttributes(XMLSecurityKey $On, array $cz = array())
    {
        if (!($this->encryptedAttribute === NULL)) {
            goto HN;
        }
        return;
        HN:
        $bO = TRUE;
        $Gg = $this->encryptedAttribute;
        foreach ($Gg as $D6) {
            $cX = SAML2Utilities::decryptElement($D6->getElementsByTagName("\105\156\x63\x72\x79\160\x74\x65\144\104\141\x74\x61")->item(0), $On, $cz);
            if ($cX->hasAttribute("\x4e\141\x6d\145")) {
                goto AK;
            }
            print_r("\x4d\x69\163\163\151\x6e\x67\40\156\x61\155\145\40\x6f\x6e\x20\x3c\x73\141\155\x6c\72\101\164\164\162\151\142\165\x74\x65\x3e\x20\x65\x6c\x65\155\x65\x6e\164\56");
            exit;
            AK:
            $yt = $cX->getAttribute("\x4e\x61\x6d\145");
            if ($cX->hasAttribute("\116\x61\155\x65\106\x6f\x72\x6d\x61\x74")) {
                goto vg;
            }
            $U4 = "\165\x72\x6e\72\157\x61\x73\151\x73\x3a\156\x61\x6d\x65\x73\72\x74\x63\x3a\x53\101\x4d\114\x3a\62\x2e\60\72\x61\164\x74\x72\156\x61\155\145\55\146\157\162\x6d\x61\164\x3a\x75\156\163\160\x65\x63\151\146\151\x65\144";
            goto jt;
            vg:
            $U4 = $cX->getAttribute("\116\141\x6d\145\106\x6f\162\155\141\x74");
            jt:
            if ($bO) {
                goto Ef;
            }
            if (!($this->nameFormat !== $U4)) {
                goto I_;
            }
            $this->nameFormat = "\165\x72\156\x3a\157\x61\x73\x69\x73\72\156\x61\x6d\x65\x73\72\164\x63\x3a\x53\x41\x4d\114\x3a\x32\x2e\60\72\x61\164\x74\162\156\x61\x6d\145\x2d\x66\157\162\155\x61\x74\72\x75\156\163\x70\145\143\151\146\151\x65\144";
            I_:
            goto o6;
            Ef:
            $this->nameFormat = $U4;
            $bO = FALSE;
            o6:
            if (array_key_exists($yt, $this->attributes)) {
                goto KC;
            }
            $this->attributes[$yt] = array();
            KC:
            $jT = SAML2Utilities::xpQuery($cX, "\x2e\x2f\163\141\155\154\x5f\x61\163\x73\x65\x72\x74\151\x6f\156\72\101\164\164\x72\151\142\165\164\x65\x56\x61\x6c\x75\145");
            foreach ($jT as $VP) {
                $this->attributes[$yt][] = trim($VP->textContent);
                OS:
            }
            VM:
            JO:
        }
        Tx:
    }
    public function getNotBefore()
    {
        return $this->notBefore;
    }
    public function setNotBefore($Qq)
    {
        $this->notBefore = $Qq;
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($Pc)
    {
        $this->notOnOrAfter = $Pc;
    }
    public function setEncryptedAttributes($ox)
    {
        $this->requiredEncAttributes = $ox;
    }
    public function getValidAudiences()
    {
        return $this->validAudiences;
    }
    public function setValidAudiences(array $PE = NULL)
    {
        $this->validAudiences = $PE;
    }
    public function getAuthnInstant()
    {
        return $this->authnInstant;
    }
    public function setAuthnInstant($co)
    {
        $this->authnInstant = $co;
    }
    public function getSessionNotOnOrAfter()
    {
        return $this->sessionNotOnOrAfter;
    }
    public function setSessionNotOnOrAfter($y7)
    {
        $this->sessionNotOnOrAfter = $y7;
    }
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }
    public function setSessionIndex($lr)
    {
        $this->sessionIndex = $lr;
    }
    public function getAuthnContext()
    {
        if (empty($this->authnContextClassRef)) {
            goto T8;
        }
        return $this->authnContextClassRef;
        T8:
        if (empty($this->authnContextDeclRef)) {
            goto T3;
        }
        return $this->authnContextDeclRef;
        T3:
        return NULL;
    }
    public function setAuthnContext($ZC)
    {
        $this->setAuthnContextClassRef($ZC);
    }
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }
    public function setAuthnContextClassRef($k9)
    {
        $this->authnContextClassRef = $k9;
    }
    public function getAuthnContextDecl()
    {
        return $this->authnContextDecl;
    }
    public function setAuthnContextDecl(SAML2_XML_Chunk $bA)
    {
        if (empty($this->authnContextDeclRef)) {
            goto nH;
        }
        print_r("\x41\x75\x74\x68\x6e\x43\x6f\x6e\164\145\170\164\x44\145\x63\x6c\122\x65\146\40\x69\x73\x20\141\154\x72\145\141\x64\171\x20\162\x65\x67\x69\x73\164\145\x72\145\x64\41\40\115\x61\x79\40\157\156\154\171\x20\x68\x61\166\145\x20\145\x69\164\x68\x65\162\x20\141\x20\104\x65\143\154\x20\x6f\x72\40\141\40\104\145\143\154\122\145\x66\x2c\40\x6e\157\164\40\142\157\x74\150\x21");
        exit;
        nH:
        $this->authnContextDecl = $bA;
    }
    public function getAuthnContextDeclRef()
    {
        return $this->authnContextDeclRef;
    }
    public function setAuthnContextDeclRef($nP)
    {
        if (empty($this->authnContextDecl)) {
            goto OV;
        }
        print_r("\101\x75\164\x68\156\103\x6f\156\164\145\170\164\x44\x65\x63\154\40\x69\163\40\141\154\x72\x65\141\144\171\40\162\145\147\x69\163\164\x65\x72\x65\144\41\40\115\x61\171\x20\157\156\x6c\x79\x20\150\141\x76\145\x20\x65\151\164\x68\145\162\x20\x61\40\104\x65\x63\154\x20\157\x72\x20\x61\x20\x44\x65\143\154\122\x65\146\x2c\x20\x6e\x6f\164\40\x62\157\164\150\x21");
        exit;
        OV:
        $this->authnContextDeclRef = $nP;
    }
    public function getAuthenticatingAuthority()
    {
        return $this->AuthenticatingAuthority;
    }
    public function setAuthenticatingAuthority($O6)
    {
        $this->AuthenticatingAuthority = $O6;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes(array $Gg)
    {
        $this->attributes = $Gg;
    }
    public function getAttributeNameFormat()
    {
        return $this->nameFormat;
    }
    public function setAttributeNameFormat($U4)
    {
        $this->nameFormat = $U4;
    }
    public function getSubjectConfirmation()
    {
        return $this->SubjectConfirmation;
    }
    public function setSubjectConfirmation(array $T3)
    {
        $this->SubjectConfirmation = $T3;
    }
    public function getSignatureKey()
    {
        return $this->signatureKey;
    }
    public function setSignatureKey(XMLsecurityKey $ag = NULL)
    {
        $this->signatureKey = $ag;
    }
    public function getSignatureData()
    {
        return $this->signatureData;
    }
    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }
    public function setEncryptionKey(XMLSecurityKey $JP = NULL)
    {
        $this->encryptionKey = $JP;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function setCertificates(array $T4)
    {
        $this->certificates = $T4;
    }
    public function getWasSignedAtConstruction()
    {
        return $this->wasSignedAtConstruction;
    }
    public function toXML(DOMNode $Nl = NULL)
    {
        if ($Nl === NULL) {
            goto tc;
        }
        $rT = $Nl->ownerDocument;
        goto Y8;
        tc:
        $rT = new DOMDocument();
        $Nl = $rT;
        Y8:
        $OC = $rT->createElementNS("\x75\162\x6e\x3a\157\141\x73\x69\163\x3a\x6e\141\155\x65\x73\72\x74\143\x3a\123\x41\115\x4c\72\62\x2e\60\72\141\163\x73\145\162\x74\151\x6f\x6e", "\x73\x61\155\x6c\72" . "\101\163\163\x65\x72\164\151\x6f\x6e");
        $Nl->appendChild($OC);
        $OC->setAttributeNS("\x75\x72\156\x3a\157\141\163\151\x73\x3a\x6e\x61\x6d\x65\163\x3a\x74\143\x3a\123\101\115\114\72\62\x2e\60\x3a\160\162\x6f\164\157\x63\x6f\154", "\x73\141\x6d\x6c\x70\72\x74\155\160", "\164\x6d\160");
        $OC->removeAttributeNS("\x75\162\156\72\x6f\141\x73\151\x73\72\156\141\155\145\163\72\x74\x63\x3a\123\x41\115\x4c\72\x32\x2e\x30\x3a\160\x72\x6f\164\157\x63\157\154", "\164\155\160");
        $OC->setAttributeNS("\x68\164\164\x70\72\57\57\x77\167\167\x2e\167\x33\56\157\162\x67\57\x32\60\60\x31\x2f\x58\115\114\x53\x63\150\x65\x6d\x61\x2d\x69\156\163\x74\141\x6e\x63\x65", "\170\163\x69\72\x74\155\x70", "\x74\155\160");
        $OC->removeAttributeNS("\x68\x74\x74\x70\72\x2f\57\167\x77\x77\56\x77\63\56\157\x72\x67\x2f\x32\x30\x30\61\x2f\130\115\114\123\x63\150\145\x6d\141\55\151\156\163\x74\141\156\x63\x65", "\x74\x6d\x70");
        $OC->setAttributeNS("\150\164\x74\x70\x3a\57\x2f\167\x77\167\56\167\x33\x2e\157\162\x67\x2f\x32\x30\60\61\x2f\130\115\114\123\x63\150\145\155\x61", "\x78\x73\72\164\155\160", "\164\x6d\160");
        $OC->removeAttributeNS("\x68\164\164\x70\72\57\x2f\167\x77\x77\x2e\167\x33\x2e\157\x72\147\57\62\60\x30\61\x2f\130\115\x4c\x53\x63\150\145\155\x61", "\x74\155\160");
        $OC->setAttribute("\x49\x44", $this->id);
        $OC->setAttribute("\126\145\x72\163\151\157\x6e", "\x32\56\x30");
        $OC->setAttribute("\111\163\163\165\x65\111\x6e\163\x74\x61\156\164", gmdate("\x59\55\155\x2d\144\x5c\124\x48\72\x69\72\163\134\132", $this->issueInstant));
        $wQ = SAML2Utilities::addString($OC, "\165\x72\156\72\157\x61\163\151\x73\72\x6e\141\155\x65\163\x3a\x74\x63\x3a\123\x41\x4d\x4c\72\x32\x2e\60\72\141\163\163\x65\162\164\x69\157\156", "\163\x61\155\x6c\72\x49\x73\163\x75\x65\162", $this->issuer);
        $this->addSubject($OC);
        $this->addConditions($OC);
        $this->addAuthnStatement($OC);
        if ($this->requiredEncAttributes == FALSE) {
            goto Bi;
        }
        $this->addEncryptedAttributeStatement($OC);
        goto da;
        Bi:
        $this->addAttributeStatement($OC);
        da:
        if (!($this->signatureKey !== NULL)) {
            goto Ml;
        }
        SAML2Utilities::insertSignature($this->signatureKey, $this->certificates, $OC, $wQ->nextSibling);
        Ml:
        return $OC;
    }
    private function addSubject(DOMElement $OC)
    {
        if (!($this->nameId === NULL && $this->encryptedNameId === NULL)) {
            goto Fd;
        }
        return;
        Fd:
        $fH = $OC->ownerDocument->createElementNS("\165\162\156\72\157\x61\x73\x69\x73\72\156\x61\x6d\x65\x73\72\164\x63\x3a\x53\x41\x4d\x4c\x3a\x32\x2e\60\x3a\141\x73\x73\145\x72\x74\151\x6f\156", "\163\141\155\x6c\72\x53\165\142\x6a\x65\143\164");
        $OC->appendChild($fH);
        if ($this->encryptedNameId === NULL) {
            goto mx;
        }
        $Q5 = $fH->ownerDocument->createElementNS("\165\162\x6e\72\157\x61\x73\151\x73\72\x6e\x61\155\145\163\72\x74\x63\72\x53\101\x4d\114\x3a\62\x2e\60\x3a\x61\x73\163\145\162\x74\151\x6f\156", "\x73\x61\155\x6c\x3a" . "\x45\x6e\x63\x72\x79\160\164\145\144\111\x44");
        $fH->appendChild($Q5);
        $Q5->appendChild($fH->ownerDocument->importNode($this->encryptedNameId, TRUE));
        goto XI;
        mx:
        SAML2Utilities::addNameId($fH, $this->nameId);
        XI:
        foreach ($this->SubjectConfirmation as $Ef) {
            $Ef->toXML($fH);
            FW:
        }
        t3:
    }
    private function addConditions(DOMElement $OC)
    {
        $rT = $OC->ownerDocument;
        $lL = $rT->createElementNS("\x75\162\x6e\x3a\x6f\141\163\x69\x73\x3a\x6e\141\x6d\x65\x73\72\164\x63\x3a\123\x41\115\x4c\x3a\62\56\60\72\141\163\x73\145\162\164\x69\157\x6e", "\163\x61\155\154\72\x43\x6f\x6e\x64\x69\x74\151\x6f\x6e\163");
        $OC->appendChild($lL);
        if (!($this->notBefore !== NULL)) {
            goto Cg;
        }
        $lL->setAttribute("\x4e\x6f\x74\102\145\146\157\x72\x65", gmdate("\x59\55\x6d\55\x64\134\124\110\x3a\x69\72\163\x5c\132", $this->notBefore));
        Cg:
        if (!($this->notOnOrAfter !== NULL)) {
            goto uS;
        }
        $lL->setAttribute("\x4e\157\x74\x4f\156\x4f\x72\101\x66\x74\145\162", gmdate("\131\x2d\155\x2d\144\134\x54\x48\x3a\151\72\163\134\x5a", $this->notOnOrAfter));
        uS:
        if (!($this->validAudiences !== NULL)) {
            goto zf;
        }
        $Wo = $rT->createElementNS("\165\162\156\72\157\141\x73\151\163\x3a\156\141\x6d\145\163\x3a\x74\143\72\x53\x41\x4d\114\x3a\62\56\60\72\141\163\163\145\162\164\151\157\x6e", "\x73\x61\x6d\154\x3a\101\x75\x64\151\145\x6e\x63\x65\x52\145\x73\x74\x72\x69\x63\164\x69\157\156");
        $lL->appendChild($Wo);
        SAML2Utilities::addStrings($Wo, "\x75\162\156\72\157\x61\x73\151\163\x3a\x6e\141\x6d\145\x73\x3a\x74\x63\x3a\x53\x41\115\x4c\72\x32\x2e\60\72\141\x73\163\145\x72\164\x69\x6f\x6e", "\x73\x61\x6d\x6c\72\x41\165\x64\x69\x65\156\143\145", FALSE, $this->validAudiences);
        zf:
    }
    private function addAuthnStatement(DOMElement $OC)
    {
        if (!($this->authnInstant === NULL || $this->authnContextClassRef === NULL && $this->authnContextDecl === NULL && $this->authnContextDeclRef === NULL)) {
            goto ha;
        }
        return;
        ha:
        $rT = $OC->ownerDocument;
        $h8 = $rT->createElementNS("\x75\x72\x6e\72\157\x61\163\x69\x73\72\x6e\141\x6d\x65\163\x3a\164\x63\x3a\123\x41\115\x4c\72\x32\56\60\x3a\x61\x73\163\x65\162\x74\151\157\156", "\x73\x61\x6d\154\72\x41\165\x74\x68\156\123\x74\x61\x74\x65\155\x65\x6e\164");
        $OC->appendChild($h8);
        $h8->setAttribute("\x41\165\164\x68\156\111\156\x73\164\141\x6e\x74", gmdate("\131\55\155\x2d\144\134\x54\x48\x3a\151\x3a\163\x5c\x5a", $this->authnInstant));
        if (!($this->sessionNotOnOrAfter !== NULL)) {
            goto Zi;
        }
        $h8->setAttribute("\x53\145\163\163\x69\157\x6e\116\x6f\164\117\x6e\117\x72\x41\146\164\x65\162", gmdate("\x59\x2d\x6d\x2d\144\x5c\x54\x48\72\151\x3a\x73\x5c\132", $this->sessionNotOnOrAfter));
        Zi:
        if (!($this->sessionIndex !== NULL)) {
            goto d0;
        }
        $h8->setAttribute("\x53\x65\163\163\x69\157\x6e\111\156\144\x65\x78", $this->sessionIndex);
        d0:
        $zV = $rT->createElementNS("\165\162\156\x3a\x6f\141\163\x69\163\72\x6e\141\x6d\145\163\72\164\143\72\x53\101\115\x4c\72\x32\56\x30\x3a\141\163\x73\145\162\164\x69\157\x6e", "\163\x61\x6d\x6c\x3a\101\x75\164\x68\x6e\103\x6f\156\x74\145\x78\164");
        $h8->appendChild($zV);
        if (empty($this->authnContextClassRef)) {
            goto W2;
        }
        SAML2Utilities::addString($zV, "\165\162\156\72\x6f\x61\x73\x69\163\x3a\x6e\141\155\145\163\x3a\x74\x63\72\x53\101\115\114\72\62\x2e\x30\x3a\x61\163\x73\x65\x72\x74\x69\x6f\156", "\163\x61\x6d\x6c\x3a\101\165\164\x68\156\x43\157\156\164\x65\x78\164\x43\154\x61\x73\163\x52\145\x66", $this->authnContextClassRef);
        W2:
        if (empty($this->authnContextDecl)) {
            goto e3;
        }
        $this->authnContextDecl->toXML($zV);
        e3:
        if (empty($this->authnContextDeclRef)) {
            goto gA;
        }
        SAML2Utilities::addString($zV, "\x75\162\x6e\72\157\x61\x73\x69\163\x3a\x6e\x61\x6d\x65\x73\72\x74\143\x3a\123\x41\x4d\114\72\62\x2e\60\x3a\141\x73\x73\x65\162\164\151\157\156", "\163\x61\x6d\x6c\x3a\x41\x75\x74\150\x6e\103\157\156\x74\x65\x78\164\x44\145\143\154\122\145\x66", $this->authnContextDeclRef);
        gA:
        SAML2Utilities::addStrings($zV, "\165\162\156\x3a\157\141\x73\151\x73\72\x6e\141\x6d\145\x73\72\x74\x63\x3a\x53\x41\115\x4c\x3a\x32\x2e\x30\72\141\x73\163\x65\162\x74\151\157\x6e", "\163\141\x6d\x6c\x3a\x41\x75\164\x68\145\156\164\x69\143\141\x74\151\x6e\147\101\165\x74\150\x6f\162\151\x74\171", FALSE, $this->AuthenticatingAuthority);
    }
    private function addAttributeStatement(DOMElement $OC)
    {
        if (!empty($this->attributes)) {
            goto uF;
        }
        return;
        uF:
        $rT = $OC->ownerDocument;
        $jq = $rT->createElementNS("\x75\162\x6e\x3a\x6f\x61\x73\151\163\72\156\x61\155\x65\163\72\x74\x63\x3a\123\101\x4d\x4c\x3a\62\x2e\60\x3a\141\163\163\145\162\164\151\x6f\156", "\x73\141\x6d\x6c\72\101\x74\164\x72\x69\142\165\164\145\123\164\141\164\145\x6d\145\156\x74");
        $OC->appendChild($jq);
        foreach ($this->attributes as $yt => $jT) {
            $cX = $rT->createElementNS("\x75\162\x6e\x3a\157\141\163\151\163\72\x6e\141\x6d\x65\x73\x3a\x74\143\x3a\x53\x41\x4d\x4c\x3a\62\x2e\x30\x3a\141\x73\163\145\x72\164\151\157\x6e", "\x73\141\155\154\x3a\101\x74\164\x72\151\x62\165\x74\x65");
            $jq->appendChild($cX);
            $cX->setAttribute("\116\x61\x6d\x65", $yt);
            if (!($this->nameFormat !== "\165\x72\156\72\157\x61\163\x69\x73\x3a\x6e\x61\x6d\x65\163\x3a\164\x63\72\x53\x41\x4d\114\x3a\x32\x2e\x30\x3a\x61\x74\164\162\156\x61\x6d\145\55\x66\157\162\x6d\141\x74\72\165\x6e\x73\x70\x65\143\x69\146\151\x65\144")) {
                goto xO;
            }
            $cX->setAttribute("\116\x61\155\145\x46\x6f\x72\x6d\141\x74", $this->nameFormat);
            xO:
            foreach ($jT as $VP) {
                if (is_string($VP)) {
                    goto Gg;
                }
                if (is_int($VP)) {
                    goto dx;
                }
                $qI = NULL;
                goto LN;
                Gg:
                $qI = "\170\x73\x3a\x73\164\x72\x69\x6e\x67";
                goto LN;
                dx:
                $qI = "\170\x73\x3a\151\156\x74\145\x67\145\162";
                LN:
                $Ba = $rT->createElementNS("\165\x72\x6e\x3a\x6f\141\163\151\163\72\x6e\141\155\x65\163\72\164\143\72\123\x41\x4d\x4c\72\x32\x2e\60\72\141\163\x73\x65\162\164\x69\157\x6e", "\163\x61\155\154\x3a\x41\x74\x74\x72\x69\142\x75\x74\145\x56\141\x6c\165\145");
                $cX->appendChild($Ba);
                if (!($qI !== NULL)) {
                    goto rZ;
                }
                $Ba->setAttributeNS("\x68\x74\164\x70\x3a\57\x2f\167\x77\x77\56\167\x33\x2e\157\162\147\57\x32\60\60\x31\x2f\x58\x4d\114\x53\x63\150\x65\155\141\55\151\156\163\x74\x61\156\143\145", "\170\163\151\72\164\x79\x70\x65", $qI);
                rZ:
                if (!is_null($VP)) {
                    goto nc;
                }
                $Ba->setAttributeNS("\x68\x74\164\160\72\57\x2f\x77\x77\167\x2e\167\x33\56\x6f\x72\x67\57\x32\x30\x30\x31\57\x58\115\114\123\x63\150\x65\x6d\141\55\x69\156\163\x74\141\156\x63\145", "\170\x73\151\x3a\156\x69\x6c", "\x74\162\x75\x65");
                nc:
                if ($VP instanceof DOMNodeList) {
                    goto qN;
                }
                $Ba->appendChild($rT->createTextNode($VP));
                goto od;
                qN:
                $nO = 0;
                Cc:
                if (!($nO < $VP->length)) {
                    goto YI;
                }
                $zN = $rT->importNode($VP->item($nO), TRUE);
                $Ba->appendChild($zN);
                aT:
                $nO++;
                goto Cc;
                YI:
                od:
                LH:
            }
            lg:
            X4:
        }
        gw:
    }
    private function addEncryptedAttributeStatement(DOMElement $OC)
    {
        if (!($this->requiredEncAttributes == FALSE)) {
            goto Qs;
        }
        return;
        Qs:
        $rT = $OC->ownerDocument;
        $jq = $rT->createElementNS("\x75\x72\156\x3a\157\141\x73\x69\x73\72\156\x61\x6d\x65\x73\72\164\143\72\123\x41\115\114\x3a\62\56\60\x3a\x61\x73\163\145\x72\164\151\157\x6e", "\163\x61\155\154\72\101\x74\x74\x72\x69\x62\165\x74\x65\123\x74\141\x74\145\155\145\156\164");
        $OC->appendChild($jq);
        foreach ($this->attributes as $yt => $jT) {
            $T9 = new DOMDocument();
            $cX = $T9->createElementNS("\165\x72\x6e\72\x6f\x61\163\x69\x73\x3a\x6e\x61\155\x65\163\72\164\143\72\123\x41\115\114\x3a\62\x2e\60\x3a\x61\163\x73\145\162\164\x69\x6f\x6e", "\x73\x61\155\x6c\72\101\164\x74\x72\x69\x62\165\x74\x65");
            $cX->setAttribute("\x4e\x61\x6d\145", $yt);
            $T9->appendChild($cX);
            if (!($this->nameFormat !== "\x75\162\x6e\x3a\x6f\141\163\x69\x73\x3a\156\x61\155\x65\x73\x3a\x74\143\x3a\123\x41\x4d\114\72\62\56\x30\72\x61\164\164\162\156\x61\x6d\145\x2d\x66\x6f\162\x6d\x61\164\72\x75\156\x73\160\x65\143\151\146\151\x65\x64")) {
                goto w9;
            }
            $cX->setAttribute("\116\141\x6d\145\106\x6f\162\x6d\x61\x74", $this->nameFormat);
            w9:
            foreach ($jT as $VP) {
                if (is_string($VP)) {
                    goto Io;
                }
                if (is_int($VP)) {
                    goto Ee;
                }
                $qI = NULL;
                goto z2;
                Io:
                $qI = "\170\x73\72\x73\164\x72\151\156\x67";
                goto z2;
                Ee:
                $qI = "\170\163\x3a\x69\x6e\x74\x65\147\x65\162";
                z2:
                $Ba = $T9->createElementNS("\x75\162\156\72\157\x61\x73\x69\x73\x3a\x6e\141\155\145\x73\72\x74\143\72\123\101\115\114\x3a\x32\56\x30\72\x61\x73\163\145\162\x74\151\x6f\x6e", "\163\x61\155\154\x3a\101\164\x74\162\151\x62\x75\x74\145\x56\141\x6c\165\x65");
                $cX->appendChild($Ba);
                if (!($qI !== NULL)) {
                    goto NN;
                }
                $Ba->setAttributeNS("\x68\164\x74\160\72\57\57\x77\167\x77\x2e\x77\x33\56\157\162\x67\57\62\x30\x30\61\x2f\130\115\x4c\123\x63\x68\145\155\141\55\151\x6e\163\x74\x61\156\x63\145", "\x78\163\x69\72\164\171\160\x65", $qI);
                NN:
                if ($VP instanceof DOMNodeList) {
                    goto ZM;
                }
                $Ba->appendChild($T9->createTextNode($VP));
                goto i2;
                ZM:
                $nO = 0;
                R_:
                if (!($nO < $VP->length)) {
                    goto Vk;
                }
                $zN = $T9->importNode($VP->item($nO), TRUE);
                $Ba->appendChild($zN);
                nt:
                $nO++;
                goto R_;
                Vk:
                i2:
                Bb:
            }
            BK:
            $Ag = new XMLSecEnc();
            $Ag->setNode($T9->documentElement);
            $Ag->type = "\150\x74\164\x70\72\57\57\x77\x77\167\x2e\x77\63\x2e\157\162\147\x2f\62\60\60\x31\x2f\60\x34\x2f\x78\x6d\154\145\x6e\143\x23\x45\154\145\155\145\156\x74";
            $ep = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
            $ep->generateSessionKey();
            $Ag->encryptKey($this->encryptionKey, $ep);
            $Q_ = $Ag->encryptNode($ep);
            $rw = $rT->createElementNS("\165\162\x6e\x3a\x6f\141\163\151\163\72\156\x61\x6d\145\x73\72\164\143\72\x53\101\x4d\x4c\72\62\x2e\x30\x3a\141\x73\x73\145\x72\x74\151\x6f\x6e", "\163\x61\x6d\x6c\x3a\105\x6e\x63\x72\x79\160\x74\145\144\101\x74\164\162\x69\x62\165\164\145");
            $jq->appendChild($rw);
            $Ue = $rT->importNode($Q_, TRUE);
            $rw->appendChild($Ue);
            Wz:
        }
        n7:
    }
}
