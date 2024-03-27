<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Exception\InvalidAudienceException;
use MiniOrange\SP\Helper\Exception\InvalidDestinationException;
use MiniOrange\SP\Helper\Exception\InvalidIssuerException;
use MiniOrange\SP\Helper\Exception\InvalidSamlStatusCodeException;
use MiniOrange\SP\Helper\Exception\InvalidSignatureInResponseException;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;

/**
 * Handles processing of SAML Responses from the IDP. Process the SAML Response
 * from the IDP and detect if it's a valid response from the IDP. Validate the
 * certificates and the SAML attributes and Update existing user attributes
 * and groups if necessary. Log the user in.
 */
class ProcessResponseAction extends BaseAction
{
    protected $certfpFromPlugin;
    protected $x509_certificate;
    private $samlResponse;
    private $acsUrl;
    private $relayState;
    private $responseSigned;
    private $assertionSigned;
    private $issuer;
    private $spEntityId;
    private $attrMappingAction;

    public function __construct(
        Context                     $context,
        SPUtility                   $spUtility,
        CheckAttributeMappingAction $attrMappingAction,
        StoreManagerInterface       $storeManager,
        ResultFactory               $resultFactory,
        ResponseFactory             $responseFactory)
    {
        //You can use dependency injection to get any class this observer may need.
        $this->attrMappingAction = $attrMappingAction;

        parent::__construct($context, $spUtility, $storeManager, $resultFactory, $responseFactory);
    }

    /**
     * Execute function to execute the classes function.
     * @throws InvalidSignatureInResponseException
     * @throws InvalidIssuerException
     * @throws InvalidAudienceException
     * @throws InvalidDestinationException
     * @throws InvalidSamlStatusCodeException
     */
    public function execute()
    {
        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idp_name) {
                $idpDetails = $item->getData();
            }
        }

        $this->spUtility->log_debug("processresponseAction", print_r($idpDetails, true));

        $this->x509_certificate = $idpDetails['x509_certificate'];

        $this->responseSigned = $idpDetails['response_signed'];
        $this->assertionSigned = $idpDetails['assertion_signed'];

        $this->issuer = $idpDetails['idp_entity_id'];
        $this->spUtility->log_debug(" in processResponseAction :");

        $this->spEntityId = $this->spUtility->getIssuerUrl();
        $this->acsUrl = $this->spUtility->getAcsUrl();

        //$this->validateStatusCode();
        $this->spUtility->log_debug(" processResponseAction : execute: validated status code");

        $responseSignatureData = $this->samlResponse->getSignatureData();
        $this->spUtility->log_debug(" processResponseAction : after responseSignatureData");
        $assertionSignatureData = current($this->samlResponse->getAssertions())->getSignatureData();
        $this->spUtility->log_debug(" processResponseAction : after assertionSignatureData");

        $this->certfpFromPlugin = XMLSecurityKey::getRawThumbprint($idpDetails['x509_certificate']);
        $this->certfpFromPlugin = iconv("UTF-8", "CP1252//IGNORE", $this->certfpFromPlugin);
        $this->spUtility->log_debug(" processResponseAction : after certfpFromPlugin");
        $this->certfpFromPlugin = preg_replace('/\s+/', '', $this->certfpFromPlugin);
        $this->spUtility->log_debug(" processResponseAction : after certfpFromPlugin 1");

        $ValidateSignature = FALSE;
        if (!empty($responseSignatureData)) {
            $cert = $idpDetails['x509_certificate'];
            $this->spUtility->log_debug(" processResponseAction : before validateResponseSignature function");
            $ValidateSignature = $this->validateResponseSignature($responseSignatureData, $cert);
            $this->spUtility->log_debug(" processResponseAction :  after validateResponseSignature function");
        }


        if (!empty($assertionSignatureData)) {
            $cert = $idpDetails['x509_certificate'];
            $this->spUtility->log_debug(" processResponseAction : before validateAssertionSignature function");
            $ValidateSignature = $this->validateAssertionSignature($assertionSignatureData, $cert);
            $this->spUtility->log_debug(" processResponseAction : after validateAssertionSignature function");
        }
        if (!$ValidateSignature) {
            $error = 'We could not sign you in.';
            $message = 'Please Contact your administrator.';
            $cause = '';
            $closeWindow = FALSE;

            if (!$ValidateSignature) {
                $error = 'Invalid Signature in SAML response';
                $message = '';
                $cause = 'Neither response nor assertion is signed by IDP';
                $closeWindow = TRUE;
            }
            $this->showErrorMessage($error, $message, $cause, $closeWindow);
            exit();
        }

        $this->attrMappingAction->setSamlResponse($this->samlResponse)->setRelayState($this->relayState)->execute();
    }

    /**
     * Function checks if the signature in the Response element
     * of the SAML response is a valid response. Throw an error
     * otherwise.
     *
     * @param $responseSignatureData
     * @throws InvalidSignatureInResponseException
     */
    private function validateResponseSignature($responseSignatureData, $cert)
    {
        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $acsUrl = $this->spUtility->getAcsUrl();

        $this->spUtility->log_debug(" processResponseAction : validateResponseSignature: response signed: ", $this->responseSigned);
        if (empty($responseSignatureData)) return;
        $validSignature = SAML2Utilities::processResponse($idp_name, $acsUrl, $this->certfpFromPlugin, $responseSignatureData, $this->samlResponse, $cert);

        return $validSignature;
    }

    /**
     * Function checks if the signature in the Assertion element
     * of the SAML response is a valid response. Throw an error
     * otherwise.
     *
     * @param $assertionSignatureData
     * @throws InvalidSignatureInResponseException
     */
    private function validateAssertionSignature($assertionSignatureData, $cert)
    {
        $this->spUtility->log_debug(" processResponseAction : In validateAssertionSignature function");

        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $acsUrl = $this->spUtility->getAcsUrl();

        if (empty($assertionSignatureData)) return;
        $validSignature = SAML2Utilities::processResponse($idp_name, $acsUrl, $this->certfpFromPlugin, $assertionSignatureData, $this->samlResponse, $cert);

        return $validSignature;
    }

    public function showErrorMessage($error, $message, $cause, $closeWindow = FALSE)
    {
        $actionToTakeUponWindow = $closeWindow === TRUE ? 'onClick="self.close();"' : 'href="' . $this->spUtility->getBaseUrl() . '/user/login"';
        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                            <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>' . $error . '</p>
                                <p>' . $message . '</p>
                                <p><strong>Possible Cause: </strong>' . $cause . '</p>
                            </div>
                            <div style="margin:3%;display:block;text-align:center;"></div>
                            <div style="margin:3%;display:block;text-align:center;">
                                <a style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF; text-decoration: none;"type="button"  ' . $actionToTakeUponWindow . ' >Done</a>
                            </div>';
        exit;
    }

    /** Setter for the RelayState Parameter */
    public function setRelayState($relayState)
    {
        $this->spUtility->log_debug("setting relaystate to: ", $relayState);
        $this->relayState = $relayState;
        return $this;
    }

    /** Setter for the SAML Response Parameter */
    public function setSamlResponse($samlResponse)
    {
        $this->samlResponse = $samlResponse;
        return $this;
    }


    /**
     * Function validates the Destination in the SAML Response.
     * Throws an error if the Destination doesn't match
     * with the one in the database.
     *
     * @throws InvalidDestinationException
     */

    /**
     * Function checks if the status coming in the SAML
     * response is SUCCESS and not a responder or
     * requester
     *
     * @param $responseSignatureData
     * @throws InvalidSamlStatusCodeException
     */
    private function validateStatusCode()
    {
        $statusCode = $this->samlResponse->getStatusCode();
        if (strpos($statusCode, 'Success') === false)
            throw new InvalidSamlStatusCodeException($statusCode, $this->samlResponse->getXML());
    }

    /**
     * Function validates the Issuer and Audience from the
     * SAML Response. Throws an error if the Issuer and
     * Audience values don't match with the one in the
     * database.
     *
     * @throws InvalidIssuerException
     * @throws InvalidAudienceException
     */

    private function validateIssuerAndAudience()
    {


        $issuer = current($this->samlResponse->getAssertions())->getIssuer();
        $audience = current(current($this->samlResponse->getAssertions())->getValidAudiences());
        if (strcmp($this->issuer, $issuer) != 0)
            throw new InvalidIssuerException($this->issuer, $issuer, $this->samlResponse->getXML());
        if (strcmp($audience, $this->spEntityId) != 0)
            throw new InvalidAudienceException($this->spEntityId, $audience, $this->samlResponse->getXML());
    }
}
