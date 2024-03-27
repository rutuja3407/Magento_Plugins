<?php


namespace MiniOrange\SP\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use MiniOrange\SP\Helper\SPUtility;
class PostLogout implements ObserverInterface
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
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x70\x6f\x73\164\x20\154\x6f\147\157\x75\164\162\145\x71\x75\145\163\164\x3a\x20{$rQ}");
        if (!$rQ) {
            goto I_4;
        }
        $this->spUtility->log_debug("\x70\x6f\163\x74\40\x6c\157\147\x6f\165\x74\162\x65\161\x75\145\x73\164\x3a\x69\x64\x70\40\156\x61\x6d\145\x20\146\x6f\x75\156\x64\40\72\x20{$rQ}");
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\x64\160\137\156\141\x6d\x65"] === $rQ)) {
                goto HYy;
            }
            $ft = $fR->getData();
            HYy:
            BRK:
        }
        wUR:
        $an = $ft["\163\x61\x6d\154\x5f\x6c\x6f\147\x6f\x75\164\x5f\x72\x65\144\151\x72\145\x63\x74\137\x75\162\x6c"];
        if ($an) {
            goto l2E;
        }
        $this->spUtility->result($this->spUtility->getBaseUrl());
        goto OU4;
        l2E:
        $this->spUtility->result($an);
        OU4:
        I_4:
        $this->spUtility->log_debug("\x70\157\x73\x74\40\154\157\147\157\165\x74\x72\145\x71\165\145\163\x74\72\x69\x64\160\40\x6e\x61\155\145\40\156\157\164\x20\x66\157\165\156\x64");
        $this->spUtility->result($this->spUtility->getBaseUrl());
    }
}
