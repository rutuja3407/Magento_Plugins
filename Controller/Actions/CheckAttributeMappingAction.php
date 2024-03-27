<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Exception\MissingAttributesException;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Actions\ShowTestResultsAction;
use MiniOrange\SP\Controller\Actions\ProcessUserAction;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseFactory;
class CheckAttributeMappingAction extends BaseAction
{
    const TEST_VALIDATE_RELAYSTATE = SPConstants::TEST_RELAYSTATE;
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
    protected $storeManager;
    public function __construct(Context $Gc, SPUtility $Kx, ShowTestResultsAction $p2, ProcessUserAction $UC, StoreManagerInterface $Wl, ResultFactory $UZ, ResponseFactory $XF)
    {
        $this->testAction = $p2;
        $this->processUserAction = $UC;
        $this->storeManager = $Wl;
        parent::__construct($Gc, $Kx, $Wl, $UZ, $XF);
    }
    public function execute()
    {
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\143\150\x65\x63\x6b\x41\164\164\162\151\142\x75\164\145\x4d\141\160\x70\151\156\147\101\143\164\x69\x6f\x6e\72\40", $rQ);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\x64\160\137\156\141\x6d\145"] === $rQ)) {
                goto Nc;
            }
            $ft = $fR->getData();
            Nc:
            G2:
        }
        sY:
        $this->emailAttribute = $ft["\145\x6d\x61\x69\154\137\x61\164\164\162\151\142\x75\164\145"];
        $this->usernameAttribute = $ft["\x75\163\145\x72\x6e\141\155\x65\137\x61\164\164\162\x69\x62\165\164\x65"];
        $this->firstName = $ft["\x66\151\x72\163\164\x6e\141\x6d\145\x5f\x61\x74\164\162\151\142\x75\164\x65"];
        $this->lastName = $ft["\154\x61\x73\164\156\141\155\x65\137\141\x74\x74\x72\x69\142\x75\164\145"];
        $this->checkIfMatchBy = $ft["\143\x72\145\141\164\145\137\155\x61\147\x65\156\164\157\137\x61\x63\143\157\165\156\164\137\x62\171"];
        $this->groupName = $ft["\x67\x72\157\x75\x70\x5f\141\164\164\162\x69\142\165\x74\x65"];
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\143\x68\x65\x63\x6b\x61\164\x74\162\151\142\x75\x74\x65\x6d\141\x70\160\x69\x6e\147\x41\143\x74\151\157\156", $rQ);
        $XQ = current(current($this->samlResponse->getAssertions())->getNameId());
        $D_ = current($this->samlResponse->getAssertions())->getAttributes();
        if (filter_var($XQ, FILTER_VALIDATE_EMAIL)) {
            goto wM;
        }
        $XQ = $this->findUserEmail($D_);
        wM:
        if (!(!$XQ && !($this->relayState == self::TEST_VALIDATE_RELAYSTATE))) {
            goto CS;
        }
        CS:
        $eC = $this->storeManager->getWebsite()->getCode();
        $D_["\x4e\141\155\x65\x49\104"] = array($XQ);
        $SK = current($this->samlResponse->getAssertions())->getSessionIndex();
        $this->spUtility->setSessionData("\x73\x65\x73\x73\151\157\x6e\151\x6e", $SK);
        $this->moSAMLcheckMapping($D_, $SK);
    }
    private function findUserEmail($D_)
    {
        if (!$D_) {
            goto Wm;
        }
        foreach ($D_ as $Yk) {
            if (!is_array($Yk)) {
                goto MD;
            }
            $Yk = $this->findUserEmail($Yk);
            MD:
            if (!filter_var($Yk, FILTER_VALIDATE_EMAIL)) {
                goto dE;
            }
            return $Yk;
            dE:
            e2:
        }
        OK:
        return '';
        Wm:
    }
    private function moSAMLcheckMapping($D_, $SK)
    {
        if (!empty($D_)) {
            goto Bf;
        }
        throw new MissingAttributesException();
        Bf:
        if (!$this->spUtility->isBlank($this->checkIfMatchBy)) {
            goto S3;
        }
        $this->checkIfMatchBy = SPConstants::DEFAULT_MAP_BY;
        S3:
        $this->processUserName($D_);
        $this->processEmail($D_);
        $this->processGroupName($D_);
        $this->processResult($D_, $SK, $D_["\116\141\155\x65\111\x44"]);
    }
    private function processResult($D_, $SK, $Q5)
    {
        switch ($this->relayState) {
            case self::TEST_VALIDATE_RELAYSTATE:
                $this->testAction->setAttrs($D_)->setNameId($Q5[0])->execute();
                goto SS;
            default:
                $this->processUserAction->setAttrs($D_)->setRelayState($this->relayState)->setSessionIndex($SK)->execute();
                goto SS;
        }
        wH:
        SS:
    }
    private function processFirstName(&$D_)
    {
        if (!empty($D_[$this->firstName])) {
            goto MH;
        }
        $xC = explode("\100", $D_["\116\141\x6d\145\111\x44"][0]);
        $D_[$this->firstName][0] = $xC[0];
        $this->spUtility->log_debug("\40\151\x6e\x73\x69\x64\145\x20\103\x68\x65\x63\x6b\x41\x74\164\162\x69\x62\165\x74\145\x4d\x61\x70\x70\151\x6e\147\x41\x63\164\151\157\156\40\72\40\x70\162\157\143\x65\163\x73\x46\x69\x72\163\x74\x4e\141\x6d\145\x3a\x20\103\150\141\156\x67\x65\144\40\x66\x69\x72\163\x74\x4e\141\x6d\145\x3a\40" . $D_[$this->firstName][0]);
        MH:
    }
    private function processLastName(&$D_)
    {
        if (!empty($D_[$this->lastName])) {
            goto n6;
        }
        $xC = explode("\x40", $D_["\x4e\x61\x6d\x65\x49\x44"][0]);
        $D_[$this->lastName][0] = $xC[1];
        $this->spUtility->log_debug("\x20\151\156\x73\151\x64\145\x20\x43\150\145\143\x6b\x41\164\164\162\x69\x62\165\164\145\x4d\141\160\x70\x69\156\x67\x41\x63\x74\151\157\x6e\x20\x3a\40\x70\162\157\143\145\x73\x73\x4c\x61\163\x74\116\x61\155\x65\72\40\x43\x68\141\x6e\147\x65\x64\x20\114\x61\163\164\x4e\x61\x6d\145\72\40" . $D_[$this->lastName][0]);
        n6:
    }
    private function processUserName(&$D_)
    {
        if (!empty($D_[$this->usernameAttribute])) {
            goto ON;
        }
        $D_[$this->usernameAttribute][0] = $this->checkIfMatchBy == SPConstants::DEFAULT_MAP_USERN ? $D_["\116\x61\155\x65\x49\104"][0] : null;
        ON:
    }
    private function processEmail(&$D_)
    {
        if (!empty($D_[$this->emailAttribute])) {
            goto MM;
        }
        $D_[$this->emailAttribute][0] = $this->checkIfMatchBy == SPConstants::DEFAULT_MAP_EMAIL ? $D_["\116\x61\155\x65\x49\104"][0] : null;
        MM:
    }
    private function processGroupName(&$D_)
    {
        if (!empty($D_[$this->groupName])) {
            goto sE;
        }
        $this->groupName = array();
        sE:
    }
    public function setSamlResponse($CN)
    {
        $this->samlResponse = $CN;
        return $this;
    }
    public function setRelayState($qY)
    {
        $this->relayState = $qY;
        return $this;
    }
}
