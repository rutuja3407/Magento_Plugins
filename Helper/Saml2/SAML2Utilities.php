<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\Lib\XMLSecEnc;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityDSig;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
class SAML2Utilities
{
    public static function generateID()
    {
        return "\x5f" . self::stringToHex(self::generateRandomBytes(21));
    }
    public static function stringToHex($Eu)
    {
        $a7 = '';
        $nO = 0;
        Pa:
        if (!($nO < strlen($Eu))) {
            goto tC;
        }
        $a7 .= sprintf("\45\x30\62\x78", ord($Eu[$nO]));
        BR:
        $nO++;
        goto Pa;
        tC:
        return $a7;
    }
    public static function generateRandomBytes($E4)
    {
        return openssl_random_pseudo_bytes($E4);
    }
    public static function generateTimestamp($dY = NULL)
    {
        if (!($dY === NULL)) {
            goto WT;
        }
        $dY = time();
        WT:
        return gmdate("\131\x2d\155\x2d\x64\x5c\x54\x48\72\x69\x3a\x73\x5c\x5a", $dY);
    }
    public static function parseNameId(\DOMElement $xa)
    {
        $a7 = array("\x56\x61\154\x75\x65" => trim($xa->textContent));
        foreach (array("\x4e\141\x6d\x65\x51\x75\x61\154\151\x66\x69\145\x72", "\123\x50\x4e\141\155\x65\121\165\x61\x6c\151\146\151\145\162", "\x46\x6f\162\155\x61\164") as $QX) {
            if (!$xa->hasAttribute($QX)) {
                goto Zj;
            }
            $a7[$QX] = $xa->getAttribute($QX);
            Zj:
            aG:
        }
        Lz:
        return $a7;
    }
    public static function xsDateTimeToTimestamp($eQ)
    {
        $ob = array();
        $UG = "\x2f\x5e\50\x5c\x64\x5c\x64\x5c\x64\134\144\x29\x2d\x28\134\144\134\x64\x29\x2d\x28\x5c\144\134\x64\51\x54\50\134\x64\134\x64\51\x3a\50\134\x64\x5c\x64\51\72\x28\134\x64\134\x64\x29\50\x3f\x3a\x5c\56\134\x64\x2b\51\77\132\44\57\104";
        if (!(preg_match($UG, $eQ, $ob) == 0)) {
            goto cl;
        }
        throw new \Exception("\111\x6e\x76\x61\x6c\151\x64\40\x53\101\x4d\114\x32\40\164\151\155\x65\x73\164\x61\x6d\160\40\160\141\x73\163\145\x64\x20\x74\x6f\40\170\163\104\141\x74\x65\x54\x69\x6d\x65\x54\157\124\151\x6d\145\x73\164\141\155\160\72\x20" . $eQ);
        cl:
        $tF = intval($ob[1]);
        $gg = intval($ob[2]);
        $vm = intval($ob[3]);
        $NK = intval($ob[4]);
        $Zw = intval($ob[5]);
        $s0 = intval($ob[6]);
        $OD = gmmktime($NK, $Zw, $s0, $gg, $vm, $tF);
        return $OD;
    }
    public static function decryptElement(\DOMElement $aa, XMLSecurityKey $I2, array $cz = array(), XMLSecurityKey $As = NULL)
    {
        try {
            return self::doDecryptElement($aa, $I2, $cz);
        } catch (\Exception $IR) {
            try {
                return self::doDecryptElement($aa, $As, $cz);
            } catch (\Exception $mH) {
                throw new \Exception("\x46\141\x69\154\145\x64\40\164\157\40\144\145\x63\162\x79\160\164\40\x58\115\x4c\40\145\154\x65\x6d\145\156\164\x2e");
            }
            throw new \Exception("\x46\141\151\x6c\145\144\40\x74\157\x20\144\145\x63\162\171\160\x74\40\130\115\x4c\40\145\x6c\145\x6d\145\156\164\56");
        }
    }
    private static function doDecryptElement(\DOMElement $aa, XMLSecurityKey $I2, array &$cz)
    {
        $nj = new XMLSecEnc();
        $nj->setNode($aa);
        $nj->type = $aa->getAttribute("\124\x79\x70\145");
        $ep = $nj->locateKey($aa);
        if ($ep) {
            goto nN;
        }
        throw new \Exception("\x43\x6f\x75\154\x64\x20\156\x6f\164\40\154\157\143\141\164\145\x20\x6b\x65\x79\40\141\x6c\x67\157\162\x69\x74\x68\155\x20\x69\x6e\x20\145\x6e\x63\162\x79\160\164\145\x64\x20\x64\141\x74\141\56");
        nN:
        $EQ = $nj->locateKeyInfo($ep);
        if ($EQ) {
            goto JJ;
        }
        throw new \Exception("\103\x6f\x75\154\x64\40\156\157\164\x20\x6c\157\143\x61\x74\x65\40\x3c\144\163\151\x67\x3a\x4b\x65\171\111\156\146\x6f\x3e\40\146\157\x72\40\x74\x68\x65\x20\145\x6e\143\162\x79\160\164\x65\144\40\153\145\x79\x2e");
        JJ:
        $kB = $I2->getAlgorith();
        if ($EQ->isEncrypted) {
            goto Mb;
        }
        $pQ = $ep->getAlgorith();
        if (!($kB !== $pQ)) {
            goto sI;
        }
        throw new \Exception("\101\154\x67\157\x72\x69\164\150\x6d\x20\155\151\x73\155\141\164\x63\150\40\x62\145\164\167\145\x65\156\x20\x69\156\160\x75\x74\x20\153\145\x79\40\141\156\144\x20\x6b\145\171\40\151\156\40\x6d\x65\163\x73\x61\x67\145\x2e\x20" . "\x4b\145\171\40\x77\141\x73\72\40" . var_export($kB, TRUE) . "\x3b\40\x6d\145\163\163\141\147\145\x20\x77\141\x73\x3a\40" . var_export($pQ, TRUE));
        sI:
        $ep = $I2;
        goto sC;
        Mb:
        $Hl = $EQ->getAlgorith();
        if (!in_array($Hl, $cz, TRUE)) {
            goto fS;
        }
        throw new \Exception("\101\x6c\x67\x6f\x72\x69\164\150\x6d\x20\144\151\163\x61\x62\154\145\x64\x3a\x20" . var_export($Hl, TRUE));
        fS:
        if (!($Hl === XMLSecurityKey::RSA_OAEP_MGF1P && $kB === XMLSecurityKey::RSA_1_5)) {
            goto CN;
        }
        $kB = XMLSecurityKey::RSA_OAEP_MGF1P;
        CN:
        if (!($kB !== $Hl)) {
            goto xi;
        }
        throw new \Exception("\101\x6c\x67\x6f\162\x69\x74\150\155\x20\155\151\163\155\x61\164\x63\x68\x20\142\145\x74\x77\145\x65\156\40\151\156\x70\x75\x74\40\153\145\x79\40\x61\156\144\40\153\x65\x79\x20\165\163\x65\144\40\164\157\40\145\156\143\162\171\x70\x74\x20" . "\x20\164\150\145\40\x73\x79\155\x6d\145\164\162\151\x63\40\153\x65\171\x20\x66\157\x72\x20\x74\x68\x65\40\x6d\145\x73\x73\141\147\x65\56\40\x4b\145\x79\x20\167\141\x73\x3a\40" . var_export($kB, TRUE) . "\x3b\x20\x6d\x65\163\163\141\x67\x65\x20\x77\141\x73\72\x20" . var_export($Hl, TRUE));
        xi:
        $nw = $EQ->encryptedCtx;
        $EQ->key = $I2->key;
        $vW = $ep->getSymmetricKeySize();
        if (!($vW === NULL)) {
            goto Fa;
        }
        throw new \Exception("\125\156\x6b\x6e\157\167\x6e\40\153\145\x79\x20\x73\x69\172\x65\40\x66\x6f\x72\40\x65\x6e\143\162\x79\160\x74\x69\157\x6e\40\141\154\x67\x6f\162\151\x74\x68\155\72\40" . var_export($ep->type, TRUE));
        Fa:
        try {
            $On = $nw->decryptKey($EQ);
            if (!(strlen($On) != $vW)) {
                goto YH;
            }
            throw new \Exception("\x55\x6e\145\170\160\145\x63\x74\145\144\40\x6b\x65\x79\40\163\x69\x7a\145\x20\x28" . strlen($On) * 8 . "\x62\151\x74\x73\51\40\x66\x6f\162\40\x65\x6e\x63\x72\x79\x70\x74\x69\x6f\156\x20\x61\x6c\x67\x6f\162\x69\x74\x68\155\72\40" . var_export($ep->type, TRUE));
            YH:
        } catch (\Exception $IR) {
            $nr = $nw->getCipherValue();
            $L5 = openssl_pkey_get_details($EQ->key);
            $L5 = sha1(json_encode($L5), TRUE);
            $On = sha1($nr . $L5, TRUE);
            if (strlen($On) > $vW) {
                goto SJ;
            }
            if (strlen($On) < $vW) {
                goto xo;
            }
            goto bI;
            SJ:
            $On = substr($On, 0, $vW);
            goto bI;
            xo:
            $On = str_pad($On, $vW);
            bI:
        }
        $ep->loadkey($On);
        sC:
        $QM = $ep->getAlgorith();
        if (!in_array($QM, $cz, TRUE)) {
            goto Dh;
        }
        throw new \Exception("\101\x6c\147\x6f\162\151\164\150\155\40\x64\x69\x73\141\x62\154\145\x64\x3a\x20" . var_export($QM, TRUE));
        Dh:
        $jn = $nj->decryptNode($ep, FALSE);
        $xa = "\74\162\x6f\157\x74\x20\170\155\154\x6e\x73\72\163\x61\x6d\x6c\x3d\x22\165\162\156\x3a\x6f\141\163\x69\163\x3a\x6e\x61\155\145\x73\x3a\x74\x63\x3a\x53\101\115\114\72\62\x2e\x30\72\141\x73\x73\x65\162\164\x69\x6f\x6e\x22\x20" . "\x78\x6d\154\x6e\163\72\170\163\151\x3d\x22\x68\164\164\160\x3a\57\x2f\x77\x77\x77\x2e\x77\63\x2e\157\162\x67\x2f\x32\x30\x30\x31\x2f\x58\x4d\x4c\x53\143\x68\145\155\x61\x2d\x69\x6e\163\x74\141\x6e\x63\x65\42\x3e" . $jn . "\74\57\x72\157\157\164\76";
        $oZ = new \DOMDocument();
        if ($oZ->loadXML($xa)) {
            goto N4;
        }
        throw new \Exception("\106\x61\151\154\145\144\x20\x74\157\40\x70\x61\x72\163\145\x20\x64\x65\143\162\x79\x70\x74\145\144\x20\130\115\x4c\x2e\40\x4d\x61\171\142\x65\40\x74\150\x65\x20\x77\x72\x6f\156\x67\x20\x73\150\x61\162\145\x64\153\145\171\40\x77\x61\163\40\x75\163\145\x64\77");
        N4:
        $nc = $oZ->firstChild->firstChild;
        if (!($nc === NULL)) {
            goto YB;
        }
        throw new \Exception("\x4d\151\x73\163\x69\156\x67\40\x65\156\143\162\171\160\x74\x65\144\40\145\x6c\145\155\x65\x6e\x74\56");
        YB:
        if ($nc instanceof \DOMElement) {
            goto w4;
        }
        throw new \Exception("\x44\145\143\162\x79\x70\x74\x65\x64\x20\145\154\145\x6d\145\x6e\164\x20\167\141\x73\x20\156\x6f\x74\40\x61\143\164\x75\141\154\154\x79\40\141\x20\x44\117\115\105\x6c\x65\x6d\x65\x6e\x74\56");
        w4:
        return $nc;
    }
    public static function extractStrings(\DOMElement $iJ, $xo, $jE)
    {
        $a7 = array();
        $zN = $iJ->firstChild;
        GB:
        if (!($zN !== NULL)) {
            goto xx;
        }
        if (!($zN->namespaceURI !== $xo || $zN->localName !== $jE)) {
            goto sl;
        }
        goto au;
        sl:
        $a7[] = trim($zN->textContent);
        au:
        $zN = $zN->nextSibling;
        goto GB;
        xx:
        return $a7;
    }
    public static function validateElement(\DOMElement $OC)
    {
        $Jt = new XMLSecurityDSig();
        $Jt->idKeys[] = "\x49\104";
        $Oa = self::xpQuery($OC, "\56\x2f\x64\x73\x3a\x53\x69\x67\x6e\141\x74\165\x72\145");
        if (count($Oa) === 0) {
            goto gp;
        }
        if (count($Oa) > 1) {
            goto kl;
        }
        goto ap;
        gp:
        return FALSE;
        goto ap;
        kl:
        throw new \Exception("\130\x4d\114\123\145\143\x3a\x20\155\157\162\145\x20\164\x68\141\x6e\x20\157\156\x65\x20\x73\151\147\x6e\x61\164\x75\162\x65\40\145\154\x65\155\x65\x6e\164\40\x69\x6e\40\x72\x6f\x6f\x74\x2e");
        ap:
        $Oa = $Oa[0];
        $Jt->sigNode = $Oa;
        $Jt->canonicalizeSignedInfo();
        if ($Jt->validateReference()) {
            goto I9;
        }
        throw new \Exception("\130\115\x4c\163\145\x63\72\40\144\151\x67\145\163\164\40\x76\141\154\x69\x64\141\x74\151\x6f\156\40\146\141\151\x6c\x65\x64");
        I9:
        $LD = FALSE;
        foreach ($Jt->getValidatedNodes() as $yv) {
            if ($yv->isSameNode($OC)) {
                goto Oz;
            }
            if ($OC->parentNode instanceof \DOMDocument && $yv->isSameNode($OC->ownerDocument)) {
                goto h8;
            }
            goto Ba;
            Oz:
            $LD = TRUE;
            goto U1;
            goto Ba;
            h8:
            $LD = TRUE;
            goto U1;
            Ba:
            Jq:
        }
        U1:
        if ($LD) {
            goto SP;
        }
        throw new \Exception("\130\115\x4c\x53\145\143\72\x20\124\x68\x65\x20\162\157\x6f\164\40\x65\x6c\x65\x6d\x65\x6e\x74\x20\x69\163\40\156\157\164\x20\x73\x69\x67\156\145\x64\56");
        SP:
        $T4 = array();
        foreach (self::xpQuery($Oa, "\56\57\144\163\72\113\x65\x79\111\156\146\157\57\144\x73\72\x58\x35\x30\x39\104\x61\x74\x61\x2f\144\x73\72\130\x35\60\x39\x43\x65\x72\x74\151\146\x69\x63\x61\x74\145") as $wl) {
            $YB = trim($wl->textContent);
            $YB = str_replace(array("\15", "\xa", "\11", "\x20"), '', $YB);
            $T4[] = $YB;
            AJ:
        }
        mV:
        $a7 = array("\123\x69\147\156\x61\164\165\162\145" => $Jt, "\103\145\162\164\151\146\x69\x63\x61\x74\x65\x73" => $T4);
        return $a7;
    }
    public static function xpQuery(\DomNode $zN, $NB)
    {
        static $UM = NULL;
        if ($zN instanceof \DOMDocument) {
            goto Ci;
        }
        $st = $zN->ownerDocument;
        goto t1;
        Ci:
        $st = $zN;
        t1:
        if (!($UM === NULL || !$UM->document->isSameNode($st))) {
            goto n8;
        }
        $UM = new \DOMXPath($st);
        $UM->registerNamespace("\163\x6f\x61\x70\x2d\145\156\x76", "\x68\x74\x74\x70\72\x2f\57\x73\x63\x68\145\155\x61\x73\56\x78\x6d\x6c\163\157\x61\160\56\x6f\162\147\57\x73\x6f\141\160\x2f\145\x6e\x76\x65\x6c\x6f\x70\x65\57");
        $UM->registerNamespace("\163\x61\155\154\x5f\x70\162\157\x74\157\143\157\154", "\x75\162\x6e\72\x6f\141\163\151\x73\x3a\x6e\141\155\x65\163\x3a\x74\143\72\123\101\x4d\114\x3a\62\56\x30\72\x70\162\x6f\x74\x6f\x63\x6f\154");
        $UM->registerNamespace("\163\141\x6d\x6c\x5f\x61\x73\x73\x65\162\164\151\157\x6e", "\x75\162\x6e\72\157\x61\x73\151\163\x3a\x6e\x61\x6d\x65\163\72\164\143\x3a\123\101\115\x4c\72\62\x2e\60\72\x61\x73\x73\x65\x72\164\x69\x6f\156");
        $UM->registerNamespace("\163\141\155\x6c\137\155\x65\x74\141\x64\x61\x74\x61", "\165\x72\156\x3a\x6f\141\x73\x69\x73\72\156\141\x6d\145\x73\72\x74\143\x3a\123\101\115\114\x3a\x32\x2e\60\72\x6d\145\164\141\144\x61\x74\x61");
        $UM->registerNamespace("\144\163", "\x68\164\164\x70\72\57\57\167\167\x77\x2e\167\x33\56\x6f\x72\x67\x2f\x32\x30\60\x30\57\x30\x39\x2f\170\x6d\x6c\x64\x73\x69\x67\x23");
        $UM->registerNamespace("\x78\x65\x6e\x63", "\150\164\164\160\x3a\x2f\57\167\x77\167\x2e\167\63\x2e\157\x72\x67\57\62\60\60\61\x2f\x30\64\x2f\x78\155\x6c\x65\156\143\x23");
        n8:
        $g_ = $UM->query($NB, $zN);
        $a7 = array();
        $nO = 0;
        b8:
        if (!($nO < $g_->length)) {
            goto E3;
        }
        $a7[$nO] = $g_->item($nO);
        JS:
        $nO++;
        goto b8;
        E3:
        return $a7;
    }
    public static function processResponse($rq, $X9, $C6, $Mf, SAML2Response $J_, $d2)
    {
        $Ox = $Mf["\103\x65\162\164\151\146\x69\x63\x61\x74\x65\x73"][0];
        $Hz = $J_->getDestination();
        if (!($Hz !== NULL && $Hz !== $X9)) {
            goto HF;
        }
        HF:
        $MI = self::checkSign($rq, $C6, $Mf, $Ox, $d2);
        return $MI;
    }
    public static function checkSign($rq, $C6, $Mf, $Ox, $d2)
    {
        $T4 = $Mf["\103\145\x72\x74\151\146\151\x63\x61\x74\145\163"];
        if (count($T4) === 0) {
            goto Sg;
        }
        $f5 = self::findCertificate($C6, $T4, $Ox);
        $Ps = NULL;
        goto kQ;
        Sg:
        $f5 = $d2;
        kQ:
        $On = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array("\x74\x79\160\x65" => "\160\165\142\154\x69\143"));
        $On->loadKey($f5);
        try {
            self::validateSignature($Mf, $On);
            return TRUE;
        } catch (Exception $IR) {
            $Ps = $IR;
        }
        if ($Ps !== NULL) {
            goto o7;
        }
        return FALSE;
        goto F9;
        o7:
        throw $Ps;
        F9:
    }
    public static function findCertificate($tW, $T4, $Ox)
    {
        $RP = array();
        foreach ($T4 as $d2) {
            $vT = strtolower(sha1(base64_decode($d2)));
            if (!($vT == $tW)) {
                goto r9;
            }
            $Nj = "\55\x2d\55\55\55\102\105\x47\x49\116\x20\103\x45\122\124\111\x46\111\103\x41\x54\x45\55\55\55\x2d\55\12" . chunk_split($d2, 64) . "\x2d\x2d\55\x2d\55\x45\x4e\x44\x20\x43\x45\122\124\111\x46\111\x43\x41\124\x45\55\x2d\x2d\x2d\x2d\12";
            return $Nj;
            r9:
            PI:
        }
        MU:
        return FALSE;
    }
    public static function validateSignature(array $BU, XMLSecurityKey $On)
    {
        $Jt = $BU["\x53\x69\147\x6e\x61\164\165\x72\145"];
        $vc = self::xpQuery($Jt->sigNode, "\x2e\57\x64\163\72\x53\151\147\156\x65\x64\x49\x6e\x66\x6f\x2f\x64\163\72\123\151\147\x6e\141\x74\x75\162\145\115\145\x74\150\x6f\x64");
        if (!empty($vc)) {
            goto C2;
        }
        echo sprintf("\115\x69\x73\163\x69\156\x67\x20\x53\151\x67\x6e\x61\164\165\162\145\115\145\164\150\x6f\144\40\145\x6c\x65\155\145\x6e\164");
        exit;
        C2:
        $vc = $vc[0];
        if ($vc->hasAttribute("\x41\154\147\157\162\151\164\150\155")) {
            goto dv;
        }
        echo sprintf("\x4d\151\163\x73\x69\156\147\x20\101\154\x67\157\162\x69\x74\150\x6d\x2d\141\x74\164\x72\151\142\165\164\x65\x20\x6f\x6e\x20\123\x69\x67\x6e\x61\x74\165\162\145\x4d\145\164\150\x6f\x64\40\145\x6c\145\x6d\145\x6e\164\56");
        exit;
        dv:
        $Vl = $vc->getAttribute("\101\154\147\157\162\151\164\x68\155");
        if (!($On->type === XMLSecurityKey::RSA_SHA1 && $Vl !== $On->type)) {
            goto NX;
        }
        $On = self::castKey($On, $Vl);
        NX:
        if ($Jt->verify($On)) {
            goto eF;
        }
        echo sprintf("\x55\156\x61\x62\154\145\x20\164\157\x20\x76\141\154\x69\144\x61\x74\145\40\123\x67\156\x61\164\165\x72\145");
        exit;
        eF:
    }
    public static function castKey(XMLSecurityKey $On, $QM, $qI = "\x70\x75\x62\x6c\x69\x63")
    {
        if (!($On->type === $QM)) {
            goto nQ;
        }
        return $On;
        nQ:
        $He = openssl_pkey_get_details($On->key);
        if (!($He === FALSE)) {
            goto pN;
        }
        throw new \Exception("\125\156\x61\x62\x6c\145\x20\x74\157\x20\x67\x65\x74\40\x6b\x65\171\x20\x64\145\164\141\151\x6c\x73\x20\146\x72\157\155\x20\130\115\114\x53\x65\x63\165\x72\151\x74\171\113\x65\171\56");
        pN:
        if (!empty($He["\153\145\x79"])) {
            goto Wd;
        }
        throw new \Exception("\x4d\151\163\163\151\x6e\147\40\x6b\145\x79\40\x69\156\x20\x70\x75\x62\x6c\x69\143\x20\x6b\x65\x79\x20\x64\145\164\141\x69\x6c\x73\x2e");
        Wd:
        $Zy = new XMLSecurityKey($QM, array("\164\x79\160\x65" => $qI));
        $Zy->loadKey($He["\153\x65\x79"]);
        return $Zy;
    }
    public static function parseBoolean(\DOMElement $zN, $XE, $Rp = null)
    {
        if ($zN->hasAttribute($XE)) {
            goto nF;
        }
        return $Rp;
        nF:
        $VP = $zN->getAttribute($XE);
        switch (strtolower($VP)) {
            case "\x30":
            case "\146\x61\154\x73\x65":
                return false;
            case "\61":
            case "\x74\162\x75\x65":
                return true;
            default:
                throw new \Exception("\111\156\x76\x61\x6c\x69\144\40\166\141\x6c\165\145\x20\157\x66\40\x62\157\157\x6c\145\141\156\40\x61\x74\x74\162\x69\x62\165\x74\145\x20" . var_export($XE, true) . "\72\40" . var_export($VP, true));
        }
        bk:
        M0:
    }
    public static function signXML($xa, $I5, $p5, $EX = '')
    {
        $Cv = array("\x74\x79\x70\x65" => "\x70\162\x69\166\141\164\145");
        $On = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $Cv);
        $On->loadKey($p5);
        $rT = new \DOMDocument();
        $rT->loadXML($xa);
        $hL = $rT->firstChild;
        if (!empty($EX)) {
            goto Xz;
        }
        self::insertSignature($On, array($I5), $hL);
        goto Tq;
        Xz:
        $Jy = $rT->getElementsByTagName($EX)->item(0);
        self::insertSignature($On, array($I5), $hL, $Jy);
        Tq:
        $Y5 = $hL->ownerDocument->saveXML($hL);
        return $Y5;
    }
    public static function insertSignature(XMLSecurityKey $On, array $T4, \DOMElement $OC, \DomNode $A9 = NULL)
    {
        $Jt = new XMLSecurityDSig();
        $Jt->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        switch ($On->type) {
            case XMLSecurityKey::RSA_SHA256:
                $qI = XMLSecurityDSig::SHA256;
                goto SB;
            case XMLSecurityKey::RSA_SHA384:
                $qI = XMLSecurityDSig::SHA384;
                goto SB;
            case XMLSecurityKey::RSA_SHA512:
                $qI = XMLSecurityDSig::SHA512;
                goto SB;
            default:
                $qI = XMLSecurityDSig::SHA1;
        }
        e2:
        SB:
        $Jt->addReferenceList(array($OC), $qI, array("\x68\164\x74\160\x3a\57\57\x77\x77\x77\56\x77\63\x2e\x6f\162\x67\57\x32\x30\x30\60\x2f\60\71\x2f\170\x6d\x6c\144\x73\151\147\43\x65\x6e\x76\x65\154\x6f\160\145\x64\x2d\x73\x69\x67\x6e\x61\x74\x75\162\145", XMLSecurityDSig::EXC_C14N), array("\151\144\x5f\x6e\x61\x6d\145" => "\111\x44", "\x6f\x76\x65\162\x77\162\x69\164\145" => FALSE));
        $Jt->sign($On);
        foreach ($T4 as $UY) {
            $Jt->add509Cert($UY, TRUE);
            KN:
        }
        qh:
        $Jt->insertSignature($OC, $A9);
    }
    public static function getEncryptionAlgorithm($vh)
    {
        switch ($vh) {
            case "\150\164\164\x70\x3a\57\x2f\x77\x77\x77\56\167\63\x2e\x6f\x72\147\57\x32\x30\x30\61\57\60\x34\x2f\170\155\x6c\x65\156\143\x23\x74\x72\x69\160\x6c\x65\144\145\x73\55\143\142\143":
                return XMLSecurityKey::TRIPLEDES_CBC;
            case "\150\164\164\160\72\x2f\x2f\x77\167\x77\x2e\167\63\x2e\157\x72\147\x2f\x32\x30\x30\61\x2f\60\x34\x2f\x78\x6d\154\145\156\143\43\141\x65\163\61\x32\70\x2d\x63\142\x63":
                return XMLSecurityKey::AES128_CBC;
            case "\x68\164\x74\160\x3a\x2f\x2f\x77\x77\167\x2e\167\x33\56\157\x72\x67\x2f\62\x30\x30\x31\x2f\60\x34\x2f\170\155\x6c\x65\x6e\x63\x23\x61\145\x73\61\x39\62\x2d\x63\142\x63":
                return XMLSecurityKey::AES192_CBC;
            case "\150\164\x74\160\72\57\x2f\167\x77\167\56\167\63\x2e\x6f\162\x67\x2f\62\x30\60\61\x2f\x30\64\57\x78\155\154\145\x6e\x63\43\x61\x65\x73\x32\65\x36\55\143\x62\x63":
                return XMLSecurityKey::AES256_CBC;
            case "\150\x74\x74\160\x3a\57\x2f\x77\167\x77\x2e\167\63\56\x6f\x72\147\x2f\x32\x30\x30\61\57\x30\x34\x2f\x78\155\154\x65\x6e\143\43\x72\163\141\x2d\61\x5f\65":
                return XMLSecurityKey::RSA_1_5;
            case "\150\x74\x74\160\72\57\x2f\x77\167\x77\56\x77\63\56\157\162\147\x2f\62\x30\60\61\57\60\x34\57\x78\155\x6c\x65\x6e\143\x23\x72\x73\x61\55\157\141\x65\x70\55\x6d\147\146\x31\160":
                return XMLSecurityKey::RSA_OAEP_MGF1P;
            case "\x68\x74\164\160\72\x2f\57\167\x77\x77\x2e\x77\63\56\x6f\x72\147\57\62\60\60\x30\57\x30\x39\57\x78\x6d\154\x64\163\151\x67\x23\144\163\141\55\x73\150\x61\x31":
                return XMLSecurityKey::DSA_SHA1;
            case "\x68\164\164\x70\72\x2f\57\x77\167\167\x2e\167\63\x2e\x6f\162\x67\57\62\x30\x30\x30\x2f\60\71\57\170\155\x6c\144\163\151\147\43\x72\x73\141\x2d\163\x68\141\61":
                return XMLSecurityKey::RSA_SHA1;
            case "\150\x74\164\x70\x3a\x2f\57\x77\167\167\56\167\x33\x2e\x6f\162\147\57\62\x30\60\x31\57\60\x34\x2f\x78\x6d\x6c\x64\x73\x69\x67\x2d\155\x6f\x72\x65\x23\x72\x73\141\x2d\x73\150\141\x32\65\x36":
                return XMLSecurityKey::RSA_SHA256;
            case "\x68\164\164\160\72\57\x2f\167\x77\167\56\167\x33\56\157\x72\x67\57\x32\60\x30\61\57\x30\64\x2f\x78\x6d\x6c\144\163\x69\147\55\155\157\162\x65\x23\x72\x73\141\55\x73\150\x61\x33\70\x34":
                return XMLSecurityKey::RSA_SHA384;
            case "\150\164\x74\x70\72\x2f\x2f\167\167\167\56\167\63\56\x6f\162\147\x2f\62\x30\x30\x31\x2f\x30\x34\x2f\170\x6d\154\x64\163\151\147\x2d\155\157\162\x65\x23\162\163\x61\55\163\x68\x61\65\x31\62":
                return XMLSecurityKey::RSA_SHA512;
            default:
                throw new \Exception("\111\x6e\x76\141\x6c\x69\144\40\x45\156\x63\x72\x79\160\x74\151\x6f\156\40\x4d\x65\164\x68\x6f\144\72\x20" . $vh);
        }
        VP:
        sL:
    }
    public static function sanitize_certificate($UY)
    {
        $UY = preg_replace("\x2f\133\xd\12\x5d\53\57", '', $UY);
        $UY = str_replace("\55", '', $UY);
        $UY = str_replace("\x42\x45\x47\111\116\x20\103\105\x52\124\x49\106\x49\x43\x41\124\x45", '', $UY);
        $UY = str_replace("\x45\x4e\104\40\103\x45\x52\124\111\x46\111\x43\x41\124\x45", '', $UY);
        $UY = str_replace("\x20", '', $UY);
        $UY = chunk_split($UY, 64, "\15\12");
        $UY = "\x2d\x2d\x2d\x2d\55\x42\105\x47\111\116\x20\103\105\122\124\x49\106\111\103\101\x54\x45\x2d\x2d\55\x2d\x2d\xd\xa" . $UY . "\x2d\55\55\55\x2d\105\x4e\x44\40\103\105\122\124\x49\106\111\x43\x41\x54\105\55\55\x2d\55\55";
        return $UY;
    }
    public static function desanitize_certificate($UY)
    {
        $UY = preg_replace("\57\133\15\12\135\53\57", '', $UY);
        $UY = str_replace("\55\x2d\x2d\55\55\x42\105\x47\x49\x4e\x20\x43\x45\x52\124\x49\106\111\103\x41\x54\105\x2d\55\x2d\x2d\55", '', $UY);
        $UY = str_replace("\x2d\x2d\55\x2d\x2d\105\x4e\104\x20\103\105\122\124\x49\x46\111\x43\x41\x54\x45\x2d\55\x2d\55\55", '', $UY);
        $UY = str_replace("\x20", '', $UY);
        return $UY;
    }
    public static function generateRandomAlphanumericValue($E4)
    {
        $MO = "\141\142\x63\x64\145\146\60\61\62\x33\64\65\66\67\70\x39";
        $ek = strlen($MO);
        $zG = '';
        $nO = 0;
        TD:
        if (!($nO < $E4)) {
            goto nJ;
        }
        $zG .= substr($MO, rand(0, 15), 1);
        OU:
        $nO++;
        goto TD;
        nJ:
        return "\141" . $zG;
    }
}
