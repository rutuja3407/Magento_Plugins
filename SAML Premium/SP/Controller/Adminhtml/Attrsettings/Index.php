<?php


namespace MiniOrange\SP\Controller\Adminhtml\Attrsettings;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use MiniOrange\SP\Helper\Curl;
class Index extends BaseAdminAction
{
    private $adminRoleModel;
    private $userGroupModel;
    protected $sp;
    private $moduleDataSetup;
    protected $customerSetupFactory;
    private $attributeSetFactory;
    public function __construct(Context $Gc, PageFactory $VM, SPUtility $Kx, ManagerInterface $c0, LoggerInterface $hI, \Magento\Authorization\Model\ResourceModel\Role\Collection $bY, \Magento\Customer\Model\ResourceModel\Group\Collection $o1, Sp $vT, ModuleDataSetupInterface $jL, CustomerSetupFactory $ih, AttributeSetFactory $Wq)
    {
        parent::__construct($Gc, $VM, $Kx, $c0, $hI, $vT);
        $this->adminRoleModel = $bY;
        $this->sp = $vT;
        $this->userGroupModel = $o1;
        $this->moduleDataSetup = $jL;
        $this->customerSetupFactory = $ih;
        $this->attributeSetFactory = $Wq;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto Ms;
        }
        $bc = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($bc == NULL)) {
            goto PF;
        }
        $MG = $this->spUtility->getCurrentAdminUser()->getData();
        $zG = $this->spUtility->getMagnetoVersion();
        $cN = $MG["\x65\155\141\151\154"];
        $Aq = $MG["\146\151\162\163\x74\156\141\155\x65"];
        $ko = $MG["\x6c\141\x73\164\156\141\x6d\x65"];
        $Lu = $this->spUtility->getBaseUrl();
        $D7 = array($Aq, $ko, $zG, $Lu);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($cN, "\111\x6e\163\164\x61\154\154\x65\144\x20\x53\x75\143\143\145\x73\163\x66\165\154\x6c\x79\x2d\x41\x63\x63\157\165\156\x74\40\x54\141\142", $D7);
        $this->spUtility->flushCache();
        PF:
        Ms:
        try {
            $As = $this->getRequest()->getParams();
            $this->checkIfValidPlugin();
            if (!$this->isFormOptionBeingSaved($As)) {
                goto o4;
            }
            if (!(!empty($As["\157\160\164\151\x6f\156"]) && $As["\x6f\x70\164\x69\157\156"] != "\x73\141\166\145\x50\162\x6f\x76\151\144\x65\x72")) {
                goto o2;
            }
            $this->checkIfRequiredFieldsEmpty(["\x73\141\155\x6c\137\141\155\137\x75\x73\145\162\156\141\155\x65" => $As, "\x73\x61\x6d\x6c\137\141\155\x5f\x61\143\143\157\x75\x6e\164\x5f\155\x61\x74\143\150\145\162" => $As]);
            o2:
            $this->processValuesAndSaveData($As);
            $this->spUtility->flushCache();
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            $this->spUtility->reinitConfig();
            o4:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->logger->debug($sS->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\x41\x54\x54\x52\x20\x53\145\x74\164\x69\156\147\163"), __("\x41\124\124\122\x20\x53\145\x74\x74\x69\156\x67\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function processValuesAndSaveData($As)
    {
        if (!empty($As["\157\x70\x74\151\157\156"]) && $As["\x6f\160\x74\151\157\x6e"] == "\x73\141\166\145\x50\x72\x6f\x76\x69\144\x65\162") {
            goto Ny;
        }
        $Lk = trim($As["\155\x6f\137\151\144\145\x6e\164\151\x74\171\137\160\162\157\x76\151\x64\x65\x72"]);
        $Lw = $this->spUtility->getidpApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\x69\144\x70\x5f\156\141\x6d\x65"] === $Lk)) {
                goto bx;
            }
            $ft = $fR->getData();
            bx:
            l6:
        }
        Q5:
        $mM = array("\146\157\x72\x6d\x5f\x6b\x65\x79", "\155\157\137\x69\144\145\156\164\151\x74\x79\137\x70\162\157\166\151\144\145\162", "\163\x61\x6d\x6c\137\141\x6d\x5f\x73\x61\155\145\141\x73\142\x69\x6c\154\x69\156\x67", "\163\141\x6d\154\137\x61\x6d\x5f\x62\x69\154\154\151\x6e\x67\141\156\x64\x73\150\151\160\x70\151\156\147", "\163\x61\x6d\x6c\x5f\141\155\137\x75\160\x64\141\164\x65\137\x61\164\x74\162\x69\142\165\164\x65", "\164\x68\151\x73\x5f\x61\x74\x74\162\x69\142\x75\164\x65", "\x73\141\155\x6c\137\141\x6d\137\146\151\162\x73\164\x5f\x6e\141\x6d\145", "\x73\x61\x6d\154\x5f\x61\155\x5f\x6c\x61\163\164\x5f\156\x61\x6d\x65", "\x73\x61\x6d\154\137\141\x6d\x5f\x61\143\143\157\165\x6e\x74\x5f\155\141\164\x63\x68\x65\162", "\163\x61\x6d\x6c\x5f\x61\155\x5f\165\163\145\162\x6e\x61\x6d\x65", "\163\141\x6d\154\137\141\155\x5f\x65\x6d\x61\151\x6c", "\163\141\155\x6c\137\x61\155\x5f\x67\x72\157\165\160\137\x6e\141\155\x65", "\x73\141\x6d\x6c\137\x61\155\x5f\x63\x69\x74\171", "\x73\141\155\x6c\x5f\x61\x6d\x5f\141\144\x64\162\145\163\x73", "\60", "\x73\x61\155\x6c\137\141\155\137\143\157\x6d\160\141\156\171\137\x69\x64", "\x73\x61\x6d\154\x5f\x61\155\137\x63\151\x74\171\x5f\142\151\x6c\x6c\x69\156\x67", "\x73\x61\x6d\154\x5f\x61\x6d\137\x61\x64\x64\162\145\163\x73\x5f\x62\151\154\x6c\x69\x6e\x67", "\163\x61\155\154\137\141\x6d\137\x70\150\x6f\x6e\x65\137\x62\151\154\154\151\156\147", "\163\141\155\154\x5f\x61\155\x5f\x73\x74\141\164\145\x5f\x62\151\154\154\x69\156\147", "\163\x61\155\x6c\137\141\x6d\x5f\172\151\160\x63\157\144\145\x5f\x62\151\154\154\151\156\x67", "\163\141\155\154\x5f\x61\155\137\x63\x6f\165\156\x74\162\x79\x5f\142\x69\x6c\x6c\151\156\x67", "\163\141\155\x6c\x5f\x61\x6d\x5f\x63\x69\x74\171\x5f\163\150\151\x70\x70\151\x6e\x67", "\x73\x61\155\154\x5f\x61\155\x5f\x61\x64\144\x72\145\x73\x73\x5f\163\x68\151\160\160\151\x6e\147", "\x73\141\155\154\137\141\x6d\x5f\x70\150\x6f\156\x65\x5f\163\x68\151\x70\x70\x69\x6e\x67", "\x73\x61\x6d\154\x5f\141\x6d\x5f\x73\164\x61\164\145\x5f\163\150\151\160\x70\x69\x6e\x67", "\163\x61\x6d\x6c\137\x61\155\137\172\151\160\x63\157\x64\x65\x5f\163\x68\151\160\x70\x69\x6e\x67", "\163\141\x6d\154\x5f\x61\155\x5f\x63\x6f\x75\x6e\x74\162\x79\137\163\x68\x69\160\160\x69\x6e\147", "\163\141\155\154\x5f\x61\x6d\137\165\x70\x64\x61\164\x65\x5f\x61\164\x74\x72\151\x62\165\x65", "\x6f\x70\x74\151\157\156", "\x73\141\x6d\154\137\141\x6d\137\x63\157\155\160\141\x6e\171", "\163\x61\x6d\x6c\137\x61\155\137\x74\x61\142\154\145", "\153\145\171", "\x73\165\142\x6d\x69\x74");
        $Fd = json_encode($As, true);
        $xA = json_decode($Fd, true);
        $this->spUtility->log_debug("\x44\145\146\141\x75\x6c\164\x20\x61\156\144\x20\x43\165\163\164\x6f\x6d\x20\101\x74\164\162\151\x62\165\164\x65\163\40\101\162\x72\x61\x79\72\40", $xA);
        $cf = $xA;
        $this->spUtility->log_debug("\x6c\x65\x74\x27\163\x20\165\156\163\x65\164\40\144\145\x66\141\x75\x6c\x74\x20\x61\164\x74\x72", $cf);
        foreach ($mM as $Yk) {
            unset($cf[$Yk]);
            zY:
        }
        zf:
        $gw = json_encode($cf, true);
        $this->spUtility->log_debug("\x61\x66\x74\145\162\40\x75\x6e\x73\145\164\x69\x6e\147\x20\x74\150\145\x20\x76\x61\154\x75\145", $gw);
        $this->spUtility->log_debug("\163\141\166\145\40\143\x75\163\x74\x6f\x6d\40\141\x74\164\x72\151\x62\x75\164\145\163");
        $xb = !empty($ft["\x69\x64\160\137\145\x6e\164\x69\164\x79\137\151\144"]) ? $ft["\151\144\x70\137\x65\x6e\164\151\x74\171\137\x69\x64"] : '';
        $ln = !empty($ft["\x73\x61\155\x6c\137\x6c\x6f\x67\151\x6e\x5f\165\x72\154"]) ? $ft["\x73\x61\x6d\154\x5f\154\157\x67\x69\156\137\165\x72\x6c"] : '';
        $dq = !empty($ft["\163\141\x6d\154\137\x6c\157\147\x69\x6e\x5f\x62\x69\156\x64\x69\156\147"]) ? $ft["\x73\141\x6d\154\137\154\157\x67\x69\x6e\137\x62\151\x6e\x64\151\156\x67"] : '';
        $WY = !empty($ft["\x73\x61\155\x6c\137\154\x6f\147\157\x75\164\x5f\165\x72\x6c"]) ? $ft["\163\141\x6d\x6c\x5f\x6c\x6f\147\x6f\x75\164\x5f\x75\162\154"] : '';
        $B3 = !empty($ft["\x73\x61\x6d\154\x5f\154\x6f\x67\157\x75\164\x5f\142\x69\x6e\x64\x69\x6e\147"]) ? $ft["\163\141\155\154\137\x6c\157\147\157\x75\164\x5f\x62\151\x6e\144\x69\x6e\x67"] : '';
        $w_ = !empty($ft["\170\x35\x30\x39\x5f\143\145\162\164\x69\146\151\x63\x61\164\x65"]) ? SAML2Utilities::sanitize_certificate($ft["\x78\x35\x30\x39\x5f\x63\x65\x72\x74\151\x66\151\143\x61\x74\x65"]) : '';
        $a5 = !empty($ft["\x72\145\163\160\x6f\156\x73\x65\x5f\163\x69\x67\156\x65\144"]) ? $ft["\x72\145\163\x70\x6f\x6e\163\x65\137\x73\151\x67\x6e\145\144"] : 0;
        $f4 = !empty($ft["\141\163\163\145\x72\164\151\157\156\x5f\163\151\x67\156\145\144"]) ? $ft["\141\x73\x73\145\x72\x74\x69\157\x6e\137\x73\151\147\156\x65\x64"] : 0;
        $o9 = !empty($ft["\163\x68\157\x77\x5f\x61\144\x6d\151\x6e\x5f\x6c\151\156\x6b"]) && $ft["\163\150\157\167\137\x61\144\x6d\x69\x6e\137\x6c\151\x6e\x6b"] == true ? 1 : 0;
        $vh = !empty($ft["\163\x68\x6f\167\x5f\x63\x75\x73\x74\157\155\145\162\x5f\x6c\x69\x6e\153"]) && $ft["\163\150\157\x77\137\x63\165\x73\164\x6f\x6d\x65\x72\137\x6c\151\x6e\153"] == true ? 1 : 0;
        $w8 = !empty($ft["\141\x75\164\x6f\137\x63\x72\x65\141\164\x65\137\141\144\155\151\156\x5f\165\x73\145\x72\163"]) && $ft["\x61\165\x74\x6f\x5f\x63\x72\145\141\164\145\x5f\141\144\x6d\151\x6e\x5f\x75\x73\145\x72\x73"] == true ? 1 : 0;
        $Gd = !empty($ft["\141\x75\164\x6f\137\143\162\x65\141\x74\x65\137\143\x75\x73\164\157\155\145\x72\x73"]) && $ft["\141\x75\164\157\137\143\162\x65\x61\164\145\x5f\x63\x75\x73\164\x6f\155\145\x72\163"] == true ? 1 : 0;
        $h0 = !empty($ft["\x64\x69\163\141\142\x6c\145\x5f\x62\x32\143"]) && $ft["\x64\x69\x73\x61\x62\154\x65\x5f\x62\62\143"] == true ? 1 : 0;
        $M1 = !empty($ft["\146\157\x72\143\x65\x5f\141\x75\x74\x68\x65\x6e\x74\x69\x63\x61\x74\x69\157\156\137\167\151\164\x68\137\151\x64\x70"]) && $ft["\x66\157\162\143\145\137\141\165\x74\150\x65\x6e\164\151\143\141\164\x69\157\x6e\x5f\167\x69\164\150\x5f\151\x64\x70"] == true ? 1 : 0;
        $M3 = !empty($ft["\141\x75\x74\x6f\x5f\162\x65\x64\151\162\x65\x63\x74\x5f\x74\x6f\137\x69\144\160"]) && $ft["\141\165\x74\x6f\x5f\x72\145\x64\x69\162\x65\143\164\x5f\164\157\x5f\151\144\160"] == true ? 1 : 0;
        $qr = !empty($ft["\x6c\151\156\153\x5f\164\x6f\x5f\151\156\151\x74\x69\141\164\x65\137\163\x73\x6f"]) && $ft["\x6c\151\156\153\x5f\x74\157\x5f\x69\x6e\151\164\x69\x61\x74\145\137\x73\x73\157"] == true ? 1 : 0;
        $S8 = !empty($As["\163\x61\x6d\x6c\x5f\141\x6d\137\165\160\x64\141\x74\145\137\x61\164\x74\162\x69\142\x75\x74\145"]) ? "\143\150\x65\x63\x6b\x65\x64" : "\165\156\143\150\x65\143\153\145\x64";
        $qx = !empty($As["\163\141\x6d\154\x5f\141\155\x5f\x61\143\143\157\165\156\164\x5f\x6d\x61\x74\143\150\x65\162"]) ? $As["\163\x61\155\154\x5f\x61\155\137\141\143\x63\157\165\x6e\x74\137\155\141\164\143\x68\x65\x72"] : '';
        $cE = !empty($As["\163\141\x6d\x6c\x5f\x61\155\x5f\145\155\141\x69\x6c"]) ? trim($As["\x73\141\x6d\154\x5f\x61\x6d\x5f\145\155\x61\x69\x6c"]) : '';
        $x_ = !empty($As["\x73\141\x6d\154\137\x61\x6d\137\x75\x73\145\162\156\x61\155\145"]) ? trim($As["\163\141\x6d\x6c\x5f\x61\155\x5f\165\x73\x65\x72\x6e\x61\x6d\x65"]) : '';
        $Fw = !empty($As["\x73\x61\155\x6c\137\x61\155\x5f\146\x69\162\x73\164\137\x6e\x61\x6d\145"]) ? trim($As["\x73\141\x6d\154\137\141\155\137\146\x69\x72\x73\164\x5f\156\141\155\x65"]) : '';
        $tV = !empty($As["\163\x61\x6d\x6c\137\141\x6d\137\154\x61\x73\164\x5f\156\141\x6d\145"]) ? trim($As["\x73\141\155\154\137\x61\x6d\x5f\154\141\163\x74\x5f\156\141\x6d\145"]) : '';
        $Wr = !empty($As["\x73\141\x6d\x6c\x5f\x61\155\x5f\x67\x72\x6f\x75\160\x5f\x6e\x61\155\145"]) ? trim($As["\x73\141\155\154\137\x61\x6d\137\x67\162\x6f\x75\160\137\156\x61\155\x65"]) : '';
        $T5 = !empty($As["\163\141\155\x6c\137\141\x6d\x5f\x63\151\164\171\x5f\x62\151\x6c\154\x69\156\x67"]) ? trim($As["\x73\x61\155\154\x5f\x61\155\x5f\143\151\164\171\x5f\142\x69\x6c\x6c\x69\x6e\147"]) : '';
        $ab = !empty($As["\163\141\155\154\137\x61\155\x5f\x73\164\x61\x74\145\x5f\x62\x69\154\154\151\156\147"]) ? trim($As["\x73\141\x6d\154\137\x61\155\137\x73\164\x61\164\x65\x5f\142\151\x6c\x6c\x69\156\x67"]) : '';
        $TC = !empty($As["\x73\141\155\154\137\141\x6d\137\143\157\165\x6e\x74\x72\x79\x5f\142\151\154\x6c\151\156\147"]) ? trim($As["\x73\x61\x6d\154\137\x61\x6d\x5f\x63\x6f\x75\156\164\x72\171\137\142\151\x6c\x6c\151\156\x67"]) : '';
        $Au = !empty($As["\x73\141\155\154\137\x61\155\137\141\x64\x64\162\145\163\163\137\x62\x69\x6c\x6c\x69\x6e\x67"]) ? trim($As["\163\x61\155\154\x5f\x61\155\x5f\x61\144\144\x72\145\x73\x73\x5f\142\151\x6c\154\x69\x6e\147"]) : '';
        $hX = !empty($As["\x73\141\x6d\154\x5f\x61\155\137\x70\x68\157\156\x65\x5f\142\x69\x6c\x6c\151\x6e\x67"]) ? trim($As["\163\141\155\154\x5f\141\x6d\x5f\160\x68\x6f\156\x65\x5f\x62\x69\x6c\154\151\x6e\x67"]) : '';
        $Sr = !empty($As["\163\141\x6d\154\x5f\141\155\137\x7a\151\160\143\157\x64\145\137\x62\x69\154\154\x69\x6e\147"]) ? trim($As["\x73\x61\155\x6c\x5f\141\155\137\x7a\151\160\143\157\x64\x65\x5f\142\x69\154\x6c\x69\x6e\147"]) : '';
        $J2 = !empty($As["\x73\x61\155\x6c\x5f\x61\155\x5f\143\x69\x74\x79\137\x73\150\151\160\160\151\x6e\x67"]) ? trim($As["\x73\141\x6d\x6c\137\141\x6d\137\143\x69\164\x79\x5f\x73\150\151\x70\160\x69\156\147"]) : '';
        $mG = !empty($As["\163\141\x6d\154\x5f\141\155\x5f\x73\164\x61\x74\x65\137\163\150\x69\160\160\151\x6e\x67"]) ? trim($As["\163\x61\155\154\x5f\x61\x6d\137\163\164\141\164\145\x5f\163\150\x69\160\160\x69\x6e\147"]) : '';
        $iz = !empty($As["\163\x61\155\x6c\137\141\x6d\137\x63\x6f\x75\x6e\164\162\171\x5f\163\150\151\160\x70\x69\156\147"]) ? trim($As["\x73\x61\x6d\x6c\137\x61\155\137\143\x6f\x75\156\164\162\x79\137\163\150\x69\x70\x70\151\156\147"]) : '';
        $mq = !empty($As["\163\x61\x6d\154\137\x61\155\137\x61\144\x64\x72\145\163\163\137\x73\150\x69\160\x70\151\x6e\147"]) ? trim($As["\163\141\155\154\137\x61\155\137\141\x64\144\162\145\x73\x73\x5f\x73\150\x69\x70\x70\151\x6e\147"]) : '';
        $Qb = !empty($As["\x73\x61\x6d\154\137\141\x6d\137\x70\x68\x6f\156\x65\137\x73\x68\151\x70\x70\x69\x6e\147"]) ? trim($As["\163\141\155\154\137\141\x6d\x5f\160\150\x6f\156\x65\x5f\x73\150\151\160\x70\151\156\x67"]) : '';
        $gE = !empty($As["\163\x61\x6d\x6c\x5f\141\x6d\137\x7a\151\x70\143\x6f\x64\145\x5f\x73\x68\151\x70\x70\x69\156\147"]) ? trim($As["\x73\x61\155\154\137\141\x6d\x5f\x7a\x69\160\143\x6f\144\145\137\x73\150\x69\x70\x70\151\156\147"]) : '';
        $lL = !empty($As["\x73\141\x6d\x6c\137\x61\x6d\137\x63\x6f\x6d\160\141\x6e\x79\x5f\151\x64"]) ? trim($As["\x73\x61\155\154\x5f\x61\155\x5f\143\157\155\160\141\156\171\137\x69\144"]) : '';
        $Rr = !empty($As["\x73\141\x6d\154\x5f\x61\155\137\164\141\142\x6c\145"]) ? trim($As["\163\x61\x6d\x6c\x5f\x61\x6d\x5f\x74\x61\x62\154\145"]) : '';
        $Rv = !empty($gw) ? $gw : '';
        $i3 = !empty($ft["\x64\x6f\x5f\x6e\157\164\x5f\x61\165\164\157\143\x72\145\x61\164\145\x5f\x69\146\137\x72\157\x6c\x65\x73\x5f\156\x6f\x74\x5f\x6d\141\160\160\145\144"]) ? $ft["\144\x6f\x5f\156\x6f\x74\x5f\x61\165\164\x6f\x63\x72\145\141\164\145\x5f\x69\146\137\162\x6f\154\145\163\x5f\x6e\x6f\x74\137\155\x61\x70\x70\145\x64"] : "\165\156\143\x68\145\x63\153\145\144";
        $AF = !empty($ft["\165\160\x64\141\164\x65\137\x62\141\x63\x6b\x65\x6e\144\x5f\x72\157\154\x65\163\x5f\157\156\x5f\163\163\157"]) ? $ft["\165\160\x64\141\x74\x65\x5f\142\141\143\x6b\x65\x6e\144\137\x72\x6f\154\x65\163\x5f\157\156\137\163\x73\157"] : "\165\156\x63\150\145\143\153\145\x64";
        $Jg = !empty($ft["\x75\160\x64\x61\164\145\x5f\146\x72\x6f\x6e\164\145\x6e\x64\137\x67\x72\x6f\x75\160\x73\x5f\157\156\x5f\163\163\x6f"]) ? $ft["\x75\160\144\x61\164\145\x5f\x66\x72\157\x6e\164\x65\x6e\144\x5f\147\162\x6f\x75\x70\x73\137\x6f\x6e\137\163\x73\157"] : "\165\156\143\x68\145\x63\x6b\x65\x64";
        $Vc = !empty($ft["\x64\145\x66\x61\165\x6c\164\x5f\x67\x72\x6f\x75\x70"]) ? $ft["\144\145\x66\x61\165\154\164\x5f\x67\162\x6f\165\160"] : '';
        $LQ = !empty($ft["\144\x65\x66\141\165\x6c\164\x5f\162\x6f\154\145"]) ? $ft["\x64\x65\146\141\165\x6c\x74\137\162\x6f\x6c\145"] : '';
        $CL = !empty($ft["\147\162\x6f\x75\x70\163\137\x6d\x61\x70\x70\x65\144"]) ? $ft["\147\162\x6f\x75\x70\163\x5f\155\141\160\x70\x65\x64"] : '';
        $sG = !empty($ft["\x72\x6f\x6c\x65\163\x5f\x6d\x61\x70\160\x65\x64"]) ? $ft["\x72\157\x6c\x65\x73\137\x6d\141\160\160\x65\x64"] : '';
        $Uo = !empty($ft["\x73\141\155\154\137\154\x6f\147\157\x75\x74\x5f\162\x65\144\151\162\145\x63\x74\x5f\165\x72\154"]) ? $ft["\163\141\155\x6c\137\154\157\x67\x6f\x75\x74\137\x72\x65\144\x69\x72\x65\x63\x74\137\165\162\154"] : '';
        $wj = !empty($As["\163\141\155\154\137\141\155\137\142\x69\154\154\x69\156\x67\x61\156\144\x73\x68\151\160\x70\x69\x6e\147"]) ? trim($As["\x73\x61\x6d\x6c\x5f\x61\155\x5f\x62\x69\154\x6c\151\156\x67\x61\156\x64\163\x68\151\160\160\x69\156\147"]) : "\156\x6f\156\145";
        $fE = !empty($As["\163\141\x6d\x6c\x5f\x61\x6d\x5f\163\x61\x6d\x65\x61\163\x62\x69\x6c\154\x69\156\x67"]) ? trim($As["\163\141\x6d\154\x5f\141\155\x5f\x73\x61\155\145\141\163\142\151\x6c\x6c\x69\x6e\147"]) : "\x6e\157\x6e\145";
        if (is_null($ft)) {
            goto aZ;
        }
        $this->spUtility->deleteIDPApps((int) $ft["\x69\x64"]);
        aZ:
        $this->spUtility->setIDPApps($Lk, $xb, $ln, $dq, $WY, $B3, $w_, $a5, $f4, $o9, $vh, $w8, $Gd, $h0, $M1, $M3, $qr, $S8, $qx, $cE, $x_, $Fw, $tV, $Wr, $T5, $ab, $TC, $Au, $hX, $Sr, $J2, $mG, $iz, $mq, $Qb, $gE, $lL, $Rr, $Rv, $i3, $AF, $Jg, $Vc, $LQ, $CL, $sG, $Uo, $wj, $fE);
        goto Ka;
        Ny:
        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $As["\155\x6f\137\151\x64\x65\156\164\x69\164\171\137\x70\162\x6f\166\x69\x64\145\162"]);
        Ka:
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_ATTR);
    }
    public function remove($fP)
    {
        $q_ = $this->customerSetupFactory->create(["\x73\x65\164\x75\x70" => $this->moduleDataSetup]);
        $q_->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, $fP);
    }
}
