<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Exception;
class XMLSecEnc
{
    const template = "\x3c\x78\145\156\x63\72\x45\x6e\143\162\171\x70\164\145\144\104\141\x74\x61\x20\x78\155\154\156\x73\72\170\145\156\143\x3d\x27\150\164\x74\160\x3a\x2f\x2f\167\x77\167\56\x77\63\x2e\x6f\x72\147\57\62\x30\x30\61\x2f\x30\x34\x2f\x78\155\x6c\x65\156\x63\43\47\x3e\xd\12\40\40\x20\74\x78\x65\156\143\x3a\x43\x69\160\150\x65\162\104\x61\164\x61\x3e\15\12\40\x20\x20\x20\40\40\74\170\x65\x6e\143\x3a\x43\151\160\150\x65\x72\126\x61\x6c\165\145\76\x3c\x2f\x78\x65\x6e\143\72\x43\x69\x70\x68\145\x72\126\x61\x6c\x75\145\x3e\xd\12\x20\x20\40\x3c\57\170\145\x6e\143\72\x43\151\x70\x68\x65\162\x44\141\x74\141\76\xd\12\x3c\x2f\170\145\156\x63\72\105\x6e\x63\x72\171\160\164\x65\144\x44\x61\164\141\x3e";
    const Element = "\x68\x74\x74\160\x3a\x2f\57\167\x77\x77\56\x77\x33\x2e\157\162\147\57\x32\60\60\61\57\x30\64\x2f\x78\x6d\x6c\x65\156\x63\x23\105\x6c\x65\x6d\145\156\164";
    const Content = "\x68\x74\x74\x70\x3a\57\57\x77\x77\x77\56\x77\x33\56\x6f\x72\147\x2f\x32\60\x30\x31\x2f\x30\64\57\170\x6d\x6c\x65\x6e\x63\43\x43\x6f\x6e\164\145\156\164";
    const URI = 3;
    const XMLENCNS = "\150\164\x74\160\72\57\57\x77\x77\x77\x2e\x77\x33\56\157\x72\x67\57\62\x30\60\x31\x2f\x30\x34\57\x78\x6d\x6c\x65\156\x63\43";
    public $type = null;
    public $encKey = null;
    private $encdoc = null;
    private $rawNode = null;
    private $references = array();
    public function __construct()
    {
        $this->_resetTemplate();
    }
    private function _resetTemplate()
    {
        $this->encdoc = new DOMDocument();
        $this->encdoc->loadXML(self::template);
    }
    public function addReference($yt, $zN, $qI)
    {
        if ($zN instanceof DOMNode) {
            goto VF;
        }
        throw new Exception("\44\156\157\144\x65\x20\x69\163\40\156\x6f\164\x20\x6f\x66\x20\x74\171\x70\x65\x20\x44\117\x4d\x4e\x6f\144\145");
        VF:
        $g5 = $this->encdoc;
        $this->_resetTemplate();
        $Om = $this->encdoc;
        $this->encdoc = $g5;
        $SF = XMLSecurityDSig::generateGUID();
        $hL = $Om->documentElement;
        $hL->setAttribute("\111\144", $SF);
        $this->references[$yt] = array("\x6e\x6f\x64\145" => $zN, "\164\x79\160\145" => $qI, "\145\x6e\x63\x6e\157\144\x65" => $Om, "\x72\x65\146\165\x72\151" => $SF);
    }
    public function setNode($zN)
    {
        $this->rawNode = $zN;
    }
    public function encryptReferences($z3)
    {
        $e8 = $this->rawNode;
        $Mg = $this->type;
        foreach ($this->references as $yt => $zo) {
            $this->encdoc = $zo["\x65\156\143\x6e\157\x64\x65"];
            $this->rawNode = $zo["\x6e\x6f\x64\145"];
            $this->type = $zo["\x74\x79\160\x65"];
            try {
                $Yi = $this->encryptNode($z3);
                $this->references[$yt]["\x65\156\143\x6e\157\144\145"] = $Yi;
            } catch (Exception $IR) {
                $this->rawNode = $e8;
                $this->type = $Mg;
                throw $IR;
            }
            fb:
        }
        VN:
        $this->rawNode = $e8;
        $this->type = $Mg;
    }
    public function encryptNode($z3, $LC = true)
    {
        $or = '';
        if (!empty($this->rawNode)) {
            goto m2;
        }
        throw new Exception("\x4e\157\144\x65\40\x74\157\x20\145\x6e\143\162\171\160\164\40\x68\141\163\x20\156\157\164\x20\142\x65\x65\156\40\163\145\x74");
        m2:
        if ($z3 instanceof XMLSecurityKey) {
            goto ub;
        }
        throw new Exception("\x49\x6e\x76\x61\x6c\151\144\x20\113\x65\x79");
        ub:
        $st = $this->rawNode->ownerDocument;
        $rt = new DOMXPath($this->encdoc);
        $QO = $rt->query("\57\170\x65\x6e\x63\72\x45\156\143\162\x79\x70\x74\145\144\104\141\164\x61\x2f\x78\x65\156\x63\x3a\x43\151\x70\150\145\x72\104\141\164\141\x2f\170\145\156\143\72\x43\x69\160\x68\145\162\x56\x61\154\x75\145");
        $JK = $QO->item(0);
        if (!($JK == null)) {
            goto CM;
        }
        throw new Exception("\105\162\162\157\162\40\x6c\157\143\141\x74\x69\x6e\147\x20\103\x69\160\x68\x65\162\126\141\x6c\165\x65\40\145\x6c\x65\x6d\145\x6e\x74\x20\x77\151\x74\150\151\156\40\x74\x65\x6d\160\x6c\141\164\x65");
        CM:
        switch ($this->type) {
            case self::Element:
                $or = $st->saveXML($this->rawNode);
                $this->encdoc->documentElement->setAttribute("\124\171\x70\145", self::Element);
                goto wX;
            case self::Content:
                $h2 = $this->rawNode->childNodes;
                foreach ($h2 as $Xk) {
                    $or .= $st->saveXML($Xk);
                    Sr:
                }
                kv:
                $this->encdoc->documentElement->setAttribute("\124\x79\160\145", self::Content);
                goto wX;
            default:
                throw new Exception("\x54\171\x70\145\40\x69\163\x20\143\x75\162\162\x65\156\x74\x6c\171\x20\x6e\157\164\40\163\x75\x70\160\157\162\x74\x65\144");
        }
        xj:
        wX:
        $WR = $this->encdoc->documentElement->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\156\143\72\105\156\143\x72\171\160\x74\151\157\x6e\x4d\145\x74\x68\157\x64"));
        $WR->setAttribute("\x41\154\147\x6f\x72\151\164\x68\x6d", $z3->getAlgorithm());
        $JK->parentNode->parentNode->insertBefore($WR, $JK->parentNode->parentNode->firstChild);
        $Lo = base64_encode($z3->encryptData($or));
        $VP = $this->encdoc->createTextNode($Lo);
        $JK->appendChild($VP);
        if ($LC) {
            goto oo;
        }
        return $this->encdoc->documentElement;
        goto sm;
        oo:
        switch ($this->type) {
            case self::Element:
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto JR;
                }
                return $this->encdoc;
                JR:
                $Gq = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                $this->rawNode->parentNode->replaceChild($Gq, $this->rawNode);
                return $Gq;
            case self::Content:
                $Gq = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                Lx:
                if (!$this->rawNode->firstChild) {
                    goto t9;
                }
                $this->rawNode->removeChild($this->rawNode->firstChild);
                goto Lx;
                t9:
                $this->rawNode->appendChild($Gq);
                return $Gq;
        }
        Xr:
        Kf:
        sm:
    }
    public function encryptKey($cQ, $mg, $C1 = true)
    {
        if (!(!$cQ instanceof XMLSecurityKey || !$mg instanceof XMLSecurityKey)) {
            goto OH;
        }
        throw new Exception("\111\156\x76\141\154\151\x64\x20\113\x65\171");
        OH:
        $hj = base64_encode($cQ->encryptData($mg->key));
        $OC = $this->encdoc->documentElement;
        $nw = $this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\x6e\143\x3a\105\156\x63\x72\171\x70\x74\x65\144\x4b\145\171");
        if ($C1) {
            goto Ju;
        }
        $this->encKey = $nw;
        goto h3;
        Ju:
        $He = $OC->insertBefore($this->encdoc->createElementNS("\150\164\x74\x70\x3a\57\x2f\167\x77\167\x2e\167\63\56\157\x72\147\57\62\60\60\x30\x2f\60\71\57\170\x6d\x6c\144\x73\151\x67\x23", "\x64\163\151\x67\72\x4b\x65\171\x49\x6e\x66\157"), $OC->firstChild);
        $He->appendChild($nw);
        h3:
        $WR = $nw->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\145\156\x63\x3a\x45\156\143\x72\171\x70\164\x69\157\x6e\115\145\x74\150\157\144"));
        $WR->setAttribute("\101\154\147\x6f\x72\x69\164\x68\x6d", $cQ->getAlgorithm());
        if (empty($cQ->name)) {
            goto A1;
        }
        $He = $nw->appendChild($this->encdoc->createElementNS("\x68\x74\x74\160\72\57\x2f\167\167\x77\56\x77\63\x2e\x6f\162\x67\x2f\62\x30\x30\x30\x2f\60\x39\x2f\170\155\x6c\144\x73\151\147\43", "\x64\163\151\147\x3a\113\x65\171\x49\156\146\157"));
        $He->appendChild($this->encdoc->createElementNS("\x68\x74\x74\160\x3a\57\57\167\167\167\x2e\x77\x33\56\x6f\x72\x67\57\62\x30\60\60\x2f\60\71\57\170\155\x6c\x64\x73\151\147\x23", "\x64\x73\151\x67\72\113\145\x79\116\141\x6d\145", $cQ->name));
        A1:
        $VL = $nw->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\x6e\143\72\x43\151\160\x68\x65\162\x44\141\x74\x61"));
        $VL->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\156\143\x3a\103\x69\x70\150\145\162\126\141\154\x75\x65", $hj));
        if (!(is_array($this->references) && count($this->references) > 0)) {
            goto lW;
        }
        $Bk = $nw->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\x6e\143\x3a\122\145\146\145\162\x65\156\143\x65\x4c\x69\163\x74"));
        foreach ($this->references as $yt => $zo) {
            $SF = $zo["\162\145\146\x75\162\x69"];
            $iu = $Bk->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\156\x63\72\104\141\164\x61\x52\x65\146\145\162\145\156\x63\145"));
            $iu->setAttribute("\x55\122\111", "\x23" . $SF);
            Qe:
        }
        Hp:
        lW:
        return;
    }
    public function decryptKey($nw)
    {
        if ($nw->isEncrypted) {
            goto jl;
        }
        throw new Exception("\x4b\145\171\40\151\x73\40\x6e\157\164\40\105\x6e\143\162\171\x70\x74\x65\x64");
        jl:
        if (!empty($nw->key)) {
            goto g1;
        }
        throw new Exception("\113\x65\171\x20\x69\x73\40\x6d\151\x73\163\151\156\147\40\x64\x61\x74\x61\x20\164\157\x20\x70\x65\x72\146\157\x72\x6d\40\x74\x68\145\x20\x64\145\x63\162\171\160\164\151\157\x6e");
        g1:
        return $this->decryptNode($nw, false);
    }
    public function decryptNode($z3, $LC = true)
    {
        if ($z3 instanceof XMLSecurityKey) {
            goto ng;
        }
        throw new Exception("\x49\x6e\166\141\154\151\x64\x20\113\x65\171");
        ng:
        $aa = $this->getCipherValue();
        if ($aa) {
            goto F5;
        }
        throw new Exception("\x43\x61\x6e\x6e\x6f\x74\40\x6c\x6f\x63\141\x74\145\x20\x65\x6e\143\162\171\x70\x74\145\x64\40\144\141\x74\141");
        goto k0;
        F5:
        $jn = $z3->decryptData($aa);
        if ($LC) {
            goto Cm;
        }
        return $jn;
        goto mO;
        Cm:
        switch ($this->type) {
            case self::Element:
                $gK = new DOMDocument();
                $gK->loadXML($jn);
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto TQ;
                }
                return $gK;
                TQ:
                $Gq = $this->rawNode->ownerDocument->importNode($gK->documentElement, true);
                $this->rawNode->parentNode->replaceChild($Gq, $this->rawNode);
                return $Gq;
            case self::Content:
                if ($this->rawNode->nodeType == XML_DOCUMENT_NODE) {
                    goto H1;
                }
                $st = $this->rawNode->ownerDocument;
                goto Yc;
                H1:
                $st = $this->rawNode;
                Yc:
                $a_ = $st->createDocumentFragment();
                $a_->appendXML($jn);
                $iJ = $this->rawNode->parentNode;
                $iJ->replaceChild($a_, $this->rawNode);
                return $iJ;
            default:
                return $jn;
        }
        fZ:
        KA:
        mO:
        k0:
    }
    public function getCipherValue()
    {
        if (!empty($this->rawNode)) {
            goto SI;
        }
        throw new Exception("\x4e\x6f\144\x65\40\164\157\x20\x64\145\x63\162\x79\160\x74\40\150\141\x73\x20\x6e\157\x74\x20\x62\145\145\156\x20\163\145\x74");
        SI:
        $st = $this->rawNode->ownerDocument;
        $rt = new DOMXPath($st);
        $rt->registerNamespace("\x78\x6d\154\145\x6e\x63\x72", self::XMLENCNS);
        $NB = "\x2e\57\x78\x6d\x6c\145\156\143\x72\72\103\x69\160\150\x65\x72\x44\x61\x74\141\x2f\170\x6d\x6c\145\156\143\x72\x3a\x43\151\160\x68\145\x72\126\x61\x6c\165\x65";
        $Wy = $rt->query($NB, $this->rawNode);
        $zN = $Wy->item(0);
        if ($zN) {
            goto VH;
        }
        return null;
        VH:
        return base64_decode($zN->nodeValue);
    }
    public function locateEncryptedData($hL)
    {
        if ($hL instanceof DOMDocument) {
            goto xr;
        }
        $st = $hL->ownerDocument;
        goto Mg;
        xr:
        $st = $hL;
        Mg:
        if (!$st) {
            goto bl;
        }
        $vD = new DOMXPath($st);
        $NB = "\57\57\x2a\133\154\x6f\x63\x61\154\55\x6e\x61\155\145\50\51\75\47\105\x6e\x63\162\x79\x70\164\x65\x64\104\141\164\x61\47\40\141\x6e\144\x20\156\x61\155\x65\x73\160\141\143\145\55\165\x72\151\50\51\x3d\x27" . self::XMLENCNS . "\47\x5d";
        $Wy = $vD->query($NB);
        return $Wy->item(0);
        bl:
        return null;
    }
    public function locateKey($zN = null)
    {
        if (!empty($zN)) {
            goto ZX;
        }
        $zN = $this->rawNode;
        ZX:
        if ($zN instanceof DOMElement) {
            goto vw;
        }
        return null;
        vw:
        if (!($st = $zN->ownerDocument)) {
            goto bu;
        }
        $vD = new DOMXPath($st);
        $vD->registerNamespace("\x78\x6d\x6c\163\145\143\x65\156\143", self::XMLENCNS);
        $NB = "\56\x2f\57\x78\155\x6c\x73\x65\x63\x65\x6e\143\72\105\x6e\143\x72\x79\x70\x74\151\157\x6e\115\145\x74\150\x6f\144";
        $Wy = $vD->query($NB, $zN);
        if (!($yB = $Wy->item(0))) {
            goto kI;
        }
        $vQ = $yB->getAttribute("\101\x6c\147\157\162\151\x74\150\155");
        try {
            $z3 = new XMLSecurityKey($vQ, array("\x74\x79\160\x65" => "\160\x72\x69\x76\x61\x74\x65"));
        } catch (Exception $IR) {
            return null;
        }
        return $z3;
        kI:
        bu:
        return null;
    }
    public function locateKeyInfo($vs = null, $zN = null)
    {
        if (!empty($zN)) {
            goto ck;
        }
        $zN = $this->rawNode;
        ck:
        return self::staticLocateKeyInfo($vs, $zN);
    }
    public static function staticLocateKeyInfo($vs = null, $zN = null)
    {
        if (!(empty($zN) || !$zN instanceof DOMElement)) {
            goto Vh;
        }
        return null;
        Vh:
        $st = $zN->ownerDocument;
        if ($st) {
            goto JB;
        }
        return null;
        JB:
        $vD = new DOMXPath($st);
        $vD->registerNamespace("\170\x6d\154\x73\145\x63\x65\156\x63", self::XMLENCNS);
        $vD->registerNamespace("\x78\x6d\154\x73\x65\143\x64\163\151\x67", XMLSecurityDSig::XMLDSIGNS);
        $NB = "\x2e\57\170\155\154\x73\x65\x63\144\163\x69\147\x3a\x4b\145\x79\x49\156\x66\x6f";
        $Wy = $vD->query($NB, $zN);
        $yB = $Wy->item(0);
        if ($yB) {
            goto fg;
        }
        return $vs;
        fg:
        foreach ($yB->childNodes as $Xk) {
            switch ($Xk->localName) {
                case "\x4b\x65\x79\116\141\x6d\x65":
                    if (empty($vs)) {
                        goto up;
                    }
                    $vs->name = $Xk->nodeValue;
                    up:
                    goto DB;
                case "\113\145\x79\126\x61\x6c\x75\145":
                    foreach ($Xk->childNodes as $Wm) {
                        switch ($Wm->localName) {
                            case "\x44\x53\101\113\x65\171\126\x61\154\x75\x65":
                                throw new Exception("\x44\x53\101\x4b\x65\171\126\x61\154\x75\145\x20\143\x75\162\162\145\156\x74\154\171\x20\156\x6f\164\x20\x73\165\x70\x70\157\162\164\x65\144");
                            case "\122\x53\101\113\x65\x79\x56\141\154\x75\145":
                                $Of = null;
                                $FP = null;
                                if (!($W8 = $Wm->getElementsByTagName("\115\x6f\144\x75\154\x75\163")->item(0))) {
                                    goto X9;
                                }
                                $Of = base64_decode($W8->nodeValue);
                                X9:
                                if (!($Z7 = $Wm->getElementsByTagName("\105\x78\160\157\156\145\156\164")->item(0))) {
                                    goto fp;
                                }
                                $FP = base64_decode($Z7->nodeValue);
                                fp:
                                if (!(empty($Of) || empty($FP))) {
                                    goto EN;
                                }
                                throw new Exception("\x4d\151\163\x73\151\156\x67\40\x4d\x6f\144\x75\154\x75\x73\x20\x6f\x72\40\105\x78\x70\x6f\x6e\x65\156\x74");
                                EN:
                                $uQ = XMLSecurityKey::convertRSA($Of, $FP);
                                $vs->loadKey($uQ);
                                goto p5;
                        }
                        Ka:
                        p5:
                        Pg:
                    }
                    Sq:
                    goto DB;
                case "\122\145\x74\x72\x69\145\166\141\x6c\115\x65\164\x68\x6f\x64":
                    $qI = $Xk->getAttribute("\x54\x79\x70\145");
                    if (!($qI !== "\x68\164\x74\160\x3a\57\57\167\x77\167\56\167\63\56\157\162\x67\57\x32\x30\x30\x31\x2f\x30\64\57\170\155\x6c\x65\156\143\43\105\156\x63\x72\171\x70\164\145\x64\x4b\145\171")) {
                        goto Bc;
                    }
                    goto DB;
                    Bc:
                    $cN = $Xk->getAttribute("\x55\x52\111");
                    if (!($cN[0] !== "\43")) {
                        goto gU;
                    }
                    goto DB;
                    gU:
                    $Gh = substr($cN, 1);
                    $NB = "\57\x2f\170\x6d\x6c\x73\145\143\145\x6e\143\x3a\105\156\143\162\x79\x70\164\x65\x64\113\x65\171\x5b\100\x49\x64\x3d\47{$Gh}\47\x5d";
                    $eH = $vD->query($NB)->item(0);
                    if ($eH) {
                        goto OD;
                    }
                    throw new Exception("\x55\156\141\142\x6c\x65\x20\x74\157\x20\154\x6f\x63\x61\x74\145\x20\105\x6e\143\x72\x79\160\x74\145\144\x4b\x65\171\x20\x77\x69\164\x68\x20\x40\111\144\75\x27{$Gh}\x27\56");
                    OD:
                    return XMLSecurityKey::fromEncryptedKeyElement($eH);
                case "\105\x6e\x63\x72\x79\x70\x74\x65\x64\x4b\145\x79":
                    return XMLSecurityKey::fromEncryptedKeyElement($Xk);
                case "\x58\65\60\71\104\141\164\141":
                    if (!($rS = $Xk->getElementsByTagName("\130\65\x30\x39\x43\x65\x72\164\x69\x66\151\x63\x61\x74\145"))) {
                        goto Ld;
                    }
                    if (!($rS->length > 0)) {
                        goto qy;
                    }
                    $n5 = $rS->item(0)->textContent;
                    $n5 = str_replace(array("\xd", "\12", "\40"), '', $n5);
                    $n5 = "\x2d\55\55\55\x2d\x42\105\107\x49\x4e\40\x43\105\122\124\x49\106\x49\x43\x41\124\x45\x2d\55\55\x2d\x2d\xa" . chunk_split($n5, 64, "\12") . "\55\x2d\x2d\x2d\55\x45\116\104\40\103\x45\122\x54\111\x46\x49\103\x41\x54\x45\x2d\55\55\x2d\55\xa";
                    $vs->loadKey($n5, false, true);
                    qy:
                    Ld:
                    goto DB;
            }
            iJ:
            DB:
            Fs:
        }
        IG:
        return $vs;
    }
}
