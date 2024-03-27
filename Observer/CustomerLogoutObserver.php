<?php

namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Controller\Actions\SendLogoutRequest;
use MiniOrange\SP\Controller\Actions\SendLogoutResponse;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;

/**
 * This is our main logout Observer class. Observer class are used as a callback
 * function for all of our events and hooks. This particular observer
 * class is being used to check if the customer has initiated the logout process.
 * If so then send a logout request to the IDP.
 */
class CustomerLogoutObserver implements ObserverInterface
{
    private $messageManager;
    private $logger;
    private $readResponseAction;
    private $spUtility;
    private $logoutRequestAction;
    private $logoutResponseAction;

    public function __construct(
        ManagerInterface   $messageManager,
        LoggerInterface    $logger,
        ReadResponseAction $readResponseAction,
        SPUtility          $spUtility,
        SendLogoutRequest  $logoutRequestAction,
        SendLogoutResponse $logoutResponseAction)
    {
        //You can use dependency injection to get any class this observer may need.
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->readResponseAction = $readResponseAction;
        $this->spUtility = $spUtility;
        $this->logoutRequestAction = $logoutRequestAction;
        $this->logoutResponseAction = $logoutResponseAction;
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

        $via_sso = $this->spUtility->getSessionData('customer_post_logout');
        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);

        $this->spUtility->log_debug("In Customer Pre Logout Observer $via_sso");
        if ($via_sso) {
            try {
                $this->spUtility->setAdminSessionData('customer_post_logout', NULL);

                $userDetails = $this->spUtility->getSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);

                $this->spUtility->log_debug("In Customer Logout Observer Users Details", $userDetails);
                if ($this->spUtility->isBlank($userDetails)
                    && $this->spUtility->isUserLoggedIn()) {    // check if user data has been set info for logout
                    $data['admin'] = FALSE;
                    $data['id'] = $this->spUtility->getCurrentUser()->getId();
                    $this->spUtility->setSessionData(SPConstants::USER_LOGOUT_DETAIL, $data);
                    $this->spUtility->log_debug("In Customer Logout Observer If(UsersDetails blank and isUserLoggedIN");
                }
                $userDetails = $this->spUtility->getSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);

                // send logout request if user details is not blank
                if (!$this->spUtility->isBlank($userDetails)) {
                    $this->spUtility->log_debug("send logout request ");
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $customerObj = $objectManager->create('Magento\Customer\Model\Customer')
                        ->load($userDetails['id']);
                    $nameId = $customerObj->getEmail();
                    $this->spUtility->log_debug("NameID :: ", $nameId);
                    $this->spUtility->log_debug("Inside 2nd if Sending logoutRequestAction");
                    $this->logoutRequestAction->setIsAdmin($userDetails['admin'])
                        ->setUserId($userDetails['id'])->setNameId($nameId)->execute();
                    $this->spUtility->log_debug("End of 2nd if Sending logoutRequestAction");
                }

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->spUtility->log_debug($e->getMessage());

            }
        }
    }
}
