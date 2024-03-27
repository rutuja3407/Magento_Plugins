<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Exception;
class XMLSecurityDSig
{
    const XMLDSIGNS = "\x68\x74\164\x70\x3a\57\57\x77\x77\x77\56\x77\x33\x2e\157\x72\147\x2f\62\60\60\60\57\x30\71\x2f\170\x6d\x6c\144\x73\151\x67\43";
    const SHA1 = "\x68\x74\x74\x70\72\57\57\x77\167\167\x2e\167\63\56\157\162\x67\57\62\x30\60\60\57\x30\x39\x2f\x78\x6d\x6c\144\x73\151\x67\43\163\150\141\x31";
    const SHA256 = "\x68\164\164\160\x3a\x2f\x2f\167\x77\x77\x2e\167\63\x2e\157\x72\147\57\62\60\x30\x31\x2f\x30\x34\57\x78\155\154\145\x6e\x63\43\163\150\141\62\x35\x36";
    const SHA384 = "\150\x74\x74\x70\72\57\57\x77\167\167\56\167\x33\56\157\x72\147\57\62\60\60\61\x2f\x30\x34\x2f\x78\x6d\x6c\144\163\151\147\x2d\155\157\162\145\x23\163\x68\141\63\x38\x34";
    const SHA512 = "\x68\x74\164\160\72\57\57\167\167\x77\56\x77\63\56\x6f\x72\x67\57\62\x30\60\x31\57\60\x34\x2f\x78\x6d\154\145\156\143\43\163\150\141\65\61\x32";
    const RIPEMD160 = "\150\x74\164\160\72\x2f\x2f\x77\x77\x77\56\x77\63\56\x6f\162\x67\57\62\x30\x30\61\x2f\x30\x34\57\x78\155\x6c\145\156\x63\43\x72\x69\160\x65\155\x64\61\x36\x30";
    const C14N = "\150\x74\x74\x70\x3a\x2f\x2f\167\167\x77\56\x77\x33\56\x6f\162\147\x2f\x54\122\57\x32\x30\x30\x31\x2f\x52\105\103\55\170\155\154\55\143\x31\x34\x6e\x2d\x32\x30\x30\x31\x30\x33\x31\x35";
    const C14N_COMMENTS = "\150\x74\164\x70\x3a\x2f\57\167\167\167\56\x77\x33\x2e\157\x72\x67\57\124\x52\x2f\62\60\60\x31\x2f\x52\x45\103\x2d\170\x6d\x6c\55\x63\61\64\156\55\x32\60\60\x31\60\x33\61\65\43\127\x69\x74\150\103\157\x6d\x6d\145\x6e\164\x73";
    const EXC_C14N = "\x68\x74\x74\x70\72\57\57\x77\167\167\x2e\167\63\56\x6f\x72\147\57\62\60\60\61\x2f\x31\x30\57\170\x6d\154\x2d\145\x78\x63\x2d\143\61\x34\156\x23";
    const EXC_C14N_COMMENTS = "\150\164\164\x70\x3a\57\x2f\167\x77\167\56\167\x33\56\x6f\x72\x67\57\x32\60\60\x31\57\x31\x30\57\x78\x6d\x6c\x2d\145\170\143\x2d\x63\61\64\156\43\127\151\x74\x68\x43\x6f\155\155\x65\156\x74\x73";
    const template = "\x3c\x64\x73\x3a\x53\151\147\156\141\164\x75\162\x65\x20\170\x6d\154\x6e\x73\72\x64\x73\x3d\x22\150\164\164\160\72\x2f\x2f\167\x77\167\56\167\x33\56\157\x72\147\57\62\x30\60\x30\x2f\x30\x39\x2f\170\155\154\x64\x73\x69\147\43\x22\76\xd\12\x20\x20\x20\74\x64\x73\72\123\151\x67\x6e\x65\x64\111\156\x66\157\76\15\12\x20\40\x20\40\40\x3c\x64\x73\72\123\x69\147\156\141\x74\x75\162\145\115\145\x74\150\157\144\x20\57\76\15\12\40\40\40\74\x2f\x64\163\x3a\123\x69\x67\156\x65\144\111\156\146\x6f\x3e\xd\xa\x20\74\x2f\144\163\x3a\123\x69\x67\156\141\164\165\162\145\x3e";
    const BASE_TEMPLATE = "\74\x53\x69\147\156\141\164\x75\x72\x65\x20\x78\155\x6c\156\163\x3d\x22\x68\164\x74\160\x3a\57\x2f\167\167\167\x2e\x77\63\x2e\157\162\147\57\x32\60\x30\60\57\60\x39\x2f\x78\155\x6c\144\163\151\147\x23\x22\76\15\12\x20\40\x20\74\x53\x69\147\156\145\144\111\x6e\x66\x6f\x3e\15\12\40\x20\x20\40\40\74\x53\151\147\156\x61\164\x75\162\x65\x4d\x65\164\150\157\x64\40\x2f\76\15\12\x20\40\x20\x3c\x2f\x53\x69\147\156\x65\x64\111\156\146\x6f\x3e\15\12\40\74\57\x53\x69\x67\x6e\141\x74\x75\162\x65\76";
    public $sigNode = null;
    public $idKeys = array();
    public $idNS = array();
    private $signedInfo = null;
    private $xPathCtx = null;
    private $canonicalMethod = null;
    private $prefix = '';
    private $searchpfx = "\x73\145\143\x64\163\x69\x67";
    private $validatedNodes = null;
    public function __construct($wt = "\144\x73")
    {
        $dn = self::BASE_TEMPLATE;
        if (empty($wt)) {
            goto q2;
        }
        $this->prefix = $wt . "\x3a";
        $rC = array("\74\x53", "\74\57\123", "\x78\155\154\x6e\x73\75");
        $LC = array("\x3c{$wt}\x3a\123", "\x3c\57{$wt}\72\123", "\x78\155\154\x6e\163\x3a{$wt}\x3d");
        $dn = str_replace($rC, $LC, $dn);
        q2:
        $t9 = new DOMDocument();
        $t9->loadXML($dn);
        $this->sigNode = $t9->documentElement;
    }
    public static function generate_GUID($wt = "\160\x66\170")
    {
        return self::generateGUID($wt);
    }
    public static function generateGUID($wt = "\160\146\x78")
    {
        $lY = md5(uniqid(mt_rand(), true));
        $ch = $wt . substr($lY, 0, 8) . "\55" . substr($lY, 8, 4) . "\x2d" . substr($lY, 12, 4) . "\55" . substr($lY, 16, 4) . "\55" . substr($lY, 20, 12);
        return $ch;
    }
    public static function get509XCert($d2, $aC = true)
    {
        $OE = self::staticGet509XCerts($d2, $aC);
        if (empty($OE)) {
            goto wR;
        }
        return $OE[0];
        wR:
        return '';
    }
    public static function staticGet509XCerts($OE, $aC = true)
    {
        if ($aC) {
            goto Ta;
        }
        return array($OE);
        goto rc;
        Ta:
        $or = '';
        $SN = array();
        $LA = explode("\xa", $OE);
        $zJ = false;
        foreach ($LA as $pw) {
            if (!$zJ) {
                goto PL;
            }
            if (!(strncmp($pw, "\x2d\55\x2d\x2d\55\x45\x4e\104\x20\103\x45\122\x54\x49\x46\111\103\x41\124\105", 20) == 0)) {
                goto TC;
            }
            $zJ = false;
            $SN[] = $or;
            $or = '';
            goto sh;
            TC:
            $or .= trim($pw);
            goto zG;
            PL:
            if (!(strncmp($pw, "\55\55\x2d\x2d\x2d\102\x45\107\111\116\40\103\x45\122\x54\111\106\x49\103\101\x54\105", 22) == 0)) {
                goto D8;
            }
            $zJ = true;
            D8:
            zG:
            sh:
        }
        YL:
        return $SN;
        rc:
    }
    public function locateSignature($Fw, $Tj = 0)
    {
        if ($Fw instanceof DOMDocument) {
            goto Bp;
        }
        $st = $Fw->ownerDocument;
        goto sS;
        Bp:
        $st = $Fw;
        sS:
        if (!$st) {
            goto Ik;
        }
        $vD = new DOMXPath($st);
        $vD->registerNamespace("\163\x65\143\144\163\151\147", self::XMLDSIGNS);
        $NB = "\56\57\x2f\163\x65\x63\x64\163\151\147\72\123\151\147\x6e\x61\164\x75\162\x65";
        $Wy = $vD->query($NB, $Fw);
        $this->sigNode = $Wy->item($Tj);
        return $this->sigNode;
        Ik:
        return null;
    }
    public function setCanonicalMethod($vh)
    {
        switch ($vh) {
            case "\150\164\x74\x70\x3a\57\57\x77\x77\167\56\167\63\x2e\157\162\147\x2f\x54\x52\57\62\x30\60\x31\x2f\x52\x45\x43\x2d\x78\x6d\x6c\55\x63\61\64\x6e\x2d\62\x30\60\61\x30\x33\61\65":
            case "\150\x74\164\160\x3a\57\x2f\167\167\x77\x2e\167\x33\56\x6f\162\x67\57\x54\122\x2f\62\60\x30\x31\57\x52\x45\x43\55\x78\x6d\x6c\x2d\143\61\64\x6e\x2d\x32\x30\x30\61\x30\63\x31\65\43\127\151\x74\150\103\157\x6d\x6d\145\x6e\164\x73":
            case "\150\164\x74\160\x3a\57\57\x77\167\167\x2e\167\x33\x2e\x6f\x72\x67\x2f\62\60\x30\x31\x2f\61\x30\57\170\x6d\x6c\55\x65\x78\x63\55\143\61\x34\156\43":
            case "\150\164\x74\160\72\57\x2f\x77\167\167\x2e\167\63\56\x6f\x72\147\57\x32\x30\x30\61\57\61\60\x2f\x78\155\154\55\145\x78\x63\55\143\x31\x34\156\43\127\x69\164\150\103\x6f\155\155\x65\156\x74\163":
                $this->canonicalMethod = $vh;
                goto Ve;
            default:
                throw new Exception("\111\156\x76\x61\x6c\x69\x64\x20\x43\x61\156\x6f\x6e\151\x63\141\x6c\x20\115\x65\x74\x68\157\144");
        }
        pq:
        Ve:
        if (!($vD = $this->getXPathObj())) {
            goto fV;
        }
        $NB = "\x2e\57" . $this->searchpfx . "\72\x53\x69\147\x6e\145\144\x49\x6e\146\x6f";
        $Wy = $vD->query($NB, $this->sigNode);
        if (!($oT = $Wy->item(0))) {
            goto MS;
        }
        $NB = "\x2e\57" . $this->searchpfx . "\103\x61\156\x6f\x6e\x69\143\x61\x6c\151\x7a\141\164\151\x6f\156\115\145\164\150\x6f\x64";
        $Wy = $vD->query($NB, $oT);
        if ($M7 = $Wy->item(0)) {
            goto ES;
        }
        $M7 = $this->createNewSignNode("\x43\141\x6e\x6f\156\x69\143\141\x6c\x69\172\141\164\x69\x6f\x6e\x4d\145\x74\x68\157\144");
        $oT->insertBefore($M7, $oT->firstChild);
        ES:
        $M7->setAttribute("\101\x6c\x67\x6f\162\151\164\150\x6d", $this->canonicalMethod);
        MS:
        fV:
    }
    private function getXPathObj()
    {
        if (!(empty($this->xPathCtx) && !empty($this->sigNode))) {
            goto P0;
        }
        $vD = new DOMXPath($this->sigNode->ownerDocument);
        $vD->registerNamespace("\x73\x65\x63\x64\x73\151\x67", self::XMLDSIGNS);
        $this->xPathCtx = $vD;
        P0:
        return $this->xPathCtx;
    }
    public function createNewSignNode($yt, $VP = null)
    {
        $st = $this->sigNode->ownerDocument;
        if (!is_null($VP)) {
            goto td;
        }
        $zN = $st->createElementNS(self::XMLDSIGNS, $this->prefix . $yt);
        goto hO;
        td:
        $zN = $st->createElementNS(self::XMLDSIGNS, $this->prefix . $yt, $VP);
        hO:
        return $zN;
    }
    public function canonicalizeSignedInfo()
    {
        $st = $this->sigNode->ownerDocument;
        $qb = null;
        if (!$st) {
            goto CX;
        }
        $vD = $this->getXPathObj();
        $NB = "\56\57\x73\x65\143\x64\x73\x69\147\x3a\x53\x69\x67\x6e\x65\144\x49\x6e\146\157";
        $Wy = $vD->query($NB, $this->sigNode);
        if (!($iK = $Wy->item(0))) {
            goto HC;
        }
        $NB = "\56\x2f\x73\x65\x63\144\163\151\x67\x3a\103\x61\x6e\x6f\156\x69\x63\x61\x6c\151\172\141\164\x69\157\x6e\115\x65\x74\150\157\144";
        $Wy = $vD->query($NB, $iK);
        if (!($M7 = $Wy->item(0))) {
            goto ro;
        }
        $qb = $M7->getAttribute("\101\x6c\x67\157\162\151\x74\150\155");
        ro:
        $this->signedInfo = $this->canonicalizeData($iK, $qb);
        return $this->signedInfo;
        HC:
        CX:
        return null;
    }
    private function canonicalizeData($zN, $qb, $mM = null, $Z0 = null)
    {
        $fS = false;
        $s7 = false;
        switch ($qb) {
            case "\x68\x74\164\x70\x3a\57\57\167\167\x77\x2e\167\x33\x2e\157\x72\147\x2f\124\122\x2f\62\60\60\61\x2f\122\x45\x43\55\170\155\154\x2d\x63\x31\64\156\x2d\x32\x30\x30\61\60\63\x31\x35":
                $fS = false;
                $s7 = false;
                goto gj;
            case "\x68\x74\x74\160\x3a\57\57\x77\x77\167\x2e\167\x33\x2e\157\x72\147\x2f\x54\x52\x2f\62\x30\60\61\x2f\122\105\103\55\170\155\x6c\55\143\61\64\x6e\55\62\x30\x30\61\x30\x33\61\65\x23\127\151\164\x68\103\157\155\x6d\x65\156\x74\x73":
                $s7 = true;
                goto gj;
            case "\x68\164\164\160\x3a\57\x2f\x77\x77\x77\x2e\167\x33\x2e\x6f\162\147\57\x32\x30\60\61\x2f\61\60\57\x78\155\154\x2d\145\170\143\x2d\143\x31\64\156\43":
                $fS = true;
                goto gj;
            case "\x68\x74\x74\160\x3a\x2f\57\x77\167\167\x2e\167\63\56\x6f\162\147\57\x32\60\60\x31\57\x31\x30\57\170\x6d\154\55\x65\x78\x63\x2d\143\61\x34\156\x23\127\x69\164\150\103\x6f\155\155\x65\x6e\x74\163":
                $fS = true;
                $s7 = true;
                goto gj;
        }
        Gz:
        gj:
        if (!(is_null($mM) && $zN instanceof DOMNode && $zN->ownerDocument !== null && $zN->isSameNode($zN->ownerDocument->documentElement))) {
            goto ul;
        }
        $hL = $zN;
        Lq:
        if (!($pc = $hL->previousSibling)) {
            goto aM;
        }
        if (!($pc->nodeType == XML_PI_NODE || $pc->nodeType == XML_COMMENT_NODE && $s7)) {
            goto a9;
        }
        goto aM;
        a9:
        $hL = $pc;
        goto Lq;
        aM:
        if (!($pc == null)) {
            goto b1;
        }
        $zN = $zN->ownerDocument;
        b1:
        ul:
        return $zN->C14N($fS, $s7, $mM, $Z0);
    }
    public function getRefIDs()
    {
        $Cd = array();
        $vD = $this->getXPathObj();
        $NB = "\x2e\57\163\145\x63\144\163\151\147\72\123\x69\x67\156\145\x64\111\x6e\146\157\57\163\145\x63\144\163\x69\x67\x3a\x52\145\146\145\x72\x65\156\143\145";
        $Wy = $vD->query($NB, $this->sigNode);
        if (!($Wy->length == 0)) {
            goto Eh;
        }
        throw new Exception("\122\x65\146\x65\x72\145\x6e\143\145\x20\156\x6f\x64\145\x73\x20\156\x6f\x74\40\146\157\x75\x6e\x64");
        Eh:
        foreach ($Wy as $Ei) {
            $Cd[] = $this->getRefNodeID($Ei);
            vx:
        }
        PM:
        return $Cd;
    }
    public function getRefNodeID($Ei)
    {
        if (!($cN = $Ei->getAttribute("\x55\x52\x49"))) {
            goto Rn;
        }
        $O9 = parse_url($cN);
        if (!empty($O9["\x70\x61\164\x68"])) {
            goto eZ;
        }
        if (!($u8 = $O9["\146\x72\x61\147\155\145\156\164"])) {
            goto t8;
        }
        return $u8;
        t8:
        eZ:
        Rn:
        return null;
    }
    public function validateReference()
    {
        $u0 = $this->sigNode->ownerDocument->documentElement;
        if ($u0->isSameNode($this->sigNode)) {
            goto eC;
        }
        if (!($this->sigNode->parentNode != null)) {
            goto Kl;
        }
        $this->sigNode->parentNode->removeChild($this->sigNode);
        Kl:
        eC:
        $vD = $this->getXPathObj();
        $NB = "\x2e\x2f\163\x65\x63\144\x73\151\147\72\x53\151\147\x6e\145\144\x49\156\x66\157\57\163\145\x63\x64\163\x69\x67\72\122\145\146\x65\162\x65\x6e\x63\145";
        $Wy = $vD->query($NB, $this->sigNode);
        if (!($Wy->length == 0)) {
            goto UM;
        }
        throw new Exception("\x52\145\x66\x65\x72\145\x6e\x63\145\x20\156\x6f\144\145\163\40\x6e\157\164\40\x66\157\x75\156\x64");
        UM:
        $this->validatedNodes = array();
        foreach ($Wy as $Ei) {
            if ($this->processRefNode($Ei)) {
                goto r1;
            }
            $this->validatedNodes = null;
            throw new Exception("\122\145\146\145\162\x65\156\x63\145\40\x76\141\154\151\144\141\164\151\x6f\156\x20\x66\x61\x69\154\x65\144");
            r1:
            pK:
        }
        fs:
        return true;
    }
    public function processRefNode($Ei)
    {
        $E0 = null;
        $D9 = true;
        if ($cN = $Ei->getAttribute("\x55\x52\111")) {
            goto iL;
        }
        $D9 = false;
        $E0 = $Ei->ownerDocument;
        goto v_;
        iL:
        $O9 = parse_url($cN);
        if (empty($O9["\x70\141\x74\x68"])) {
            goto o5;
        }
        $E0 = file_get_contents($O9);
        goto Ut;
        o5:
        if ($u8 = $O9["\x66\162\x61\147\155\x65\156\164"]) {
            goto ZZ;
        }
        $E0 = $Ei->ownerDocument;
        goto l6;
        ZZ:
        $D9 = false;
        $rt = new DOMXPath($Ei->ownerDocument);
        if (!($this->idNS && is_array($this->idNS))) {
            goto Pv;
        }
        foreach ($this->idNS as $tc => $s8) {
            $rt->registerNamespace($tc, $s8);
            qu:
        }
        XE:
        Pv:
        $AW = "\x40\x49\144\x3d\42" . $u8 . "\x22";
        if (!is_array($this->idKeys)) {
            goto R8;
        }
        foreach ($this->idKeys as $OY) {
            $AW .= "\40\157\162\x20\100{$OY}\75\x27{$u8}\47";
            Tn:
        }
        dH:
        R8:
        $NB = "\57\x2f\52\x5b" . $AW . "\x5d";
        $E0 = $rt->query($NB)->item(0);
        l6:
        Ut:
        v_:
        $or = $this->processTransforms($Ei, $E0, $D9);
        if ($this->validateDigest($Ei, $or)) {
            goto t5;
        }
        return false;
        t5:
        if (!$E0 instanceof DOMElement) {
            goto PD;
        }
        if (!empty($u8)) {
            goto vP;
        }
        $this->validatedNodes[] = $E0;
        goto sq;
        vP:
        $this->validatedNodes[$u8] = $E0;
        sq:
        PD:
        return true;
    }
    public function processTransforms($Ei, $xe, $D9 = true)
    {
        $or = $xe;
        $vD = new DOMXPath($Ei->ownerDocument);
        $vD->registerNamespace("\x73\145\x63\x64\x73\x69\147", self::XMLDSIGNS);
        $NB = "\x2e\57\163\x65\143\x64\163\151\x67\x3a\x54\162\141\x6e\163\x66\x6f\x72\x6d\163\57\x73\145\143\x64\163\x69\x67\x3a\x54\x72\x61\x6e\163\x66\x6f\x72\155";
        $wu = $vD->query($NB, $Ei);
        $Qy = "\x68\164\x74\x70\72\x2f\x2f\167\167\167\x2e\x77\x33\x2e\157\162\x67\57\x54\122\57\62\x30\60\61\57\x52\x45\103\x2d\x78\x6d\x6c\55\143\x31\64\156\55\62\60\x30\x31\60\63\x31\x35";
        $mM = null;
        $Z0 = null;
        foreach ($wu as $T6) {
            $QM = $T6->getAttribute("\101\x6c\x67\x6f\x72\x69\x74\x68\x6d");
            switch ($QM) {
                case "\150\164\164\x70\x3a\57\x2f\x77\167\x77\x2e\167\63\56\x6f\x72\147\x2f\62\60\x30\x31\x2f\61\x30\x2f\170\x6d\x6c\x2d\x65\x78\x63\55\x63\61\64\x6e\43":
                case "\150\164\164\160\72\57\x2f\x77\x77\167\56\x77\63\56\157\x72\147\57\62\60\x30\61\x2f\x31\60\x2f\170\155\154\x2d\x65\170\x63\x2d\143\61\64\156\43\127\x69\x74\150\x43\157\x6d\x6d\x65\x6e\164\x73":
                    if (!$D9) {
                        goto xI;
                    }
                    $Qy = $QM;
                    goto cT;
                    xI:
                    $Qy = "\150\x74\164\160\x3a\57\x2f\x77\x77\167\x2e\x77\x33\x2e\157\162\x67\57\x32\60\x30\61\x2f\61\60\57\170\155\x6c\x2d\x65\x78\143\55\x63\x31\64\156\43";
                    cT:
                    $zN = $T6->firstChild;
                    sn:
                    if (!$zN) {
                        goto hQ;
                    }
                    if (!($zN->localName == "\x49\156\x63\154\x75\163\151\166\145\116\141\x6d\145\x73\x70\141\143\x65\x73")) {
                        goto TY;
                    }
                    if (!($kH = $zN->getAttribute("\120\x72\145\146\151\170\x4c\x69\x73\164"))) {
                        goto rF;
                    }
                    $sR = array();
                    $bX = explode("\x20", $kH);
                    foreach ($bX as $kH) {
                        $W5 = trim($kH);
                        if (empty($W5)) {
                            goto S1;
                        }
                        $sR[] = $W5;
                        S1:
                        UP:
                    }
                    Ns:
                    if (!(count($sR) > 0)) {
                        goto kX;
                    }
                    $Z0 = $sR;
                    kX:
                    rF:
                    goto hQ;
                    TY:
                    $zN = $zN->nextSibling;
                    goto sn;
                    hQ:
                    goto VG;
                case "\150\x74\x74\x70\x3a\57\57\167\x77\x77\x2e\167\63\x2e\157\162\147\57\x54\122\x2f\62\60\x30\61\x2f\x52\x45\x43\55\170\x6d\154\x2d\x63\61\x34\156\55\62\x30\x30\x31\x30\63\x31\65":
                case "\150\x74\164\x70\72\57\57\167\167\167\56\x77\x33\56\x6f\x72\x67\57\x54\122\x2f\62\60\x30\61\57\122\x45\x43\x2d\x78\x6d\154\x2d\143\61\64\156\x2d\62\60\60\61\x30\x33\x31\65\x23\x57\151\x74\150\103\157\x6d\155\145\x6e\x74\163":
                    if (!$D9) {
                        goto fH;
                    }
                    $Qy = $QM;
                    goto eh;
                    fH:
                    $Qy = "\x68\164\164\160\72\57\57\x77\x77\x77\56\x77\x33\x2e\157\x72\147\57\x54\122\57\62\x30\60\61\57\x52\105\103\x2d\170\155\x6c\x2d\x63\x31\x34\x6e\55\x32\x30\x30\61\60\x33\x31\x35";
                    eh:
                    goto VG;
                case "\150\164\x74\x70\x3a\57\x2f\167\x77\x77\x2e\x77\x33\56\x6f\x72\147\57\124\x52\x2f\x31\x39\x39\71\x2f\x52\x45\x43\55\x78\160\141\164\x68\x2d\x31\71\71\71\x31\x31\x31\66":
                    $zN = $T6->firstChild;
                    gb:
                    if (!$zN) {
                        goto E6;
                    }
                    if (!($zN->localName == "\x58\x50\x61\x74\150")) {
                        goto pr;
                    }
                    $mM = array();
                    $mM["\x71\x75\145\162\171"] = "\50\56\57\x2f\56\x20\174\40\56\57\x2f\x40\52\40\x7c\x20\x2e\x2f\x2f\156\141\x6d\145\163\160\x61\143\145\72\x3a\x2a\51\133" . $zN->nodeValue . "\x5d";
                    $FR["\156\141\x6d\x65\x73\160\141\143\x65\x73"] = array();
                    $BR = $vD->query("\x2e\57\x6e\141\155\145\163\x70\141\143\145\x3a\72\52", $zN);
                    foreach ($BR as $zr) {
                        if (!($zr->localName != "\170\x6d\154")) {
                            goto ST;
                        }
                        $mM["\156\x61\155\x65\163\160\141\x63\145\x73"][$zr->localName] = $zr->nodeValue;
                        ST:
                        so:
                    }
                    ut:
                    goto E6;
                    pr:
                    $zN = $zN->nextSibling;
                    goto gb;
                    E6:
                    goto VG;
            }
            Np:
            VG:
            s8:
        }
        Ck:
        if (!$or instanceof DOMElement) {
            goto tV;
        }
        $or = $this->canonicalizeData($xe, $Qy, $mM, $Z0);
        tV:
        return $or;
    }
    public function validateDigest($Ei, $or)
    {
        $vD = new DOMXPath($Ei->ownerDocument);
        $vD->registerNamespace("\x73\x65\143\144\163\x69\147", self::XMLDSIGNS);
        $NB = "\x73\x74\x72\151\x6e\x67\50\56\57\x73\x65\x63\x64\163\x69\147\72\x44\151\x67\145\x73\164\115\x65\164\150\157\144\x2f\x40\x41\x6c\x67\x6f\162\151\164\150\155\x29";
        $Vj = $vD->evaluate($NB, $Ei);
        $sS = $this->calculateDigest($Vj, $or, false);
        $NB = "\x73\x74\x72\x69\156\x67\50\x2e\x2f\163\x65\x63\144\163\151\147\72\x44\x69\147\145\163\164\126\141\154\165\x65\51";
        $re = $vD->evaluate($NB, $Ei);
        return $sS == base64_decode($re);
    }
    public function calculateDigest($Vj, $or, $nl = true)
    {
        switch ($Vj) {
            case self::SHA1:
                $X5 = "\163\x68\x61\61";
                goto uW;
            case self::SHA256:
                $X5 = "\x73\150\141\x32\65\x36";
                goto uW;
            case self::SHA384:
                $X5 = "\x73\x68\x61\x33\70\x34";
                goto uW;
            case self::SHA512:
                $X5 = "\x73\x68\x61\x35\61\62";
                goto uW;
            case self::RIPEMD160:
                $X5 = "\x72\151\160\x65\155\x64\61\x36\x30";
                goto uW;
            default:
                throw new Exception("\x43\141\x6e\x6e\157\x74\40\x76\x61\x6c\151\144\141\x74\x65\x20\x64\x69\147\x65\x73\x74\72\x20\125\x6e\163\165\160\160\157\x72\164\x65\144\x20\101\x6c\147\157\x72\x69\164\150\155\40\74{$Vj}\76");
        }
        r6:
        uW:
        $Iw = hash($X5, $or, true);
        if (!$nl) {
            goto Xa;
        }
        $Iw = base64_encode($Iw);
        Xa:
        return $Iw;
    }
    public function addReference($zN, $QM, $ll = null, $Es = null)
    {
        if (!($vD = $this->getXPathObj())) {
            goto Cy;
        }
        $NB = "\56\x2f\163\x65\143\144\x73\x69\x67\x3a\x53\x69\147\156\x65\144\111\x6e\146\157";
        $Wy = $vD->query($NB, $this->sigNode);
        if (!($fV = $Wy->item(0))) {
            goto q6;
        }
        $this->addRefInternal($fV, $zN, $QM, $ll, $Es);
        q6:
        Cy:
    }
    private function addRefInternal($WX, $zN, $QM, $ll = null, $Es = null)
    {
        $wt = null;
        $g7 = null;
        $dN = "\x49\144";
        $BL = true;
        $oy = false;
        if (!is_array($Es)) {
            goto K9;
        }
        $wt = empty($Es["\160\162\145\146\x69\x78"]) ? null : $Es["\160\x72\x65\x66\151\x78"];
        $g7 = empty($Es["\x70\162\145\x66\x69\170\137\x6e\163"]) ? null : $Es["\160\162\145\x66\x69\x78\137\156\163"];
        $dN = empty($Es["\151\x64\x5f\x6e\x61\x6d\x65"]) ? "\x49\144" : $Es["\x69\x64\137\156\141\x6d\145"];
        $BL = !isset($Es["\157\166\145\162\x77\x72\151\164\145"]) ? true : (bool) $Es["\157\166\145\x72\167\162\151\164\145"];
        $oy = !isset($Es["\x66\157\162\143\x65\x5f\x75\x72\151"]) ? false : (bool) $Es["\146\x6f\x72\x63\x65\x5f\x75\x72\x69"];
        K9:
        $hi = $dN;
        if (empty($wt)) {
            goto rs;
        }
        $hi = $wt . "\72" . $hi;
        rs:
        $Ei = $this->createNewSignNode("\x52\x65\x66\145\162\x65\156\x63\x65");
        $WX->appendChild($Ei);
        if (!$zN instanceof DOMDocument) {
            goto PX;
        }
        if ($oy) {
            goto c1;
        }
        goto zI;
        PX:
        $cN = null;
        if ($BL) {
            goto eN;
        }
        $cN = $g7 ? $zN->getAttributeNS($g7, $dN) : $zN->getAttribute($dN);
        eN:
        if (!empty($cN)) {
            goto Vo;
        }
        $cN = self::generateGUID();
        $zN->setAttributeNS($g7, $hi, $cN);
        Vo:
        $Ei->setAttribute("\125\122\x49", "\43" . $cN);
        goto zI;
        c1:
        $Ei->setAttribute("\x55\122\111", '');
        zI:
        $cP = $this->createNewSignNode("\124\162\141\x6e\x73\x66\157\x72\155\163");
        $Ei->appendChild($cP);
        if (is_array($ll)) {
            goto Pr;
        }
        if (!empty($this->canonicalMethod)) {
            goto gn;
        }
        goto vJ;
        Pr:
        foreach ($ll as $T6) {
            $It = $this->createNewSignNode("\x54\162\141\x6e\x73\x66\x6f\x72\x6d");
            $cP->appendChild($It);
            if (is_array($T6) && !empty($T6["\150\164\x74\x70\x3a\x2f\x2f\x77\x77\167\56\167\63\56\x6f\x72\147\x2f\124\x52\x2f\61\71\71\x39\x2f\122\x45\x43\55\x78\160\x61\164\x68\55\61\x39\71\x39\61\x31\x31\66"]) && !empty($T6["\x68\164\x74\160\72\57\57\x77\167\167\x2e\x77\x33\x2e\x6f\162\147\57\124\122\57\61\x39\71\x39\57\x52\x45\103\55\170\160\x61\x74\x68\x2d\x31\x39\71\x39\x31\x31\x31\66"]["\161\165\145\x72\171"])) {
                goto RC;
            }
            $It->setAttribute("\x41\x6c\147\157\162\x69\164\x68\155", $T6);
            goto z7;
            RC:
            $It->setAttribute("\101\154\147\157\162\151\x74\x68\155", "\x68\164\x74\x70\x3a\57\57\167\167\167\x2e\x77\63\x2e\x6f\x72\147\x2f\124\122\57\61\71\x39\71\57\122\x45\x43\x2d\170\x70\x61\x74\x68\x2d\61\71\71\71\x31\x31\x31\x36");
            $Pp = $this->createNewSignNode("\130\x50\141\x74\x68", $T6["\150\x74\x74\160\72\x2f\57\x77\x77\x77\x2e\167\x33\x2e\157\x72\x67\57\x54\122\x2f\61\71\71\x39\x2f\122\x45\103\x2d\x78\160\x61\164\x68\55\61\71\71\x39\61\x31\61\x36"]["\x71\x75\x65\162\x79"]);
            $It->appendChild($Pp);
            if (empty($T6["\x68\x74\164\x70\x3a\x2f\x2f\167\x77\167\56\167\63\x2e\x6f\x72\147\x2f\124\122\x2f\61\71\71\x39\57\x52\105\103\x2d\x78\160\141\x74\x68\55\61\71\x39\x39\x31\x31\x31\x36"]["\156\x61\155\145\x73\x70\x61\143\x65\163"])) {
                goto Dj;
            }
            foreach ($T6["\150\164\x74\x70\72\x2f\57\x77\167\167\x2e\x77\x33\x2e\x6f\162\x67\57\x54\122\57\61\x39\x39\x39\57\122\x45\x43\55\170\x70\x61\x74\150\x2d\x31\x39\x39\71\x31\x31\x31\66"]["\156\141\x6d\x65\163\160\141\143\x65\x73"] as $wt => $Wu) {
                $Pp->setAttributeNS("\x68\x74\164\x70\72\57\57\x77\167\x77\x2e\x77\63\56\157\162\x67\x2f\62\x30\x30\x30\x2f\170\155\154\156\163\x2f", "\x78\155\x6c\156\163\72{$wt}", $Wu);
                Th:
            }
            ge:
            Dj:
            z7:
            BC:
        }
        Pd:
        goto vJ;
        gn:
        $It = $this->createNewSignNode("\x54\162\141\x6e\x73\146\x6f\162\155");
        $cP->appendChild($It);
        $It->setAttribute("\101\x6c\147\x6f\162\151\x74\x68\155", $this->canonicalMethod);
        vJ:
        $s3 = $this->processTransforms($Ei, $zN);
        $sS = $this->calculateDigest($QM, $s3);
        $hw = $this->createNewSignNode("\104\x69\147\x65\163\x74\115\145\164\x68\x6f\x64");
        $Ei->appendChild($hw);
        $hw->setAttribute("\101\154\x67\157\x72\x69\x74\x68\x6d", $QM);
        $re = $this->createNewSignNode("\104\x69\x67\x65\163\164\x56\x61\154\165\x65", $sS);
        $Ei->appendChild($re);
    }
    public function addReferenceList($sj, $QM, $ll = null, $Es = null)
    {
        if (!($vD = $this->getXPathObj())) {
            goto S0;
        }
        $NB = "\56\57\163\x65\143\144\x73\x69\x67\72\x53\x69\147\x6e\145\x64\x49\156\x66\157";
        $Wy = $vD->query($NB, $this->sigNode);
        if (!($fV = $Wy->item(0))) {
            goto QJ;
        }
        foreach ($sj as $zN) {
            $this->addRefInternal($fV, $zN, $QM, $ll, $Es);
            mo:
        }
        Db:
        QJ:
        S0:
    }
    public function addObject($or, $mX = null, $Yy = null)
    {
        $mU = $this->createNewSignNode("\x4f\142\152\x65\143\x74");
        $this->sigNode->appendChild($mU);
        if (empty($mX)) {
            goto NK;
        }
        $mU->setAttribute("\x4d\x69\155\x65\x54\x79\x70\x65", $mX);
        NK:
        if (empty($Yy)) {
            goto ac;
        }
        $mU->setAttribute("\105\156\143\x6f\x64\151\156\x67", $Yy);
        ac:
        if ($or instanceof DOMElement) {
            goto tt;
        }
        $mh = $this->sigNode->ownerDocument->createTextNode($or);
        goto i8;
        tt:
        $mh = $this->sigNode->ownerDocument->importNode($or, true);
        i8:
        $mU->appendChild($mh);
        return $mU;
    }
    public function locateKey($zN = null)
    {
        if (!empty($zN)) {
            goto KO;
        }
        $zN = $this->sigNode;
        KO:
        if ($zN instanceof DOMNode) {
            goto Mi;
        }
        return null;
        Mi:
        if (!($st = $zN->ownerDocument)) {
            goto My;
        }
        $vD = new DOMXPath($st);
        $vD->registerNamespace("\163\x65\x63\x64\x73\x69\147", self::XMLDSIGNS);
        $NB = "\163\164\162\x69\156\x67\x28\56\57\163\145\x63\x64\163\151\x67\x3a\123\151\147\156\145\144\x49\x6e\146\157\57\163\145\143\144\163\x69\147\72\123\x69\x67\x6e\141\x74\x75\x72\145\x4d\x65\x74\x68\x6f\144\x2f\100\101\x6c\x67\x6f\x72\151\x74\150\155\x29";
        $QM = $vD->evaluate($NB, $zN);
        if (!$QM) {
            goto rM;
        }
        try {
            $z3 = new XMLSecurityKey($QM, array("\164\171\x70\x65" => "\x70\x75\x62\x6c\151\x63"));
        } catch (Exception $IR) {
            return null;
        }
        return $z3;
        rM:
        My:
        return null;
    }
    public function verify($z3)
    {
        $st = $this->sigNode->ownerDocument;
        $vD = new DOMXPath($st);
        $vD->registerNamespace("\x73\145\x63\144\x73\151\147", self::XMLDSIGNS);
        $NB = "\163\164\x72\x69\x6e\147\x28\56\x2f\163\x65\x63\x64\x73\x69\x67\72\x53\x69\147\156\141\x74\165\x72\x65\x56\141\x6c\x75\145\51";
        $fl = $vD->evaluate($NB, $this->sigNode);
        if (!empty($fl)) {
            goto V4;
        }
        throw new Exception("\125\156\x61\142\154\145\x20\164\x6f\x20\154\157\x63\x61\164\x65\40\123\151\x67\156\x61\164\x75\162\x65\126\141\154\x75\x65");
        V4:
        return $z3->verifySignature($this->signedInfo, base64_decode($fl));
    }
    public function sign($z3, $m6 = null)
    {
        if (!($m6 != null)) {
            goto Li;
        }
        $this->resetXPathObj();
        $this->appendSignature($m6);
        $this->sigNode = $m6->lastChild;
        Li:
        if (!($vD = $this->getXPathObj())) {
            goto Q1;
        }
        $NB = "\56\x2f\163\145\x63\144\x73\151\147\72\123\x69\x67\x6e\x65\144\x49\156\146\157";
        $Wy = $vD->query($NB, $this->sigNode);
        if (!($fV = $Wy->item(0))) {
            goto La;
        }
        $NB = "\x2e\x2f\163\145\143\x64\163\151\x67\72\x53\151\147\156\141\164\x75\x72\145\x4d\x65\x74\150\x6f\x64";
        $Wy = $vD->query($NB, $fV);
        $MD = $Wy->item(0);
        $MD->setAttribute("\x41\x6c\147\x6f\162\151\x74\150\155", $z3->type);
        $or = $this->canonicalizeData($fV, $this->canonicalMethod);
        $fl = base64_encode($this->signData($z3, $or));
        $pp = $this->createNewSignNode("\123\151\x67\156\x61\164\165\x72\x65\x56\x61\x6c\165\x65", $fl);
        if ($sv = $fV->nextSibling) {
            goto WR;
        }
        $this->sigNode->appendChild($pp);
        goto y2;
        WR:
        $sv->parentNode->insertBefore($pp, $sv);
        y2:
        La:
        Q1:
    }
    private function resetXPathObj()
    {
        $this->xPathCtx = null;
    }
    public function appendSignature($tM, $A9 = false)
    {
        $RT = $A9 ? $tM->firstChild : null;
        return $this->insertSignature($tM, $RT);
    }
    public function insertSignature($zN, $RT = null)
    {
        $rT = $zN->ownerDocument;
        $Oa = $rT->importNode($this->sigNode, true);
        if ($RT == null) {
            goto Az;
        }
        return $zN->insertBefore($Oa, $RT);
        goto Fj;
        Az:
        return $zN->insertBefore($Oa);
        Fj:
    }
    public function signData($z3, $or)
    {
        return $z3->signData($or);
    }
    public function appendCert()
    {
    }
    public function appendKey($z3, $iJ = null)
    {
        $z3->serializeKey($iJ);
    }
    public function add509Cert($d2, $aC = true, $GV = false, $Es = null)
    {
        if (!($vD = $this->getXPathObj())) {
            goto LI;
        }
        self::staticAdd509Cert($this->sigNode, $d2, $aC, $GV, $vD, $Es);
        LI:
    }
    public static function staticAdd509Cert($Lj, $d2, $aC = true, $GV = false, $vD = null, $Es = null)
    {
        if (!$GV) {
            goto AL;
        }
        $d2 = file_get_contents($d2);
        AL:
        if ($Lj instanceof DOMElement) {
            goto Lm;
        }
        throw new Exception("\x49\x6e\x76\x61\x6c\x69\144\x20\x70\141\162\x65\x6e\164\40\116\x6f\x64\x65\x20\160\141\x72\141\155\x65\164\x65\162");
        Lm:
        $tQ = $Lj->ownerDocument;
        if (!empty($vD)) {
            goto Nu;
        }
        $vD = new DOMXPath($Lj->ownerDocument);
        $vD->registerNamespace("\x73\145\x63\144\163\x69\147", self::XMLDSIGNS);
        Nu:
        $NB = "\x2e\57\x73\145\143\144\x73\x69\147\x3a\x4b\x65\171\x49\156\x66\x6f";
        $Wy = $vD->query($NB, $Lj);
        $He = $Wy->item(0);
        $BS = '';
        if (!$He) {
            goto k2;
        }
        $kH = $He->lookupPrefix(self::XMLDSIGNS);
        if (empty($kH)) {
            goto cc;
        }
        $BS = $kH . "\x3a";
        cc:
        goto RW;
        k2:
        $kH = $Lj->lookupPrefix(self::XMLDSIGNS);
        if (empty($kH)) {
            goto LJ;
        }
        $BS = $kH . "\x3a";
        LJ:
        $lC = false;
        $He = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\113\145\171\111\x6e\146\157");
        $NB = "\x2e\57\x73\x65\143\144\163\151\147\x3a\117\142\x6a\145\143\164";
        $Wy = $vD->query($NB, $Lj);
        if (!($Wt = $Wy->item(0))) {
            goto g5;
        }
        $Wt->parentNode->insertBefore($He, $Wt);
        $lC = true;
        g5:
        if ($lC) {
            goto ib;
        }
        $Lj->appendChild($He);
        ib:
        RW:
        $OE = self::staticGet509XCerts($d2, $aC);
        $ks = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\130\65\60\71\104\x61\x74\141");
        $He->appendChild($ks);
        $qr = false;
        $Jc = false;
        if (!is_array($Es)) {
            goto wx;
        }
        if (empty($Es["\x69\x73\x73\x75\145\162\123\x65\x72\151\x61\x6c"])) {
            goto D0;
        }
        $qr = true;
        D0:
        if (empty($Es["\163\x75\142\152\145\143\164\116\x61\x6d\x65"])) {
            goto aw;
        }
        $Jc = true;
        aw:
        wx:
        foreach ($OE as $s_) {
            if (!($qr || $Jc)) {
                goto RZ;
            }
            if (!($YB = openssl_x509_parse("\x2d\55\x2d\55\x2d\102\x45\x47\111\116\40\103\x45\x52\124\x49\106\x49\103\101\x54\x45\x2d\x2d\55\x2d\x2d\12" . chunk_split($s_, 64, "\12") . "\x2d\55\x2d\55\x2d\105\x4e\104\40\x43\105\x52\x54\111\x46\111\x43\101\x54\105\x2d\x2d\x2d\x2d\x2d\xa"))) {
                goto c6;
            }
            if (!($Jc && !empty($YB["\163\165\142\152\x65\143\164"]))) {
                goto bo;
            }
            if (is_array($YB["\163\165\142\x6a\145\143\x74"])) {
                goto Gx;
            }
            $CJ = $YB["\151\x73\x73\x75\x65\162"];
            goto dd;
            Gx:
            $kt = array();
            foreach ($YB["\163\165\x62\x6a\x65\x63\x74"] as $On => $VP) {
                if (is_array($VP)) {
                    goto ik;
                }
                array_unshift($kt, "{$On}\75{$VP}");
                goto J0;
                ik:
                foreach ($VP as $qg) {
                    array_unshift($kt, "{$On}\75{$qg}");
                    aB:
                }
                X6:
                J0:
                R2:
            }
            KB:
            $CJ = implode("\x2c", $kt);
            dd:
            $Fd = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\130\65\x30\x39\x53\165\x62\x6a\145\x63\164\x4e\141\155\145", $CJ);
            $ks->appendChild($Fd);
            bo:
            if (!($qr && !empty($YB["\151\x73\163\165\145\162"]) && !empty($YB["\163\145\162\151\x61\154\x4e\165\155\142\145\x72"]))) {
                goto Pl;
            }
            if (is_array($YB["\151\163\x73\x75\x65\x72"])) {
                goto K3;
            }
            $E_ = $YB["\151\x73\x73\165\145\162"];
            goto sd;
            K3:
            $kt = array();
            foreach ($YB["\x69\x73\163\x75\145\162"] as $On => $VP) {
                array_unshift($kt, "{$On}\x3d{$VP}");
                of:
            }
            qK:
            $E_ = implode("\x2c", $kt);
            sd:
            $aj = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\x58\65\x30\x39\x49\x73\x73\165\x65\x72\x53\x65\162\x69\x61\154");
            $ks->appendChild($aj);
            $PC = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\130\65\x30\x39\111\x73\163\x75\x65\162\x4e\141\x6d\145", $E_);
            $aj->appendChild($PC);
            $PC = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\130\x35\x30\x39\x53\x65\162\x69\x61\x6c\116\165\155\142\x65\162", $YB["\x73\145\162\151\x61\154\x4e\165\x6d\142\x65\162"]);
            $aj->appendChild($PC);
            Pl:
            c6:
            RZ:
            $v9 = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\130\x35\x30\x39\x43\145\162\164\x69\146\x69\x63\141\x74\145", $s_);
            $ks->appendChild($v9);
            B1:
        }
        BT:
    }
    public function appendToKeyInfo($zN)
    {
        $Lj = $this->sigNode;
        $tQ = $Lj->ownerDocument;
        $vD = $this->getXPathObj();
        if (!empty($vD)) {
            goto hX;
        }
        $vD = new DOMXPath($Lj->ownerDocument);
        $vD->registerNamespace("\163\x65\143\144\x73\151\147", self::XMLDSIGNS);
        hX:
        $NB = "\x2e\57\x73\x65\143\144\x73\x69\147\x3a\x4b\145\171\x49\x6e\146\x6f";
        $Wy = $vD->query($NB, $Lj);
        $He = $Wy->item(0);
        if ($He) {
            goto h9;
        }
        $BS = '';
        $kH = $Lj->lookupPrefix(self::XMLDSIGNS);
        if (empty($kH)) {
            goto TH;
        }
        $BS = $kH . "\x3a";
        TH:
        $lC = false;
        $He = $tQ->createElementNS(self::XMLDSIGNS, $BS . "\113\x65\171\x49\x6e\x66\157");
        $NB = "\x2e\x2f\163\x65\x63\x64\x73\151\147\72\x4f\142\x6a\x65\x63\164";
        $Wy = $vD->query($NB, $Lj);
        if (!($Wt = $Wy->item(0))) {
            goto Z1;
        }
        $Wt->parentNode->insertBefore($He, $Wt);
        $lC = true;
        Z1:
        if ($lC) {
            goto Uo;
        }
        $Lj->appendChild($He);
        Uo:
        h9:
        $He->appendChild($zN);
        return $He;
    }
    public function getValidatedNodes()
    {
        return $this->validatedNodes;
    }
}
