<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MiniOrange\SP\Helper\Exception\MissingAttributesException;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use Magento\Framework\App\Http;
use Magento\Framework\App\Http\Interceptor;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\AddressFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Adminhtml\Attrsettings\Index;
use Magento\User\Model\User;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResponseFactory;
use MiniOrange\SP\Controller\Actions\CustomerLoginAction;
use Magento\Customer\Model\CustomerFactory;
use Magento\User\Model\UserFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Backend\Helper\Data;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use Magento\Framework\UrlInterface;
class ProcessUserAction extends BaseAction
{
    protected $messageManager;
    private $attrs;
    private $relayState;
    private $sessionIndex;
    private $emailAttribute;
    private $usernameAttribute;
    private $firstNameKey;
    private $lastNameKey;
    private $billingcountryNameKey;
    private $billingcityNameKey;
    private $billingphoneNameKey;
    private $billingstreetAddressNameKey;
    private $billingmapStateNameKey;
    private $billingzipCodeNameKey;
    private $shippingcountryNameKey;
    private $shippingcityNameKey;
    private $shippingphoneNameKey;
    private $shippingstreetAddressNameKey;
    private $shippingmapStateNameKey;
    private $shippingzipCodeNameKey;
    private $defaultRole;
    private $custom_tablename;
    private $custom_attributes;
    private $defaultGroup;
    private $checkIfMatchBy;
    private $groupNameKey;
    private $userGroupModel;
    private $adminRoleModel;
    private $adminUserModel;
    private $firstName;
    private $lastName;
    private $groupName;
    protected $storeManager;
    private $customerRepository;
    private $customerLoginAction;
    protected $responseFactory;
    private $customerFactory;
    private $customerModel;
    private $userFactory;
    private $randomUtility;
    private $adminConfig;
    private $dontAllowUnlistedUserRole;
    private $dontCreateUserIfRoleNotMapped;
    private $_state;
    private $_configLoader;
    private $b2bUser;
    private $accountId;
    protected $companyIdKey;
    private $index;
    private $countryName;
    private $_addressFactory;
    private $collectionFactory;
    private $updateRole;
    private $group_mapping;
    private $role_mapping;
    private $updateFrontendRole;
    private $updateAttribute;
    private $dataAddressFactory;
    private $autoCreateAdminUser;
    private $autoCreateCustomer;
    protected $eavConfig;
    protected $auto_redirect;
    protected $urlBuilder;
    private $HelperBackend;
    public function __construct(ManagerInterface $c0, Context $Gc, SPUtility $Kx, Index $D2, \Magento\Customer\Model\ResourceModel\Group\Collection $o1, \Magento\Authorization\Model\ResourceModel\Role\Collection $bY, User $yg, Customer $u6, CustomerRepositoryInterface $kS, StoreManagerInterface $Wl, ResponseFactory $XF, CustomerLoginAction $iL, CustomerFactory $j8, UserFactory $t2, Random $NG, State $hS, ConfigLoaderInterface $sE, Data $sW, AddressFactory $Zi, AddressFactory $PJ, ResultFactory $UZ, CollectionFactory $cY, Config $EL, UrlInterface $yT)
    {
        $this->customerModel = $u6;
        $this->index = $D2;
        $this->messageManager = $c0;
        $this->userGroupModel = $o1;
        $this->adminRoleModel = $bY;
        $this->adminUserModel = $yg;
        $this->customerRepository = $kS;
        $this->storeManager = $Wl;
        $this->responseFactory = $XF;
        $this->customerLoginAction = $iL;
        $this->customerFactory = $j8;
        $this->userFactory = $t2;
        $this->randomUtility = $NG;
        $this->_state = $hS;
        $this->HelperBackend = $sW;
        $this->_configLoader = $sE;
        $this->dataAddressFactory = $Zi;
        $this->_addressFactory = $PJ;
        $this->collectionFactory = $cY;
        $this->eavConfig = $EL;
        $this->urlBuilder = $yT;
        $this->b2bUser = false;
        parent::__construct($Gc, $Kx, $Wl, $UZ, $XF);
    }
    public function execute()
    {
        $this->spUtility->log_debug("\x20\151\156\x73\x69\x64\145\40\x63\x6c\141\163\x73\x20\x50\x72\x6f\143\145\163\163\125\163\x65\x72\101\x63\x74\151\157\156\x20\x3a\40\145\x78\145\143\165\x74\145\x3a\x20");
        if (!empty($this->attrs)) {
            goto Py;
        }
        throw new MissingAttributesException();
        Py:
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\160\162\x6f\x63\x65\x73\x73\125\163\x65\162\101\143\164\151\x6f\156", $rQ);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\x64\x70\137\156\x61\155\145"] === $rQ)) {
                goto Gm;
            }
            $ft = $fR->getData();
            Gm:
            fw:
        }
        g7:
        $this->emailAttribute = $ft["\145\x6d\x61\x69\154\137\141\164\164\162\151\142\165\x74\145"];
        $this->spUtility->log_debug("\x65\x6d\141\151\x6c\x20\141\164\x74\162\x69\x62\x75\x74\x65", $this->emailAttribute);
        $this->usernameAttribute = $ft["\165\163\145\x72\156\141\155\x65\x5f\141\164\164\162\x69\x62\x75\164\x65"];
        $this->firstNameKey = $ft["\x66\151\x72\x73\164\156\x61\155\x65\x5f\141\x74\164\162\x69\142\165\164\x65"];
        $this->lastNameKey = $ft["\154\x61\163\164\x6e\x61\155\x65\x5f\141\x74\164\162\151\142\x75\x74\145"];
        $this->groupNameKey = $ft["\147\162\157\165\x70\137\x61\164\x74\162\151\x62\165\x74\145"];
        $this->companyIdKey = $ft["\142\62\x62\137\x61\x74\164\x72\151\x62\x75\164\145"];
        $this->checkIfMatchBy = $ft["\143\162\145\x61\x74\x65\x5f\155\141\147\145\x6e\164\157\x5f\x61\143\143\x6f\165\156\x74\137\142\171"];
        $this->dontCreateUserIfRoleNotMapped = $ft["\x64\x6f\x5f\156\x6f\164\x5f\x61\x75\164\157\143\x72\145\141\x74\x65\137\151\146\x5f\162\x6f\154\x65\x73\x5f\156\x6f\164\x5f\155\141\160\x70\145\x64"];
        $this->spUtility->log_debug("\144\157\137\x6e\x6f\x74\x5f\x61\x75\x74\157\x63\x72\x65\x61\x74\145\x5f\x69\146\x5f\162\157\154\x65\163\x5f\x6e\157\x74\x5f\155\x61\160\160\x65\x64\x3a\40", $this->dontCreateUserIfRoleNotMapped);
        $this->billingcountryNameKey = $ft["\142\151\x6c\x6c\151\156\147\137\143\157\x75\156\164\x72\171\137\x61\x74\164\162\x69\x62\x75\x74\145"];
        $this->billingcountryNameKey = $this->spUtility->isBlank($this->billingcountryNameKey) ? SPConstants::MAP_COUNTRY_BILLING : $this->billingcountryNameKey;
        $this->spUtility->log_debug("\x62\x69\154\154\x69\x6e\147\143\x6f\165\156\x74\162\171\116\141\155\145\x4b\145\x79\x3a\40");
        $this->billingcityNameKey = $ft["\x62\x69\154\154\151\156\x67\x5f\143\x69\164\x79\x5f\x61\164\164\x72\151\x62\165\x74\x65"];
        $this->billingcityNameKey = $this->spUtility->isBlank($this->billingcityNameKey) ? SPConstants::MAP_CITY_BILLING : $this->billingcityNameKey;
        $this->spUtility->log_debug("\x62\151\x6c\x6c\x69\x6e\147\x63\x69\x74\171\x4e\x61\155\145\x4b\145\171\72\x20");
        $this->billingphoneNameKey = $ft["\142\151\x6c\x6c\x69\x6e\x67\x5f\160\150\x6f\x6e\x65\137\x61\164\x74\x72\x69\142\165\164\145"];
        $this->billingphoneNameKey = $this->spUtility->isBlank($this->billingphoneNameKey) ? SPConstants::MAP_PHONE_BILLING : $this->billingphoneNameKey;
        $this->spUtility->log_debug("\x62\151\x6c\x6c\x69\x6e\x67\x70\150\157\156\145\116\x61\155\145\113\145\171\72\40", $this->billingphoneNameKey);
        $this->billingstreetAddressNameKey = $ft["\142\x69\x6c\154\151\156\147\137\141\144\144\x72\145\163\x73\137\x61\164\x74\162\x69\x62\165\x74\x65"];
        $this->billingstreetAddressNameKey = $this->spUtility->isBlank($this->billingstreetAddressNameKey) ? SPConstants::MAP_ADDRESS_BILLING : $this->billingstreetAddressNameKey;
        $this->spUtility->log_debug("\142\x69\x6c\x6c\x69\156\x67\163\x74\162\x65\x65\x74\101\x64\144\162\145\x73\x73\116\141\155\145\x4b\145\x79\72\x20", $this->billingstreetAddressNameKey);
        $this->billingmapStateNameKey = $ft["\142\x69\154\154\x69\x6e\147\x5f\163\x74\x61\164\x65\137\141\164\x74\162\x69\x62\165\164\x65"];
        $this->billingmapStateNameKey = $this->spUtility->isBlank($this->billingmapStateNameKey) ? SPConstants::MAP_STATE_BILLING : $this->billingmapStateNameKey;
        $this->spUtility->log_debug("\142\x69\x6c\x6c\151\156\x67\x6d\141\x70\123\164\x61\164\x65\116\141\x6d\145\113\145\x79\x3a\40", $this->billingmapStateNameKey);
        $this->billingzipCodeNameKey = $ft["\x62\151\154\154\x69\156\147\137\172\151\160\137\141\164\164\x72\151\x62\x75\x74\x65"];
        $this->billingzipCodeNameKey = $this->spUtility->isBlank($this->billingzipCodeNameKey) ? SPConstants::MAP_ZIPCODE_BILLING : $this->billingzipCodeNameKey;
        $this->spUtility->log_debug("\x62\x69\154\154\151\156\147\172\x69\160\x43\x6f\144\x65\x4e\x61\155\145\113\x65\x79\72\40", $this->billingzipCodeNameKey);
        $this->shippingcountryNameKey = $ft["\x73\x68\x69\x70\160\151\x6e\147\137\x63\157\x75\156\164\x72\x79\137\141\x74\164\162\x69\142\165\x74\145"];
        $this->shippingcountryNameKey = $this->spUtility->isBlank($this->shippingcountryNameKey) ? SPConstants::MAP_COUNTRY_SHIPPING : $this->shippingcountryNameKey;
        $this->spUtility->log_debug("\163\x68\x69\160\x70\x69\x6e\147\143\x6f\165\x6e\x74\162\171\116\141\x6d\145\x4b\145\171\72\40", $this->shippingcountryNameKey);
        $this->shippingcityNameKey = $ft["\x73\150\151\160\x70\x69\156\x67\137\143\151\x74\x79\137\141\x74\x74\162\151\x62\165\x74\x65"];
        $this->shippingcityNameKey = $this->spUtility->isBlank($this->shippingcityNameKey) ? SPConstants::MAP_CITY_SHIPPING : $this->shippingcityNameKey;
        $this->spUtility->log_debug("\x73\x68\151\160\160\x69\156\x67\143\151\164\x79\116\x61\155\x65\x4b\x65\x79\72\40", $this->shippingcityNameKey);
        $this->shippingphoneNameKey = $ft["\x73\150\151\x70\160\151\156\x67\x5f\160\150\157\156\x65\137\141\x74\x74\162\151\x62\x75\164\145"];
        $this->shippingphoneNameKey = $this->spUtility->isBlank($this->shippingphoneNameKey) ? SPConstants::MAP_PHONE_SHIPPING : $this->shippingphoneNameKey;
        $this->spUtility->log_debug("\163\x68\151\160\160\x69\x6e\147\x70\x68\x6f\x6e\x65\116\141\x6d\x65\x4b\145\x79\72\x20", $this->shippingphoneNameKey);
        $this->shippingstreetAddressNameKey = $ft["\x73\x68\x69\160\160\x69\x6e\x67\x5f\141\x64\x64\162\145\x73\163\x5f\141\x74\x74\x72\x69\x62\x75\x74\x65"];
        $this->shippingstreetAddressNameKey = $this->spUtility->isBlank($this->shippingstreetAddressNameKey) ? SPConstants::MAP_ADDRESS_SHIPPING : $this->shippingstreetAddressNameKey;
        $this->spUtility->log_debug("\163\150\x69\160\x70\151\x6e\x67\163\x74\162\x65\145\164\101\x64\144\162\x65\163\163\116\x61\155\x65\113\x65\x79\x3a\x20", $this->shippingstreetAddressNameKey);
        $this->shippingmapStateNameKey = $ft["\x73\x68\151\160\160\151\156\147\137\163\164\141\x74\145\x5f\141\x74\x74\162\x69\142\x75\164\145"];
        $this->shippingmapStateNameKey = $this->spUtility->isBlank($this->shippingmapStateNameKey) ? SPConstants::MAP_STATE_SHIPPING : $this->shippingmapStateNameKey;
        $this->spUtility->log_debug("\163\x68\151\x70\x70\x69\x6e\x67\155\141\160\x53\164\x61\164\145\x4e\x61\155\x65\x4b\145\171\72\40", $this->shippingmapStateNameKey);
        $this->shippingzipCodeNameKey = $ft["\163\150\x69\x70\x70\151\x6e\147\137\172\x69\x70\x5f\x61\164\x74\162\151\142\165\x74\145"];
        $this->shippingzipCodeNameKey = $this->spUtility->isBlank($this->shippingzipCodeNameKey) ? SPConstants::MAP_ZIPCODE_SHIPPING : $this->shippingzipCodeNameKey;
        $this->custom_attributes = $ft["\143\x75\163\x74\x6f\155\137\141\164\x74\162\x69\142\165\x74\145\x73"];
        $this->custom_tablename = $ft["\143\165\x73\164\x6f\155\x5f\164\141\x62\154\145\x6e\x61\155\145"];
        $this->spUtility->log_debug("\x63\165\163\x74\x6f\x6d\x5f\x74\x61\142\x6c\145\x6e\x61\155\x65\x3a\40", $this->custom_tablename);
        $this->defaultRole = $ft["\x64\145\146\141\x75\154\164\137\162\x6f\x6c\x65"];
        $this->defaultGroup = $ft["\x64\145\146\x61\x75\x6c\x74\137\x67\x72\157\165\160"];
        $this->spUtility->log_debug("\144\x65\x66\141\x75\154\x74\x47\162\157\x75\x70\72\40", $this->defaultGroup);
        $this->role_mapping = $ft["\x72\x6f\154\x65\163\x5f\x6d\141\160\160\145\x64"];
        $this->group_mapping = $ft["\147\x72\157\x75\160\x73\137\x6d\141\160\160\x65\144"];
        $this->spUtility->log_debug("\x67\x72\x6f\x75\x70\x5f\155\x61\160\x70\151\156\x67\72\x20", print_r($this->group_mapping, true));
        $this->updateRole = $ft["\x75\160\144\x61\x74\x65\x5f\x62\141\x63\x6b\x65\x6e\x64\x5f\162\157\x6c\145\x73\137\157\x6e\x5f\163\x73\x6f"];
        $this->updateFrontendRole = $ft["\x75\160\144\141\164\x65\137\x66\x72\x6f\x6e\164\145\x6e\x64\137\x67\162\157\165\x70\x73\137\157\156\x5f\x73\163\157"];
        $this->updateAttribute = $ft["\165\x70\x64\141\164\x65\137\x61\x74\x74\162\x69\142\165\164\x65\x73\137\157\156\x5f\x6c\x6f\x67\151\156"];
        $this->spUtility->log_debug("\165\x70\144\x61\x74\145\x5f\141\164\164\x72\x69\x62\165\x74\x65\163\137\157\x6e\x5f\154\x6f\x67\x69\x6e\x3a\40", $this->updateAttribute);
        $this->autoCreateAdminUser = $ft["\141\165\164\157\137\x63\x72\145\141\164\x65\x5f\x61\x64\155\x69\156\x5f\165\x73\145\162\163"];
        $this->autoCreateCustomer = $ft["\x61\x75\x74\157\x5f\143\162\x65\x61\x74\x65\x5f\143\x75\163\164\157\x6d\145\162\x73"];
        $this->auto_redirect = $ft["\x61\x75\x74\157\x5f\162\145\x64\x69\162\x65\143\x74\137\x74\x6f\137\151\x64\x70"];
        $G5 = !empty($this->attrs[$this->emailAttribute]) ? $this->attrs[$this->emailAttribute][0] : null;
        $Aq = !empty($this->attrs[$this->firstNameKey]) ? $this->attrs[$this->firstNameKey][0] : null;
        $ko = !empty($this->attrs[$this->lastNameKey]) ? $this->attrs[$this->lastNameKey][0] : null;
        $Rg = !empty($this->attrs[$this->usernameAttribute]) ? $this->attrs[$this->usernameAttribute][0] : null;
        $PU = !empty($this->attrs[$this->groupNameKey]) ? $this->attrs[$this->groupNameKey] : null;
        $BL = !empty($this->attrs[$this->groupNameKey]) ? $this->attrs[$this->groupNameKey] : null;
        $qh = !empty($this->attrs[$this->billingcountryNameKey]) ? $this->attrs[$this->billingcountryNameKey][0] : null;
        $IS = !empty($this->attrs[$this->billingcityNameKey]) ? $this->attrs[$this->billingcityNameKey][0] : null;
        $S6 = !empty($this->attrs[$this->billingphoneNameKey]) ? $this->attrs[$this->billingphoneNameKey][0] : null;
        $SV = !empty($this->attrs[$this->billingstreetAddressNameKey]) ? $this->attrs[$this->billingstreetAddressNameKey][0] : null;
        $al = !empty($this->attrs[$this->billingmapStateNameKey]) ? $this->attrs[$this->billingmapStateNameKey][0] : null;
        $yP = !empty($this->attrs[$this->billingzipCodeNameKey]) ? $this->attrs[$this->billingzipCodeNameKey][0] : null;
        $uf = !empty($this->attrs[$this->shippingcountryNameKey]) ? $this->attrs[$this->shippingcountryNameKey][0] : null;
        $OO = !empty($this->attrs[$this->shippingcityNameKey]) ? $this->attrs[$this->shippingcityNameKey][0] : null;
        $qR = !empty($this->attrs[$this->shippingphoneNameKey]) ? $this->attrs[$this->shippingphoneNameKey][0] : null;
        $RN = !empty($this->attrs[$this->shippingstreetAddressNameKey]) ? $this->attrs[$this->shippingstreetAddressNameKey][0] : null;
        $cZ = !empty($this->attrs[$this->shippingmapStateNameKey]) ? $this->attrs[$this->shippingmapStateNameKey][0] : null;
        $Ju = !empty($this->attrs[$this->shippingzipCodeNameKey]) ? $this->attrs[$this->shippingzipCodeNameKey][0] : null;
        $As = $this->getRequest()->getParams();
        if (!$this->spUtility->isBlank($this->checkIfMatchBy)) {
            goto Yo;
        }
        $this->checkIfMatchBy = SPConstants::DEFAULT_MAP_BY;
        Yo:
        $this->spUtility->log_debug("\x63\150\145\143\x6b\111\146\115\x61\x74\143\150\x42\x79\x3a\x20", $this->checkIfMatchBy);
        $Kb = '';
        if (is_array($PU)) {
            goto pb;
        }
        $Kb = $this->spUtility->isBlank(json_decode((string) $PU)) ? $PU : json_decode((string) $PU)[0];
        goto Ru;
        pb:
        $Kb = $PU;
        Ru:
        $this->groupNameKey = $Kb;
        $this->spUtility->log_debug("\x46\x69\x72\163\x74\x47\162\x6f\x75\x70\40\146\x72\x6f\x6d\x20\x49\144\x50\40\50\151\146\40\115\165\154\x74\151\x70\154\x65\51\72\x20");
        $y0 = null;
        $CG = null;
        if (!$qh) {
            goto eI;
        }
        $y0 = $this->getCountryCodeBasedOnMapping($qh);
        eI:
        if (!$uf) {
            goto qc;
        }
        $CG = $this->getCountryCodeBasedOnMapping($uf);
        qc:
        $this->processUserAction($G5, $Aq, $ko, $Rg, $Kb, $this->defaultGroup, $this->checkIfMatchBy, $this->attrs["\116\x61\155\x65\x49\x44"][0], $As, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG);
    }
    private function processUserAction($G5, $Aq, $ko, $Rg, $BL, $wR, $nY, $Q5, $As, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG)
    {
        $G5 = !$this->spUtility->isBlank($G5) ? $G5 : $this->findUserEmail();
        $zc = false;
        $this->spUtility->log_debug("\40\x69\x6e\x73\x69\x64\145\x20\x70\162\157\x63\x65\163\163\x55\163\x65\162\101\143\164\x69\x6f\156\50\x29\40");
        $zc = $this->spUtility->checkIfFlowStartedFromBackend($this->relayState);
        $this->spUtility->log_debug("\160\x72\157\x63\x65\x73\163\125\163\x65\162\101\x63\x74\151\157\x6e\50\x29\x3a\x20\x69\163\101\144\x6d\151\x6e\x3a\40" . json_encode($zc));
        $user = null;
        $c5 = \Magento\Framework\App\ObjectManager::getInstance();
        $FK = $c5->get("\x4d\x61\x67\x65\156\x74\157\134\103\x75\163\164\157\155\145\x72\x5c\x4d\157\x64\145\x6c\134\123\x65\163\163\x69\157\x6e");
        if (!$FK->isLoggedIn()) {
            goto Uh;
        }
        $this->spUtility->log_debug("\160\162\157\x63\x65\163\163\125\x73\x65\x72\101\143\164\151\x6f\156\50\x29\72\x20\x55\163\145\162\x20\x73\145\163\163\x69\x6f\x6e\40\106\x6f\x75\x6e\x64");
        $JN = $this->spUtility->getBaseUrlFromUrl($this->relayState);
        if (!($G5 != $FK->getCustomer()->getEmail())) {
            goto YA;
        }
        $this->messageManager->addErrorMessage(__("\x41\156\157\164\150\x65\x72\x20\125\x73\x65\162\40\x69\x73\x20\141\154\x72\x65\x61\144\171\x20\154\157\x67\147\x65\x64\x20\x69\156\x2e\40\120\x6c\145\141\163\145\x20\x6c\x6f\147\x6f\165\164\x20\x66\151\x72\x73\x74\x20\141\156\x64\40\164\150\145\x6e\x20\164\162\171\x20\x61\x67\x61\151\156\x2e"));
        $this->spUtility->log_debug("\101\x6e\x6f\164\x68\x65\162\40\x55\x73\145\162\40\151\163\40\x61\x6c\x72\x65\141\x64\171\x20\154\157\147\x67\145\x64\40\x69\x6e\56\40\x50\154\145\x61\163\x65\40\x6c\x6f\147\157\x75\x74\40\x66\151\x72\163\x74\40\x61\156\144\x20\x74\150\145\x6e\40\x74\x72\171\40\141\147\x61\x69\156\56");
        YA:
        $this->spUtility->log_debug("\160\x72\157\143\x65\163\163\125\x73\145\x72\101\143\164\151\157\156\50\51\x3a\x20\x52\145\x64\151\x72\145\x63\164\151\x6e\147\x20\x74\x6f\x3a\x20" . $JN);
        $this->responseFactory->create()->setRedirect($JN)->sendResponse();
        exit;
        Uh:
        $this->spUtility->log_debug("\40\x69\156\x73\x69\x64\145\x20\x70\x72\157\x63\x65\x73\x73\125\x73\x65\x72\101\x63\x74\x69\x6f\156\50\51\72\x20\116\157\40\125\x73\x65\162\40\x73\x65\x73\x73\151\x6f\x6e\40\106\157\165\x6e\144");
        if ($zc) {
            goto Rs;
        }
        $we = $this->defaultGroup;
        $KE = $this->autoCreateCustomer;
        $user = $this->getCustomerFromAttributes($G5);
        if ($user) {
            goto Y8;
        }
        if (!$user && $KE) {
            goto M_;
        }
        $this->spUtility->log_debug("\x54\150\151\163\x20\x43\165\163\164\157\155\x65\x72\40\144\157\x65\x73\40\156\157\164\x20\145\x78\151\x73\164\163\40\141\156\x64\x20\143\x61\156\156\x6f\164\x20\x62\x65\x20\141\x75\164\x6f\55\x63\162\x65\141\164\x65\144\x2e");
        $this->messageManager->addErrorMessage(__("\x54\x68\x69\163\x20\103\165\x73\164\157\x6d\x65\x72\40\144\x6f\x65\x73\x20\156\x6f\164\40\145\x78\151\163\x74\163\40\141\156\x64\x20\x63\x61\156\156\x6f\164\40\x62\x65\40\x61\x75\164\157\55\x63\x72\145\141\164\145\x64\x2e\120\154\x65\141\163\x65\x20\x63\157\156\x74\141\x63\x74\x20\171\157\x75\162\x20\x41\144\155\x69\156\x69\x73\164\x72\x61\x74\157\162\x2e"));
        if ($this->auto_redirect) {
            goto RE;
        }
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl() . "\143\x75\163\x74\x6f\x6d\145\x72\x2f\x61\143\x63\157\x75\x6e\164")->sendResponse();
        goto EC;
        RE:
        $As = $this->getRequest()->getParams();
        $lP = $this->storeManager->getStore()->getBaseUrl();
        if (empty($As["\122\x65\x6c\141\171\x53\164\141\x74\x65"])) {
            goto AY;
        }
        $lP = $As["\x52\x65\154\x61\171\x53\164\141\x74\145"];
        AY:
        $this->responseFactory->create()->setRedirect($lP)->sendResponse();
        EC:
        exit("\124\150\151\x73\x20\103\165\163\x74\x6f\155\145\162\x20\x64\157\x65\x73\40\156\157\164\x20\x65\170\151\163\164\x73\x20\141\156\144\x20\143\141\x6e\x6e\157\164\40\142\x65\x20\141\x75\x74\x6f\x2d\143\162\x65\x61\x74\x65\144\56\x50\154\x65\141\163\145\40\x63\x6f\x6e\x74\x61\x63\x74\x20\171\x6f\x75\x72\40\x41\x64\x6d\x69\x6e\151\163\164\x72\141\164\157\x72\56");
        goto v9;
        M_:
        $this->spUtility->log_debug("\40\x70\x72\157\143\x65\x73\163\x55\163\145\162\101\x63\164\x69\x6f\x6e\50\51\72\x20\x43\165\x73\x74\x6f\155\x65\162\40\116\x6f\x74\x20\x46\157\x75\156\x64\x2c\x20\x43\x72\145\x61\164\151\x6e\147\x20\117\x6e\145\x3a\x20");
        $user = $this->createNewUser($G5, $Aq, $ko, $Rg, $BL, $we, $Q5, $user, $zc, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG);
        v9:
        goto z7;
        Y8:
        $this->spUtility->log_debug("\40\x70\162\157\143\x65\x73\163\125\163\145\162\x41\143\x74\x69\157\x6e\50\51\40\72\x20\125\x73\x65\162\x20\x46\x6f\x75\x6e\x64\72\40\125\x70\x64\141\x74\151\x6e\x67\40\x41\x74\164\x72\151\x62\x75\164\145\x73\x20");
        $user = $this->updateUserAttributes($Rg, $G5, $Aq, $ko, $BL, $we, $Q5, $user, $zc, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG);
        $user = $this->updateCustomAttributes($user, $G5);
        z7:
        goto Il;
        Rs:
        $wR = $this->defaultRole;
        $this->spUtility->log_debug("\160\x72\x6f\143\145\x73\163\x55\x73\x65\162\x41\x63\164\151\x6f\156\x28\x29\x20\x3a\x20\40\x64\145\x66\141\x75\154\x74\122\x6f\x6c\145\x20\146\162\157\x6d\40\x73\x65\x74\x74\x69\156\147\163\72\40" . json_encode($wR));
        $user = $this->getAdminUserFromAttributes($G5);
        $KE = $this->autoCreateAdminUser;
        if (!$this->spUtility->isBlank($user)) {
            goto x6;
        }
        if ($this->spUtility->isBlank($user) && $KE) {
            goto Vs;
        }
        echo "\x54\150\x69\x73\x20\x62\x61\x63\x6b\x65\x6e\x64\40\165\x73\145\162\x20\x64\157\145\x73\x20\x6e\157\164\x20\145\x78\x69\163\164\x73\40\x61\x6e\x64\40\143\x61\156\156\x6f\x74\40\142\145\x20\141\x75\x74\157\55\143\162\x65\141\164\145\x64\56\x20\120\x6c\x65\141\x73\145\x20\x63\157\156\x74\141\x63\164\x20\171\x6f\165\x72\x20\x61\144\155\151\x6e\x69\163\x74\162\141\164\x6f\x72\56";
        exit;
        goto Tu;
        Vs:
        $this->spUtility->log_debug("\160\162\x6f\x63\145\163\163\125\163\x65\x72\x41\143\164\x69\x6f\x6e\50\x29\72\x20\x41\144\x6d\151\156\x55\163\x65\x72\x20\116\x6f\x74\40\106\x6f\x75\x6e\144\x3a\x20\103\162\x65\141\164\151\x6e\147\x20\117\x6e\145");
        $user = $this->createNewUser($G5, $Aq, $ko, $Rg, $BL, $wR, $Q5, $user, $zc, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG);
        Tu:
        goto yr;
        x6:
        $this->spUtility->log_debug("\160\162\157\143\x65\x73\x73\x55\x73\145\162\101\x63\x74\151\157\x6e\x28\51\x3a\x20\101\x64\155\151\x6e\x20\x55\x73\x65\x72\x20\x46\x6f\165\x6e\x64\72\x20\125\160\x64\x61\x74\151\x6e\147\40\101\x74\x74\x72\151\x62\x75\164\x65\163\40");
        $user = $this->updateUserAttributes($Rg, $G5, $Aq, $ko, $BL, $wR, $Q5, $user, $zc, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG);
        yr:
        Il:
        $this->spUtility->log_debug("\x50\x72\x6f\x63\145\163\163\x55\163\x65\x72\x41\143\x74\x69\x6f\x6e\x28\51\x20\72\x20\142\145\x66\157\x72\x65\x20\x72\x65\x64\151\x72\145\143\164\151\156\x67\40\x75\163\x65\x72\x73\72\40\162\145\x6c\141\x79\123\164\x61\164\x65\40\x3a" . $this->relayState);
        if (null == $user && $zc) {
            goto R_;
        }
        if (null == $user) {
            goto nT;
        }
        if (null != $user && $zc) {
            goto Gb;
        }
        $this->spUtility->setSessionData("\143\165\163\164\x6f\x6d\145\162\x5f\160\157\x73\164\x5f\x6c\x6f\147\x6f\x75\164", 1);
        $user = $this->customerModel->load($user->getId());
        $this->spUtility->log_debug("\120\x72\157\x63\145\163\x73\x55\163\x65\x72\101\x63\164\x69\x6f\x6e\50\51\40\x3a\40\x72\x65\x64\151\162\x65\x63\x74\151\x6e\x67\x20\x63\165\x73\x74\x6f\155\x65\162\40\x3a");
        $this->customerLoginAction->setUser($user)->setCustomerId($user->getId())->setRelayState($this->relayState)->setAxCompanyId($this->accountId)->execute();
        goto lD;
        Gb:
        $this->spUtility->setAdminSessionData("\141\x64\155\x69\x6e\x5f\x70\157\163\x74\137\x6c\x6f\147\157\165\164", 1);
        $this->spUtility->log_debug("\x50\x72\157\x63\145\x73\x73\125\x73\x65\162\x41\x63\x74\151\x6f\x6e\50\x29\x20\72\x20\162\145\144\x69\162\x65\143\164\x69\156\x67\x20\x61\144\x6d\151\x6e\40\72");
        $this->redirectToBackendAndLogin($user->getUsername(), $this->sessionIndex, $this->relayState);
        lD:
        goto Fm;
        R_:
        echo "\124\150\151\163\40\125\163\x65\x72\40\x64\157\145\x73\x20\x6e\157\x74\40\x65\x78\x69\163\164\x73\40\x61\156\x64\40\x63\141\156\x6e\x6f\164\40\142\145\x20\141\165\x74\157\55\x63\162\x65\141\164\145\144\x2e\x20\120\x6c\x65\141\x73\145\x20\143\x6f\x6e\x74\x61\x63\164\x20\x79\x6f\x75\x72\x20\101\144\x6d\x69\x6e\x69\x73\x74\x72\141\x74\157\162\56";
        exit;
        goto Fm;
        nT:
        $this->messageManager->addErrorMessage(__("\124\x68\x69\163\40\x55\x73\145\162\40\144\157\145\x73\x20\x6e\x6f\x74\x20\145\x78\151\163\164\163\40\141\x6e\x64\x20\143\141\x6e\x6e\157\x74\40\x62\145\x20\x61\165\x74\157\55\x63\162\x65\x61\164\145\144\x2e\x20\120\x6c\145\x61\x73\145\40\x63\157\156\x74\141\143\164\x20\x79\157\x75\x72\40\x41\144\155\x69\156\151\x73\164\x72\141\164\x6f\162\56"));
        if ($this->auto_redirect) {
            goto I5;
        }
        $JN = $this->storeManager->getStore()->getBaseUrl();
        $this->responseFactory->create()->setRedirect($JN . "\x63\x75\x73\x74\x6f\155\145\x72\x2f\141\x63\143\x6f\165\x6e\x74")->sendResponse();
        exit;
        goto ZQ;
        I5:
        $As = $this->getRequest()->getParams();
        $lP = $this->storeManager->getStore()->getBaseUrl();
        if (empty($As["\122\x65\154\141\x79\123\x74\141\164\x65"])) {
            goto Xk;
        }
        $lP = $As["\122\x65\x6c\141\x79\123\x74\x61\164\145"];
        Xk:
        $this->responseFactory->create()->setRedirect($lP)->sendResponse();
        exit;
        ZQ:
        Fm:
    }
    private function checkIfB2BFlow()
    {
        $c7 = $this->spUtility->getStoreConfig(SPConstants::B2B_STORE_URL);
        if ($this->spUtility->isBlank($c7)) {
            goto l0;
        }
        $W4 = strpos($this->relayState, $c7);
        if (!($W4 !== false && !$this->spUtility->isBlank($this->accountId))) {
            goto Gl;
        }
        $this->spUtility->log_debug("\x63\x68\x65\x63\153\x49\146\x42\x32\x42\x46\x6c\x6f\x77\72\x20\x42\62\x42\x20\x46\154\x6f\167\x20\x46\157\x75\156\x64\x3a\x20");
        return true;
        Gl:
        l0:
        $this->spUtility->log_debug("\143\x68\x65\x63\153\x49\x66\x42\62\102\106\x6c\x6f\167\72\x20\116\x6f\x74\40\102\62\x42\40\x66\154\x6f\x77\56\x2e\x20\103\157\x6e\164\151\156\x75\x69\x6e\147\x20\167\x69\x74\150\x20\102\62\103\72\40");
        return false;
    }
    public function getCountryCodeBasedOnMapping($Yk)
    {
        $Ae = ["\x41\x46\x47\x48\x41\x4e\111\123\124\x41\x4e" => "\x41\x46", "\xc3\205\x4c\x41\116\x44\x20\111\x53\114\101\x4e\x44\123" => "\101\130", "\x41\x4c\102\101\x4e\x49\x41" => "\x41\114", "\101\x4c\x47\x45\x52\111\101" => "\104\x5a", "\101\x4d\105\122\111\103\101\x4e\40\x53\101\115\117\101" => "\101\x53", "\101\x4e\x44\x4f\x52\x52\101" => "\x41\x44", "\101\x4e\x47\x4f\114\101" => "\101\117", "\x41\x4e\x47\x55\x49\114\x4c\x41" => "\101\x49", "\x41\x4e\124\101\x52\x43\124\111\x43\x41" => "\101\121", "\101\116\124\111\x47\x55\x41\40\x41\x4e\104\40\x42\x41\122\102\x55\x44\101" => "\x41\x47", "\101\122\107\105\116\x54\111\116\101" => "\101\122", "\x41\122\x4d\x45\x4e\x49\x41" => "\x41\x4d", "\x41\x52\x55\102\x41" => "\x41\127", "\101\125\x53\124\122\101\114\111\101" => "\x41\x55", "\x41\x55\x53\124\x52\111\101" => "\101\124", "\x41\x5a\105\x52\102\x41\111\112\101\116" => "\101\x5a", "\102\101\110\101\x4d\101\123" => "\102\x53", "\x42\x41\x48\122\x41\x49\116" => "\x42\x48", "\102\101\x4e\107\x4c\101\104\x45\x53\x48" => "\102\104", "\102\101\x52\x42\x41\104\x4f\x53" => "\x42\x42", "\x42\105\114\101\122\125\x53" => "\x42\x59", "\102\105\114\x47\111\x55\x4d" => "\x42\x45", "\x42\105\114\111\132\105" => "\102\132", "\x42\105\116\x49\x4e" => "\102\x4a", "\102\x45\x52\115\125\x44\x41" => "\102\x4d", "\102\110\x55\x54\x41\x4e" => "\x42\124", "\102\x4f\114\x49\126\x49\101" => "\102\x4f", "\102\117\123\x4e\111\101\40\x41\116\x44\40\110\x45\x52\132\x45\107\x4f\x56\111\x4e\101" => "\x42\x41", "\x42\117\x54\x53\x57\101\x4e\x41" => "\x42\x57", "\x42\x4f\x55\x56\x45\x54\40\x49\x53\x4c\x41\x4e\104" => "\102\126", "\102\x52\101\132\111\x4c" => "\102\122", "\x42\122\111\124\x49\123\110\x20\x49\116\x44\x49\101\116\40\x4f\103\x45\101\116\40\x54\105\122\x52\x49\x54\x4f\122\x59" => "\x49\x4f", "\102\x52\111\124\111\123\x48\x20\x56\111\122\x47\x49\x4e\x20\x49\x53\114\101\116\104\123" => "\x56\107", "\102\122\125\x4e\x45\x49" => "\x42\x4e", "\x42\x55\114\x47\101\122\x49\101" => "\102\107", "\102\x55\x52\113\x49\116\x41\x20\106\x41\x53\117" => "\102\x46", "\x42\125\122\125\x4e\x44\x49" => "\x42\111", "\103\101\x4d\102\x4f\x44\111\101" => "\x4b\x48", "\x43\x41\115\x45\x52\x4f\117\x4e" => "\x43\115", "\x43\101\x4e\x41\x44\101" => "\x43\101", "\103\101\x50\x45\40\x56\105\x52\104\x45" => "\x43\126", "\103\x41\122\111\102\102\105\x41\116\40\116\x45\124\110\x45\122\x4c\x41\116\x44\x53" => "\x42\x51", "\x43\x41\x59\115\x41\116\x20\x49\x53\114\x41\116\104\123" => "\x4b\131", "\x43\x45\116\x54\122\101\x4c\40\x41\106\122\111\103\101\x4e\40\x52\x45\120\125\x42\114\111\103" => "\x43\x46", "\103\x48\x41\x44" => "\x54\104", "\103\x48\111\x4c\x45" => "\103\114", "\103\x48\x49\116\101" => "\103\116", "\x43\x48\x52\x49\x53\x54\x4d\101\x53\40\111\x53\114\101\116\104" => "\103\x58", "\103\x4f\103\117\123\40\133\x4b\105\105\x4c\x49\116\x47\x5d\x20\111\123\114\x41\x4e\104\x53" => "\x43\x43", "\103\x4f\114\117\115\102\111\x41" => "\x43\117", "\x43\117\115\117\x52\x4f\x53" => "\x4b\115", "\103\x4f\x4e\107\117\40\x2d\x20\102\x52\101\132\132\x41\x56\x49\114\x4c\105" => "\103\107", "\x43\117\x4e\x47\x4f\x20\x2d\40\x4b\x49\116\123\x48\x41\x53\101" => "\103\x44", "\103\117\x4f\x4b\40\111\x53\114\x41\x4e\104\x53" => "\103\x4b", "\103\117\x53\124\101\x20\122\111\x43\101" => "\103\122", "\x43\303\x94\x54\x45\x20\104\342\x80\x99\x49\126\117\x49\x52\105" => "\x43\x49", "\103\x52\117\101\x54\111\101" => "\110\x52", "\103\x55\x42\101" => "\103\125", "\x43\125\122\101\303\207\x41\117" => "\103\127", "\103\131\120\x52\x55\123" => "\x43\131", "\x43\132\105\x43\110\40\x52\x45\x50\x55\x42\x4c\x49\x43" => "\103\132", "\104\x45\116\x4d\x41\122\x4b" => "\x44\113", "\x44\112\x49\102\x4f\125\x54\111" => "\x44\x4a", "\x44\117\115\x49\116\x49\103\101" => "\104\x4d", "\x44\117\115\x49\116\111\103\x41\x4e\40\x52\x45\x50\125\102\114\111\x43" => "\x44\x4f", "\105\x43\x55\x41\x44\117\122" => "\105\x43", "\x45\107\x59\x50\x54" => "\x45\x47", "\x45\x4c\40\x53\101\x4c\x56\x41\x44\x4f\x52" => "\123\x56", "\105\x51\x55\x41\124\117\122\111\101\x4c\40\x47\125\x49\116\x45\101" => "\107\121", "\105\x52\111\124\122\x45\101" => "\105\x52", "\105\123\x54\117\116\111\x41" => "\105\105", "\x45\x54\x48\x49\117\120\x49\101" => "\105\x54", "\x46\101\114\113\114\x41\116\104\40\111\123\x4c\x41\x4e\x44\123" => "\x46\x4b", "\106\x41\122\117\105\40\x49\x53\114\x41\x4e\104\123" => "\106\117", "\106\x49\x4a\x49" => "\x46\112", "\x46\111\x4e\114\x41\x4e\x44" => "\106\x49", "\x46\x52\101\x4e\x43\105" => "\x46\x52", "\x46\122\x45\116\x43\x48\40\107\x55\111\x41\116\101" => "\107\106", "\x46\122\x45\x4e\103\x48\x20\120\117\x4c\131\x4e\x45\123\111\x41" => "\x50\x46", "\x46\x52\x45\x4e\103\110\x20\123\x4f\x55\x54\110\105\x52\116\x20\x54\x45\122\x52\x49\124\117\122\111\x45\x53" => "\x54\106", "\x47\x41\102\117\x4e" => "\107\x41", "\107\x41\x4d\102\x49\101" => "\x47\x4d", "\107\x45\x4f\122\107\111\101" => "\107\x45", "\x47\105\122\x4d\101\x4e\x59" => "\x44\105", "\x47\110\x41\116\101" => "\107\110", "\107\x49\x42\122\101\x4c\124\101\122" => "\107\111", "\x47\122\x45\x45\x43\x45" => "\107\x52", "\107\x52\105\105\x4e\x4c\x41\x4e\104" => "\x47\114", "\x47\122\105\x4e\101\104\101" => "\x47\x44", "\107\x55\101\104\105\x4c\117\125\x50\x45" => "\107\120", "\x47\125\101\x4d" => "\107\125", "\107\x55\x41\x54\105\115\101\114\x41" => "\107\x54", "\x47\x55\105\122\116\123\105\x59" => "\x47\107", "\107\x55\x49\x4e\105\101" => "\x47\116", "\x47\x55\111\x4e\105\101\55\x42\x49\123\123\x41\x55" => "\107\x57", "\x47\x55\x59\x41\116\101" => "\x47\131", "\110\x41\x49\x54\x49" => "\110\x54", "\x48\105\101\122\104\40\x49\123\x4c\101\x4e\104\x20\x41\116\104\40\x4d\x43\x44\x4f\116\101\x4c\x44\x20\x49\123\114\101\116\104\123" => "\x48\x4d", "\110\117\116\x44\x55\122\101\x53" => "\110\x4e", "\x48\x4f\116\x47\x20\113\117\x4e\107\x20\x53\101\x52\40\103\x48\111\x4e\x41" => "\x48\113", "\110\125\x4e\x47\101\122\131" => "\110\x55", "\x49\103\x45\x4c\101\x4e\x44" => "\111\123", "\x49\116\x44\111\101" => "\111\116", "\111\x4e\104\117\x4e\x45\x53\111\x41" => "\x49\104", "\111\122\x41\116" => "\111\122", "\x49\122\x41\x51" => "\111\x51", "\x49\x52\x45\x4c\x41\116\104" => "\111\105", "\x49\123\x4c\x45\40\117\106\40\x4d\x41\x4e" => "\x49\115", "\111\x53\x52\x41\105\x4c" => "\x49\114", "\111\124\101\x4c\x59" => "\111\x54", "\112\101\x4d\101\x49\x43\101" => "\x4a\115", "\x4a\101\x50\101\116" => "\112\x50", "\112\x45\x52\123\105\131" => "\112\x45", "\112\117\x52\104\x41\x4e" => "\x4a\117", "\113\101\x5a\101\x4b\110\123\124\101\116" => "\x4b\x5a", "\113\105\x4e\x59\x41" => "\x4b\105", "\x4b\111\x52\x49\102\x41\124\x49" => "\x4b\111", "\x4b\x55\x57\101\x49\124" => "\113\x57", "\113\x59\122\x47\131\x5a\x53\x54\x41\116" => "\x4b\107", "\114\101\117\x53" => "\114\x41", "\114\101\124\126\x49\x41" => "\x4c\126", "\114\105\x42\101\x4e\x4f\x4e" => "\x4c\102", "\x4c\x45\123\117\x54\x48\117" => "\114\123", "\x4c\111\102\105\x52\111\x41" => "\x4c\x52", "\x4c\111\102\131\101" => "\x4c\131", "\x4c\111\105\103\110\x54\105\116\123\124\105\x49\x4e" => "\114\x49", "\x4c\x49\x54\x48\x55\x41\116\x49\101" => "\114\x54", "\x4c\x55\x58\x45\x4d\102\117\125\x52\107" => "\x4c\125", "\x4d\101\x43\x41\x55\x20\123\x41\x52\x20\x43\x48\x49\x4e\x41" => "\115\x4f", "\115\101\x43\x45\104\117\116\111\101" => "\x4d\113", "\x4d\x41\104\x41\x47\101\x53\103\101\x52" => "\x4d\x47", "\x4d\101\x4c\x41\x57\111" => "\115\127", "\x4d\101\114\101\131\x53\x49\x41" => "\x4d\x59", "\115\101\x4c\x44\x49\x56\105\123" => "\x4d\126", "\115\x41\114\111" => "\115\114", "\x4d\101\114\x54\x41" => "\x4d\124", "\x4d\x41\x52\123\x48\x41\x4c\114\40\111\123\114\x41\116\x44\123" => "\x4d\110", "\115\x41\122\x54\111\x4e\111\x51\x55\105" => "\115\x51", "\x4d\101\x55\122\x49\x54\101\x4e\x49\x41" => "\115\122", "\115\101\125\122\x49\x54\x49\x55\x53" => "\x4d\x55", "\115\101\131\x4f\124\124\105" => "\131\124", "\x4d\x45\x58\x49\x43\x4f" => "\x4d\130", "\115\x49\103\122\117\116\x45\123\x49\x41" => "\x46\x4d", "\115\x4f\x4c\104\117\x56\101" => "\x4d\x44", "\115\x4f\x4e\101\103\x4f" => "\115\x43", "\115\x4f\116\107\117\x4c\111\101" => "\x4d\x4e", "\x4d\x4f\x4e\124\105\116\105\107\x52\117" => "\x4d\x45", "\x4d\117\x4e\x54\x53\105\x52\122\101\124" => "\x4d\123", "\115\x4f\x52\x4f\x43\103\117" => "\x4d\101", "\115\x4f\132\x41\115\102\x49\x51\x55\105" => "\115\132", "\x4d\131\x41\116\x4d\x41\122\x20\133\x42\x55\x52\115\101\135" => "\115\x4d", "\x4e\x41\115\x49\102\111\x41" => "\x4e\101", "\x4e\x41\x55\x52\125" => "\x4e\x52", "\x4e\105\120\x41\x4c" => "\x4e\x50", "\x4e\x45\124\110\x45\x52\x4c\x41\116\x44\123" => "\116\x4c", "\116\x45\124\110\105\122\114\101\116\x44\123\x20\x41\x4e\x54\111\x4c\x4c\105\x53" => "\101\116", "\x4e\x45\x57\40\x43\101\114\x45\x44\117\x4e\111\101" => "\116\103", "\x4e\x45\127\x20\132\x45\101\x4c\x41\116\x44" => "\116\132", "\116\111\103\x41\122\101\x47\x55\x41" => "\116\111", "\x4e\111\107\x45\x52" => "\x4e\x45", "\x4e\111\x47\105\x52\x49\101" => "\116\107", "\116\x49\125\x45" => "\x4e\x55", "\x4e\x4f\x52\x46\x4f\114\x4b\x20\x49\123\x4c\x41\116\104" => "\116\106", "\x4e\x4f\x52\x54\110\x45\x52\x4e\x20\115\x41\122\111\101\x4e\101\x20\x49\123\x4c\101\116\x44\123" => "\x4d\120", "\x4e\x4f\122\x54\x48\x20\113\117\122\105\101" => "\x4b\120", "\x4e\117\x52\127\x41\x59" => "\116\x4f", "\117\x4d\101\116" => "\117\115", "\x50\101\x4b\111\123\x54\101\116" => "\x50\113", "\120\101\114\x41\125" => "\x50\x57", "\x50\x41\114\x45\123\x54\111\116\111\x41\x4e\40\x54\105\122\x52\x49\124\x4f\122\111\105\123" => "\120\x53", "\120\101\x4e\x41\115\101" => "\x50\x41", "\120\x41\x50\125\101\x20\116\105\127\40\107\125\x49\116\105\101" => "\x50\x47", "\x50\101\122\101\107\x55\x41\131" => "\x50\131", "\120\x45\122\125" => "\120\105", "\x50\x48\x49\114\x49\x50\120\x49\x4e\x45\123" => "\x50\110", "\x50\x49\124\103\x41\x49\x52\x4e\40\111\x53\114\101\x4e\x44\x53" => "\x50\116", "\x50\x4f\x4c\x41\116\104" => "\x50\114", "\x50\x4f\x52\x54\125\107\x41\x4c" => "\x50\124", "\x51\x41\124\x41\x52" => "\x51\101", "\122\303\x89\x55\116\x49\x4f\x4e" => "\x52\x45", "\122\x4f\115\101\116\x49\x41" => "\x52\117", "\x52\125\x53\123\111\x41" => "\122\125", "\122\127\x41\116\x44\x41" => "\x52\x57", "\x53\101\111\116\124\x20\x42\x41\122\124\110\xc3\211\114\105\x4d\x59" => "\x42\x4c", "\x53\x41\111\x4e\124\40\110\105\x4c\x45\x4e\x41" => "\123\110", "\x53\101\x49\x4e\124\40\x4b\x49\x54\124\123\40\101\116\104\x20\116\105\126\x49\123" => "\x4b\116", "\123\101\x49\116\124\x20\114\125\103\111\101" => "\x4c\x43", "\123\101\x49\116\124\40\115\x41\122\124\111\116" => "\x4d\x46", "\123\x41\x49\116\124\x20\x50\111\x45\x52\122\105\x20\x41\116\104\40\115\x49\121\125\x45\114\x4f\116" => "\120\115", "\x53\x41\111\x4e\x54\x20\126\111\116\x43\x45\116\124\x20\x41\x4e\x44\x20\124\110\105\x20\107\x52\x45\116\101\x44\111\x4e\x45\123" => "\126\x43", "\123\x41\115\117\101" => "\127\123", "\x53\101\x4e\x20\x4d\101\x52\x49\116\117" => "\x53\x4d", "\x53\303\203\117\x20\x54\x4f\115\xc3\211\x20\101\116\x44\40\120\122\xc3\x8d\x4e\x43\111\x50\105" => "\123\x54", "\123\x41\125\x44\111\x20\101\122\x41\102\111\101" => "\x53\x41", "\123\105\x4e\105\x47\101\x4c" => "\x53\x4e", "\x53\105\122\x42\111\101" => "\x52\x53", "\123\105\131\103\x48\105\114\114\105\123" => "\123\103", "\x53\111\x45\122\x52\x41\40\114\105\117\116\x45" => "\x53\x4c", "\x53\111\x4e\107\101\x50\x4f\122\105" => "\123\x47", "\123\111\x4e\x54\40\115\101\x41\122\124\x45\x4e" => "\123\130", "\123\114\117\x56\101\113\111\x41" => "\123\113", "\123\114\x4f\126\x45\x4e\x49\x41" => "\x53\x49", "\x53\117\x4c\117\x4d\117\x4e\x20\x49\123\114\101\116\x44\x53" => "\123\102", "\123\117\115\101\114\111\101" => "\123\x4f", "\x53\117\125\124\110\40\x41\106\x52\x49\x43\101" => "\x5a\101", "\123\117\125\124\x48\40\107\x45\x4f\x52\107\x49\101\40\101\x4e\104\x20\124\110\x45\40\x53\x4f\x55\x54\110\40\123\x41\x4e\x44\127\x49\x43\110\40\x49\x53\114\101\116\x44\x53" => "\x47\123", "\x53\117\125\124\x48\40\x4b\x4f\122\x45\101" => "\113\x52", "\x53\x50\101\111\x4e" => "\105\x53", "\123\122\111\40\x4c\101\116\113\101" => "\x4c\x4b", "\x53\125\104\101\x4e" => "\x53\104", "\x53\x55\122\111\116\x41\115\x45" => "\x53\x52", "\x53\x56\x41\x4c\102\x41\x52\x44\40\101\116\x44\x20\x4a\x41\x4e\x20\x4d\x41\x59\x45\x4e" => "\123\112", "\123\127\x41\132\x49\114\101\116\104" => "\123\132", "\x53\127\105\104\x45\x4e" => "\x53\105", "\123\127\x49\x54\x5a\105\122\114\x41\116\104" => "\x43\110", "\123\x59\x52\x49\101" => "\123\131", "\x54\x41\111\127\x41\116\54\40\120\x52\117\126\111\116\103\x45\40\x4f\x46\40\103\x48\111\x4e\x41" => "\124\x57", "\x54\x41\112\x49\x4b\x49\123\x54\x41\116" => "\124\112", "\x54\x41\x4e\132\101\x4e\x49\101" => "\124\132", "\124\110\101\111\114\x41\116\104" => "\124\110", "\x54\x49\x4d\117\x52\55\x4c\x45\x53\124\x45" => "\x54\114", "\x54\x4f\107\x4f" => "\x54\x47", "\x54\117\x4b\105\x4c\101\125" => "\124\113", "\x54\117\x4e\107\101" => "\124\x4f", "\x54\x52\111\x4e\111\x44\x41\x44\x20\101\x4e\104\x20\124\x4f\x42\x41\x47\x4f" => "\x54\x54", "\x54\125\x4e\111\123\111\x41" => "\124\116", "\124\x55\122\113\105\131" => "\124\122", "\124\x55\122\x4b\x4d\x45\116\x49\x53\x54\x41\x4e" => "\124\x4d", "\x54\125\122\113\123\40\x41\116\104\x20\x43\x41\x49\x43\117\123\40\x49\123\114\x41\x4e\x44\x53" => "\124\103", "\124\x55\126\x41\x4c\125" => "\x54\126", "\x55\107\x41\116\x44\101" => "\125\x47", "\x55\x4b\x52\101\111\x4e\x45" => "\x55\x41", "\x55\x4e\111\x54\x45\x44\40\x41\x52\x41\x42\40\x45\115\111\122\101\x54\x45\123" => "\x41\105", "\125\116\111\124\x45\104\x20\113\x49\116\x47\x44\117\115" => "\x47\x42", "\x55\x4e\111\x54\x45\104\40\x53\124\101\x54\105\x53" => "\x55\123", "\125\122\125\x47\x55\101\x59" => "\x55\x59", "\x55\56\123\56\x20\117\125\x54\x4c\x59\111\x4e\x47\40\111\123\114\x41\116\x44\123" => "\x55\115", "\125\56\x53\56\40\126\111\122\x47\x49\116\x20\x49\123\x4c\101\x4e\104\123" => "\x56\111", "\125\132\102\x45\x4b\x49\123\x54\x41\116" => "\125\x5a", "\x56\x41\116\x55\x41\124\x55" => "\126\x55", "\126\x41\124\x49\x43\x41\116\40\x43\111\124\x59" => "\126\x41", "\126\x45\x4e\105\132\125\105\114\x41" => "\x56\105", "\126\111\x45\124\x4e\x41\115" => "\126\116", "\127\x41\x4c\x4c\x49\x53\x20\x41\x4e\104\40\106\x55\124\x55\116\101" => "\127\x46", "\127\x45\123\x54\x45\x52\116\40\123\101\x48\101\122\x41" => "\105\110", "\131\105\x4d\x45\x4e" => "\131\x45", "\132\101\115\102\x49\x41" => "\x5a\115", "\132\111\115\102\101\x42\x57\x45" => "\x5a\127"];
        if (!(strlen($Yk) <= 3)) {
            goto oV;
        }
        if (in_array(strtoupper($Yk), $Ae)) {
            goto Ea;
        }
        $this->messageManager->addErrorMessage("\x49\x6e\x76\141\154\151\x64\x20\x43\x6f\165\x6e\x74\162\171\x20\116\141\155\x65\56");
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl())->sendResponse();
        exit;
        Ea:
        return $Yk;
        oV:
        if (array_key_exists(strtoupper($Yk), $Ae)) {
            goto P1;
        }
        $this->messageManager->addErrorMessage("\111\156\x76\x61\154\x69\144\x20\103\157\165\x6e\x74\162\x79\x20\116\141\x6d\145\56");
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl())->sendResponse();
        exit;
        P1:
        $N9 = $Ae[strtoupper($Yk)];
        return $N9;
    }
    private function updateUserAttributes($Rg, $G5, $Aq, $ko, $BL, $wR, $Q5, $user, $zc, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG)
    {
        $l0 = $user->getId();
        if ($zc) {
            goto i_;
        }
        $Gh = $this->spUtility->getCustomer($l0);
        $this->spUtility->log_debug("\x75\x70\x64\x61\164\x65\125\x73\x65\162\101\164\x74\x72\x69\x62\165\164\x65\x73\x28\x29\72\40\143\x75\x73\x74\157\155\145\162\x3a\40" . $Q5);
        if (!$this->spUtility->isBlank($Aq)) {
            goto Is;
        }
        $Aq = explode("\100", $G5)[0];
        $Aq = preg_replace("\x2f\133\x5e\101\x2d\132\x61\55\x7a\x30\x2d\71\134\55\135\x2f", '', $Aq);
        Is:
        if (!$this->spUtility->isBlank($ko)) {
            goto qA;
        }
        $ko = explode("\x40", $G5)[1];
        $ko = preg_replace("\x2f\133\x5e\x41\55\x5a\x61\x2d\172\60\x2d\71\134\55\x5d\57", '', $ko);
        qA:
        $pI = array("\116\x61\x6d\145\111\104" => $Q5, "\x53\x65\x73\163\151\x6f\x6e\111\x6e\144\145\x78" => $this->sessionIndex);
        $this->spUtility->saveConfig("\x65\170\x74\162\141", $pI, $l0, $zc);
        $eW = $this->group_mapping;
        $Hn = $this->processRoles($wR, $zc, $eW, $BL);
        if (!$this->spUtility->isBlank($Hn)) {
            goto IY;
        }
        $Hn = $this->processDefaultRole($zc, $wR);
        IY:
        $SR = $this->spUtility->getCurrentStore();
        $iM = $this->spUtility->getCurrentWebsiteId();
        $Gh = $this->customerFactory->create();
        $Gh->setWebsiteId($iM)->loadByEmail($G5);
        if (!(!$this->spUtility->isBlank($Hn) && !empty($this->updateFrontendRole) && strcasecmp($this->updateFrontendRole, SPConstants::enable) === 0)) {
            goto BD;
        }
        $Gh->setWebsiteId($iM)->setStore($SR)->setGroupId($Hn)->setForceConfirmed(true)->save();
        BD:
        if (!(!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0)) {
            goto YB;
        }
        $Gh->setWebsiteId($iM)->setStore($SR)->setFirstname($Aq)->setLastname($ko)->setEmail($G5)->setForceConfirmed(true)->save();
        if (!($S6 || $SV || $IS || $al || $y0 || $yP)) {
            goto sR;
        }
        $Kp = $Gh->getDefaultBilling();
        $kI = $this->_addressFactory->create()->load($Kp);
        if (is_null($Kp)) {
            goto XP;
        }
        $kI->setFirstname($Aq);
        $kI->setLastname($ko);
        $this->spUtility->log_debug("\x20\145\x78\151\164\x69\156\x67\x20\165\x70\x64\141\x74\145\x55\163\145\162\101\164\164\162\x69\142\165\164\145\x73\x3a\x20\x70\x68\157\x6e\145");
        if (empty($S6)) {
            goto Vj;
        }
        $kI->setTelephone($S6);
        Vj:
        if (empty($SV)) {
            goto dl;
        }
        $kI->setStreet($SV);
        dl:
        if (empty($IS)) {
            goto Om;
        }
        $kI->setCity($IS);
        Om:
        if (empty($al)) {
            goto JZ;
        }
        $Ma = $this->collectionFactory->create()->addRegionNameFilter($al)->getFirstItem()->toArray();
        if (empty($Ma["\x72\x65\x67\151\157\156\137\x69\144"])) {
            goto xD;
        }
        $kI->setRegionId($Ma["\162\x65\x67\151\x6f\156\x5f\x69\x64"]);
        xD:
        JZ:
        if (empty($y0)) {
            goto gv;
        }
        $kI->setCountryId($y0);
        gv:
        if (empty($yP)) {
            goto oE;
        }
        $kI->setPostcode($yP);
        oE:
        $kI->save();
        goto BN;
        XP:
        $UW = $this->dataAddressFactory->create();
        $UW->setFirstname($Aq);
        $UW->setLastname($ko);
        if (empty($S6)) {
            goto UQ;
        }
        $UW->setTelephone($S6);
        UQ:
        if (empty($SV)) {
            goto rO;
        }
        $UW->setStreet($SV);
        rO:
        if (empty($IS)) {
            goto hZ;
        }
        $UW->setCity($IS);
        hZ:
        if (empty($y0)) {
            goto bQ;
        }
        $UW->setCountryId($y0);
        bQ:
        if (empty($al)) {
            goto R5;
        }
        $Ma = $this->collectionFactory->create()->addRegionNameFilter($al)->getFirstItem()->toArray();
        if (empty($Ma["\162\x65\147\151\x6f\x6e\137\x69\144"])) {
            goto V5;
        }
        $UW->setRegionId($Ma["\x72\145\x67\151\157\156\x5f\x69\x64"]);
        V5:
        R5:
        if (empty($yP)) {
            goto Rm;
        }
        $UW->setPostcode($yP);
        Rm:
        $UW->setIsDefaultBilling("\x31");
        $UW->setSaveInAddressBook("\61");
        $UW->setCustomerId($Gh->getId());
        try {
            $UW->save();
            $Gh = $UW->getCustomer();
        } catch (\Exception $ax) {
            $this->spUtility->log_debug("\101\156\x20\145\x72\162\157\162\40\157\x63\143\165\x72\162\145\144\x20\x77\150\151\x6c\145\x20\x74\x72\x79\151\156\x67\x20\x74\x6f\x20\x73\x65\x74\x20\141\x64\144\x72\145\163\x73\x3a\x20{$ax->getMessage()}");
        }
        BN:
        sR:
        if (!($qR || $RN || $OO || $cZ || $CG || $Ju)) {
            goto SZ;
        }
        $Yh = $Gh->getDefaultShipping();
        $ZV = $this->_addressFactory->create()->load($Yh);
        if (is_null($Yh)) {
            goto wf;
        }
        $ZV->setFirstname($Aq);
        $ZV->setLastname($ko);
        $this->spUtility->log_debug("\40\x65\x78\151\164\x69\x6e\x67\x20\165\160\x64\141\164\x65\x55\163\145\x72\101\164\x74\x72\x69\x62\x75\164\145\163\x3a\40\x70\150\x6f\156\x65");
        if (empty($qR)) {
            goto iB;
        }
        $ZV->setTelephone($qR);
        iB:
        if (empty($RN)) {
            goto Y2;
        }
        $ZV->setStreet($RN);
        Y2:
        if (empty($OO)) {
            goto nt;
        }
        $ZV->setCity($OO);
        nt:
        if (empty($cZ)) {
            goto Pk;
        }
        $Ma = $this->collectionFactory->create()->addRegionNameFilter($cZ)->getFirstItem()->toArray();
        if (empty($Ma["\x72\145\147\151\157\x6e\137\x69\x64"])) {
            goto Mp;
        }
        $ZV->setRegionId($Ma["\162\145\147\151\157\156\137\151\144"]);
        Mp:
        Pk:
        if (empty($CG)) {
            goto Lq;
        }
        $ZV->setCountryId($CG);
        Lq:
        if (empty($Ju)) {
            goto aB;
        }
        $ZV->setPostcode($Ju);
        aB:
        $ZV->save();
        goto Ie;
        wf:
        $UW = $this->dataAddressFactory->create();
        $UW->setFirstname($Aq);
        $UW->setLastname($ko);
        if (empty($qR)) {
            goto yZ;
        }
        $UW->setTelephone($qR);
        yZ:
        if (empty($RN)) {
            goto lZ;
        }
        $UW->setStreet($RN);
        lZ:
        if (empty($OO)) {
            goto SY;
        }
        $UW->setCity($OO);
        SY:
        if (empty($cZ)) {
            goto Rx;
        }
        $Ma = $this->collectionFactory->create()->addRegionNameFilter($cZ)->getFirstItem()->toArray();
        if (empty($Ma["\162\145\x67\x69\x6f\x6e\x5f\x69\144"])) {
            goto GO;
        }
        $UW->setRegionId($Ma["\162\x65\147\x69\157\x6e\x5f\x69\144"]);
        GO:
        Rx:
        if (empty($CG)) {
            goto uK;
        }
        $UW->setCountryId($CG);
        uK:
        if (empty($Ju)) {
            goto bz;
        }
        $UW->setPostcode($Ju);
        bz:
        $UW->setIsDefaultShipping("\x31");
        $UW->setSaveInAddressBook("\61");
        $UW->setCustomerId($Gh->getId());
        try {
            $UW->save();
            $Gh = $UW->getCustomer();
        } catch (\Exception $ax) {
            $this->spUtility->log_debug("\101\x6e\x20\x65\162\x72\x6f\162\40\x6f\x63\x63\165\x72\162\x65\x64\40\x77\150\151\154\145\40\x74\x72\x79\151\156\147\40\x74\157\40\x73\145\x74\40\x61\144\144\162\145\x73\163\x3a\x20{$ax->getMessage()}");
        }
        Ie:
        SZ:
        YB:
        goto xq;
        i_:
        $Hn = null;
        $Xo = $this->spUtility->getAdminUserById($l0);
        $this->spUtility->log_debug("\x75\x70\x64\x61\x74\x65\125\163\x65\x72\101\164\164\162\151\x62\x75\164\x65\x73\x28\51\72\40\141\x64\x6d\151\x6e\72\40");
        if (!(!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0)) {
            goto xS;
        }
        if (!$this->spUtility->isBlank($Aq)) {
            goto RY;
        }
        $Aq = explode("\x40", $G5)[0];
        $Aq = preg_replace("\57\x5b\x5e\101\x2d\x5a\x61\55\172\x30\x2d\x39\134\55\135\57", '', $Aq);
        RY:
        if (!$this->spUtility->isBlank($ko)) {
            goto vN;
        }
        $ko = explode("\100", $G5)[1];
        $ko = preg_replace("\x2f\x5b\x5e\101\55\x5a\x61\55\172\60\55\x39\x5c\x2d\135\57", '', $ko);
        vN:
        if ($this->spUtility->isBlank($Aq)) {
            goto lm;
        }
        $Xo->setFirstname($Aq);
        lm:
        if ($this->spUtility->isBlank($ko)) {
            goto o3;
        }
        $Xo->setLastname($ko);
        o3:
        if ($this->spUtility->isBlank($Aq)) {
            goto KB;
        }
        $this->spUtility->saveConfig(SPConstants::DB_FIRSTNAME, $Aq, $l0, $zc);
        KB:
        if ($this->spUtility->isBlank($ko)) {
            goto Bw;
        }
        $this->spUtility->saveConfig(SPConstants::DB_LASTNAME, $ko, $l0, $zc);
        Bw:
        xS:
        $m9 = $this->role_mapping;
        $eW = $this->group_mapping;
        if ($this->spUtility->isBlank($m9)) {
            goto NJ;
        }
        $Hn = $this->processRoles($wR, $zc, $m9, $BL);
        NJ:
        if (!$this->spUtility->isBlank($Hn)) {
            goto xm;
        }
        $Hn = $this->processDefaultRole($zc, $wR);
        xm:
        $Hn = $this->spUtility->isBlank($Hn) ? 1 : $Hn;
        if (!(!$this->spUtility->isBlank($Hn) && !empty($this->updateRole) && strcasecmp($this->updateRole, SPConstants::enable) === 0)) {
            goto mO;
        }
        $Xo->setRoleId($Hn);
        mO:
        if (!isset($Rg)) {
            goto hk;
        }
        $Xo->setUsername($Rg);
        hk:
        $this->spUtility->log_debug("\40\x65\x78\151\x74\x69\x6e\x67\40\165\160\x64\x61\164\x65\125\163\x65\162\101\164\x74\162\x69\142\x75\164\x65\x73\x3a\x20\x61\144\155\x69\156\125\x73\x65\x72\40\x45\155\x61\x69\154", $Xo->getEmail());
        $Xo->save();
        xq:
        if (!(!empty($Hn) && !empty($this->dontAllowUnlistedUserRole) && $this->dontAllowUnlistedUserRole == SPConstants::enable)) {
            goto OX;
        }
        return;
        OX:
        return $user;
    }
    private function redirectToBackendAndLogin($l0, $SK, $qY)
    {
        $this->spUtility->setAdminSessionData("\141\144\x6d\x69\156\137\160\x6f\x73\164\137\x6c\157\x67\157\x75\x74", 1);
        $m7 = "\x61\144\x6d\x69\156\x68\x74\155\154";
        $cv = $l0;
        $this->_request->setPathInfo("\x2f\x61\144\x6d\x69\156");
        $this->spUtility->log_debug("\162\x65\x64\151\162\x65\143\164\x54\157\x42\141\x63\153\145\x6e\x64\101\x6e\x64\114\157\147\151\156\72\40\165\163\x65\162\72\x20" . $cv);
        try {
            $this->_state->setAreaCode($m7);
        } catch (LocalizedException $ax) {
        }
        $this->_objectManager->configure($this->_configLoader->load($m7));
        $user = $this->_objectManager->get("\115\141\147\x65\156\x74\157\x5c\x55\163\x65\162\134\115\157\x64\x65\x6c\134\125\x73\145\x72")->loadByUsername($cv);
        $QF = $this->_objectManager->get("\x4d\x61\x67\x65\156\164\x6f\134\x42\x61\x63\x6b\145\x6e\144\x5c\115\157\144\145\154\x5c\x41\x75\x74\150\x5c\123\145\163\163\x69\x6f\156");
        $QF->setUser($user);
        $QF->processLogin();
        $this->spUtility->reinitconfig();
        if (!$QF->isLoggedIn()) {
            goto yi;
        }
        $this->spUtility->log_debug("\162\x65\144\x69\162\x65\143\x74\x54\x6f\x42\141\143\153\x65\156\144\x41\x6e\144\114\x6f\147\x69\156\72\x20\x69\x73\114\x6f\147\x67\x65\x64\x49\156\x3a\x20\x74\162\x75\x65");
        $Vj = $this->_objectManager->get("\x4d\141\x67\145\156\164\157\x5c\106\x72\x61\x6d\x65\167\157\162\153\134\x53\x74\144\x6c\151\x62\x5c\103\x6f\157\x6b\x69\x65\x4d\141\x6e\x61\x67\145\162\111\156\x74\145\x72\x66\141\143\145");
        $S_ = $QF->getSessionId();
        if (!$S_) {
            goto LI;
        }
        $this->spUtility->log_debug("\x72\145\144\151\162\x65\143\164\x54\157\x42\x61\x63\x6b\145\x6e\x64\x41\x6e\x64\x4c\157\x67\151\x6e\x3a\x20\143\x6f\x6f\x6b\x69\x65\x56\x61\x6c\165\x65\x3a\x20\x74\x72\165\145");
        $I4 = $this->_objectManager->get("\115\x61\x67\145\x6e\164\157\134\102\141\143\153\x65\156\x64\x5c\x4d\x6f\144\x65\154\134\123\x65\163\x73\151\157\156\134\101\144\155\151\156\103\x6f\156\146\151\x67");
        $D4 = str_replace("\x61\165\x74\x6f\x6c\157\x67\151\x6e\x2e\x70\x68\160", "\x69\x6e\x64\x65\x78\56\160\150\160", $I4->getCookiePath());
        $dp = $this->_objectManager->get("\115\x61\x67\x65\156\x74\x6f\x5c\106\x72\141\155\145\167\157\162\x6b\x5c\123\x74\x64\x6c\151\142\134\x43\x6f\x6f\x6b\x69\145\x5c\x43\157\157\153\x69\x65\x4d\x65\x74\141\144\x61\x74\141\106\x61\143\164\x6f\x72\171")->createPublicCookieMetadata()->setDuration(3600)->setPath($D4)->setDomain($I4->getCookieDomain())->setSecure($I4->getCookieSecure())->setHttpOnly($I4->getCookieHttpOnly());
        $Vj->setPublicCookie($I4->getName(), $S_, $dp);
        if (!class_exists("\x4d\x61\x67\145\156\x74\x6f\x5c\x53\x65\x63\165\x72\x69\164\x79\134\x4d\157\x64\x65\x6c\134\x41\x64\155\x69\x6e\123\145\163\x73\151\x6f\x6e\x73\x4d\x61\x6e\141\x67\x65\162")) {
            goto Kq;
        }
        $this->spUtility->log_debug("\x72\x65\144\151\162\x65\143\164\124\x6f\102\141\143\x6b\x65\x6e\144\x41\x6e\x64\x4c\157\147\x69\156\x3a\x20\143\154\141\x73\163\x20\145\x78\151\163\164\x20\101\144\x6d\151\156\x53\145\163\163\151\x6f\x6e\x73\115\141\x6e\141\147\x65\x72\x3a\x20\164\162\165\145");
        $Qy = $this->_objectManager->get("\115\141\147\x65\156\164\157\x5c\x53\145\143\165\x72\x69\164\171\134\x4d\157\x64\145\154\x5c\x41\x64\x6d\x69\156\x53\x65\x73\x73\151\x6f\156\163\x4d\141\x6e\x61\147\145\x72");
        $Qy->processLogin();
        Kq:
        LI:
        $cs = $this->HelperBackend->getHomePageUrl();
        header("\x4c\157\x63\141\164\151\157\156\x3a\x20\x20" . $cs);
        exit;
        yi:
        $this->_objectManager->configure($this->_configLoader->load($m7));
        $user = $this->_objectManager->get("\115\141\x67\145\x6e\164\x6f\x5c\x55\x73\145\x72\134\115\157\x64\145\x6c\x5c\x55\163\145\162")->loadByUsername($cv);
        $QF = $this->_objectManager->get("\115\141\x67\145\x6e\x74\x6f\134\102\141\x63\x6b\145\x6e\x64\134\115\x6f\144\145\x6c\134\x41\165\x74\150\x5c\x53\x65\x73\x73\x69\x6f\x6e");
        $QF->setUser($user);
        $QF->processLogin();
        if (!$QF->isLoggedIn()) {
            goto q0;
        }
        $this->spUtility->log_debug("\x72\145\x64\151\162\x65\x63\164\124\x6f\102\141\143\x6b\145\x6e\144\x41\156\144\x4c\x6f\147\x69\x6e\x3a\40\151\x73\x4c\x6f\147\x67\145\x64\x49\156\x3a\40\x74\x72\165\x65");
        $Vj = $this->_objectManager->get("\115\141\x67\145\156\x74\x6f\x5c\106\162\x61\x6d\x65\167\157\x72\x6b\x5c\x53\164\x64\x6c\151\142\x5c\x43\157\x6f\153\x69\145\115\x61\156\141\147\x65\x72\111\156\164\145\162\146\141\x63\x65");
        $S_ = $QF->getSessionId();
        if (!$S_) {
            goto Y7;
        }
        $this->spUtility->log_debug("\162\x65\x64\151\x72\x65\x63\x74\124\157\102\141\143\x6b\x65\156\x64\x41\x6e\x64\114\x6f\x67\x69\x6e\x3a\40\x63\157\x6f\x6b\x69\145\126\x61\154\165\145\72\x20\x74\162\x75\145");
        $I4 = $this->_objectManager->get("\115\141\x67\x65\x6e\164\x6f\134\x42\141\x63\x6b\145\156\x64\x5c\x4d\x6f\x64\145\x6c\134\x53\145\163\163\151\x6f\156\134\x41\x64\x6d\x69\156\103\157\x6e\146\151\x67");
        $D4 = str_replace("\x61\x75\x74\x6f\x6c\157\x67\151\156\56\x70\150\x70", "\x69\156\144\x65\170\x2e\x70\150\160", $I4->getCookiePath());
        $dp = $this->_objectManager->get("\115\x61\x67\145\156\x74\x6f\134\x46\x72\141\155\145\167\x6f\x72\x6b\x5c\123\164\x64\154\x69\x62\134\x43\x6f\157\x6b\151\145\134\103\157\157\x6b\x69\x65\115\145\x74\141\144\x61\x74\x61\106\x61\x63\164\x6f\x72\171")->createPublicCookieMetadata()->setDuration(3600)->setPath($D4)->setDomain($I4->getCookieDomain())->setSecure($I4->getCookieSecure())->setHttpOnly($I4->getCookieHttpOnly());
        $Vj->setPublicCookie($I4->getName(), $S_, $dp);
        Y7:
        $JY = $this->spUtility->getAdminUrl("\141\x64\155\151\156\57\144\141\163\x68\x62\157\141\x72\x64\57\151\x6e\x64\145\x78");
        $this->spUtility->log_debug("\x72\145\x64\x69\162\145\143\164\x54\157\x42\141\143\x6b\145\x6e\144\x41\x6e\x64\114\157\147\151\156\x3a\x20\146\x69\x6e\141\154\125\162\x6c\72\x20" . $JY);
        $this->messageManager->addSuccess("\x59\157\x75\x20\x61\162\145\40\x6c\157\x67\x67\x65\x64\x20\151\x6e\40\163\165\x63\143\x65\163\163\x66\165\x6c\154\171\x2e");
        $this->responseFactory->create()->setRedirect($JY)->sendResponse();
        q0:
    }
    private function generateEmail($Rg)
    {
        $Oi = $this->spUtility->getBaseUrl();
        $Oi = substr($Oi, strpos($Oi, "\x2f\57"), strlen($Oi) - 1);
        return $Rg . "\x40" . $Oi;
    }
    private function processRoles($wR, $zc, $wK, $BL)
    {
        if (!$zc) {
            goto O4;
        }
        $wR = $this->defaultRole;
        O4:
        $this->spUtility->log_debug("\144\x65\x66\141\165\154\x74\40\x52\157\154\x65\72\x20", $wR);
        if (!$wK) {
            goto HN;
        }
        $wK = json_decode($wK);
        HN:
        $X3 = null;
        if (!(empty($BL) || empty($wK))) {
            goto xF;
        }
        return null;
        xF:
        foreach ($wK as $Yg => $N6) {
            $PU = explode("\x3b", $N6);
            foreach ($PU as $r7) {
                if (empty($r7)) {
                    goto Gj;
                }
                foreach ($BL as $oV) {
                    if (!($oV == $r7)) {
                        goto x_;
                    }
                    $X3 = $Yg;
                    return $X3;
                    x_:
                    S5:
                }
                Jp:
                Gj:
                SI:
            }
            Aj:
            te:
        }
        dH:
        return $this->getGroupIdByName($wR);
    }
    private function processDefaultRole($zc, $wR)
    {
        if (!$this->spUtility->isBlank($wR)) {
            goto Kw;
        }
        $this->spUtility->log_debug("\160\x72\157\143\x65\x73\x73\104\145\146\x61\x75\x6c\x74\x52\x6f\x6c\x65\50\x29\72\x20\x64\x65\x66\141\165\x6c\164\x52\157\x6c\145\x20\x69\x73\x20\102\x6c\x61\156\153\56\40\x53\x65\x74\x74\151\x6e\147\x20\120\162\x65\104\x65\146\151\x6e\145\144\122\x6f\x6c\x65\163");
        $wR = $zc ? SPConstants::DEFAULT_ROLE : SPConstants::DEFAULT_GROUP;
        Kw:
        $Rb = $zc ? $this->getRoleIdByName($wR) : $this->getGroupIdByName($wR);
        $this->spUtility->log_debug("\x70\162\157\143\x65\163\x73\x44\x65\146\141\165\x6c\164\x52\157\x6c\x65\50\51\x3a\x20\x72\145\x74\x75\162\x6e\151\156\x67\x20\144\x65\x66\x61\165\x6c\x74\x20\x52\x6f\x6c\145\x2f\107\x72\157\x75\157\x20\111\144\x3a\x20" . $Rb . "\40\146\157\162\40\x72\x6f\154\145\x2f\x67\162\157\x75\160\x20" . $wR);
        return $Rb;
    }
    private function getRoleIdByName($Dz)
    {
        $Pj = $this->adminRoleModel->toOptionArray();
        foreach ($Pj as $lZ) {
            if (!($Dz == $lZ["\154\141\x62\x65\x6c"])) {
                goto FW;
            }
            $this->spUtility->log_debug("\x67\x65\x74\122\x6f\x6c\145\111\x64\102\x79\116\x61\x6d\x65\50\x29\x3a\x20\162\145\x74\165\162\156\x69\x6e\147\x20\x72\x6f\x6c\145\x49\144\72\40" . $lZ["\166\x61\x6c\x75\x65"] . "\x20\x66\157\162\x20\162\157\154\145\72\40" . $lZ["\x6c\141\x62\145\x6c"]);
            return $lZ["\166\x61\x6c\165\145"];
            FW:
            Zr:
        }
        WB:
        $this->spUtility->log_debug("\x67\145\x74\x47\162\157\165\x70\111\144\x42\x79\116\x61\155\x65\x28\x29\x3a\40\x53\157\155\x65\x74\150\151\156\x67\x20\x77\x65\156\x74\x20\167\162\x6f\156\x67\56\40\x44\145\146\x61\165\154\x74\40\x52\x6f\154\x65\x49\x64\40\x63\x61\156\x6e\x6f\164\40\142\145\40\106\157\x75\x6e\x64\x3a\x20");
    }
    private function getGroupIdByName($BL)
    {
        $PU = $this->userGroupModel->toOptionArray();
        foreach ($PU as $oV) {
            if (!($BL == $oV["\154\x61\142\145\x6c"])) {
                goto pG;
            }
            $this->spUtility->log_debug("\x67\x65\164\107\x72\x6f\165\x70\111\x64\x42\x79\116\141\155\x65\50\51\x3a\40\x72\x65\164\x75\x72\x6e\x69\156\147\40\147\x72\157\x75\160\x49\x64\x3a\x20" . $oV["\x76\x61\x6c\x75\x65"] . "\40\146\157\x72\x20\x72\157\154\145\72\40" . $oV["\154\141\x62\145\154"]);
            return $oV["\166\141\154\x75\145"];
            pG:
            oB:
        }
        bj:
        $this->spUtility->log_debug("\147\145\164\107\162\157\165\160\111\144\102\171\x4e\x61\x6d\x65\x28\51\x3a\x20\x53\157\x6d\x65\x74\x68\x69\x6e\x67\x20\x77\x65\x6e\x74\x20\167\162\x6f\156\147\56\x20\x44\x65\x66\141\165\154\164\x20\x47\x72\157\x75\x70\111\144\40\x63\x61\x6e\156\x6f\x74\x20\x62\x65\x20\x46\x6f\165\156\144\x3a\40");
    }
    private function createNewUser($G5, $Aq, $ko, $Rg, $BL, $wR, $Q5, $user, $zc, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG)
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto d_;
        }
        $this->spUtility->flushCache();
        $this->spUtility->reinitConfig();
        if ($this->spUtility->getRemainingUsersCount()) {
            goto m4;
        }
        $xG = (int) AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::MAGENTO_COUNTER), SPConstants::DEFAULT_TOKEN_VALUE);
        $this->spUtility->setStoreConfig(SPConstants::MAGENTO_COUNTER, AESEncryption::encrypt_data($xG + 1, SPConstants::DEFAULT_TOKEN_VALUE));
        $this->spUtility->flushCache();
        $this->spUtility->reinitConfig();
        goto hY;
        m4:
        $this->spUtility->log_debug("\131\x6f\x75\x72\x20\101\x75\x74\157\x20\103\162\x65\x61\164\x65\x20\x55\163\x65\162\x20\114\151\155\x69\x74\x20\x66\x6f\x72\x20\164\x68\145\40\x74\162\151\141\x6c\40\115\151\x6e\151\157\x72\141\156\147\x65\40\x4d\141\x67\x65\x6e\164\x6f\x20\x53\101\115\114\40\x53\x50\x20\160\x6c\165\x67\x69\156\40\151\x73\x20\x65\x78\143\145\x65\x64\x65\144\56\x20\120\154\x65\x61\x73\145\40\x55\160\x67\162\141\x64\145\x20\x74\157\40\141\156\x79\40\157\x66\40\x74\150\145\x20\x50\x72\145\x6d\151\x75\x6d\40\x50\154\x61\156\x20\x74\x6f\x20\143\x6f\156\x74\x69\x6e\165\x65\x20\164\x68\145\40\163\145\x72\166\151\143\x65\56");
        print_r("\x59\157\x75\x72\x20\101\x75\x74\157\x20\x43\x72\145\141\x74\145\40\x55\x73\x65\162\40\x4c\151\x6d\151\164\40\146\x6f\x72\40\164\150\x65\40\164\x72\151\x61\154\x20\115\151\x6e\x69\157\x72\141\x6e\x67\x65\40\x4d\x61\x67\x65\x6e\164\157\40\x53\101\115\114\40\123\x50\40\160\154\x75\x67\x69\156\x20\151\x73\40\x65\170\143\145\145\x64\x65\x64\x2e\40\120\154\x65\141\163\x65\x20\x55\160\147\x72\x61\x64\x65\x20\x74\157\x20\x61\156\171\x20\x6f\146\40\x74\x68\x65\40\x50\162\145\155\151\165\x6d\x20\120\x6c\x61\x6e\x20\164\x6f\x20\143\x6f\156\164\151\156\x75\x65\x20\x74\x68\x65\x20\x73\x65\162\x76\151\x63\x65\56");
        exit;
        hY:
        d_:
        $this->spUtility->log_debug("\143\162\145\141\x74\145\x4e\145\x77\125\x73\145\162\x28\51\x3a\x20\145\x6d\141\151\x6c\x3a\40" . $G5);
        $this->spUtility->log_debug("\143\x72\145\x61\164\x65\116\145\x77\125\163\145\162\x28\51\72\x20\141\144\155\x69\156\72\x20" . json_encode($zc));
        $GL = $this->generatePassword(16);
        $fx = !$this->spUtility->isBlank($G5) ? $G5 : $this->findUserEmail();
        if (!empty($fx)) {
            goto fS;
        }
        if ($zc) {
            goto Mc;
        }
        $this->spUtility->log_debug("\124\150\x69\x73\x20\103\165\163\x74\x6f\x6d\145\162\40\x65\x6d\x61\151\154\40\156\x6f\164\x20\146\x6f\165\156\x64\56");
        $this->messageManager->addErrorMessage(__("\x45\x6d\x61\x69\154\x20\x6e\x6f\x74\40\146\157\165\x6e\x64\56\120\x6c\x65\x61\x73\x65\40\143\157\x6e\x74\x61\x63\x74\40\x79\157\165\162\x20\x41\x64\x6d\x69\156\151\163\x74\162\141\164\x6f\x72\x2e"));
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl() . "\x2f\x63\165\163\x74\157\155\145\x72\x2f\141\143\143\x6f\165\x6e\164")->sendResponse();
        exit;
        goto QT;
        Mc:
        $this->spUtility->log_debug("\x54\x68\x69\163\40\141\x64\155\151\156\x20\145\x6d\x61\151\154\x20\x6e\x6f\x74\x20\146\x6f\x75\156\x64\56");
        echo "\x54\150\x69\163\x20\141\x64\x6d\151\156\x20\145\155\x61\151\x6c\40\x6e\157\164\x20\x66\x6f\x75\x6e\x64\56\x2e\x50\x6c\x65\x61\x73\145\40\x63\157\156\164\x61\143\x74\x20\171\157\x75\x72\x20\x41\x64\x6d\x69\156\x69\x73\164\162\141\164\x6f\x72\x2e";
        exit;
        QT:
        fS:
        if (!$this->spUtility->isBlank($Aq)) {
            goto dC;
        }
        $Aq = explode("\100", $fx)[0];
        $Aq = preg_replace("\x2f\x5b\136\101\x2d\x5a\x61\55\x7a\x30\x2d\71\134\55\x5d\x2f", '', $Aq);
        $this->spUtility->log_debug("\x63\x72\145\x61\164\145\116\145\x77\125\x73\x65\x72\50\x29\72\x20\103\150\141\156\147\x65\x64\x20\x66\151\162\163\x74\x4e\141\155\x65\72\40" . $Aq);
        dC:
        if (!$this->spUtility->isBlank($ko)) {
            goto ew;
        }
        $ko = explode("\100", $fx)[1];
        $ko = preg_replace("\57\x5b\136\x41\x2d\x5a\x61\55\172\x30\55\x39\x5c\55\x5d\57", '', $ko);
        $this->spUtility->log_debug("\143\x72\x65\141\164\145\116\x65\x77\125\x73\145\x72\x28\x29\72\40\103\150\141\x6e\147\x65\144\40\154\x61\x73\x74\116\x61\155\x65\72\40" . $ko);
        ew:
        if ($zc) {
            goto cf;
        }
        $wK = $this->group_mapping;
        $this->spUtility->log_debug("\143\162\145\x61\x74\145\x4e\145\167\x55\x73\x65\162\50\51\x20\72\x20\147\x72\x6f\165\160\x73\115\141\160\x70\145\144\x20" . $wK);
        goto NB;
        cf:
        $wK = $this->role_mapping;
        $this->spUtility->log_debug("\x63\162\145\141\x74\x65\x4e\145\167\125\163\x65\162\x28\x29\40\72\x20\x72\x6f\154\145\163\x4d\x61\160\160\x65\x64\40" . $wK);
        NB:
        if (!(!empty($this->dontCreateUserIfRoleNotMapped) && strcasecmp($this->dontCreateUserIfRoleNotMapped, SPConstants::enable) === 0)) {
            goto i1;
        }
        if ($this->isRoleMappingConfiguredForUser($wK, $BL)) {
            goto Ov;
        }
        if (!$zc) {
            goto Jz;
        }
        echo "\x57\x65\x20\x63\141\156\156\x6f\164\40\x6c\157\x67\40\171\x6f\x75\40\151\x6e\x2e\x20\120\x6c\x65\141\163\145\40\x6d\x61\x70\x20\x74\150\x65\40\162\157\x6c\145\163";
        exit;
        Jz:
        $this->messageManager->addErrorMessage("\x57\x65\40\x63\141\x6e\x6e\157\x74\x20\x6c\157\147\40\x79\157\165\x20\151\156\x2e\x20\x50\154\x65\141\x73\x65\x20\x6d\x61\160\x20\x74\x68\145\40\162\x6f\154\x65\x73");
        if ($this->auto_redirect) {
            goto nr;
        }
        $JN = $this->storeManager->getStore()->getBaseUrl();
        $this->responseFactory->create()->setRedirect($JN . "\143\165\x73\x74\157\x6d\x65\162\x2f\141\143\143\x6f\165\x6e\x74")->sendResponse();
        exit;
        goto ei;
        nr:
        $As = $this->getRequest()->getParams();
        $lP = $this->storeManager->getStore()->getBaseUrl();
        if (empty($As["\122\145\x6c\141\171\123\164\141\x74\145"])) {
            goto zZ;
        }
        $lP = $As["\x52\145\x6c\141\171\123\164\141\x74\x65"];
        zZ:
        $this->responseFactory->create()->setRedirect($lP)->sendResponse();
        exit;
        ei:
        Ov:
        i1:
        $Hn = $this->processRoles($wR, $zc, $wK, $BL);
        if (!$this->spUtility->isBlank($Hn)) {
            goto jc;
        }
        $Hn = $this->processDefaultRole($zc, $wR);
        jc:
        $Rg = !$this->spUtility->isBlank($Rg) ? $Rg : $Aq;
        $user = $zc ? $this->createAdminUser($Rg, $Aq, $ko, $fx, $GL, $Hn) : $this->createCustomer($Rg, $Aq, $ko, $fx, $GL, $Hn, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG);
        $l0 = $user->getId();
        $this->spUtility->log_debug("\143\162\145\x61\164\145\x4e\145\167\x55\163\x65\162\x28\x29\x3a\x20\x75\163\145\162\x20\x63\x72\x65\141\x74\145\144\40\167\151\164\150\40\151\144\x3a\40" . $l0);
        $pI = array("\x4e\x61\x6d\145\x49\104" => $Q5, "\x53\145\x73\x73\151\x6f\156\x49\156\x64\x65\x78" => $this->sessionIndex);
        $this->spUtility->saveConfig("\145\x78\x74\162\141", $pI, $l0, $zc);
        $F2 = $this->spUtility->getCustomerStoreConfig("\145\170\164\162\x61", $l0);
        return $user;
    }
    private function findUserEmail()
    {
        $this->spUtility->log_debug("\x70\162\157\x63\x65\x73\x73\122\145\163\160\x6f\x6e\x73\145\101\143\164\151\157\x6e\72\x20\x66\x69\156\144\125\x73\x65\162\x45\x6d\141\x69\154");
        if (!$this->attrs) {
            goto ar;
        }
        foreach ($this->attrs as $Yk) {
            if (!filter_var($Yk[0], FILTER_VALIDATE_EMAIL)) {
                goto JF;
            }
            return $Yk[0];
            JF:
            KS:
        }
        G3:
        return '';
        ar:
    }
    private function isRoleMappingConfiguredForUser($wK, $BL)
    {
        $this->spUtility->log_debug("\151\163\122\x6f\x6c\145\x4d\141\160\x70\x69\156\x67\x43\157\x6e\x66\151\147\x75\x72\145\x64\x46\x6f\x72\125\163\x65\162\72\40\162\157\154\145\x5f\155\141\x70\160\x69\x6e\147\x3a\40", $wK);
        $this->spUtility->log_debug("\x69\x73\x52\x6f\154\x65\115\x61\160\160\x69\156\147\x43\x6f\x6e\146\x69\147\x75\x72\x65\144\106\157\162\125\x73\x65\162\x3a\x20\147\x72\x6f\165\x70\x4e\x61\155\145\72\x20", $BL);
        if (!$wK) {
            goto sS;
        }
        $wK = json_decode($wK);
        sS:
        $X3 = null;
        if (!(empty($BL) || empty($wK))) {
            goto EL;
        }
        return null;
        EL:
        foreach ($wK as $Yg => $N6) {
            $PU = explode("\x3b", $N6);
            foreach ($PU as $r7) {
                if (empty($r7)) {
                    goto To;
                }
                foreach ($BL as $oV) {
                    if (!($oV == $r7)) {
                        goto rs;
                    }
                    $X3 = $Yg;
                    return $X3;
                    rs:
                    t_:
                }
                oc:
                To:
                gF:
            }
            nu:
            Rl:
        }
        mN:
        return false;
    }
    private function createCustomer($Rg, $Aq, $ko, $fx, $GL, $Vw, $SV, $al, $yP, $S6, $IS, $y0, $RN, $cZ, $Ju, $qR, $OO, $CG)
    {
        if (!($Vw === "\x30" || $Vw == null)) {
            goto uY;
        }
        $Vw = "\x31";
        uY:
        $this->spUtility->log_debug("\143\x72\145\x61\x74\x65\x43\165\163\x74\157\x6d\x65\162\x28\x29\x3a\40\145\x6d\141\151\154\x3a\40" . $fx);
        $Vw = $this->spUtility->isBlank($Vw) ? 1 : $Vw;
        $this->spUtility->log_debug("\143\162\x65\x61\x74\x65\103\165\163\x74\157\155\145\x72\x28\x29\x3a\x20\x72\157\154\x65\40\141\163\x73\151\147\156\x65\144\72\x20" . $Vw);
        $SR = $this->spUtility->getCurrentStore();
        $iM = $this->spUtility->getCurrentWebsiteId();
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\x69\x64\x70\137\156\141\155\145"] === $rQ)) {
                goto a3;
            }
            $ft = $fR->getData();
            a3:
            QB:
        }
        qj:
        $Gh = $this->customerFactory->create()->setWebsiteId($iM)->setStore($SR)->setFirstname($Aq)->setLastname($ko)->setEmail($fx)->setPassword($GL)->setGroupId($Vw);
        $HY = array_Keys((array) $this->attrs);
        $SJ = json_decode($ft["\x63\165\163\164\157\x6d\x5f\141\x74\164\162\151\x62\x75\x74\145\x73"]);
        $zO = array_values((array) $SJ);
        $Z4 = array_intersect($HY, $zO);
        foreach ($Z4 as $Up) {
            $t1 = array_search($Up, (array) $SJ);
            $Qo = $this->attrs[$Up][0];
            $Gh->setData($t1, $Qo);
            SE:
        }
        HY:
        $Gh->save();
        $HY = array_Keys((array) $this->attrs);
        $this->spUtility->log_debug("\141\x74\x74\x72\x69\x62\165\x74\x65\163\x20\153\x65\x79\x73\x20", $HY);
        $SJ = json_decode((string) $this->spUtility->getStoreConfig(SPConstants::CUSTOM_MAPPED));
        $zO = array_values((array) $SJ);
        $Z4 = array_intersect($HY, $zO);
        $this->spUtility->log_debug("\40\x55\160\x64\x61\164\x65\40\165\163\x65\x72\x27\163\x20\143\x75\163\164\157\155\x20\x61\164\x74\162\x69\142\165\x74\x65\163");
        $l0 = $Gh->getId();
        if (!($S6 || $SV || $IS || $al || $y0 || $yP)) {
            goto Wg;
        }
        $UW = $this->dataAddressFactory->create();
        $UW->setFirstname($Aq);
        $UW->setLastname($ko);
        if (empty($S6)) {
            goto k5;
        }
        $UW->setTelephone($S6);
        k5:
        if (empty($SV)) {
            goto xe;
        }
        $UW->setStreet($SV);
        xe:
        if (empty($IS)) {
            goto Uf;
        }
        $UW->setCity($IS);
        Uf:
        if (empty($al)) {
            goto rQ;
        }
        $Ma = $this->collectionFactory->create()->addRegionNameFilter($al)->getFirstItem()->toArray();
        if (empty($Ma["\x72\145\147\x69\x6f\156\x5f\x69\144"])) {
            goto it;
        }
        $UW->setRegionId($Ma["\162\x65\x67\151\x6f\156\x5f\151\x64"]);
        it:
        rQ:
        if (empty($y0)) {
            goto fc;
        }
        $UW->setCountryId($y0);
        fc:
        if (empty($yP)) {
            goto Ej;
        }
        $UW->setPostcode($yP);
        Ej:
        $UW->setIsDefaultBilling("\x31");
        $UW->setSaveInAddressBook("\61");
        $UW->setCustomerId($Gh->getId());
        try {
            $UW->save();
            $Gh = $UW->getCustomer();
        } catch (\Exception $ax) {
            $this->spUtility->log_debug("\x41\x6e\40\145\162\x72\x6f\x72\40\157\143\143\165\162\162\145\144\x20\167\x68\x69\x6c\145\40\x74\x72\171\151\156\147\40\164\x6f\40\163\x65\x74\x20\141\144\x64\x72\x65\x73\163\72\40{$ax->getMessage()}");
        }
        Wg:
        if (!($qR || $RN || $OO || $cZ || $CG || $Ju)) {
            goto ay;
        }
        $UW = $this->dataAddressFactory->create();
        $UW->setFirstname($Aq);
        $UW->setLastname($ko);
        if (empty($qR)) {
            goto YE;
        }
        $UW->setTelephone($qR);
        YE:
        if (empty($RN)) {
            goto yw;
        }
        $UW->setStreet($RN);
        yw:
        if (empty($OO)) {
            goto z2;
        }
        $UW->setCity($OO);
        z2:
        if (empty($cZ)) {
            goto Me;
        }
        $Ma = $this->collectionFactory->create()->addRegionNameFilter($cZ)->getFirstItem()->toArray();
        if (empty($Ma["\x72\x65\x67\x69\x6f\x6e\137\151\144"])) {
            goto TP;
        }
        $UW->setRegionId($Ma["\x72\145\147\151\x6f\x6e\137\151\144"]);
        TP:
        Me:
        if (empty($CG)) {
            goto NU;
        }
        $UW->setCountryId($CG);
        NU:
        if (empty($Ju)) {
            goto QZ;
        }
        $UW->setPostcode($Ju);
        QZ:
        $UW->setIsDefaultShipping("\61");
        $UW->setSaveInAddressBook("\x31");
        $UW->setCustomerId($Gh->getId());
        try {
            $UW->save();
            $Gh = $UW->getCustomer();
        } catch (\Exception $ax) {
            $this->spUtility->log_debug("\101\156\40\145\x72\x72\x6f\162\x20\157\x63\143\x75\x72\x72\x65\144\x20\x77\150\151\154\145\x20\x74\162\171\151\156\147\x20\x74\157\x20\163\145\x74\x20\141\144\144\162\145\x73\163\72\40{$ax->getMessage()}");
        }
        ay:
        $this->updateCustomAttributes($Gh, $fx);
        return $Gh;
    }
    private function updateCustomAttributes($user, $G5)
    {
        if (!(!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0)) {
            goto Iz;
        }
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x70\x72\x6f\143\145\163\x73\125\163\x65\162\x41\143\x74\151\157\x6e", $rQ);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\144\160\137\156\141\x6d\145"] === $rQ)) {
                goto My;
            }
            $ft = $fR->getData();
            My:
            qV:
        }
        NO:
        $this->spUtility->log_debug("\40\111\x6e\40\x75\x70\144\x61\x74\x65\103\x75\x73\x74\x6f\155\101\x74\x74\x72\151\x62\165\164\145\x20\x66\165\x6e\143\x74\x69\x6f\x6e");
        $l0 = $user->getId();
        $this->spUtility->log_debug("\x75\x73\145\x72\40\x69\x64", $l0);
        $zc = is_a($user, "\x5c\115\141\x67\x65\156\x74\x6f\x5c\125\x73\x65\162\134\115\157\x64\145\x6c\x5c\125\x73\x65\162") ? TRUE : FALSE;
        $HY = array_Keys((array) $this->attrs);
        $this->spUtility->log_debug("\141\x74\164\x72\x69\x62\x75\164\145\163\40\x6b\x65\171\x73\40", $HY);
        $SJ = json_decode($ft["\x63\165\x73\164\x6f\155\137\141\x74\164\x72\151\142\165\x74\145\163"]);
        $zO = array_values((array) $SJ);
        foreach ((array) $SJ as $PT => $Yk) {
            $FZ = $this->eavConfig->getEntityType("\143\x75\163\x74\157\155\145\x72")->getId();
            $e2 = $this->eavConfig->getAttribute($FZ, $PT);
            if ($e2 && $e2->getId()) {
                goto fr;
            }
            $this->spUtility->log_debug("{$PT}\x20\143\x75\163\164\157\x6d\40\141\164\x74\x72\x69\142\165\x74\x65\x20\x6e\157\x74\40\x70\x72\x65\163\x65\156\164");
            fr:
            FJ:
        }
        u9:
        $Z4 = array_intersect($HY, $zO);
        $this->spUtility->log_debug("\40\x55\160\144\x61\x74\x65\x20\165\163\145\162\47\163\40\143\165\x73\x74\157\x6d\x20\141\164\164\162\151\142\x75\164\x65\x73");
        foreach ($Z4 as $Up) {
            $t1 = array_search($Up, (array) $SJ);
            $Qo = $this->attrs[$Up][0];
            $iM = $this->spUtility->getCurrentWebsiteId();
            $this->spUtility->log_debug("\143\x75\163\164\157\155\40\141\x74\x74\162\x69\x62\165\164\x65\40\x3d\x20{$t1}\x20\141\x6e\144\x20\x63\x75\163\x74\157\x6d\40\166\141\154\165\x65\40\x3d\40{$Qo}\40");
            $Gh = $this->customerFactory->create();
            $Gh->setWebsiteId($iM);
            $Gh->loadByEmail($G5);
            $Gh->setData($t1, $Qo);
            $Gh->save();
            rT:
        }
        V_:
        Iz:
        return $user;
    }
    private function createAdminUser($Rg, $Aq, $ko, $fx, $GL, $Vw)
    {
        if (!($Vw === "\x30" || $Vw == null)) {
            goto K8;
        }
        $Vw = "\61";
        K8:
        $this->spUtility->log_debug("\120\x72\157\x63\145\x73\x73\125\163\x65\x72\x41\x63\164\151\157\x6e\40\x3a\x20\143\162\x65\141\164\x65\x41\x64\x6d\x69\x6e\x55\163\x65\162\50\51\72\x20");
        if (!(strlen($Rg) >= 40)) {
            goto tg;
        }
        $Rg = substr($Rg, 0, 40);
        tg:
        $cA = ["\165\x73\x65\x72\156\141\155\x65" => $Rg, "\x66\x69\x72\163\164\x6e\141\155\145" => $Aq, "\154\141\163\x74\156\141\155\x65" => $ko, "\145\x6d\x61\x69\154" => $fx, "\160\x61\163\163\x77\x6f\162\144" => $GL, "\151\156\x74\x65\162\x66\x61\x63\x65\x5f\154\x6f\x63\141\154\x65" => "\x65\x6e\137\x55\x53", "\151\x73\x5f\141\x63\164\151\166\145" => 1];
        $Vw = $this->spUtility->isBlank($Vw) ? 1 : $Vw;
        $this->spUtility->log_debug("\x50\x72\157\x63\x65\x73\x73\125\x73\145\x72\x41\x63\164\151\x6f\156\x20\72\x20\143\x72\145\141\x74\x65\x41\144\155\151\156\125\x73\145\162\x20\x31\72\40");
        $user = $this->userFactory->create();
        $user->setData($cA);
        $user->setRoleId($Vw);
        $user->save();
        return $user;
    }
    private function getAdminUserFromAttributes($G5)
    {
        $Xo = false;
        $this->spUtility->log_debug("\x50\x72\157\143\x65\163\x73\x55\x73\145\x72\x41\143\164\x69\x6f\156\72\40\x67\x65\164\101\x64\155\x69\x6e\125\x73\145\x72\106\x72\157\x6d\101\164\x74\162\x69\x62\165\x74\145\x73\x28\51\x3a\40");
        $Os = $this->adminUserModel->getResource()->getConnection();
        $oo = $Os->select()->from($this->adminUserModel->getResource()->getMainTable())->where("\145\155\x61\151\154\x3d\72\x65\x6d\141\x69\154");
        $XU = ["\145\155\x61\x69\154" => $G5];
        $Xo = $Os->fetchRow($oo, $XU);
        $Xo = is_array($Xo) ? $this->adminUserModel->loadByUsername($Xo["\165\x73\145\x72\156\x61\x6d\x65"]) : $Xo;
        $this->spUtility->log_debug("\147\x65\164\x41\144\155\x69\x6e\125\x73\145\x72\106\x72\x6f\155\101\x74\x74\162\151\x62\165\x74\145\x73\50\51\72\40\x66\145\164\143\150\x65\x64\x20\x61\x64\155\x69\156\x55\x73\x65\x72\x3a\40");
        return $Xo;
    }
    private function getCustomerFromAttributes($G5)
    {
        $iM = $this->spUtility->getCurrentWebsiteId();
        $DR = $this->urlBuilder->getCurrentUrl();
        $this->spUtility->log_debug("\x67\x65\x74\x43\165\163\x74\157\x6d\x65\x72\x46\162\x6f\155\101\164\164\162\151\142\165\x74\145\x73\x28\x29\72\40\143\165\162\162\x65\156\164\x20\120\x61\x67\x65\40\x55\162\154\x3a\40" . $DR);
        try {
            $this->spUtility->log_debug("\147\145\164\103\x75\x73\164\157\155\x65\x72\106\162\x6f\155\101\164\164\162\151\x62\x75\x74\145\163\x28\51\x3a\40\x63\x75\163\x74\x6f\155\145\x72\72\40" . $G5);
            $this->spUtility->log_debug("\x67\145\164\103\x75\163\x74\157\x6d\x65\162\106\162\157\x6d\x41\x74\164\162\x69\142\x75\x74\145\163\x28\x29\x3a\x20\x57\x65\142\x73\151\164\145\x49\144\x3a\40" . $iM);
            $Gh = $this->customerRepository->get($G5, $iM);
            return !is_null($Gh) ? $Gh : FALSE;
        } catch (NoSuchEntityException $sS) {
            $this->spUtility->log_debug("\147\145\164\x43\165\163\x74\157\155\145\x72\106\162\x6f\155\101\164\x74\162\151\142\165\164\145\163\x28\x29\72\40\x63\141\164\x63\150\x20");
            return FALSE;
        }
    }
    public function setAttrs($D_)
    {
        $this->attrs = $D_;
        return $this;
    }
    public function setRelayState($qY)
    {
        $this->relayState = $qY;
        return $this;
    }
    public function setSessionIndex($SK)
    {
        $this->sessionIndex = $SK;
        return $this;
    }
    public function generatePassword($vS = 16)
    {
        $Ot = \Magento\Framework\Math\Random::CHARS_LOWERS . \Magento\Framework\Math\Random::CHARS_UPPERS . \Magento\Framework\Math\Random::CHARS_DIGITS . "\x23\44\x25\46\x2a\x2e\73\72\x28\51\100\41";
        $this->spUtility->log_debug("\143\150\x61\x72\x73\72\x20" . $Ot);
        $Ya = $this->randomUtility->getRandomString($vS, $Ot);
        $this->spUtility->log_debug("\x50\141\163\163\x77\x6f\x72\x64\72\x20" . $Ya);
        return $Ya;
    }
}
