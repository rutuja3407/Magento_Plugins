<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Exception\InvalidRequestInstantException;
use MiniOrange\SP\Helper\Exception\InvalidRequestVersionException;
use MiniOrange\SP\Helper\SPConstants;
class AuthnRequest
{
    private $requestType = SPConstants::AUTHN_REQUEST;
    private $acsUrl;
    private $issuer;
    private $ssoUrl;
    private $forceAuthn;
    private $bindingType;
    private $destination;
    public function __construct($X9, $wQ, $s9, $KM, $H2)
    {
        $this->acsUrl = $X9;
        $this->issuer = $wQ;
        $this->forceAuthn = $KM;
        $this->destination = $s9;
        $this->bindingType = $H2;
    }
    public function build()
    {
        $S5 = $this->generateXML();
        if (!(empty($this->bindingType) || $this->bindingType == SPConstants::HTTP_REDIRECT)) {
            goto GM;
        }
        $a1 = gzdeflate($S5);
        $CN = base64_encode($a1);
        $AB = urlencode($CN);
        $S5 = $AB;
        GM:
        return $S5;
    }
    private function generateXML()
    {
        $S5 = "\x3c\77\x78\x6d\x6c\40\166\x65\x72\163\x69\x6f\156\75\42\61\x2e\x30\42\x20\145\156\143\x6f\144\x69\156\x67\75\x22\125\124\x46\55\70\x22\77\x3e" . "\40\x3c\163\141\155\154\x70\72\x41\x75\164\x68\x6e\122\145\x71\165\145\163\164\xd\xa\x20\x20\40\40\x20\40\x20\x20\x20\40\x20\x20\40\x20\x20\x20\40\40\x20\x20\x20\x20\40\40\40\x20\40\40\x20\x20\x20\x20\x78\x6d\154\x6e\x73\72\x73\x61\x6d\x6c\160\75\42\x75\162\x6e\x3a\157\x61\163\x69\163\72\156\x61\155\145\163\72\164\x63\x3a\x53\x41\x4d\114\72\62\x2e\60\x3a\x70\x72\x6f\x74\x6f\143\157\154\x22\15\xa\x20\x20\40\x20\40\x20\40\x20\40\x20\x20\x20\x20\x20\40\x20\x20\x20\x20\x20\x20\x20\40\40\x20\x20\x20\40\x20\40\40\x20\x78\155\154\x6e\x73\75\42\165\x72\156\72\157\141\163\x69\x73\x3a\156\x61\x6d\x65\x73\x3a\x74\143\x3a\x53\x41\115\x4c\x3a\x32\x2e\x30\72\x61\x73\163\x65\162\164\x69\157\x6e\x22\x20\111\x44\75\x22" . SAML2Utilities::generateID() . "\x22\40\40\126\145\162\x73\x69\x6f\156\x3d\x22\62\x2e\60\x22\40\111\x73\163\x75\x65\111\156\x73\x74\141\x6e\164\x3d\42" . SAML2Utilities::generateTimestamp() . "\x22";
        if (!($this->forceAuthn == 1)) {
            goto nA;
        }
        nA:
        $S5 .= "\40\x20\40\x20\x20\120\x72\x6f\164\157\x63\157\154\x42\151\156\x64\x69\x6e\x67\x3d\x22\165\162\156\x3a\157\x61\163\x69\163\x3a\x6e\141\155\145\163\72\164\x63\72\x53\x41\115\x4c\72\62\56\x30\72\142\151\156\x64\151\x6e\x67\x73\x3a\110\124\x54\x50\x2d\120\117\123\x54\x22\x20\101\163\163\145\162\164\151\x6f\x6e\103\x6f\x6e\x73\x75\x6d\x65\162\x53\x65\162\x76\x69\x63\x65\125\x52\114\x3d\42" . $this->acsUrl . "\x22\x20\40\40\x20\40\x20\104\145\163\164\151\x6e\141\x74\x69\157\x6e\75\x22" . $this->destination . "\42\x3e\15\xa\40\40\x20\x20\x20\40\40\x20\40\40\40\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\40\40\40\40\40\x20\40\40\40\x20\74\x73\141\155\154\x3a\111\x73\x73\x75\x65\162\x20\x78\155\x6c\x6e\163\72\163\x61\x6d\154\x3d\42\165\162\x6e\72\x6f\141\163\151\x73\72\156\x61\155\145\163\x3a\164\x63\72\123\x41\115\x4c\72\x32\x2e\x30\x3a\141\x73\163\145\x72\164\151\157\x6e\x22\76" . $this->issuer . "\x3c\x2f\x73\141\x6d\154\72\x49\x73\x73\x75\x65\162\x3e\15\xa\x20\x20\x20\x20\40\40\40\x20\x20\40\40\x20\x20\40\40\x20\x20\40\x20\x20\40\x20\x20\x20\40\x20\40\40\x20\40\x20\40\x3c\163\x61\155\154\x70\x3a\116\x61\x6d\145\111\x44\x50\x6f\x6c\151\x63\171\40\x41\x6c\x6c\157\167\x43\162\145\141\x74\x65\x3d\42\164\x72\165\x65\42\x20\106\157\x72\x6d\x61\x74\75\42\165\162\156\72\x6f\x61\x73\x69\163\72\x6e\141\x6d\145\163\x3a\x74\143\x3a\x53\101\x4d\114\72\61\56\x31\72\156\141\x6d\x65\151\x64\x2d\146\x6f\x72\155\141\x74\72\165\x6e\163\x70\x65\x63\151\x66\151\145\x64\x22\x2f\x3e\15\xa\x20\x20\x20\x20\x20\x20\40\40\x20\x20\x20\40\x20\40\40\40\x20\40\x20\x20\40\x20\40\40\40\40\40\x20\74\57\x73\x61\x6d\154\x70\x3a\101\165\164\150\x6e\122\x65\x71\165\145\x73\164\76";
        return $S5;
    }
}
