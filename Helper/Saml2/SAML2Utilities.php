<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityDSig;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecEnc;
use MiniOrange\SP\Helper\SPConstants;
class SAML2Utilities
{
    public static function generateID()
    {
        return "\137" . self::stringToHex(self::generateRandomBytes(21));
    }
    public static function stringToHex($cV)
    {
        $iG = '';
        $X5 = 0;
        jEb:
        if (!($X5 < strlen($cV))) {
            goto xG8;
        }
        $iG .= sprintf("\x25\60\x32\170", ord($cV[$X5]));
        uwS:
        $X5++;
        goto jEb;
        xG8:
        return $iG;
    }
    public static function generateRandomBytes($vS)
    {
        return openssl_random_pseudo_bytes($vS);
    }
    public static function generateTimestamp($OJ = NULL)
    {
        if (!($OJ === NULL)) {
            goto DCL;
        }
        $OJ = time();
        DCL:
        return gmdate("\x59\x2d\x6d\55\x64\x5c\x54\x48\72\x69\x3a\163\x5c\x5a", $OJ);
    }
    public static function xpQuery(\DomNode $w5, $dU)
    {
        static $Cw = NULL;
        if ($w5 instanceof \DOMDocument) {
            goto zM7;
        }
        $Dy = $w5->ownerDocument;
        goto c61;
        zM7:
        $Dy = $w5;
        c61:
        if (!($Cw === NULL || !$Cw->document->isSameNode($Dy))) {
            goto iBY;
        }
        $Cw = new \DOMXPath($Dy);
        $Cw->registerNamespace("\x73\157\141\x70\55\x65\x6e\x76", "\x68\x74\164\160\x3a\x2f\57\x73\x63\x68\145\155\141\x73\x2e\x78\155\154\x73\x6f\x61\160\56\157\162\147\x2f\163\x6f\141\160\x2f\145\156\166\145\x6c\157\x70\145\57");
        $Cw->registerNamespace("\163\141\x6d\154\137\160\162\157\x74\x6f\143\x6f\x6c", "\165\162\156\x3a\x6f\141\163\151\163\72\156\141\155\x65\163\x3a\x74\x63\x3a\123\101\115\114\x3a\62\56\x30\72\160\162\x6f\164\157\143\157\x6c");
        $Cw->registerNamespace("\x73\141\x6d\154\x5f\141\163\x73\x65\x72\164\x69\x6f\156", "\x75\x72\x6e\x3a\157\141\163\x69\x73\72\x6e\141\x6d\x65\x73\x3a\164\143\x3a\123\101\x4d\x4c\72\x32\x2e\x30\72\141\163\x73\145\x72\164\151\x6f\x6e");
        $Cw->registerNamespace("\163\x61\155\x6c\137\155\x65\x74\x61\144\141\164\x61", "\165\162\156\x3a\x6f\x61\x73\151\x73\x3a\156\141\x6d\145\163\72\x74\143\72\123\101\x4d\x4c\72\x32\x2e\x30\x3a\x6d\x65\x74\x61\x64\141\x74\x61");
        $Cw->registerNamespace("\x64\x73", "\x68\x74\x74\x70\x3a\57\x2f\x77\x77\x77\56\167\63\56\157\162\147\x2f\62\x30\60\60\x2f\x30\71\57\170\155\x6c\x64\163\151\x67\43");
        $Cw->registerNamespace("\170\145\x6e\143", "\150\164\164\x70\x3a\57\57\x77\167\167\x2e\x77\63\56\157\162\147\x2f\x32\60\x30\x31\57\60\64\x2f\170\155\154\145\x6e\x63\x23");
        iBY:
        $Z4 = $Cw->query($dU, $w5);
        $iG = array();
        $X5 = 0;
        CYp:
        if (!($X5 < $Z4->length)) {
            goto u5G;
        }
        $iG[$X5] = $Z4->item($X5);
        tV6:
        $X5++;
        goto CYp;
        u5G:
        return $iG;
    }
    public static function parseNameId(\DOMElement $BY)
    {
        $iG = array("\126\141\x6c\x75\x65" => trim($BY->textContent));
        foreach (array("\116\x61\x6d\145\121\165\x61\154\151\146\x69\x65\x72", "\123\x50\116\x61\155\x65\x51\165\x61\x6c\x69\x66\151\145\x72", "\x46\x6f\162\x6d\x61\x74") as $Wt) {
            if (!$BY->hasAttribute($Wt)) {
                goto y8R;
            }
            $iG[$Wt] = $BY->getAttribute($Wt);
            y8R:
            Aax:
        }
        rUI:
        return $iG;
    }
    public static function xsDateTimeToTimestamp($eg)
    {
        $he = array();
        $t0 = "\57\136\50\134\x64\134\x64\x5c\x64\x5c\144\51\x2d\50\x5c\x64\134\x64\51\x2d\50\x5c\x64\134\x64\x29\x54\50\134\x64\134\x64\51\x3a\50\134\x64\x5c\144\x29\72\x28\x5c\x64\134\x64\x29\50\77\x3a\x5c\56\134\144\53\51\x3f\132\44\57\104";
        if (!(preg_match($t0, $eg, $he) == 0)) {
            goto gs3;
        }
        throw new \Exception("\x49\156\166\141\x6c\x69\144\40\123\x41\x4d\114\x32\x20\164\151\155\145\163\x74\x61\x6d\x70\x20\160\x61\163\163\x65\144\40\x74\x6f\40\170\163\x44\x61\x74\145\124\151\x6d\145\x54\x6f\x54\151\155\145\163\164\141\155\160\72\x20" . $eg);
        gs3:
        $So = intval($he[1]);
        $s3 = intval($he[2]);
        $R2 = intval($he[3]);
        $G9 = intval($he[4]);
        $wa = intval($he[5]);
        $GV = intval($he[6]);
        $FN = gmmktime($G9, $wa, $GV, $s3, $R2, $So);
        return $FN;
    }
    private static function doDecryptElement(\DOMElement $gU, XMLSecurityKey $rf, array &$KA)
    {
        $Xb = new XMLSecEnc();
        $Xb->setNode($gU);
        $Xb->type = $gU->getAttribute("\124\x79\160\145");
        $tb = $Xb->locateKey($gU);
        if ($tb) {
            goto J9b;
        }
        throw new \Exception("\x43\157\165\154\144\40\156\157\164\x20\x6c\x6f\x63\141\164\x65\x20\x6b\x65\171\40\141\x6c\147\x6f\162\151\x74\x68\x6d\x20\x69\x6e\x20\x65\156\143\x72\x79\x70\x74\x65\x64\x20\144\141\164\141\56");
        J9b:
        $ES = $Xb->locateKeyInfo($tb);
        if ($ES) {
            goto M4Z;
        }
        throw new \Exception("\x43\157\165\x6c\144\40\156\157\164\x20\154\x6f\143\141\x74\145\40\74\144\x73\151\147\72\113\145\x79\111\x6e\x66\x6f\76\40\x66\x6f\162\40\x74\150\x65\x20\x65\156\x63\x72\171\x70\x74\x65\x64\x20\x6b\x65\171\x2e");
        M4Z:
        $a0 = $rf->getAlgorith();
        if ($ES->isEncrypted) {
            goto tEY;
        }
        $J5 = $tb->getAlgorith();
        if (!($a0 !== $J5)) {
            goto lZ4;
        }
        throw new \Exception("\101\x6c\147\x6f\162\x69\x74\x68\155\40\155\151\x73\155\x61\x74\143\150\x20\x62\x65\164\167\145\145\x6e\40\151\x6e\160\x75\x74\x20\x6b\x65\171\40\x61\x6e\144\x20\x6b\x65\171\40\x69\x6e\x20\x6d\145\163\163\141\147\145\56\40" . "\113\x65\x79\x20\167\141\163\x3a\x20" . var_export($a0, TRUE) . "\x3b\x20\155\145\163\163\141\147\x65\x20\x77\x61\163\x3a\x20" . var_export($J5, TRUE));
        lZ4:
        $tb = $rf;
        goto R43;
        tEY:
        $C2 = $ES->getAlgorith();
        if (!in_array($C2, $KA, TRUE)) {
            goto w1D;
        }
        throw new \Exception("\x41\x6c\147\x6f\162\151\164\150\x6d\40\x64\151\163\x61\x62\x6c\x65\x64\72\x20" . var_export($C2, TRUE));
        w1D:
        if (!($C2 === XMLSecurityKey::RSA_OAEP_MGF1P && $a0 === XMLSecurityKey::RSA_1_5)) {
            goto LO1;
        }
        $a0 = XMLSecurityKey::RSA_OAEP_MGF1P;
        LO1:
        if (!($a0 !== $C2)) {
            goto Qz5;
        }
        throw new \Exception("\101\x6c\147\x6f\162\151\x74\150\x6d\x20\x6d\151\x73\x6d\141\164\x63\x68\40\142\x65\x74\167\x65\145\156\40\x69\x6e\x70\x75\164\40\x6b\145\171\x20\x61\156\x64\40\153\145\171\40\x75\x73\145\144\40\x74\157\40\145\156\x63\x72\x79\x70\x74\x20" . "\40\x74\150\145\40\163\171\155\x6d\145\164\x72\151\x63\x20\153\x65\171\x20\146\157\162\x20\164\150\145\x20\x6d\x65\163\163\x61\147\145\56\x20\x4b\145\x79\x20\x77\141\163\x3a\x20" . var_export($a0, TRUE) . "\73\x20\x6d\145\x73\163\141\147\x65\x20\167\141\x73\x3a\40" . var_export($C2, TRUE));
        Qz5:
        $d2 = $ES->encryptedCtx;
        $ES->key = $rf->key;
        $VV = $tb->getSymmetricKeySize();
        if (!($VV === NULL)) {
            goto YYT;
        }
        throw new \Exception("\125\156\x6b\156\157\167\x6e\40\153\x65\x79\40\x73\x69\x7a\145\x20\x66\157\162\x20\145\x6e\x63\162\171\160\164\151\157\156\40\x61\154\147\x6f\162\x69\x74\x68\x6d\x3a\x20" . var_export($tb->type, TRUE));
        YYT:
        try {
            $zg = $d2->decryptKey($ES);
            if (!(strlen($zg) != $VV)) {
                goto pF4;
            }
            throw new \Exception("\x55\156\145\170\160\145\143\164\145\144\40\x6b\x65\x79\x20\163\151\172\x65\40\x28" . strlen($zg) * 8 . "\x62\151\x74\163\51\x20\146\x6f\x72\x20\145\x6e\143\162\171\160\164\x69\157\x6e\40\141\154\x67\157\x72\151\164\x68\155\72\x20" . var_export($tb->type, TRUE));
            pF4:
        } catch (\Exception $sS) {
            $tu = $d2->getCipherValue();
            $fQ = openssl_pkey_get_details($ES->key);
            $fQ = sha1(json_encode($fQ), TRUE);
            $zg = sha1($tu . $fQ, TRUE);
            if (strlen($zg) > $VV) {
                goto Bqz;
            }
            if (strlen($zg) < $VV) {
                goto uUH;
            }
            goto GJT;
            Bqz:
            $zg = substr($zg, 0, $VV);
            goto GJT;
            uUH:
            $zg = str_pad($zg, $VV);
            GJT:
        }
        $tb->loadkey($zg);
        R43:
        $X7 = $tb->getAlgorith();
        if (!in_array($X7, $KA, TRUE)) {
            goto KrC;
        }
        throw new \Exception("\101\154\x67\x6f\162\x69\x74\x68\x6d\x20\x64\151\x73\x61\142\x6c\x65\x64\72\40" . var_export($X7, TRUE));
        KrC:
        $G8 = $Xb->decryptNode($tb, FALSE);
        $BY = "\x3c\x72\157\x6f\x74\x20\170\155\154\x6e\x73\x3a\163\141\155\x6c\75\x22\165\162\x6e\x3a\x6f\141\163\151\x73\72\156\x61\x6d\145\x73\72\x74\143\x3a\x53\101\115\114\72\62\x2e\x30\72\x61\163\x73\x65\x72\164\x69\x6f\156\42\x20" . "\x78\155\154\156\x73\x3a\x78\163\151\x3d\x22\x68\164\x74\160\x3a\x2f\57\167\x77\x77\56\167\63\56\x6f\162\x67\57\x32\x30\60\61\x2f\x58\x4d\114\x53\x63\150\x65\155\141\55\151\156\x73\x74\141\156\143\145\x22\76" . $G8 . "\x3c\x2f\x72\x6f\157\x74\x3e";
        $ck = new \DOMDocument();
        if ($ck->loadXML($BY)) {
            goto ucT;
        }
        throw new \Exception("\106\141\x69\154\145\144\40\164\x6f\40\160\x61\x72\x73\x65\40\x64\145\143\162\171\160\x74\145\144\x20\x58\115\114\56\40\115\141\x79\142\145\40\164\150\x65\40\167\162\x6f\156\x67\x20\x73\150\x61\162\145\144\x6b\x65\171\40\167\141\163\40\x75\163\x65\144\77");
        ucT:
        $uo = $ck->firstChild->firstChild;
        if (!($uo === NULL)) {
            goto FB1;
        }
        throw new \Exception("\x4d\x69\163\x73\151\156\x67\40\x65\156\143\x72\171\x70\x74\x65\144\x20\145\x6c\145\x6d\x65\x6e\x74\x2e");
        FB1:
        if ($uo instanceof \DOMElement) {
            goto NZT;
        }
        throw new \Exception("\x44\x65\143\x72\171\x70\x74\x65\144\40\145\x6c\145\155\x65\156\x74\x20\x77\141\163\40\156\157\x74\x20\x61\x63\x74\x75\141\x6c\154\171\x20\x61\40\x44\x4f\115\105\x6c\145\155\x65\156\164\56");
        NZT:
        return $uo;
    }
    public static function decryptElement(\DOMElement $gU, XMLSecurityKey $rf, array $KA = array(), XMLSecurityKey $UV = NULL)
    {
        try {
            return self::doDecryptElement($gU, $rf, $KA);
        } catch (\Exception $sS) {
            try {
                return self::doDecryptElement($gU, $UV, $KA);
            } catch (\Exception $nF) {
                throw new \Exception("\x46\x61\151\154\x65\x64\40\164\157\x20\x64\x65\143\x72\x79\160\x74\40\x58\x4d\x4c\x20\x65\x6c\x65\x6d\x65\156\164\x2e");
            }
            throw new \Exception("\x46\141\x69\154\x65\x64\x20\164\x6f\x20\144\x65\143\162\171\x70\164\40\130\115\x4c\40\x65\154\x65\155\145\x6e\164\x2e");
        }
    }
    public static function extractStrings(\DOMElement $US, $kV, $t6)
    {
        $iG = array();
        $w5 = $US->firstChild;
        wBl:
        if (!($w5 !== NULL)) {
            goto in0;
        }
        if (!($w5->namespaceURI !== $kV || $w5->localName !== $t6)) {
            goto m6N;
        }
        goto WtN;
        m6N:
        $iG[] = trim($w5->textContent);
        WtN:
        $w5 = $w5->nextSibling;
        goto wBl;
        in0:
        return $iG;
    }
    public static function validateElement(\DOMElement $N7)
    {
        $I0 = new XMLSecurityDSig();
        $I0->idKeys[] = "\x49\x44";
        $yt = self::xpQuery($N7, "\56\57\x64\x73\72\x53\x69\147\156\141\x74\x75\x72\x65");
        if (count($yt) === 0) {
            goto fxg;
        }
        if (count($yt) > 1) {
            goto hqA;
        }
        goto DNY;
        fxg:
        return FALSE;
        goto DNY;
        hqA:
        throw new \Exception("\x58\x4d\x4c\123\145\x63\72\x20\155\157\162\x65\x20\x74\150\141\x6e\x20\x6f\156\x65\40\x73\x69\x67\156\x61\164\x75\162\x65\x20\145\154\145\x6d\x65\156\164\40\x69\156\40\x72\157\x6f\x74\x2e");
        DNY:
        $yt = $yt[0];
        $I0->sigNode = $yt;
        $I0->canonicalizeSignedInfo();
        if ($I0->validateReference()) {
            goto Dc8;
        }
        throw new \Exception("\x58\x4d\114\163\145\143\72\40\x64\151\x67\145\163\164\40\166\141\x6c\151\144\141\x74\151\157\156\40\x66\x61\151\x6c\x65\144");
        Dc8:
        $cU = FALSE;
        foreach ($I0->getValidatedNodes() as $tz) {
            if ($tz->isSameNode($N7)) {
                goto dLU;
            }
            if ($N7->parentNode instanceof \DOMDocument && $tz->isSameNode($N7->ownerDocument)) {
                goto Q1K;
            }
            goto Rk7;
            dLU:
            $cU = TRUE;
            goto qqp;
            goto Rk7;
            Q1K:
            $cU = TRUE;
            goto qqp;
            Rk7:
            L6_:
        }
        qqp:
        if ($cU) {
            goto KxC;
        }
        throw new \Exception("\130\115\114\123\145\143\x3a\x20\124\x68\x65\x20\x72\x6f\157\164\40\145\154\x65\x6d\x65\156\164\40\x69\x73\40\156\x6f\x74\x20\163\151\x67\156\145\x64\x2e");
        KxC:
        $fp = array();
        foreach (self::xpQuery($yt, "\x2e\57\x64\x73\72\113\145\x79\x49\x6e\x66\x6f\57\x64\x73\x3a\130\x35\60\71\104\x61\164\141\57\144\163\72\130\65\x30\x39\103\145\x72\x74\151\x66\151\143\x61\164\145") as $wm) {
            $MS = trim($wm->textContent);
            $MS = str_replace(array("\15", "\xa", "\11", "\x20"), '', $MS);
            $fp[] = $MS;
            xx7:
        }
        ECo:
        $iG = array("\x53\x69\147\156\x61\164\165\x72\145" => $I0, "\103\x65\162\164\151\146\x69\x63\x61\x74\x65\x73" => $fp);
        return $iG;
    }
    public static function validateSignature(array $lv, XMLSecurityKey $zg)
    {
        $I0 = $lv["\123\x69\x67\x6e\x61\164\x75\x72\x65"];
        $d3 = self::xpQuery($I0->sigNode, "\x2e\x2f\144\x73\x3a\x53\x69\147\x6e\x65\x64\x49\156\x66\x6f\57\x64\x73\x3a\123\151\147\x6e\141\164\x75\162\x65\x4d\x65\164\150\157\x64");
        if (!empty($d3)) {
            goto zmO;
        }
        echo sprintf("\115\151\163\163\x69\156\147\x20\123\151\x67\x6e\x61\x74\165\162\x65\x4d\145\164\150\157\x64\40\145\x6c\145\155\x65\156\164");
        exit;
        zmO:
        $d3 = $d3[0];
        if ($d3->hasAttribute("\101\154\x67\157\x72\151\164\x68\155")) {
            goto UuD;
        }
        echo sprintf("\x4d\151\x73\163\x69\156\x67\x20\x41\154\147\157\162\x69\164\x68\x6d\x2d\x61\164\164\x72\151\142\165\x74\x65\40\x6f\x6e\40\123\x69\147\156\141\164\x75\162\145\115\145\x74\x68\157\144\40\x65\154\x65\x6d\x65\156\164\56");
        exit;
        UuD:
        $s4 = $d3->getAttribute("\x41\154\147\x6f\162\151\164\x68\155");
        if (!($zg->type === XMLSecurityKey::RSA_SHA1 && $s4 !== $zg->type)) {
            goto KHE;
        }
        $zg = self::castKey($zg, $s4);
        KHE:
        if ($I0->verify($zg)) {
            goto krE;
        }
        echo sprintf("\x55\156\141\142\154\145\40\164\x6f\x20\166\141\154\151\144\x61\x74\145\40\x53\x67\156\x61\164\x75\162\x65");
        exit;
        krE:
    }
    public static function castKey(XMLSecurityKey $zg, $X7, $Nv = "\x70\165\142\154\151\143")
    {
        if (!($zg->type === $X7)) {
            goto q8k;
        }
        return $zg;
        q8k:
        $Fp = openssl_pkey_get_details($zg->key);
        if (!($Fp === FALSE)) {
            goto VL2;
        }
        throw new \Exception("\x55\x6e\x61\142\154\145\x20\164\x6f\40\x67\x65\164\x20\x6b\x65\x79\x20\x64\145\164\x61\151\x6c\163\40\x66\162\157\x6d\x20\130\x4d\114\x53\x65\x63\x75\162\151\164\171\113\145\x79\x2e");
        VL2:
        if (!empty($Fp["\153\x65\171"])) {
            goto L0r;
        }
        throw new \Exception("\115\151\163\x73\151\x6e\x67\x20\153\145\x79\40\x69\156\x20\160\x75\142\154\x69\x63\x20\x6b\x65\x79\x20\x64\x65\x74\141\151\x6c\163\x2e");
        L0r:
        $hx = new XMLSecurityKey($X7, array("\x74\x79\x70\x65" => $Nv));
        $hx->loadKey($Fp["\x6b\x65\171"]);
        return $hx;
    }
    public static function processResponse($rQ, $R6, $U1, $E1, SAML2Response $qm, $hj)
    {
        $MP = $E1["\x43\145\x72\164\151\x66\151\143\141\x74\x65\x73"][0];
        $dl = $qm->getDestination();
        if (!($dl !== NULL && $dl !== $R6)) {
            goto v0k;
        }
        v0k:
        $rl = self::checkSign($rQ, $U1, $E1, $MP, $hj);
        return $rl;
    }
    public static function checkSign($rQ, $U1, $E1, $MP, $hj)
    {
        $fp = $E1["\x43\x65\162\164\151\146\151\143\141\164\x65\163"];
        if (count($fp) === 0) {
            goto kHM;
        }
        $Jr = self::findCertificate($U1, $fp, $MP);
        $t5 = NULL;
        goto dq7;
        kHM:
        $Jr = $hj;
        dq7:
        $zg = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array("\164\x79\160\x65" => "\x70\x75\142\x6c\151\143"));
        $zg->loadKey($Jr);
        try {
            self::validateSignature($E1, $zg);
            return TRUE;
        } catch (Exception $sS) {
            $t5 = $sS;
        }
        if ($t5 !== NULL) {
            goto hEP;
        }
        return FALSE;
        goto nHv;
        hEP:
        throw $t5;
        nHv:
    }
    public static function findCertificate($Gt, $fp, $MP)
    {
        $PA = array();
        foreach ($fp as $hj) {
            $zf = strtolower(sha1(base64_decode($hj)));
            if (!($zf == $Gt)) {
                goto JJ0;
            }
            $ku = "\x2d\55\x2d\55\55\x42\x45\x47\x49\x4e\x20\103\105\x52\124\x49\106\x49\103\x41\124\x45\x2d\x2d\x2d\55\55\xa" . chunk_split($hj, 64) . "\55\x2d\x2d\x2d\x2d\105\x4e\104\40\x43\x45\122\124\111\106\x49\103\101\124\105\x2d\55\55\55\55\12";
            return $ku;
            JJ0:
            o3s:
        }
        b59:
        return FALSE;
    }
    public static function parseBoolean(\DOMElement $w5, $s6, $tL = null)
    {
        if ($w5->hasAttribute($s6)) {
            goto uf3;
        }
        return $tL;
        uf3:
        $Yk = $w5->getAttribute($s6);
        switch (strtolower($Yk)) {
            case "\60":
            case "\146\x61\154\x73\x65":
                return false;
            case "\x31":
            case "\164\162\x75\x65":
                return true;
            default:
                throw new \Exception("\x49\x6e\x76\x61\x6c\151\144\x20\x76\x61\154\165\x65\x20\x6f\x66\40\142\x6f\x6f\154\145\141\156\x20\141\164\164\162\x69\x62\165\x74\145\x20" . var_export($s6, true) . "\72\x20" . var_export($Yk, true));
        }
        H6F:
        c7a:
    }
    public static function insertSignature(XMLSecurityKey $zg, array $fp, \DOMElement $N7, \DomNode $G1 = NULL)
    {
        $I0 = new XMLSecurityDSig();
        $I0->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        switch ($zg->type) {
            case XMLSecurityKey::RSA_SHA256:
                $Nv = XMLSecurityDSig::SHA256;
                goto rU0;
            case XMLSecurityKey::RSA_SHA384:
                $Nv = XMLSecurityDSig::SHA384;
                goto rU0;
            case XMLSecurityKey::RSA_SHA512:
                $Nv = XMLSecurityDSig::SHA512;
                goto rU0;
            default:
                $Nv = XMLSecurityDSig::SHA1;
        }
        qyb:
        rU0:
        $I0->addReferenceList(array($N7), $Nv, array("\x68\x74\164\x70\72\57\x2f\167\x77\x77\56\167\x33\x2e\x6f\162\x67\57\x32\60\x30\60\57\x30\x39\x2f\170\155\154\x64\x73\x69\147\43\145\x6e\x76\x65\x6c\157\160\145\144\x2d\x73\x69\x67\156\x61\x74\x75\162\145", XMLSecurityDSig::EXC_C14N), array("\151\x64\x5f\x6e\141\x6d\145" => "\x49\104", "\x6f\x76\145\162\167\x72\151\x74\145" => FALSE));
        $I0->sign($zg);
        foreach ($fp as $Vv) {
            $I0->add509Cert($Vv, TRUE);
            YvP:
        }
        G2Y:
        $I0->insertSignature($N7, $G1);
    }
    public static function signXML($BY, $Fe, $AH, $JC = '')
    {
        $fT = array("\164\x79\x70\145" => "\x70\162\x69\166\141\x74\145");
        $zg = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $fT);
        $zg->loadKey($AH);
        $L5 = new \DOMDocument();
        $L5->loadXML($BY);
        $rM = $L5->firstChild;
        if (!empty($JC)) {
            goto CyK;
        }
        self::insertSignature($zg, array($Fe), $rM);
        goto OkI;
        CyK:
        $K8 = $L5->getElementsByTagName($JC)->item(0);
        self::insertSignature($zg, array($Fe), $rM, $K8);
        OkI:
        $gg = $rM->ownerDocument->saveXML($rM);
        return $gg;
    }
    public static function getEncryptionAlgorithm($bs)
    {
        switch ($bs) {
            case "\150\x74\164\x70\72\x2f\57\x77\x77\167\56\167\63\x2e\x6f\162\147\x2f\x32\x30\60\61\57\x30\x34\x2f\x78\155\154\145\156\143\x23\x74\x72\x69\160\x6c\145\144\x65\x73\55\x63\x62\143":
                return XMLSecurityKey::TRIPLEDES_CBC;
            case "\x68\164\164\160\72\57\x2f\167\167\x77\x2e\x77\x33\56\157\162\147\x2f\x32\x30\x30\61\x2f\60\x34\x2f\x78\155\154\x65\x6e\143\43\141\x65\x73\x31\x32\x38\x2d\143\x62\143":
                return XMLSecurityKey::AES128_CBC;
            case "\150\164\164\160\72\57\x2f\x77\x77\167\x2e\x77\63\x2e\157\x72\147\57\x32\x30\x30\61\57\x30\x34\57\170\x6d\154\x65\156\x63\43\x61\x65\x73\61\71\62\55\x63\x62\x63":
                return XMLSecurityKey::AES192_CBC;
            case "\x68\164\164\160\72\57\57\167\x77\167\56\167\63\56\x6f\x72\147\x2f\x32\60\60\x31\x2f\x30\64\57\170\155\154\145\x6e\143\43\x61\145\x73\62\65\66\55\143\142\143":
                return XMLSecurityKey::AES256_CBC;
            case "\150\164\164\x70\x3a\x2f\x2f\167\x77\x77\56\x77\63\x2e\157\162\147\x2f\x32\60\60\x31\57\x30\x34\x2f\170\x6d\x6c\145\156\x63\x23\x72\x73\141\x2d\x31\137\x35":
                return XMLSecurityKey::RSA_1_5;
            case "\x68\164\164\160\72\57\57\167\167\167\56\x77\x33\56\157\x72\x67\57\62\60\x30\61\x2f\x30\64\57\x78\155\x6c\x65\156\x63\43\x72\x73\141\x2d\x6f\141\145\160\55\x6d\x67\146\x31\160":
                return XMLSecurityKey::RSA_OAEP_MGF1P;
            case "\150\164\x74\x70\x3a\x2f\x2f\x77\x77\167\x2e\167\63\56\x6f\162\x67\57\x32\x30\x30\x30\57\x30\71\57\170\155\154\x64\x73\151\147\43\x64\x73\x61\55\x73\150\x61\x31":
                return XMLSecurityKey::DSA_SHA1;
            case "\x68\x74\x74\160\72\x2f\57\167\167\167\x2e\x77\x33\x2e\x6f\162\147\x2f\x32\60\x30\x30\x2f\60\x39\x2f\x78\155\154\144\x73\151\x67\43\162\x73\141\x2d\163\150\141\x31":
                return XMLSecurityKey::RSA_SHA1;
            case "\150\164\x74\160\72\x2f\57\167\167\167\x2e\x77\63\56\x6f\162\147\57\62\60\x30\x31\x2f\60\64\x2f\170\155\154\x64\x73\x69\147\x2d\x6d\157\162\145\x23\x72\163\141\55\x73\150\x61\62\x35\x36":
                return XMLSecurityKey::RSA_SHA256;
            case "\150\x74\164\x70\x3a\x2f\x2f\x77\167\x77\56\167\x33\x2e\x6f\x72\147\57\62\60\x30\61\x2f\x30\x34\x2f\170\155\x6c\x64\x73\151\147\x2d\155\x6f\162\145\43\x72\x73\141\55\163\x68\x61\x33\70\64":
                return XMLSecurityKey::RSA_SHA384;
            case "\150\x74\x74\160\x3a\x2f\57\167\x77\x77\x2e\x77\63\56\x6f\162\x67\x2f\x32\60\60\x31\x2f\60\64\57\170\155\154\x64\163\151\147\55\x6d\157\162\x65\43\162\x73\141\55\x73\x68\x61\x35\x31\x32":
                return XMLSecurityKey::RSA_SHA512;
            default:
                throw new \Exception("\111\x6e\x76\141\154\151\x64\x20\105\x6e\143\x72\171\x70\164\151\x6f\156\x20\x4d\145\164\x68\x6f\x64\72\x20" . $bs);
        }
        vcc:
        pC9:
    }
    public static function sanitize_certificate($Vv)
    {
        $Vv = preg_replace("\x2f\x5b\15\xa\135\53\57", '', $Vv);
        $Vv = str_replace("\55", '', $Vv);
        $Vv = str_replace("\x42\105\107\x49\x4e\40\103\105\122\124\x49\106\x49\103\101\x54\105", '', $Vv);
        $Vv = str_replace("\105\x4e\x44\x20\x43\x45\122\124\x49\106\111\103\101\x54\105", '', $Vv);
        $Vv = str_replace("\40", '', $Vv);
        $Vv = chunk_split($Vv, 64, "\15\xa");
        $Vv = "\55\55\55\55\55\102\105\107\111\116\x20\103\x45\x52\x54\x49\x46\111\x43\x41\124\x45\x2d\x2d\x2d\55\55\15\xa" . $Vv . "\55\55\x2d\x2d\55\105\116\104\40\103\105\122\x54\x49\x46\111\x43\101\x54\x45\55\55\55\x2d\55";
        return $Vv;
    }
    public static function desanitize_certificate($Vv)
    {
        $Vv = preg_replace("\x2f\133\15\xa\135\x2b\57", '', $Vv);
        $Vv = str_replace("\x2d\x2d\x2d\x2d\x2d\102\105\x47\111\x4e\x20\x43\x45\122\x54\x49\106\111\x43\x41\x54\105\x2d\x2d\55\55\x2d", '', $Vv);
        $Vv = str_replace("\x2d\55\x2d\x2d\x2d\105\x4e\104\40\x43\105\122\x54\111\106\111\x43\x41\x54\x45\x2d\55\55\x2d\x2d", '', $Vv);
        $Vv = str_replace("\x20", '', $Vv);
        return $Vv;
    }
    public static function generateRandomAlphanumericValue($vS)
    {
        $Ot = "\141\x62\143\144\145\146\60\61\62\63\x34\65\x36\67\70\x39";
        $oB = strlen($Ot);
        $il = '';
        $X5 = 0;
        DfA:
        if (!($X5 < $vS)) {
            goto APe;
        }
        $il .= substr($Ot, rand(0, 15), 1);
        Rru:
        $X5++;
        goto DfA;
        APe:
        return "\x61" . $il;
    }
}
