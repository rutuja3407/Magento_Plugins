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
    public function __construct(Context $gt, QuoteFactory $Bg, SPUtility $fR, \Magento\Customer\Model\Session $fD, ResponseFactory $Jv, StoreManagerInterface $VO, ResultFactory $ps, RequestInterface $E1, TokenFactory $qA)
    {
        $this->customerSession = $fD;
        $this->responseFactory = $Jv;
        $this->tokenModelFactory = $qA;
        $this->request = $E1;
        parent::__construct($gt, $fR, $VO, $ps, $Jv);
    }
    public function execute()
    {
        $v4 = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x43\x75\x73\164\x6f\155\145\x72\114\157\x67\x69\156\101\x63\x74\x69\x6f\x6e", $v4);
        $hR = $this->getIdpDetails($v4);
        $this->spUtility->log_debug("\103\165\x73\164\x6f\155\x65\162\114\x6f\147\x69\x6e\101\x63\x74\151\x6f\x6e\x20\72\40\145\170\x65\143\x75\164\x65\x3a\x20\x72\145\154\x61\171\x73\x74\x61\x74\145\72\x20" . $this->relayState);
        $this->customerSession->setCustomerAsLoggedIn($this->user);
        $Sn = $this->spUtility->getCustomer($this->customerId);
        $vH = $this->spUtility->getStoreById($Sn->getStoreId());
        $kv = new DataObject(["\143\x75\163\x74\157\x6d\145\x72\x5f\151\x64" => $this->customerId]);
        $Te = $this->request->getParams();
        if (empty($Te["\160\x61\x72\x65\x6e\x74\137\141\143\143\x6f\x75\x6e\x74\137\151\144"])) {
            goto vk;
        }
        $this->customerSession->setIsAdmin(true);
        $this->_eventManager->dispatch("\143\165\163\x74\x6f\155\x65\162\137\x73\x61\x76\145\137\141\x66\x74\x65\x72", ["\143\x75\x73\x74\x6f\155\x65\162" => $Te]);
        vk:
        $Sq = $this->customerSession->isLoggedIn() && $this->customerSession->getIsAdmin();
        $this->_eventManager->dispatch("\163\x70\x5f\x63\165\x73\x74\157\155\x65\x72\137\154\x6f\x67\151\156", ["\143\x75\x73\x74\x6f\x6d\x65\x72\x5f\x64\x61\x74\141" => $kv]);
        $this->spUtility->log_debug("\103\165\163\164\157\x6d\145\x72\x4c\x6f\x67\151\156\x41\143\164\x69\157\156\x20\x3a\40\x65\x78\145\x63\x75\x74\x65\x28\51\x3a\40\x20\x45\166\145\156\x74\x20\104\x69\163\x70\141\164\x63\150\x65\144");
        $Rs = $this->spUtility->isBlank($this->relayState) ? $vH->getBaseUrl() : $this->relayState;
        $this->spUtility->log_debug("\103\165\163\164\x6f\155\x65\162\x4c\x6f\x67\151\156\101\143\x74\x69\x6f\156\40\x3a\40\145\x78\x65\x63\165\x74\145\x3a\40\x72\145\x64\x69\x72\145\143\x74\125\x72\154\x2e\x20" . $Rs);
        if (!(!empty($hR) && $hR["\x6d\157\x5f\x73\141\155\154\137\150\145\141\x64\x6c\145\163\x73\137\163\x73\x6f"])) {
            goto K7;
        }
        if (!empty($hR["\x6d\x6f\137\163\141\155\154\x5f\146\x72\x6f\x6e\164\x65\x6e\x64\137\160\157\163\164\x5f\165\x72\x6c"])) {
            goto v6;
        }
        $this->spUtility->messageManager->addErrorMessage("\x59\157\165\40\150\141\x76\145\40\x65\x6e\141\x62\154\x65\x64\x20\x74\x68\x65\x20\110\145\141\x64\x6c\145\x73\163\123\123\117\x20\142\x75\164\40\x46\162\157\x6e\164\145\x6e\x64\40\x55\x52\114\40\151\x73\40\x6e\157\x74\x20\x70\x72\x6f\166\151\x64\145\144\x20\151\156\40\164\150\145\40\x63\x6f\156\x66\x69\147\165\x72\x61\x74\151\x6f\x6e\56");
        $this->responseFactory->create()->setRedirect($Rs)->sendResponse();
        exit;
        goto Yt;
        v6:
        $this->handleHeadlessSSO($hR);
        Yt:
        K7:
        $this->spUtility->messageManager->addSuccessMessage("\x59\x6f\165\40\x61\162\145\x20\154\x6f\147\147\145\144\x20\151\x6e\x20\x53\x75\143\143\145\163\x73\x66\165\x6c\x6c\x79\56");
        $this->responseFactory->create()->setRedirect($Rs)->sendResponse();
        exit;
    }
    protected function getIdpDetails($v4)
    {
        $yG = $this->spUtility->getIDPApps();
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\144\160\137\x6e\141\x6d\145"] === $v4)) {
                goto E4;
            }
            return $ub->getData();
            E4:
            Ca:
        }
        ED:
        return null;
    }
    protected function handleHeadlessSSO($hR)
    {
        $this->spUtility->log_debug("\103\x75\x73\164\x6f\155\145\x72\x4c\157\147\x69\156\x41\143\x74\x69\x6f\156\x3a\x20\110\x65\x61\x64\x4c\x65\163\163\123\123\x4f\40\105\156\x61\142\x6c\x65\144\40\x73\145\x73\x73\x69\x6f\156\40");
        if ($this->customerSession->isLoggedIn()) {
            goto Qz;
        }
        $this->spUtility->log_debug("\x43\x75\x73\164\157\155\145\162\x4c\157\x67\x69\156\x41\143\164\151\x6f\156\72\40\143\x75\163\x74\157\x6d\145\x72\40\123\x65\x73\x73\x69\157\x6e\40\x69\x73\x20\156\x6f\164\x20\x43\162\x65\x61\x74\x65\x64");
        $Z2 = $this->generateFrontendPostUrl($hR["\155\x6f\137\163\141\x6d\154\137\146\x72\157\156\x74\x65\x6e\x64\x5f\x70\157\163\164\137\165\162\x6c"], null, "\x43\x75\x73\164\x6f\x6d\145\162\40\x6e\x6f\x74\40\154\x6f\147\x67\x65\x64\40\x69\156");
        goto tl;
        Qz:
        $this->spUtility->log_debug("\103\165\x73\164\x6f\155\145\x72\x4c\x6f\x67\x69\x6e\101\143\x74\x69\157\x6e\72\40\103\x75\163\164\x6f\x6d\x65\x72\x20\163\x65\163\163\x69\157\156\x20\x65\170\x69\x73\x74\x73");
        $oG = $this->customerSession->getCustomer()->getId();
        $this->spUtility->log_debug("\x43\165\163\164\x6f\155\145\x72\114\x6f\147\151\156\x41\x63\164\151\157\x6e\x3a\40\103\x75\163\164\157\x6d\x65\162\x49\x44\40", $oG);
        $tw = $this->generateCustomerToken($oG);
        if ($tw) {
            goto NF;
        }
        $this->spUtility->log_debug("\x43\165\x73\x74\157\155\145\162\114\x6f\147\151\156\101\x63\164\x69\157\x6e\72\40\143\165\163\164\157\x6d\x65\162\40\124\157\153\145\156\40\151\x73\40\x45\x6d\x70\164\x79\40");
        $Z2 = $this->generateFrontendPostUrl($hR["\155\157\x5f\x73\x61\155\x6c\137\x66\162\157\x6e\x74\145\x6e\144\137\x70\157\163\x74\137\x75\162\x6c"], null, "\106\141\151\x6c\145\144\40\x74\157\40\x67\145\x6e\145\x72\141\164\145\x20\x63\165\x73\x74\x6f\155\145\x72\x20\164\x6f\x6b\145\x6e");
        goto VL;
        NF:
        $Z2 = $this->generateFrontendPostUrl($hR["\x6d\157\x5f\163\141\x6d\154\137\x66\x72\x6f\156\164\x65\156\x64\137\x70\x6f\163\164\137\x75\162\154"], $tw);
        VL:
        tl:
        $this->responseFactory->create()->setRedirect($Z2)->sendResponse();
        exit;
    }
    private function generateCustomerToken($oG)
    {
        try {
            $tw = $this->tokenModelFactory->create()->createCustomerToken($oG)->getToken();
            $this->spUtility->log_debug("\103\165\x73\164\x6f\155\145\162\114\157\x67\151\156\x41\x63\x74\x69\x6f\x6e\x3a\40\103\165\163\164\x6f\155\x65\162\40\x74\157\x6b\145\x6e\40\143\162\145\x61\164\145\x64");
            return $tw;
        } catch (\Exception $IR) {
            $this->spUtility->log_debug("\x43\x75\x73\x74\157\155\145\x72\114\x6f\x67\x69\156\x41\x63\x74\151\157\x6e\x3a\40\x54\157\x6b\x65\x6e\x20\143\162\x65\141\x74\151\157\156\40\x65\162\162\157\162\x20\55\40" . $IR->getMessage());
            return null;
        }
    }
    protected function generateFrontendPostUrl($Z2, $tw = null, $E3 = array())
    {
        $Te = ["\143\165\163\x74\157\155\x65\162\x5f\x74\157\153\145\x6e" => $tw] + $E3;
        return $Z2 . "\77" . http_build_query($Te);
    }
    public function setUser($user)
    {
        $this->user = $user;
        $this->spUtility->log_debug("\x73\145\164\164\151\156\x67\40\125\163\145\x72\x3a\x20");
        return $this;
    }
    public function setCustomerId($Gh)
    {
        $this->customerId = $Gh;
        $this->spUtility->log_debug("\163\145\x74\x74\x69\x6e\x67\40\x63\165\x73\164\157\x6d\x65\162\111\x64\x3a\x20" . $Gh);
        return $this;
    }
    public function setRelayState($Nf)
    {
        $this->relayState = $Nf;
        return $this;
    }
    public function setAxCompanyId($xt)
    {
        $this->accountId = $xt;
        return $this;
    }
    public function setScope($HN)
    {
        $this->isAdminScope = $HN;
        return $this;
    }
}
