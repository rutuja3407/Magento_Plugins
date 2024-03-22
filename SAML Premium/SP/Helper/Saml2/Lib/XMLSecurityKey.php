<?php


namespace MiniOrange\SP\Helper\Saml2\Lib;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use DOMElement;
class XMLSecurityKey
{
    const TRIPLEDES_CBC = "\x68\x74\x74\x70\x3a\x2f\x2f\167\167\x77\56\x77\x33\56\157\162\147\x2f\x32\x30\60\61\x2f\x30\64\x2f\170\x6d\154\x65\x6e\x63\x23\x74\x72\151\x70\154\145\144\x65\x73\55\143\142\x63";
    const AES128_CBC = "\x68\x74\x74\x70\x3a\57\57\167\167\167\x2e\x77\63\56\157\162\x67\x2f\x32\x30\x30\x31\x2f\60\64\57\x78\x6d\154\145\156\x63\x23\141\145\x73\x31\62\x38\x2d\x63\x62\143";
    const AES192_CBC = "\x68\164\164\x70\x3a\57\57\167\x77\167\56\167\x33\56\x6f\x72\147\57\x32\60\x30\x31\x2f\x30\64\x2f\x78\x6d\154\x65\156\x63\43\x61\x65\163\x31\x39\x32\x2d\x63\x62\143";
    const AES256_CBC = "\x68\x74\164\160\72\57\x2f\x77\x77\x77\x2e\x77\63\56\157\162\x67\x2f\x32\x30\60\x31\x2f\60\64\57\170\x6d\154\x65\156\143\43\x61\145\x73\x32\65\66\x2d\143\x62\x63";
    const AES128_GCM = "\150\x74\164\160\x3a\x2f\x2f\167\x77\167\x2e\167\x33\x2e\x6f\x72\147\57\x32\60\x30\71\57\170\x6d\x6c\145\156\x63\x31\61\x23\141\x65\x73\61\x32\70\55\x67\x63\155";
    const AES192_GCM = "\150\x74\x74\x70\x3a\57\x2f\167\167\x77\56\167\63\x2e\x6f\x72\x67\x2f\x32\x30\60\x39\57\x78\155\154\145\156\x63\x31\61\43\141\x65\x73\x31\x39\x32\55\147\x63\x6d";
    const AES256_GCM = "\x68\164\164\x70\72\x2f\57\167\167\167\56\167\63\56\157\162\147\57\62\60\60\x39\x2f\170\155\x6c\145\156\x63\x31\61\x23\141\x65\163\62\x35\x36\55\x67\143\155";
    const RSA_1_5 = "\x68\164\x74\160\x3a\x2f\57\167\x77\x77\56\167\63\x2e\157\162\147\x2f\62\60\60\61\57\60\64\x2f\170\x6d\x6c\145\x6e\143\x23\162\163\x61\55\61\x5f\65";
    const RSA_OAEP_MGF1P = "\x68\164\x74\160\x3a\x2f\x2f\x77\x77\167\56\167\x33\x2e\x6f\x72\x67\x2f\62\x30\x30\x31\57\60\64\x2f\x78\155\154\145\156\143\x23\x72\163\141\x2d\157\x61\x65\160\55\155\x67\x66\61\160";
    const RSA_OAEP = "\150\164\x74\x70\x3a\57\x2f\167\x77\167\56\167\63\56\x6f\x72\147\57\x32\x30\x30\71\x2f\170\155\154\x65\156\143\61\x31\x23\x72\163\x61\55\157\141\145\160";
    const DSA_SHA1 = "\150\x74\164\160\72\57\57\167\167\x77\x2e\x77\63\56\157\162\147\x2f\x32\60\60\x30\x2f\60\x39\57\170\155\154\x64\163\151\x67\x23\x64\163\141\55\x73\x68\x61\x31";
    const RSA_SHA1 = "\150\164\x74\160\72\x2f\57\167\x77\x77\x2e\167\x33\x2e\x6f\x72\147\57\x32\60\60\60\57\60\71\x2f\170\155\154\144\x73\x69\x67\43\162\163\x61\x2d\163\x68\141\x31";
    const RSA_SHA256 = "\x68\164\x74\160\x3a\57\57\167\167\x77\56\x77\63\56\x6f\162\x67\57\x32\x30\60\x31\x2f\x30\x34\x2f\170\155\x6c\x64\163\x69\147\x2d\155\x6f\162\x65\x23\x72\163\x61\55\x73\150\141\x32\x35\x36";
    const RSA_SHA384 = "\150\x74\x74\160\x3a\57\57\x77\167\x77\x2e\167\x33\56\157\162\147\57\62\60\x30\61\57\x30\x34\57\x78\155\x6c\x64\163\x69\x67\x2d\155\x6f\x72\145\43\x72\x73\x61\55\x73\x68\x61\x33\x38\64";
    const RSA_SHA512 = "\150\x74\164\x70\x3a\x2f\57\167\167\167\56\x77\63\56\157\x72\147\x2f\x32\x30\x30\61\57\60\64\57\x78\x6d\154\x64\x73\151\147\x2d\x6d\x6f\x72\145\x23\162\163\141\55\163\150\141\x35\x31\x32";
    const HMAC_SHA1 = "\150\164\x74\160\72\x2f\x2f\167\x77\167\x2e\x77\x33\56\x6f\162\x67\x2f\x32\x30\x30\60\x2f\x30\71\x2f\x78\155\154\x64\x73\x69\x67\x23\150\x6d\141\x63\x2d\163\150\141\x31";
    const AUTHTAG_LENGTH = 16;
    private $cryptParams = array();
    public $type = 0;
    public $key = null;
    public $passphrase = '';
    public $iv = null;
    public $name = null;
    public $keyChain = null;
    public $isEncrypted = false;
    public $encryptedCtx = null;
    public $guid = null;
    private $x509Certificate = null;
    private $X509Thumbprint = null;
    public function __construct($Nv, $As = null)
    {
        switch ($Nv) {
            case self::TRIPLEDES_CBC:
                $this->cryptParams["\154\x69\x62\162\x61\162\x79"] = "\x6f\x70\145\x6e\x73\x73\154";
                $this->cryptParams["\x63\151\160\150\145\x72"] = "\x64\145\x73\x2d\145\144\145\x33\55\x63\x62\x63";
                $this->cryptParams["\x74\x79\x70\x65"] = "\163\171\x6d\155\145\164\162\151\143";
                $this->cryptParams["\155\x65\164\x68\x6f\144"] = "\x68\164\164\160\72\x2f\57\167\x77\167\x2e\x77\x33\56\157\162\x67\57\62\x30\x30\x31\57\60\64\x2f\170\155\x6c\145\156\x63\x23\x74\x72\151\160\x6c\x65\x64\x65\163\x2d\x63\142\x63";
                $this->cryptParams["\153\x65\x79\x73\151\x7a\x65"] = 24;
                $this->cryptParams["\142\154\x6f\143\153\163\151\172\145"] = 8;
                goto kd;
            case self::AES128_CBC:
                $this->cryptParams["\154\151\142\162\141\162\x79"] = "\157\x70\145\156\x73\163\x6c";
                $this->cryptParams["\143\151\160\x68\145\162"] = "\141\x65\x73\55\x31\x32\x38\x2d\x63\x62\x63";
                $this->cryptParams["\x74\171\160\x65"] = "\x73\x79\x6d\x6d\x65\x74\162\151\x63";
                $this->cryptParams["\x6d\x65\x74\150\x6f\x64"] = "\150\164\x74\x70\x3a\x2f\57\167\167\x77\x2e\167\x33\56\157\162\x67\x2f\62\x30\60\61\x2f\x30\64\x2f\x78\155\154\145\156\x63\43\141\x65\x73\61\x32\70\55\143\x62\x63";
                $this->cryptParams["\153\x65\x79\163\151\x7a\x65"] = 16;
                $this->cryptParams["\x62\154\x6f\x63\153\163\x69\172\145"] = 16;
                goto kd;
            case self::AES192_CBC:
                $this->cryptParams["\x6c\151\142\x72\141\162\171"] = "\x6f\x70\x65\x6e\163\x73\154";
                $this->cryptParams["\143\151\160\150\145\x72"] = "\141\145\163\x2d\x31\x39\x32\x2d\x63\x62\x63";
                $this->cryptParams["\x74\171\160\145"] = "\163\x79\155\x6d\145\164\x72\151\x63";
                $this->cryptParams["\155\145\x74\150\157\x64"] = "\150\164\164\x70\72\57\57\x77\167\167\56\167\63\x2e\157\162\x67\57\x32\60\60\x31\x2f\x30\64\x2f\x78\155\154\x65\x6e\x63\x23\141\x65\163\61\x39\x32\x2d\143\142\143";
                $this->cryptParams["\153\x65\x79\x73\x69\172\x65"] = 24;
                $this->cryptParams["\142\154\x6f\x63\x6b\x73\151\x7a\145"] = 16;
                goto kd;
            case self::AES256_CBC:
                $this->cryptParams["\154\x69\x62\x72\141\x72\x79"] = "\x6f\x70\x65\156\163\x73\154";
                $this->cryptParams["\x63\151\160\150\x65\x72"] = "\x61\145\x73\55\x32\x35\x36\55\143\x62\x63";
                $this->cryptParams["\164\171\160\x65"] = "\163\x79\x6d\155\x65\x74\x72\151\x63";
                $this->cryptParams["\x6d\x65\164\150\x6f\144"] = "\x68\164\x74\x70\x3a\57\57\x77\x77\167\56\x77\63\x2e\x6f\162\147\57\x32\60\60\x31\x2f\x30\64\x2f\x78\155\x6c\x65\156\143\43\141\x65\163\62\x35\66\55\143\x62\143";
                $this->cryptParams["\x6b\145\x79\163\151\172\x65"] = 32;
                $this->cryptParams["\x62\154\157\143\x6b\x73\151\172\x65"] = 16;
                goto kd;
            case self::AES128_GCM:
                $this->cryptParams["\154\x69\142\x72\x61\162\171"] = "\157\160\145\x6e\163\x73\x6c";
                $this->cryptParams["\143\151\160\x68\145\162"] = "\141\x65\x73\x2d\61\x32\x38\55\147\x63\155";
                $this->cryptParams["\x74\x79\160\145"] = "\163\x79\155\x6d\145\x74\x72\151\143";
                $this->cryptParams["\x6d\145\164\x68\x6f\144"] = "\150\164\x74\x70\72\57\57\167\167\167\x2e\167\x33\x2e\x6f\x72\147\57\62\x30\x30\71\x2f\170\155\x6c\145\156\x63\61\61\43\141\145\x73\61\x32\x38\55\x67\143\x6d";
                $this->cryptParams["\153\145\171\x73\151\x7a\145"] = 16;
                $this->cryptParams["\x62\154\157\143\153\x73\x69\172\145"] = 16;
                goto kd;
            case self::AES192_GCM:
                $this->cryptParams["\x6c\151\142\162\x61\x72\x79"] = "\157\x70\145\156\163\x73\154";
                $this->cryptParams["\143\151\x70\x68\145\x72"] = "\x61\x65\163\x2d\61\x39\62\x2d\147\143\155";
                $this->cryptParams["\x74\x79\160\145"] = "\x73\x79\155\x6d\145\x74\162\151\x63";
                $this->cryptParams["\x6d\145\164\x68\x6f\144"] = "\150\164\164\x70\72\57\57\167\x77\x77\x2e\167\x33\56\157\162\147\57\62\x30\60\x39\x2f\170\155\x6c\x65\156\x63\x31\61\x23\141\145\163\x31\x39\62\x2d\x67\x63\x6d";
                $this->cryptParams["\x6b\x65\171\163\151\172\145"] = 24;
                $this->cryptParams["\x62\x6c\157\143\x6b\163\151\x7a\x65"] = 16;
                goto kd;
            case self::AES256_GCM:
                $this->cryptParams["\154\x69\142\x72\x61\162\171"] = "\157\x70\x65\156\163\163\x6c";
                $this->cryptParams["\x63\x69\160\150\x65\x72"] = "\141\x65\x73\x2d\x32\65\66\x2d\x67\143\x6d";
                $this->cryptParams["\164\x79\x70\145"] = "\163\171\x6d\x6d\145\164\x72\x69\143";
                $this->cryptParams["\x6d\145\164\150\157\x64"] = "\150\164\x74\160\72\57\57\x77\x77\x77\56\x77\63\56\157\162\x67\x2f\62\x30\60\71\57\170\155\x6c\x65\x6e\143\61\x31\x23\x61\145\163\x32\x35\66\x2d\x67\143\155";
                $this->cryptParams["\153\x65\171\163\x69\x7a\145"] = 32;
                $this->cryptParams["\x62\154\x6f\143\x6b\x73\151\172\x65"] = 16;
                goto kd;
            case self::RSA_1_5:
                $this->cryptParams["\x6c\151\142\162\141\162\x79"] = "\x6f\x70\145\x6e\163\163\154";
                $this->cryptParams["\160\141\x64\x64\x69\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\155\145\x74\x68\x6f\144"] = "\150\164\x74\160\x3a\57\57\167\x77\x77\x2e\x77\x33\56\157\162\147\57\62\60\x30\61\x2f\60\64\57\170\x6d\154\x65\x6e\143\43\x72\x73\141\55\x31\137\x35";
                if (!(is_array($As) && !empty($As["\164\x79\160\x65"]))) {
                    goto ow;
                }
                if (!($As["\x74\171\160\x65"] == "\x70\165\142\x6c\151\143" || $As["\164\x79\160\x65"] == "\160\162\151\166\x61\x74\145")) {
                    goto ur;
                }
                $this->cryptParams["\164\x79\x70\145"] = $As["\x74\x79\160\x65"];
                goto kd;
                ur:
                ow:
                throw new Exception("\103\x65\162\x74\x69\146\151\143\x61\164\145\x20\x22\164\171\160\145\x22\40\50\x70\162\151\166\141\164\x65\57\160\165\142\154\151\x63\51\x20\x6d\165\163\x74\x20\x62\x65\x20\160\x61\163\x73\145\144\x20\x76\x69\x61\x20\x70\x61\162\141\x6d\145\x74\x65\162\163");
            case self::RSA_OAEP_MGF1P:
                $this->cryptParams["\x6c\x69\x62\x72\141\162\x79"] = "\x6f\160\145\156\163\163\154";
                $this->cryptParams["\x70\141\144\144\151\156\x67"] = OPENSSL_PKCS1_OAEP_PADDING;
                $this->cryptParams["\155\x65\164\x68\157\x64"] = "\x68\164\x74\x70\x3a\x2f\57\x77\167\x77\x2e\167\63\x2e\157\162\147\57\x32\x30\60\x31\57\60\x34\57\x78\x6d\x6c\145\156\x63\x23\162\163\141\x2d\x6f\x61\145\160\x2d\x6d\147\x66\61\160";
                $this->cryptParams["\x68\x61\x73\x68"] = null;
                if (!(is_array($As) && !empty($As["\164\x79\x70\145"]))) {
                    goto ER;
                }
                if (!($As["\x74\x79\x70\145"] == "\x70\x75\142\154\151\x63" || $As["\x74\171\160\145"] == "\x70\x72\x69\166\141\x74\145")) {
                    goto yt;
                }
                $this->cryptParams["\x74\171\160\x65"] = $As["\x74\x79\x70\145"];
                goto kd;
                yt:
                ER:
                throw new Exception("\103\x65\162\164\x69\x66\x69\x63\x61\x74\x65\x20\42\164\x79\x70\145\x22\40\x28\x70\162\151\x76\141\164\x65\x2f\x70\x75\x62\x6c\x69\x63\x29\x20\155\x75\163\164\40\x62\145\x20\160\x61\163\163\x65\x64\x20\166\151\141\40\160\141\162\141\x6d\145\x74\x65\162\x73");
            case self::RSA_OAEP:
                $this->cryptParams["\154\151\142\162\141\x72\171"] = "\157\x70\145\156\163\x73\x6c";
                $this->cryptParams["\x70\141\144\144\x69\156\147"] = OPENSSL_PKCS1_OAEP_PADDING;
                $this->cryptParams["\155\145\164\x68\x6f\144"] = "\150\164\164\160\x3a\57\x2f\x77\167\167\56\167\63\x2e\157\162\x67\57\x32\60\x30\x39\57\170\155\x6c\145\156\x63\x31\x31\43\x72\x73\141\55\x6f\141\x65\160";
                $this->cryptParams["\150\141\163\150"] = "\150\x74\x74\x70\72\x2f\57\167\x77\x77\56\x77\63\x2e\x6f\162\147\x2f\62\x30\60\x39\x2f\170\x6d\154\145\x6e\143\x31\x31\x23\x6d\147\146\x31\163\x68\141\x31";
                if (!(is_array($As) && !empty($As["\x74\x79\160\145"]))) {
                    goto iL;
                }
                if (!($As["\x74\x79\160\145"] == "\160\x75\142\154\151\143" || $As["\164\171\x70\x65"] == "\160\162\151\166\x61\x74\x65")) {
                    goto FH;
                }
                $this->cryptParams["\164\x79\160\x65"] = $As["\164\171\160\145"];
                goto kd;
                FH:
                iL:
                throw new Exception("\x43\x65\162\164\x69\146\151\x63\x61\x74\145\40\42\164\x79\x70\x65\x22\40\50\x70\x72\151\166\141\x74\145\x2f\x70\165\x62\154\x69\143\x29\40\x6d\x75\163\x74\x20\x62\145\40\x70\141\x73\163\x65\x64\x20\166\x69\141\40\x70\141\162\x61\x6d\x65\164\x65\x72\x73");
            case self::RSA_SHA1:
                $this->cryptParams["\x6c\x69\x62\162\141\x72\171"] = "\x6f\x70\x65\x6e\163\x73\154";
                $this->cryptParams["\x6d\145\164\150\157\144"] = "\150\x74\164\x70\x3a\57\x2f\167\167\167\x2e\x77\63\x2e\157\162\147\57\x32\x30\x30\60\x2f\x30\x39\x2f\170\155\154\144\x73\151\x67\43\x72\x73\141\x2d\163\x68\x61\61";
                $this->cryptParams["\160\x61\x64\144\151\156\x67"] = OPENSSL_PKCS1_PADDING;
                if (!(is_array($As) && !empty($As["\164\x79\x70\145"]))) {
                    goto Mr;
                }
                if (!($As["\164\x79\160\145"] == "\x70\x75\x62\154\151\x63" || $As["\164\x79\160\x65"] == "\160\162\x69\166\x61\164\145")) {
                    goto HP;
                }
                $this->cryptParams["\164\171\160\x65"] = $As["\164\x79\160\x65"];
                goto kd;
                HP:
                Mr:
                throw new Exception("\x43\x65\162\x74\151\146\151\143\141\x74\145\40\42\164\171\x70\x65\42\40\x28\x70\x72\151\166\x61\x74\x65\57\x70\165\x62\154\151\x63\x29\x20\155\165\163\164\x20\x62\x65\x20\160\141\x73\163\x65\x64\40\166\151\141\x20\160\x61\162\141\x6d\x65\x74\145\162\163");
            case self::RSA_SHA256:
                $this->cryptParams["\154\151\x62\162\141\162\171"] = "\x6f\x70\x65\x6e\163\x73\154";
                $this->cryptParams["\155\145\x74\150\x6f\x64"] = "\150\164\164\x70\72\57\57\167\x77\167\x2e\x77\x33\x2e\157\162\x67\57\62\x30\60\61\57\x30\x34\57\170\x6d\154\x64\x73\151\147\x2d\x6d\x6f\162\x65\43\162\x73\141\x2d\x73\x68\141\62\65\x36";
                $this->cryptParams["\160\x61\144\x64\x69\x6e\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\144\151\x67\x65\163\164"] = "\x53\x48\101\x32\65\66";
                if (!(is_array($As) && !empty($As["\164\x79\x70\x65"]))) {
                    goto JJ;
                }
                if (!($As["\164\x79\160\x65"] == "\x70\165\x62\x6c\151\x63" || $As["\164\x79\160\145"] == "\x70\x72\x69\166\x61\x74\x65")) {
                    goto vV;
                }
                $this->cryptParams["\x74\171\160\145"] = $As["\164\x79\x70\145"];
                goto kd;
                vV:
                JJ:
                throw new Exception("\103\145\x72\164\x69\146\x69\x63\141\x74\x65\40\42\x74\x79\160\145\42\x20\50\160\x72\x69\x76\141\x74\145\57\x70\x75\142\154\x69\x63\51\40\155\165\x73\x74\40\142\x65\x20\160\141\163\163\145\x64\40\x76\151\141\x20\x70\x61\x72\141\155\x65\164\145\x72\163");
            case self::RSA_SHA384:
                $this->cryptParams["\x6c\151\x62\162\141\x72\171"] = "\x6f\x70\145\x6e\163\x73\x6c";
                $this->cryptParams["\155\x65\164\150\157\144"] = "\x68\x74\164\x70\x3a\57\x2f\x77\x77\167\56\x77\63\x2e\157\x72\147\x2f\62\60\x30\x31\x2f\60\64\57\170\x6d\x6c\x64\163\151\x67\55\155\157\x72\x65\43\x72\x73\141\55\x73\150\141\63\x38\64";
                $this->cryptParams["\160\x61\x64\x64\151\x6e\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\144\151\x67\x65\163\x74"] = "\x53\110\101\x33\x38\64";
                if (!(is_array($As) && !empty($As["\x74\171\x70\x65"]))) {
                    goto ir;
                }
                if (!($As["\164\171\160\x65"] == "\x70\x75\142\154\151\x63" || $As["\x74\x79\x70\145"] == "\160\162\151\x76\x61\164\x65")) {
                    goto k8;
                }
                $this->cryptParams["\164\x79\x70\145"] = $As["\x74\x79\x70\145"];
                goto kd;
                k8:
                ir:
                throw new Exception("\103\145\x72\164\151\146\x69\x63\141\164\x65\40\x22\x74\171\x70\x65\42\40\x28\160\x72\151\166\x61\164\145\x2f\x70\165\142\x6c\151\143\x29\40\155\165\x73\164\x20\x62\145\x20\x70\141\163\x73\145\144\40\166\x69\x61\x20\160\141\x72\x61\155\145\x74\x65\x72\x73");
            case self::RSA_SHA512:
                $this->cryptParams["\154\151\x62\x72\x61\162\171"] = "\x6f\x70\x65\x6e\x73\163\x6c";
                $this->cryptParams["\x6d\x65\x74\x68\x6f\x64"] = "\x68\x74\x74\x70\x3a\57\x2f\167\x77\167\56\167\63\56\157\x72\147\57\x32\x30\x30\x31\57\60\64\57\x78\155\x6c\x64\x73\151\x67\55\x6d\157\162\x65\43\162\x73\141\x2d\x73\150\x61\65\x31\x32";
                $this->cryptParams["\x70\x61\x64\144\x69\x6e\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\x69\147\145\163\x74"] = "\123\110\x41\x35\x31\x32";
                if (!(is_array($As) && !empty($As["\164\171\160\x65"]))) {
                    goto AV;
                }
                if (!($As["\x74\x79\160\x65"] == "\x70\165\x62\x6c\151\143" || $As["\164\171\x70\x65"] == "\x70\x72\151\166\x61\164\145")) {
                    goto OF;
                }
                $this->cryptParams["\x74\x79\x70\145"] = $As["\x74\x79\160\145"];
                goto kd;
                OF:
                AV:
                throw new Exception("\x43\145\162\164\151\146\x69\x63\141\x74\x65\40\42\x74\171\x70\145\42\x20\50\160\162\151\166\141\164\145\x2f\160\165\142\154\151\x63\x29\40\155\165\x73\x74\x20\142\x65\40\160\x61\x73\x73\x65\144\x20\x76\151\141\x20\x70\141\x72\141\x6d\x65\x74\x65\x72\163");
            case self::HMAC_SHA1:
                $this->cryptParams["\x6c\151\142\162\x61\x72\171"] = $Nv;
                $this->cryptParams["\155\x65\164\x68\x6f\144"] = "\x68\x74\x74\x70\72\x2f\57\x77\167\167\56\x77\63\x2e\x6f\x72\x67\57\62\x30\60\60\x2f\60\x39\x2f\170\x6d\x6c\x64\163\151\x67\x23\x68\155\141\x63\x2d\163\150\x61\x31";
                goto kd;
            default:
                throw new Exception("\111\156\x76\141\154\151\x64\x20\x4b\x65\171\40\x54\171\160\145");
        }
        Tr:
        kd:
        $this->type = $Nv;
    }
    public function getSymmetricKeySize()
    {
        if (isset($this->cryptParams["\153\145\171\163\x69\x7a\x65"])) {
            goto Ws;
        }
        return null;
        Ws:
        return $this->cryptParams["\153\x65\x79\x73\x69\x7a\x65"];
    }
    public function generateSessionKey()
    {
        if (isset($this->cryptParams["\153\145\171\x73\151\x7a\x65"])) {
            goto Yu6;
        }
        throw new Exception("\x55\x6e\153\156\x6f\x77\x6e\40\153\x65\x79\x20\x73\151\x7a\x65\x20\146\157\x72\x20\x74\171\160\145\x20\42" . $this->type . "\42\x2e");
        Yu6:
        $in = $this->cryptParams["\x6b\145\171\x73\x69\x7a\x65"];
        $zg = openssl_random_pseudo_bytes($in);
        if (!($this->type === self::TRIPLEDES_CBC)) {
            goto ZWv;
        }
        $X5 = 0;
        BXr:
        if (!($X5 < strlen($zg))) {
            goto AUA;
        }
        $eq = ord($zg[$X5]) & 0xfe;
        $ww = 1;
        $fU = 1;
        lF1:
        if (!($fU < 8)) {
            goto pwc;
        }
        $ww ^= $eq >> $fU & 1;
        u4A:
        $fU++;
        goto lF1;
        pwc:
        $eq |= $ww;
        $zg[$X5] = chr($eq);
        wMJ:
        $X5++;
        goto BXr;
        AUA:
        ZWv:
        $this->key = $zg;
        return $zg;
    }
    public static function getRawThumbprint($hj)
    {
        $Eu = explode("\xa", $hj);
        $F2 = '';
        $S3 = false;
        foreach ($Eu as $gu) {
            if (!$S3) {
                goto LsN;
            }
            if (!(strncmp($gu, "\x2d\x2d\x2d\x2d\x2d\x45\x4e\x44\40\x43\x45\x52\124\111\x46\111\x43\101\124\x45", 20) == 0)) {
                goto NG1;
            }
            goto Qp0;
            NG1:
            $F2 .= trim($gu);
            goto Thl;
            LsN:
            if (!(strncmp($gu, "\x2d\55\x2d\55\55\x42\105\x47\x49\x4e\40\x43\105\122\124\x49\106\x49\x43\x41\124\105", 22) == 0)) {
                goto Fod;
            }
            $S3 = true;
            Fod:
            Thl:
            nya:
        }
        Qp0:
        if (empty($F2)) {
            goto MTo;
        }
        return strtolower(sha1(base64_decode($F2)));
        MTo:
        return null;
    }
    public function loadKey($zg, $EM = false, $sD = false)
    {
        if ($EM) {
            goto dMW;
        }
        $this->key = $zg;
        goto ett;
        dMW:
        $this->key = file_get_contents($zg);
        ett:
        if ($sD) {
            goto FGL;
        }
        $this->x509Certificate = null;
        goto NxX;
        FGL:
        $this->key = openssl_x509_read($this->key);
        openssl_x509_export($this->key, $o0);
        $this->x509Certificate = $o0;
        $this->key = $o0;
        NxX:
        if (!($this->cryptParams["\154\x69\x62\162\x61\x72\171"] == "\157\x70\x65\156\163\x73\154")) {
            goto eF2;
        }
        switch ($this->cryptParams["\x74\171\160\145"]) {
            case "\x70\x75\142\154\x69\143":
                if (!$sD) {
                    goto pXs;
                }
                $this->X509Thumbprint = self::getRawThumbprint($this->key);
                pXs:
                $this->key = openssl_get_publickey($this->key);
                if ($this->key) {
                    goto T4w;
                }
                throw new Exception("\x55\156\x61\142\154\145\x20\164\157\40\145\170\x74\x72\x61\x63\x74\40\x70\x75\142\154\x69\x63\40\153\145\x79");
                T4w:
                goto PZg;
            case "\x70\x72\x69\x76\141\x74\x65":
                $this->key = openssl_get_privatekey($this->key, $this->passphrase);
                goto PZg;
            case "\x73\x79\155\x6d\145\164\x72\151\143":
                if (!(strlen($this->key) < $this->cryptParams["\153\145\171\163\151\x7a\x65"])) {
                    goto pfb;
                }
                throw new Exception("\x4b\x65\x79\40\x6d\x75\x73\x74\x20\x63\157\x6e\x74\x61\151\x6e\x20\141\164\40\x6c\145\141\x73\164\40" . $this->cryptParams["\153\x65\171\x73\x69\172\145"] . "\40\x63\x68\141\162\x61\143\164\x65\162\x73\x20\146\157\162\40\164\x68\x69\x73\40\x63\x69\x70\x68\x65\162\54\x20\143\157\x6e\164\x61\x69\x6e\163\x20" . strlen($this->key));
                pfb:
                goto PZg;
            default:
                throw new Exception("\125\156\x6b\156\157\167\x6e\x20\164\171\x70\x65");
        }
        yQ1:
        PZg:
        eF2:
    }
    private function padISO10126($F2, $eG)
    {
        if (!($eG > 256)) {
            goto DiA;
        }
        throw new Exception("\102\154\x6f\x63\x6b\40\x73\x69\x7a\145\40\x68\x69\147\150\145\x72\x20\164\x68\141\156\40\62\65\66\x20\156\157\x74\x20\x61\154\154\157\x77\145\x64");
        DiA:
        $JA = $eG - strlen($F2) % $eG;
        $RA = chr($JA);
        return $F2 . str_repeat($RA, $JA);
    }
    private function unpadISO10126($F2)
    {
        $JA = substr($F2, -1);
        $iv = ord($JA);
        return substr($F2, 0, -$iv);
    }
    private function encryptSymmetric($F2)
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cryptParams["\143\151\160\x68\145\162"]));
        $lr = null;
        if (in_array($this->cryptParams["\x63\151\x70\x68\x65\162"], ["\141\x65\163\55\x31\62\x38\x2d\147\x63\155", "\141\x65\163\55\x31\x39\x32\55\x67\x63\x6d", "\141\145\x73\x2d\62\x35\66\55\147\x63\x6d"])) {
            goto Xld;
        }
        $F2 = $this->padISO10126($F2, $this->cryptParams["\x62\154\x6f\x63\x6b\x73\x69\x7a\145"]);
        $FF = openssl_encrypt($F2, $this->cryptParams["\x63\151\x70\x68\145\x72"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        goto fyu;
        Xld:
        if (!(version_compare(PHP_VERSION, "\x37\x2e\x31\x2e\x30") < 0)) {
            goto M0N;
        }
        throw new Exception("\x50\x48\x50\40\x37\56\61\56\60\x20\151\x73\x20\x72\145\161\x75\151\x72\x65\x64\x20\x74\157\x20\x75\163\145\40\101\105\123\x20\107\x43\x4d\40\141\x6c\x67\x6f\x72\151\164\x68\x6d\x73");
        M0N:
        $lr = openssl_random_pseudo_bytes(self::AUTHTAG_LENGTH);
        $FF = openssl_encrypt($F2, $this->cryptParams["\143\x69\160\x68\145\x72"], $this->key, OPENSSL_RAW_DATA, $this->iv, $lr);
        fyu:
        if (!(false === $FF)) {
            goto F6C;
        }
        throw new Exception("\106\x61\151\154\165\162\x65\x20\x65\156\143\162\171\x70\x74\x69\x6e\147\x20\x44\x61\164\141\x20\x28\x6f\x70\145\156\x73\163\x6c\x20\163\171\155\155\x65\x74\162\x69\x63\51\40\x2d\40" . openssl_error_string());
        F6C:
        return $this->iv . $FF . $lr;
    }
    private function decryptSymmetric($F2)
    {
        $xX = openssl_cipher_iv_length($this->cryptParams["\x63\151\160\150\145\x72"]);
        $this->iv = substr($F2, 0, $xX);
        $F2 = substr($F2, $xX);
        $lr = null;
        if (in_array($this->cryptParams["\x63\151\160\150\x65\162"], ["\141\x65\x73\x2d\61\x32\x38\55\x67\x63\155", "\141\x65\x73\55\x31\x39\x32\x2d\147\143\155", "\141\x65\x73\x2d\x32\x35\66\x2d\147\143\x6d"])) {
            goto rHZ;
        }
        $G8 = openssl_decrypt($F2, $this->cryptParams["\x63\x69\160\x68\145\162"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        goto UIc;
        rHZ:
        if (!(version_compare(PHP_VERSION, "\67\56\x31\x2e\60") < 0)) {
            goto Ivi;
        }
        throw new Exception("\120\x48\120\40\x37\x2e\x31\x2e\x30\x20\151\163\x20\x72\145\161\x75\151\162\145\x64\40\x74\x6f\x20\x75\163\145\x20\101\x45\123\x20\107\x43\115\x20\141\x6c\x67\157\x72\x69\x74\150\x6d\163");
        Ivi:
        $Jx = 0 - self::AUTHTAG_LENGTH;
        $lr = substr($F2, $Jx);
        $F2 = substr($F2, 0, $Jx);
        $G8 = openssl_decrypt($F2, $this->cryptParams["\143\151\x70\x68\145\x72"], $this->key, OPENSSL_RAW_DATA, $this->iv, $lr);
        UIc:
        if (!(false === $G8)) {
            goto rhQ;
        }
        throw new Exception("\106\x61\151\154\165\162\145\40\144\145\x63\162\171\160\x74\151\x6e\x67\x20\x44\x61\164\141\x20\x28\157\x70\x65\156\163\163\154\40\163\x79\x6d\155\x65\164\x72\151\143\x29\x20\55\x20" . openssl_error_string());
        rhQ:
        return null !== $lr ? $G8 : $this->unpadISO10126($G8);
    }
    private function encryptPublic($F2)
    {
        if (openssl_public_encrypt($F2, $FF, $this->key, $this->cryptParams["\160\x61\x64\144\x69\x6e\147"])) {
            goto tfE;
        }
        throw new Exception("\x46\141\x69\x6c\x75\x72\145\40\145\x6e\143\x72\x79\x70\164\151\x6e\147\40\x44\141\164\141\x20\x28\x6f\x70\145\156\163\x73\x6c\40\x70\x75\x62\x6c\151\143\51\40\55\40" . openssl_error_string());
        tfE:
        return $FF;
    }
    private function decryptPublic($F2)
    {
        if (openssl_public_decrypt($F2, $G8, $this->key, $this->cryptParams["\x70\x61\144\144\151\156\x67"])) {
            goto hNh;
        }
        throw new Exception("\x46\141\x69\x6c\x75\162\x65\x20\x64\x65\143\x72\171\x70\164\x69\156\x67\x20\x44\x61\x74\141\40\50\x6f\x70\145\156\x73\x73\154\40\x70\165\x62\x6c\x69\x63\51\x20\55\40" . openssl_error_string());
        hNh:
        return $G8;
    }
    private function encryptPrivate($F2)
    {
        if (openssl_private_encrypt($F2, $FF, $this->key, $this->cryptParams["\x70\141\144\x64\x69\x6e\x67"])) {
            goto beu;
        }
        throw new Exception("\106\141\151\x6c\165\162\145\40\x65\x6e\143\162\171\160\164\151\x6e\147\40\104\141\164\x61\40\x28\x6f\x70\x65\x6e\163\x73\154\40\160\x72\x69\x76\141\x74\x65\51\40\x2d\40" . openssl_error_string());
        beu:
        return $FF;
    }
    private function decryptPrivate($F2)
    {
        if (openssl_private_decrypt($F2, $G8, $this->key, $this->cryptParams["\160\x61\144\144\x69\156\x67"])) {
            goto XsW;
        }
        throw new Exception("\106\141\x69\154\165\x72\145\40\x64\145\x63\162\171\160\x74\x69\156\x67\x20\104\x61\x74\x61\40\50\157\x70\145\x6e\x73\x73\x6c\40\x70\x72\x69\166\141\164\x65\x29\40\x2d\x20" . openssl_error_string());
        XsW:
        return $G8;
    }
    private function signOpenSSL($F2)
    {
        $s4 = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\144\x69\147\145\x73\164"])) {
            goto vIo;
        }
        $s4 = $this->cryptParams["\x64\x69\x67\x65\x73\164"];
        vIo:
        if (openssl_sign($F2, $Ne, $this->key, $s4)) {
            goto ZMO;
        }
        throw new Exception("\106\x61\x69\154\165\162\145\x20\x53\x69\x67\x6e\151\156\147\40\104\141\164\x61\x3a\40" . openssl_error_string() . "\40\x2d\x20" . $s4);
        ZMO:
        return $Ne;
    }
    private function verifyOpenSSL($F2, $Ne)
    {
        $s4 = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\144\151\x67\145\163\164"])) {
            goto QX6;
        }
        $s4 = $this->cryptParams["\144\151\x67\x65\x73\164"];
        QX6:
        return openssl_verify($F2, $Ne, $this->key, $s4);
    }
    public function encryptData($F2)
    {
        if (!($this->cryptParams["\x6c\151\x62\162\141\162\x79"] === "\157\x70\145\x6e\163\x73\154")) {
            goto TDu;
        }
        switch ($this->cryptParams["\164\171\x70\145"]) {
            case "\163\171\155\x6d\145\x74\x72\151\143":
                return $this->encryptSymmetric($F2);
            case "\x70\165\142\154\151\143":
                return $this->encryptPublic($F2);
            case "\x70\162\x69\x76\x61\x74\145":
                return $this->encryptPrivate($F2);
        }
        g6j:
        CkK:
        TDu:
    }
    public function decryptData($F2)
    {
        if (!($this->cryptParams["\x6c\151\142\162\x61\x72\x79"] === "\157\160\145\156\x73\163\x6c")) {
            goto ytQ;
        }
        switch ($this->cryptParams["\x74\x79\160\145"]) {
            case "\x73\171\x6d\x6d\x65\164\162\151\143":
                return $this->decryptSymmetric($F2);
            case "\x70\x75\x62\x6c\151\x63":
                return $this->decryptPublic($F2);
            case "\x70\x72\151\166\x61\x74\145":
                return $this->decryptPrivate($F2);
        }
        Cb6:
        xmo:
        ytQ:
    }
    public function signData($F2)
    {
        switch ($this->cryptParams["\x6c\x69\x62\x72\x61\162\x79"]) {
            case "\157\160\x65\156\x73\163\x6c":
                return $this->signOpenSSL($F2);
            case self::HMAC_SHA1:
                return hash_hmac("\x73\x68\141\x31", $F2, $this->key, true);
        }
        cRX:
        n9O:
    }
    public function verifySignature($F2, $Ne)
    {
        switch ($this->cryptParams["\154\151\x62\162\141\x72\171"]) {
            case "\157\x70\145\156\163\x73\154":
                return $this->verifyOpenSSL($F2, $Ne);
            case self::HMAC_SHA1:
                $z1 = hash_hmac("\x73\x68\x61\x31", $F2, $this->key, true);
                return strcmp($Ne, $z1) == 0;
        }
        d75:
        Wwe:
    }
    public function getAlgorith()
    {
        return $this->getAlgorithm();
    }
    public function getAlgorithm()
    {
        return $this->cryptParams["\155\145\164\x68\157\x64"];
    }
    public static function makeAsnSegment($Nv, $WV)
    {
        switch ($Nv) {
            case 0x2:
                if (!(ord($WV) > 0x7f)) {
                    goto b2U;
                }
                $WV = chr(0) . $WV;
                b2U:
                goto jV4;
            case 0x3:
                $WV = chr(0) . $WV;
                goto jV4;
        }
        zoR:
        jV4:
        $vS = strlen($WV);
        if ($vS < 128) {
            goto fdq;
        }
        if ($vS < 0x100) {
            goto B3b;
        }
        if ($vS < 0x10000) {
            goto Pps;
        }
        $vR = null;
        goto tsy;
        Pps:
        $vR = sprintf("\x25\143\45\x63\x25\143\45\143\x25\x73", $Nv, 0x82, $vS / 0x100, $vS % 0x100, $WV);
        tsy:
        goto IV2;
        B3b:
        $vR = sprintf("\45\143\x25\x63\45\x63\45\163", $Nv, 0x81, $vS, $WV);
        IV2:
        goto Io2;
        fdq:
        $vR = sprintf("\45\143\45\143\x25\163", $Nv, $vS, $WV);
        Io2:
        return $vR;
    }
    public static function convertRSA($p4, $Gk)
    {
        $Uj = self::makeAsnSegment(0x2, $Gk);
        $MY = self::makeAsnSegment(0x2, $p4);
        $Rn = self::makeAsnSegment(0x30, $MY . $Uj);
        $ws = self::makeAsnSegment(0x3, $Rn);
        $p7 = pack("\110\52", "\x33\x30\60\104\60\66\60\71\62\x41\70\x36\64\x38\70\x36\x46\x37\60\x44\60\x31\60\x31\x30\61\x30\x35\x30\x30");
        $v6 = self::makeAsnSegment(0x30, $p7 . $ws);
        $g2 = base64_encode($v6);
        $sp = "\55\x2d\55\x2d\x2d\102\105\107\x49\x4e\x20\x50\125\x42\x4c\111\103\40\x4b\x45\131\x2d\x2d\55\55\55\12";
        $Jx = 0;
        nSX:
        if (!($ok = substr($g2, $Jx, 64))) {
            goto D1q;
        }
        $sp = $sp . $ok . "\12";
        $Jx += 64;
        goto nSX;
        D1q:
        return $sp . "\55\55\x2d\x2d\55\105\116\x44\40\x50\125\x42\114\111\x43\40\113\105\131\55\x2d\55\55\x2d\12";
    }
    public function serializeKey($US)
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
    public static function fromEncryptedKeyElement(DOMElement $rM)
    {
        $Cb = new XMLSecEnc();
        $Cb->setNode($rM);
        if ($Tb = $Cb->locateKey()) {
            goto P75;
        }
        throw new Exception("\x55\x6e\141\x62\154\x65\40\x74\157\x20\x6c\x6f\x63\x61\x74\x65\40\x61\x6c\147\157\x72\x69\164\x68\x6d\40\146\157\162\x20\164\150\151\163\x20\x45\x6e\143\x72\x79\160\x74\145\x64\x20\x4b\x65\171");
        P75:
        $Tb->isEncrypted = true;
        $Tb->encryptedCtx = $Cb;
        XMLSecEnc::staticLocateKeyInfo($Tb, $rM);
        return $Tb;
    }
}
