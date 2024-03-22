<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecEnc;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use DOMElement;
class XMLSecurityDSig
{
    const XMLDSIGNS = "\150\164\x74\160\72\x2f\x2f\167\167\x77\56\x77\x33\56\157\x72\x67\x2f\62\60\x30\x30\57\60\71\57\170\x6d\154\144\163\151\147\43";
    const SHA1 = "\x68\164\164\160\72\x2f\57\x77\x77\x77\56\x77\x33\56\157\x72\x67\x2f\62\x30\60\60\x2f\60\71\x2f\x78\x6d\x6c\144\163\x69\x67\43\x73\x68\x61\x31";
    const SHA256 = "\150\164\x74\160\x3a\x2f\x2f\167\167\167\56\x77\63\x2e\157\162\147\57\x32\60\x30\61\57\60\x34\57\x78\155\x6c\145\x6e\x63\43\163\x68\x61\62\65\x36";
    const SHA384 = "\x68\164\x74\x70\72\57\x2f\x77\167\x77\x2e\167\x33\56\157\x72\147\57\62\x30\60\61\57\60\64\57\170\x6d\x6c\x64\x73\151\x67\x2d\155\x6f\162\145\x23\x73\150\x61\x33\70\64";
    const SHA512 = "\150\x74\x74\160\72\x2f\57\x77\167\167\56\167\x33\56\x6f\x72\147\57\62\x30\x30\61\57\x30\x34\57\170\155\x6c\145\x6e\x63\43\x73\x68\141\x35\x31\x32";
    const RIPEMD160 = "\x68\164\x74\x70\72\57\57\x77\167\x77\56\x77\63\x2e\157\x72\147\x2f\62\x30\60\61\57\x30\64\57\170\x6d\x6c\145\156\x63\x23\162\151\160\x65\155\144\x31\x36\x30";
    const C14N = "\x68\164\x74\160\72\57\57\x77\167\167\56\167\63\x2e\x6f\x72\147\x2f\124\122\x2f\62\60\60\61\x2f\x52\105\103\55\170\x6d\x6c\55\x63\x31\64\156\55\x32\x30\x30\x31\60\63\x31\x35";
    const C14N_COMMENTS = "\x68\x74\x74\160\x3a\57\57\x77\x77\x77\x2e\x77\63\56\157\162\147\x2f\124\x52\57\62\x30\60\x31\57\x52\105\x43\55\x78\x6d\x6c\x2d\143\61\x34\156\x2d\x32\x30\60\x31\60\x33\x31\x35\x23\x57\x69\x74\x68\103\157\x6d\x6d\145\156\164\x73";
    const EXC_C14N = "\x68\x74\164\x70\x3a\57\57\167\x77\x77\56\x77\x33\56\x6f\162\x67\x2f\62\x30\60\x31\57\61\x30\x2f\170\155\154\55\x65\170\x63\x2d\x63\x31\x34\x6e\x23";
    const EXC_C14N_COMMENTS = "\x68\164\x74\160\x3a\x2f\x2f\167\x77\167\x2e\167\x33\56\x6f\x72\147\x2f\62\60\60\61\57\61\60\57\170\x6d\x6c\55\145\170\x63\x2d\x63\x31\x34\x6e\43\x57\x69\x74\150\x43\157\155\155\145\x6e\164\163";
    const template = "\x3c\144\163\x3a\123\151\147\x6e\141\164\x75\x72\x65\40\x78\155\x6c\156\x73\72\144\x73\x3d\x22\x68\x74\164\160\x3a\57\x2f\x77\x77\167\x2e\x77\63\56\x6f\x72\x67\x2f\62\x30\60\60\57\x30\x39\x2f\x78\x6d\154\x64\x73\151\147\x23\x22\76\15\12\x20\40\40\x3c\144\x73\72\123\151\x67\x6e\x65\144\111\x6e\x66\157\76\xd\12\40\40\x20\x20\x20\74\x64\x73\x3a\x53\x69\147\156\x61\164\165\x72\x65\x4d\145\x74\150\x6f\x64\x20\57\x3e\15\12\x20\x20\x20\x3c\x2f\x64\163\x3a\123\151\147\156\x65\144\x49\156\x66\157\76\xd\12\40\x3c\x2f\x64\x73\72\123\151\147\x6e\141\x74\x75\x72\x65\76";
    const BASE_TEMPLATE = "\x3c\x53\x69\x67\x6e\x61\x74\x75\x72\145\40\170\155\x6c\x6e\x73\75\x22\x68\164\x74\160\x3a\x2f\x2f\167\167\167\x2e\167\x33\x2e\157\162\x67\x2f\62\60\x30\x30\57\x30\x39\x2f\170\x6d\x6c\144\163\151\147\x23\42\76\xd\xa\x20\x20\x20\x3c\123\151\147\156\145\144\x49\x6e\146\x6f\76\15\12\x20\x20\x20\40\x20\x3c\x53\151\147\156\x61\x74\165\x72\x65\115\145\x74\150\157\144\40\57\x3e\xd\12\x20\40\x20\x3c\x2f\x53\151\147\156\145\x64\x49\x6e\x66\157\x3e\xd\xa\x20\x3c\x2f\123\151\147\x6e\x61\164\x75\162\145\76";
    public $sigNode = null;
    public $idKeys = array();
    public $idNS = array();
    private $signedInfo = null;
    private $xPathCtx = null;
    private $canonicalMethod = null;
    private $prefix = '';
    private $searchpfx = "\x73\x65\x63\144\x73\151\x67";
    private $validatedNodes = null;
    public function __construct($pw = "\144\x73")
    {
        $ah = self::BASE_TEMPLATE;
        if (empty($pw)) {
            goto ZM;
        }
        $this->prefix = $pw . "\x3a";
        $QY = array("\74\x53", "\x3c\57\x53", "\170\155\x6c\x6e\163\x3d");
        $PE = array("\x3c{$pw}\72\x53", "\74\57{$pw}\72\x53", "\x78\155\154\156\163\x3a{$pw}\75");
        $ah = str_replace($QY, $PE, $ah);
        ZM:
        $m5 = new DOMDocument();
        $m5->loadXML($ah);
        $this->sigNode = $m5->documentElement;
    }
    private function resetXPathObj()
    {
        $this->xPathCtx = null;
    }
    private function getXPathObj()
    {
        if (!(empty($this->xPathCtx) && !empty($this->sigNode))) {
            goto wB;
        }
        $gR = new DOMXPath($this->sigNode->ownerDocument);
        $gR->registerNamespace("\x73\145\143\x64\163\x69\147", self::XMLDSIGNS);
        $this->xPathCtx = $gR;
        wB:
        return $this->xPathCtx;
    }
    public static function generateGUID($pw = "\160\146\x78")
    {
        $V_ = md5(uniqid(mt_rand(), true));
        $Aw = $pw . substr($V_, 0, 8) . "\x2d" . substr($V_, 8, 4) . "\x2d" . substr($V_, 12, 4) . "\x2d" . substr($V_, 16, 4) . "\55" . substr($V_, 20, 12);
        return $Aw;
    }
    public static function generate_GUID($pw = "\x70\x66\x78")
    {
        return self::generateGUID($pw);
    }
    public function locateSignature($NV, $v1 = 0)
    {
        if ($NV instanceof DOMDocument) {
            goto pg;
        }
        $Dy = $NV->ownerDocument;
        goto xi;
        pg:
        $Dy = $NV;
        xi:
        if (!$Dy) {
            goto oO;
        }
        $gR = new DOMXPath($Dy);
        $gR->registerNamespace("\x73\x65\x63\144\163\x69\147", self::XMLDSIGNS);
        $dU = "\x2e\57\57\x73\x65\x63\x64\x73\x69\147\72\123\151\147\x6e\141\x74\165\162\145";
        $o7 = $gR->query($dU, $NV);
        $this->sigNode = $o7->item($v1);
        return $this->sigNode;
        oO:
        return null;
    }
    public function createNewSignNode($rb, $Yk = null)
    {
        $Dy = $this->sigNode->ownerDocument;
        if (!is_null($Yk)) {
            goto D4;
        }
        $w5 = $Dy->createElementNS(self::XMLDSIGNS, $this->prefix . $rb);
        goto G1;
        D4:
        $w5 = $Dy->createElementNS(self::XMLDSIGNS, $this->prefix . $rb, $Yk);
        G1:
        return $w5;
    }
    public function setCanonicalMethod($bs)
    {
        switch ($bs) {
            case "\x68\x74\x74\x70\x3a\x2f\x2f\167\167\167\x2e\167\x33\x2e\157\162\x67\57\124\122\57\x32\x30\60\x31\57\x52\105\x43\x2d\x78\155\154\55\143\x31\x34\x6e\55\62\x30\x30\61\x30\63\x31\65":
            case "\150\x74\x74\160\x3a\57\57\167\167\167\56\x77\63\56\x6f\162\x67\57\124\122\57\62\60\60\61\x2f\x52\x45\x43\x2d\x78\x6d\x6c\x2d\143\x31\64\x6e\55\62\x30\60\61\x30\63\x31\x35\x23\x57\151\164\x68\x43\x6f\x6d\155\145\x6e\164\x73":
            case "\150\x74\164\x70\x3a\57\x2f\x77\x77\167\x2e\x77\x33\x2e\157\x72\147\57\x32\60\x30\x31\57\x31\60\x2f\170\x6d\154\55\145\x78\x63\55\x63\x31\64\156\43":
            case "\150\x74\164\x70\x3a\57\57\167\x77\167\x2e\x77\63\x2e\157\x72\147\x2f\x32\x30\60\61\57\x31\60\x2f\170\155\154\x2d\145\170\143\x2d\x63\x31\64\156\x23\x57\151\x74\x68\x43\x6f\155\155\x65\x6e\x74\x73":
                $this->canonicalMethod = $bs;
                goto jG;
            default:
                throw new Exception("\111\x6e\x76\x61\x6c\151\x64\x20\103\x61\156\157\156\151\x63\x61\x6c\40\x4d\x65\164\150\x6f\144");
        }
        Oq:
        jG:
        if (!($gR = $this->getXPathObj())) {
            goto jP;
        }
        $dU = "\x2e\x2f" . $this->searchpfx . "\x3a\x53\x69\147\156\x65\x64\x49\156\x66\x6f";
        $o7 = $gR->query($dU, $this->sigNode);
        if (!($YR = $o7->item(0))) {
            goto AR;
        }
        $dU = "\56\x2f" . $this->searchpfx . "\103\x61\156\157\x6e\x69\143\141\154\151\172\x61\164\151\x6f\x6e\x4d\145\x74\x68\x6f\x64";
        $o7 = $gR->query($dU, $YR);
        if ($bF = $o7->item(0)) {
            goto WD;
        }
        $bF = $this->createNewSignNode("\x43\141\x6e\157\156\x69\x63\141\x6c\151\x7a\x61\164\151\157\x6e\x4d\145\x74\150\157\144");
        $YR->insertBefore($bF, $YR->firstChild);
        WD:
        $bF->setAttribute("\101\x6c\x67\x6f\x72\x69\x74\150\155", $this->canonicalMethod);
        AR:
        jP:
    }
    private function canonicalizeData($w5, $gk, $V4 = null, $wq = null)
    {
        $qS = false;
        $o2 = false;
        switch ($gk) {
            case "\150\164\164\160\x3a\x2f\57\167\x77\x77\x2e\x77\x33\56\x6f\x72\147\57\124\x52\57\62\60\60\61\x2f\122\x45\103\55\x78\x6d\154\x2d\x63\61\64\156\55\x32\x30\x30\61\60\x33\x31\x35":
                $qS = false;
                $o2 = false;
                goto MU;
            case "\150\x74\x74\160\x3a\57\x2f\x77\x77\167\56\x77\x33\x2e\157\162\147\x2f\124\x52\x2f\62\x30\60\61\57\x52\105\103\x2d\x78\155\x6c\55\x63\x31\64\156\55\62\x30\x30\61\60\63\61\x35\43\127\151\x74\150\103\x6f\155\x6d\x65\x6e\x74\163":
                $o2 = true;
                goto MU;
            case "\x68\164\164\x70\x3a\57\57\167\167\x77\x2e\x77\63\56\157\162\x67\57\62\60\60\61\x2f\61\x30\x2f\x78\x6d\154\55\145\x78\x63\x2d\143\x31\x34\156\43":
                $qS = true;
                goto MU;
            case "\x68\164\164\x70\72\57\x2f\x77\167\x77\x2e\x77\63\56\x6f\162\x67\x2f\x32\x30\60\x31\x2f\61\x30\57\170\x6d\x6c\x2d\x65\170\143\x2d\143\61\x34\156\43\127\x69\164\150\x43\x6f\x6d\155\x65\156\164\x73":
                $qS = true;
                $o2 = true;
                goto MU;
        }
        p0:
        MU:
        if (!(is_null($V4) && $w5 instanceof DOMNode && $w5->ownerDocument !== null && $w5->isSameNode($w5->ownerDocument->documentElement))) {
            goto PZ;
        }
        $rM = $w5;
        pT:
        if (!($nk = $rM->previousSibling)) {
            goto a7;
        }
        if (!($nk->nodeType == XML_PI_NODE || $nk->nodeType == XML_COMMENT_NODE && $o2)) {
            goto El;
        }
        goto a7;
        El:
        $rM = $nk;
        goto pT;
        a7:
        if (!($nk == null)) {
            goto f7;
        }
        $w5 = $w5->ownerDocument;
        f7:
        PZ:
        return $w5->C14N($qS, $o2, $V4, $wq);
    }
    public function canonicalizeSignedInfo()
    {
        $Dy = $this->sigNode->ownerDocument;
        $gk = null;
        if (!$Dy) {
            goto tm;
        }
        $gR = $this->getXPathObj();
        $dU = "\x2e\x2f\163\x65\x63\144\x73\151\x67\72\123\151\147\x6e\x65\x64\111\156\146\157";
        $o7 = $gR->query($dU, $this->sigNode);
        if (!($U9 = $o7->item(0))) {
            goto Fz;
        }
        $dU = "\x2e\57\163\x65\143\x64\163\x69\147\x3a\103\141\x6e\157\x6e\151\x63\141\154\151\172\141\x74\151\x6f\x6e\x4d\145\x74\150\157\x64";
        $o7 = $gR->query($dU, $U9);
        if (!($bF = $o7->item(0))) {
            goto b0;
        }
        $gk = $bF->getAttribute("\x41\x6c\147\157\162\151\164\150\x6d");
        b0:
        $this->signedInfo = $this->canonicalizeData($U9, $gk);
        return $this->signedInfo;
        Fz:
        tm:
        return null;
    }
    public function calculateDigest($lF, $F2, $BI = true)
    {
        switch ($lF) {
            case self::SHA1:
                $d_ = "\163\x68\x61\x31";
                goto uH;
            case self::SHA256:
                $d_ = "\x73\x68\x61\x32\65\x36";
                goto uH;
            case self::SHA384:
                $d_ = "\163\150\x61\x33\x38\64";
                goto uH;
            case self::SHA512:
                $d_ = "\163\150\141\x35\x31\62";
                goto uH;
            case self::RIPEMD160:
                $d_ = "\x72\151\160\145\x6d\144\61\66\x30";
                goto uH;
            default:
                throw new Exception("\x43\x61\156\156\157\164\40\x76\x61\154\151\144\141\x74\x65\x20\144\151\147\x65\163\164\x3a\x20\125\156\163\165\x70\x70\x6f\162\164\x65\144\40\101\154\x67\x6f\162\x69\x74\150\155\x20\x3c{$lF}\x3e");
        }
        Ga:
        uH:
        $J1 = hash($d_, $F2, true);
        if (!$BI) {
            goto Dc;
        }
        $J1 = base64_encode($J1);
        Dc:
        return $J1;
    }
    public function validateDigest($gI, $F2)
    {
        $gR = new DOMXPath($gI->ownerDocument);
        $gR->registerNamespace("\163\x65\143\144\x73\x69\x67", self::XMLDSIGNS);
        $dU = "\x73\x74\162\x69\156\147\50\x2e\x2f\x73\145\x63\144\x73\151\x67\x3a\104\x69\147\145\163\x74\115\x65\164\150\157\144\x2f\100\x41\154\147\x6f\x72\151\x74\150\x6d\x29";
        $lF = $gR->evaluate($dU, $gI);
        $Du = $this->calculateDigest($lF, $F2, false);
        $dU = "\x73\164\x72\151\x6e\147\x28\x2e\57\163\x65\x63\x64\163\151\147\x3a\x44\151\x67\x65\x73\164\126\x61\x6c\165\x65\x29";
        $W3 = $gR->evaluate($dU, $gI);
        return $Du == base64_decode($W3);
    }
    public function processTransforms($gI, $OM, $q9 = true)
    {
        $F2 = $OM;
        $gR = new DOMXPath($gI->ownerDocument);
        $gR->registerNamespace("\163\145\x63\144\x73\x69\x67", self::XMLDSIGNS);
        $dU = "\56\57\163\145\x63\x64\x73\x69\x67\x3a\x54\x72\141\156\x73\146\157\x72\155\x73\x2f\163\x65\143\x64\x73\151\x67\x3a\124\x72\x61\x6e\163\146\157\x72\155";
        $A8 = $gR->query($dU, $gI);
        $nV = "\x68\164\164\x70\72\x2f\x2f\167\167\x77\x2e\167\x33\x2e\157\x72\147\x2f\124\122\x2f\x32\x30\x30\61\x2f\x52\x45\103\55\x78\155\x6c\x2d\x63\61\x34\156\x2d\x32\60\60\61\x30\63\61\65";
        $V4 = null;
        $wq = null;
        foreach ($A8 as $UA) {
            $X7 = $UA->getAttribute("\101\154\147\x6f\x72\151\164\150\x6d");
            switch ($X7) {
                case "\150\x74\164\160\x3a\x2f\57\167\x77\167\56\x77\x33\x2e\x6f\x72\147\57\62\x30\60\x31\x2f\x31\60\57\170\x6d\154\55\x65\x78\x63\55\x63\x31\64\x6e\43":
                case "\150\x74\x74\160\x3a\x2f\57\x77\167\167\x2e\x77\63\x2e\157\x72\147\57\x32\60\x30\61\x2f\x31\x30\57\x78\155\x6c\55\145\170\x63\x2d\x63\61\x34\156\43\x57\x69\x74\150\103\157\155\155\x65\156\164\163":
                    if (!$q9) {
                        goto Lz;
                    }
                    $nV = $X7;
                    goto Xl;
                    Lz:
                    $nV = "\x68\164\164\160\x3a\x2f\57\x77\167\167\56\x77\63\56\157\x72\x67\x2f\62\x30\60\x31\x2f\x31\x30\57\x78\155\154\55\x65\x78\143\x2d\x63\61\x34\156\x23";
                    Xl:
                    $w5 = $UA->firstChild;
                    qz:
                    if (!$w5) {
                        goto lc;
                    }
                    if (!($w5->localName == "\x49\x6e\x63\x6c\165\163\x69\x76\145\116\x61\x6d\145\163\x70\x61\143\x65\x73")) {
                        goto Qo;
                    }
                    if (!($rT = $w5->getAttribute("\x50\162\x65\146\151\x78\x4c\x69\x73\164"))) {
                        goto UX;
                    }
                    $yb = array();
                    $MI = explode("\x20", $rT);
                    foreach ($MI as $rT) {
                        $RQ = trim($rT);
                        if (empty($RQ)) {
                            goto Ys;
                        }
                        $yb[] = $RQ;
                        Ys:
                        zA:
                    }
                    mi:
                    if (!(count($yb) > 0)) {
                        goto gG;
                    }
                    $wq = $yb;
                    gG:
                    UX:
                    goto lc;
                    Qo:
                    $w5 = $w5->nextSibling;
                    goto qz;
                    lc:
                    goto WQ;
                case "\150\164\164\160\72\57\x2f\x77\167\167\56\167\63\56\x6f\x72\x67\x2f\124\x52\x2f\x32\60\60\x31\x2f\x52\x45\103\x2d\170\x6d\154\55\143\61\64\x6e\x2d\x32\60\60\61\x30\x33\61\65":
                case "\x68\x74\164\160\x3a\x2f\x2f\167\x77\167\x2e\x77\x33\56\157\162\x67\x2f\x54\x52\57\62\60\x30\61\57\x52\105\103\x2d\170\x6d\x6c\x2d\143\61\x34\156\55\x32\x30\60\x31\60\63\61\x35\43\x57\x69\164\150\x43\157\x6d\x6d\145\156\164\163":
                    if (!$q9) {
                        goto Nk;
                    }
                    $nV = $X7;
                    goto KQ;
                    Nk:
                    $nV = "\x68\164\164\160\72\57\x2f\167\167\x77\x2e\167\x33\x2e\157\x72\147\x2f\x54\x52\57\x32\x30\x30\61\57\x52\x45\103\x2d\170\x6d\154\55\x63\x31\x34\x6e\x2d\62\x30\60\61\60\x33\61\x35";
                    KQ:
                    goto WQ;
                case "\150\x74\x74\x70\72\x2f\57\x77\x77\x77\x2e\x77\63\56\x6f\162\x67\x2f\x54\122\57\61\x39\71\71\57\x52\105\103\55\x78\160\141\164\150\x2d\61\71\x39\71\x31\61\x31\66":
                    $w5 = $UA->firstChild;
                    bZ:
                    if (!$w5) {
                        goto Q_;
                    }
                    if (!($w5->localName == "\x58\120\x61\164\150")) {
                        goto Ql;
                    }
                    $V4 = array();
                    $V4["\161\165\x65\x72\x79"] = "\50\x2e\57\57\56\x20\174\x20\56\57\x2f\x40\x2a\x20\174\x20\x2e\x2f\x2f\x6e\x61\155\145\163\160\x61\143\x65\x3a\x3a\52\51\x5b" . $w5->nodeValue . "\135";
                    $WO["\x6e\141\155\145\x73\160\x61\x63\145\163"] = array();
                    $zW = $gR->query("\56\57\156\141\155\x65\163\160\x61\143\x65\72\72\x2a", $w5);
                    foreach ($zW as $nC) {
                        if (!($nC->localName != "\170\x6d\154")) {
                            goto q7;
                        }
                        $V4["\156\x61\x6d\145\163\160\x61\143\x65\x73"][$nC->localName] = $nC->nodeValue;
                        q7:
                        vW:
                    }
                    IH:
                    goto Q_;
                    Ql:
                    $w5 = $w5->nextSibling;
                    goto bZ;
                    Q_:
                    goto WQ;
            }
            es:
            WQ:
            hV:
        }
        Ei:
        if (!$F2 instanceof DOMElement) {
            goto mZ;
        }
        $F2 = $this->canonicalizeData($OM, $nV, $V4, $wq);
        mZ:
        return $F2;
    }
    public function processRefNode($gI)
    {
        $fW = null;
        $q9 = true;
        if ($Rq = $gI->getAttribute("\x55\122\111")) {
            goto NX;
        }
        $q9 = false;
        $fW = $gI->ownerDocument;
        goto L4;
        NX:
        $dZ = parse_url($Rq);
        if (empty($dZ["\x70\141\x74\x68"])) {
            goto Pl;
        }
        $fW = file_get_contents($dZ);
        goto BC;
        Pl:
        if ($Yp = $dZ["\146\162\x61\147\155\x65\156\164"]) {
            goto Fi;
        }
        $fW = $gI->ownerDocument;
        goto p7;
        Fi:
        $q9 = false;
        $vc = new DOMXPath($gI->ownerDocument);
        if (!($this->idNS && is_array($this->idNS))) {
            goto IC;
        }
        foreach ($this->idNS as $Ii => $za) {
            $vc->registerNamespace($Ii, $za);
            jH:
        }
        B9:
        IC:
        $kX = "\100\111\x64\x3d\x22" . $Yp . "\x22";
        if (!is_array($this->idKeys)) {
            goto O1;
        }
        foreach ($this->idKeys as $ZS) {
            $kX .= "\40\157\162\40\x40{$ZS}\75\47{$Yp}\47";
            wb:
        }
        Pu:
        O1:
        $dU = "\x2f\57\52\133" . $kX . "\x5d";
        $fW = $vc->query($dU)->item(0);
        p7:
        BC:
        L4:
        $F2 = $this->processTransforms($gI, $fW, $q9);
        if ($this->validateDigest($gI, $F2)) {
            goto Xu;
        }
        return false;
        Xu:
        if (!$fW instanceof DOMElement) {
            goto Iy;
        }
        if (!empty($Yp)) {
            goto ju;
        }
        $this->validatedNodes[] = $fW;
        goto rR;
        ju:
        $this->validatedNodes[$Yp] = $fW;
        rR:
        Iy:
        return true;
    }
    public function getRefNodeID($gI)
    {
        if (!($Rq = $gI->getAttribute("\x55\122\x49"))) {
            goto PX;
        }
        $dZ = parse_url($Rq);
        if (!empty($dZ["\x70\x61\x74\150"])) {
            goto TM;
        }
        if (!($Yp = $dZ["\x66\162\141\147\155\x65\x6e\x74"])) {
            goto dM;
        }
        return $Yp;
        dM:
        TM:
        PX:
        return null;
    }
    public function getRefIDs()
    {
        $oW = array();
        $gR = $this->getXPathObj();
        $dU = "\x2e\57\x73\x65\x63\144\x73\x69\x67\x3a\x53\151\147\x6e\145\x64\111\156\146\x6f\57\163\x65\x63\144\163\151\147\x3a\x52\x65\x66\x65\x72\145\156\x63\145";
        $o7 = $gR->query($dU, $this->sigNode);
        if (!($o7->length == 0)) {
            goto Dj;
        }
        throw new Exception("\122\145\x66\x65\x72\x65\156\x63\x65\x20\x6e\157\144\x65\x73\x20\x6e\x6f\164\x20\146\157\165\x6e\144");
        Dj:
        foreach ($o7 as $gI) {
            $oW[] = $this->getRefNodeID($gI);
            Qv:
        }
        dz:
        return $oW;
    }
    public function validateReference()
    {
        $Mv = $this->sigNode->ownerDocument->documentElement;
        if ($Mv->isSameNode($this->sigNode)) {
            goto Tl;
        }
        if (!($this->sigNode->parentNode != null)) {
            goto aO;
        }
        $this->sigNode->parentNode->removeChild($this->sigNode);
        aO:
        Tl:
        $gR = $this->getXPathObj();
        $dU = "\56\x2f\x73\145\143\x64\163\151\x67\x3a\x53\151\x67\156\x65\144\x49\156\146\x6f\x2f\x73\145\x63\x64\x73\x69\x67\x3a\x52\145\x66\145\162\x65\156\143\x65";
        $o7 = $gR->query($dU, $this->sigNode);
        if (!($o7->length == 0)) {
            goto Vl;
        }
        throw new Exception("\122\x65\x66\145\x72\x65\156\x63\145\x20\x6e\x6f\x64\145\x73\40\156\157\164\x20\x66\x6f\165\156\144");
        Vl:
        $this->validatedNodes = array();
        foreach ($o7 as $gI) {
            if ($this->processRefNode($gI)) {
                goto Eq;
            }
            $this->validatedNodes = null;
            throw new Exception("\x52\145\x66\x65\x72\x65\x6e\143\145\40\166\141\x6c\x69\x64\141\164\151\x6f\156\x20\x66\141\151\154\x65\x64");
            Eq:
            vP:
        }
        gJ:
        return true;
    }
    private function addRefInternal($ts, $w5, $X7, $fB = null, $jp = null)
    {
        $pw = null;
        $Gs = null;
        $uJ = "\111\x64";
        $NC = true;
        $jG = false;
        if (!is_array($jp)) {
            goto Md;
        }
        $pw = empty($jp["\160\162\145\x66\x69\x78"]) ? null : $jp["\x70\x72\145\146\x69\170"];
        $Gs = empty($jp["\160\x72\x65\x66\x69\x78\x5f\x6e\163"]) ? null : $jp["\x70\x72\145\x66\151\x78\x5f\x6e\163"];
        $uJ = empty($jp["\x69\144\x5f\x6e\x61\x6d\145"]) ? "\x49\144" : $jp["\151\x64\137\156\141\155\145"];
        $NC = !isset($jp["\x6f\x76\145\162\167\x72\151\x74\145"]) ? true : (bool) $jp["\x6f\x76\145\162\x77\162\151\x74\x65"];
        $jG = !isset($jp["\x66\x6f\x72\x63\145\x5f\165\x72\151"]) ? false : (bool) $jp["\x66\x6f\x72\x63\145\137\165\162\151"];
        Md:
        $Qr = $uJ;
        if (empty($pw)) {
            goto f0;
        }
        $Qr = $pw . "\x3a" . $Qr;
        f0:
        $gI = $this->createNewSignNode("\x52\145\x66\x65\x72\x65\156\143\145");
        $ts->appendChild($gI);
        if (!$w5 instanceof DOMDocument) {
            goto Z_;
        }
        if ($jG) {
            goto pw;
        }
        goto eZ;
        Z_:
        $Rq = null;
        if ($NC) {
            goto VG;
        }
        $Rq = $Gs ? $w5->getAttributeNS($Gs, $uJ) : $w5->getAttribute($uJ);
        VG:
        if (!empty($Rq)) {
            goto gM;
        }
        $Rq = self::generateGUID();
        $w5->setAttributeNS($Gs, $Qr, $Rq);
        gM:
        $gI->setAttribute("\125\122\111", "\x23" . $Rq);
        goto eZ;
        pw:
        $gI->setAttribute("\x55\122\111", '');
        eZ:
        $ym = $this->createNewSignNode("\124\162\141\x6e\x73\146\157\162\155\x73");
        $gI->appendChild($ym);
        if (is_array($fB)) {
            goto wE;
        }
        if (!empty($this->canonicalMethod)) {
            goto n0;
        }
        goto HQ;
        wE:
        foreach ($fB as $UA) {
            $d0 = $this->createNewSignNode("\x54\x72\x61\x6e\163\x66\157\x72\x6d");
            $ym->appendChild($d0);
            if (is_array($UA) && !empty($UA["\150\x74\x74\x70\72\57\57\x77\x77\x77\56\x77\x33\x2e\x6f\x72\147\x2f\x54\122\57\x31\71\71\x39\x2f\x52\x45\103\x2d\170\160\x61\x74\x68\55\61\x39\71\x39\x31\61\61\x36"]) && !empty($UA["\x68\x74\164\x70\72\x2f\57\167\x77\167\x2e\167\63\56\x6f\162\x67\57\x54\122\57\61\x39\71\x39\57\x52\105\x43\x2d\x78\x70\141\x74\x68\55\61\71\71\71\61\61\x31\x36"]["\161\165\x65\162\171"])) {
                goto cs;
            }
            $d0->setAttribute("\101\x6c\x67\157\162\151\x74\150\x6d", $UA);
            goto qT;
            cs:
            $d0->setAttribute("\x41\x6c\x67\157\x72\x69\x74\150\x6d", "\x68\x74\164\x70\72\57\57\x77\167\x77\x2e\x77\63\56\x6f\162\x67\57\x54\x52\x2f\x31\x39\71\x39\57\122\105\x43\x2d\170\160\x61\164\150\55\61\x39\71\71\61\61\x31\66");
            $uX = $this->createNewSignNode("\x58\x50\x61\164\x68", $UA["\150\164\x74\160\x3a\57\57\x77\167\x77\56\167\63\56\x6f\162\x67\57\x54\x52\x2f\x31\71\71\x39\x2f\122\105\x43\55\x78\160\141\164\150\55\61\x39\x39\71\x31\x31\61\66"]["\161\165\145\x72\x79"]);
            $d0->appendChild($uX);
            if (empty($UA["\150\164\164\160\72\57\x2f\167\167\x77\56\167\x33\56\x6f\162\x67\x2f\124\x52\x2f\x31\71\x39\71\x2f\x52\105\103\x2d\170\160\x61\164\x68\x2d\61\71\x39\71\x31\61\x31\66"]["\x6e\x61\x6d\x65\163\160\x61\x63\145\163"])) {
                goto Br;
            }
            foreach ($UA["\x68\164\x74\x70\72\x2f\x2f\x77\167\x77\x2e\x77\x33\x2e\x6f\x72\x67\57\124\x52\57\x31\x39\x39\71\57\122\105\x43\55\170\x70\x61\164\x68\55\61\x39\71\x39\x31\61\61\66"]["\156\141\155\x65\x73\160\x61\x63\145\163"] as $pw => $x5) {
                $uX->setAttributeNS("\x68\x74\x74\x70\72\x2f\57\x77\167\x77\56\x77\63\x2e\x6f\162\147\57\62\60\x30\60\x2f\x78\155\x6c\x6e\163\x2f", "\170\155\x6c\x6e\x73\72{$pw}", $x5);
                QU:
            }
            DQ:
            Br:
            qT:
            hG:
        }
        oK:
        goto HQ;
        n0:
        $d0 = $this->createNewSignNode("\x54\x72\141\156\x73\146\x6f\x72\x6d");
        $ym->appendChild($d0);
        $d0->setAttribute("\101\154\x67\157\x72\151\164\150\x6d", $this->canonicalMethod);
        HQ:
        $xc = $this->processTransforms($gI, $w5);
        $Du = $this->calculateDigest($X7, $xc);
        $I3 = $this->createNewSignNode("\104\151\x67\x65\x73\164\x4d\x65\164\x68\157\144");
        $gI->appendChild($I3);
        $I3->setAttribute("\101\154\x67\157\162\151\x74\x68\x6d", $X7);
        $W3 = $this->createNewSignNode("\x44\151\147\145\x73\x74\126\x61\154\x75\145", $Du);
        $gI->appendChild($W3);
    }
    public function addReference($w5, $X7, $fB = null, $jp = null)
    {
        if (!($gR = $this->getXPathObj())) {
            goto Ok;
        }
        $dU = "\56\57\x73\145\x63\144\x73\151\147\72\123\x69\147\156\145\144\x49\x6e\146\x6f";
        $o7 = $gR->query($dU, $this->sigNode);
        if (!($Tk = $o7->item(0))) {
            goto h6;
        }
        $this->addRefInternal($Tk, $w5, $X7, $fB, $jp);
        h6:
        Ok:
    }
    public function addReferenceList($wC, $X7, $fB = null, $jp = null)
    {
        if (!($gR = $this->getXPathObj())) {
            goto Zs;
        }
        $dU = "\56\57\x73\x65\x63\x64\163\151\147\x3a\123\x69\x67\x6e\x65\144\x49\156\x66\x6f";
        $o7 = $gR->query($dU, $this->sigNode);
        if (!($Tk = $o7->item(0))) {
            goto N2;
        }
        foreach ($wC as $w5) {
            $this->addRefInternal($Tk, $w5, $X7, $fB, $jp);
            Ct:
        }
        gR:
        N2:
        Zs:
    }
    public function addObject($F2, $Gn = null, $sp = null)
    {
        $n3 = $this->createNewSignNode("\117\142\152\x65\143\164");
        $this->sigNode->appendChild($n3);
        if (empty($Gn)) {
            goto qB;
        }
        $n3->setAttribute("\115\x69\155\145\x54\171\160\145", $Gn);
        qB:
        if (empty($sp)) {
            goto HG;
        }
        $n3->setAttribute("\x45\x6e\143\x6f\x64\151\156\x67", $sp);
        HG:
        if ($F2 instanceof DOMElement) {
            goto lK;
        }
        $dc = $this->sigNode->ownerDocument->createTextNode($F2);
        goto Et;
        lK:
        $dc = $this->sigNode->ownerDocument->importNode($F2, true);
        Et:
        $n3->appendChild($dc);
        return $n3;
    }
    public function locateKey($w5 = null)
    {
        if (!empty($w5)) {
            goto nl;
        }
        $w5 = $this->sigNode;
        nl:
        if ($w5 instanceof DOMNode) {
            goto hy;
        }
        return null;
        hy:
        if (!($Dy = $w5->ownerDocument)) {
            goto gt;
        }
        $gR = new DOMXPath($Dy);
        $gR->registerNamespace("\163\x65\143\x64\163\x69\x67", self::XMLDSIGNS);
        $dU = "\x73\164\x72\151\156\147\x28\56\57\x73\x65\x63\x64\x73\x69\x67\x3a\x53\151\x67\156\x65\144\111\156\x66\157\x2f\163\x65\x63\x64\163\151\x67\x3a\123\151\x67\156\x61\164\165\x72\145\115\145\x74\x68\157\x64\x2f\100\x41\x6c\147\x6f\162\x69\164\x68\x6d\51";
        $X7 = $gR->evaluate($dU, $w5);
        if (!$X7) {
            goto yQ;
        }
        try {
            $Tb = new XMLSecurityKey($X7, array("\x74\x79\x70\145" => "\x70\165\142\x6c\151\x63"));
        } catch (Exception $sS) {
            return null;
        }
        return $Tb;
        yQ:
        gt:
        return null;
    }
    public function verify($Tb)
    {
        $Dy = $this->sigNode->ownerDocument;
        $gR = new DOMXPath($Dy);
        $gR->registerNamespace("\163\x65\x63\x64\x73\x69\147", self::XMLDSIGNS);
        $dU = "\163\x74\x72\x69\x6e\x67\x28\56\x2f\163\145\143\144\163\151\147\x3a\x53\151\x67\x6e\x61\164\165\162\x65\126\141\154\x75\x65\x29";
        $L1 = $gR->evaluate($dU, $this->sigNode);
        if (!empty($L1)) {
            goto v2;
        }
        throw new Exception("\125\156\x61\142\154\x65\x20\x74\157\40\x6c\157\143\141\x74\x65\40\x53\151\147\x6e\x61\164\x75\162\x65\x56\141\x6c\165\145");
        v2:
        return $Tb->verifySignature($this->signedInfo, base64_decode($L1));
    }
    public function signData($Tb, $F2)
    {
        return $Tb->signData($F2);
    }
    public function sign($Tb, $FE = null)
    {
        if (!($FE != null)) {
            goto Lf;
        }
        $this->resetXPathObj();
        $this->appendSignature($FE);
        $this->sigNode = $FE->lastChild;
        Lf:
        if (!($gR = $this->getXPathObj())) {
            goto ZP;
        }
        $dU = "\56\x2f\x73\145\143\x64\163\x69\x67\x3a\x53\151\147\x6e\145\144\x49\x6e\x66\157";
        $o7 = $gR->query($dU, $this->sigNode);
        if (!($Tk = $o7->item(0))) {
            goto HI;
        }
        $dU = "\x2e\57\x73\145\x63\x64\x73\x69\x67\x3a\123\x69\147\x6e\x61\x74\x75\x72\x65\x4d\x65\x74\x68\x6f\144";
        $o7 = $gR->query($dU, $Tk);
        $E_ = $o7->item(0);
        $E_->setAttribute("\101\x6c\x67\x6f\x72\x69\x74\150\x6d", $Tb->type);
        $F2 = $this->canonicalizeData($Tk, $this->canonicalMethod);
        $L1 = base64_encode($this->signData($Tb, $F2));
        $f1 = $this->createNewSignNode("\123\151\147\x6e\141\x74\165\x72\145\126\141\x6c\x75\145", $L1);
        if ($la = $Tk->nextSibling) {
            goto mV;
        }
        $this->sigNode->appendChild($f1);
        goto Oz;
        mV:
        $la->parentNode->insertBefore($f1, $la);
        Oz:
        HI:
        ZP:
    }
    public function appendCert()
    {
    }
    public function appendKey($Tb, $US = null)
    {
        $Tb->serializeKey($US);
    }
    public function insertSignature($w5, $sK = null)
    {
        $L5 = $w5->ownerDocument;
        $yt = $L5->importNode($this->sigNode, true);
        if ($sK == null) {
            goto u8;
        }
        return $w5->insertBefore($yt, $sK);
        goto v3;
        u8:
        return $w5->insertBefore($yt);
        v3:
    }
    public function appendSignature($sA, $G1 = false)
    {
        $sK = $G1 ? $sA->firstChild : null;
        return $this->insertSignature($sA, $sK);
    }
    public static function get509XCert($hj, $Ed = true)
    {
        $a4 = self::staticGet509XCerts($hj, $Ed);
        if (empty($a4)) {
            goto d4;
        }
        return $a4[0];
        d4:
        return '';
    }
    public static function staticGet509XCerts($a4, $Ed = true)
    {
        if ($Ed) {
            goto ca;
        }
        return array($a4);
        goto KR;
        ca:
        $F2 = '';
        $ho = array();
        $Eu = explode("\12", $a4);
        $S3 = false;
        foreach ($Eu as $gu) {
            if (!$S3) {
                goto xN;
            }
            if (!(strncmp($gu, "\55\x2d\x2d\x2d\55\105\116\x44\40\103\105\x52\x54\111\106\111\103\101\x54\105", 20) == 0)) {
                goto MO;
            }
            $S3 = false;
            $ho[] = $F2;
            $F2 = '';
            goto bO;
            MO:
            $F2 .= trim($gu);
            goto H2;
            xN:
            if (!(strncmp($gu, "\x2d\55\x2d\x2d\55\x42\x45\x47\111\x4e\40\x43\105\x52\124\111\106\111\x43\x41\124\105", 22) == 0)) {
                goto Ow;
            }
            $S3 = true;
            Ow:
            H2:
            bO:
        }
        Dn:
        return $ho;
        KR:
    }
    public static function staticAdd509Cert($DK, $hj, $Ed = true, $oH = false, $gR = null, $jp = null)
    {
        if (!$oH) {
            goto YX;
        }
        $hj = file_get_contents($hj);
        YX:
        if ($DK instanceof DOMElement) {
            goto o8;
        }
        throw new Exception("\x49\x6e\166\141\154\151\x64\x20\x70\x61\x72\x65\x6e\164\40\x4e\157\x64\x65\40\160\141\x72\x61\155\145\x74\x65\162");
        o8:
        $Z3 = $DK->ownerDocument;
        if (!empty($gR)) {
            goto v4;
        }
        $gR = new DOMXPath($DK->ownerDocument);
        $gR->registerNamespace("\x73\x65\x63\144\163\151\147", self::XMLDSIGNS);
        v4:
        $dU = "\x2e\57\x73\x65\x63\144\163\x69\147\72\113\x65\x79\111\156\x66\x6f";
        $o7 = $gR->query($dU, $DK);
        $Fp = $o7->item(0);
        $ue = '';
        if (!$Fp) {
            goto G7;
        }
        $rT = $Fp->lookupPrefix(self::XMLDSIGNS);
        if (empty($rT)) {
            goto B0;
        }
        $ue = $rT . "\72";
        B0:
        goto Tp;
        G7:
        $rT = $DK->lookupPrefix(self::XMLDSIGNS);
        if (empty($rT)) {
            goto Su;
        }
        $ue = $rT . "\x3a";
        Su:
        $xR = false;
        $Fp = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\113\145\171\111\156\x66\157");
        $dU = "\x2e\57\x73\145\x63\144\x73\x69\147\72\117\142\152\x65\x63\x74";
        $o7 = $gR->query($dU, $DK);
        if (!($F4 = $o7->item(0))) {
            goto vG;
        }
        $F4->parentNode->insertBefore($Fp, $F4);
        $xR = true;
        vG:
        if ($xR) {
            goto n9;
        }
        $DK->appendChild($Fp);
        n9:
        Tp:
        $a4 = self::staticGet509XCerts($hj, $Ed);
        $LU = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\130\65\x30\71\104\141\164\x61");
        $Fp->appendChild($LU);
        $v2 = false;
        $fS = false;
        if (!is_array($jp)) {
            goto ln;
        }
        if (empty($jp["\151\163\163\x75\x65\162\123\145\162\x69\x61\154"])) {
            goto CW;
        }
        $v2 = true;
        CW:
        if (empty($jp["\x73\x75\142\x6a\145\143\164\116\x61\x6d\145"])) {
            goto bL;
        }
        $fS = true;
        bL:
        ln:
        foreach ($a4 as $TF) {
            if (!($v2 || $fS)) {
                goto Yn;
            }
            if (!($MS = openssl_x509_parse("\x2d\55\x2d\55\x2d\102\x45\x47\111\116\40\x43\105\122\x54\x49\x46\111\103\101\x54\x45\x2d\x2d\55\55\55\12" . chunk_split($TF, 64, "\12") . "\55\55\55\55\x2d\x45\x4e\104\x20\103\105\122\124\111\106\111\103\101\x54\x45\x2d\55\x2d\x2d\55\12"))) {
                goto Uo;
            }
            if (!($fS && !empty($MS["\163\165\x62\152\145\143\164"]))) {
                goto YI;
            }
            if (is_array($MS["\x73\x75\142\x6a\145\143\x74"])) {
                goto Xe;
            }
            $Iz = $MS["\151\x73\x73\x75\x65\x72"];
            goto a8;
            Xe:
            $wv = array();
            foreach ($MS["\x73\165\x62\152\145\x63\x74"] as $zg => $Yk) {
                if (is_array($Yk)) {
                    goto vq;
                }
                array_unshift($wv, "{$zg}\x3d{$Yk}");
                goto wT;
                vq:
                foreach ($Yk as $xE) {
                    array_unshift($wv, "{$zg}\x3d{$xE}");
                    J2:
                }
                iG:
                wT:
                xP:
            }
            W1:
            $Iz = implode("\54", $wv);
            a8:
            $Uh = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\130\65\x30\x39\x53\165\x62\152\145\x63\x74\x4e\141\155\x65", $Iz);
            $LU->appendChild($Uh);
            YI:
            if (!($v2 && !empty($MS["\151\x73\x73\x75\x65\x72"]) && !empty($MS["\x73\145\162\151\x61\154\x4e\x75\155\142\145\162"]))) {
                goto NZ;
            }
            if (is_array($MS["\x69\x73\x73\x75\145\x72"])) {
                goto ik;
            }
            $kB = $MS["\151\163\163\165\x65\162"];
            goto JE;
            ik:
            $wv = array();
            foreach ($MS["\x69\x73\x73\x75\145\x72"] as $zg => $Yk) {
                array_unshift($wv, "{$zg}\x3d{$Yk}");
                xI:
            }
            nb:
            $kB = implode("\x2c", $wv);
            JE:
            $a7 = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\x58\x35\60\71\111\x73\x73\165\x65\x72\123\145\162\x69\x61\x6c");
            $LU->appendChild($a7);
            $FH = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\130\65\x30\x39\x49\x73\x73\x75\x65\x72\116\141\x6d\145", $kB);
            $a7->appendChild($FH);
            $FH = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\130\65\x30\x39\x53\x65\x72\x69\141\x6c\x4e\165\x6d\142\145\162", $MS["\x73\145\162\151\x61\x6c\116\x75\x6d\x62\x65\x72"]);
            $a7->appendChild($FH);
            NZ:
            Uo:
            Yn:
            $fg = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\x58\65\x30\x39\103\x65\x72\x74\151\146\151\x63\x61\x74\145", $TF);
            $LU->appendChild($fg);
            kc:
        }
        vu:
    }
    public function add509Cert($hj, $Ed = true, $oH = false, $jp = null)
    {
        if (!($gR = $this->getXPathObj())) {
            goto LL;
        }
        self::staticAdd509Cert($this->sigNode, $hj, $Ed, $oH, $gR, $jp);
        LL:
    }
    public function appendToKeyInfo($w5)
    {
        $DK = $this->sigNode;
        $Z3 = $DK->ownerDocument;
        $gR = $this->getXPathObj();
        if (!empty($gR)) {
            goto dY;
        }
        $gR = new DOMXPath($DK->ownerDocument);
        $gR->registerNamespace("\163\145\x63\144\x73\x69\x67", self::XMLDSIGNS);
        dY:
        $dU = "\56\57\163\x65\x63\144\x73\151\147\x3a\113\145\171\111\156\x66\157";
        $o7 = $gR->query($dU, $DK);
        $Fp = $o7->item(0);
        if ($Fp) {
            goto iU;
        }
        $ue = '';
        $rT = $DK->lookupPrefix(self::XMLDSIGNS);
        if (empty($rT)) {
            goto eD;
        }
        $ue = $rT . "\72";
        eD:
        $xR = false;
        $Fp = $Z3->createElementNS(self::XMLDSIGNS, $ue . "\x4b\145\x79\111\x6e\146\x6f");
        $dU = "\56\57\163\x65\x63\144\163\x69\147\x3a\117\x62\x6a\145\143\x74";
        $o7 = $gR->query($dU, $DK);
        if (!($F4 = $o7->item(0))) {
            goto FK;
        }
        $F4->parentNode->insertBefore($Fp, $F4);
        $xR = true;
        FK:
        if ($xR) {
            goto jm;
        }
        $DK->appendChild($Fp);
        jm:
        iU:
        $Fp->appendChild($w5);
        return $Fp;
    }
    public function getValidatedNodes()
    {
        return $this->validatedNodes;
    }
}
