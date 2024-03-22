<?php


namespace MiniOrange\SP\Controller\Adminhtml\Rolesettings;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use MiniOrange\SP\Helper\Curl;
class Index extends BaseAdminAction
{
    private $adminRoleModel;
    private $userGroupModel;
    private $attributeModel;
    private $samlResponse;
    private $params;
    private $adminUserModel;
    protected $sp;
    public function __construct(Context $Gc, PageFactory $VM, SPUtility $Kx, ManagerInterface $c0, LoggerInterface $hI, \Magento\Authorization\Model\ResourceModel\Role\Collection $bY, \Magento\Customer\Model\ResourceModel\Attribute\Collection $d1, \Magento\Customer\Model\ResourceModel\Group\Collection $o1, Sp $vT)
    {
        parent::__construct($Gc, $VM, $Kx, $c0, $hI, $vT);
        $this->adminRoleModel = $bY;
        $this->userGroupModel = $o1;
        $this->attributeModel = $d1;
        $this->messageManager = $c0;
        $this->logger = $hI;
        $this->sp = $vT;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto wn;
        }
        $bc = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($bc == NULL)) {
            goto M1;
        }
        $MG = $this->spUtility->getCurrentAdminUser()->getData();
        $zG = $this->spUtility->getMagnetoVersion();
        $cN = $MG["\145\x6d\141\x69\x6c"];
        $Aq = $MG["\x66\x69\x72\163\x74\156\x61\155\145"];
        $ko = $MG["\154\x61\x73\164\x6e\x61\x6d\x65"];
        $Lu = $this->spUtility->getBaseUrl();
        $D7 = array($Aq, $ko, $zG, $Lu);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($cN, "\x49\x6e\x73\x74\x61\154\154\145\144\40\x53\165\x63\x63\145\163\163\x66\x75\154\x6c\x79\55\101\x63\x63\157\x75\x6e\x74\40\124\x61\142", $D7);
        $this->spUtility->flushCache();
        M1:
        wn:
        try {
            $As = $this->getRequest()->getParams();
            $this->checkIfValidPlugin();
            if (!$this->isFormOptionBeingSaved($As)) {
                goto gW;
            }
            $this->processValuesAndSaveData($As);
            $this->spUtility->flushCache();
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            $this->spUtility->reinitConfig();
            gW:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->spUtility->log_debug($sS->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\122\117\x4c\105\x20\123\x65\164\x74\151\x6e\147\x73"), __("\x52\117\x4c\105\40\123\145\164\164\151\156\x67\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function processValuesAndSaveData($As)
    {
        if (!empty($As["\x6f\160\164\x69\x6f\x6e"]) && $As["\157\x70\164\151\157\156"] == "\x73\x61\166\145\120\162\x6f\x76\151\x64\145\162") {
            goto yN;
        }
        $Lk = trim($As["\155\x6f\x5f\151\x64\x65\x6e\x74\151\164\x79\x5f\x70\x72\x6f\x76\151\x64\x65\162"]);
        $Lw = $this->spUtility->getidpApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\x69\x64\x70\137\x6e\141\155\x65"] === $Lk)) {
                goto v6;
            }
            $ft = $fR->getData();
            v6:
            L2:
        }
        zw:
        $xb = !empty($ft["\151\144\x70\137\x65\x6e\x74\x69\164\171\x5f\x69\144"]) ? $ft["\x69\144\160\x5f\x65\x6e\164\x69\164\x79\x5f\x69\144"] : '';
        $ln = !empty($ft["\x73\x61\155\154\x5f\x6c\x6f\147\x69\156\137\x75\162\x6c"]) ? $ft["\x73\141\x6d\x6c\x5f\x6c\157\x67\151\x6e\x5f\x75\x72\x6c"] : '';
        $dq = !empty($ft["\x73\x61\155\154\137\x6c\x6f\x67\151\x6e\x5f\142\x69\156\x64\x69\x6e\147"]) ? $ft["\163\141\155\154\x5f\154\157\x67\x69\156\137\x62\x69\x6e\144\x69\x6e\147"] : '';
        $WY = !empty($ft["\x73\x61\x6d\154\x5f\154\x6f\x67\x6f\165\164\137\165\x72\154"]) ? $ft["\x73\141\x6d\154\137\x6c\157\147\x6f\165\164\137\165\162\x6c"] : '';
        $B3 = !empty($ft["\x73\x61\155\x6c\x5f\x6c\x6f\147\157\165\x74\137\142\x69\x6e\x64\x69\x6e\147"]) ? $ft["\x73\x61\155\x6c\137\x6c\x6f\147\157\165\x74\137\x62\x69\x6e\x64\x69\x6e\147"] : '';
        $w_ = !empty($ft["\x78\65\x30\x39\x5f\x63\145\x72\164\x69\146\151\143\141\164\145"]) ? SAML2Utilities::sanitize_certificate($ft["\170\x35\x30\x39\x5f\x63\145\162\x74\151\x66\151\143\x61\164\x65"]) : '';
        $a5 = !empty($ft["\162\x65\x73\x70\157\156\x73\145\x5f\x73\151\147\x6e\145\144"]) ? $ft["\162\x65\x73\x70\157\156\163\x65\x5f\x73\x69\147\x6e\145\x64"] : 0;
        $f4 = !empty($ft["\141\x73\x73\x65\x72\x74\151\157\x6e\x5f\163\151\147\156\x65\144"]) ? $ft["\x61\163\x73\145\x72\164\151\157\x6e\x5f\163\x69\x67\156\145\x64"] : 0;
        $o9 = !empty($ft["\163\x68\x6f\x77\137\x61\x64\x6d\151\x6e\137\154\151\156\153"]) && $ft["\163\x68\157\167\x5f\141\144\155\x69\x6e\137\154\x69\156\153"] == true ? 1 : 0;
        $vh = !empty($ft["\x73\150\x6f\167\137\143\x75\x73\x74\157\x6d\145\x72\137\x6c\x69\x6e\153"]) && $ft["\163\x68\157\167\137\x63\165\x73\164\x6f\x6d\145\x72\x5f\x6c\x69\156\153"] == true ? 1 : 0;
        $w8 = !empty($ft["\141\165\x74\x6f\x5f\143\162\145\141\164\145\x5f\x61\144\x6d\151\156\x5f\x75\163\145\162\163"]) && $ft["\141\x75\164\157\x5f\143\x72\x65\x61\164\x65\x5f\x61\x64\x6d\x69\x6e\137\x75\163\145\x72\163"] == true ? 1 : 0;
        $Gd = !empty($ft["\141\165\164\x6f\137\143\x72\145\x61\164\145\x5f\x63\165\x73\x74\157\x6d\x65\162\x73"]) && $ft["\x61\165\164\157\x5f\x63\x72\145\141\x74\x65\137\143\165\x73\164\157\x6d\145\162\x73"] == true ? 1 : 0;
        $h0 = !empty($ft["\x64\x69\163\141\142\x6c\x65\137\142\x32\143"]) && $ft["\x64\151\163\141\142\x6c\145\137\x62\x32\x63"] == true ? 1 : 0;
        $M1 = !empty($ft["\146\x6f\162\143\x65\x5f\141\x75\x74\150\x65\x6e\164\x69\x63\141\x74\151\157\156\137\167\151\x74\150\137\151\144\x70"]) && $ft["\146\157\162\x63\145\137\141\165\x74\150\x65\x6e\x74\x69\x63\x61\x74\151\157\156\x5f\x77\151\164\150\x5f\x69\x64\160"] == true ? 1 : 0;
        $M3 = !empty($ft["\x61\165\164\x6f\137\162\x65\144\x69\162\x65\143\164\137\x74\x6f\x5f\151\144\x70"]) && $ft["\x61\165\164\x6f\x5f\162\145\x64\151\162\x65\x63\x74\x5f\164\157\137\x69\x64\160"] == true ? 1 : 0;
        $qr = !empty($ft["\x6c\151\156\x6b\x5f\164\157\x5f\151\156\x69\x74\151\x61\x74\145\x5f\163\163\x6f"]) && $ft["\154\151\156\x6b\x5f\164\157\137\x69\156\151\x74\151\141\164\x65\137\x73\163\x6f"] == true ? 1 : 0;
        $S8 = !empty($ft["\x75\160\144\x61\164\145\x5f\x61\x74\164\x72\151\x62\165\164\x65\163\x5f\157\x6e\x5f\x6c\157\147\x69\156"]) ? $ft["\165\x70\x64\141\x74\x65\137\141\164\x74\162\x69\142\165\164\145\163\x5f\157\156\x5f\154\x6f\x67\x69\156"] : "\165\x6e\143\x68\145\x63\x6b\x65\x64";
        $qx = !empty($ft["\143\x72\145\x61\164\x65\x5f\x6d\141\147\x65\156\x74\157\x5f\141\x63\x63\157\x75\x6e\164\x5f\142\171"]) ? $ft["\x63\162\x65\x61\x74\145\137\x6d\141\147\145\156\164\157\137\x61\143\x63\157\x75\x6e\x74\x5f\x62\171"] : '';
        $cE = !empty($ft["\x65\155\141\x69\x6c\137\141\164\x74\162\x69\x62\165\x74\x65"]) ? $ft["\145\x6d\x61\x69\x6c\137\x61\x74\x74\x72\151\x62\165\164\145"] : '';
        $x_ = !empty($ft["\x75\x73\x65\x72\156\141\x6d\145\x5f\141\164\x74\162\x69\x62\165\x74\x65"]) ? $ft["\x75\x73\x65\x72\x6e\x61\x6d\145\137\x61\x74\164\162\x69\142\x75\164\x65"] : '';
        $Fw = !empty($ft["\x66\x69\x72\x73\164\x6e\x61\155\x65\137\x61\164\x74\x72\x69\x62\165\164\145"]) ? $ft["\146\x69\162\163\164\156\141\x6d\x65\137\x61\164\164\x72\151\x62\x75\x74\x65"] : '';
        $tV = !empty($ft["\x6c\141\x73\x74\156\141\x6d\145\137\x61\x74\164\162\151\142\165\164\145"]) ? $ft["\154\141\163\x74\x6e\x61\x6d\x65\137\x61\164\x74\162\151\142\x75\164\x65"] : '';
        $Wr = !empty($ft["\147\x72\157\x75\x70\x5f\141\164\164\x72\151\x62\x75\164\x65"]) ? $ft["\147\x72\x6f\x75\x70\x5f\x61\164\164\162\151\x62\165\x74\x65"] : '';
        $T5 = !empty($ft["\x62\151\154\x6c\x69\156\147\x5f\143\x69\164\x79\137\141\x74\x74\x72\151\142\165\164\145"]) ? $ft["\142\151\154\154\x69\x6e\x67\137\x63\151\x74\x79\x5f\x61\164\x74\162\x69\x62\x75\x74\x65"] : '';
        $ab = !empty($ft["\142\151\154\x6c\151\156\147\x5f\x73\x74\x61\x74\145\137\x61\164\164\x72\151\x62\x75\x74\145"]) ? $ft["\142\x69\154\x6c\x69\156\x67\137\163\x74\x61\164\x65\137\x61\x74\164\x72\x69\142\165\164\x65"] : '';
        $TC = !empty($ft["\142\x69\154\154\151\x6e\147\137\x63\x6f\x75\x6e\x74\x72\171\137\141\x74\164\x72\151\142\x75\x74\x65"]) ? $ft["\142\151\154\154\x69\x6e\x67\137\143\x6f\165\156\164\x72\171\x5f\141\164\x74\x72\x69\142\x75\164\x65"] : '';
        $Au = !empty($ft["\142\151\x6c\154\x69\156\147\137\x61\x64\144\162\145\x73\163\137\x61\164\x74\x72\x69\142\165\164\x65"]) ? $ft["\x62\x69\154\x6c\151\x6e\147\x5f\141\144\x64\162\145\163\163\x5f\141\164\x74\x72\151\x62\x75\164\x65"] : '';
        $hX = !empty($ft["\x62\x69\154\x6c\x69\x6e\x67\137\160\150\x6f\x6e\145\x5f\141\x74\164\x72\x69\x62\165\164\145"]) ? $ft["\x62\151\154\154\x69\156\147\137\x70\x68\x6f\x6e\x65\137\x61\164\164\x72\151\x62\x75\x74\145"] : '';
        $Sr = !empty($ft["\142\x69\x6c\154\151\x6e\x67\x5f\x7a\151\160\137\x61\x74\x74\x72\x69\142\x75\164\x65"]) ? $ft["\x62\151\154\x6c\151\x6e\147\x5f\172\151\x70\137\x61\164\164\162\x69\x62\x75\x74\x65"] : '';
        $J2 = !empty($ft["\x73\x68\151\160\x70\151\156\x67\137\143\151\164\x79\137\141\164\x74\x72\x69\142\165\x74\x65"]) ? $ft["\163\x68\151\x70\x70\151\x6e\x67\x5f\x63\151\x74\171\137\x61\x74\x74\162\151\142\165\164\x65"] : '';
        $mG = !empty($ft["\163\x68\151\x70\x70\151\156\x67\x5f\x73\x74\141\x74\x65\137\x61\x74\164\162\151\x62\x75\164\145"]) ? $ft["\x73\150\151\x70\x70\x69\156\x67\137\x73\x74\141\164\145\x5f\x61\x74\164\162\x69\x62\x75\x74\145"] : '';
        $iz = !empty($ft["\163\x68\151\160\x70\151\x6e\x67\137\143\x6f\165\x6e\164\162\171\137\141\164\164\x72\151\142\165\x74\x65"]) ? $ft["\163\150\x69\x70\160\151\156\x67\137\x63\157\x75\x6e\164\162\x79\137\141\x74\164\x72\x69\142\x75\x74\145"] : '';
        $mq = !empty($ft["\x73\x68\x69\160\x70\151\x6e\147\137\x61\x64\144\x72\145\163\163\137\141\164\x74\x72\151\x62\165\x74\145"]) ? $ft["\163\x68\151\x70\x70\x69\156\147\x5f\x61\x64\x64\162\x65\163\x73\137\x61\x74\164\162\x69\x62\165\164\145"] : '';
        $Qb = !empty($ft["\x73\x68\x69\160\160\x69\x6e\x67\137\x70\150\157\x6e\145\x5f\x61\164\x74\162\151\142\165\164\x65"]) ? $ft["\163\150\x69\x70\160\x69\x6e\147\137\x70\150\x6f\x6e\x65\137\141\164\x74\162\x69\142\165\x74\x65"] : '';
        $gE = !empty($ft["\163\x68\151\x70\x70\x69\156\x67\137\x7a\151\160\x5f\141\x74\164\162\151\142\165\164\145"]) ? $ft["\x73\150\151\x70\x70\151\156\x67\x5f\x7a\x69\160\137\x61\x74\164\162\x69\x62\165\164\x65"] : '';
        $lL = !empty($ft["\142\x32\x62\x5f\x61\x74\x74\162\151\142\x75\164\x65"]) ? $ft["\x62\62\142\x5f\x61\x74\164\162\151\142\x75\164\145"] : '';
        $Rr = !empty($ft["\143\165\x73\164\157\155\x5f\164\141\x62\x6c\x65\x6e\141\155\x65"]) ? $ft["\x63\165\163\x74\x6f\155\x5f\x74\141\142\x6c\145\x6e\x61\x6d\x65"] : '';
        $Rv = !empty($ft["\x63\165\163\x74\x6f\155\137\141\164\164\162\151\142\165\x74\145\163"]) ? $ft["\143\165\x73\x74\157\155\137\x61\164\164\162\x69\142\x75\164\x65\163"] : '';
        $i3 = !empty($As["\155\x6f\x5f\163\141\155\x6c\x5f\144\x6f\x6e\164\x5f\x63\x72\x65\x61\164\145\x5f\x75\163\x65\162\x5f\x69\x66\137\x72\157\x6c\145\137\156\x6f\x74\137\x6d\x61\160\160\145\x64"]) ? "\x63\150\x65\x63\x6b\145\x64" : "\x75\x6e\143\150\145\143\153\145\x64";
        $AF = !empty($As["\x73\141\155\x6c\137\141\x6d\137\x75\x70\x64\x61\x74\145\137\162\157\154\x65\163"]) ? "\x63\x68\145\x63\x6b\145\144" : "\x75\156\x63\x68\x65\143\x6b\x65\x64";
        $Jg = !empty($As["\163\x61\x6d\x6c\x5f\141\155\137\165\160\x64\x61\x74\x65\137\146\162\157\x6e\x74\x65\156\x64\x5f\162\x6f\x6c\145\163"]) ? "\x63\150\145\143\153\x65\x64" : "\165\156\143\150\x65\x63\153\145\144";
        $Vc = !empty($As["\163\x61\x6d\x6c\137\141\x6d\x5f\144\145\x66\x61\x75\154\164\137\147\162\x6f\165\x70"]) ? trim($As["\x73\x61\x6d\x6c\137\x61\x6d\137\144\145\x66\141\x75\x6c\x74\x5f\x67\x72\157\165\x70"]) : '';
        $LQ = !empty($As["\x73\141\x6d\154\137\x61\155\x5f\x64\145\x66\141\165\154\164\137\x72\157\154\x65"]) ? trim($As["\163\141\155\x6c\x5f\141\155\x5f\x64\x65\x66\x61\x75\154\x74\137\162\157\x6c\x65"]) : '';
        $CL = json_encode($this->processCustomerRoleMapping($As));
        $sG = json_encode($this->processAdminRoleMapping($As));
        $Uo = !empty($ft["\163\141\x6d\x6c\137\x6c\157\147\x6f\x75\164\x5f\162\x65\144\151\162\x65\x63\164\137\x75\x72\x6c"]) ? $ft["\163\141\x6d\154\137\x6c\157\x67\157\165\x74\137\162\145\x64\151\x72\x65\x63\164\137\165\x72\x6c"] : '';
        $wj = !empty($ft["\163\141\155\x6c\137\145\x6e\141\x62\x6c\145\137\142\151\x6c\154\x69\x6e\x67\x61\x6e\x64\163\x68\x69\x70\160\151\156\147"]) ? $ft["\163\141\x6d\154\137\x65\x6e\141\142\154\x65\x5f\x62\x69\154\154\x69\x6e\147\141\x6e\x64\x73\x68\x69\x70\160\x69\156\147"] : "\156\x6f\x6e\x65";
        $fE = !empty($ft["\163\141\x6d\154\137\x73\x61\155\145\141\x73\x62\151\154\x6c\x69\x6e\147"]) ? $ft["\x73\x61\155\x6c\137\x73\x61\x6d\145\x61\x73\142\151\x6c\154\151\x6e\x67"] : "\x6e\x6f\x6e\145";
        if (is_null($ft)) {
            goto rU;
        }
        $this->spUtility->deleteIDPApps((int) $ft["\151\x64"]);
        rU:
        $this->spUtility->setIDPApps($Lk, $xb, $ln, $dq, $WY, $B3, $w_, $a5, $f4, $o9, $vh, $w8, $Gd, $h0, $M1, $M3, $qr, $S8, $qx, $cE, $x_, $Fw, $tV, $Wr, $T5, $ab, $TC, $Au, $hX, $Sr, $J2, $mG, $iz, $mq, $Qb, $gE, $lL, $Rr, $Rv, $i3, $AF, $Jg, $Vc, $LQ, $CL, $sG, $Uo, $wj, $fE);
        $uc = trim($As["\163\x61\x6d\x6c\x5f\141\x6d\137\144\x65\x66\x61\165\x6c\x74\x5f\162\x6f\154\x65"]);
        $QB = trim($As["\x73\x61\155\x6c\137\141\155\137\144\145\x66\141\x75\x6c\x74\137\147\x72\157\x75\160"]);
        $od = !empty($As["\163\x61\x6d\154\137\x61\x6d\x5f\144\157\156\164\137\x61\154\154\157\x77\x5f\x75\156\x6c\x69\x73\164\145\144\x5f\x75\x73\145\162\137\x72\157\x6c\145"]) ? "\143\150\x65\143\153\x65\x64" : "\x75\x6e\x43\x68\145\143\153\145\144";
        $T4 = !empty($As["\x6d\157\x5f\163\141\x6d\x6c\137\x64\157\x6e\x74\137\x63\x72\x65\x61\164\145\x5f\165\163\145\162\137\151\146\137\x72\157\x6c\145\x5f\x6e\x6f\x74\137\155\x61\160\x70\145\x64"]) ? "\143\x68\x65\x63\153\145\144" : "\165\156\x63\x68\x65\x63\x6b\x65\x64";
        $tj = $this->processAdminRoleMapping($As);
        $AN = $this->processCustomerRoleMapping($As);
        $HB = !empty($As["\163\x61\x6d\154\x5f\x61\155\x5f\x75\160\x64\x61\x74\145\x5f\162\x6f\154\145\x73"]) ? "\x63\x68\145\143\x6b\145\x64" : "\165\156\x63\150\x65\x63\x6b\145\x64";
        $Yx = !empty($As["\163\x61\x6d\x6c\137\x61\x6d\x5f\165\160\x64\x61\164\x65\137\x66\162\x6f\x6e\x74\x65\x6e\x64\137\x72\x6f\x6c\x65\163"]) ? "\143\150\145\x63\x6b\x65\x64" : "\x75\156\143\x68\x65\x63\x6b\145\144";
        goto zQ;
        yN:
        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $As["\155\x6f\x5f\x69\x64\x65\x6e\164\151\164\171\x5f\160\162\x6f\166\x69\144\145\x72"]);
        zQ:
    }
    private function processAdminRoleMapping($As)
    {
        $tj = array();
        $Pj = $this->adminRoleModel->toOptionArray();
        foreach ($Pj as $lZ) {
            $Wt = "\x73\x61\x6d\x6c\x5f\x61\x6d\x5f\x61\144\155\151\x6e\x5f\x61\164\164\162\137\166\141\x6c\165\x65\163\137" . $lZ["\x76\x61\154\165\145"];
            if (empty($As[$Wt])) {
                goto sw;
            }
            $tj[$lZ["\166\x61\154\165\145"]] = $As[$Wt];
            sw:
            Ne:
        }
        bS:
        return $tj;
    }
    private function processCustomerRoleMapping($As)
    {
        $AN = array();
        $PU = $this->userGroupModel->toOptionArray();
        foreach ($PU as $oV) {
            $Wt = "\x73\x61\x6d\x6c\137\141\155\137\147\162\x6f\x75\x70\137\x61\164\164\x72\137\x76\141\154\165\x65\163\137" . $oV["\x76\x61\x6c\x75\145"];
            if (empty($As[$Wt])) {
                goto Wa;
            }
            $AN[$oV["\x76\141\x6c\165\145"]] = $As[$Wt];
            Wa:
            K0:
        }
        sV:
        return $AN;
    }
}
