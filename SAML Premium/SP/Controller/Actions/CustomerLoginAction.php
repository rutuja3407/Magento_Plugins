<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\UrlInterface;
use Magento\Framework\DataObject;
use Magento\Backend\Model\Auth\Session;
use Magento\Quote\Model\QuoteFactory;
use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\App\ResponseFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
class CustomerLoginAction extends BaseAction
{
    protected $relayState;
    protected $user;
    protected $customerSession;
    protected $responseFactory;
    protected $customerId;
    protected $accountId;
    protected $request;
    public function __construct(Context $Gc, QuoteFactory $Ly, SPUtility $Kx, \Magento\Customer\Model\Session $FK, ResponseFactory $XF, StoreManagerInterface $Wl, ResultFactory $UZ, RequestInterface $nD)
    {
        $this->customerSession = $FK;
        $this->responseFactory = $XF;
        $this->request = $nD;
        parent::__construct($Gc, $Kx, $Wl, $UZ, $XF);
    }
    public function execute()
    {
        $this->spUtility->log_debug("\x43\165\163\164\157\x6d\145\162\114\x6f\x67\151\x6e\x41\143\x74\151\x6f\156\x20\x3a\x20\145\x78\145\x63\x75\164\145\x3a\40\162\x65\154\x61\x79\163\x74\141\164\x65\72\40" . $this->relayState);
        $this->customerSession->setCustomerAsLoggedIn($this->user);
        $Gh = $this->spUtility->getCustomer($this->customerId);
        $SR = $this->spUtility->getStoreById($Gh->getStoreId());
        $EB = new DataObject(["\143\165\x73\164\x6f\x6d\x65\x72\137\151\144" => $this->customerId]);
        $As = $this->request->getParams();
        $this->spUtility->update_customer_id_in_customer_visitor($this->customerId);
        if (empty($As["\160\x61\x72\x65\x6e\164\137\x61\143\143\157\165\156\x74\x5f\151\144"])) {
            goto S_;
        }
        $this->customerSession->setIsAdmin(true);
        $this->_eventManager->dispatch("\x63\x75\163\x74\157\155\145\x72\x5f\x73\x61\x76\x65\137\x61\x66\164\x65\x72", ["\143\x75\x73\164\157\155\145\162" => $As]);
        S_:
        $tZ = $this->customerSession->isLoggedIn() && $this->customerSession->getIsAdmin();
        $this->_eventManager->dispatch("\163\160\x5f\x63\x75\163\x74\x6f\x6d\x65\x72\137\154\x6f\147\151\x6e", ["\143\x75\163\164\x6f\155\x65\x72\137\144\141\164\x61" => $EB]);
        $this->spUtility->log_debug("\x43\x75\x73\x74\157\x6d\145\x72\114\x6f\147\x69\156\x41\143\x74\x69\157\156\x20\x3a\40\145\170\145\143\165\x74\145\50\x29\x3a\40\40\105\x76\x65\156\x74\40\104\151\163\160\141\164\143\150\145\x64");
        $JN = $this->spUtility->isBlank($this->relayState) ? $SR->getBaseUrl() : $this->relayState;
        $this->spUtility->log_debug("\x43\165\x73\164\157\x6d\x65\162\x4c\157\147\151\156\x41\x63\164\151\x6f\156\x20\72\x20\x65\x78\x65\x63\165\x74\x65\x3a\40\x72\145\144\151\162\x65\143\164\125\162\154\56\40" . $JN);
        $this->spUtility->messageManager->addSuccess("\x59\157\165\x20\141\162\145\40\154\x6f\x67\x67\145\x64\x20\x69\x6e\40\123\165\x63\143\145\163\x73\146\x75\154\154\x79\x2e");
        $this->responseFactory->create()->setRedirect($JN)->sendResponse();
        exit;
    }
    public function setUser($user)
    {
        $this->user = $user;
        $this->spUtility->log_debug("\163\x65\164\164\151\x6e\147\x20\125\x73\145\162\x3a\x20");
        return $this;
    }
    public function setCustomerId($lA)
    {
        $this->customerId = $lA;
        $this->spUtility->log_debug("\163\x65\164\x74\x69\x6e\x67\x20\x63\x75\x73\x74\x6f\155\x65\x72\x49\144\x3a\x20" . $lA);
        return $this;
    }
    public function setRelayState($qY)
    {
        $this->relayState = $qY;
        return $this;
    }
    public function setAxCompanyId($ay)
    {
        $this->accountId = $ay;
        return $this;
    }
    public function setScope($X0)
    {
        $this->isAdminScope = $X0;
        return $this;
    }
}
