<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;

class CustomerLoginAction extends BaseAction
{
    protected $relayState;
    protected $user;
    protected $customerSession;
    protected $responseFactory;
    protected $customerId;
    protected $accountId;
    protected $request;
    protected $tokenModelFactory;

    public function __construct(
        Context                         $context,
        QuoteFactory                    $quoteFactory,
        SPUtility                       $spUtility,
        \Magento\Customer\Model\Session $customerSession,
        ResponseFactory                 $responseFactory,
        StoreManagerInterface           $storeManager,
        ResultFactory                   $resultFactory,
        RequestInterface                $request,
        TokenFactory                    $tokenModelFactory
    )
    {
        $this->customerSession = $customerSession;
        $this->responseFactory = $responseFactory;
        $this->tokenModelFactory = $tokenModelFactory;
        $this->request = $request;
        parent::__construct($context, $spUtility, $storeManager, $resultFactory, $responseFactory);
    }

    /**
     * Execute function to execute the classes function.
     */

    public function execute()
    {
        $idpName = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("CustomerLoginAction", $idpName);
        $idpDetails = $this->getIdpDetails($idpName);
        $this->spUtility->log_debug("CustomerLoginAction : execute: relaystate: " . $this->relayState);
        $this->customerSession->setCustomerAsLoggedIn($this->user);

        $customer = $this->spUtility->getCustomer($this->customerId);
        $store = $this->spUtility->getStoreById($customer->getStoreId());
        $customerData = new DataObject(['customer_id' => $this->customerId]);
        $params = $this->request->getParams();

        if (!empty($params['parent_account_id'])) {
            $this->customerSession->setIsAdmin(true);
            $this->_eventManager->dispatch('customer_save_after', ['customer' => $params]);
        }
        $isAdmin = $this->customerSession->isLoggedIn() && $this->customerSession->getIsAdmin();
        $this->_eventManager->dispatch('sp_customer_login', ['customer_data' => $customerData]);
        $this->spUtility->log_debug("CustomerLoginAction : execute():  Event Dispatched");

        $redirectUrl = $this->spUtility->isBlank($this->relayState) ? $store->getBaseUrl() : $this->relayState;
        $this->spUtility->log_debug("CustomerLoginAction : execute: redirectUrl. " . $redirectUrl);

        if (!empty($idpDetails) && $idpDetails['mo_saml_headless_sso']) {
            if (!empty($idpDetails['mo_saml_frontend_post_url'])) {
                $this->handleHeadlessSSO($idpDetails);
            } else {
                $this->spUtility->messageManager->addErrorMessage('You have enabled the HeadlessSSO but Frontend URL is not provided in the configuration.');
                $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
                exit;
            }

        }

        $this->spUtility->messageManager->addSuccessMessage('You are logged in Successfully.');
        $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
        exit;
    }

    protected function getIdpDetails($idpName)
    {
        $collection = $this->spUtility->getIDPApps();
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idpName) {
                return $item->getData();
            }
        }

        return null;
    }

    protected function handleHeadlessSSO($idpDetails)
    {
        $this->spUtility->log_debug("CustomerLoginAction: HeadLessSSO Enabled session ");

        if ($this->customerSession->isLoggedIn()) {
            $this->spUtility->log_debug("CustomerLoginAction: Customer session exists");
            $customerId = $this->customerSession->getCustomer()->getId();
            $this->spUtility->log_debug("CustomerLoginAction: CustomerID ", $customerId);

            $customerToken = $this->generateCustomerToken($customerId);

            if ($customerToken) {

                $frontendUrl = $this->generateFrontendPostUrl($idpDetails['mo_saml_frontend_post_url'], $customerToken);

            } else {
                // Redirect to the frontend URL with an error code
                $this->spUtility->log_debug("CustomerLoginAction: customer Token is Empty ");
                $frontendUrl = $this->generateFrontendPostUrl($idpDetails['mo_saml_frontend_post_url'], null, 'Failed to generate customer token');

            }
        } else {
            // Redirect to the frontend URL with an error code
            $this->spUtility->log_debug("CustomerLoginAction: customer Session is not Created");
            $frontendUrl = $this->generateFrontendPostUrl($idpDetails['mo_saml_frontend_post_url'], null, 'Customer not logged in');
        }
        // Redirect to the frontend URL
        $this->responseFactory->create()->setRedirect($frontendUrl)->sendResponse();
        exit;

    }

    private function generateCustomerToken($customerId)
    {
        try {
            $customerToken = $this->tokenModelFactory->create()->createCustomerToken($customerId)->getToken();
            $this->spUtility->log_debug("CustomerLoginAction: Customer token created");
            return $customerToken;
        } catch (\Exception $e) {
            $this->spUtility->log_debug("CustomerLoginAction: Token creation error - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate the frontend URL with customer token and additional parameters.
     *
     * @param string $frontendUrl
     * @param string|null $customerToken
     * @param array $additionalParams
     * @return string
     */
    protected function generateFrontendPostUrl($frontendUrl, $customerToken = null, $additionalParams = [])
    {
        $params = ['customer_token' => $customerToken] + $additionalParams;
        return $frontendUrl . '?' . http_build_query($params);
    }

    /** Setter for the user Parameter */
    public function setUser($user)
    {
        $this->user = $user;
        $this->spUtility->log_debug("setting User: ");
        return $this;
    }

    /** Setter for the customerId Parameter */
    public function setCustomerId($id)
    {
        $this->customerId = $id;
        $this->spUtility->log_debug("setting customerId: " . $id);
        return $this;
    }

    /** Setter for the RelayState Parameter */
    public function setRelayState($relayState)
    {
        $this->relayState = $relayState;
        return $this;
    }

    public function setAxCompanyId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function setScope($isAdminScope)
    {
        $this->isAdminScope = $isAdminScope;
        return $this;
    }


}
