<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Exception\MissingAttributesException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
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
    public function __construct(Context $gt, SPUtility $fR, ShowTestResultsAction $Ip, ProcessUserAction $JJ, StoreManagerInterface $VO, ResultFactory $ps, ResponseFactory $Jv)
    {
        $this->testAction = $Ip;
        $this->processUserAction = $JJ;
        $this->storeManager = $VO;
        parent::__construct($gt, $fR, $VO, $ps, $Jv);
    }
    public function execute()
    {
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\143\150\x65\x63\153\101\164\x74\x72\x69\x62\165\164\145\115\141\160\160\151\156\x67\101\x63\164\x69\157\156\x3a\x20", $rq);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\x64\x70\137\x6e\141\x6d\145"] === $rq)) {
                goto RG;
            }
            $hR = $ub->getData();
            RG:
            eb:
        }
        KI:
        $this->emailAttribute = $hR["\x65\155\x61\x69\154\137\x61\x74\164\162\x69\142\x75\164\x65"];
        $this->usernameAttribute = $hR["\165\163\x65\162\x6e\x61\155\x65\x5f\x61\x74\x74\x72\x69\x62\165\x74\x65"];
        $this->firstName = $hR["\146\151\162\x73\x74\156\141\x6d\145\x5f\141\x74\164\162\151\142\165\x74\x65"];
        $this->lastName = $hR["\x6c\x61\163\x74\156\x61\155\145\137\141\x74\x74\x72\x69\x62\x75\x74\x65"];
        $this->checkIfMatchBy = $hR["\x63\162\x65\x61\164\145\137\155\x61\x67\x65\156\164\x6f\x5f\x61\143\x63\x6f\165\156\x74\137\x62\x79"];
        $this->groupName = $hR["\147\x72\x6f\165\160\137\141\164\164\x72\151\x62\x75\164\x65"];
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\143\x68\x65\143\153\x61\x74\164\x72\x69\x62\165\x74\x65\x6d\141\160\160\151\x6e\x67\x41\143\164\151\x6f\x6e", $rq);
        $bC = current(current($this->samlResponse->getAssertions())->getNameId());
        $q_ = current($this->samlResponse->getAssertions())->getAttributes();
        if (filter_var($bC, FILTER_VALIDATE_EMAIL)) {
            goto hW;
        }
        $bC = $this->findUserEmail($q_);
        hW:
        if (!(!$bC && !($this->relayState == self::TEST_VALIDATE_RELAYSTATE))) {
            goto DH;
        }
        DH:
        $Ay = $this->storeManager->getWebsite()->getCode();
        $q_["\x4e\141\155\145\111\104"] = array($bC);
        $lr = current($this->samlResponse->getAssertions())->getSessionIndex();
        $this->spUtility->setSessionData("\163\145\163\x73\x69\157\x6e\151\156", $lr);
        $this->moSAMLcheckMapping($q_, $lr);
    }
    private function findUserEmail($q_)
    {
        if (!$q_) {
            goto yK;
        }
        foreach ($q_ as $VP) {
            if (!is_array($VP)) {
                goto NE;
            }
            $VP = $this->findUserEmail($VP);
            NE:
            if (!filter_var($VP, FILTER_VALIDATE_EMAIL)) {
                goto BG;
            }
            return $VP;
            BG:
            zy:
        }
        xM:
        return '';
        yK:
    }
    private function moSAMLcheckMapping($q_, $lr)
    {
        if (!empty($q_)) {
            goto rO;
        }
        throw new MissingAttributesException();
        rO:
        if (!$this->spUtility->isBlank($this->checkIfMatchBy)) {
            goto bF;
        }
        $this->checkIfMatchBy = SPConstants::DEFAULT_MAP_BY;
        bF:
        $this->processUserName($q_);
        $this->processEmail($q_);
        $this->processGroupName($q_);
        $this->processResult($q_, $lr, $q_["\x4e\141\x6d\145\111\104"]);
    }
    private function processUserName(&$q_)
    {
        if (!empty($q_[$this->usernameAttribute])) {
            goto Oc;
        }
        $q_[$this->usernameAttribute][0] = $this->checkIfMatchBy == SPConstants::DEFAULT_MAP_USERN ? $q_["\116\141\155\x65\x49\104"][0] : null;
        Oc:
    }
    private function processEmail(&$q_)
    {
        if (!empty($q_[$this->emailAttribute])) {
            goto JG;
        }
        $q_[$this->emailAttribute][0] = $this->checkIfMatchBy == SPConstants::DEFAULT_MAP_EMAIL ? $q_["\116\141\x6d\145\111\104"][0] : null;
        JG:
    }
    private function processGroupName(&$q_)
    {
        if (!empty($q_[$this->groupName])) {
            goto kn;
        }
        $this->groupName = array();
        kn:
    }
    private function processResult($q_, $lr, $Au)
    {
        switch ($this->relayState) {
            case self::TEST_VALIDATE_RELAYSTATE:
                $this->testAction->setAttrs($q_)->setNameId($Au[0])->execute();
                goto Vg;
            default:
                $this->processUserAction->setAttrs($q_)->setRelayState($this->relayState)->setSessionIndex($lr)->execute();
                goto Vg;
        }
        jB:
        Vg:
    }
    public function setRelayState($Nf)
    {
        $this->relayState = $Nf;
        return $this;
    }
    public function setSamlResponse($yO)
    {
        $this->samlResponse = $yO;
        return $this;
    }
    private function processFirstName(&$q_)
    {
        if (!empty($q_[$this->firstName])) {
            goto Ay;
        }
        $Dq = explode("\100", $q_["\116\141\x6d\145\x49\x44"][0]);
        $q_[$this->firstName][0] = $Dq[0];
        $this->spUtility->log_debug("\x20\151\x6e\163\x69\144\145\40\103\x68\145\x63\x6b\101\164\164\x72\x69\142\x75\x74\145\x4d\x61\x70\x70\x69\x6e\147\x41\143\x74\151\x6f\x6e\x20\72\x20\160\x72\157\x63\145\x73\x73\106\x69\x72\163\164\116\x61\155\145\x3a\x20\x43\150\x61\x6e\147\x65\144\x20\x66\151\162\163\164\116\141\155\x65\72\x20" . $q_[$this->firstName][0]);
        Ay:
    }
    private function processLastName(&$q_)
    {
        if (!empty($q_[$this->lastName])) {
            goto Wt;
        }
        $Dq = explode("\x40", $q_["\116\x61\x6d\x65\111\104"][0]);
        $q_[$this->lastName][0] = $Dq[1];
        $this->spUtility->log_debug("\40\151\156\x73\151\x64\x65\x20\103\150\x65\143\153\101\x74\x74\162\151\x62\x75\x74\x65\115\x61\160\x70\x69\156\x67\x41\x63\164\151\x6f\156\x20\72\40\x70\162\157\x63\x65\163\x73\114\141\x73\164\116\x61\155\145\72\40\103\150\x61\x6e\147\x65\144\40\x4c\141\163\164\116\x61\x6d\x65\72\x20" . $q_[$this->lastName][0]);
        Wt:
    }
}
