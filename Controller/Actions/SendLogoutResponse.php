<?php

namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\LogoutResponse;
use MiniOrange\SP\Helper\SPConstants;

/**
 * Handles generation and sending of LogoutRequest to the IDP
 * for processing. LogoutRequest is generated based on the ID
 * set in the observer. NameId is fetched and sent in the logout
 * request based on if the user is admin or customer.
 */
class SendLogoutResponse extends BaseAction
{
    private $isAdmin;
    private $userId;
    private $requestId;

    /**
     * Execute function to execute the classes function.
     * @throws NotRegisteredException
     * @throws \Exception
     */
    public function execute()
    {
        $this->checkIfValidPlugin();

        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("logoutrequest: ", $idp_name);
        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idp_name) {
                $idpDetails = $item->getData();
            }
        }

        if (!$this->spUtility->isSPConfigured() || !($idpDetails['saml_logout_url'])) return;
        //get required values from the database
        $destination = $this->spUtility->getLogoutUrl();
        //$bindingType = $this->spUtility->getStoreConfig(SPConstants::LOGOUT_BINDING);
        $bindingType = $idpDetails['saml_logout_binding'];
        $sendRelayState = $this->isAdmin ? $this->spUtility->getAdminBaseUrl() : $this->spUtility->getBaseUrl();
        $issuer = $this->spUtility->getIssuerUrl();
        //generate the logout request
        $logoutResponse = (new LogoutResponse($this->requestId, $issuer, $destination, $bindingType))->build();
        // send saml request over
        if (empty($bindingType)
            || $bindingType == SPConstants::HTTP_REDIRECT)
            return $this->sendHTTPRedirectResponse($logoutResponse, $sendRelayState, $destination);
        else
            $this->sendHTTPPostResponse($logoutResponse, $sendRelayState, $destination);
    }

    /** The setter function for the request Id Parameter */
    public function setRequestId($id)
    {
        $this->requestId = $id;
        return $this;
    }
}
