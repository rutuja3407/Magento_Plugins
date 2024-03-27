<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Authorization\Model\ResourceModel\Role\Collection;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Math\Random;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Helper\Exception\NotRegisteredException;
use MiniOrange\SP\Helper\Exception\RequiredFieldsException;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;


/**
 * The base action class that is inherited by each of the action
 * class. It consists of certain common functions that needs to
 * be inherited by each of the action class. Extends the
 * \Magento\Framework\App\Action\Action class which is usually
 * extended by Controller class.
 */
abstract class BaseAction extends \Magento\Framework\App\Action\Action
{

    protected $spUtility;
    protected $context;
    protected $resultFactory;
    protected $storeManager;
    protected $responseFactory;

    public function __construct(
        Context               $context,
        SPUtility             $spUtility,
        StoreManagerInterface $storeManager,
        ResultFactory         $resultFactory,
        ResponseFactory       $responseFactory)
    {
        //You can use dependency injection to get any class this observer may need.

        $this->spUtility = $spUtility;
        $this->resultFactory = $resultFactory;
        $this->storeManager = $storeManager;
        $this->responseFactory = $responseFactory;
        parent::__construct($context);
    }

    /** This function is abstract that needs to be implemented by each Action Class */
    public abstract function execute();

    /**
     * This function checks if the required fields passed to
     * this function are empty or not. If empty throw an exception.
     *
     * @param $array
     * @throws RequiredFieldsException
     */
    protected function checkIfRequiredFieldsEmpty($array)
    {
        foreach ($array as $key => $value) {
            if (
                (is_array($value) && (empty($value[$key]) || $this->spUtility->isBlank($value[$key])))
                || $this->spUtility->isBlank($value)
            )
                throw new RequiredFieldsException();
        }
    }

    /**
     * This function is used to send LogoutResponse as a request Parameter.
     * LogoutResponse is sent in the request parameter if the binding is
     * set as HTTP Redirect. Http Redirect is the default way Logout Response
     * is sent.
     *
     * @param $samlResponse
     * @param $sendRelayState
     * @param $ssoUrl
     */
    protected function sendHTTPRedirectResponse($samlResponse, $sendRelayState, $ssoUrl)
    {
        $redirect = $ssoUrl;
        $redirect .= strpos($ssoUrl, '?') !== false ? '&' : '?';
        $redirect .= 'SAMLResponse=' . $samlResponse . '&RelayState=' . urlencode($sendRelayState);
        return $this->resultRedirectFactory->create()->setUrl($redirect);
    }

    /* ===================================================================================================
                THE FUNCTIONS BELOW ARE PREMIUM PLUGIN SPECIFIC AND DIFFER IN THE FREE VERSION
       ===================================================================================================
     */

    /**
     * This function checks if the user has registered himself
     * and throws an Exception if not registered. Checks the
     * if the admin key and api key are saved in the database.
     *
     * @throws NotRegisteredException
     */
    protected function checkIfValidPlugin()
    {
        if (!$this->spUtility->micr() || !$this->spUtility->mclv()) {
            throw new NotRegisteredException;
        }
    }


    /**
     * This function is used to send LogoutRequest & AuthRequest as a request Parameter.
     * LogoutRequest & AuthRequest is sent in the request parameter if the binding is
     * set as HTTP Redirect. Http Redirect is the default way Authn Request
     * is sent. [PREMIUM] - Function also generates the signature and appends it in the
     * parameter as well along with the relayState parameter
     * @param $samlRequest
     * @param $sendRelayState
     * @param $idpUrl
     */
    protected function sendHTTPRedirectRequest($samlRequest, $sendRelayState, $idpUrl)
    {

        $this->spUtility->log_debug("BaseAction: sendHTTPRedirectRequest");
        $samlRequest = "SAMLRequest=" . $samlRequest . "&RelayState=" . urlencode($sendRelayState)
            . '&SigAlg=' . urlencode(XMLSecurityKey::RSA_SHA256);
        $param = array('type' => 'private');
        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $param);
        $certFilePath = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));

        $key->loadKey($certFilePath);

        $signature = $key->signData($samlRequest);
        $signature = base64_encode($signature);
        $redirect = $idpUrl;
        $this->spUtility->log_debug("BaseAction: idpUrl:" . $idpUrl);
        $redirect .= strpos($idpUrl, '?') !== false ? '&' : '?';
        $redirect .= $samlRequest . '&Signature=' . urlencode($signature);
        $this->spUtility->log_debug("BaseAction: sendHTTPRedirectRequest: redirect=" . $redirect);

        header('Location:  ' . $redirect);
        exit;

    }

    protected function sendHTTPRedirectAuthRequest($samlRequest, $sendRelayState, $idpUrl, $params)
    {
        $this->spUtility->log_debug("BaseAction: sendHTTPRedirectAuthRequest");
        $samlRequest = "SAMLRequest=" . $samlRequest . "&RelayState=" . urlencode($sendRelayState)
            . '&SigAlg=' . urlencode(XMLSecurityKey::RSA_SHA256);
        foreach ($params as $key => $value) {
            if (!($key == "relayState"))
                $samlRequest = $samlRequest . "&" . "$key" . "=" . urlencode($value);
        }

        $param = array('type' => 'private');
        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $param);
        $certFilePath = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));

        $key->loadKey($certFilePath);
        $signature = $key->signData($samlRequest);
        $signature = base64_encode($signature);
        $redirect = $idpUrl;
        $this->spUtility->log_debug("BaseAction: idpUrl:" . $idpUrl);
        $redirect .= strpos($idpUrl, '?') !== false ? '&' : '?';
        $redirect .= $samlRequest . '&Signature=' . urlencode($signature);
        $this->spUtility->log_debug("BaseAction: sendHTTPRedirectAuthRequest: redirect=" . $redirect);
        header('Location:  ' . $redirect);
        exit;
    }


    /**
     * This function is used to send LogoutRequest & AuthRequest as a post Parameter.
     * LogoutRequest & AuthRequest is sent in the post parameter if the binding is
     * set as HTTP Post. [PREMIUM] - Function also generates the signature and
     * appends it in the XML document before sending it over as post
     * parameter data along with the relayState parameter.
     * @param $samlRequest
     * @param $sendRelayState
     * @param $idpUrl
     */
    protected function sendHTTPPostRequest($samlRequest, $sendRelayState, $sloUrl)
    {
        $privateKeyPath = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $this->spUtility->log_debug("BaseAction: sendHTTPPostRequest: start");
        $publicCertPath = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $signedXML = SAML2Utilities::signXML($samlRequest, $this->spUtility->getFileContents($publicCertPath),
            $this->spUtility->getFileContents($privateKeyPath), 'NameIDPolicy');
        $base64EncodedXML = base64_encode($signedXML);
        //post request
        ob_clean();
        print_r("  <html><head><script src='https://code.jquery.com/jquery-1.11.3.min.js'></script><script type=\"text/javascript\">
                    $(function(){document.forms['saml-request-form'].submit();});</script></head>
                    <body>
                        Please wait...
                        <form action=\"" . $sloUrl . "\" method=\"post\" id=\"saml-request-form\" style=\"display:none;\">
                            <input type=\"hidden\" name=\"SAMLRequest\" value=\"" . $base64EncodedXML . "\" />
                            <input type=\"hidden\" name=\"RelayState\" value=\"" . htmlentities($sendRelayState) . "\" />
                        </form>
                    </body>
                </html>");
        return;
    }

    protected function sendHTTPPostAuthRequest($samlRequest, $sendRelayState, $sloUrl, $params)
    {
        $privateKeyPath = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $this->spUtility->log_debug("BaseAction: sendHTTPPostAuthRequest: start");
        $publicCertPath = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $signedXML = SAML2Utilities::signXML($samlRequest, $this->spUtility->getFileContents($publicCertPath),
            $this->spUtility->getFileContents($privateKeyPath), 'NameIDPolicy');
        $base64EncodedXML = base64_encode($signedXML);
        //post request
        ob_clean();
        print_r("  <html><head><script src='https://code.jquery.com/jquery-1.11.3.min.js'></script><script type=\"text/javascript\">
                    $(function(){document.forms['saml-request-form'].submit();});</script></head>
                    <body>
                        Please wait...
                        <form action=\"" . $sloUrl . "\" method=\"post\" id=\"saml-request-form\" style=\"display:none;\">
                            <input type=\"hidden\" name=\"SAMLRequest\" value=\"" . $base64EncodedXML . "\" />
                            <input type=\"hidden\" name=\"RelayState\" value=\"" . htmlentities($sendRelayState) . "\" />
                        </form>
                    </body>
                </html>");
        return;
    }

    /**
     * This function is used to send Logout Response as a post Parameter.
     * Logout Response is sent in the post parameter if the binding is
     * set as HTTP Post.
     *
     * @param $samlResponse
     * @param $sendRelayState
     * @param $ssoUrl
     */
    protected function sendHTTPPostResponse($samlResponse, $sendRelayState, $ssoUrl)
    {
        $this->spUtility->log_debug("BaseAction: sendHTTPPostResponse: start");
        $privateKeyPath = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $publicCertPath = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $signedXML = SAML2Utilities::signXML($samlResponse, $this->spUtility->getFileContents($publicCertPath),
            $this->spUtility->getFileContents($privateKeyPath), 'Status');
        $base64EncodedXML = base64_encode($signedXML);
        //post request
        ob_clean();
        print_r("  <html><head><script src='https://code.jquery.com/jquery-1.11.3.min.js'></script><script type=\"text/javascript\">
                    $(function(){document.forms['saml-request-form'].submit();});</script></head>
                    <body>
                        Please wait...
                        <form action=\"" . $ssoUrl . "\" method=\"post\" id=\"saml-request-form\" style=\"display:none;\">
                            <input type=\"hidden\" name=\"SAMLResponse\" value=\"" . $base64EncodedXML . "\" />
                            <input type=\"hidden\" name=\"RelayState\" value=\"" . htmlentities($sendRelayState) . "\" />
                        </form>
                    </body>
                </html>");
        return;
    }
}
