<?php


namespace MiniOrange\SP\Helper;

use DOMElement;
use DOMNode;
use DOMDocument;
use Exception;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecEnc;
use MiniOrange\SP\Helper\Saml2\lib\XMLSecurityDSig;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
class IDPMetadataReader extends IdentityProviders
{
    private $identityProviders;
    private $serviceProviders;
    public function __construct(DOMNode $BY = NULL)
    {
        $this->identityProviders = array();
        $this->serviceProviders = array();
        $Vk = SAML2Utilities::xpQuery($BY, "\56\57\163\x61\x6d\x6c\x5f\x6d\145\x74\x61\144\x61\x74\141\x3a\105\x6e\x74\151\164\171\x44\x65\x73\143\162\x69\x70\164\157\x72");
        foreach ($Vk as $En) {
            $op = SAML2Utilities::xpQuery($En, "\56\x2f\x73\141\x6d\x6c\x5f\155\145\x74\x61\x64\x61\164\141\72\111\x44\x50\123\x53\117\x44\x65\163\x63\x72\x69\160\164\x6f\x72");
            if (empty($op)) {
                goto Ta;
            }
            array_push($this->identityProviders, new IdentityProviders($En));
            Ta:
            Cy:
        }
        Gq:
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
