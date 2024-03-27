<?php

namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;

/**
 * Handles processing of customer verify license key form.
 * Checks if the license key entered by the user is a valid
 * license key for his account or not. If so then activate
 * his license.
 *
 * The main function of this action class is to authenticate
 * the user credentials as provided by calling an API and
 * fetching all of the relevant information of the customer.
 * Store the key, token and email in the database.
 */
class LKAction extends BaseAdminAction
{
    private $REQUEST;

    /**
     * Execute function to execute the classes function.
     * Handles the removing the configured license and customer
     * account from the module by removing the necessary keys
     * and feeing the key.
     *
     * @throws \Exception
     */
    public function removeAccount()
    {
        $this->spUtility->defaultlog('In LKAction');
        if ($this->spUtility->micr() && $this->spUtility->mclv()) {
            $token = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
            $code = AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::SAMLSP_LK), $token);
            $content = json_decode($this->spUtility->update_status($code), true);
            $this->spUtility->mius();
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, '');
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, '');
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, '');
            $this->spUtility->setStoreConfig(SPConstants::API_KEY, '');
            $this->spUtility->setStoreConfig(SPConstants::TOKEN, '');
            $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        } else if ($this->spUtility->micr()) {
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, '');
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, '');
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_EMAIL, '');
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_KEY, '');
            $this->spUtility->setStoreConfig(SPConstants::API_KEY, '');
            $this->spUtility->setStoreConfig(SPConstants::TOKEN, '');
            $this->spUtility->setStoreConfig(SPConstants::REG_STATUS, SPConstants::STATUS_VERIFY_LOGIN);
        }
    }

    /** Setter for the request Parameter */
    public function setRequestParam($request)
    {
        $this->REQUEST = $request;
        return $this;
    }

    /* ===================================================================================================
            THE FUNCTIONS BELOW ARE PREMIUM PLUGIN SPECIFIC AND DIFFER IN THE FREE VERSION
        ===================================================================================================
    */

    /**
     * Execute function to execute the classes function.
     * Handles the license key verify form. Takes the
     * license key given by the Admin and send it to server
     * for  validation.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $this->checkIfRequiredFieldsEmpty(array('lk' => $this->REQUEST));
        $lk = $this->REQUEST['lk'];
        $result = json_decode((string)$this->spUtility->ccl(), true);
        $this->spUtility->defaultlog('ccl response: ' . print_r($result, true));
        $expiry = array_key_exists('licenseExpiry', $result) ? (strtotime($result['licenseExpiry']) === false ? null : strtotime($result['licenseExpiry'])) : null;
        $this->spUtility->log_debug("response from ccl: ", print_r($result, true));
        // Number of subsites received from ccl response.
        if (!empty($result['noOfSubSites']))
            $noofsubsitespurchased = $result['noOfSubSites'];
        else {
            if ($this->spUtility->check_license_plan(3))
                $noofsubsitespurchased = 2;

            elseif ($this->spUtility->check_license_plan(2))
                $noofsubsitespurchased = 1;

            else
                $noofsubsitespurchased = 1;

        }
        $this->spUtility->defaultlog('Number of subsites allowed: ' . $noofsubsitespurchased);
        $this->spUtility->setStoreConfig(SPConstants::WEBSITES_LIMIT, AESEncryption::encrypt_data($noofsubsitespurchased, SPConstants::DEFAULT_TOKEN_VALUE));
        switch ($result['status']) {
            case 'SUCCESS':
                $this->_vlk_success($lk, $expiry);
                break;
            default:
                $this->_vlk_fail();
                break;
        }
    }

    /* ===================================================================================================
                            THE FUNCTIONS BELOW ARE ONLY FOR THE PREMIUM PLUGIN
        ===================================================================================================
    */

    /**
     * Handles the steps to take on successful
     * validation of the license key entered by the Admin.
     *
     * @param $code refers to the code entered by the Admin which has been verified
     * @param $usersCount refers to the number of users puchsed by Admin
     */
    public function _vlk_success($code, $expiry)
    {
        $content = json_decode((string)$this->spUtility->vml(trim($code)), true);
        if (!is_array($content) || empty($content['status']) || empty($content['status'])) {
            $this->messageManager->addErrorMessage(SPMessages::ENTERED_INVALID_KEY);
            return;
        }

        if (strcasecmp($content['status'], 'SUCCESS') == 0) {
            $key = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_LK, AESEncryption::encrypt_data($code, $key));
            $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, AESEncryption::encrypt_data("true", $key));
            $this->spUtility->setStoreConfig(SPConstants::LICENSE_EXPIRY_DATE, AESEncryption::encrypt_data($expiry, $key));
            /** @todo:  run scheduler here for every 3 day check */
            $this->messageManager->addSuccessMessage(SPMessages::LICENSE_VERIFIED);
        } else if (strcasecmp($content['status'], 'FAILED') == 0) {
            if (strcasecmp($content['message'], 'Code has Expired') == 0)
                $this->messageManager->addErrorMessage(SPMessages::LICENSE_KEY_IN_USE);
            else
                $this->messageManager->addErrorMessage(SPMessages::ENTERED_INVALID_KEY);
        } else {
            $this->messageManager->addErrorMessage(SPMessages::ERROR_OCCURRED);
        }
    }


    /**
     * Handles the steps to take on un-successful
     * validation of the license key entered by the Admin.
     */
    public function _vlk_fail()
    {
        $key = $this->spUtility->getStoreConfig(SPConstants::TOKEN);
        $this->spUtility->setStoreConfig(SPConstants::SAMLSP_CKL, AESEncryption::encrypt_data("false", $key));
        $this->messageManager->addErrorMessage(SPMessages::NOT_UPGRADED_YET);
    }
}
