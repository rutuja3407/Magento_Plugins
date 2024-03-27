<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use MiniOrange\SP\Helper\SPUtility;
class AdminPostLogoutObserver implements ObserverInterface
{
    private $messageManager;
    private $logger;
    private $spUtility;
    public function __construct(ManagerInterface $c0, LoggerInterface $hI, SPUtility $Kx)
    {
        $this->messageManager = $c0;
        $this->logger = $hI;
        $this->spUtility = $Kx;
    }
    public function execute(Observer $ug)
    {
        $this->spUtility->log_debug("\123\101\x4d\x4c\101\x64\x6d\x69\x6e\x4c\157\x67\157\165\164\x4f\x62\163\x65\162\x76\145\x72\x3a\x20\105\x78\x65\x63\165\164\x65");
        $rQ = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
        if (!$rQ) {
            goto vD1;
        }
        $rQ = $this->spUtility->getAdminSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x53\x41\x4d\114\101\144\x6d\151\x6e\114\x6f\x67\157\165\x74\117\142\163\145\x72\x76\x65\162\72\40\101\x70\x70\40\x4e\141\x6d\x65\72\x20" . $rQ);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\144\160\137\156\x61\155\x65"] === $rQ)) {
                goto wcv;
            }
            $ft = $fR->getData();
            wcv:
            IRV:
        }
        L_o:
        $Cs = $ft["\x73\141\155\154\137\154\157\x67\157\x75\x74\x5f\162\x65\x64\x69\x72\145\143\164\137\165\162\x6c"];
        $this->spUtility->log_debug("\x53\x41\x4d\114\x41\144\x6d\151\x6e\114\x6f\x67\157\x75\164\x4f\x62\x73\x65\162\166\145\162\72\x20\154\157\147\157\165\164\40\x72\x65\144\151\162\145\143\x74\40\x75\162\x6c\x3a\40" . $Cs);
        if (empty($Cs)) {
            goto zkW;
        }
        $this->spUtility->redirectURL($Cs);
        $this->spUtility->log_debug("\x53\x41\115\x4c\101\x64\x6d\151\x6e\x4c\157\147\x6f\x75\164\x4f\x62\x73\145\x72\x76\145\x72\x3a\40\x72\145\x64\x69\162\145\143\164\x69\156\x67\40\164\x6f\x20\x6c\157\147\157\x75\x74\x20\165\x72\x6c\x20");
        exit;
        zkW:
        vD1:
        $this->spUtility->redirectURL($this->spUtility->getAdminBaseUrl());
        exit;
    }
}
