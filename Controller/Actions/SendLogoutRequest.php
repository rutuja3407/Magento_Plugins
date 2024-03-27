<?php

namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\LogoutRequest;
use MiniOrange\SP\Helper\SPConstants;

/**
 * Handles generation and sending of LogoutRequest to the IDP
 * for processing. LogoutRequest is generated based on the ID
 * set in the observer. NameId is fetched and sent in the logout
 * request based on if the user is admin or customer.
 */
class SendLogoutRequest extends BaseAction
{
    protected $relay;
    private $isAdmin;
    private $userId;
    private $nameId;
    private $sessionIndex;

    /**
     * Execute function to execute the classes function.
     * @throws NotRegisteredException
     * @throws \Exception
     */

    public function execute()
    {
        $this->spUtility->log_debug("In SendLogoutRequest.php");

        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        if (!$idp_name) {
            $idp_name = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
        }
        $this->spUtility->log_debug("logoutrequest: ", $idp_name);
        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idp_name) {
                $idpDetails = $item->getData();
            }
        }

        if (!$this->spUtility->isSPConfigured() || !($idpDetails['saml_logout_url']))
            return;
        //get required values from the database
        $destination = $idpDetails['saml_logout_url'];
        $bindingType = $idpDetails['saml_logout_binding'];

        $nameId = $this->nameId;

        $sessionIndex = $this->isAdmin ? $this->spUtility->getAdminStoreConfig(SPConstants::SESSION_INDEX, $this->userId)
            : $this->spUtility->getCustomerStoreConfig(SPConstants::SESSION_INDEX, $this->userId);
        $sendRelayState = $this->isAdmin ? $this->relay : $this->spUtility->getBaseUrl();
        $issuer = $this->spUtility->getIssuerUrl();

        //remove nameid and session index for user
        $this->spUtility->saveConfig(SPConstants::NAME_ID, '', $this->userId, $this->isAdmin);
        $this->spUtility->saveConfig(SPConstants::SESSION_INDEX, '', $this->userId, $this->isAdmin);
        //generate the logout request
        $this->spUtility->log_debug("In SendLogoutRequest: before logoutRequest: ");

        $logoutRequest = (new LogoutRequest($this->spUtility))->setIssuer($issuer)->setDestination($destination)
            ->setNameId($nameId)->setSessionIndexes($sessionIndex)
            ->setBindingType($bindingType)->build();

        $this->spUtility->log_debug("In SendLogoutRequest: logoutRequest: ", $logoutRequest);
        // send saml request over

        if (empty($bindingType) || $bindingType == SPConstants::HTTP_REDIRECT) {
            $this->spUtility->log_debug("In SendLogoutRequest: logoutRequest: HTTP_REDIRECT");
            return $this->sendHTTPRedirectRequest($logoutRequest, $sendRelayState, $destination);
        } else {
            $this->spUtility->log_debug("In SendLogoutRequest: logoutRequest: POST_REDIRECT");
            $this->sendHTTPPostRequest($logoutRequest, $sendRelayState, $destination);
        }

    }

    public function setNameId($nameId)
    {
        $this->nameId = $nameId;
        return $this;
    }

    /** The setter function for the isAdmin Parameter */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    /** The setter function for the userId Parameter */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setrelay($relay)
    {
        $this->relay = $relay;
        return $this;
    }


}
