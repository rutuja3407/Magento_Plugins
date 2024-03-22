<?php


namespace MiniOrange\SP\Controller\Adminhtml\Account;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use MiniOrange\SP\Controller\Actions\RegisterNewUserAction;
use MiniOrange\SP\Controller\Actions\ValidateOTPAction;
use MiniOrange\SP\Controller\Actions\ResendOTPAction;
use MiniOrange\SP\Controller\Actions\SendOTPToPhone;
use MiniOrange\SP\Controller\Actions\LoginExistingUserAction;
use MiniOrange\SP\Controller\Actions\LKAction;
use MiniOrange\SP\Controller\Actions\ForgotPasswordAction;
class Index extends BaseAdminAction
{
    private $options = array("\162\145\147\x69\x73\164\x65\x72\x4e\x65\167\x55\x73\145\162", "\x76\141\x6c\151\x64\x61\164\145\x4e\x65\167\125\163\145\x72", "\162\145\x73\145\x6e\144\117\x54\120", "\x73\145\156\x64\117\124\120\x50\x68\157\x6e\x65", "\154\157\147\x69\x6e\x45\x78\151\163\x74\x69\x6e\147\125\163\x65\x72", "\x72\x65\x73\145\x74\x50\141\x73\163\x77\157\x72\x64", "\162\x65\155\x6f\166\145\x41\143\x63\x6f\x75\156\x74", "\x65\x78\x74\145\x6e\x64\124\x72\x69\141\x6c", "\166\145\x72\151\146\171\x4c\x69\x63\x65\156\163\x65\113\145\171");
    private $registerNewUserAction;
    private $validateOTPAction;
    private $resendOTPAction;
    private $sendOTPToPhone;
    private $loginExistingUserAction;
    private $forgotPasswordAction;
    private $lkAction;
    private $extendTrial;
    protected $sp;
    public function __construct(Context $Gc, PageFactory $VM, SPUtility $Kx, ManagerInterface $c0, LoggerInterface $hI, RegisterNewUserAction $Vu, ValidateOTPAction $vX, ResendOTPAction $zw, SendOTPToPhone $GD, LoginExistingUserAction $iw, LKAction $Fy, ForgotPasswordAction $Zz, Sp $vT)
    {
        parent::__construct($Gc, $VM, $Kx, $c0, $hI, $vT);
        $this->registerNewUserAction = $Vu;
        $this->validateOTPAction = $vX;
        $this->resendOTPAction = $zw;
        $this->sendOTPToPhone = $GD;
        $this->loginExistingUserAction = $iw;
        $this->forgotPasswordAction = $Zz;
        $this->lkAction = $Fy;
        $this->sp = $vT;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto oL;
        }
        $bc = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($bc == NULL)) {
            goto rg;
        }
        $MG = $this->spUtility->getCurrentAdminUser()->getData();
        $zG = $this->spUtility->getMagnetoVersion();
        $cN = $MG["\x65\155\141\x69\154"];
        $Aq = $MG["\x66\x69\x72\x73\164\156\x61\155\x65"];
        $ko = $MG["\154\141\163\x74\x6e\x61\x6d\x65"];
        $Lu = $this->spUtility->getBaseUrl();
        $D7 = array($Aq, $ko, $zG, $Lu);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($cN, "\111\156\163\164\141\154\x6c\145\144\40\123\165\x63\143\x65\163\163\146\165\154\154\171\x2d\101\143\143\x6f\165\x6e\164\x20\x54\x61\142", $D7);
        $this->spUtility->flushCache();
        rg:
        oL:
        try {
            $As = $this->getRequest()->getParams();
            if (!$this->isFormOptionBeingSaved($As)) {
                goto QW;
            }
            $PT = array_values($As);
            $dz = array_intersect($PT, $this->options);
            if (!(count($dz) > 0)) {
                goto UK;
            }
            $this->_route_data(array_values($dz)[0], $As);
            $this->spUtility->flushCache();
            UK:
            $this->spUtility->reinitConfig();
            QW:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->logger->debug($sS->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\x41\143\143\x6f\x75\x6e\164\40\x53\145\164\164\x69\x6e\x67\163"), __("\101\x63\x63\157\165\x6e\164\x20\123\145\164\x74\x69\156\x67\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function _route_data($NR, $As)
    {
        switch ($NR) {
            case $this->options[0]:
                $this->registerNewUserAction->setRequestParam($As)->execute();
                goto sq;
            case $this->options[1]:
                $this->validateOTPAction->setRequestParam($As)->execute();
                goto sq;
            case $this->options[2]:
                $this->resendOTPAction->setRequestParam($As)->execute();
                goto sq;
            case $this->options[3]:
                $this->sendOTPToPhone->setRequestParam($As)->execute();
                goto sq;
            case $this->options[4]:
                $this->loginExistingUserAction->setRequestParam($As)->execute();
                goto sq;
            case $this->options[5]:
                $this->forgotPasswordAction->setRequestParam($As)->execute();
                goto sq;
            case $this->options[6]:
                $this->lkAction->setRequestParam($As)->removeAccount();
                goto sq;
            case $this->options[7]:
                $this->spUtility->extendTrial();
                goto sq;
            case $this->options[8]:
                $this->lkAction->setRequestParam($As)->execute();
                goto sq;
        }
        Db:
        sq:
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_ACCOUNT);
    }
}
