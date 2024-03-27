<?php

namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Controller\Actions\SendLogoutRequest;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;

/**
 * This is our main logout Observer class. Observer class are used as a callback
 * function for all of our events and hooks. This particular observer
 * class is being used to check if the admin has initiated the logout process.
 * If so then send a logout request to the IDP.
 */
class AdminLogoutObserver implements ObserverInterface
{
    protected $userFactory;
    private $messageManager;
    private $logger;
    private $readResponseAction;
    private $spUtility;
    private $logoutRequestAction;

    public function __construct(
        ManagerInterface   $messageManager,
        LoggerInterface    $logger,
        ReadResponseAction $readResponseAction,
        SPUtility          $spUtility,
        SendLogoutRequest  $logoutRequestAction,
        UserFactory        $userFactory
    )
    {
        //You can use dependency injection to get any class this observer may need.
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->readResponseAction = $readResponseAction;
        $this->spUtility = $spUtility;
        $this->logoutRequestAction = $logoutRequestAction;
        $this->userFactory = $userFactory;

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
        $via_sso = $this->spUtility->getAdminSessionData('admin_post_logout');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $backendUrl = $objectManager->create('Magento\Backend\Model\UrlInterface');
        $current_logoutUrl = $backendUrl->getCurrentUrl();
        $this->spUtility->setAdminSessionData('admin_logout_url', $current_logoutUrl);
        $this->spUtility->log_debug("In Admin pre Logout Observer $via_sso");

        if ($via_sso) {
            try {
                $this->spUtility->setAdminSessionData('admin_post_logout', NULL);

                $userDetails = $this->spUtility->getAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);

                $this->spUtility->log_debug("In admin Logout Observer Users Details", $userDetails);
                if ($this->spUtility->isBlank($userDetails)
                    && $this->spUtility->isUserLoggedIn()) {    // check if user data has been set info for logout
                    $data['admin'] = TRUE;
                    $data['id'] = $this->spUtility->getCurrentAdminUser()->getId();
                    $this->spUtility->setAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, $data);
                    $this->spUtility->log_debug("In Admin Logout Observer If(UsersDetails blank and isUserLoggedIN");
                    //return;
                }
                $userDetails = $this->spUtility->getAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);

                // send logout request if user details is not blank
                if (!$this->spUtility->isBlank($userDetails)) {
                    $this->spUtility->log_debug("send logout request ");

                    $user = $this->userFactory->create()->load($userDetails['id']);
                    $nameId = $user->getEmail();
                    $this->logoutRequestAction->setIsAdmin($userDetails['admin'])->setrelay($current_logoutUrl)
                        ->setUserId($userDetails['id'])->setNameId($nameId)->execute();
                }

                // check if logout response needs to be sent out
                $sendLogoutResponse = $this->spUtility->getAdminSessionData(SPConstants::SEND_RESPONSE, TRUE);
                $requestId = $this->spUtility->getAdminSessionData(SPConstants::LOGOUT_REQUEST_ID, TRUE);
                // send logout response if request came from IDP
                $this->spUtility->log_debug("sendLogoutResponse ", $sendLogoutResponse);
                $this->spUtility->log_debug("requestId ", $requestId);
                if ($sendLogoutResponse) {
                    $this->spUtility->log_debug("Inside if ", $sendLogoutResponse);
                    $this->logoutResponseAction->setRequestId($requestId)->execute();
                }

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->spUtility->log_debug($e->getMessage());

            }

        }
    }
}
