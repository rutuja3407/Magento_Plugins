<?php

namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;

/**
 * This is our main logout Observer class. Observer class are used as a callback
 * function for all of our events and hooks. This particular observer
 * class is being used to check if the customer has initiated the logout process.
 * If so then send a logout request to the IDP.
 */
class AdminPostLogoutObserver implements ObserverInterface
{
    private $messageManager;
    private $logger;
    private $spUtility;


    public function __construct(
        ManagerInterface $messageManager,
        LoggerInterface  $logger,
        SPUtility        $spUtility)
    {
        //You can use dependency injection to get any class this observer may need.
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->spUtility = $spUtility;

    }

    /**
     * This function is called as soon as the observer class is initialized.
     * Checks if the request parameter has any of the configured request
     * parameters and handles any exception that the system might throw.
     *
     * @param $observer
     */
    public function execute(Observer $observer)
    {

        $this->spUtility->log_debug("SAMLAdminLogoutObserver: Execute");
        $idp_name = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);

        if ($idp_name) {
            $idp_name = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
            $this->spUtility->log_debug("SAMLAdminLogoutObserver: App Name: " . $idp_name);

            $collection = $this->spUtility->getIDPApps();
            $idpDetails = null;
            foreach ($collection as $item) {
                if ($item->getData()["idp_name"] === $idp_name) {
                    $idpDetails = $item->getData();
                }
            }
            $logout_redirect = $idpDetails['saml_logout_redirect_url'];
            $this->spUtility->log_debug("SAMLAdminLogoutObserver: logout redirect url: " . $logout_redirect);

            if (!empty($logout_redirect)) {

                $this->spUtility->redirectURL($logout_redirect);
                $this->spUtility->log_debug("SAMLAdminLogoutObserver: redirecting to logout url ");
                exit;
            }

        }

        $this->spUtility->redirectURL($this->spUtility->getAdminBaseUrl());
        exit;

    }
}
