<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\SAML2Assertion;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;

/**
 * This is our main Observer class. Observer class are used as a callback
 * function for all of our events and hooks. This particular observer
 * class is being used to check if a SAML request or response was made
 * to the website. If so then read and process it. Every Observer class
 * needs to implement ObserverInterface.
 */
class Logout extends Action implements CsrfAwareActionInterface
{
    protected $messageManager;
    protected $logger;
    protected $spUtility;
    protected $adminLoginAction;
    protected $testAction;
    protected $storeManager;
    protected $currentControllerName;
    protected $currentActionName;
    protected $readLogoutRequestAction;
    protected $requestInterface;
    protected $request;
    protected $formkey;
    protected $_pageFactory;
    protected $acsUrl;
    protected $repostSAMLResponseRequest;
    protected $repostSAMLResponsePostData;
    protected $responseFactory;
    protected $baseRelayState;
    protected $REQUEST;
    protected $POST;

    public function __construct(
        ManagerInterface        $messageManager,
        LoggerInterface         $logger,
        Context                 $context,
        SPUtility               $spUtility,
        AdminLoginAction        $adminLoginAction,
        Http                    $httpRequest,
        ReadLogoutRequestAction $readLogoutRequestAction,
        RequestInterface        $request,
        StoreManagerInterface   $storeManager,
        ShowTestResultsAction   $testAction,
        ResultFactory           $resultFactory,
        PageFactory             $pageFactory,
        FormKey                 $formkey)
    {
        //You can use dependency injection to get any class this observer may need.
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->spUtility = $spUtility;
        $this->adminLoginAction = $adminLoginAction;
        $this->readLogoutRequestAction = $readLogoutRequestAction;
        $this->currentControllerName = $httpRequest->getControllerName();
        $this->currentActionName = $httpRequest->getActionName();
        $this->request = $request;
        $this->testAction = $testAction;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->_pageFactory = $pageFactory;

        parent::__construct($context);
        $this->formkey = $formkey;
        $this->getRequest()->setParam('form_key', $this->formkey->getFormKey());
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->responseFactory = $objectManager->get('\Magento\Framework\App\ResponseFactory');

    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function execute()
    {
        $this->spUtility->log_debug("In logout");
        $params = $this->getRequest()->getParams();

        if ((($this->spUtility->getSessionData(SPConstants::IDP_NAME) == NULL) || ($this->spUtility->getAdminSessionData(SPConstants::IDP_NAME) == NULL))) {
            $samlResponse = $params['SAMLResponse'];
            $saml_response = base64_decode($params['SAMLResponse']);
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
            $this->spUtility->log_debug("logout: Execute:customer_idp name " . $idpDetails['idp_name']);

            $this->spUtility->setAdminSessionData(SPConstants::IDP_NAME, $idpDetails['idp_name']);
            $this->spUtility->log_debug("logout: Execute:admin_idp name " . $idpDetails['idp_name']);

        }
        $admin = null;
        $admin = $this->spUtility->checkIfFlowStartedFromBackend($params['RelayState']);
        $customerredirectUrl = $params['RelayState'];
        $adminredirectUrl = $params['RelayState'];

        if ($admin) {
            $this->spUtility->setAdminSessionData('admin_post_logout', NULL);

            $admin_logout_url = $adminredirectUrl;

            $this->spUtility->setAdminSessionData('admin_postlogout_action', 1);
            $this->spUtility->log_debug("logout: Execute:admin");
            $this->spUtility->log_debug("logout: admin logout url:" . json_encode($admin_logout_url));
            $this->spUtility->setAdminSessionData('admin_logout_url', NULL);
            if ($admin_logout_url != NULL) {
                $this->spUtility->redirectURL($admin_logout_url);
                exit;
            } else {
                $this->spUtility->setSessionData('customer_post_logout', NULL);
                $this->spUtility->redirectURL($admin_logout_url);
                exit;
            }


        } else {
            $this->spUtility->setSessionData('customer_post_logout', NULL);
            $this->spUtility->log_debug("logout: Execute:customer");
            $logout_redirect = $customerredirectUrl . 'customer/account/logout';
            $this->spUtility->redirectURL($logout_redirect);
            exit;
        }

    }

    /** Setter for the request Parameter */
    public function setRequestParam($request)
    {
        $this->REQUEST = $request;
        return $this;
    }


    /** Setter for the post Parameter */
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }


}
