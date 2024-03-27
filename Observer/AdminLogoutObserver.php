<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Controller\Actions\SendLogoutRequest;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class AdminLogoutObserver implements ObserverInterface
{
    protected $userFactory;
    private $messageManager;
    private $logger;
    private $readResponseAction;
    private $spUtility;
    private $logoutRequestAction;
    public function __construct(ManagerInterface $b_, LoggerInterface $kU, ReadResponseAction $kn, SPUtility $fR, SendLogoutRequest $GL, UserFactory $Do)
    {
        $this->messageManager = $b_;
        $this->logger = $kU;
        $this->readResponseAction = $kn;
        $this->spUtility = $fR;
        $this->logoutRequestAction = $GL;
        $this->userFactory = $Do;
    }
    public function execute(Observer $UJ)
    {
        $gf = $this->spUtility->getAdminSessionData("\141\144\x6d\x69\156\137\160\157\x73\x74\137\154\x6f\147\x6f\165\164");
        $F_ = \Magento\Framework\App\ObjectManager::getInstance();
        $Qi = $F_->create("\x4d\141\x67\x65\156\164\x6f\134\x42\x61\143\153\x65\x6e\144\x5c\x4d\157\x64\x65\154\x5c\125\162\154\x49\x6e\164\x65\162\146\141\x63\145");
        $Hy = $Qi->getCurrentUrl();
        $this->spUtility->setAdminSessionData("\141\x64\155\151\156\x5f\154\157\147\157\x75\x74\137\165\x72\x6c", $Hy);
        $this->spUtility->log_debug("\111\x6e\40\101\144\155\151\x6e\40\x70\162\145\40\114\157\147\157\165\x74\40\x4f\x62\x73\145\162\x76\145\x72\x20{$gf}");
        if (!$gf) {
            goto wT;
        }
        try {
            $this->spUtility->setAdminSessionData("\x61\x64\x6d\151\156\x5f\160\157\x73\164\x5f\154\x6f\x67\157\165\x74", NULL);
            $Sh = $this->spUtility->getAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            $this->spUtility->log_debug("\x49\x6e\x20\x61\144\155\151\156\x20\114\x6f\x67\157\x75\164\x20\117\142\x73\x65\x72\166\145\162\x20\125\x73\x65\162\163\40\x44\x65\x74\x61\151\154\x73", $Sh);
            if (!($this->spUtility->isBlank($Sh) && $this->spUtility->isUserLoggedIn())) {
                goto sK;
            }
            $or["\x61\144\155\151\156"] = TRUE;
            $or["\151\144"] = $this->spUtility->getCurrentAdminUser()->getId();
            $this->spUtility->setAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, $or);
            $this->spUtility->log_debug("\111\x6e\40\x41\144\x6d\151\x6e\40\x4c\x6f\x67\157\x75\x74\40\x4f\142\163\x65\162\166\x65\x72\x20\x49\146\50\x55\163\x65\x72\x73\104\145\x74\x61\151\154\163\x20\x62\x6c\x61\x6e\x6b\40\x61\156\144\x20\151\163\125\x73\x65\162\x4c\x6f\x67\147\x65\x64\x49\116");
            sK:
            $Sh = $this->spUtility->getAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            if ($this->spUtility->isBlank($Sh)) {
                goto pH;
            }
            $this->spUtility->log_debug("\163\x65\156\144\40\x6c\x6f\147\157\165\x74\40\x72\145\161\x75\145\163\x74\x20");
            $user = $this->userFactory->create()->load($Sh["\x69\144"]);
            $Au = $user->getEmail();
            $this->logoutRequestAction->setIsAdmin($Sh["\141\x64\155\151\156"])->setrelay($Hy)->setUserId($Sh["\151\x64"])->setNameId($Au)->execute();
            pH:
            $Nq = $this->spUtility->getAdminSessionData(SPConstants::SEND_RESPONSE, TRUE);
            $Bj = $this->spUtility->getAdminSessionData(SPConstants::LOGOUT_REQUEST_ID, TRUE);
            $this->spUtility->log_debug("\163\145\x6e\x64\x4c\157\x67\157\165\164\x52\x65\x73\x70\157\x6e\x73\145\x20", $Nq);
            $this->spUtility->log_debug("\x72\145\x71\x75\x65\163\164\x49\x64\x20", $Bj);
            if (!$Nq) {
                goto XW;
            }
            $this->spUtility->log_debug("\111\156\163\x69\x64\x65\40\x69\146\x20", $Nq);
            $this->logoutResponseAction->setRequestId($Bj)->execute();
            XW:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->spUtility->log_debug($IR->getMessage());
        }
        wT:
    }
}
