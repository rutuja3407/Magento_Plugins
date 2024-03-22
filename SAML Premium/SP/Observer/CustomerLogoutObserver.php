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
use MiniOrange\SP\Controller\Actions\SendLogoutResponse;
class CustomerLogoutObserver implements ObserverInterface
{
    private $messageManager;
    private $logger;
    private $readResponseAction;
    private $spUtility;
    private $logoutRequestAction;
    private $logoutResponseAction;
    public function __construct(ManagerInterface $c0, LoggerInterface $hI, ReadResponseAction $Ei, SPUtility $Kx, SendLogoutRequest $oN, SendLogoutResponse $v7)
    {
        $this->messageManager = $c0;
        $this->logger = $hI;
        $this->readResponseAction = $Ei;
        $this->spUtility = $Kx;
        $this->logoutRequestAction = $oN;
        $this->logoutResponseAction = $v7;
    }
    public function execute(Observer $ug)
    {
        $Yt = $this->spUtility->getSessionData("\x63\x75\x73\x74\157\155\145\x72\137\x70\157\163\164\137\154\x6f\147\157\165\x74");
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x49\156\40\103\x75\x73\164\157\x6d\145\x72\x20\120\162\145\x20\114\x6f\x67\157\x75\164\40\x4f\142\163\145\x72\166\x65\162\x20{$Yt}");
        if (!$Yt) {
            goto nBi;
        }
        try {
            $this->spUtility->setAdminSessionData("\143\x75\163\164\157\x6d\x65\x72\137\160\x6f\163\x74\137\x6c\x6f\147\x6f\x75\164", NULL);
            $kF = $this->spUtility->getSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            $this->spUtility->log_debug("\x49\156\40\103\165\x73\x74\157\x6d\145\162\x20\x4c\157\147\157\165\164\x20\117\x62\x73\x65\x72\166\145\162\40\x55\163\145\x72\163\40\104\145\x74\141\x69\x6c\x73", $kF);
            if (!($this->spUtility->isBlank($kF) && $this->spUtility->isUserLoggedIn())) {
                goto xi2;
            }
            $F2["\141\144\x6d\x69\x6e"] = FALSE;
            $F2["\x69\x64"] = $this->spUtility->getCurrentUser()->getId();
            $this->spUtility->setSessionData(SPConstants::USER_LOGOUT_DETAIL, $F2);
            $this->spUtility->log_debug("\x49\x6e\40\x43\165\163\164\x6f\x6d\145\x72\40\114\157\147\x6f\x75\164\x20\x4f\142\x73\x65\162\x76\145\162\40\111\x66\50\x55\x73\145\x72\163\x44\x65\x74\141\151\154\x73\40\142\x6c\x61\156\153\40\141\x6e\144\40\x69\x73\125\163\145\x72\114\x6f\147\x67\x65\144\x49\x4e");
            xi2:
            $kF = $this->spUtility->getSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            if ($this->spUtility->isBlank($kF)) {
                goto Xh7;
            }
            $this->spUtility->log_debug("\163\x65\156\x64\40\x6c\157\x67\x6f\x75\x74\x20\162\145\x71\x75\145\x73\164\x20");
            $c5 = \Magento\Framework\App\ObjectManager::getInstance();
            $i2 = $c5->create("\115\141\147\x65\156\x74\x6f\x5c\103\x75\163\x74\157\155\145\x72\x5c\x4d\x6f\144\145\154\x5c\x43\165\163\x74\x6f\155\145\162")->load($kF["\x69\144"]);
            $Q5 = $i2->getEmail();
            $this->spUtility->log_debug("\116\141\x6d\x65\x49\104\40\72\x3a\40", $Q5);
            $this->spUtility->log_debug("\x49\x6e\x73\x69\144\145\x20\x32\156\x64\x20\x69\x66\40\123\x65\156\x64\151\156\x67\x20\154\157\147\157\x75\x74\x52\145\x71\x75\x65\163\x74\101\x63\164\x69\157\156");
            $this->logoutRequestAction->setIsAdmin($kF["\x61\x64\155\x69\x6e"])->setUserId($kF["\151\x64"])->setNameId($Q5)->execute();
            $this->spUtility->log_debug("\105\156\x64\x20\x6f\146\40\x32\x6e\x64\40\x69\146\40\x53\x65\x6e\144\x69\156\x67\40\154\157\147\157\x75\164\122\x65\x71\x75\145\163\x74\x41\143\164\151\x6f\x6e");
            Xh7:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->spUtility->log_debug($sS->getMessage());
        }
        nBi:
    }
}
