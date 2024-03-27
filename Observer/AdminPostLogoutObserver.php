<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class AdminPostLogoutObserver implements ObserverInterface
{
    private $messageManager;
    private $logger;
    private $spUtility;
    public function __construct(ManagerInterface $b_, LoggerInterface $kU, SPUtility $fR)
    {
        $this->messageManager = $b_;
        $this->logger = $kU;
        $this->spUtility = $fR;
    }
    public function execute(Observer $UJ)
    {
        $this->spUtility->log_debug("\x53\101\115\x4c\x41\144\155\151\156\x4c\x6f\147\157\x75\164\x4f\x62\x73\145\x72\x76\145\x72\72\40\105\x78\x65\143\165\x74\x65");
        $rq = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
        if (!$rq) {
            goto T_;
        }
        $rq = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x53\x41\x4d\x4c\x41\144\x6d\x69\x6e\x4c\157\x67\x6f\165\x74\117\x62\163\145\x72\x76\x65\162\72\40\x41\160\x70\x20\116\141\x6d\145\x3a\x20" . $rq);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\x64\x70\137\156\x61\x6d\x65"] === $rq)) {
                goto PQ;
            }
            $hR = $ub->getData();
            PQ:
            e_:
        }
        eo:
        $KF = $hR["\x73\x61\155\x6c\137\154\157\147\x6f\x75\x74\x5f\162\145\x64\x69\x72\145\143\x74\x5f\165\x72\x6c"];
        $this->spUtility->log_debug("\123\x41\115\114\x41\x64\x6d\x69\156\114\157\x67\157\x75\164\x4f\x62\163\x65\162\166\145\162\x3a\x20\x6c\157\147\157\165\164\x20\x72\145\x64\151\162\x65\143\x74\x20\165\x72\154\x3a\40" . $KF);
        if (empty($KF)) {
            goto AF;
        }
        $this->spUtility->redirectURL($KF);
        $this->spUtility->log_debug("\123\101\115\114\101\x64\155\151\156\114\x6f\147\157\x75\x74\x4f\x62\x73\x65\x72\x76\145\x72\x3a\40\x72\x65\x64\151\162\145\x63\164\151\156\x67\x20\x74\x6f\40\154\x6f\x67\x6f\x75\164\40\x75\162\x6c\40");
        exit;
        AF:
        T_:
        $this->spUtility->redirectURL($this->spUtility->getAdminBaseUrl());
        exit;
    }
}
