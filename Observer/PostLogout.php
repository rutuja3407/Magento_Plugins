<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class PostLogout implements ObserverInterface
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
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\160\157\x73\x74\x20\154\157\147\157\165\x74\x72\145\x71\165\x65\x73\164\x3a\40{$rq}");
        if (!$rq) {
            goto ad;
        }
        $this->spUtility->log_debug("\160\157\163\x74\40\154\x6f\x67\x6f\x75\164\x72\145\x71\x75\x65\163\x74\72\x69\x64\x70\40\156\x61\155\x65\40\x66\157\x75\156\x64\40\72\40{$rq}");
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\x64\x70\x5f\156\x61\x6d\145"] === $rq)) {
                goto dp;
            }
            $hR = $ub->getData();
            dp:
            q7:
        }
        Jb:
        $pW = $hR["\163\x61\x6d\154\x5f\x6c\157\147\157\x75\x74\137\162\145\144\151\162\145\143\164\x5f\165\162\x6c"];
        if ($pW) {
            goto kt;
        }
        $this->spUtility->result($this->spUtility->getBaseUrl());
        goto pS;
        kt:
        $this->spUtility->result($pW);
        pS:
        ad:
        $this->spUtility->log_debug("\160\157\163\164\x20\154\x6f\147\157\x75\164\162\145\x71\x75\145\163\x74\x3a\x69\144\160\x20\156\x61\155\x65\40\156\157\x74\40\x66\x6f\x75\156\144");
        $this->spUtility->result($this->spUtility->getBaseUrl());
    }
}
