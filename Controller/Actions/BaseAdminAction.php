<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\Exception\NotRegisteredException;
use MiniOrange\SP\Helper\Exception\RequiredFieldsException;
use MiniOrange\SP\Helper\Exception\SupportQueryRequiredFieldsException;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
abstract class BaseAdminAction extends \Magento\Backend\App\Action
{
    protected $spUtility;
    protected $context;
    protected $resultPageFactory;
    protected $messageManager;
    protected $logger;
    protected $sp;
    public function __construct(Context $gt, PageFactory $Jq, SPUtility $fR, ManagerInterface $b_, LoggerInterface $kU, Sp $ou)
    {
        $this->spUtility = $fR;
        $this->resultPageFactory = $Jq;
        $this->messageManager = $b_;
        $this->logger = $kU;
        $this->sp = $ou;
        parent::__construct($gt);
    }
    public function checkIfSupportQueryFieldsEmpty($AI)
    {
        try {
            $this->checkIfRequiredFieldsEmpty($AI);
        } catch (RequiredFieldsException $IR) {
            throw new SupportQueryRequiredFieldsException();
        }
    }
    protected function checkIfRequiredFieldsEmpty($AI)
    {
        foreach ($AI as $On => $VP) {
            if (!(is_array($VP) && (empty($VP[$On]) || $this->spUtility->isBlank($VP[$On])) || $this->spUtility->isBlank($VP))) {
                goto Cd;
            }
            throw new RequiredFieldsException();
            Cd:
            lY:
        }
        Xn:
    }
    public abstract function execute();
    protected function isFormOptionBeingSaved($Te)
    {
        return !empty($Te["\157\160\x74\x69\x6f\x6e"]);
    }
    protected function checkIfValidPlugin()
    {
        if (!(!$this->spUtility->micr() || !$this->spUtility->mclv())) {
            goto Ug;
        }
        throw new NotRegisteredException();
        Ug:
    }
}
