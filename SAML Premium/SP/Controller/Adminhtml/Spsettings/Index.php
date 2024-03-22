<?php


namespace MiniOrange\SP\Controller\Adminhtml\Spsettings;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Block\Sp;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResponseFactory;
use MiniOrange\SP\Helper\Curl;
class Index extends BaseAdminAction
{
    private $adminRoleModel;
    private $userGroupModel;
    protected $sp;
    protected $responseFactory;
    private $moduleDataSetup;
    protected $customerSetupFactory;
    private $attributeSetFactory;
    public function __construct(Context $Gc, PageFactory $VM, SPUtility $Kx, ManagerInterface $c0, LoggerInterface $hI, \Magento\Authorization\Model\ResourceModel\Role\Collection $bY, \Magento\Customer\Model\ResourceModel\Group\Collection $o1, Sp $vT, ModuleDataSetupInterface $jL, CustomerSetupFactory $ih, AttributeSetFactory $Wq, ResponseFactory $XF)
    {
        parent::__construct($Gc, $VM, $Kx, $c0, $hI, $vT);
        $this->adminRoleModel = $bY;
        $this->sp = $vT;
        $this->userGroupModel = $o1;
        $this->moduleDataSetup = $jL;
        $this->customerSetupFactory = $ih;
        $this->attributeSetFactory = $Wq;
        $this->responseFactory = $XF;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto HK;
        }
        $bc = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($bc == NULL)) {
            goto ci;
        }
        $MG = $this->spUtility->getCurrentAdminUser()->getData();
        $zG = $this->spUtility->getMagnetoVersion();
        $cN = $MG["\x65\155\141\151\x6c"];
        $Aq = $MG["\x66\151\162\163\x74\x6e\x61\x6d\x65"];
        $ko = $MG["\154\141\163\x74\x6e\141\155\x65"];
        $Lu = $this->spUtility->getBaseUrl();
        $D7 = array($Aq, $ko, $zG, $Lu);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($cN, "\111\x6e\163\164\141\x6c\x6c\x65\x64\40\123\165\x63\x63\x65\x73\163\146\165\x6c\x6c\171\x2d\x41\x63\143\157\165\156\164\x20\124\x61\x62", $D7);
        $this->spUtility->flushCache();
        ci:
        HK:
        try {
            $As = $this->getRequest()->getParams();
            $this->checkIfValidPlugin();
            if (empty($As["\x61\144\x64"])) {
                goto LM;
            }
            $this->spUtility->checkIdpLimit();
            LM:
            if (!$this->isFormOptionBeingSaved($As)) {
                goto mg;
            }
            if (!(!$this->spUtility->check_license_plan(3) && !$this->spUtility->check_license_plan(4))) {
                goto pj;
            }
            $Lw = $this->spUtility->getIDPApps();
            foreach ($Lw as $fR) {
                if (!($fR->getData()["\x69\x64\x70\x5f\x6e\x61\x6d\x65"] !== $As["\163\x61\x6d\x6c\x5f\151\x64\x65\x6e\x74\151\164\171\137\156\x61\x6d\x65"])) {
                    goto BX;
                }
                $ft = $fR->getData();
                $this->spUtility->deleteIDPApps((int) $ft["\x69\144"]);
                BX:
                Mf:
            }
            WT:
            pj:
            if ($As["\x6f\160\164\x69\157\x6e"] == "\163\x61\x76\145\x49\104\x50\x53\145\164\164\151\x6e\147\x73") {
                goto gB;
            }
            if (!($As["\157\160\x74\x69\x6f\156"] == "\x75\x70\x6c\x6f\141\x64\137\155\145\164\141\x64\x61\164\141\x5f\146\x69\x6c\145")) {
                goto d8;
            }
            $jA = "\151\144\160\x4d\x65\x74\141\144\141\x74\141\x2f";
            $Sv = "\155\x65\164\141\144\141\164\141\137\146\151\154\145";
            $Gb = $this->getRequest()->getFiles($Sv);
            $JY = $As["\x75\x70\154\x6f\x61\x64\x5f\x75\162\x6c"];
            if (!empty($As["\163\141\155\x6c\137\x69\x64\145\156\x74\151\x74\171\137\x6e\141\x6d\x65"]) || !empty($As["\163\145\x6c\x65\143\164\x65\x64\137\x70\x72\x6f\x76\151\144\145\162"]) && (!$this->spUtility->isBlank($Gb["\x74\155\160\137\x6e\x61\155\x65"]) || !$this->spUtility->isBlank($JY))) {
                goto sM;
            }
            if (empty($As["\x73\x61\155\154\137\x69\144\145\x6e\164\x69\x74\x79\137\x6e\141\x6d\x65"]) && !empty($As["\x73\145\x6c\145\143\x74\145\x64\x5f\x70\x72\157\166\x69\144\x65\x72"])) {
                goto Ns;
            }
            if (empty($As["\163\x61\x6d\x6c\x5f\151\x64\x65\x6e\x74\x69\x74\x79\137\x6e\x61\155\145"]) || $this->spUtility->isBlank($Gb["\164\155\160\137\156\x61\155\145"]) && $this->spUtility->isBlank($JY)) {
                goto yj;
            }
            goto Dp;
            sM:
            $he = array();
            $XC = !empty($As["\163\x61\x6d\154\137\x69\x64\145\x6e\164\151\164\x79\137\x6e\141\155\x65"]) ? $As["\x73\141\155\x6c\x5f\x69\144\x65\x6e\164\151\x74\171\137\x6e\141\155\x65"] : $As["\x73\145\154\145\143\164\145\x64\x5f\x70\x72\x6f\166\x69\144\145\162"];
            $t0 = preg_match("\57\x5b\x27\x5e\302\243\44\45\x26\x2a\50\x29\175\173\100\43\x7e\77\76\x20\74\x3e\54\174\x3d\x2b\xc2\xac\x2d\x5d\x2f", $XC);
            if (!$t0) {
                goto St;
            }
            $this->getMessageManager()->addErrorMessage("\x53\160\x65\143\x69\141\x6c\x20\x63\x68\141\162\x61\143\x74\x65\x72\x73\40\141\x72\145\x20\x6e\157\x74\40\x61\154\x6c\x6f\167\145\144\x20\151\156\40\164\x68\145\x20\111\x64\145\156\164\x69\164\x79\x20\120\162\x6f\166\x69\144\x65\162\40\116\x61\155\145\x21");
            goto rN;
            St:
            $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $XC);
            $this->spUtility->handle_upload_metadata($Gb, $JY, $As);
            $this->spUtility->reinitConfig();
            $this->spUtility->flushCache();
            $this->getMessageManager()->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            rN:
            goto Dp;
            Ns:
            $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $As["\163\x65\x6c\145\x63\164\145\144\x5f\160\162\157\166\151\x64\145\x72"]);
            $this->spUtility->flushCache();
            goto Dp;
            yj:
            $this->getMessageManager()->addErrorMessage("\116\x6f\40\x4d\x65\164\141\144\141\164\x61\40\x49\104\x50\40\x4e\x61\155\x65\57\x46\151\x6c\145\57\125\x52\114\x20\120\162\157\x76\151\144\145\x64\x2e");
            Dp:
            d8:
            goto VO;
            gB:
            if (!empty($As["\163\141\x6d\x6c\137\x69\x64\x65\x6e\x74\151\164\171\137\156\x61\x6d\145"])) {
                goto WA;
            }
            $As["\x73\x61\x6d\x6c\137\151\144\145\156\x74\151\x74\171\x5f\x6e\141\x6d\145"] = $As["\x73\145\x6c\145\x63\164\x65\144\x5f\160\x72\157\166\x69\x64\x65\162"];
            WA:
            $this->checkIfRequiredFieldsEmpty(array("\163\141\155\x6c\x5f\151\x64\145\156\x74\151\164\x79\137\x6e\141\155\145" => $As, "\163\x61\x6d\154\137\x69\163\x73\x75\x65\162" => $As, "\163\x61\x6d\x6c\x5f\154\157\147\x69\156\x5f\x75\x72\154" => $As, "\163\141\x6d\x6c\x5f\170\x35\60\71\137\x63\x65\x72\x74\151\x66\151\x63\141\164\145" => $As));
            $this->processValuesAndSaveData($As);
            if (!$this->spUtility->check_license_plan(3)) {
                goto tO;
            }
            $LW = $this->spUtility->getAdminUrl("\155\157\163\x70\163\141\155\154\x2f\151\x64\160\163\57\x69\156\x64\x65\x78");
            header("\114\157\x63\x61\164\x69\x6f\x6e\x3a" . $LW);
            exit;
            tO:
            $this->spUtility->flushCache();
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            VO:
            mg:
        } catch (\Exception $sS) {
            $this->messageManager->addErrorMessage($sS->getMessage());
            $this->logger->debug($sS->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\x53\x50\40\x53\145\164\164\151\156\147\163"), __("\x53\x50\x20\x53\x65\x74\164\x69\156\x67\x73"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function processValuesAndSaveData($As)
    {
        if (!(!empty($As["\157\160\164\x69\157\156"]) && $As["\x6f\160\164\151\157\156"] != "\147\157\102\x61\143\153")) {
            goto aQ;
        }
        $Lk = trim($As["\x73\141\x6d\x6c\x5f\x69\x64\x65\x6e\x74\151\164\171\137\x6e\141\155\x65"]);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\x69\x64\160\137\156\x61\155\x65"] === $Lk)) {
                goto xM;
            }
            $ft = $fR->getData();
            xM:
            iD:
        }
        AD:
        aQ:
        $xb = !empty($As["\x73\141\x6d\x6c\x5f\151\x73\x73\165\145\x72"]) ? trim($As["\x73\141\155\154\137\151\163\x73\x75\145\162"]) : '';
        $ln = !empty($As["\x73\x61\155\154\137\154\x6f\147\151\156\137\x75\x72\x6c"]) ? trim($As["\163\141\x6d\154\x5f\x6c\157\x67\151\156\137\165\162\154"]) : '';
        $dq = !empty($As["\x73\141\155\x6c\137\x6c\157\x67\x69\x6e\137\x62\151\156\x64\x69\156\x67\x5f\164\x79\x70\x65"]) ? $As["\163\x61\x6d\154\x5f\154\157\147\x69\x6e\x5f\x62\x69\156\x64\x69\x6e\x67\x5f\164\x79\160\145"] : '';
        $WY = !empty($As["\x73\x61\x6d\x6c\x5f\154\x6f\x67\157\x75\x74\137\165\162\154"]) ? trim($As["\163\x61\x6d\x6c\137\x6c\157\147\157\165\x74\x5f\x75\x72\x6c"]) : '';
        $B3 = !empty($As["\x73\141\x6d\x6c\137\x6c\x6f\x67\x6f\165\x74\137\142\151\156\x64\151\x6e\x67\137\164\171\x70\x65"]) ? $As["\x73\x61\155\154\x5f\x6c\x6f\x67\157\165\164\137\x62\151\x6e\x64\x69\x6e\147\137\164\x79\x70\145"] : '';
        $w_ = !empty($As["\163\141\155\154\x5f\170\65\60\x39\137\x63\145\x72\164\151\x66\x69\x63\x61\164\145"]) ? SAML2Utilities::sanitize_certificate($As["\163\x61\155\154\x5f\x78\65\60\x39\137\x63\x65\x72\x74\x69\x66\151\143\141\164\x65"]) : '';
        $a5 = !empty($As["\x73\141\x6d\x6c\x5f\162\145\163\160\x6f\x6e\163\x65\137\x73\x69\147\156\145\144"]) && $As["\163\141\155\x6c\137\162\x65\x73\160\x6f\156\163\x65\137\163\x69\x67\x6e\145\144"] == "\x59\145\163" ? 1 : 0;
        $f4 = !empty($As["\x73\x61\155\x6c\x5f\x61\x73\x73\145\x72\x74\x69\157\x6e\137\163\x69\x67\x6e\145\144"]) && $As["\163\x61\155\154\x5f\141\x73\x73\x65\162\164\151\157\x6e\137\x73\x69\x67\156\x65\144"] == "\131\x65\163" ? 1 : 0;
        $o9 = !empty($ft["\x73\150\x6f\x77\137\141\x64\155\151\156\137\x6c\x69\x6e\153"]) && $ft["\x73\150\157\x77\137\141\x64\x6d\151\x6e\x5f\x6c\x69\156\153"] == true ? 1 : 0;
        $vh = !empty($ft["\163\150\157\x77\137\143\165\163\164\x6f\x6d\x65\162\x5f\154\151\156\153"]) && $ft["\163\150\157\167\x5f\x63\x75\x73\x74\x6f\x6d\145\x72\137\x6c\x69\156\x6b"] == true ? 1 : 0;
        $w8 = !empty($ft["\x61\165\x74\157\137\143\162\145\x61\164\x65\137\x61\144\x6d\x69\156\137\165\x73\x65\x72\x73"]) && $ft["\x61\x75\164\157\137\143\x72\x65\x61\164\x65\137\141\x64\x6d\151\156\137\165\163\145\x72\163"] == true ? 1 : 0;
        $Gd = !empty($ft["\x61\165\164\x6f\x5f\143\162\x65\141\x74\x65\137\x63\x75\x73\164\x6f\x6d\x65\x72\x73"]) && $ft["\141\x75\x74\157\137\x63\x72\145\141\x74\x65\x5f\x63\x75\163\164\157\x6d\x65\x72\x73"] == true ? 1 : 0;
        $h0 = !empty($ft["\x64\x69\x73\x61\142\x6c\145\137\x62\x32\x63"]) && $ft["\x64\151\x73\x61\x62\x6c\145\x5f\x62\x32\x63"] == true ? 1 : 0;
        $M1 = !empty($ft["\146\x6f\162\x63\x65\137\x61\165\164\x68\x65\x6e\x74\x69\143\141\164\x69\157\156\x5f\167\151\x74\150\x5f\x69\144\x70"]) && $ft["\x66\157\x72\x63\145\x5f\x61\165\164\150\x65\156\x74\151\143\x61\164\x69\x6f\x6e\x5f\x77\151\x74\x68\x5f\151\144\x70"] == true ? 1 : 0;
        $M3 = !empty($ft["\141\165\164\157\x5f\162\145\144\x69\162\x65\x63\x74\137\164\x6f\x5f\x69\144\x70"]) && $ft["\141\x75\164\157\x5f\x72\145\x64\151\x72\x65\143\x74\x5f\x74\x6f\x5f\151\144\x70"] == true ? 1 : 0;
        $qr = !empty($ft["\x6c\151\x6e\x6b\137\164\x6f\137\x69\x6e\151\164\151\x61\x74\x65\137\x73\163\x6f"]) && $ft["\x6c\151\x6e\x6b\137\x74\157\137\x69\x6e\x69\x74\151\141\164\x65\137\163\x73\157"] == true ? 1 : 0;
        $S8 = !empty($ft["\x75\x70\x64\141\164\x65\x5f\x61\x74\x74\x72\x69\x62\x75\164\x65\x73\x5f\x6f\x6e\137\x6c\157\147\151\x6e"]) ? $ft["\x75\x70\144\141\x74\145\x5f\x61\164\164\x72\x69\142\x75\164\145\163\x5f\157\x6e\x5f\x6c\x6f\147\151\156"] : "\165\x6e\x63\150\x65\x63\x6b\145\144";
        $qx = !empty($ft["\x63\x72\145\x61\164\145\137\x6d\x61\x67\145\156\x74\157\137\x61\143\143\x6f\165\156\x74\x5f\x62\x79"]) ? $ft["\143\162\145\x61\x74\x65\x5f\155\141\147\x65\156\x74\x6f\x5f\141\143\143\x6f\x75\156\x74\x5f\142\x79"] : '';
        $cE = !empty($ft["\x65\x6d\141\x69\x6c\x5f\x61\164\164\162\x69\x62\165\x74\x65"]) ? $ft["\x65\x6d\141\x69\154\x5f\x61\x74\164\x72\151\142\165\x74\145"] : '';
        $x_ = !empty($ft["\x75\x73\x65\162\156\x61\x6d\x65\x5f\141\164\x74\x72\151\142\x75\x74\145"]) ? $ft["\165\x73\145\x72\156\x61\155\145\x5f\141\x74\x74\x72\151\142\165\164\x65"] : '';
        $Fw = !empty($ft["\x66\151\x72\x73\x74\x6e\x61\155\145\137\x61\164\164\x72\151\142\x75\164\x65"]) ? $ft["\x66\x69\162\163\164\156\141\155\x65\137\x61\x74\x74\162\x69\142\165\164\x65"] : '';
        $tV = !empty($ft["\154\141\163\164\x6e\x61\155\145\137\141\164\164\x72\151\142\x75\x74\x65"]) ? $ft["\154\141\163\x74\156\x61\155\x65\137\141\164\164\x72\x69\142\165\x74\x65"] : '';
        $Wr = !empty($ft["\147\x72\157\165\160\137\x61\164\164\162\151\x62\x75\164\x65"]) ? $ft["\x67\x72\x6f\x75\x70\137\x61\164\164\x72\x69\142\x75\x74\145"] : '';
        $T5 = !empty($ft["\142\x69\x6c\x6c\x69\156\147\x5f\143\151\x74\x79\137\141\x74\164\x72\151\142\165\x74\145"]) ? $ft["\142\151\154\x6c\x69\156\x67\137\143\151\164\171\x5f\x61\164\164\x72\x69\x62\x75\x74\x65"] : '';
        $ab = !empty($ft["\x62\x69\x6c\154\x69\156\x67\137\x73\164\x61\x74\145\x5f\x61\x74\164\x72\151\x62\165\164\145"]) ? $ft["\x62\151\x6c\154\x69\x6e\x67\137\x73\164\x61\x74\x65\x5f\x61\x74\164\162\x69\x62\x75\164\x65"] : '';
        $TC = !empty($ft["\x62\151\x6c\x6c\x69\156\x67\137\143\x6f\x75\156\x74\x72\x79\x5f\x61\164\164\162\x69\x62\x75\164\x65"]) ? $ft["\142\151\x6c\x6c\x69\x6e\x67\x5f\x63\x6f\165\156\164\162\171\x5f\141\164\x74\x72\x69\142\x75\x74\145"] : '';
        $Au = !empty($ft["\142\x69\x6c\x6c\x69\x6e\147\x5f\141\144\144\162\x65\163\163\x5f\x61\164\x74\x72\x69\142\165\x74\145"]) ? $ft["\x62\x69\154\x6c\151\x6e\x67\137\x61\144\144\162\145\x73\x73\x5f\x61\x74\164\x72\x69\142\x75\164\x65"] : '';
        $hX = !empty($ft["\142\x69\154\154\151\x6e\147\137\x70\150\x6f\156\x65\137\141\x74\164\x72\x69\142\165\164\145"]) ? $ft["\x62\151\154\x6c\151\156\147\x5f\160\150\157\156\x65\x5f\x61\164\164\162\151\x62\x75\x74\145"] : '';
        $Sr = !empty($ft["\142\x69\x6c\154\151\156\147\137\172\151\x70\137\x61\x74\164\x72\x69\x62\x75\x74\x65"]) ? $ft["\142\x69\x6c\x6c\x69\156\147\x5f\x7a\151\160\x5f\x61\x74\164\x72\151\142\x75\x74\145"] : '';
        $J2 = !empty($ft["\x73\150\x69\160\160\151\156\x67\x5f\x63\x69\164\171\137\x61\x74\164\x72\x69\x62\165\164\x65"]) ? $ft["\163\150\151\160\160\151\x6e\147\137\x63\151\164\171\137\x61\164\x74\162\x69\142\165\164\145"] : '';
        $mG = !empty($ft["\x73\150\x69\x70\x70\x69\x6e\x67\137\x73\x74\x61\164\x65\x5f\x61\164\x74\162\x69\142\165\164\145"]) ? $ft["\x73\x68\151\x70\x70\x69\156\147\137\x73\164\x61\x74\x65\137\141\164\164\162\151\142\x75\164\x65"] : '';
        $iz = !empty($ft["\163\150\x69\x70\160\151\x6e\x67\x5f\x63\157\x75\x6e\x74\x72\x79\137\x61\x74\164\x72\x69\x62\x75\x74\145"]) ? $ft["\163\x68\x69\160\x70\x69\x6e\147\137\143\x6f\165\156\164\162\171\x5f\x61\164\x74\x72\x69\x62\x75\x74\x65"] : '';
        $mq = !empty($ft["\x73\150\151\x70\160\151\x6e\x67\137\x61\x64\144\162\145\x73\x73\137\141\x74\x74\162\x69\142\x75\x74\145"]) ? $ft["\163\x68\151\160\160\151\x6e\x67\x5f\141\144\144\162\145\163\163\137\141\164\164\x72\151\142\165\x74\x65"] : '';
        $Qb = !empty($ft["\163\150\x69\x70\160\151\x6e\x67\137\x70\x68\157\156\145\x5f\141\x74\164\x72\151\142\165\x74\145"]) ? $ft["\163\x68\151\x70\160\151\156\x67\137\160\x68\x6f\x6e\x65\137\x61\x74\x74\162\151\142\x75\x74\145"] : '';
        $gE = !empty($ft["\163\x68\151\160\x70\151\156\147\137\172\x69\160\137\x61\x74\164\x72\x69\x62\165\x74\145"]) ? $ft["\163\x68\x69\x70\160\151\156\147\137\172\151\x70\x5f\x61\164\164\162\x69\142\x75\164\145"] : '';
        $lL = !empty($ft["\x62\x32\x62\137\x61\164\x74\162\x69\x62\165\164\145"]) ? $ft["\142\62\142\x5f\141\164\x74\162\x69\x62\165\x74\x65"] : '';
        $Rr = !empty($ft["\x63\x75\163\x74\x6f\155\137\164\141\142\x6c\145\x6e\141\x6d\145"]) ? $ft["\x63\x75\x73\164\157\155\x5f\164\141\x62\154\145\156\141\x6d\145"] : '';
        $Rv = !empty($ft["\x63\x75\x73\x74\x6f\x6d\137\x61\x74\164\162\151\142\165\x74\145\x73"]) ? $ft["\x63\165\x73\164\x6f\155\137\141\164\164\x72\151\142\x75\x74\145\163"] : '';
        $i3 = !empty($ft["\x64\x6f\137\x6e\x6f\164\x5f\x61\165\x74\x6f\143\x72\x65\x61\164\x65\x5f\x69\146\137\162\x6f\x6c\145\x73\x5f\156\x6f\x74\137\x6d\141\160\x70\x65\x64"]) ? $ft["\144\157\x5f\156\x6f\x74\x5f\x61\x75\x74\x6f\143\162\145\141\x74\145\137\x69\146\x5f\162\157\x6c\x65\x73\137\156\157\x74\x5f\x6d\x61\160\160\145\144"] : "\165\156\x63\150\145\143\x6b\145\x64";
        $AF = !empty($ft["\x75\160\x64\141\164\x65\137\142\x61\143\x6b\x65\156\x64\x5f\162\157\154\x65\163\137\157\x6e\137\x73\x73\157"]) ? $ft["\165\160\x64\141\164\145\x5f\x62\x61\143\153\x65\x6e\x64\x5f\162\157\154\145\163\137\x6f\156\x5f\x73\163\x6f"] : "\165\156\x63\150\145\143\x6b\145\144";
        $Jg = !empty($ft["\x75\x70\x64\141\164\x65\137\x66\x72\x6f\156\x74\145\156\144\137\147\x72\157\165\x70\x73\137\x6f\156\x5f\x73\x73\x6f"]) ? $ft["\x75\x70\144\x61\164\x65\137\146\162\x6f\x6e\164\x65\156\144\x5f\147\162\157\x75\x70\163\137\x6f\x6e\137\163\163\157"] : "\x75\156\x63\x68\145\143\x6b\145\x64";
        $Vc = !empty($ft["\x64\x65\146\x61\165\154\x74\137\147\x72\x6f\x75\x70"]) ? $ft["\x64\x65\x66\x61\x75\x6c\x74\x5f\147\162\157\165\x70"] : '';
        $LQ = !empty($ft["\x64\x65\x66\141\x75\154\x74\x5f\x72\x6f\154\145"]) ? $ft["\x64\145\x66\141\x75\x6c\164\x5f\162\x6f\x6c\x65"] : '';
        $CL = !empty($ft["\x67\162\157\165\160\x73\x5f\155\x61\160\160\x65\x64"]) ? $ft["\x67\x72\x6f\x75\x70\163\x5f\155\x61\x70\160\145\x64"] : '';
        $sG = !empty($ft["\x72\x6f\154\145\x73\137\x6d\141\x70\x70\145\x64"]) ? $ft["\162\157\x6c\145\x73\137\155\141\x70\160\145\x64"] : '';
        $Uo = !empty($ft["\x73\x61\x6d\x6c\x5f\x6c\x6f\147\157\x75\x74\137\162\x65\x64\x69\162\x65\143\x74\137\165\x72\154"]) ? $ft["\163\x61\x6d\x6c\x5f\154\x6f\147\x6f\x75\x74\x5f\x72\x65\x64\151\162\145\143\164\x5f\165\162\x6c"] : '';
        $wj = !empty($ft["\163\141\155\x6c\137\x65\156\141\x62\154\145\x5f\x62\x69\x6c\x6c\x69\x6e\x67\141\156\144\163\x68\151\160\x70\151\x6e\147"]) ? $ft["\163\x61\x6d\154\137\145\156\141\142\x6c\x65\137\142\151\x6c\x6c\151\156\x67\x61\x6e\144\163\x68\x69\160\160\151\156\147"] : "\156\157\156\145";
        $fE = !empty($ft["\x73\141\155\154\x5f\x73\141\x6d\x65\x61\x73\x62\151\x6c\154\x69\156\x67"]) ? $ft["\x73\141\155\154\x5f\163\141\x6d\145\141\163\142\151\154\154\x69\x6e\x67"] : "\x6e\157\156\145";
        if (!is_null($ft)) {
            goto SG;
        }
        $this->spUtility->checkIdpLimit();
        goto Ot;
        SG:
        $this->spUtility->deleteIDPApps((int) $ft["\151\144"]);
        Ot:
        $this->spUtility->setIDPApps($Lk, $xb, $ln, $dq, $WY, $B3, $w_, $a5, $f4, $o9, $vh, $w8, $Gd, $h0, $M1, $M3, $qr, $S8, $qx, $cE, $x_, $Fw, $tV, $Wr, $T5, $ab, $TC, $Au, $hX, $Sr, $J2, $mG, $iz, $mq, $Qb, $gE, $lL, $Rr, $Rv, $i3, $AF, $Jg, $Vc, $LQ, $CL, $sG, $Uo, $wj, $fE);
        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $Lk);
        $this->spUtility->setStoreConfig(SPConstants::IDP_NAME, $Lk);
        $this->spUtility->reinitConfig();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_SPSETTINGS);
    }
}
