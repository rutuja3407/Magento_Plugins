<?php


namespace MiniOrange\SP\Helper;

use DOMNode;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
class IDPMetadataReader extends IdentityProviders
{
    private $identityProviders;
    private $serviceProviders;
    public function __construct(DOMNode $xa = NULL)
    {
        $this->identityProviders = array();
        $this->serviceProviders = array();
        $Hd = SAML2Utilities::xpQuery($xa, "\56\57\x73\141\155\x6c\x5f\155\145\164\141\x64\x61\164\x61\x3a\105\156\x74\151\164\x79\x44\145\163\143\x72\x69\160\x74\157\x72");
        foreach ($Hd as $j0) {
            $sp = SAML2Utilities::xpQuery($j0, "\56\57\x73\141\155\x6c\x5f\x6d\x65\x74\x61\x64\141\x74\141\72\111\104\120\x53\123\x4f\104\x65\163\x63\162\151\x70\x74\157\162");
            if (empty($sp)) {
                goto wQ;
            }
            array_push($this->identityProviders, new IdentityProviders($j0));
            wQ:
            HO:
        }
        uU:
    }
    public function getIdentityProviders()
    {
        return $this->identityProviders;
    }
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }
}
