<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\AuthnRequest;
use MiniOrange\SP\Helper\SPConstants;

/**
 * Handles generation and sending of AuthnRequest to the IDP
 * for authentication. AuthnRequest is generated and user is
 * redirected to the IDP for authentication.
 */
class SendAuthnRequest extends BaseAction
{

    public function __construct(\Magento\Backend\App\Action\Context         $context,
                                \MiniOrange\SP\Helper\SPUtility             $spUtility,
                                StoreManagerInterface                       $storeManager,
                                \Magento\Framework\Controller\ResultFactory $resultFactory,
                                RequestInterface                            $request,
                                \Magento\Framework\App\ResponseFactory      $responseFactory)
    {
        //You can use dependency injection to get any class this observer may need.
        $this->_request = $request;
        parent::__construct($context, $spUtility, $storeManager, $resultFactory, $responseFactory);
    }

    /**
     * Execute function to execute the classes function.
     * @throws NotRegisteredException
     * @throws \Exception
     */
    public function execute()
    {
        if ($this->spUtility->isTrialExpired()) {
            $this->spUtility->log_debug("ProcessUserAction: execute : Your demo account has expired.");
            print_r("Your Demo account has expired. Please contact magentosupport@xecurify.com");
            exit;
        }
        $this->spUtility->log_debug("SendAuthnRequest: execute");
        $params = $this->getRequest()->getParams();  //get params
        $this->checkIfValidPlugin(); // check if it's a valid plugin
        $idp_name = $params["idp_name"];
        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $idp_name);
        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        $previousUrl = $this->_redirect->getRefererUrl();
        $this->spUtility->log_debug("SendAuthorizationRequest: collection :", count($collection));
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idp_name) {
                $idpDetails = $item->getData();
            }
        }
        $relayState = !empty($params['relayState']) ? $params['relayState'] : $previousUrl;
        $admin = $this->spUtility->checkIfFlowStartedFromBackend($relayState);
        if ($relayState != SPConstants::TEST_RELAYSTATE && !$admin) {
            $flag = false;
            $currentWebsiteId = $this->spUtility->getCurrentWebsiteId();
            $websiteIds = $this->spUtility->getStoreConfig(SPConstants::WEBSITE_IDS);
            $selectedWebsiteCount = $this->spUtility->getStoreConfig(SPConstants::WEBSITE_COUNT);
            $selectedWebsites = $this->spUtility->isBlank($websiteIds) ? array() : json_decode($websiteIds);
            $websiteLimit = $this->spUtility->getWebsiteLimit();
            if (!$this->spUtility->isBlank($selectedWebsites))
                foreach ($selectedWebsites as $key => $website) {
                    if ($currentWebsiteId == $key) {
                        $flag = true;
                        break;
                    }
                }
            if ($flag == false || $selectedWebsiteCount > $websiteLimit) {
                print_r("You have not selected this website for SSO");
                $this->messageManager->addErrorMessage(__("You have not selected this website for SSO"));
                return;
            }

        }


        $auto_redirect_from_signin = $this->spUtility->isAutoRedirectEnabled($idp_name);
        $auto_redirect_from_allpage = $this->spUtility->isAllPageAutoRedirectEnabled($idp_name);
        if ($relayState != SPConstants::TEST_RELAYSTATE && $auto_redirect_from_signin && ($auto_redirect_from_allpage == NULL || $auto_redirect_from_allpage == 0)) {
            if(!$admin)
            {
                $relayState = $this->_request->getServer('HTTP_REFERER');
                if ($relayState)
                    $relayState = preg_replace('/\/$/', '', $relayState);
            }
            $this->spUtility->flushCache();
        }

        //get required values from the database
        $ssoUrl = $idpDetails['saml_login_url'];
        $bindingType = $idpDetails['saml_login_binding'];
        $forceAuthn = $idpDetails['force_authentication_with_idp'];
        $acsUrl = $this->spUtility->getAcsUrl();
        $issuer = $this->spUtility->getIssuerUrl();
        $this->spUtility->log_debug("SendAuthnRequest: execute: idp configuration fetched ");
        //generate the saml request
        $samlRequest = (new AuthnRequest($acsUrl, $issuer, $ssoUrl, $forceAuthn, $bindingType))->build();
        // send saml request over
        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("before sending saml request", $idp_name);
        if (empty($bindingType) || $bindingType == SPConstants::HTTP_REDIRECT)
            $this->sendHTTPRedirectRequest($samlRequest, $relayState, $ssoUrl);
        else
            $this->sendHTTPPostRequest($samlRequest, $relayState, $ssoUrl);

    }
}
