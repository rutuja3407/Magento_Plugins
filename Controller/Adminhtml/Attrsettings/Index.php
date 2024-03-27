<?php


namespace MiniOrange\SP\Controller\Adminhtml\Attrsettings;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\View\Result\PageFactory;
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
    protected $customerSetupFactory;
    private $adminRoleModel;
    private $userGroupModel;
    private $moduleDataSetup;
    private $attributeSetFactory;
    public function __construct(Context $gt, PageFactory $Jq, SPUtility $fR, ManagerInterface $b_, LoggerInterface $kU, \Magento\Authorization\Model\ResourceModel\Role\Collection $sP, \Magento\Customer\Model\ResourceModel\Group\Collection $i6, Sp $ou, ModuleDataSetupInterface $iD, CustomerSetupFactory $Kn, AttributeSetFactory $Z8)
    {
        parent::__construct($gt, $Jq, $fR, $b_, $kU, $ou);
        $this->adminRoleModel = $sP;
        $this->sp = $ou;
        $this->userGroupModel = $i6;
        $this->moduleDataSetup = $iD;
        $this->customerSetupFactory = $Kn;
        $this->attributeSetFactory = $Z8;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto uc;
        }
        $Az = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($Az == NULL)) {
            goto bn;
        }
        $fU = $this->spUtility->getCurrentAdminUser()->getData();
        $RH = $this->spUtility->getMagnetoVersion();
        $ii = $fU["\x65\155\x61\x69\154"];
        $FO = $fU["\x66\x69\162\163\164\156\141\155\145"];
        $Fo = $fU["\x6c\141\x73\164\156\x61\155\145"];
        $kz = $this->spUtility->getBaseUrl();
        $jT = array($FO, $Fo, $RH, $kz);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($ii, "\111\x6e\163\x74\141\x6c\x6c\x65\x64\x20\123\165\143\143\x65\x73\x73\x66\x75\x6c\154\x79\55\x41\143\143\x6f\165\x6e\x74\x20\124\141\x62", $jT);
        $this->spUtility->flushCache();
        bn:
        uc:
        try {
            $Te = $this->getRequest()->getParams();
            $this->checkIfValidPlugin();
            if (!$this->isFormOptionBeingSaved($Te)) {
                goto Ry;
            }
            if (!(!empty($Te["\x6f\x70\164\151\157\x6e"]) && $Te["\x6f\160\164\151\x6f\156"] != "\x73\x61\x76\145\120\x72\x6f\166\151\x64\x65\x72")) {
                goto ZV;
            }
            $this->checkIfRequiredFieldsEmpty(["\x73\141\155\154\x5f\x61\x6d\137\165\163\145\x72\156\x61\x6d\145" => $Te, "\x73\141\x6d\154\x5f\x61\x6d\137\x61\x63\x63\157\x75\x6e\x74\x5f\155\x61\x74\143\150\x65\162" => $Te]);
            ZV:
            $this->processValuesAndSaveData($Te);
            $this->spUtility->flushCache();
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            $this->spUtility->reinitConfig();
            Ry:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->logger->debug($IR->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\101\x54\x54\x52\40\123\145\x74\x74\x69\x6e\x67\x73"), __("\101\124\124\x52\40\123\145\164\164\x69\x6e\x67\163"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function processValuesAndSaveData($Te)
    {
        if (!empty($Te["\x6f\160\x74\x69\x6f\x6e"]) && $Te["\x6f\160\x74\x69\157\x6e"] == "\163\141\x76\145\x50\162\x6f\166\x69\x64\145\162") {
            goto yV;
        }
        $gu = trim($Te["\155\x6f\137\x69\x64\145\156\164\x69\x74\x79\x5f\x70\x72\x6f\166\x69\x64\x65\x72"]);
        $yG = $this->spUtility->getidpApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\x64\x70\137\156\x61\x6d\x65"] === $gu)) {
                goto fX;
            }
            $hR = $ub->getData();
            fX:
            kC:
        }
        v5:
        $cK = array("\146\157\x72\x6d\137\x6b\x65\171", "\155\157\x5f\151\144\x65\x6e\x74\x69\x74\171\x5f\160\162\x6f\x76\x69\x64\x65\162", "\163\141\x6d\x6c\x5f\141\x6d\137\163\x61\155\x65\x61\x73\x62\151\154\154\x69\156\147", "\163\x61\x6d\154\x5f\x61\155\x5f\142\151\154\154\x69\x6e\147\141\156\x64\163\x68\x69\160\x70\151\156\147", "\x73\x61\x6d\154\x5f\141\155\x5f\165\160\144\x61\x74\145\137\141\x74\164\x72\x69\142\165\x74\145", "\164\x68\151\163\137\141\164\164\162\151\x62\165\x74\145", "\x73\x61\155\x6c\x5f\141\155\137\x66\151\162\x73\164\137\x6e\x61\155\145", "\x73\141\155\154\x5f\141\x6d\137\x6c\141\163\164\137\x6e\x61\x6d\x65", "\163\x61\x6d\x6c\x5f\141\155\x5f\x61\x63\143\157\165\x6e\x74\137\x6d\x61\x74\143\x68\x65\162", "\163\x61\155\x6c\137\141\155\137\x75\163\x65\162\x6e\141\155\145", "\x73\141\155\154\x5f\141\x6d\137\x65\x6d\141\151\154", "\x73\141\x6d\x6c\x5f\141\155\137\147\x72\x6f\165\x70\x5f\156\x61\155\145", "\163\141\155\154\x5f\141\155\137\143\x69\x74\171", "\163\x61\155\154\x5f\x61\x6d\137\x61\144\x64\162\145\x73\163", "\60", "\x73\141\155\x6c\137\x61\155\x5f\x63\157\x6d\160\141\x6e\171\x5f\x69\x64", "\163\141\155\x6c\137\141\155\x5f\143\151\164\x79\137\142\x69\x6c\x6c\151\x6e\147", "\x73\x61\x6d\154\x5f\141\155\137\x61\144\144\162\x65\163\x73\x5f\x62\151\x6c\x6c\151\156\x67", "\x73\x61\155\154\x5f\x61\155\x5f\160\x68\x6f\x6e\x65\137\x62\151\154\x6c\151\156\x67", "\163\x61\x6d\x6c\x5f\x61\x6d\137\x73\x74\x61\164\145\137\x62\151\154\x6c\x69\156\x67", "\163\141\155\154\x5f\x61\155\x5f\x7a\x69\x70\x63\157\x64\x65\137\x62\151\154\x6c\x69\x6e\147", "\163\x61\155\154\x5f\x61\x6d\x5f\143\x6f\x75\x6e\x74\x72\171\x5f\142\151\154\154\151\156\147", "\163\x61\x6d\154\x5f\141\x6d\137\143\x69\164\171\x5f\x73\150\151\160\160\x69\156\x67", "\x73\x61\155\154\x5f\141\x6d\137\141\144\144\162\145\x73\163\x5f\163\150\x69\160\x70\x69\x6e\147", "\x73\x61\x6d\154\x5f\141\x6d\x5f\x70\150\x6f\x6e\x65\x5f\163\x68\151\160\x70\x69\156\147", "\163\x61\x6d\x6c\x5f\141\x6d\137\x73\x74\141\x74\145\x5f\163\x68\151\x70\x70\x69\x6e\147", "\x73\141\155\154\137\141\155\x5f\x7a\x69\x70\x63\x6f\144\x65\x5f\x73\x68\x69\160\x70\x69\156\147", "\x73\x61\155\154\137\141\x6d\x5f\x63\157\165\x6e\164\x72\171\137\x73\x68\151\160\160\151\156\147", "\x73\141\155\154\x5f\x61\x6d\x5f\165\x70\x64\x61\164\145\x5f\141\164\x74\x72\x69\142\x75\x65", "\x6f\160\164\x69\157\156", "\163\x61\x6d\154\137\141\155\x5f\143\157\155\160\141\156\171", "\163\141\x6d\x6c\137\141\155\x5f\x74\x61\142\154\x65", "\x6b\x65\171", "\163\165\142\155\x69\164");
        $EV = json_encode($Te, true);
        $Cw = json_decode($EV, true);
        $this->spUtility->log_debug("\104\x65\146\x61\165\154\164\40\x61\156\144\x20\x43\165\x73\x74\157\155\40\101\x74\x74\162\x69\142\x75\x74\x65\163\x20\101\162\162\141\171\72\40", $Cw);
        $C3 = $Cw;
        $this->spUtility->log_debug("\154\145\x74\47\163\x20\x75\156\x73\x65\x74\x20\144\145\x66\x61\165\x6c\164\x20\x61\x74\164\x72", $C3);
        foreach ($cK as $VP) {
            unset($C3[$VP]);
            zd:
        }
        UF:
        $If = json_encode($C3, true);
        $this->spUtility->log_debug("\141\146\164\x65\162\40\x75\156\x73\145\x74\151\156\x67\40\x74\x68\145\x20\166\x61\x6c\x75\x65", $If);
        $this->spUtility->log_debug("\163\141\166\x65\x20\143\165\x73\x74\157\x6d\40\x61\x74\x74\x72\151\142\165\x74\x65\x73");
        $FW = !empty($hR["\151\x64\x70\x5f\145\x6e\x74\x69\164\171\137\151\x64"]) ? $hR["\151\x64\160\x5f\x65\x6e\164\x69\164\x79\137\151\x64"] : '';
        $I_ = !empty($hR["\x73\x61\155\x6c\137\x6c\157\x67\151\156\x5f\x75\x72\x6c"]) ? $hR["\163\141\155\x6c\137\x6c\157\147\x69\156\x5f\165\162\x6c"] : '';
        $CI = !empty($hR["\x73\141\155\x6c\137\154\157\x67\x69\x6e\x5f\142\151\156\144\x69\156\x67"]) ? $hR["\163\x61\x6d\x6c\137\x6c\x6f\147\151\x6e\x5f\142\x69\x6e\144\x69\x6e\x67"] : '';
        $tb = !empty($hR["\x73\x61\x6d\154\x5f\154\157\x67\157\165\x74\x5f\165\x72\x6c"]) ? $hR["\x73\141\x6d\x6c\x5f\x6c\x6f\x67\157\165\164\137\165\162\154"] : '';
        $fF = !empty($hR["\163\141\155\x6c\137\x6c\x6f\147\x6f\x75\x74\137\142\151\x6e\x64\x69\156\147"]) ? $hR["\163\141\155\154\137\154\x6f\x67\x6f\165\x74\x5f\x62\151\x6e\x64\151\156\x67"] : '';
        $zS = !empty($hR["\x78\x35\x30\71\x5f\143\145\162\x74\x69\146\x69\143\x61\x74\x65"]) ? SAML2Utilities::sanitize_certificate($hR["\x78\x35\x30\x39\x5f\x63\x65\162\x74\x69\146\x69\143\141\164\x65"]) : '';
        $LT = !empty($hR["\162\x65\x73\160\x6f\x6e\163\x65\x5f\163\x69\x67\156\x65\x64"]) ? $hR["\x72\145\163\x70\x6f\x6e\163\x65\137\x73\x69\x67\156\145\x64"] : 0;
        $GK = !empty($hR["\x61\163\x73\x65\x72\x74\151\x6f\x6e\x5f\163\x69\x67\x6e\x65\144"]) ? $hR["\x61\x73\x73\x65\162\164\151\157\x6e\x5f\x73\151\147\x6e\145\144"] : 0;
        $y4 = !empty($hR["\x73\150\x6f\167\x5f\141\x64\155\151\156\137\x6c\151\156\x6b"]) && $hR["\163\150\x6f\167\x5f\141\144\155\x69\x6e\137\154\151\156\x6b"] == true ? 1 : 0;
        $t8 = !empty($hR["\x73\x68\157\x77\x5f\143\x75\x73\164\x6f\x6d\145\x72\137\154\151\x6e\153"]) && $hR["\163\x68\157\x77\137\x63\x75\x73\164\157\x6d\145\x72\x5f\x6c\x69\156\153"] == true ? 1 : 0;
        $Ab = !empty($hR["\x61\165\164\157\x5f\x63\162\x65\141\164\145\x5f\x61\144\155\x69\156\x5f\165\x73\145\162\x73"]) && $hR["\141\x75\x74\157\137\143\x72\145\141\164\145\137\x61\144\x6d\151\156\137\165\163\x65\x72\x73"] == true ? 1 : 0;
        $Cx = !empty($hR["\141\x75\x74\157\137\143\x72\x65\141\x74\x65\137\143\165\163\164\x6f\x6d\145\x72\163"]) && $hR["\x61\x75\x74\157\x5f\x63\x72\x65\141\x74\145\137\143\165\x73\164\x6f\155\x65\162\x73"] == true ? 1 : 0;
        $kY = !empty($hR["\x64\x69\x73\141\x62\154\x65\137\142\62\x63"]) && $hR["\144\x69\163\141\x62\154\145\137\142\x32\x63"] == true ? 1 : 0;
        $ni = !empty($hR["\x66\157\x72\x63\x65\137\141\x75\x74\150\x65\156\164\151\143\141\x74\151\157\156\x5f\167\151\x74\x68\x5f\151\144\160"]) && $hR["\x66\157\x72\x63\x65\137\141\x75\x74\150\145\x6e\x74\151\x63\141\164\151\x6f\156\137\x77\151\x74\x68\137\x69\x64\160"] == true ? 1 : 0;
        $j2 = !empty($hR["\141\165\x74\157\x5f\x72\x65\144\x69\162\145\x63\164\137\164\157\x5f\x69\x64\x70"]) && $hR["\x61\165\x74\157\137\162\145\144\x69\x72\145\143\x74\137\x74\x6f\x5f\151\x64\160"] == true ? 1 : 0;
        $zL = !empty($hR["\154\x69\156\x6b\137\x74\x6f\137\151\x6e\x69\x74\x69\x61\164\145\x5f\163\x73\157"]) && $hR["\154\151\x6e\x6b\x5f\164\x6f\137\x69\156\151\164\x69\141\x74\x65\137\x73\x73\157"] == true ? 1 : 0;
        $Ql = !empty($Te["\163\x61\155\x6c\x5f\141\x6d\x5f\x75\x70\144\141\x74\x65\x5f\141\164\164\162\x69\x62\x75\164\x65"]) ? "\x63\x68\145\x63\x6b\x65\144" : "\x75\x6e\x63\x68\145\x63\x6b\x65\144";
        $Hr = !empty($Te["\163\x61\x6d\154\137\141\155\x5f\x61\143\x63\157\x75\x6e\x74\x5f\155\141\x74\x63\x68\x65\x72"]) ? $Te["\x73\x61\x6d\x6c\137\141\155\x5f\141\143\x63\157\165\x6e\164\x5f\155\x61\x74\x63\x68\x65\162"] : '';
        $Qx = !empty($Te["\x73\141\155\x6c\137\x61\155\x5f\145\155\141\151\154"]) ? trim($Te["\163\141\x6d\154\137\x61\155\x5f\x65\155\x61\x69\x6c"]) : '';
        $pf = !empty($Te["\163\x61\155\154\x5f\x61\x6d\x5f\x75\x73\x65\x72\156\141\x6d\145"]) ? trim($Te["\163\141\155\x6c\x5f\141\x6d\x5f\165\163\x65\x72\x6e\x61\x6d\x65"]) : '';
        $Z3 = !empty($Te["\x73\141\155\x6c\x5f\141\155\x5f\x66\x69\x72\x73\164\x5f\x6e\x61\x6d\145"]) ? trim($Te["\163\x61\x6d\154\137\x61\155\x5f\146\x69\x72\163\164\137\156\141\x6d\x65"]) : '';
        $ph = !empty($Te["\163\141\x6d\154\x5f\141\155\137\x6c\141\163\164\x5f\x6e\x61\155\145"]) ? trim($Te["\163\x61\x6d\154\137\141\155\x5f\154\x61\x73\164\x5f\156\141\155\145"]) : '';
        $qS = !empty($Te["\163\x61\155\154\x5f\x61\x6d\137\147\162\157\165\160\x5f\x6e\141\155\145"]) ? trim($Te["\163\x61\x6d\x6c\137\141\x6d\x5f\147\162\x6f\165\x70\x5f\x6e\141\x6d\x65"]) : '';
        $pY = !empty($Te["\163\x61\155\154\137\141\155\x5f\143\x69\164\171\x5f\x62\151\x6c\154\151\156\147"]) ? trim($Te["\x73\x61\x6d\x6c\x5f\141\155\x5f\143\x69\x74\x79\x5f\142\x69\154\x6c\151\156\147"]) : '';
        $mP = !empty($Te["\x73\141\155\x6c\x5f\141\155\x5f\163\x74\x61\164\145\x5f\x62\151\x6c\x6c\x69\156\147"]) ? trim($Te["\x73\x61\155\x6c\x5f\141\155\x5f\163\164\141\x74\145\137\x62\x69\154\154\151\156\x67"]) : '';
        $gr = !empty($Te["\x73\x61\155\154\137\x61\155\137\x63\157\165\x6e\x74\162\x79\137\142\151\154\154\x69\x6e\x67"]) ? trim($Te["\x73\141\155\x6c\137\x61\x6d\x5f\x63\157\x75\156\x74\162\171\x5f\x62\x69\x6c\154\x69\x6e\147"]) : '';
        $lF = !empty($Te["\163\x61\155\154\137\141\x6d\137\x61\144\144\162\145\163\163\137\142\151\x6c\154\x69\x6e\147"]) ? trim($Te["\163\x61\155\154\x5f\x61\155\137\x61\x64\x64\x72\145\x73\x73\x5f\142\x69\154\x6c\151\x6e\x67"]) : '';
        $M8 = !empty($Te["\x73\141\x6d\154\137\x61\x6d\137\160\150\x6f\156\x65\x5f\142\151\x6c\x6c\151\156\147"]) ? trim($Te["\163\141\x6d\154\137\141\155\x5f\x70\150\x6f\156\x65\x5f\142\x69\x6c\x6c\151\156\x67"]) : '';
        $h5 = !empty($Te["\163\141\155\x6c\x5f\141\155\137\x7a\151\160\x63\x6f\x64\x65\x5f\142\151\x6c\154\x69\156\x67"]) ? trim($Te["\x73\x61\x6d\x6c\137\x61\x6d\137\172\x69\x70\143\x6f\x64\145\x5f\142\x69\154\154\151\156\147"]) : '';
        $se = !empty($Te["\163\141\155\154\137\141\155\137\x63\151\x74\x79\137\163\150\151\x70\160\151\156\x67"]) ? trim($Te["\x73\141\x6d\x6c\x5f\x61\155\137\x63\x69\x74\x79\x5f\163\150\x69\160\160\x69\x6e\147"]) : '';
        $zd = !empty($Te["\x73\141\x6d\154\x5f\141\155\x5f\163\164\x61\164\x65\137\x73\x68\x69\x70\x70\151\156\x67"]) ? trim($Te["\163\141\x6d\x6c\137\141\x6d\137\x73\164\x61\164\x65\137\163\x68\x69\x70\x70\x69\x6e\x67"]) : '';
        $il = !empty($Te["\163\141\155\154\137\x61\x6d\137\143\x6f\165\x6e\x74\x72\171\137\163\x68\x69\x70\160\151\x6e\x67"]) ? trim($Te["\x73\141\155\154\x5f\141\x6d\x5f\143\157\165\156\x74\162\171\137\163\x68\151\x70\x70\x69\156\147"]) : '';
        $We = !empty($Te["\x73\141\155\154\x5f\141\155\137\141\144\x64\162\145\163\163\x5f\x73\x68\x69\160\x70\151\156\x67"]) ? trim($Te["\163\141\x6d\x6c\x5f\141\155\137\x61\x64\x64\162\x65\163\163\137\163\150\x69\160\160\x69\156\x67"]) : '';
        $Qn = !empty($Te["\x73\x61\x6d\x6c\137\141\155\x5f\x70\150\157\x6e\x65\x5f\x73\x68\151\160\x70\x69\156\x67"]) ? trim($Te["\x73\x61\x6d\x6c\137\141\x6d\x5f\160\150\157\156\145\137\163\150\151\x70\160\151\x6e\147"]) : '';
        $Pt = !empty($Te["\163\x61\155\x6c\137\141\155\x5f\x7a\151\160\x63\x6f\x64\x65\x5f\x73\x68\151\160\160\x69\156\147"]) ? trim($Te["\x73\141\x6d\x6c\x5f\x61\155\x5f\x7a\151\160\x63\x6f\144\145\137\163\x68\151\160\x70\151\156\x67"]) : '';
        $Dx = !empty($Te["\x73\141\x6d\x6c\x5f\x61\155\x5f\x63\157\155\x70\141\156\x79\x5f\151\x64"]) ? trim($Te["\163\x61\x6d\154\137\x61\x6d\x5f\143\157\155\x70\141\156\x79\x5f\151\144"]) : '';
        $C5 = !empty($Te["\x73\141\x6d\x6c\x5f\141\x6d\137\x74\141\x62\154\x65"]) ? trim($Te["\163\141\x6d\x6c\x5f\x61\155\137\x74\x61\142\x6c\x65"]) : '';
        $V9 = !empty($If) ? $If : '';
        $WD = !empty($hR["\x64\x6f\x5f\156\x6f\x74\137\x61\165\164\157\143\162\145\141\164\x65\137\x69\146\137\162\x6f\154\145\x73\x5f\x6e\157\x74\x5f\x6d\x61\x70\x70\145\x64"]) ? $hR["\144\x6f\x5f\156\x6f\164\137\x61\165\x74\x6f\143\x72\x65\x61\x74\145\137\x69\146\137\162\157\x6c\145\163\x5f\156\x6f\x74\137\x6d\141\160\x70\145\144"] : "\x75\156\x63\150\x65\x63\153\x65\x64";
        $VJ = !empty($hR["\165\160\x64\x61\164\145\x5f\142\x61\x63\x6b\x65\156\x64\x5f\x72\x6f\154\145\163\x5f\157\156\x5f\163\x73\157"]) ? $hR["\165\160\x64\x61\x74\145\x5f\x62\x61\143\x6b\145\x6e\x64\137\162\x6f\154\145\x73\137\x6f\156\137\x73\163\157"] : "\165\156\143\150\145\143\153\145\144";
        $mk = !empty($hR["\x75\x70\x64\x61\x74\x65\x5f\146\162\x6f\x6e\x74\145\156\144\137\147\162\157\x75\160\x73\137\157\x6e\x5f\163\x73\157"]) ? $hR["\x75\160\x64\x61\x74\145\x5f\146\162\x6f\156\x74\x65\156\144\137\147\x72\157\x75\160\163\x5f\x6f\x6e\137\163\163\x6f"] : "\165\x6e\x63\150\145\143\x6b\x65\144";
        $YO = !empty($hR["\x64\x65\x66\141\x75\154\x74\137\147\162\157\x75\160"]) ? $hR["\144\145\146\141\x75\154\164\x5f\x67\x72\x6f\165\160"] : '';
        $Le = !empty($hR["\x64\x65\x66\x61\165\154\164\137\162\157\x6c\145"]) ? $hR["\x64\145\x66\141\165\x6c\164\x5f\162\x6f\x6c\x65"] : '';
        $Qu = !empty($hR["\x67\162\157\165\x70\163\x5f\155\141\x70\x70\x65\x64"]) ? $hR["\x67\x72\x6f\x75\x70\163\x5f\155\x61\x70\x70\145\144"] : '';
        $by = !empty($hR["\162\157\x6c\x65\163\137\x6d\x61\160\x70\x65\144"]) ? $hR["\162\157\x6c\x65\x73\137\x6d\x61\x70\160\145\144"] : '';
        $bw = !empty($hR["\x73\x61\155\x6c\x5f\154\157\x67\157\165\164\x5f\162\145\x64\151\x72\x65\x63\x74\137\165\x72\154"]) ? $hR["\163\x61\155\x6c\137\154\x6f\x67\157\x75\x74\137\162\x65\144\151\x72\145\x63\x74\137\165\162\154"] : '';
        $Yx = !empty($Te["\163\141\155\x6c\x5f\141\155\137\142\151\154\x6c\x69\x6e\147\141\x6e\x64\163\150\x69\x70\x70\151\156\147"]) ? trim($Te["\x73\x61\155\x6c\137\x61\155\137\x62\x69\154\x6c\151\156\147\x61\156\x64\163\x68\151\x70\160\x69\x6e\147"]) : "\156\157\156\145";
        $lN = !empty($Te["\x73\x61\155\154\x5f\x61\x6d\x5f\163\141\155\145\x61\x73\x62\x69\x6c\x6c\151\156\x67"]) ? trim($Te["\163\141\155\x6c\137\x61\155\x5f\163\x61\x6d\145\141\x73\142\x69\x6c\154\151\x6e\x67"]) : "\156\157\156\145";
        $GE = !empty($hR["\x6d\x6f\x5f\x73\x61\x6d\x6c\x5f\x68\145\141\x64\154\145\x73\163\137\x73\163\x6f"]) && $hR["\155\157\x5f\x73\x61\x6d\154\137\150\145\141\144\154\x65\163\163\137\x73\x73\x6f"] == true ? 1 : 0;
        $Q7 = !empty($hR["\x6d\x6f\137\163\x61\x6d\154\x5f\146\x72\x6f\x6e\164\x65\156\x64\137\160\x6f\x73\x74\137\165\x72\154"]) ? $hR["\x6d\157\x5f\x73\141\155\154\x5f\146\x72\157\156\164\x65\156\144\137\160\x6f\x73\164\x5f\165\162\x6c"] : '';
        if (is_null($hR)) {
            goto YM;
        }
        $this->spUtility->deleteIDPApps((int) $hR["\x69\144"]);
        YM:
        $this->spUtility->setIDPApps($gu, $FW, $I_, $CI, $tb, $fF, $zS, $LT, $GK, $y4, $t8, $Ab, $Cx, $kY, $ni, $j2, $zL, $Ql, $Hr, $Qx, $pf, $Z3, $ph, $qS, $pY, $mP, $gr, $lF, $M8, $h5, $se, $zd, $il, $We, $Qn, $Pt, $Dx, $C5, $V9, $WD, $VJ, $mk, $YO, $Le, $Qu, $by, $bw, $Yx, $lN, $GE, $Q7);
        goto Js;
        yV:
        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $Te["\x6d\157\x5f\151\144\x65\x6e\x74\151\164\x79\x5f\x70\x72\x6f\166\151\144\x65\x72"]);
        Js:
    }
    public function remove($MA)
    {
        $Zj = $this->customerSetupFactory->create(["\x73\145\164\165\160" => $this->moduleDataSetup]);
        $Zj->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, $MA);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::MODULE_ATTR);
    }
}
