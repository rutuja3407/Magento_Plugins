<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\Exception\InvalidRequestInstantException;
use MiniOrange\SP\Helper\Exception\InvalidRequestVersionException;
use MiniOrange\SP\Helper\Exception\MissingIssuerValueException;
class AuthnRequest
{
    private $requestType = SPConstants::AUTHN_REQUEST;
    private $acsUrl;
    private $issuer;
    private $ssoUrl;
    private $forceAuthn;
    private $bindingType;
    private $destination;
    public function __construct($R6, $wz, $Iy, $cI, $CR)
    {
        $this->acsUrl = $R6;
        $this->issuer = $wz;
        $this->forceAuthn = $cI;
        $this->destination = $Iy;
        $this->bindingType = $CR;
    }
    private function generateXML()
    {
        $Yb = "\74\77\x78\155\154\40\x76\145\162\163\151\157\x6e\x3d\42\61\56\60\42\x20\x65\156\x63\157\x64\x69\156\x67\x3d\x22\125\x54\106\x2d\x38\x22\x3f\x3e" . "\x20\x3c\x73\x61\155\154\x70\72\x41\x75\164\x68\x6e\x52\145\161\x75\x65\x73\x74\x20\xd\xa\x20\x20\x20\40\40\40\x20\x20\40\x20\x20\40\x20\x20\x20\40\x20\x20\x20\40\40\40\40\x20\x20\x20\40\40\x20\x20\40\x20\x78\x6d\x6c\156\x73\x3a\163\x61\x6d\154\160\75\x22\x75\x72\156\72\x6f\x61\x73\151\163\x3a\156\141\x6d\145\x73\72\x74\x63\72\123\101\115\x4c\72\x32\56\x30\72\160\x72\157\164\x6f\x63\x6f\154\x22\40\15\12\40\x20\40\40\x20\40\x20\x20\x20\x20\x20\x20\40\40\40\x20\40\40\x20\x20\40\40\40\x20\x20\40\x20\x20\x20\x20\x20\x20\170\155\154\156\163\x3d\x22\165\x72\156\x3a\157\141\163\151\163\x3a\156\141\x6d\145\x73\x3a\x74\143\72\x53\x41\x4d\x4c\x3a\62\56\60\72\141\163\163\145\x72\x74\151\157\x6e\42\40\111\104\x3d\42" . SAML2Utilities::generateID() . "\42\40\x20\x56\x65\x72\163\151\157\156\x3d\42\62\56\60\x22\40\x49\163\163\x75\145\x49\156\x73\164\141\156\x74\x3d\42" . SAML2Utilities::generateTimestamp() . "\x22";
        if (!($this->forceAuthn == 1)) {
            goto lO;
        }
        lO:
        $Yb .= "\40\40\x20\x20\40\120\x72\x6f\x74\157\x63\x6f\154\x42\151\156\144\x69\x6e\x67\x3d\42\165\x72\156\x3a\x6f\141\163\151\x73\72\x6e\x61\x6d\x65\163\x3a\164\x63\x3a\123\101\x4d\x4c\72\x32\56\x30\72\142\x69\156\x64\x69\156\147\163\x3a\110\x54\x54\x50\55\120\117\123\x54\x22\x20\101\163\x73\x65\162\x74\151\x6f\x6e\x43\x6f\156\163\x75\x6d\145\162\123\145\x72\x76\151\143\x65\x55\x52\114\75\42" . $this->acsUrl . "\x22\40\40\x20\40\x20\40\104\145\163\164\151\x6e\141\164\x69\x6f\156\x3d\x22" . $this->destination . "\x22\76\15\xa\x20\40\40\x20\40\40\x20\x20\40\40\40\x20\40\40\x20\x20\40\x20\40\x20\x20\40\40\x20\40\x20\x20\x20\40\40\40\40\x3c\x73\x61\x6d\x6c\72\x49\163\163\165\x65\162\40\170\155\x6c\156\163\x3a\x73\x61\x6d\154\75\42\x75\162\x6e\x3a\x6f\x61\163\x69\163\x3a\x6e\x61\x6d\145\x73\72\x74\143\x3a\123\101\x4d\114\x3a\62\x2e\60\x3a\141\x73\x73\145\162\164\151\157\x6e\x22\76" . $this->issuer . "\74\x2f\x73\x61\x6d\x6c\x3a\111\163\163\165\x65\x72\x3e\xd\xa\40\40\40\40\x20\40\x20\x20\40\40\x20\x20\40\40\40\x20\x20\40\x20\x20\x20\40\x20\x20\40\x20\x20\40\40\x20\40\x20\x3c\163\x61\x6d\154\x70\72\116\141\155\x65\x49\104\x50\x6f\x6c\151\143\x79\x20\101\154\154\x6f\x77\103\162\145\x61\x74\145\75\x22\164\x72\165\145\x22\40\x46\157\162\x6d\141\x74\x3d\42\165\162\156\72\x6f\x61\x73\151\163\72\x6e\x61\155\x65\x73\72\164\143\72\x53\x41\x4d\114\x3a\x31\x2e\x31\72\x6e\x61\x6d\145\x69\144\55\146\x6f\x72\155\x61\164\x3a\x75\x6e\163\x70\145\x63\151\x66\151\145\x64\x22\x2f\76\xd\12\x20\x20\x20\x20\40\x20\40\40\40\x20\x20\x20\40\40\40\40\x20\x20\40\x20\40\40\40\x20\x20\x20\x20\x20\74\57\163\x61\155\x6c\160\72\x41\165\x74\x68\x6e\x52\145\161\x75\x65\163\164\76";
        return $Yb;
    }
    public function build()
    {
        $Yb = $this->generateXML();
        if (!(empty($this->bindingType) || $this->bindingType == SPConstants::HTTP_REDIRECT)) {
            goto KP;
        }
        $zy = gzdeflate($Yb);
        $k1 = base64_encode($zy);
        $Og = urlencode($k1);
        $Yb = $Og;
        KP:
        return $Yb;
    }
}
