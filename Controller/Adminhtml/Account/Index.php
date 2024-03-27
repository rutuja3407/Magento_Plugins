<?php


namespace MiniOrange\SP\Controller\Adminhtml\Account;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Controller\Actions\ForgotPasswordAction;
use MiniOrange\SP\Controller\Actions\LKAction;
use MiniOrange\SP\Controller\Actions\LoginExistingUserAction;
use MiniOrange\SP\Controller\Actions\RegisterNewUserAction;
use MiniOrange\SP\Controller\Actions\ResendOTPAction;
use MiniOrange\SP\Controller\Actions\SendOTPToPhone;
use MiniOrange\SP\Controller\Actions\ValidateOTPAction;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class Index extends BaseAdminAction
{
    protected $sp;
    private $options = array("\x72\145\x67\x69\x73\x74\145\x72\116\145\167\125\x73\145\x72", "\x76\x61\x6c\151\144\x61\x74\145\x4e\145\167\125\x73\x65\x72", "\x72\145\x73\x65\x6e\144\117\124\x50", "\x73\x65\x6e\x64\x4f\124\120\x50\x68\157\156\x65", "\154\157\147\151\x6e\105\170\151\163\164\151\x6e\x67\x55\163\145\162", "\x72\x65\x73\x65\164\x50\141\163\x73\x77\x6f\x72\144", "\x72\x65\155\x6f\166\x65\x41\x63\x63\x6f\165\156\x74", "\145\170\x74\145\x6e\144\124\x72\151\x61\x6c", "\x76\145\162\151\146\171\114\151\143\145\156\163\x65\113\145\171");
    private $registerNewUserAction;
    private $validateOTPAction;
    private $resendOTPAction;
    private $sendOTPToPhone;
    private $loginExistingUserAction;
    private $forgotPasswordAction;
    private $lkAction;
    private $extendTrial;
    public function __construct(Context $gt, PageFactory $Jq, SPUtility $fR, ManagerInterface $b_, LoggerInterface $kU, RegisterNewUserAction $hF, ValidateOTPAction $B_, ResendOTPAction $mn, SendOTPToPhone $HZ, LoginExistingUserAction $AS, LKAction $M4, ForgotPasswordAction $jV, Sp $ou)
    {
        parent::__construct($gt, $Jq, $fR, $b_, $kU, $ou);
        $this->registerNewUserAction = $hF;
        $this->validateOTPAction = $B_;
        $this->resendOTPAction = $mn;
        $this->sendOTPToPhone = $HZ;
        $this->loginExistingUserAction = $AS;
        $this->forgotPasswordAction = $jV;
        $this->lkAction = $M4;
        $this->sp = $ou;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto u9;
        }
        $Az = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($Az == NULL)) {
            goto N6;
        }
        $fU = $this->spUtility->getCurrentAdminUser()->getData();
        $RH = $this->spUtility->getMagnetoVersion();
        $ii = $fU["\145\x6d\x61\151\154"];
        $FO = $fU["\x66\x69\x72\x73\164\156\x61\155\x65"];
        $Fo = $fU["\154\x61\x73\x74\x6e\x61\155\x65"];
        $kz = $this->spUtility->getBaseUrl();
        $jT = array($FO, $Fo, $RH, $kz);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($ii, "\x49\x6e\x73\x74\x61\x6c\x6c\x65\144\x20\123\x75\143\143\145\x73\163\x66\165\154\154\x79\55\101\x63\x63\157\165\x6e\x74\40\x54\141\142", $jT);
        $this->spUtility->flushCache();
        N6:
        u9:
        try {
            $Te = $this->getRequest()->getParams();
            if (!$this->isFormOptionBeingSaved($Te)) {
                goto bR;
            }
            $pb = array_values($Te);
            $F6 = array_intersect($pb, $this->options);
            if (!(count($F6) > 0)) {
                goto GO;
            }
            $this->_route_data(array_values($F6)[0], $Te);
            $this->spUtility->flushCache();
            GO:
            $this->spUtility->reinitConfig();
            bR:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->logger->debug($IR->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\101\x63\143\157\165\x6e\164\x20\x53\145\x74\x74\151\x6e\147\x73"), __("\101\x63\143\x6f\x75\x6e\164\40\x53\x65\164\x74\x69\156\147\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function _route_data($a2, $Te)
    {
        switch ($a2) {
            case $this->options[0]:
                $this->registerNewUserAction->setRequestParam($Te)->execute();
                goto SU;
            case $this->options[1]:
                $this->validateOTPAction->setRequestParam($Te)->execute();
                goto SU;
            case $this->options[2]:
                $this->resendOTPAction->setRequestParam($Te)->execute();
                goto SU;
            case $this->options[3]:
                $this->sendOTPToPhone->setRequestParam($Te)->execute();
                goto SU;
            case $this->options[4]:
                $this->loginExistingUserAction->setRequestParam($Te)->execute();
                goto SU;
            case $this->options[5]:
                $this->forgotPasswordAction->setRequestParam($Te)->execute();
                goto SU;
            case $this->options[6]:
                $this->lkAction->setRequestParam($Te)->removeAccount();
                goto SU;
            case $this->options[7]:
                $this->spUtility->extendTrial();
                goto SU;
            case $this->options[8]:
                $this->lkAction->setRequestParam($Te)->execute();
                goto SU;
        }
        Pb:
        SU:
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_ACCOUNT);
    }
}
