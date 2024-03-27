<?php

namespace MiniOrange\SP\Helper;

use DOMNode;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;

class IDPMetadataReader extends IdentityProviders
{

    private $identityProviders;
    private $serviceProviders;

    public function __construct(DOMNode $xml = NULL)
    {

        $this->identityProviders = array();
        $this->serviceProviders = array();

        $entityDescriptors = SAML2Utilities::xpQuery($xml, './saml_metadata:EntityDescriptor');

        foreach ($entityDescriptors as $entityDescriptor) {
            $idpSSODescriptor = SAML2Utilities::xpQuery($entityDescriptor, './saml_metadata:IDPSSODescriptor');

            if (!empty($idpSSODescriptor)) {
                array_push($this->identityProviders, new IdentityProviders($entityDescriptor));
            }
            //TODO: add sp descriptor
        }
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
