<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\Exception\NotRegisteredException;
use MiniOrange\SP\Helper\Exception\RequiredFieldsException;
use MiniOrange\SP\Helper\Exception\SupportQueryRequiredFieldsException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
abstract class BaseAdminAction extends \Magento\Backend\App\Action
{
    protected $spUtility;
    protected $context;
    protected $resultPageFactory;
    protected $messageManager;
    protected $logger;
    protected $sp;
    public function __construct(Context $Gc, PageFactory $VM, SPUtility $Kx, ManagerInterface $c0, LoggerInterface $hI, Sp $vT)
    {
        $this->spUtility = $Kx;
        $this->resultPageFactory = $VM;
        $this->messageManager = $c0;
        $this->logger = $hI;
        $this->sp = $vT;
        parent::__construct($Gc);
    }
    protected function isFormOptionBeingSaved($As)
    {
        return !empty($As["\157\x70\x74\x69\157\x6e"]);
    }
    protected function checkIfRequiredFieldsEmpty($JX)
    {
        foreach ($JX as $zg => $Yk) {
            if (!(is_array($Yk) && (empty($Yk[$zg]) || $this->spUtility->isBlank($Yk[$zg])) || $this->spUtility->isBlank($Yk))) {
                goto VQ;
            }
            throw new RequiredFieldsException();
            VQ:
            u3:
        }
        U_:
    }
    public function checkIfSupportQueryFieldsEmpty($JX)
    {
        try {
            $this->checkIfRequiredFieldsEmpty($JX);
        } catch (RequiredFieldsException $sS) {
            throw new SupportQueryRequiredFieldsException();
        }
    }
    public abstract function execute();
    protected function checkIfValidPlugin()
    {
        if (!(!$this->spUtility->micr() || !$this->spUtility->mclv())) {
            goto u7;
        }
        throw new NotRegisteredException();
        u7:
    }
}
