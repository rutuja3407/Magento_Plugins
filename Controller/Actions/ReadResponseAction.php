<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\SAML2Assertion;
use MiniOrange\SP\Helper\Saml2\SAML2Response;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;

/**
 * Handles reading of SAML Responses from the IDP. Read the SAML Response
 * from the IDP and process it to detect if it's a valid response from the IDP.
 * Generate a SAML Response Object and log the user in. Update existing user
 * attributes and groups if necessary.
 */
class ReadResponseAction extends BaseAction implements HttpPostActionInterface, HttpGetActionInterface
{
    protected $spUtility;
    protected $logout;
    private $REQUEST;
    private $POST;
    private $processResponseAction;

    public function __construct(
        Context               $context,
        SPUtility             $spUtility,
        ProcessResponseAction $processResponseAction,
        StoreManagerInterface $storeManager,
        ResultFactory         $resultFactory,
        ResponseFactory       $responseFactory,
        Logout                $logout)
    {
        //You can use dependency injection to get any class this observer may need.
        $this->processResponseAction = $processResponseAction;
        $this->logout = $logout;
        parent::__construct($context, $spUtility, $storeManager, $resultFactory, $responseFactory);
    }

    /**
     * Execute function to execute the classes function.
     * @throws NotRegisteredException
     * @throws InvalidSAMLVersionException
     * @throws MissingIDException
     * @throws MissingIssuerValueException
     * @throws MissingNameIdException
     * @throws InvalidNumberOfNameIDsException
     * @throws \Exception
     */
    public function execute()
    {
        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("readresponseAction", $idp_name);
        $this->checkIfValidPlugin();
        $params = $this->getRequest()->getParams();
        // read the response
        $samlResponse = $this->REQUEST['SAMLResponse'];

        $saml_response = base64_decode($_POST['SAMLResponse']);

        $document = new \DOMDocument();
        $document->loadXML($saml_response);
        $saml_response_xml = $document->firstChild;
        $saml_obj = new SAML2Assertion($saml_response_xml, $this->spUtility);
        $issuer = $saml_obj->getIssuer();

        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_entity_id"] === $issuer) {
                $idpDetails = $item->getData();
            }
        }

        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $idpDetails['idp_name']);
        $this->spUtility->setAdminSessionData(SPConstants::IDP_NAME, $idpDetails['idp_name']);

        $relayState = !empty($this->REQUEST['RelayState']) ? $this->REQUEST['RelayState'] : '/';

        //decode the saml response
        $samlResponse = base64_decode((string)$samlResponse);
        $this->spUtility->log_debug("samlResponse", print_r($samlResponse, true));
        if (empty($this->POST['SAMLResponse'])) {
            $samlResponse = gzinflate($samlResponse);
        }

        $document = new \DOMDocument();
        $document->loadXML($samlResponse);
        $samlResponseXML = $document->firstChild;
        //if logout response then redirect the user to the relayState

        if ($samlResponseXML->localName == 'LogoutResponse') {
            $this->logout->setRequestParam($this->REQUEST)->setPostParam($this->POST)->execute();
        }

        $samlResponse = new SAML2Response($samlResponseXML, $this->spUtility);    //convert the xml to SAML2Response object
        $this->spUtility->log_debug("before processuseraction");
        $this->processResponseAction->setSamlResponse($samlResponse)
            ->setRelayState($relayState)->execute();
    }

    /** Setter for the post Parameter */
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }

    /** Setter for the request Parameter */
    public function setRequestParam($request)
    {
        $this->REQUEST = $request;
        return $this;
    }
}
