<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

use DOMElement;
use Exception;
class XMLSecurityKey
{
    const TRIPLEDES_CBC = "\x68\x74\164\160\x3a\x2f\57\x77\x77\x77\x2e\167\x33\x2e\157\x72\147\x2f\62\x30\60\61\x2f\x30\64\x2f\x78\155\x6c\x65\x6e\143\43\164\x72\x69\x70\154\145\144\x65\163\55\143\142\143";
    const AES128_CBC = "\x68\x74\164\x70\x3a\x2f\57\167\167\167\x2e\167\x33\56\x6f\x72\x67\57\x32\x30\x30\x31\x2f\x30\64\57\x78\155\x6c\x65\156\x63\43\x61\145\x73\x31\x32\x38\55\143\x62\x63";
    const AES192_CBC = "\x68\164\164\160\x3a\x2f\57\x77\167\167\x2e\167\63\x2e\x6f\162\147\x2f\x32\60\60\61\57\60\64\57\x78\x6d\x6c\145\x6e\143\x23\141\x65\x73\61\71\62\55\x63\142\143";
    const AES256_CBC = "\x68\164\x74\x70\x3a\57\57\167\167\x77\x2e\x77\63\x2e\x6f\x72\x67\57\62\60\60\x31\57\x30\64\x2f\x78\x6d\154\145\156\x63\x23\x61\145\163\62\65\x36\55\143\x62\143";
    const AES128_GCM = "\150\x74\164\x70\72\57\57\x77\x77\x77\x2e\x77\x33\56\157\162\x67\57\x32\x30\x30\71\57\x78\155\x6c\x65\156\143\61\61\x23\x61\145\x73\x31\62\x38\55\147\143\x6d";
    const AES192_GCM = "\150\x74\164\x70\72\x2f\57\167\x77\x77\x2e\167\63\56\157\x72\x67\x2f\62\x30\60\71\x2f\170\155\154\145\x6e\x63\61\61\x23\x61\145\x73\x31\x39\62\x2d\x67\143\x6d";
    const AES256_GCM = "\150\164\164\x70\x3a\57\57\x77\167\x77\56\167\63\x2e\x6f\162\x67\x2f\x32\x30\60\71\x2f\x78\155\154\145\x6e\x63\61\61\x23\x61\x65\163\62\65\x36\55\x67\143\x6d";
    const RSA_1_5 = "\x68\164\x74\160\x3a\x2f\x2f\x77\x77\x77\56\167\x33\56\157\162\x67\x2f\62\60\60\x31\57\60\x34\x2f\x78\x6d\154\145\x6e\x63\x23\x72\163\141\55\61\137\65";
    const RSA_OAEP_MGF1P = "\150\x74\164\x70\x3a\x2f\57\167\x77\x77\x2e\x77\63\x2e\x6f\x72\147\57\x32\60\60\61\x2f\x30\x34\57\x78\155\x6c\145\x6e\143\x23\x72\163\141\x2d\x6f\141\x65\160\55\x6d\147\x66\61\x70";
    const RSA_OAEP = "\150\x74\x74\x70\72\x2f\57\x77\167\167\56\167\x33\56\x6f\x72\147\57\62\x30\60\x39\57\170\x6d\x6c\145\156\143\x31\x31\43\x72\x73\141\x2d\x6f\x61\x65\160";
    const DSA_SHA1 = "\x68\x74\x74\160\72\57\x2f\x77\x77\x77\x2e\x77\63\56\x6f\x72\x67\x2f\62\60\60\60\x2f\x30\71\x2f\x78\155\154\144\163\151\x67\x23\x64\163\141\x2d\163\150\141\x31";
    const RSA_SHA1 = "\150\x74\x74\160\x3a\x2f\57\167\167\x77\x2e\x77\63\56\x6f\x72\147\57\x32\x30\60\60\x2f\60\71\57\x78\x6d\154\144\163\151\147\43\x72\x73\x61\55\163\150\x61\x31";
    const RSA_SHA256 = "\150\164\x74\x70\72\57\x2f\x77\167\x77\56\167\63\x2e\x6f\x72\x67\57\62\x30\60\61\x2f\x30\x34\57\x78\155\x6c\x64\x73\x69\147\x2d\x6d\157\x72\x65\43\162\x73\x61\x2d\163\150\x61\x32\65\x36";
    const RSA_SHA384 = "\x68\164\x74\x70\x3a\x2f\57\167\x77\x77\x2e\x77\x33\x2e\x6f\x72\x67\57\x32\x30\60\61\57\x30\x34\57\x78\x6d\x6c\144\163\151\147\55\x6d\x6f\162\145\x23\162\x73\141\55\x73\150\141\x33\x38\64";
    const RSA_SHA512 = "\x68\164\x74\x70\72\x2f\x2f\167\x77\x77\56\x77\63\56\x6f\x72\147\x2f\x32\x30\x30\x31\x2f\x30\x34\x2f\x78\x6d\154\144\x73\151\147\x2d\x6d\x6f\162\145\x23\162\163\141\55\163\x68\141\65\x31\62";
    const HMAC_SHA1 = "\x68\x74\x74\x70\72\x2f\x2f\x77\x77\167\56\167\x33\56\x6f\x72\147\57\x32\60\60\60\x2f\x30\x39\x2f\x78\x6d\x6c\144\163\x69\147\43\x68\155\x61\x63\x2d\163\150\141\61";
    const AUTHTAG_LENGTH = 16;
    public $type = 0;
    public $key = null;
    public $passphrase = '';
    public $iv = null;
    public $name = null;
    public $keyChain = null;
    public $isEncrypted = false;
    public $encryptedCtx = null;
    public $guid = null;
    private $cryptParams = array();
    private $x509Certificate = null;
    private $X509Thumbprint = null;
    public function __construct($qI, $Te = null)
    {
        switch ($qI) {
            case self::TRIPLEDES_CBC:
                $this->cryptParams["\x6c\151\x62\162\x61\x72\x79"] = "\157\160\145\x6e\x73\163\x6c";
                $this->cryptParams["\x63\151\x70\150\x65\x72"] = "\x64\145\163\55\145\144\145\63\x2d\x63\142\x63";
                $this->cryptParams["\x74\x79\160\x65"] = "\x73\x79\155\x6d\145\x74\x72\151\143";
                $this->cryptParams["\155\145\x74\150\x6f\144"] = "\150\164\x74\x70\x3a\57\x2f\x77\167\x77\x2e\x77\63\56\157\162\147\57\x32\60\x30\x31\x2f\60\x34\57\x78\155\x6c\145\156\143\43\164\162\x69\x70\x6c\145\144\x65\163\x2d\143\142\x63";
                $this->cryptParams["\x6b\x65\x79\x73\x69\172\145"] = 24;
                $this->cryptParams["\x62\x6c\157\x63\x6b\x73\x69\x7a\145"] = 8;
                goto sV;
            case self::AES128_CBC:
                $this->cryptParams["\154\x69\142\162\141\x72\171"] = "\x6f\160\145\156\x73\163\x6c";
                $this->cryptParams["\x63\x69\x70\x68\x65\162"] = "\x61\145\163\55\61\62\x38\55\x63\142\143";
                $this->cryptParams["\x74\171\160\x65"] = "\163\171\x6d\155\x65\x74\162\151\x63";
                $this->cryptParams["\x6d\145\x74\150\157\x64"] = "\x68\164\x74\160\72\x2f\57\x77\x77\167\x2e\x77\x33\x2e\x6f\162\147\x2f\62\60\60\61\57\x30\64\x2f\170\x6d\x6c\x65\156\143\x23\141\145\x73\61\x32\70\55\x63\142\x63";
                $this->cryptParams["\x6b\x65\x79\x73\151\x7a\145"] = 16;
                $this->cryptParams["\142\x6c\157\x63\x6b\163\151\x7a\x65"] = 16;
                goto sV;
            case self::AES192_CBC:
                $this->cryptParams["\154\x69\x62\x72\141\162\x79"] = "\157\160\145\156\163\163\154";
                $this->cryptParams["\x63\151\x70\x68\x65\162"] = "\141\145\163\x2d\x31\71\x32\x2d\x63\142\x63";
                $this->cryptParams["\x74\x79\x70\x65"] = "\163\171\x6d\155\145\164\162\x69\x63";
                $this->cryptParams["\x6d\x65\164\150\x6f\x64"] = "\150\x74\164\160\x3a\x2f\x2f\167\167\x77\56\x77\63\x2e\x6f\162\x67\57\62\x30\60\x31\57\60\64\57\170\155\x6c\145\156\143\x23\x61\x65\163\61\71\x32\x2d\143\142\143";
                $this->cryptParams["\x6b\x65\x79\163\151\172\x65"] = 24;
                $this->cryptParams["\142\154\157\x63\153\x73\x69\172\145"] = 16;
                goto sV;
            case self::AES256_CBC:
                $this->cryptParams["\154\x69\x62\x72\x61\162\x79"] = "\x6f\x70\x65\x6e\163\163\154";
                $this->cryptParams["\143\151\160\x68\x65\162"] = "\141\x65\x73\x2d\x32\65\x36\55\x63\142\143";
                $this->cryptParams["\164\x79\x70\145"] = "\x73\x79\155\x6d\x65\164\x72\151\x63";
                $this->cryptParams["\x6d\x65\164\150\x6f\144"] = "\150\x74\164\160\72\x2f\x2f\x77\x77\x77\56\x77\x33\56\157\162\x67\57\62\x30\60\x31\x2f\x30\64\57\170\155\154\145\156\x63\43\x61\x65\x73\x32\65\66\x2d\143\x62\143";
                $this->cryptParams["\x6b\x65\171\x73\151\172\x65"] = 32;
                $this->cryptParams["\142\x6c\157\143\x6b\x73\x69\172\x65"] = 16;
                goto sV;
            case self::AES128_GCM:
                $this->cryptParams["\154\151\x62\x72\x61\x72\x79"] = "\157\x70\145\x6e\x73\x73\x6c";
                $this->cryptParams["\x63\x69\x70\x68\x65\162"] = "\141\x65\163\55\x31\62\x38\55\x67\x63\x6d";
                $this->cryptParams["\x74\x79\160\145"] = "\x73\x79\155\x6d\145\164\162\151\x63";
                $this->cryptParams["\x6d\x65\x74\x68\157\x64"] = "\150\x74\164\x70\x3a\57\x2f\x77\167\167\56\167\63\x2e\157\162\147\x2f\x32\60\x30\x39\57\170\x6d\154\x65\x6e\143\61\x31\43\141\145\163\x31\62\70\x2d\x67\x63\x6d";
                $this->cryptParams["\x6b\145\x79\163\151\172\x65"] = 16;
                $this->cryptParams["\x62\154\x6f\143\153\163\x69\x7a\x65"] = 16;
                goto sV;
            case self::AES192_GCM:
                $this->cryptParams["\x6c\x69\x62\162\141\x72\x79"] = "\x6f\160\145\x6e\x73\163\x6c";
                $this->cryptParams["\x63\151\160\x68\145\x72"] = "\x61\145\x73\x2d\x31\x39\62\x2d\147\143\155";
                $this->cryptParams["\164\171\x70\x65"] = "\x73\171\x6d\x6d\x65\164\162\151\x63";
                $this->cryptParams["\x6d\145\x74\x68\x6f\x64"] = "\x68\164\x74\x70\x3a\x2f\57\167\x77\167\x2e\x77\x33\x2e\157\x72\147\57\x32\60\60\x39\57\170\x6d\x6c\x65\x6e\x63\61\x31\x23\141\145\x73\x31\71\x32\55\x67\143\x6d";
                $this->cryptParams["\153\145\171\163\x69\x7a\x65"] = 24;
                $this->cryptParams["\142\154\x6f\x63\153\x73\x69\172\145"] = 16;
                goto sV;
            case self::AES256_GCM:
                $this->cryptParams["\154\x69\142\162\x61\162\x79"] = "\157\160\x65\156\163\163\x6c";
                $this->cryptParams["\143\x69\x70\x68\x65\x72"] = "\141\x65\x73\x2d\62\x35\x36\x2d\x67\143\155";
                $this->cryptParams["\x74\171\x70\145"] = "\x73\x79\x6d\x6d\x65\x74\162\x69\x63";
                $this->cryptParams["\155\145\x74\150\157\144"] = "\150\164\x74\160\72\x2f\57\x77\x77\x77\x2e\x77\x33\x2e\157\162\147\x2f\x32\60\x30\x39\57\x78\155\x6c\145\156\x63\61\x31\x23\141\145\163\x32\65\66\x2d\147\x63\x6d";
                $this->cryptParams["\x6b\x65\x79\x73\x69\x7a\x65"] = 32;
                $this->cryptParams["\x62\x6c\x6f\143\153\x73\x69\x7a\145"] = 16;
                goto sV;
            case self::RSA_1_5:
                $this->cryptParams["\x6c\151\142\x72\141\162\171"] = "\157\x70\x65\156\163\x73\x6c";
                $this->cryptParams["\x70\x61\144\144\x69\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x6d\x65\x74\x68\157\144"] = "\150\164\164\x70\72\57\x2f\x77\167\167\56\x77\63\56\x6f\162\147\x2f\x32\60\60\x31\x2f\x30\x34\x2f\170\x6d\x6c\x65\x6e\x63\43\x72\x73\x61\x2d\x31\x5f\65";
                if (!(is_array($Te) && !empty($Te["\164\x79\160\x65"]))) {
                    goto zj;
                }
                if (!($Te["\164\171\160\145"] == "\160\x75\142\x6c\151\143" || $Te["\x74\171\160\145"] == "\x70\x72\x69\166\x61\164\x65")) {
                    goto Xl;
                }
                $this->cryptParams["\164\x79\160\145"] = $Te["\x74\x79\x70\x65"];
                goto sV;
                Xl:
                zj:
                throw new Exception("\x43\145\x72\164\151\146\151\143\141\x74\x65\x20\42\164\171\x70\x65\x22\x20\50\160\162\x69\166\x61\x74\145\57\x70\165\x62\x6c\151\143\51\x20\x6d\x75\x73\x74\40\142\x65\x20\x70\x61\163\x73\145\144\40\x76\x69\141\x20\160\141\x72\x61\x6d\145\x74\145\162\x73");
            case self::RSA_OAEP_MGF1P:
                $this->cryptParams["\x6c\151\142\162\x61\x72\x79"] = "\157\160\145\156\163\x73\x6c";
                $this->cryptParams["\160\141\x64\144\151\x6e\147"] = OPENSSL_PKCS1_OAEP_PADDING;
                $this->cryptParams["\x6d\145\x74\150\x6f\144"] = "\x68\164\164\x70\72\57\x2f\x77\x77\167\x2e\167\x33\56\x6f\x72\x67\57\62\60\60\61\x2f\60\64\57\x78\x6d\x6c\145\x6e\143\x23\162\163\x61\x2d\157\x61\x65\x70\x2d\x6d\147\x66\61\x70";
                $this->cryptParams["\150\141\163\x68"] = null;
                if (!(is_array($Te) && !empty($Te["\164\171\160\145"]))) {
                    goto B_;
                }
                if (!($Te["\x74\171\160\145"] == "\160\x75\142\x6c\x69\x63" || $Te["\164\171\x70\x65"] == "\x70\162\151\x76\x61\x74\x65")) {
                    goto vN;
                }
                $this->cryptParams["\x74\x79\160\145"] = $Te["\x74\x79\x70\x65"];
                goto sV;
                vN:
                B_:
                throw new Exception("\103\x65\162\164\x69\x66\151\143\141\x74\x65\40\x22\x74\x79\x70\145\x22\x20\x28\160\162\x69\166\141\x74\x65\57\x70\165\142\154\x69\x63\51\x20\x6d\x75\x73\164\40\x62\x65\40\160\x61\x73\x73\x65\144\x20\x76\x69\x61\40\x70\x61\x72\x61\155\145\x74\x65\162\163");
            case self::RSA_OAEP:
                $this->cryptParams["\x6c\x69\x62\162\141\x72\171"] = "\157\x70\x65\x6e\x73\163\154";
                $this->cryptParams["\x70\x61\144\144\151\156\x67"] = OPENSSL_PKCS1_OAEP_PADDING;
                $this->cryptParams["\155\x65\164\x68\x6f\144"] = "\150\x74\164\160\x3a\57\x2f\x77\x77\167\56\167\x33\56\157\162\147\x2f\62\60\60\71\57\x78\x6d\154\x65\x6e\x63\61\61\43\162\x73\x61\55\157\141\145\160";
                $this->cryptParams["\150\141\x73\x68"] = "\x68\x74\x74\160\x3a\x2f\x2f\x77\167\167\x2e\167\x33\56\x6f\x72\x67\57\x32\60\60\71\57\x78\155\x6c\x65\156\143\x31\61\43\155\x67\x66\61\163\150\141\x31";
                if (!(is_array($Te) && !empty($Te["\164\x79\x70\x65"]))) {
                    goto xQ;
                }
                if (!($Te["\x74\171\x70\x65"] == "\x70\x75\x62\x6c\x69\143" || $Te["\164\171\x70\x65"] == "\160\x72\151\166\141\x74\145")) {
                    goto gq;
                }
                $this->cryptParams["\164\171\160\x65"] = $Te["\x74\171\160\145"];
                goto sV;
                gq:
                xQ:
                throw new Exception("\x43\x65\x72\x74\151\146\151\143\141\x74\145\x20\42\x74\171\160\x65\42\x20\x28\x70\x72\x69\x76\141\x74\145\57\160\165\x62\x6c\151\143\x29\40\x6d\x75\x73\x74\40\142\x65\40\160\x61\163\163\145\144\x20\166\x69\141\x20\x70\141\162\141\x6d\x65\x74\145\162\x73");
            case self::RSA_SHA1:
                $this->cryptParams["\x6c\151\142\x72\141\x72\171"] = "\x6f\160\x65\156\163\x73\x6c";
                $this->cryptParams["\x6d\145\x74\150\157\144"] = "\150\164\x74\x70\x3a\57\x2f\167\x77\x77\x2e\x77\63\x2e\x6f\162\x67\57\x32\60\60\x30\x2f\x30\71\57\x78\155\x6c\144\163\x69\x67\x23\x72\163\x61\x2d\163\150\141\x31";
                $this->cryptParams["\x70\141\x64\144\151\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                if (!(is_array($Te) && !empty($Te["\x74\x79\160\145"]))) {
                    goto nd;
                }
                if (!($Te["\164\x79\x70\x65"] == "\x70\165\x62\x6c\x69\x63" || $Te["\164\x79\x70\x65"] == "\160\162\151\x76\141\x74\145")) {
                    goto Dw;
                }
                $this->cryptParams["\164\x79\160\145"] = $Te["\x74\171\160\x65"];
                goto sV;
                Dw:
                nd:
                throw new Exception("\103\145\162\164\x69\146\x69\x63\141\x74\145\40\x22\x74\x79\160\145\x22\x20\x28\x70\162\151\166\141\164\x65\57\160\x75\x62\154\x69\x63\x29\40\155\x75\x73\x74\40\142\145\x20\160\141\163\x73\145\x64\40\x76\x69\141\x20\160\141\162\x61\155\x65\x74\x65\x72\x73");
            case self::RSA_SHA256:
                $this->cryptParams["\x6c\151\142\x72\x61\162\x79"] = "\157\x70\x65\x6e\163\163\x6c";
                $this->cryptParams["\x6d\145\x74\150\x6f\144"] = "\x68\x74\164\160\72\57\x2f\x77\167\x77\x2e\x77\63\x2e\157\x72\x67\57\x32\60\60\x31\57\x30\64\57\x78\x6d\154\x64\163\x69\x67\x2d\x6d\x6f\162\x65\x23\x72\163\141\x2d\163\x68\x61\62\x35\x36";
                $this->cryptParams["\160\x61\x64\144\151\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\151\147\x65\163\164"] = "\123\x48\101\62\x35\x36";
                if (!(is_array($Te) && !empty($Te["\x74\171\x70\145"]))) {
                    goto C4;
                }
                if (!($Te["\x74\171\160\145"] == "\160\165\142\154\151\143" || $Te["\164\x79\160\x65"] == "\160\x72\x69\166\141\164\x65")) {
                    goto sM;
                }
                $this->cryptParams["\164\171\160\145"] = $Te["\x74\171\160\145"];
                goto sV;
                sM:
                C4:
                throw new Exception("\x43\145\x72\164\151\146\151\x63\141\x74\x65\40\x22\x74\171\x70\145\42\40\x28\x70\162\151\166\141\x74\x65\57\160\165\142\x6c\151\143\51\40\x6d\165\x73\164\x20\x62\x65\40\160\x61\163\x73\145\x64\40\x76\x69\141\x20\160\x61\162\x61\x6d\145\164\145\162\163");
            case self::RSA_SHA384:
                $this->cryptParams["\154\x69\x62\x72\x61\162\171"] = "\157\x70\x65\156\x73\163\154";
                $this->cryptParams["\x6d\145\x74\x68\x6f\144"] = "\x68\x74\x74\160\x3a\57\x2f\x77\x77\167\56\167\x33\56\x6f\162\147\57\62\60\60\61\x2f\60\x34\57\170\x6d\x6c\x64\163\151\x67\x2d\x6d\157\x72\x65\43\162\163\141\x2d\163\150\x61\63\x38\x34";
                $this->cryptParams["\160\x61\x64\144\x69\x6e\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\x69\x67\x65\x73\x74"] = "\x53\110\x41\x33\x38\64";
                if (!(is_array($Te) && !empty($Te["\x74\171\160\145"]))) {
                    goto FL;
                }
                if (!($Te["\x74\x79\x70\x65"] == "\x70\x75\x62\x6c\151\143" || $Te["\164\x79\160\x65"] == "\x70\x72\x69\x76\x61\164\145")) {
                    goto np;
                }
                $this->cryptParams["\x74\x79\160\x65"] = $Te["\x74\x79\x70\145"];
                goto sV;
                np:
                FL:
                throw new Exception("\103\145\x72\164\x69\146\x69\143\x61\164\x65\40\x22\x74\x79\x70\x65\x22\40\x28\x70\162\151\x76\x61\164\x65\57\160\165\x62\154\151\x63\x29\40\x6d\165\x73\x74\x20\142\x65\40\x70\141\x73\163\145\144\40\166\x69\x61\40\x70\141\162\x61\155\145\x74\x65\162\163");
            case self::RSA_SHA512:
                $this->cryptParams["\154\151\x62\x72\141\x72\x79"] = "\157\160\x65\156\x73\163\x6c";
                $this->cryptParams["\x6d\x65\164\150\x6f\144"] = "\150\x74\x74\160\x3a\x2f\x2f\167\167\x77\56\x77\x33\56\157\162\x67\x2f\62\x30\60\61\57\60\x34\x2f\170\x6d\154\144\x73\151\147\x2d\x6d\x6f\x72\145\43\162\x73\x61\55\163\150\141\x35\x31\x32";
                $this->cryptParams["\160\x61\144\x64\151\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\144\151\x67\145\x73\x74"] = "\x53\x48\101\x35\61\62";
                if (!(is_array($Te) && !empty($Te["\x74\171\160\145"]))) {
                    goto Pi;
                }
                if (!($Te["\x74\x79\160\x65"] == "\x70\x75\142\x6c\151\143" || $Te["\164\x79\160\145"] == "\160\x72\x69\166\141\164\145")) {
                    goto WN;
                }
                $this->cryptParams["\164\171\160\145"] = $Te["\x74\171\160\145"];
                goto sV;
                WN:
                Pi:
                throw new Exception("\x43\145\162\x74\151\x66\151\143\141\x74\x65\x20\x22\164\x79\x70\x65\x22\40\50\x70\x72\x69\x76\141\164\145\x2f\160\165\142\x6c\151\143\51\x20\x6d\165\163\x74\x20\142\145\40\x70\141\163\x73\x65\144\40\x76\x69\141\x20\x70\x61\162\141\155\145\x74\x65\x72\163");
            case self::HMAC_SHA1:
                $this->cryptParams["\x6c\x69\142\162\x61\162\171"] = $qI;
                $this->cryptParams["\155\x65\164\x68\x6f\144"] = "\150\164\x74\160\x3a\x2f\x2f\x77\167\x77\x2e\167\63\56\157\162\147\57\62\x30\x30\60\x2f\60\71\57\170\155\x6c\144\x73\x69\x67\x23\x68\155\141\x63\55\163\x68\x61\x31";
                goto sV;
            default:
                throw new Exception("\x49\156\x76\x61\154\x69\x64\40\x4b\x65\x79\x20\x54\171\x70\x65");
        }
        Ix:
        sV:
        $this->type = $qI;
    }
    public static function convertRSA($Of, $FP)
    {
        $IV = self::makeAsnSegment(0x2, $FP);
        $Vd = self::makeAsnSegment(0x2, $Of);
        $is = self::makeAsnSegment(0x30, $Vd . $IV);
        $ti = self::makeAsnSegment(0x3, $is);
        $TQ = pack("\x48\x2a", "\63\60\x30\x44\x30\66\60\71\x32\101\70\66\64\70\x38\66\106\67\60\x44\x30\61\x30\61\x30\x31\60\x35\60\60");
        $md = self::makeAsnSegment(0x30, $TQ . $ti);
        $NJ = base64_encode($md);
        $Yy = "\55\55\x2d\x2d\x2d\102\x45\x47\x49\x4e\x20\x50\125\102\114\111\103\x20\113\105\131\x2d\55\55\x2d\55\xa";
        $Bd = 0;
        Rq:
        if (!($ru = substr($NJ, $Bd, 64))) {
            goto mM;
        }
        $Yy = $Yy . $ru . "\12";
        $Bd += 64;
        goto Rq;
        mM:
        return $Yy . "\x2d\x2d\x2d\55\x2d\105\x4e\104\40\120\x55\102\x4c\111\103\40\113\105\x59\x2d\x2d\55\55\x2d\xa";
    }
    public static function makeAsnSegment($qI, $Ee)
    {
        switch ($qI) {
            case 0x2:
                if (!(ord($Ee) > 0x7f)) {
                    goto vW;
                }
                $Ee = chr(0) . $Ee;
                vW:
                goto JA;
            case 0x3:
                $Ee = chr(0) . $Ee;
                goto JA;
        }
        sW:
        JA:
        $E4 = strlen($Ee);
        if ($E4 < 128) {
            goto pP;
        }
        if ($E4 < 0x100) {
            goto Uy;
        }
        if ($E4 < 0x10000) {
            goto nu;
        }
        $NA = null;
        goto gS;
        nu:
        $NA = sprintf("\45\143\45\x63\45\x63\x25\143\x25\163", $qI, 0x82, $E4 / 0x100, $E4 % 0x100, $Ee);
        gS:
        goto kW;
        Uy:
        $NA = sprintf("\x25\143\x25\143\x25\x63\x25\163", $qI, 0x81, $E4, $Ee);
        kW:
        goto vX;
        pP:
        $NA = sprintf("\45\143\45\143\x25\x73", $qI, $E4, $Ee);
        vX:
        return $NA;
    }
    public static function fromEncryptedKeyElement(DOMElement $hL)
    {
        $To = new XMLSecEnc();
        $To->setNode($hL);
        if ($z3 = $To->locateKey()) {
            goto bV;
        }
        throw new Exception("\x55\x6e\x61\142\154\145\x20\x74\157\40\154\x6f\143\x61\164\145\x20\141\x6c\x67\157\162\151\x74\x68\x6d\40\146\x6f\x72\x20\x74\150\x69\x73\40\x45\156\x63\x72\171\x70\164\145\x64\40\x4b\145\x79");
        bV:
        $z3->isEncrypted = true;
        $z3->encryptedCtx = $To;
        XMLSecEnc::staticLocateKeyInfo($z3, $hL);
        return $z3;
    }
    public function getSymmetricKeySize()
    {
        if (isset($this->cryptParams["\153\x65\x79\163\x69\x7a\145"])) {
            goto tH;
        }
        return null;
        tH:
        return $this->cryptParams["\x6b\145\171\x73\151\172\145"];
    }
    public function generateSessionKey()
    {
        if (isset($this->cryptParams["\153\x65\171\x73\151\172\x65"])) {
            goto ZU;
        }
        throw new Exception("\125\x6e\x6b\156\157\x77\156\40\153\145\x79\x20\163\151\172\x65\40\146\x6f\162\40\164\171\x70\x65\x20\x22" . $this->type . "\x22\56");
        ZU:
        $YS = $this->cryptParams["\x6b\x65\171\x73\151\x7a\x65"];
        $On = openssl_random_pseudo_bytes($YS);
        if (!($this->type === self::TRIPLEDES_CBC)) {
            goto Pm;
        }
        $nO = 0;
        n1:
        if (!($nO < strlen($On))) {
            goto ui;
        }
        $Fe = ord($On[$nO]) & 0xfe;
        $bV = 1;
        $Ft = 1;
        s4:
        if (!($Ft < 8)) {
            goto Tt;
        }
        $bV ^= $Fe >> $Ft & 1;
        Cb:
        $Ft++;
        goto s4;
        Tt:
        $Fe |= $bV;
        $On[$nO] = chr($Fe);
        Fv:
        $nO++;
        goto n1;
        ui:
        Pm:
        $this->key = $On;
        return $On;
    }
    public function loadKey($On, $er = false, $li = false)
    {
        if ($er) {
            goto eB;
        }
        $this->key = $On;
        goto ZJ;
        eB:
        $this->key = file_get_contents($On);
        ZJ:
        if ($li) {
            goto jT;
        }
        $this->x509Certificate = null;
        goto ld;
        jT:
        $this->key = openssl_x509_read($this->key);
        openssl_x509_export($this->key, $dU);
        $this->x509Certificate = $dU;
        $this->key = $dU;
        ld:
        if (!($this->cryptParams["\x6c\151\142\162\141\x72\171"] == "\157\x70\145\x6e\163\163\x6c")) {
            goto mP;
        }
        switch ($this->cryptParams["\x74\171\160\x65"]) {
            case "\x70\165\x62\x6c\x69\x63":
                if (!$li) {
                    goto ud;
                }
                $this->X509Thumbprint = self::getRawThumbprint($this->key);
                ud:
                $this->key = openssl_get_publickey($this->key);
                if ($this->key) {
                    goto hG;
                }
                throw new Exception("\125\x6e\x61\142\x6c\145\x20\x74\x6f\x20\x65\170\164\x72\x61\x63\x74\40\160\165\x62\154\x69\143\x20\153\145\171");
                hG:
                goto SH;
            case "\160\x72\x69\166\x61\164\x65":
                $this->key = openssl_get_privatekey($this->key, $this->passphrase);
                goto SH;
            case "\x73\171\x6d\x6d\145\164\x72\151\143":
                if (!(strlen($this->key) < $this->cryptParams["\153\x65\171\x73\151\x7a\x65"])) {
                    goto ow;
                }
                throw new Exception("\x4b\145\171\x20\x6d\165\x73\164\40\x63\157\156\x74\141\151\156\40\x61\x74\x20\154\x65\x61\x73\164\x20" . $this->cryptParams["\153\145\171\x73\151\172\x65"] . "\40\x63\x68\x61\162\x61\143\164\145\162\163\x20\x66\x6f\x72\40\164\x68\151\x73\x20\x63\151\x70\x68\145\x72\x2c\40\143\x6f\156\164\141\151\x6e\x73\x20" . strlen($this->key));
                ow:
                goto SH;
            default:
                throw new Exception("\x55\156\153\x6e\157\167\156\40\x74\171\160\145");
        }
        mN:
        SH:
        mP:
    }
    public static function getRawThumbprint($d2)
    {
        $LA = explode("\xa", $d2);
        $or = '';
        $zJ = false;
        foreach ($LA as $pw) {
            if (!$zJ) {
                goto B8;
            }
            if (!(strncmp($pw, "\55\55\55\55\x2d\105\116\x44\x20\103\x45\x52\124\111\x46\111\103\101\x54\x45", 20) == 0)) {
                goto zJ;
            }
            goto Eb;
            zJ:
            $or .= trim($pw);
            goto wj;
            B8:
            if (!(strncmp($pw, "\x2d\55\55\x2d\x2d\x42\x45\x47\111\116\40\103\x45\x52\124\111\x46\x49\103\x41\124\105", 22) == 0)) {
                goto mq;
            }
            $zJ = true;
            mq:
            wj:
            dj:
        }
        Eb:
        if (empty($or)) {
            goto hb;
        }
        return strtolower(sha1(base64_decode($or)));
        hb:
        return null;
    }
    public function encryptData($or)
    {
        if (!($this->cryptParams["\154\x69\x62\162\x61\x72\x79"] === "\157\x70\145\x6e\163\163\154")) {
            goto sr;
        }
        switch ($this->cryptParams["\164\171\x70\x65"]) {
            case "\x73\171\x6d\x6d\x65\x74\x72\x69\x63":
                return $this->encryptSymmetric($or);
            case "\x70\165\x62\x6c\x69\x63":
                return $this->encryptPublic($or);
            case "\160\162\151\x76\x61\x74\145":
                return $this->encryptPrivate($or);
        }
        yo:
        ts:
        sr:
    }
    private function encryptSymmetric($or)
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cryptParams["\x63\151\160\x68\145\x72"]));
        $P_ = null;
        if (in_array($this->cryptParams["\143\x69\x70\x68\145\x72"], ["\x61\x65\x73\x2d\61\x32\x38\55\147\x63\x6d", "\141\145\x73\x2d\x31\71\x32\55\x67\x63\x6d", "\141\145\x73\55\x32\x35\66\55\147\143\x6d"])) {
            goto E8;
        }
        $or = $this->padISO10126($or, $this->cryptParams["\142\154\157\143\153\x73\151\x7a\x65"]);
        $o1 = openssl_encrypt($or, $this->cryptParams["\x63\x69\160\x68\145\x72"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        goto Za;
        E8:
        if (!(version_compare(PHP_VERSION, "\67\56\x31\56\x30") < 0)) {
            goto Un;
        }
        throw new Exception("\x50\110\120\40\x37\56\x31\56\60\40\151\163\40\162\145\161\165\151\162\x65\144\x20\164\x6f\x20\165\163\x65\40\101\x45\x53\40\x47\103\115\x20\x61\154\147\157\x72\x69\x74\x68\155\163");
        Un:
        $P_ = openssl_random_pseudo_bytes(self::AUTHTAG_LENGTH);
        $o1 = openssl_encrypt($or, $this->cryptParams["\x63\x69\x70\x68\x65\162"], $this->key, OPENSSL_RAW_DATA, $this->iv, $P_);
        Za:
        if (!(false === $o1)) {
            goto iK;
        }
        throw new Exception("\106\141\151\154\x75\x72\x65\40\x65\156\143\162\171\160\x74\151\x6e\x67\x20\104\x61\164\141\40\50\157\160\145\156\x73\163\154\40\163\x79\x6d\x6d\x65\164\162\x69\x63\x29\40\x2d\x20" . openssl_error_string());
        iK:
        return $this->iv . $o1 . $P_;
    }
    private function padISO10126($or, $wV)
    {
        if (!($wV > 256)) {
            goto U4;
        }
        throw new Exception("\102\154\157\143\153\40\x73\x69\172\145\40\150\151\147\150\x65\162\x20\164\x68\x61\156\x20\x32\x35\66\40\156\x6f\164\x20\141\154\x6c\157\x77\x65\144");
        U4:
        $ma = $wV - strlen($or) % $wV;
        $qs = chr($ma);
        return $or . str_repeat($qs, $ma);
    }
    private function encryptPublic($or)
    {
        if (openssl_public_encrypt($or, $o1, $this->key, $this->cryptParams["\x70\x61\x64\144\x69\156\x67"])) {
            goto mF;
        }
        throw new Exception("\x46\141\151\x6c\x75\x72\145\x20\x65\x6e\x63\x72\x79\160\x74\151\x6e\x67\x20\x44\x61\164\x61\x20\x28\x6f\160\145\156\x73\163\x6c\x20\160\x75\142\154\x69\143\51\40\55\x20" . openssl_error_string());
        mF:
        return $o1;
    }
    private function encryptPrivate($or)
    {
        if (openssl_private_encrypt($or, $o1, $this->key, $this->cryptParams["\160\x61\x64\x64\151\x6e\x67"])) {
            goto oc;
        }
        throw new Exception("\x46\x61\x69\x6c\165\x72\145\40\145\156\x63\162\x79\160\x74\151\156\x67\40\104\141\x74\141\x20\50\x6f\x70\145\156\x73\163\x6c\40\x70\x72\151\x76\x61\164\x65\x29\x20\x2d\x20" . openssl_error_string());
        oc:
        return $o1;
    }
    public function decryptData($or)
    {
        if (!($this->cryptParams["\154\151\142\x72\141\x72\x79"] === "\x6f\x70\145\x6e\x73\163\154")) {
            goto hw;
        }
        switch ($this->cryptParams["\164\x79\x70\145"]) {
            case "\x73\171\155\155\145\x74\162\151\143":
                return $this->decryptSymmetric($or);
            case "\160\x75\142\x6c\151\x63":
                return $this->decryptPublic($or);
            case "\160\x72\151\x76\141\x74\145":
                return $this->decryptPrivate($or);
        }
        Tv:
        g0:
        hw:
    }
    private function decryptSymmetric($or)
    {
        $F9 = openssl_cipher_iv_length($this->cryptParams["\x63\151\x70\x68\x65\162"]);
        $this->iv = substr($or, 0, $F9);
        $or = substr($or, $F9);
        $P_ = null;
        if (in_array($this->cryptParams["\143\151\x70\150\x65\162"], ["\x61\145\163\55\61\62\70\55\x67\x63\x6d", "\141\x65\163\x2d\x31\71\62\55\147\x63\x6d", "\141\x65\163\x2d\x32\x35\x36\55\x67\x63\x6d"])) {
            goto nj;
        }
        $jn = openssl_decrypt($or, $this->cryptParams["\x63\x69\x70\x68\x65\x72"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        goto zS;
        nj:
        if (!(version_compare(PHP_VERSION, "\x37\x2e\x31\x2e\x30") < 0)) {
            goto iD;
        }
        throw new Exception("\x50\x48\120\40\67\56\x31\x2e\60\x20\x69\163\40\162\145\161\x75\x69\x72\145\x64\40\x74\x6f\40\165\x73\x65\40\x41\105\x53\x20\107\103\x4d\40\x61\154\x67\157\162\151\164\x68\x6d\x73");
        iD:
        $Bd = 0 - self::AUTHTAG_LENGTH;
        $P_ = substr($or, $Bd);
        $or = substr($or, 0, $Bd);
        $jn = openssl_decrypt($or, $this->cryptParams["\x63\151\160\x68\x65\x72"], $this->key, OPENSSL_RAW_DATA, $this->iv, $P_);
        zS:
        if (!(false === $jn)) {
            goto JD;
        }
        throw new Exception("\x46\141\x69\154\x75\x72\x65\40\144\145\x63\x72\x79\160\164\x69\156\147\x20\x44\141\x74\141\x20\50\157\x70\145\x6e\163\x73\x6c\x20\163\x79\155\155\145\x74\x72\x69\x63\51\x20\55\x20" . openssl_error_string());
        JD:
        return null !== $P_ ? $jn : $this->unpadISO10126($jn);
    }
    private function unpadISO10126($or)
    {
        $ma = substr($or, -1);
        $gl = ord($ma);
        return substr($or, 0, -$gl);
    }
    private function decryptPublic($or)
    {
        if (openssl_public_decrypt($or, $jn, $this->key, $this->cryptParams["\x70\x61\x64\x64\151\156\147"])) {
            goto v1;
        }
        throw new Exception("\106\x61\x69\x6c\165\x72\145\40\144\x65\143\162\171\x70\164\151\x6e\x67\40\104\141\x74\141\40\50\157\x70\145\x6e\163\x73\154\x20\x70\x75\142\x6c\x69\143\x29\x20\x2d\40" . openssl_error_string());
        v1:
        return $jn;
    }
    private function decryptPrivate($or)
    {
        if (openssl_private_decrypt($or, $jn, $this->key, $this->cryptParams["\x70\141\x64\144\151\156\x67"])) {
            goto Wn;
        }
        throw new Exception("\106\141\x69\154\x75\x72\145\x20\x64\145\143\162\x79\x70\x74\151\x6e\147\x20\x44\141\x74\141\x20\50\x6f\160\145\x6e\163\163\154\x20\160\162\151\x76\141\x74\x65\51\40\55\40" . openssl_error_string());
        Wn:
        return $jn;
    }
    public function signData($or)
    {
        switch ($this->cryptParams["\154\151\x62\x72\x61\x72\171"]) {
            case "\157\160\x65\x6e\163\x73\x6c":
                return $this->signOpenSSL($or);
            case self::HMAC_SHA1:
                return hash_hmac("\163\x68\x61\x31", $or, $this->key, true);
        }
        NC:
        jo:
    }
    private function signOpenSSL($or)
    {
        $Vl = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\144\151\x67\145\163\x74"])) {
            goto z0;
        }
        $Vl = $this->cryptParams["\x64\151\147\145\163\164"];
        z0:
        if (openssl_sign($or, $wR, $this->key, $Vl)) {
            goto s2;
        }
        throw new Exception("\106\x61\x69\154\x75\162\x65\x20\x53\x69\x67\x6e\151\156\147\x20\104\x61\164\x61\x3a\40" . openssl_error_string() . "\40\55\x20" . $Vl);
        s2:
        return $wR;
    }
    public function verifySignature($or, $wR)
    {
        switch ($this->cryptParams["\x6c\151\142\162\x61\162\x79"]) {
            case "\x6f\160\145\156\163\163\x6c":
                return $this->verifyOpenSSL($or, $wR);
            case self::HMAC_SHA1:
                $rZ = hash_hmac("\x73\150\x61\61", $or, $this->key, true);
                return strcmp($wR, $rZ) == 0;
        }
        zw:
        G8:
    }
    private function verifyOpenSSL($or, $wR)
    {
        $Vl = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\144\151\147\145\x73\x74"])) {
            goto hp;
        }
        $Vl = $this->cryptParams["\x64\151\147\x65\x73\x74"];
        hp:
        return openssl_verify($or, $wR, $this->key, $Vl);
    }
    public function getAlgorith()
    {
        return $this->getAlgorithm();
    }
    public function getAlgorithm()
    {
        return $this->cryptParams["\155\145\164\x68\157\x64"];
    }
    public function serializeKey($iJ)
    {
    }
    public function getX509Certificate()
    {
        return $this->x509Certificate;
    }
    public function getX509Thumbprint()
    {
        return $this->X509Thumbprint;
    }
}
