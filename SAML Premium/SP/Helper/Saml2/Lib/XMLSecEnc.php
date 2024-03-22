<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use DOMElement;
class XMLSecEnc
{
    const template = "\74\170\x65\x6e\x63\72\x45\156\143\x72\x79\160\x74\x65\144\x44\x61\x74\x61\40\170\155\x6c\x6e\x73\x3a\170\145\x6e\143\75\47\x68\164\x74\x70\72\57\x2f\167\167\x77\x2e\167\63\56\x6f\x72\x67\x2f\62\x30\60\61\x2f\x30\x34\x2f\170\155\x6c\x65\x6e\x63\x23\47\x3e\15\12\x20\40\40\74\170\x65\x6e\x63\72\x43\x69\160\x68\145\162\104\141\164\x61\x3e\xd\xa\x20\40\40\40\40\x20\74\x78\145\x6e\143\72\x43\151\160\x68\x65\162\126\141\x6c\165\145\x3e\74\57\170\145\x6e\x63\x3a\103\x69\x70\x68\145\162\x56\141\154\x75\x65\x3e\15\12\x20\40\40\x3c\57\170\x65\156\143\x3a\x43\151\x70\x68\145\162\104\141\x74\x61\76\xd\xa\74\x2f\170\145\x6e\143\72\x45\x6e\x63\162\171\x70\x74\x65\x64\x44\x61\164\x61\76";
    const Element = "\x68\164\164\160\72\57\57\167\167\x77\56\x77\63\56\157\x72\x67\x2f\62\x30\60\61\x2f\60\64\x2f\170\155\x6c\x65\156\143\x23\105\154\145\x6d\145\x6e\164";
    const Content = "\150\x74\x74\x70\72\57\57\167\x77\x77\x2e\x77\63\x2e\x6f\x72\x67\57\x32\x30\x30\x31\x2f\60\64\x2f\x78\155\154\x65\x6e\x63\x23\x43\157\x6e\164\x65\x6e\x74";
    const URI = 3;
    const XMLENCNS = "\150\x74\164\160\x3a\57\57\x77\167\x77\x2e\x77\x33\56\x6f\162\x67\57\x32\60\60\61\57\60\x34\57\170\x6d\154\x65\156\143\43";
    private $encdoc = null;
    private $rawNode = null;
    public $type = null;
    public $encKey = null;
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
    public function addReference($rb, $w5, $Nv)
    {
        if ($w5 instanceof DOMNode) {
            goto g4;
        }
        throw new Exception("\x24\156\x6f\x64\145\40\x69\x73\40\x6e\157\164\x20\x6f\x66\40\164\171\x70\x65\40\104\117\115\116\x6f\x64\x65");
        g4:
        $TS = $this->encdoc;
        $this->_resetTemplate();
        $bq = $this->encdoc;
        $this->encdoc = $TS;
        $bG = XMLSecurityDSig::generateGUID();
        $rM = $bq->documentElement;
        $rM->setAttribute("\x49\144", $bG);
        $this->references[$rb] = array("\x6e\x6f\x64\x65" => $w5, "\164\171\x70\145" => $Nv, "\x65\x6e\143\156\157\x64\145" => $bq, "\x72\x65\x66\x75\x72\151" => $bG);
    }
    public function setNode($w5)
    {
        $this->rawNode = $w5;
    }
    public function encryptNode($Tb, $PE = true)
    {
        $F2 = '';
        if (!empty($this->rawNode)) {
            goto jf;
        }
        throw new Exception("\x4e\x6f\144\145\40\x74\157\40\145\x6e\143\162\171\160\x74\40\150\x61\163\x20\x6e\157\164\x20\142\145\x65\x6e\40\x73\x65\164");
        jf:
        if ($Tb instanceof XMLSecurityKey) {
            goto HX;
        }
        throw new Exception("\x49\156\166\x61\x6c\151\x64\40\113\145\x79");
        HX:
        $Dy = $this->rawNode->ownerDocument;
        $vc = new DOMXPath($this->encdoc);
        $Jt = $vc->query("\x2f\170\145\156\x63\72\x45\156\x63\162\171\160\164\145\144\x44\x61\164\x61\57\x78\x65\156\143\x3a\x43\151\x70\x68\x65\x72\x44\x61\164\x61\x2f\170\x65\156\x63\72\103\x69\x70\150\x65\x72\126\x61\154\x75\x65");
        $r0 = $Jt->item(0);
        if (!($r0 == null)) {
            goto hS;
        }
        throw new Exception("\x45\162\x72\x6f\162\x20\154\x6f\143\141\x74\x69\156\x67\x20\x43\151\x70\x68\145\162\126\x61\154\165\x65\40\145\154\x65\x6d\145\156\164\x20\167\x69\x74\x68\151\x6e\x20\x74\145\x6d\x70\154\x61\x74\145");
        hS:
        switch ($this->type) {
            case self::Element:
                $F2 = $Dy->saveXML($this->rawNode);
                $this->encdoc->documentElement->setAttribute("\x54\x79\x70\x65", self::Element);
                goto pz;
            case self::Content:
                $Xy = $this->rawNode->childNodes;
                foreach ($Xy as $rD) {
                    $F2 .= $Dy->saveXML($rD);
                    e0:
                }
                eq:
                $this->encdoc->documentElement->setAttribute("\x54\171\160\x65", self::Content);
                goto pz;
            default:
                throw new Exception("\x54\171\160\x65\40\x69\163\x20\x63\165\162\162\145\x6e\x74\154\x79\40\156\157\164\40\x73\x75\160\160\157\162\x74\x65\144");
        }
        pB:
        pz:
        $cw = $this->encdoc->documentElement->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\156\x63\72\105\156\x63\x72\171\160\x74\x69\157\156\x4d\145\164\150\157\144"));
        $cw->setAttribute("\x41\154\147\x6f\162\x69\164\150\x6d", $Tb->getAlgorithm());
        $r0->parentNode->parentNode->insertBefore($cw, $r0->parentNode->parentNode->firstChild);
        $DZ = base64_encode($Tb->encryptData($F2));
        $Yk = $this->encdoc->createTextNode($DZ);
        $r0->appendChild($Yk);
        if ($PE) {
            goto ml;
        }
        return $this->encdoc->documentElement;
        goto hh;
        ml:
        switch ($this->type) {
            case self::Element:
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto Od;
                }
                return $this->encdoc;
                Od:
                $tp = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                $this->rawNode->parentNode->replaceChild($tp, $this->rawNode);
                return $tp;
            case self::Content:
                $tp = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                Yw:
                if (!$this->rawNode->firstChild) {
                    goto OT;
                }
                $this->rawNode->removeChild($this->rawNode->firstChild);
                goto Yw;
                OT:
                $this->rawNode->appendChild($tp);
                return $tp;
        }
        Zj:
        UN:
        hh:
    }
    public function encryptReferences($Tb)
    {
        $Th = $this->rawNode;
        $Tw = $this->type;
        foreach ($this->references as $rb => $mj) {
            $this->encdoc = $mj["\145\x6e\143\156\157\144\x65"];
            $this->rawNode = $mj["\x6e\x6f\x64\145"];
            $this->type = $mj["\164\x79\x70\145"];
            try {
                $mo = $this->encryptNode($Tb);
                $this->references[$rb]["\145\x6e\143\156\x6f\x64\145"] = $mo;
            } catch (Exception $sS) {
                $this->rawNode = $Th;
                $this->type = $Tw;
                throw $sS;
            }
            k0:
        }
        CR:
        $this->rawNode = $Th;
        $this->type = $Tw;
    }
    public function getCipherValue()
    {
        if (!empty($this->rawNode)) {
            goto wF;
        }
        throw new Exception("\116\157\144\145\x20\x74\157\40\144\145\143\x72\x79\x70\x74\40\x68\x61\x73\x20\x6e\157\x74\x20\x62\145\x65\156\x20\x73\145\x74");
        wF:
        $Dy = $this->rawNode->ownerDocument;
        $vc = new DOMXPath($Dy);
        $vc->registerNamespace("\170\x6d\154\x65\156\x63\x72", self::XMLENCNS);
        $dU = "\56\x2f\170\x6d\x6c\x65\x6e\x63\162\72\x43\151\x70\x68\x65\x72\104\x61\x74\141\57\170\x6d\x6c\145\x6e\143\162\72\x43\151\x70\150\145\162\126\141\x6c\165\145";
        $o7 = $vc->query($dU, $this->rawNode);
        $w5 = $o7->item(0);
        if ($w5) {
            goto aR;
        }
        return null;
        aR:
        return base64_decode($w5->nodeValue);
    }
    public function decryptNode($Tb, $PE = true)
    {
        if ($Tb instanceof XMLSecurityKey) {
            goto xH;
        }
        throw new Exception("\x49\156\166\141\154\x69\x64\40\x4b\145\171");
        xH:
        $gU = $this->getCipherValue();
        if ($gU) {
            goto en;
        }
        throw new Exception("\103\x61\156\x6e\157\164\40\154\157\143\x61\164\145\40\145\x6e\143\x72\171\x70\x74\145\144\x20\144\x61\164\141");
        goto bl;
        en:
        $G8 = $Tb->decryptData($gU);
        if ($PE) {
            goto wZ;
        }
        return $G8;
        goto Ls;
        wZ:
        switch ($this->type) {
            case self::Element:
                $RF = new DOMDocument();
                $RF->loadXML($G8);
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto rp;
                }
                return $RF;
                rp:
                $tp = $this->rawNode->ownerDocument->importNode($RF->documentElement, true);
                $this->rawNode->parentNode->replaceChild($tp, $this->rawNode);
                return $tp;
            case self::Content:
                if ($this->rawNode->nodeType == XML_DOCUMENT_NODE) {
                    goto AW;
                }
                $Dy = $this->rawNode->ownerDocument;
                goto Fs;
                AW:
                $Dy = $this->rawNode;
                Fs:
                $ec = $Dy->createDocumentFragment();
                $ec->appendXML($G8);
                $US = $this->rawNode->parentNode;
                $US->replaceChild($ec, $this->rawNode);
                return $US;
            default:
                return $G8;
        }
        mc:
        Qh:
        Ls:
        bl:
    }
    public function encryptKey($nK, $Q4, $Hx = true)
    {
        if (!(!$nK instanceof XMLSecurityKey || !$Q4 instanceof XMLSecurityKey)) {
            goto eN;
        }
        throw new Exception("\x49\156\166\x61\154\151\x64\40\x4b\x65\x79");
        eN:
        $X8 = base64_encode($nK->encryptData($Q4->key));
        $N7 = $this->encdoc->documentElement;
        $d2 = $this->encdoc->createElementNS(self::XMLENCNS, "\170\145\x6e\143\x3a\x45\x6e\143\x72\171\160\x74\145\x64\x4b\145\171");
        if ($Hx) {
            goto Ef;
        }
        $this->encKey = $d2;
        goto RA;
        Ef:
        $Fp = $N7->insertBefore($this->encdoc->createElementNS("\150\x74\x74\x70\72\x2f\x2f\167\x77\167\56\x77\63\x2e\x6f\x72\x67\x2f\62\60\x30\60\57\x30\x39\57\170\x6d\x6c\144\x73\x69\147\43", "\x64\163\x69\x67\x3a\x4b\x65\x79\x49\156\146\x6f"), $N7->firstChild);
        $Fp->appendChild($d2);
        RA:
        $cw = $d2->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\145\156\143\72\x45\x6e\143\162\171\160\x74\x69\157\x6e\115\x65\164\x68\x6f\144"));
        $cw->setAttribute("\x41\x6c\x67\x6f\x72\151\164\150\155", $nK->getAlgorithm());
        if (empty($nK->name)) {
            goto rf;
        }
        $Fp = $d2->appendChild($this->encdoc->createElementNS("\x68\x74\x74\160\x3a\57\57\x77\x77\167\x2e\x77\63\x2e\157\x72\x67\57\x32\x30\60\60\57\60\71\x2f\x78\x6d\154\144\163\x69\x67\x23", "\x64\x73\x69\147\x3a\x4b\145\171\x49\156\146\x6f"));
        $Fp->appendChild($this->encdoc->createElementNS("\x68\164\x74\x70\72\57\x2f\167\167\167\x2e\167\x33\x2e\x6f\162\147\x2f\62\60\x30\60\x2f\60\x39\x2f\170\x6d\154\144\x73\x69\x67\x23", "\144\163\151\147\72\x4b\145\x79\116\x61\155\145", $nK->name));
        rf:
        $GR = $d2->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\145\x6e\x63\x3a\103\151\x70\x68\145\162\x44\x61\164\141"));
        $GR->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\145\x6e\143\x3a\x43\151\160\x68\x65\162\x56\x61\154\x75\145", $X8));
        if (!(is_array($this->references) && count($this->references) > 0)) {
            goto T5;
        }
        $Nc = $d2->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\156\143\72\122\x65\x66\x65\162\x65\x6e\143\145\114\151\x73\x74"));
        foreach ($this->references as $rb => $mj) {
            $bG = $mj["\162\145\146\165\x72\x69"];
            $tD = $Nc->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\156\x63\x3a\x44\141\x74\x61\x52\x65\146\x65\162\x65\x6e\143\x65"));
            $tD->setAttribute("\125\x52\111", "\43" . $bG);
            X3:
        }
        jI:
        T5:
        return;
    }
    public function decryptKey($d2)
    {
        if ($d2->isEncrypted) {
            goto qm;
        }
        throw new Exception("\x4b\x65\171\40\151\163\40\x6e\157\164\x20\105\x6e\x63\162\171\x70\164\x65\x64");
        qm:
        if (!empty($d2->key)) {
            goto cd;
        }
        throw new Exception("\x4b\x65\x79\40\151\x73\40\x6d\151\x73\163\151\156\x67\40\x64\141\164\x61\x20\x74\157\40\160\145\x72\146\x6f\x72\155\40\x74\150\145\40\144\145\x63\x72\171\160\164\151\157\156");
        cd:
        return $this->decryptNode($d2, false);
    }
    public function locateEncryptedData($rM)
    {
        if ($rM instanceof DOMDocument) {
            goto v8;
        }
        $Dy = $rM->ownerDocument;
        goto PQ;
        v8:
        $Dy = $rM;
        PQ:
        if (!$Dy) {
            goto zv;
        }
        $gR = new DOMXPath($Dy);
        $dU = "\57\x2f\x2a\133\x6c\157\143\x61\154\55\156\x61\x6d\145\x28\51\75\x27\x45\x6e\143\162\171\160\164\145\144\104\141\x74\x61\x27\x20\141\x6e\144\x20\x6e\141\x6d\x65\x73\160\141\x63\145\55\165\162\151\50\51\75\47" . self::XMLENCNS . "\x27\135";
        $o7 = $gR->query($dU);
        return $o7->item(0);
        zv:
        return null;
    }
    public function locateKey($w5 = null)
    {
        if (!empty($w5)) {
            goto mt;
        }
        $w5 = $this->rawNode;
        mt:
        if ($w5 instanceof DOMElement) {
            goto GJ;
        }
        return null;
        GJ:
        if (!($Dy = $w5->ownerDocument)) {
            goto Ra;
        }
        $gR = new DOMXPath($Dy);
        $gR->registerNamespace("\x78\155\154\x73\145\x63\x65\156\143", self::XMLENCNS);
        $dU = "\56\57\x2f\x78\x6d\154\163\x65\x63\x65\156\143\72\105\156\x63\x72\x79\x70\164\x69\157\156\115\x65\x74\150\x6f\x64";
        $o7 = $gR->query($dU, $w5);
        if (!($KW = $o7->item(0))) {
            goto OH;
        }
        $X2 = $KW->getAttribute("\101\x6c\x67\x6f\x72\151\164\150\155");
        try {
            $Tb = new XMLSecurityKey($X2, array("\164\x79\x70\145" => "\x70\162\151\x76\141\164\145"));
        } catch (Exception $sS) {
            return null;
        }
        return $Tb;
        OH:
        Ra:
        return null;
    }
    public static function staticLocateKeyInfo($aP = null, $w5 = null)
    {
        if (!(empty($w5) || !$w5 instanceof DOMElement)) {
            goto CG;
        }
        return null;
        CG:
        $Dy = $w5->ownerDocument;
        if ($Dy) {
            goto Wt;
        }
        return null;
        Wt:
        $gR = new DOMXPath($Dy);
        $gR->registerNamespace("\x78\155\x6c\163\x65\x63\x65\156\x63", self::XMLENCNS);
        $gR->registerNamespace("\x78\155\x6c\x73\145\143\144\x73\x69\x67", XMLSecurityDSig::XMLDSIGNS);
        $dU = "\56\57\x78\x6d\154\x73\145\143\x64\x73\151\147\x3a\x4b\x65\x79\x49\156\x66\x6f";
        $o7 = $gR->query($dU, $w5);
        $KW = $o7->item(0);
        if ($KW) {
            goto sB;
        }
        return $aP;
        sB:
        foreach ($KW->childNodes as $rD) {
            switch ($rD->localName) {
                case "\x4b\145\171\x4e\141\155\x65":
                    if (empty($aP)) {
                        goto rq;
                    }
                    $aP->name = $rD->nodeValue;
                    rq:
                    goto yY;
                case "\x4b\145\x79\126\141\154\165\x65":
                    foreach ($rD->childNodes as $pn) {
                        switch ($pn->localName) {
                            case "\x44\123\x41\113\x65\171\x56\141\x6c\165\x65":
                                throw new Exception("\x44\x53\101\x4b\x65\171\x56\x61\x6c\165\145\40\143\x75\x72\162\145\156\x74\x6c\171\40\156\x6f\164\40\163\x75\160\160\157\162\164\x65\144");
                            case "\x52\123\x41\x4b\145\171\x56\x61\154\x75\145":
                                $p4 = null;
                                $Gk = null;
                                if (!($T_ = $pn->getElementsByTagName("\115\x6f\144\x75\x6c\x75\163")->item(0))) {
                                    goto aF;
                                }
                                $p4 = base64_decode($T_->nodeValue);
                                aF:
                                if (!($gM = $pn->getElementsByTagName("\105\170\x70\x6f\156\145\156\x74")->item(0))) {
                                    goto t7;
                                }
                                $Gk = base64_decode($gM->nodeValue);
                                t7:
                                if (!(empty($p4) || empty($Gk))) {
                                    goto df;
                                }
                                throw new Exception("\x4d\151\x73\163\151\156\147\40\x4d\x6f\x64\165\x6c\x75\x73\40\x6f\162\x20\105\170\x70\x6f\156\x65\x6e\164");
                                df:
                                $Ud = XMLSecurityKey::convertRSA($p4, $Gk);
                                $aP->loadKey($Ud);
                                goto Rb;
                        }
                        mB:
                        Rb:
                        Pc:
                    }
                    vf:
                    goto yY;
                case "\x52\x65\164\162\x69\x65\166\x61\154\x4d\145\164\x68\x6f\x64":
                    $Nv = $rD->getAttribute("\x54\171\x70\x65");
                    if (!($Nv !== "\150\164\x74\x70\72\57\x2f\x77\167\167\x2e\x77\x33\56\157\162\147\57\x32\60\60\x31\x2f\x30\64\57\x78\155\154\x65\156\143\x23\x45\156\143\x72\x79\x70\164\145\144\113\x65\x79")) {
                        goto Xg;
                    }
                    goto yY;
                    Xg:
                    $Rq = $rD->getAttribute("\125\x52\111");
                    if (!($Rq[0] !== "\x23")) {
                        goto wP;
                    }
                    goto yY;
                    wP:
                    $lA = substr($Rq, 1);
                    $dU = "\57\57\x78\155\x6c\163\145\x63\145\156\x63\x3a\x45\x6e\143\x72\171\x70\164\145\144\x4b\145\x79\x5b\x40\x49\144\75\47{$lA}\47\135";
                    $qZ = $gR->query($dU)->item(0);
                    if ($qZ) {
                        goto Nr;
                    }
                    throw new Exception("\x55\156\141\142\154\145\40\164\157\x20\154\157\x63\x61\164\x65\40\x45\156\143\162\x79\x70\x74\145\144\113\x65\171\40\167\151\x74\x68\40\x40\x49\x64\75\x27{$lA}\47\x2e");
                    Nr:
                    return XMLSecurityKey::fromEncryptedKeyElement($qZ);
                case "\x45\156\x63\162\171\160\164\145\x64\x4b\145\x79":
                    return XMLSecurityKey::fromEncryptedKeyElement($rD);
                case "\x58\x35\x30\71\104\x61\164\x61":
                    if (!($Oy = $rD->getElementsByTagName("\130\65\60\x39\103\x65\x72\164\151\146\151\x63\141\x74\145"))) {
                        goto r_;
                    }
                    if (!($Oy->length > 0)) {
                        goto e9;
                    }
                    $TJ = $Oy->item(0)->textContent;
                    $TJ = str_replace(array("\15", "\12", "\x20"), '', $TJ);
                    $TJ = "\x2d\55\x2d\x2d\55\x42\x45\107\111\116\x20\103\x45\122\124\x49\x46\111\x43\101\x54\105\55\x2d\x2d\x2d\55\12" . chunk_split($TJ, 64, "\12") . "\x2d\x2d\x2d\x2d\55\x45\116\104\40\x43\x45\x52\124\111\x46\x49\103\x41\x54\x45\x2d\x2d\x2d\55\x2d\xa";
                    $aP->loadKey($TJ, false, true);
                    e9:
                    r_:
                    goto yY;
            }
            hM:
            yY:
            eS:
        }
        X_:
        return $aP;
    }
    public function locateKeyInfo($aP = null, $w5 = null)
    {
        if (!empty($w5)) {
            goto wU;
        }
        $w5 = $this->rawNode;
        wU:
        return self::staticLocateKeyInfo($aP, $w5);
    }
}
