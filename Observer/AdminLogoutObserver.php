<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\ObserverInterface;
use MiniOrange\SP\Helper\SPMessages;
use Magento\Framework\Event\Observer;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Actions\SendLogoutRequest;
use Magento\User\Model\UserFactory;
class AdminLogoutObserver implements ObserverInterface
{
    private $messageManager;
    private $logger;
    private $readResponseAction;
    private $spUtility;
    private $logoutRequestAction;
    protected $userFactory;
    public function __construct(ManagerInterface $c0, LoggerInterface $hI, ReadResponseAction $Ei, SPUtility $Kx, SendLogoutRequest $oN, UserFactory $t2)
    {
        $this->messageManager = $c0;
        $this->logger = $hI;
        $this->readResponseAction = $Ei;
        $this->spUtility = $Kx;
        $this->logoutRequestAction = $oN;
        $this->userFactory = $t2;
    }
    public function execute(Observer $ug)
    {
        $Yt = $this->spUtility->getAdminSessionData("\x61\144\155\x69\x6e\x5f\160\x6f\163\164\137\154\x6f\147\x6f\x75\x74");
        $c5 = \Magento\Framework\App\ObjectManager::getInstance();
        $cs = $c5->create("\x4d\141\x67\x65\x6e\164\157\x5c\x42\x61\x63\153\x65\156\x64\x5c\115\157\144\145\x6c\134\125\162\154\111\156\164\x65\x72\146\141\143\x65");
        $Bt = $cs->getCurrentUrl();
        $this->spUtility->setAdminSessionData("\141\144\155\x69\156\137\x6c\x6f\147\x6f\x75\164\x5f\x75\162\154", $Bt);
        $this->spUtility->log_debug("\x49\156\40\x41\144\x6d\x69\x6e\40\x70\x72\x65\40\x4c\157\147\157\x75\x74\40\x4f\142\x73\x65\x72\x76\145\x72\x20{$Yt}");
        if (!$Yt) {
            goto QEI;
        }
        try {
            $this->spUtility->setAdminSessionData("\141\x64\x6d\151\156\x5f\x70\157\163\164\x5f\x6c\157\x67\157\165\x74", NULL);
            $kF = $this->spUtility->getAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            $this->spUtility->log_debug("\x49\x6e\x20\x61\x64\x6d\151\x6e\x20\114\157\147\157\x75\x74\40\x4f\142\163\145\162\x76\145\162\x20\x55\163\x65\x72\163\x20\104\x65\164\141\x69\x6c\x73", $kF);
            if (!($this->spUtility->isBlank($kF) && $this->spUtility->isUserLoggedIn())) {
                goto CIW;
            }
            $F2["\x61\x64\x6d\x69\156"] = TRUE;
            $F2["\x69\x64"] = $this->spUtility->getCurrentAdminUser()->getId();
            $this->spUtility->setAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, $F2);
            $this->spUtility->log_debug("\111\x6e\x20\101\144\x6d\x69\x6e\x20\114\x6f\x67\157\x75\164\40\x4f\142\x73\145\x72\166\x65\162\40\x49\x66\x28\125\x73\145\162\163\x44\x65\164\x61\x69\154\163\x20\142\154\x61\x6e\153\40\141\156\x64\x20\151\163\125\x73\x65\162\x4c\x6f\x67\x67\145\x64\111\x4e");
            CIW:
            $kF = $this->spUtility->getAdminSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            if ($this->spUtility->isBlank($kF)) {
                goto NKb;
            }
            $this->spUtility->log_debug("\x73\145\x6e\144\x20\154\x6f\147\x6f\165\164\40\x72\145\x71\x75\x65\163\x74\x20");
            $user = $this->userFactory->create()->load($kF["\151\144"]);
            $Q5 = $user->getEmail();
            $this->logoutRequestAction->setIsAdmin($kF["\x61\x64\155\x69\x6e"])->setrelay($Bt)->setUserId($kF["\x69\x64"])->setNameId($Q5)->execute();
            NKb:
            $Ze = $this->spUtility->getAdminSessionData(SPConstants::SEND_RESPONSE, TRUE);
            $tO = $this->spUtility->getAdminSessionData(SPConstants::LOGOUT_REQUEST_ID, TRUE);
            $this->spUtility->log_debug("\163\145\156\144\114\157\x67\x6f\165\164\122\x65\163\160\x6f\x6e\163\x65\40", $Ze);
            $this->spUtility->log_debug("\162\145\161\165\145\163\164\x49\144\x20", $tO);
            if (!$Ze) {
                goto uJd;
            }
            $this->spUtility->log_debug("\x49\156\x73\x69\x64\145\x20\151\146\x20", $Ze);
            $this->logoutResponseAction->setRequestId($tO)->execute();
            uJd:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->spUtility->log_debug($sS->getMessage());
        }
        QEI:
    }
}
