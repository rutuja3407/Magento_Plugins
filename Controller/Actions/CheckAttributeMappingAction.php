<?php

namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Exception\MissingAttributesException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;

/**
 * This class handles checking of the SAML attributes and NameID
 * coming in the response and mapping it to the attribute mapping
 * done in the plugin settings by the admin to update the user.
 */
class CheckAttributeMappingAction extends BaseAction
{
    const TEST_VALIDATE_RELAYSTATE = SPConstants::TEST_RELAYSTATE;
    protected $storeManager;
    private $samlResponse;
    private $relayState;
    private $emailAttribute;
    private $usernameAttribute;
    private $firstName;
    private $lastName;
    private $checkIfMatchBy;
    private $groupName;
    private $testAction;
    private $processUserAction;

    public function __construct(
        Context               $context,
        SPUtility             $spUtility,
        ShowTestResultsAction $testAction,
        ProcessUserAction     $processUserAction,
        StoreManagerInterface $storeManager,
        ResultFactory         $resultFactory,
        ResponseFactory       $responseFactory)
    {
        $this->testAction = $testAction;
        $this->processUserAction = $processUserAction;
        $this->storeManager = $storeManager;
        parent::__construct($context, $spUtility, $storeManager, $resultFactory, $responseFactory);
    }

    /**
     * Execute function to execute the classes function.
     */
    public function execute()
    {
        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("checkAttributeMappingAction: ", $idp_name);
        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idp_name) {
                $idpDetails = $item->getData();
            }
        }
        $this->emailAttribute = $idpDetails['email_attribute'];
        $this->usernameAttribute = $idpDetails['username_attribute'];
        $this->firstName = $idpDetails['firstname_attribute'];
        $this->lastName = $idpDetails['lastname_attribute'];
        $this->checkIfMatchBy = $idpDetails['create_magento_account_by'];
        $this->groupName = $idpDetails['group_attribute'];

        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("checkattributemappingAction", $idp_name);
        $ssoemail = current(current($this->samlResponse->getAssertions())->getNameId());
        $attrs = current($this->samlResponse->getAssertions())->getAttributes();

        if (!filter_var($ssoemail, FILTER_VALIDATE_EMAIL)) {
            $ssoemail = $this->findUserEmail($attrs);
        }
        if (!$ssoemail && !($this->relayState == self::TEST_VALIDATE_RELAYSTATE)) {
            // $this->spUtility->log_debug("This Customer email not found.");

            //     $this->messageManager->addErrorMessage(__("Email not found.Please contact your Administrator."));
            //     return $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl().'/customer/account')->sendResponse();
        }
        $sitecode = $this->storeManager->getWebsite()->getCode();
        $attrs['NameID'] = array($ssoemail);
        $sessionIndex = current($this->samlResponse->getAssertions())->getSessionIndex();
        $this->spUtility->setSessionData('sessionin', $sessionIndex);
        //$_COOKIE['sessionin'] = $sessionIndex;

        // setcookie("sessionin", $sessionIndex, time() + (86400 * 30), "/");
        $this->moSAMLcheckMapping($attrs, $sessionIndex);
    }

    private function findUserEmail($attrs)
    {

        if ($attrs) {
            foreach ($attrs as $value) {
                if (is_array($value)) {
                    $value = $this->findUserEmail($value);
                }
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return $value;
                }
            }
            return "";
        }
    }


    /**
     * This function checks the SAML Attribute Mapping done
     * in the plugin and matches it to update the user's
     * attributes.
     *
     * @param $attrs
     * @param $sessionIndex
     * @throws MissingAttributesException;
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function moSAMLcheckMapping($attrs, $sessionIndex)
    {
        if (empty($attrs)) throw new MissingAttributesException;
        if ($this->spUtility->isBlank($this->checkIfMatchBy)) $this->checkIfMatchBy = SPConstants::DEFAULT_MAP_BY;
        $this->processUserName($attrs);
        $this->processEmail($attrs);
        $this->processGroupName($attrs);
        $this->processResult($attrs, $sessionIndex, $attrs['NameID']);
    }

    /**
     * Check if the attribute list has a UserName. If
     * no UserName is found then NameID is considered as
     * the UserName. This is done because Magento needs
     * a UserName for creating a new user.
     *
     * @param $attrs
     */
    private function processUserName(&$attrs)
    {
        if (empty($attrs[$this->usernameAttribute]))
            $attrs[$this->usernameAttribute][0]
                = $this->checkIfMatchBy == SPConstants::DEFAULT_MAP_USERN ? $attrs['NameID'][0] : null;
    }

    /**
     * Check if the attribute list has a Email. If
     * no Email is found then NameID is considered as
     * the Email. This is done because Magento needs
     * a Email for creating a new user.
     *
     * @param $attrs
     */
    private function processEmail(&$attrs)
    {
        if (empty($attrs[$this->emailAttribute]))
            $attrs[$this->emailAttribute][0]
                = $this->checkIfMatchBy == SPConstants::DEFAULT_MAP_EMAIL ? $attrs['NameID'][0] : null;
    }

    /**
     * Check if the attribute list has a Group/Role. If
     * no Group/Role is found then NameID is considered as
     * the Group/Role. This is done because Magento needs
     * a Group/Role for creating a new user.
     *
     * @param $attrs
     */
    private function processGroupName(&$attrs)
    {
        if (empty($attrs[$this->groupName]))
            $this->groupName = array();
    }

    /**
     * Process the result to either show a Test result
     * screen or log/create user in Magento.
     *
     * @param $attrs
     * @param $sessionIndex
     * @param $nameId
     * @throws MissingAttributesException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processResult($attrs, $sessionIndex, $nameId)
    {
        switch ($this->relayState) {
            case self::TEST_VALIDATE_RELAYSTATE :
                $this->testAction->setAttrs($attrs)->setNameId($nameId[0])->execute();
                break;
            default:
                $this->processUserAction->setAttrs($attrs)->setRelayState($this->relayState)
                    ->setSessionIndex($sessionIndex)->execute();
                break;
        }
    }

    /** Setter for the RelayState Parameter */
    public function setRelayState($relayState)
    {
        $this->relayState = $relayState;
        return $this;
    }

    /** Setter for the SAML Response Parameter */
    public function setSamlResponse($samlResponse)
    {
        $this->samlResponse = $samlResponse;
        return $this;
    }

    /**
     * Check if the attribute list has a FirstName. If
     * no firstName is found then NameID is considered as
     * the firstName. This is done because Magento needs
     * a firstName for creating a new user.
     *
     * @param $attrs
     */
    private function processFirstName(&$attrs)
    {
        if (empty($attrs[$this->firstName])) {
            $temp = explode('@', $attrs['NameID'][0]);
            $attrs[$this->firstName][0] = $temp[0];
            $this->spUtility->log_debug(" inside CheckAttributeMappingAction : processFirstName: Changed firstName: " . $attrs[$this->firstName][0]);
        }

    }

    /**
     * Check if the attribute list has a FirstName. If
     * no firstName is found then NameID is considered as
     * the firstName. This is done because Magento needs
     * a firstName for creating a new user.
     *
     * @param $attrs
     */
    private function processLastName(&$attrs)
    {
        if (empty($attrs[$this->lastName])) {
            $temp = explode('@', $attrs['NameID'][0]);
            $attrs[$this->lastName][0] = $temp[1];
            $this->spUtility->log_debug(" inside CheckAttributeMappingAction : processLastName: Changed LastName: " . $attrs[$this->lastName][0]);
        }

    }
}
