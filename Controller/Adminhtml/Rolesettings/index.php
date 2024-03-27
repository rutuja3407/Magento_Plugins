<?php


namespace MiniOrange\SP\Controller\Adminhtml\Rolesettings;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
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
    private $adminRoleModel;
    private $userGroupModel;
    private $attributeModel;
    private $samlResponse;
    private $params;
    private $adminUserModel;
    public function __construct(Context $gt, PageFactory $Jq, SPUtility $fR, ManagerInterface $b_, LoggerInterface $kU, \Magento\Authorization\Model\ResourceModel\Role\Collection $sP, \Magento\Customer\Model\ResourceModel\Attribute\Collection $dx, \Magento\Customer\Model\ResourceModel\Group\Collection $i6, Sp $ou)
    {
        parent::__construct($gt, $Jq, $fR, $b_, $kU, $ou);
        $this->adminRoleModel = $sP;
        $this->userGroupModel = $i6;
        $this->attributeModel = $dx;
        $this->messageManager = $b_;
        $this->logger = $kU;
        $this->sp = $ou;
    }
    public function execute()
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto A6;
        }
        $Az = $this->spUtility->getStoreConfig(SPConstants::SEND_EMAIL);
        if (!($Az == NULL)) {
            goto SE;
        }
        $fU = $this->spUtility->getCurrentAdminUser()->getData();
        $RH = $this->spUtility->getMagnetoVersion();
        $ii = $fU["\x65\155\x61\x69\x6c"];
        $FO = $fU["\146\151\x72\163\164\x6e\x61\155\x65"];
        $Fo = $fU["\x6c\141\163\x74\x6e\x61\155\145"];
        $kz = $this->spUtility->getBaseUrl();
        $jT = array($FO, $Fo, $RH, $kz);
        $this->spUtility->setStoreConfig(SPConstants::SEND_EMAIL, 1);
        Curl::submit_to_magento_team($ii, "\111\x6e\x73\164\x61\x6c\x6c\x65\x64\x20\123\165\143\143\x65\163\x73\146\165\x6c\x6c\171\x2d\x41\x63\143\157\x75\x6e\x74\x20\x54\x61\142", $jT);
        $this->spUtility->flushCache();
        SE:
        A6:
        try {
            $Te = $this->getRequest()->getParams();
            $this->checkIfValidPlugin();
            if (!$this->isFormOptionBeingSaved($Te)) {
                goto yA;
            }
            $this->processValuesAndSaveData($Te);
            $this->spUtility->flushCache();
            $this->messageManager->addSuccessMessage(SPMessages::SETTINGS_SAVED);
            $this->spUtility->reinitConfig();
            yA:
        } catch (\Exception $IR) {
            $this->messageManager->addErrorMessage($IR->getMessage());
            $this->spUtility->log_debug($IR->getMessage());
        }
        $Vy = $this->resultPageFactory->create();
        $Vy->setActiveMenu(SPConstants::MODULE_DIR . SPConstants::MODULE_BASE);
        $Vy->addBreadcrumb(__("\122\117\114\x45\x20\x53\x65\164\164\x69\156\x67\x73"), __("\x52\x4f\114\105\40\x53\145\x74\164\151\x6e\147\x73"));
        $Vy->getConfig()->getTitle()->prepend(__(SPConstants::MODULE_TITLE));
        return $Vy;
    }
    private function processValuesAndSaveData($Te)
    {
        if (!empty($Te["\x6f\x70\164\x69\157\156"]) && $Te["\x6f\x70\x74\151\157\x6e"] == "\x73\x61\x76\x65\x50\162\157\166\x69\x64\x65\x72") {
            goto L_;
        }
        $gu = trim($Te["\155\157\x5f\151\144\145\156\164\x69\164\171\x5f\160\162\157\x76\x69\144\145\162"]);
        $yG = $this->spUtility->getidpApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\144\160\x5f\x6e\x61\155\145"] === $gu)) {
                goto fO;
            }
            $hR = $ub->getData();
            fO:
            pG:
        }
        Bv:
        $FW = !empty($hR["\151\x64\x70\x5f\145\156\x74\151\164\171\x5f\151\144"]) ? $hR["\151\144\160\137\145\x6e\x74\x69\164\171\x5f\x69\144"] : '';
        $I_ = !empty($hR["\163\x61\x6d\154\137\x6c\157\x67\151\156\x5f\165\x72\154"]) ? $hR["\163\x61\x6d\154\137\x6c\157\147\151\x6e\137\165\162\154"] : '';
        $CI = !empty($hR["\x73\141\x6d\x6c\137\x6c\157\x67\151\x6e\137\142\151\x6e\x64\x69\x6e\x67"]) ? $hR["\x73\141\155\154\x5f\x6c\x6f\147\151\x6e\137\142\x69\156\144\151\156\147"] : '';
        $tb = !empty($hR["\163\141\x6d\x6c\x5f\x6c\157\x67\157\165\x74\x5f\165\x72\154"]) ? $hR["\163\141\x6d\x6c\x5f\154\157\147\x6f\165\x74\137\165\162\154"] : '';
        $fF = !empty($hR["\x73\141\x6d\154\137\x6c\157\147\157\x75\164\137\x62\x69\x6e\144\151\x6e\147"]) ? $hR["\x73\x61\x6d\x6c\x5f\154\157\147\157\165\x74\137\142\x69\156\144\151\x6e\x67"] : '';
        $zS = !empty($hR["\x78\x35\60\x39\x5f\143\x65\x72\164\x69\x66\151\x63\141\x74\x65"]) ? SAML2Utilities::sanitize_certificate($hR["\170\x35\x30\71\137\143\x65\162\164\x69\146\x69\x63\141\x74\x65"]) : '';
        $LT = !empty($hR["\162\145\163\160\x6f\156\163\x65\x5f\163\151\147\x6e\x65\x64"]) ? $hR["\x72\145\163\x70\x6f\x6e\x73\x65\x5f\x73\151\x67\x6e\145\144"] : 0;
        $GK = !empty($hR["\141\163\163\x65\162\x74\x69\157\x6e\137\x73\x69\147\x6e\145\144"]) ? $hR["\x61\163\x73\x65\162\164\x69\157\156\137\x73\151\147\156\145\144"] : 0;
        $y4 = !empty($hR["\x73\x68\157\167\x5f\x61\144\x6d\x69\x6e\137\154\x69\x6e\x6b"]) && $hR["\x73\x68\x6f\x77\137\141\144\x6d\151\156\x5f\154\x69\156\153"] == true ? 1 : 0;
        $t8 = !empty($hR["\x73\150\x6f\x77\x5f\x63\x75\163\164\157\155\x65\x72\137\154\x69\x6e\153"]) && $hR["\163\150\x6f\167\x5f\x63\165\163\164\157\x6d\145\162\137\x6c\x69\x6e\x6b"] == true ? 1 : 0;
        $Ab = !empty($hR["\141\x75\164\157\137\x63\162\145\141\x74\x65\137\x61\x64\x6d\151\x6e\137\165\x73\145\x72\163"]) && $hR["\x61\x75\164\x6f\x5f\143\x72\145\x61\x74\x65\x5f\141\144\x6d\x69\156\x5f\x75\x73\145\x72\x73"] == true ? 1 : 0;
        $Cx = !empty($hR["\141\165\x74\x6f\137\x63\x72\145\141\x74\145\137\143\x75\163\164\x6f\x6d\x65\x72\163"]) && $hR["\x61\165\164\157\137\143\x72\x65\x61\x74\x65\137\x63\165\163\164\157\x6d\x65\x72\x73"] == true ? 1 : 0;
        $kY = !empty($hR["\144\151\x73\141\x62\154\145\137\142\x32\143"]) && $hR["\144\x69\163\141\x62\x6c\145\137\142\62\143"] == true ? 1 : 0;
        $ni = !empty($hR["\x66\x6f\162\x63\145\137\x61\x75\x74\x68\x65\x6e\x74\151\x63\141\x74\151\157\156\x5f\167\151\164\150\137\151\x64\160"]) && $hR["\146\157\162\143\145\x5f\x61\165\164\x68\145\156\164\151\x63\141\x74\151\157\156\x5f\167\151\164\x68\137\151\144\x70"] == true ? 1 : 0;
        $j2 = !empty($hR["\141\x75\164\157\137\162\x65\144\x69\x72\145\143\x74\x5f\x74\x6f\137\x69\x64\x70"]) && $hR["\141\165\164\x6f\137\x72\145\144\x69\x72\145\143\164\137\x74\157\137\x69\144\160"] == true ? 1 : 0;
        $zL = !empty($hR["\x6c\151\x6e\153\137\164\157\137\x69\x6e\x69\x74\x69\141\x74\x65\x5f\163\x73\x6f"]) && $hR["\154\x69\156\153\x5f\x74\x6f\x5f\151\156\151\x74\x69\x61\x74\145\x5f\x73\163\x6f"] == true ? 1 : 0;
        $Ql = !empty($hR["\165\160\x64\x61\164\145\137\x61\x74\164\162\151\x62\x75\x74\145\163\x5f\157\x6e\x5f\x6c\x6f\x67\x69\156"]) ? $hR["\165\x70\x64\x61\164\145\x5f\x61\164\x74\162\151\x62\165\164\145\x73\137\x6f\x6e\137\154\157\147\x69\x6e"] : "\165\156\143\150\x65\143\x6b\x65\x64";
        $Hr = !empty($hR["\143\x72\145\x61\x74\145\x5f\x6d\141\147\x65\x6e\x74\x6f\x5f\141\143\x63\157\x75\156\164\137\x62\171"]) ? $hR["\x63\162\x65\x61\x74\x65\137\x6d\x61\x67\145\156\164\x6f\x5f\141\143\143\157\165\156\x74\x5f\142\x79"] : '';
        $Qx = !empty($hR["\145\155\x61\151\154\137\141\164\x74\x72\x69\142\x75\x74\x65"]) ? $hR["\145\x6d\141\x69\154\137\141\164\x74\x72\x69\142\x75\164\x65"] : '';
        $pf = !empty($hR["\165\163\x65\162\x6e\x61\x6d\x65\137\x61\x74\x74\x72\151\x62\165\164\145"]) ? $hR["\x75\163\145\x72\x6e\x61\155\x65\137\141\x74\x74\162\151\x62\x75\x74\145"] : '';
        $Z3 = !empty($hR["\146\151\162\163\164\156\x61\x6d\x65\x5f\141\164\164\162\151\x62\x75\x74\x65"]) ? $hR["\146\151\x72\163\x74\x6e\x61\155\x65\x5f\141\x74\x74\162\x69\x62\165\164\145"] : '';
        $ph = !empty($hR["\x6c\x61\163\x74\156\141\155\x65\x5f\x61\x74\x74\x72\x69\x62\x75\164\x65"]) ? $hR["\154\x61\x73\164\156\141\155\145\137\x61\164\164\x72\151\x62\x75\x74\145"] : '';
        $qS = !empty($hR["\x67\x72\x6f\x75\160\x5f\141\164\164\x72\151\142\165\164\x65"]) ? $hR["\147\162\157\x75\x70\137\x61\x74\x74\162\151\x62\165\x74\145"] : '';
        $pY = !empty($hR["\142\151\154\x6c\x69\x6e\147\x5f\x63\x69\164\x79\137\x61\164\164\162\x69\x62\165\x74\145"]) ? $hR["\x62\x69\154\154\151\x6e\x67\137\x63\151\164\x79\137\141\x74\164\x72\151\x62\x75\164\145"] : '';
        $mP = !empty($hR["\142\151\154\x6c\x69\156\x67\x5f\163\x74\x61\x74\x65\x5f\x61\164\x74\162\151\x62\x75\164\x65"]) ? $hR["\x62\x69\x6c\154\x69\x6e\x67\x5f\163\164\141\164\145\137\x61\164\164\162\x69\x62\x75\164\145"] : '';
        $gr = !empty($hR["\142\151\154\x6c\151\x6e\147\x5f\x63\157\x75\156\164\162\171\x5f\x61\x74\x74\162\x69\142\165\x74\145"]) ? $hR["\x62\x69\154\154\151\x6e\147\137\x63\x6f\165\156\164\162\171\137\141\x74\164\162\x69\x62\165\x74\145"] : '';
        $lF = !empty($hR["\142\x69\154\154\x69\x6e\147\137\141\144\x64\162\x65\163\163\137\x61\x74\x74\x72\x69\142\165\x74\x65"]) ? $hR["\142\x69\x6c\x6c\151\156\x67\137\x61\x64\x64\x72\x65\163\163\137\x61\164\164\162\151\142\x75\164\x65"] : '';
        $M8 = !empty($hR["\142\x69\154\154\x69\156\147\x5f\160\x68\157\156\x65\137\141\x74\164\162\151\x62\165\x74\x65"]) ? $hR["\x62\x69\x6c\x6c\x69\156\x67\x5f\160\x68\x6f\156\x65\137\141\164\x74\x72\151\x62\165\x74\145"] : '';
        $h5 = !empty($hR["\x62\151\x6c\154\x69\x6e\x67\137\x7a\151\160\137\x61\x74\x74\x72\x69\x62\165\x74\145"]) ? $hR["\x62\151\x6c\154\x69\x6e\x67\137\172\151\160\x5f\x61\164\164\x72\151\x62\x75\x74\x65"] : '';
        $se = !empty($hR["\x73\150\x69\x70\160\x69\x6e\x67\x5f\x63\151\x74\171\137\141\x74\x74\162\x69\142\x75\x74\145"]) ? $hR["\x73\150\151\x70\160\x69\x6e\x67\x5f\x63\x69\x74\171\137\x61\x74\164\x72\151\142\165\x74\x65"] : '';
        $zd = !empty($hR["\163\x68\151\x70\160\151\x6e\147\137\x73\x74\141\x74\x65\x5f\x61\x74\164\162\151\x62\x75\164\x65"]) ? $hR["\x73\150\151\160\160\151\x6e\x67\137\163\x74\x61\164\x65\x5f\x61\164\164\162\151\142\x75\164\145"] : '';
        $il = !empty($hR["\163\150\x69\x70\x70\151\x6e\147\x5f\x63\157\x75\156\x74\162\x79\x5f\x61\164\x74\x72\x69\x62\165\x74\145"]) ? $hR["\x73\150\x69\160\160\151\156\x67\137\x63\x6f\x75\156\164\162\171\x5f\141\x74\x74\162\151\x62\x75\164\x65"] : '';
        $We = !empty($hR["\163\x68\151\x70\x70\151\x6e\147\x5f\141\144\x64\x72\x65\163\x73\x5f\x61\164\164\162\151\x62\x75\164\145"]) ? $hR["\163\150\x69\160\160\151\x6e\x67\x5f\x61\144\x64\162\x65\163\163\x5f\x61\x74\x74\162\x69\x62\165\164\145"] : '';
        $Qn = !empty($hR["\163\x68\x69\160\160\x69\156\147\x5f\160\150\x6f\x6e\145\137\141\x74\164\x72\x69\142\165\164\145"]) ? $hR["\163\150\151\160\x70\x69\156\x67\x5f\160\x68\157\x6e\x65\x5f\141\x74\x74\162\x69\142\x75\164\x65"] : '';
        $Pt = !empty($hR["\x73\150\x69\160\160\x69\x6e\x67\x5f\172\x69\160\137\141\164\164\x72\x69\x62\x75\164\145"]) ? $hR["\x73\150\x69\x70\160\x69\x6e\x67\137\172\x69\x70\137\x61\164\164\162\x69\x62\x75\x74\x65"] : '';
        $Dx = !empty($hR["\x62\62\x62\x5f\x61\164\164\162\x69\142\165\164\x65"]) ? $hR["\x62\x32\142\137\x61\164\x74\x72\x69\142\x75\164\145"] : '';
        $C5 = !empty($hR["\x63\165\163\164\x6f\x6d\x5f\x74\x61\142\x6c\x65\x6e\141\155\x65"]) ? $hR["\143\x75\163\164\157\155\x5f\x74\x61\142\x6c\145\x6e\141\x6d\x65"] : '';
        $V9 = !empty($hR["\143\x75\x73\164\157\x6d\x5f\141\x74\164\x72\x69\x62\165\164\x65\163"]) ? $hR["\x63\x75\163\x74\x6f\x6d\137\141\x74\x74\162\x69\142\x75\x74\145\163"] : '';
        $WD = !empty($Te["\155\157\x5f\x73\141\155\154\x5f\144\x6f\x6e\164\137\143\x72\145\141\164\x65\137\165\163\x65\x72\137\151\x66\x5f\x72\157\154\x65\137\156\157\x74\x5f\155\141\160\160\145\x64"]) ? "\143\150\145\x63\153\145\144" : "\165\156\143\150\145\143\x6b\145\x64";
        $VJ = !empty($Te["\x73\x61\155\154\x5f\141\155\137\165\x70\144\x61\164\145\x5f\x72\x6f\x6c\x65\163"]) ? "\143\x68\145\143\x6b\x65\144" : "\165\x6e\x63\150\x65\143\x6b\x65\144";
        $mk = !empty($Te["\163\x61\x6d\154\137\141\x6d\x5f\165\160\144\141\164\x65\137\146\162\x6f\156\164\145\x6e\144\137\x72\x6f\x6c\145\163"]) ? "\x63\x68\145\x63\153\x65\144" : "\x75\x6e\x63\150\x65\x63\x6b\x65\144";
        $YO = !empty($Te["\163\x61\x6d\x6c\x5f\x61\155\x5f\144\x65\x66\x61\165\154\164\x5f\147\162\x6f\165\x70"]) ? trim($Te["\163\141\x6d\154\137\x61\155\x5f\x64\145\x66\141\x75\x6c\x74\137\147\x72\157\x75\160"]) : '';
        $Le = !empty($Te["\x73\x61\155\x6c\137\x61\155\137\x64\145\x66\141\x75\x6c\x74\x5f\x72\x6f\154\145"]) ? trim($Te["\x73\x61\155\154\x5f\x61\x6d\137\x64\145\x66\x61\x75\154\x74\137\162\x6f\154\x65"]) : '';
        $Qu = json_encode($this->processCustomerRoleMapping($Te));
        $by = json_encode($this->processAdminRoleMapping($Te));
        $bw = !empty($hR["\163\x61\x6d\x6c\137\154\157\147\x6f\x75\164\x5f\x72\145\x64\151\162\145\x63\164\137\x75\x72\x6c"]) ? $hR["\x73\141\x6d\154\137\154\157\147\157\x75\164\x5f\162\145\144\151\162\145\143\x74\137\x75\162\154"] : '';
        $Yx = !empty($hR["\x73\x61\x6d\x6c\137\145\x6e\141\x62\x6c\x65\x5f\142\151\x6c\154\x69\156\x67\141\x6e\x64\163\150\151\x70\160\x69\156\x67"]) ? $hR["\163\x61\155\x6c\137\145\156\141\x62\154\145\137\142\x69\154\154\x69\x6e\x67\141\156\x64\163\x68\151\x70\x70\x69\x6e\x67"] : "\x6e\157\x6e\x65";
        $lN = !empty($hR["\x73\x61\x6d\x6c\x5f\x73\x61\155\x65\141\x73\x62\151\154\x6c\x69\156\147"]) ? $hR["\x73\x61\155\x6c\137\x73\141\x6d\x65\141\x73\142\x69\x6c\x6c\151\x6e\147"] : "\x6e\157\x6e\x65";
        $GE = !empty($hR["\x6d\x6f\x5f\x73\x61\x6d\x6c\137\150\x65\x61\x64\x6c\x65\x73\x73\x5f\x73\x73\x6f"]) && $hR["\155\157\137\x73\141\155\x6c\137\150\145\141\x64\154\145\x73\163\x5f\163\x73\x6f"] == true ? 1 : 0;
        $Q7 = !empty($hR["\x6d\x6f\x5f\x73\141\155\154\137\146\162\157\156\x74\x65\156\x64\137\160\157\x73\164\x5f\x75\162\x6c"]) ? $hR["\x6d\157\x5f\x73\141\x6d\154\137\146\x72\157\x6e\164\x65\156\144\x5f\x70\x6f\163\164\x5f\165\162\154"] : '';
        if (is_null($hR)) {
            goto ch;
        }
        $this->spUtility->deleteIDPApps((int) $hR["\x69\144"]);
        ch:
        $this->spUtility->setIDPApps($gu, $FW, $I_, $CI, $tb, $fF, $zS, $LT, $GK, $y4, $t8, $Ab, $Cx, $kY, $ni, $j2, $zL, $Ql, $Hr, $Qx, $pf, $Z3, $ph, $qS, $pY, $mP, $gr, $lF, $M8, $h5, $se, $zd, $il, $We, $Qn, $Pt, $Dx, $C5, $V9, $WD, $VJ, $mk, $YO, $Le, $Qu, $by, $bw, $Yx, $lN, $GE, $Q7);
        $kw = trim($Te["\163\x61\x6d\x6c\x5f\141\x6d\x5f\x64\145\x66\141\x75\154\x74\x5f\x72\157\154\x65"]);
        $yy = trim($Te["\163\x61\x6d\154\x5f\141\x6d\x5f\x64\145\x66\x61\x75\154\164\137\147\x72\x6f\x75\160"]);
        $sZ = !empty($Te["\x73\141\155\x6c\x5f\x61\x6d\137\x64\157\x6e\164\x5f\x61\x6c\154\157\x77\x5f\165\x6e\x6c\x69\163\164\x65\x64\137\x75\163\145\162\137\x72\x6f\x6c\x65"]) ? "\143\x68\x65\143\153\x65\x64" : "\165\156\x43\x68\145\x63\153\145\144";
        $Mz = !empty($Te["\155\157\x5f\x73\141\x6d\x6c\x5f\144\157\x6e\164\137\143\162\145\x61\164\145\137\x75\x73\x65\162\137\x69\146\137\x72\157\x6c\145\x5f\156\157\x74\137\155\x61\x70\160\145\144"]) ? "\143\x68\145\x63\x6b\145\144" : "\x75\156\143\150\145\x63\x6b\145\144";
        $AA = $this->processAdminRoleMapping($Te);
        $W9 = $this->processCustomerRoleMapping($Te);
        $bE = !empty($Te["\163\x61\155\x6c\137\x61\155\137\x75\x70\144\141\164\x65\137\162\x6f\154\145\x73"]) ? "\x63\150\145\x63\153\145\x64" : "\x75\x6e\x63\x68\x65\x63\153\145\144";
        $Gw = !empty($Te["\163\x61\x6d\154\x5f\x61\x6d\137\x75\160\144\x61\x74\x65\137\146\x72\x6f\x6e\164\145\156\144\137\162\157\154\145\x73"]) ? "\x63\150\145\x63\x6b\x65\x64" : "\x75\x6e\x63\150\145\x63\153\145\144";
        goto Nk;
        L_:
        $this->spUtility->setStoreConfig(SPConstants::DEFAULT_PROVIDER, $Te["\155\157\x5f\x69\144\x65\156\164\151\x74\171\137\x70\x72\x6f\166\151\144\x65\x72"]);
        Nk:
    }
    private function processCustomerRoleMapping($Te)
    {
        $W9 = array();
        $DE = $this->userGroupModel->toOptionArray();
        foreach ($DE as $tk) {
            $QX = "\x73\141\x6d\154\x5f\x61\x6d\x5f\147\x72\157\x75\x70\x5f\x61\164\164\x72\137\166\x61\x6c\165\145\163\x5f" . $tk["\166\141\x6c\165\145"];
            if (empty($Te[$QX])) {
                goto wu;
            }
            $W9[$tk["\x76\x61\154\x75\x65"]] = $Te[$QX];
            wu:
            xl:
        }
        uy:
        return $W9;
    }
    private function processAdminRoleMapping($Te)
    {
        $AA = array();
        $CP = $this->adminRoleModel->toOptionArray();
        foreach ($CP as $An) {
            $QX = "\163\141\155\x6c\x5f\141\155\x5f\x61\x64\155\151\x6e\x5f\x61\164\x74\162\x5f\x76\141\154\165\x65\x73\137" . $An["\x76\141\154\165\145"];
            if (empty($Te[$QX])) {
                goto hi;
            }
            $AA[$An["\166\141\x6c\x75\145"]] = $Te[$QX];
            hi:
            Fp:
        }
        SN:
        return $AA;
    }
}
