<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use MiniOrange\SP\Controller\Actions\ReadResponseAction;
use MiniOrange\SP\Controller\Actions\SendLogoutRequest;
use MiniOrange\SP\Controller\Actions\SendLogoutResponse;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class CustomerLogoutObserver implements ObserverInterface
{
    private $messageManager;
    private $logger;
    private $readResponseAction;
    private $spUtility;
    private $logoutRequestAction;
    private $logoutResponseAction;
    public function __construct(ManagerInterface $b_, LoggerInterface $kU, ReadResponseAction $kn, SPUtility $fR, SendLogoutRequest $GL, SendLogoutResponse $zP)
    {
        $this->messageManager = $b_;
        $this->logger = $kU;
        $this->readResponseAction = $kn;
        $this->spUtility = $fR;
        $this->logoutRequestAction = $GL;
        $this->logoutResponseAction = $zP;
    }
    public function execute(Observer $UJ)
    {
        $gf = $this->spUtility->getSessionData("\x63\165\163\x74\157\x6d\145\162\137\x70\x6f\163\x74\x5f\154\157\x67\157\165\x74");
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x49\x6e\x20\x43\x75\x73\x74\x6f\x6d\x65\162\x20\x50\162\x65\40\114\x6f\x67\157\x75\164\x20\117\142\163\145\162\x76\145\x72\x20{$gf}");
        if (!$gf) {
            goto LP;
        }
        try {
            $this->spUtility->setAdminSessionData("\x63\165\163\164\157\x6d\145\x72\x5f\160\157\x73\164\137\154\x6f\x67\x6f\165\164", NULL);
            $Sh = $this->spUtility->getSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            $this->spUtility->log_debug("\x49\x6e\x20\103\165\163\164\157\155\145\x72\x20\x4c\x6f\147\x6f\x75\164\40\x4f\142\x73\145\x72\166\x65\162\x20\125\163\x65\162\163\40\x44\x65\x74\x61\151\x6c\163", $Sh);
            if (!($this->spUtility->isBlank($Sh) && $this->spUtility->isUserLoggedIn())) {
                goto pV;
            }
            $or["\141\144\x6d\x69\156"] = FALSE;
            $or["\151\x64"] = $this->spUtility->getCurrentUser()->getId();
            $this->spUtility->setSessionData(SPConstants::USER_LOGOUT_DETAIL, $or);
            $this->spUtility->log_debug("\x49\x6e\40\x43\165\163\x74\x6f\x6d\x65\162\x20\114\157\x67\157\165\x74\x20\117\142\163\145\162\x76\145\x72\x20\111\x66\x28\x55\163\145\162\x73\x44\145\x74\x61\151\x6c\x73\x20\x62\154\141\x6e\x6b\x20\141\156\144\x20\x69\163\125\163\x65\162\114\157\147\x67\x65\x64\x49\x4e");
            pV:
            $Sh = $this->spUtility->getSessionData(SPConstants::USER_LOGOUT_DETAIL, TRUE);
            if ($this->spUtility->isBlank($Sh)) {
                goto tD;
            }
            $this->spUtility->log_debug("\x73\145\x6e\144\x20\154\157\x67\157\x75\x74\40\x72\x65\161\x75\145\163\164\40");
            $F_ = \Magento\Framework\App\ObjectManager::getInstance();
            $lu = $F_->create("\115\x61\x67\145\x6e\164\157\134\x43\165\x73\x74\x6f\x6d\145\x72\134\115\x6f\x64\x65\154\x5c\103\x75\163\164\157\155\x65\162")->load($Sh["\151\144"]);
            $Au = $lu->getEmail();
            $this->spUtility->log_debug("\x4e\x61\155\145\111\104\40\x3a\72\x20", $Au);
            $this->spUtility->log_debug("\x49\x6e\163\x69\144\145\x20\x32\156\x64\40\x69\x66\40\123\145\x6e\144\x69\x6e\x67\x20\x6c\x6f\x67\x6f\x75\164\122\145\161\x75\145\163\x74\101\143\x74\151\x6f\156");
            $this->logoutRequestAction->setIsAdmin($Sh["\141\x64\155\151\x6e"])->setUserId($Sh["\151\x64"])->setNameId($Au)->execute();
            $this->spUtility->log_debug("\x45\x6e\x64\40\x6f\x66\x20\62\x6e\x64\x20\x69\x66\x20\x53\x65\156\144\151\156\x67\40\x6c\157\147\157\x75\x74\x52\145\x71\x75\x65\x73\x74\x41\x63\x74\x69\x6f\x6e");
            tD:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->spUtility->log_debug($IR->getMessage());
        }
        LP:
    }
}
