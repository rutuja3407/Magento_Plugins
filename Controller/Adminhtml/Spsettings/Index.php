<?php


namespace MiniOrange\SP\Controller\Adminhtml\Spsettings;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\Curl;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class Index extends BaseAdminAction
{
    protected $sp;
    protected $responseFactory;
    protected $customerSetupFactory;
    private $adminRoleModel;
    private $userGroupModel;
    private $moduleDataSetup;
    private $attributeSetFactory;
    public function __construct(Context $gt, PageFactory $Jq, SPUtility $fR, ManagerInterface $b_, LoggerInterface $kU, \Magento\Authorization\Model\ResourceModel\Role\Collection $sP, \Magento\Customer\Model\ResourceModel\Group\Collection $i6, Sp $ou, ModuleDataSetupInterface $iD, CustomerSetupFactory $Kn, AttributeSetFactory $Z8, ResponseFactory $Jv)
    {
        parent::__construct($gt, $Jq, $fR, $b_, $kU, $ou);
        $this->adminRoleModel = $sP;
        $this->sp = $ou;
        $this->userGroupModel = $i6;
        $this->moduleDataSetup = $iD;
        $this->customerSetupFactory = $Kn;
        $this->attributeSetFactory = $Z8;
        $this->responseFactory = $Jv;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto PG;
        }
        $Az = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($Az == NULL)) {
            goto l5;
        }
        $fU = $this->spUtility->getCurrentAdminUser()->getData();
        $RH = $this->spUtility->getMagnetoVersion();
        $ii = $fU["\145\155\x61\x69\x6c"];
        $FO = $fU["\146\x69\x72\163\164\156\x61\x6d\x65"];
        $Fo = $fU["\x6c\141\x73\164\156\x61\155\145"];
        $kz = $this->spUtility->getBaseUrl();
        $jT = array($FO, $Fo, $RH, $kz);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($ii, "\111\156\163\x74\x61\x6c\154\145\144\x20\123\165\x63\x63\145\163\163\x66\x75\154\x6c\171\x2d\x41\x63\143\x6f\165\156\x74\x20\124\141\x62", $jT);
        $this->spUtility->flushCache();
        l5:
        PG:
        try {
            $Te = $this->getRequest()->getParams();
            $this->checkIfValidPlugin();
            if (empty($Te["\x61\x64\x64"])) {
                goto Fq;
            }
            $this->spUtility->checkIdpLimit();
            Fq:
            if (!$this->isFormOptionBeingSaved($Te)) {
                goto XF;
            }
            if (!(!$this->spUtility->check_license_plan(3) && !$this->spUtility->check_license_plan(4))) {
                goto gK;
            }
            $yG = $this->spUtility->getIDPApps();
            foreach ($yG as $ub) {
                if (!($ub->getData()["\x69\x64\160\137\x6e\141\155\145"] !== $Te["\x73\x61\155\154\x5f\151\x64\x65\156\164\151\x74\x79\x5f\x6e\141\x6d\145"])) {
                    goto lt;
                }
                $hR = $ub->getData();
                $this->spUtility->deleteIDPApps((int) $hR["\151\x64"]);
                lt:
                ic:
            }
            WG:
            gK:
            if ($Te["\x6f\160\164\151\157\x6e"] == "\163\x61\x76\145\x49\x44\120\x53\x65\164\164\x69\x6e\147\x73") {
                goto ZG;
            }
            if (!($Te["\x6f\160\164\151\x6f\156"] == "\x75\160\x6c\157\141\144\137\155\145\x74\x61\144\x61\x74\x61\x5f\x66\x69\x6c\145")) {
                goto F_;
            }
            $Ds = "\151\x64\160\x4d\145\x74\x61\x64\x61\164\x61\57";
            $GB = "\x6d\145\164\x61\x64\x61\x74\141\x5f\146\151\x6c\x65";
            $SI = $this->getRequest()->getFiles($GB);
            $At = $Te["\165\160\154\157\x61\144\x5f\x75\162\x6c"];
            if (!empty($Te["\x73\141\155\x6c\137\x69\x64\x65\156\x74\x69\164\x79\137\x6e\x61\x6d\145"]) || !empty($Te["\x73\x65\154\145\143\x74\x65\x64\x5f\x70\162\157\x76\x69\144\x65\x72"]) && (!$this->spUtility->isBlank($SI["\164\155\160\137\156\x61\x6d\x65"]) || !$this->spUtility->isBlank($At))) {
                goto Ow;
            }
            if (empty($Te["\x73\141\155\x6c\137\x69\x64\145\156\164\151\x74\171\x5f\x6e\141\155\145"]) && !empty($Te["\163\x65\154\x65\x63\164\x65\x64\137\x70\162\x6f\x76\x69\x64\x65\162"])) {
                goto Zp;
            }
            if (empty($Te["\x73\141\x6d\154\x5f\x69\144\x65\156\x74\x69\x74\171\x5f\x6e\141\x6d\x65"]) || $this->spUtility->isBlank($SI["\x74\x6d\160\137\156\x61\x6d\x65"]) && $this->spUtility->isBlank($At)) {
                goto tu;
            }
            goto rC;
            Ow:
            $ob = array();
            $dp = !empty($Te["\x73\x61\155\x6c\x5f\151\x64\145\x6e\164\151\x74\171\x5f\156\141\155\145"]) ? $Te["\163\x61\155\x6c\137\x69\x64\145\156\x74\151\164\171\137\156\141\x6d\x65"] : $Te["\163\145\x6c\x65\143\164\x65\144\137\160\x72\x6f\166\x69\x64\145\x72"];
            $UG = preg_match("\x2f\x5b\x27\136\302\243\44\45\46\52\x28\x29\175\x7b\x40\x23\x7e\77\x3e\x20\x3c\x3e\x2c\x7c\x3d\53\xc2\254\x2d\135\x2f", $dp);
            if (!$UG) {
                goto TB;
            }
            $this->getMessageManager()->addErrorMessage("\x53\x70\x65\143\151\141\x6c\x20\x63\x68\x61\162\141\x63\164\x65\x72\x73\40\x61\x72\145\x20\x6e\x6f\164\40\141\x6c\x6c\157\x77\x65\144\x20\x69\156\40\x74\x68\x65\x20\111\x64\x65\x6e\164\x69\164\171\x20\x50\162\x6f\166\151\144\x65\162\40\116\141\155\145\x21");
            goto rA;
            TB:
            $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $dp);
            $this->spUtility->handle_upload_metadata($SI, $At, $Te);
            $this->spUtility->reinitConfig();
            $this->spUtility->flushCache();
            $this->getMessageManager()->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            rA:
            goto rC;
            Zp:
            $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $Te["\x73\145\154\x65\143\x74\145\x64\x5f\160\162\x6f\x76\x69\144\145\x72"]);
            $this->spUtility->flushCache();
            goto rC;
            tu:
            $this->getMessageManager()->addErrorMessage("\116\157\40\x4d\145\x74\141\x64\141\x74\141\40\x49\104\120\40\x4e\x61\x6d\145\x2f\106\151\154\145\x2f\125\122\x4c\x20\x50\162\x6f\166\151\144\x65\144\56");
            rC:
            F_:
            goto v9;
            ZG:
            if (!empty($Te["\163\x61\x6d\x6c\x5f\151\x64\145\x6e\164\x69\164\x79\x5f\x6e\141\155\145"])) {
                goto z8;
            }
            $Te["\x73\141\155\154\137\151\144\x65\156\164\x69\x74\171\137\x6e\x61\x6d\145"] = $Te["\163\145\154\x65\143\x74\145\144\x5f\x70\162\157\166\x69\x64\x65\162"];
            z8:
            $this->checkIfRequiredFieldsEmpty(array("\x73\x61\155\x6c\x5f\x69\144\x65\156\x74\x69\x74\x79\137\156\141\x6d\x65" => $Te, "\x73\x61\x6d\x6c\x5f\x69\163\x73\165\x65\x72" => $Te, "\x73\x61\155\154\137\x6c\157\147\x69\156\137\165\162\x6c" => $Te, "\x73\x61\x6d\154\137\x78\x35\x30\x39\x5f\x63\x65\162\164\151\146\151\x63\x61\164\x65" => $Te));
            $this->processValuesAndSaveData($Te);
            if (!$this->spUtility->check_license_plan(3)) {
                goto Oi;
            }
            $JQ = $this->spUtility->getAdminUrl("\x6d\157\163\x70\163\141\155\154\57\x69\x64\x70\163\57\x69\156\144\145\170");
            header("\114\x6f\x63\141\x74\151\157\156\x3a" . $JQ);
            exit;
            Oi:
            $this->spUtility->flushCache();
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            v9:
            XF:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->logger->debug($IR->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\x53\120\40\x53\x65\x74\x74\x69\156\x67\163"), __("\x53\120\x20\x53\145\x74\164\x69\x6e\147\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function processValuesAndSaveData($Te)
    {
        if (!(!empty($Te["\157\x70\x74\151\157\156"]) && $Te["\157\160\x74\x69\x6f\x6e"] != "\147\157\x42\141\143\153")) {
            goto Hk;
        }
        $gu = trim($Te["\x73\x61\155\x6c\137\x69\x64\x65\x6e\x74\151\164\171\137\156\x61\155\145"]);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\x64\160\x5f\x6e\x61\x6d\145"] === $gu)) {
                goto K5;
            }
            $hR = $ub->getData();
            K5:
            BW:
        }
        pZ:
        Hk:
        $FW = !empty($Te["\x73\x61\x6d\154\137\151\163\x73\165\x65\x72"]) ? trim($Te["\163\x61\x6d\x6c\137\x69\x73\163\165\145\162"]) : '';
        $I_ = !empty($Te["\163\141\155\x6c\x5f\154\x6f\147\151\x6e\x5f\x75\x72\154"]) ? trim($Te["\163\141\x6d\154\137\x6c\157\x67\151\156\x5f\x75\162\154"]) : '';
        $CI = !empty($Te["\163\x61\155\x6c\x5f\x6c\x6f\147\x69\x6e\x5f\x62\x69\x6e\144\151\156\147\137\164\x79\x70\x65"]) ? $Te["\x73\141\155\154\137\x6c\157\x67\151\156\137\x62\151\156\x64\151\156\x67\137\x74\171\160\x65"] : '';
        $tb = !empty($Te["\x73\141\155\154\137\154\157\147\157\x75\164\137\165\x72\154"]) ? trim($Te["\x73\x61\x6d\154\x5f\154\x6f\147\157\165\164\137\x75\162\x6c"]) : '';
        $fF = !empty($Te["\x73\141\x6d\154\x5f\154\x6f\147\x6f\165\164\137\x62\151\x6e\x64\x69\156\147\x5f\x74\x79\x70\x65"]) ? $Te["\x73\141\155\x6c\137\154\157\147\x6f\x75\164\137\x62\x69\x6e\x64\x69\x6e\147\137\x74\x79\x70\145"] : '';
        $zS = !empty($Te["\163\x61\155\154\137\170\65\60\x39\137\x63\145\162\x74\151\x66\151\143\x61\164\x65"]) ? SAML2Utilities::sanitize_certificate($Te["\x73\x61\155\154\137\x78\65\60\x39\137\x63\x65\162\x74\151\x66\x69\143\x61\164\145"]) : '';
        $LT = !empty($Te["\163\141\x6d\x6c\x5f\162\x65\163\x70\157\156\163\x65\x5f\x73\151\147\x6e\145\x64"]) && $Te["\163\x61\155\x6c\137\x72\x65\163\160\157\156\163\x65\137\x73\151\x67\156\145\x64"] == "\131\145\x73" ? 1 : 0;
        $GK = !empty($Te["\x73\141\x6d\x6c\137\141\x73\x73\145\x72\x74\x69\x6f\x6e\x5f\x73\x69\147\x6e\145\x64"]) && $Te["\163\x61\x6d\x6c\x5f\141\163\x73\x65\162\164\x69\157\156\x5f\163\151\x67\x6e\145\144"] == "\x59\x65\163" ? 1 : 0;
        $y4 = !empty($hR["\163\150\x6f\167\x5f\141\144\x6d\151\x6e\137\x6c\x69\x6e\x6b"]) && $hR["\163\x68\157\x77\x5f\141\x64\x6d\151\156\x5f\154\x69\x6e\153"] == true ? 1 : 0;
        $t8 = !empty($hR["\x73\150\x6f\167\137\143\165\163\164\157\155\x65\162\x5f\x6c\x69\x6e\x6b"]) && $hR["\x73\x68\x6f\x77\137\x63\165\x73\x74\x6f\155\145\x72\x5f\154\151\156\153"] == true ? 1 : 0;
        $Ab = !empty($hR["\x61\x75\x74\x6f\x5f\143\x72\145\141\164\145\x5f\x61\x64\x6d\151\x6e\137\x75\x73\x65\x72\163"]) && $hR["\141\165\164\x6f\x5f\143\162\145\x61\x74\145\137\141\x64\155\x69\x6e\137\165\163\145\x72\163"] == true ? 1 : 0;
        $Cx = !empty($hR["\x61\x75\164\157\x5f\143\162\x65\141\x74\145\137\x63\165\163\x74\157\x6d\145\x72\x73"]) && $hR["\141\165\x74\x6f\x5f\143\x72\x65\x61\x74\x65\137\x63\165\163\x74\157\x6d\145\162\x73"] == true ? 1 : 0;
        $kY = !empty($hR["\144\151\x73\141\142\x6c\x65\x5f\142\x32\143"]) && $hR["\x64\151\x73\x61\x62\x6c\145\x5f\x62\62\x63"] == true ? 1 : 0;
        $ni = !empty($hR["\146\x6f\162\x63\x65\137\141\x75\x74\x68\x65\156\x74\151\x63\x61\164\x69\x6f\x6e\x5f\x77\x69\164\150\x5f\x69\x64\160"]) && $hR["\x66\x6f\162\143\145\x5f\141\x75\x74\150\x65\x6e\164\x69\143\x61\164\x69\157\x6e\x5f\167\x69\164\x68\x5f\x69\x64\160"] == true ? 1 : 0;
        $j2 = !empty($hR["\141\x75\x74\157\x5f\162\x65\x64\x69\162\145\143\164\x5f\164\157\x5f\151\144\x70"]) && $hR["\141\x75\x74\x6f\x5f\x72\145\x64\151\x72\145\143\x74\137\x74\157\x5f\151\144\160"] == true ? 1 : 0;
        $zL = !empty($hR["\x6c\x69\156\153\137\x74\x6f\x5f\x69\156\x69\x74\151\x61\164\145\137\x73\x73\157"]) && $hR["\x6c\151\156\153\x5f\164\157\x5f\x69\x6e\x69\164\x69\141\164\x65\137\163\163\157"] == true ? 1 : 0;
        $Ql = !empty($hR["\x75\160\x64\141\164\x65\x5f\x61\x74\x74\162\151\142\165\164\x65\x73\x5f\x6f\156\137\154\x6f\147\x69\156"]) ? $hR["\165\x70\x64\x61\164\x65\x5f\x61\164\164\x72\x69\x62\x75\164\145\163\137\157\156\137\x6c\x6f\x67\x69\x6e"] : "\165\x6e\143\x68\x65\143\153\145\x64";
        $Hr = !empty($hR["\x63\x72\x65\x61\164\x65\x5f\x6d\141\x67\145\156\164\157\x5f\141\x63\x63\x6f\165\x6e\164\137\x62\x79"]) ? $hR["\x63\x72\x65\x61\164\145\137\155\141\147\x65\x6e\164\157\137\141\x63\143\x6f\165\156\x74\137\142\x79"] : '';
        $Qx = !empty($hR["\145\155\141\151\x6c\x5f\141\x74\x74\162\151\x62\165\164\145"]) ? $hR["\x65\155\141\x69\154\x5f\141\164\164\x72\151\x62\165\x74\145"] : '';
        $pf = !empty($hR["\165\x73\x65\162\x6e\141\155\145\137\141\164\x74\162\151\x62\x75\x74\x65"]) ? $hR["\165\x73\145\162\x6e\141\x6d\145\137\141\164\x74\x72\x69\142\165\164\145"] : '';
        $Z3 = !empty($hR["\146\151\x72\x73\x74\x6e\x61\155\x65\137\141\164\x74\162\x69\142\x75\164\145"]) ? $hR["\x66\x69\x72\163\164\156\x61\155\145\137\x61\164\x74\x72\151\142\165\164\145"] : '';
        $ph = !empty($hR["\154\x61\163\x74\x6e\141\x6d\145\x5f\141\164\x74\x72\x69\x62\x75\164\x65"]) ? $hR["\154\x61\163\x74\156\141\x6d\145\137\141\x74\x74\x72\151\x62\165\x74\145"] : '';
        $qS = !empty($hR["\x67\162\157\x75\160\137\x61\164\x74\162\x69\142\165\164\x65"]) ? $hR["\147\162\157\x75\160\137\x61\164\x74\x72\151\x62\165\164\x65"] : '';
        $pY = !empty($hR["\x62\151\154\154\151\x6e\x67\137\143\151\x74\x79\137\x61\x74\164\x72\x69\x62\165\164\x65"]) ? $hR["\x62\x69\154\x6c\151\156\x67\137\143\151\164\171\137\141\164\164\x72\151\x62\165\x74\x65"] : '';
        $mP = !empty($hR["\x62\151\154\x6c\x69\156\x67\x5f\x73\x74\x61\x74\x65\x5f\x61\x74\164\x72\151\x62\x75\x74\145"]) ? $hR["\142\x69\x6c\x6c\x69\x6e\147\137\x73\164\141\x74\145\137\141\x74\164\162\151\x62\165\x74\145"] : '';
        $gr = !empty($hR["\x62\x69\x6c\x6c\x69\x6e\147\137\x63\x6f\x75\156\x74\162\x79\137\x61\x74\x74\x72\x69\x62\x75\x74\x65"]) ? $hR["\x62\x69\154\x6c\151\156\x67\x5f\143\157\165\156\164\x72\x79\x5f\141\x74\x74\x72\151\142\x75\x74\x65"] : '';
        $lF = !empty($hR["\142\x69\154\154\151\x6e\x67\137\141\144\x64\162\145\x73\163\x5f\141\164\x74\162\x69\x62\165\164\x65"]) ? $hR["\x62\x69\x6c\154\x69\156\x67\x5f\141\144\x64\162\x65\x73\x73\x5f\x61\x74\x74\x72\151\x62\x75\164\x65"] : '';
        $M8 = !empty($hR["\142\x69\154\154\151\156\147\x5f\x70\x68\x6f\156\x65\x5f\141\164\164\162\151\142\165\x74\x65"]) ? $hR["\x62\151\154\x6c\x69\156\147\x5f\x70\x68\157\x6e\x65\x5f\x61\164\x74\x72\151\142\x75\x74\x65"] : '';
        $h5 = !empty($hR["\142\x69\154\x6c\x69\x6e\147\x5f\172\151\x70\x5f\141\164\164\x72\x69\x62\165\164\x65"]) ? $hR["\142\x69\x6c\x6c\x69\156\x67\x5f\x7a\x69\x70\x5f\x61\x74\164\162\151\142\x75\164\x65"] : '';
        $se = !empty($hR["\x73\150\151\160\x70\151\x6e\147\137\143\151\x74\x79\x5f\141\x74\164\x72\151\142\165\x74\x65"]) ? $hR["\x73\150\151\160\160\151\156\147\137\143\151\x74\x79\137\141\164\x74\162\151\x62\165\164\145"] : '';
        $zd = !empty($hR["\x73\150\x69\x70\x70\x69\x6e\147\137\163\x74\x61\x74\145\137\x61\164\x74\x72\x69\x62\x75\164\145"]) ? $hR["\163\150\151\160\160\x69\156\147\x5f\163\x74\141\164\x65\x5f\141\164\x74\x72\151\x62\x75\164\x65"] : '';
        $il = !empty($hR["\x73\150\151\x70\160\x69\156\x67\137\x63\x6f\165\x6e\x74\162\x79\x5f\141\x74\164\x72\x69\142\165\x74\x65"]) ? $hR["\x73\150\x69\x70\160\151\156\147\x5f\x63\157\x75\x6e\x74\x72\x79\x5f\x61\164\164\162\151\142\x75\164\145"] : '';
        $We = !empty($hR["\x73\x68\x69\160\x70\151\x6e\x67\x5f\141\144\144\162\145\163\163\137\x61\x74\x74\x72\x69\x62\165\164\x65"]) ? $hR["\163\x68\x69\160\x70\151\x6e\147\x5f\141\144\144\x72\145\x73\x73\137\x61\164\164\x72\151\x62\x75\164\145"] : '';
        $Qn = !empty($hR["\163\x68\151\160\160\x69\x6e\147\137\x70\x68\x6f\x6e\x65\137\x61\x74\164\162\151\x62\x75\x74\145"]) ? $hR["\163\150\151\x70\x70\x69\x6e\x67\137\160\x68\157\156\x65\x5f\x61\x74\x74\x72\151\x62\x75\x74\x65"] : '';
        $Pt = !empty($hR["\163\150\151\x70\x70\x69\156\147\x5f\x7a\151\x70\x5f\x61\x74\164\x72\x69\142\x75\164\145"]) ? $hR["\x73\x68\151\x70\160\151\x6e\x67\137\x7a\151\160\x5f\141\164\x74\162\x69\x62\x75\164\x65"] : '';
        $Dx = !empty($hR["\x62\62\142\137\x61\x74\x74\162\151\x62\x75\x74\145"]) ? $hR["\x62\x32\x62\x5f\x61\164\164\x72\x69\142\x75\x74\145"] : '';
        $C5 = !empty($hR["\143\165\x73\164\x6f\155\137\164\141\x62\x6c\145\156\141\155\x65"]) ? $hR["\143\165\x73\164\x6f\x6d\x5f\x74\141\142\x6c\x65\x6e\x61\155\145"] : '';
        $V9 = !empty($hR["\x63\x75\x73\164\157\155\x5f\x61\x74\x74\x72\x69\142\165\164\145\163"]) ? $hR["\x63\165\x73\164\x6f\x6d\137\141\x74\x74\162\x69\142\165\164\x65\163"] : '';
        $WD = !empty($hR["\x64\157\x5f\156\157\164\137\x61\165\x74\x6f\143\162\145\x61\x74\x65\137\151\x66\137\162\x6f\154\145\x73\137\156\x6f\164\137\155\141\160\160\x65\x64"]) ? $hR["\x64\x6f\137\x6e\x6f\x74\137\x61\165\x74\157\x63\x72\x65\141\x74\145\137\x69\146\137\162\157\x6c\145\x73\137\156\157\x74\137\155\141\x70\x70\x65\144"] : "\x75\x6e\x63\x68\x65\143\153\145\144";
        $VJ = !empty($hR["\165\x70\x64\x61\x74\145\x5f\142\141\x63\x6b\x65\156\144\137\x72\x6f\154\x65\x73\137\157\156\x5f\x73\163\x6f"]) ? $hR["\x75\x70\144\141\x74\x65\137\x62\x61\x63\x6b\145\156\x64\x5f\x72\x6f\x6c\x65\163\x5f\x6f\x6e\137\x73\163\157"] : "\165\x6e\143\150\145\143\x6b\x65\144";
        $mk = !empty($hR["\x75\x70\144\141\164\x65\137\x66\x72\x6f\156\x74\x65\156\x64\x5f\147\x72\x6f\165\160\x73\137\x6f\x6e\x5f\x73\x73\157"]) ? $hR["\x75\x70\x64\141\164\x65\x5f\x66\x72\x6f\156\x74\x65\156\144\137\147\162\x6f\165\160\x73\x5f\157\156\137\163\163\x6f"] : "\165\x6e\143\x68\x65\143\x6b\145\x64";
        $YO = !empty($hR["\x64\x65\x66\141\165\154\164\137\x67\162\157\x75\x70"]) ? $hR["\x64\x65\146\141\165\x6c\164\137\x67\162\x6f\165\x70"] : '';
        $Le = !empty($hR["\x64\145\146\141\x75\x6c\164\137\x72\x6f\x6c\145"]) ? $hR["\144\145\x66\141\165\x6c\164\137\162\157\x6c\145"] : '';
        $Qu = !empty($hR["\x67\162\157\x75\x70\x73\x5f\155\141\x70\160\x65\x64"]) ? $hR["\x67\x72\x6f\x75\x70\x73\x5f\155\x61\160\160\x65\144"] : '';
        $by = !empty($hR["\162\x6f\x6c\145\x73\137\x6d\x61\x70\x70\x65\x64"]) ? $hR["\x72\x6f\x6c\145\x73\x5f\x6d\x61\x70\160\145\x64"] : '';
        $bw = !empty($hR["\163\x61\155\x6c\x5f\x6c\157\x67\x6f\165\x74\x5f\x72\145\x64\151\162\x65\x63\164\x5f\x75\162\x6c"]) ? $hR["\x73\141\x6d\154\x5f\x6c\x6f\x67\157\x75\x74\x5f\x72\x65\x64\x69\x72\145\x63\164\137\165\x72\x6c"] : '';
        $Yx = !empty($hR["\163\x61\x6d\x6c\x5f\x65\x6e\x61\142\x6c\x65\137\142\151\154\154\x69\156\x67\x61\x6e\144\163\x68\x69\x70\160\151\x6e\x67"]) ? $hR["\163\141\155\x6c\x5f\145\x6e\141\142\154\x65\137\x62\x69\154\154\151\x6e\x67\x61\x6e\144\163\150\151\x70\160\151\x6e\147"] : "\x6e\x6f\x6e\145";
        $lN = !empty($hR["\x73\x61\x6d\154\x5f\x73\x61\155\x65\x61\x73\142\x69\154\x6c\151\x6e\147"]) ? $hR["\163\141\155\154\137\163\141\155\145\141\x73\x62\x69\154\154\x69\x6e\147"] : "\x6e\157\156\145";
        $GE = !empty($hR["\155\157\137\x73\x61\155\154\137\150\x65\x61\x64\x6c\x65\x73\163\x5f\x73\163\157"]) && $hR["\x6d\157\137\163\141\x6d\154\137\150\x65\x61\x64\154\145\x73\x73\x5f\x73\163\157"] == true ? 1 : 0;
        $Q7 = !empty($hR["\155\x6f\x5f\163\141\x6d\154\x5f\x66\162\157\156\x74\145\156\x64\x5f\160\157\x73\x74\137\x75\162\154"]) ? $hR["\155\x6f\x5f\163\141\155\x6c\x5f\x66\x72\157\x6e\164\x65\x6e\x64\x5f\160\157\163\x74\x5f\x75\x72\154"] : '';
        if (!is_null($hR)) {
            goto WH;
        }
        $this->spUtility->checkIdpLimit();
        goto Mt;
        WH:
        $this->spUtility->deleteIDPApps((int) $hR["\151\x64"]);
        Mt:
        $this->spUtility->setIDPApps($gu, $FW, $I_, $CI, $tb, $fF, $zS, $LT, $GK, $y4, $t8, $Ab, $Cx, $kY, $ni, $j2, $zL, $Ql, $Hr, $Qx, $pf, $Z3, $ph, $qS, $pY, $mP, $gr, $lF, $M8, $h5, $se, $zd, $il, $We, $Qn, $Pt, $Dx, $C5, $V9, $WD, $VJ, $mk, $YO, $Le, $Qu, $by, $bw, $Yx, $lN, $GE, $Q7);
        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $gu);
        $this->spUtility->setStoreConfig(SPConstants::IDP_NAME, $gu);
        $this->spUtility->reinitConfig();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_SPSETTINGS);
    }
}
