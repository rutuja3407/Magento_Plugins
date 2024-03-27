<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Http;
use Magento\Framework\App\Http\Interceptor;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\State;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Controller\Adminhtml\Attrsettings\Index;
use MiniOrange\SP\Helper\Exception\MissingAttributesException;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
class ProcessUserAction extends BaseAction
{
    protected $messageManager;
    protected $storeManager;
    protected $responseFactory;
    protected $companyIdKey;
    protected $eavConfig;
    protected $auto_redirect;
    protected $urlBuilder;
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
    private $customerRepository;
    private $customerLoginAction;
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
    private $HelperBackend;
    public function __construct(ManagerInterface $b_, Context $gt, SPUtility $fR, Index $ur, \Magento\Customer\Model\ResourceModel\Group\Collection $i6, \Magento\Authorization\Model\ResourceModel\Role\Collection $sP, User $T1, Customer $sX, CustomerRepositoryInterface $oa, StoreManagerInterface $VO, ResponseFactory $Jv, CustomerLoginAction $BH, CustomerFactory $kD, UserFactory $Do, Random $jK, State $p1, ConfigLoaderInterface $cJ, Data $BJ, AddressFactory $hy, AddressFactory $SM, ResultFactory $ps, CollectionFactory $tt, Config $c5, UrlInterface $kT)
    {
        $this->customerModel = $sX;
        $this->index = $ur;
        $this->messageManager = $b_;
        $this->userGroupModel = $i6;
        $this->adminRoleModel = $sP;
        $this->adminUserModel = $T1;
        $this->customerRepository = $oa;
        $this->storeManager = $VO;
        $this->responseFactory = $Jv;
        $this->customerLoginAction = $BH;
        $this->customerFactory = $kD;
        $this->userFactory = $Do;
        $this->randomUtility = $jK;
        $this->_state = $p1;
        $this->HelperBackend = $BJ;
        $this->_configLoader = $cJ;
        $this->dataAddressFactory = $hy;
        $this->_addressFactory = $SM;
        $this->collectionFactory = $tt;
        $this->eavConfig = $c5;
        $this->urlBuilder = $kT;
        $this->b2bUser = false;
        parent::__construct($gt, $fR, $VO, $ps, $Jv);
    }
    public function execute()
    {
        $this->spUtility->log_debug("\x20\x69\x6e\x73\x69\144\145\x20\143\154\x61\x73\x73\40\120\162\157\x63\145\x73\163\125\x73\x65\162\x41\143\164\x69\157\156\40\72\x20\145\170\145\x63\165\x74\x65\x3a\40");
        if (!empty($this->attrs)) {
            goto Fx;
        }
        throw new MissingAttributesException();
        Fx:
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\160\x72\x6f\x63\x65\163\163\125\x73\x65\162\101\x63\x74\151\157\x6e", $rq);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\x64\x70\x5f\x6e\x61\x6d\x65"] === $rq)) {
                goto co;
            }
            $hR = $ub->getData();
            co:
            Ar:
        }
        d3:
        $this->emailAttribute = $hR["\145\x6d\141\x69\x6c\x5f\x61\x74\x74\162\x69\142\165\164\x65"];
        $this->spUtility->log_debug("\145\155\x61\x69\154\x20\141\x74\x74\x72\x69\x62\165\x74\x65", $this->emailAttribute);
        $this->usernameAttribute = $hR["\x75\x73\x65\x72\156\141\x6d\145\x5f\141\164\x74\x72\151\x62\165\x74\x65"];
        $this->firstNameKey = $hR["\x66\151\162\163\x74\156\x61\x6d\x65\x5f\x61\x74\164\x72\x69\x62\x75\164\x65"];
        $this->lastNameKey = $hR["\x6c\141\163\x74\x6e\141\155\145\137\x61\x74\164\162\x69\142\165\164\145"];
        $this->groupNameKey = $hR["\x67\x72\x6f\165\160\137\x61\x74\164\x72\x69\142\x75\x74\x65"];
        $this->companyIdKey = $hR["\x62\62\x62\x5f\x61\x74\x74\162\x69\x62\x75\164\x65"];
        $this->checkIfMatchBy = $hR["\x63\x72\145\141\x74\x65\x5f\155\141\x67\145\156\164\x6f\x5f\x61\x63\143\157\x75\156\x74\137\142\x79"];
        $this->dontCreateUserIfRoleNotMapped = $hR["\144\x6f\137\x6e\157\x74\x5f\141\165\x74\157\x63\x72\145\x61\x74\x65\137\151\x66\137\x72\x6f\154\x65\x73\x5f\x6e\x6f\x74\x5f\x6d\141\x70\x70\145\x64"];
        $this->spUtility->log_debug("\144\157\137\x6e\x6f\x74\137\141\165\x74\x6f\x63\x72\x65\x61\x74\145\x5f\151\x66\x5f\162\x6f\154\145\x73\137\156\x6f\x74\x5f\155\x61\160\x70\x65\144\72\x20", $this->dontCreateUserIfRoleNotMapped);
        $this->billingcountryNameKey = $hR["\142\x69\x6c\154\x69\156\147\137\143\157\x75\156\x74\162\171\137\x61\164\x74\162\x69\142\x75\164\x65"];
        $this->billingcountryNameKey = $this->spUtility->isBlank($this->billingcountryNameKey) ? SPConstants::MAP_COUNTRY_BILLING : $this->billingcountryNameKey;
        $this->spUtility->log_debug("\x62\151\x6c\154\x69\156\x67\143\x6f\x75\x6e\164\162\x79\x4e\141\x6d\x65\113\145\171\x3a\x20");
        $this->billingcityNameKey = $hR["\142\151\154\x6c\x69\x6e\147\x5f\x63\151\x74\x79\137\141\x74\164\162\151\142\x75\164\145"];
        $this->billingcityNameKey = $this->spUtility->isBlank($this->billingcityNameKey) ? SPConstants::MAP_CITY_BILLING : $this->billingcityNameKey;
        $this->spUtility->log_debug("\142\x69\154\x6c\x69\x6e\147\143\151\164\171\x4e\141\155\x65\x4b\x65\171\72\40");
        $this->billingphoneNameKey = $hR["\x62\x69\x6c\x6c\x69\156\147\137\160\150\x6f\156\145\137\x61\x74\164\162\151\x62\x75\x74\145"];
        $this->billingphoneNameKey = $this->spUtility->isBlank($this->billingphoneNameKey) ? SPConstants::MAP_PHONE_BILLING : $this->billingphoneNameKey;
        $this->spUtility->log_debug("\x62\151\x6c\x6c\x69\156\147\x70\150\157\x6e\x65\116\x61\155\145\x4b\x65\171\72\x20", $this->billingphoneNameKey);
        $this->billingstreetAddressNameKey = $hR["\142\151\x6c\154\151\x6e\x67\137\141\x64\144\162\145\163\163\137\x61\164\164\x72\x69\142\165\x74\x65"];
        $this->billingstreetAddressNameKey = $this->spUtility->isBlank($this->billingstreetAddressNameKey) ? SPConstants::MAP_ADDRESS_BILLING : $this->billingstreetAddressNameKey;
        $this->spUtility->log_debug("\142\x69\x6c\154\151\156\147\x73\164\162\145\x65\164\x41\x64\x64\x72\145\x73\163\x4e\x61\x6d\145\113\145\x79\72\x20", $this->billingstreetAddressNameKey);
        $this->billingmapStateNameKey = $hR["\142\151\x6c\x6c\151\156\x67\x5f\x73\164\x61\164\x65\137\x61\x74\164\162\151\142\165\164\x65"];
        $this->billingmapStateNameKey = $this->spUtility->isBlank($this->billingmapStateNameKey) ? SPConstants::MAP_STATE_BILLING : $this->billingmapStateNameKey;
        $this->spUtility->log_debug("\x62\151\154\x6c\x69\156\147\155\141\160\x53\x74\x61\x74\145\x4e\141\155\x65\113\x65\171\x3a\x20", $this->billingmapStateNameKey);
        $this->billingzipCodeNameKey = $hR["\142\x69\154\154\151\x6e\147\137\172\x69\160\137\x61\164\x74\x72\x69\x62\165\164\x65"];
        $this->billingzipCodeNameKey = $this->spUtility->isBlank($this->billingzipCodeNameKey) ? SPConstants::MAP_ZIPCODE_BILLING : $this->billingzipCodeNameKey;
        $this->spUtility->log_debug("\x62\x69\x6c\154\151\x6e\x67\172\151\160\103\157\144\x65\x4e\141\x6d\145\113\145\x79\72\x20", $this->billingzipCodeNameKey);
        $this->shippingcountryNameKey = $hR["\x73\150\x69\160\x70\151\156\x67\x5f\x63\157\x75\156\164\x72\171\x5f\141\164\164\x72\x69\x62\x75\164\145"];
        $this->shippingcountryNameKey = $this->spUtility->isBlank($this->shippingcountryNameKey) ? SPConstants::MAP_COUNTRY_SHIPPING : $this->shippingcountryNameKey;
        $this->spUtility->log_debug("\x73\150\151\160\160\151\x6e\147\x63\x6f\165\x6e\x74\x72\x79\116\x61\155\x65\113\x65\x79\72\40", $this->shippingcountryNameKey);
        $this->shippingcityNameKey = $hR["\163\150\151\160\x70\x69\156\147\x5f\x63\151\164\171\137\141\164\164\162\x69\x62\165\x74\x65"];
        $this->shippingcityNameKey = $this->spUtility->isBlank($this->shippingcityNameKey) ? SPConstants::MAP_CITY_SHIPPING : $this->shippingcityNameKey;
        $this->spUtility->log_debug("\163\x68\x69\160\160\151\156\147\143\151\164\x79\116\141\155\145\x4b\x65\171\72\40", $this->shippingcityNameKey);
        $this->shippingphoneNameKey = $hR["\163\x68\x69\x70\160\x69\x6e\147\x5f\x70\150\x6f\x6e\145\x5f\x61\164\164\x72\151\142\165\164\145"];
        $this->shippingphoneNameKey = $this->spUtility->isBlank($this->shippingphoneNameKey) ? SPConstants::MAP_PHONE_SHIPPING : $this->shippingphoneNameKey;
        $this->spUtility->log_debug("\x73\x68\151\x70\x70\151\156\x67\x70\150\157\x6e\145\116\x61\155\x65\113\145\x79\72\x20", $this->shippingphoneNameKey);
        $this->shippingstreetAddressNameKey = $hR["\x73\150\x69\160\x70\x69\x6e\147\x5f\x61\x64\144\162\145\x73\163\x5f\x61\164\x74\x72\151\x62\165\x74\x65"];
        $this->shippingstreetAddressNameKey = $this->spUtility->isBlank($this->shippingstreetAddressNameKey) ? SPConstants::MAP_ADDRESS_SHIPPING : $this->shippingstreetAddressNameKey;
        $this->spUtility->log_debug("\x73\x68\x69\x70\160\x69\156\x67\x73\x74\x72\145\x65\x74\x41\x64\144\x72\x65\163\163\116\141\155\x65\113\x65\x79\x3a\x20", $this->shippingstreetAddressNameKey);
        $this->shippingmapStateNameKey = $hR["\163\150\x69\x70\x70\x69\156\x67\x5f\x73\x74\x61\164\x65\x5f\x61\x74\x74\162\x69\x62\x75\164\145"];
        $this->shippingmapStateNameKey = $this->spUtility->isBlank($this->shippingmapStateNameKey) ? SPConstants::MAP_STATE_SHIPPING : $this->shippingmapStateNameKey;
        $this->spUtility->log_debug("\x73\150\x69\x70\160\x69\156\147\x6d\141\160\123\x74\x61\164\x65\x4e\141\155\x65\x4b\145\x79\72\40", $this->shippingmapStateNameKey);
        $this->shippingzipCodeNameKey = $hR["\163\x68\x69\x70\160\151\156\147\x5f\x7a\151\x70\137\x61\x74\164\x72\151\x62\x75\x74\x65"];
        $this->shippingzipCodeNameKey = $this->spUtility->isBlank($this->shippingzipCodeNameKey) ? SPConstants::MAP_ZIPCODE_SHIPPING : $this->shippingzipCodeNameKey;
        $this->custom_attributes = $hR["\143\165\x73\x74\157\x6d\137\141\164\164\x72\151\x62\x75\164\x65\163"];
        $this->custom_tablename = $hR["\x63\165\x73\x74\157\x6d\x5f\x74\x61\x62\x6c\x65\x6e\x61\x6d\145"];
        $this->spUtility->log_debug("\x63\165\163\164\x6f\x6d\137\x74\141\142\154\145\156\x61\155\x65\72\x20", $this->custom_tablename);
        $this->defaultRole = $hR["\x64\145\146\141\x75\154\x74\137\x72\x6f\x6c\x65"];
        $this->defaultGroup = $hR["\144\x65\x66\141\x75\x6c\164\137\x67\162\x6f\165\160"];
        $this->spUtility->log_debug("\x64\145\146\x61\x75\154\164\x47\x72\x6f\165\x70\72\x20", $this->defaultGroup);
        $this->role_mapping = $hR["\162\157\x6c\145\x73\137\x6d\141\160\x70\x65\144"];
        $this->group_mapping = $hR["\x67\x72\x6f\x75\x70\x73\x5f\155\141\x70\x70\145\144"];
        $this->spUtility->log_debug("\x67\162\157\x75\160\x5f\x6d\x61\160\160\x69\156\147\72\x20", print_r($this->group_mapping, true));
        $this->updateRole = $hR["\165\x70\x64\x61\164\145\137\x62\141\143\x6b\145\156\x64\x5f\162\157\154\x65\163\137\x6f\x6e\x5f\163\x73\157"];
        $this->updateFrontendRole = $hR["\x75\x70\144\x61\164\x65\137\x66\x72\x6f\156\x74\x65\x6e\x64\137\x67\162\157\x75\x70\x73\x5f\x6f\156\x5f\163\x73\x6f"];
        $this->updateAttribute = $hR["\165\x70\x64\141\164\145\x5f\x61\164\164\162\151\142\165\164\145\163\x5f\x6f\156\x5f\x6c\x6f\x67\151\156"];
        $this->spUtility->log_debug("\165\x70\x64\x61\x74\145\x5f\141\x74\x74\162\x69\142\x75\x74\x65\163\137\157\156\x5f\154\157\147\151\x6e\72\x20", $this->updateAttribute);
        $this->autoCreateAdminUser = $hR["\x61\x75\164\157\137\x63\162\x65\141\x74\x65\137\141\x64\155\x69\x6e\x5f\x75\163\x65\x72\163"];
        $this->autoCreateCustomer = $hR["\141\x75\x74\157\137\143\x72\145\x61\x74\x65\137\143\165\163\164\x6f\155\x65\x72\x73"];
        $this->auto_redirect = $hR["\x61\165\164\x6f\137\x72\145\x64\x69\x72\145\x63\x74\137\x74\157\137\x69\144\160"];
        $Dl = !empty($this->attrs[$this->emailAttribute]) ? $this->attrs[$this->emailAttribute][0] : null;
        $FO = !empty($this->attrs[$this->firstNameKey]) ? $this->attrs[$this->firstNameKey][0] : null;
        $Fo = !empty($this->attrs[$this->lastNameKey]) ? $this->attrs[$this->lastNameKey][0] : null;
        $SD = !empty($this->attrs[$this->usernameAttribute]) ? $this->attrs[$this->usernameAttribute][0] : null;
        $DE = !empty($this->attrs[$this->groupNameKey]) ? $this->attrs[$this->groupNameKey] : null;
        $VC = !empty($this->attrs[$this->groupNameKey]) ? $this->attrs[$this->groupNameKey] : null;
        $KE = !empty($this->attrs[$this->billingcountryNameKey]) ? $this->attrs[$this->billingcountryNameKey][0] : null;
        $eC = !empty($this->attrs[$this->billingcityNameKey]) ? $this->attrs[$this->billingcityNameKey][0] : null;
        $TE = !empty($this->attrs[$this->billingphoneNameKey]) ? $this->attrs[$this->billingphoneNameKey][0] : null;
        $Kk = !empty($this->attrs[$this->billingstreetAddressNameKey]) ? $this->attrs[$this->billingstreetAddressNameKey][0] : null;
        $jF = !empty($this->attrs[$this->billingmapStateNameKey]) ? $this->attrs[$this->billingmapStateNameKey][0] : null;
        $Gb = !empty($this->attrs[$this->billingzipCodeNameKey]) ? $this->attrs[$this->billingzipCodeNameKey][0] : null;
        $v1 = !empty($this->attrs[$this->shippingcountryNameKey]) ? $this->attrs[$this->shippingcountryNameKey][0] : null;
        $iW = !empty($this->attrs[$this->shippingcityNameKey]) ? $this->attrs[$this->shippingcityNameKey][0] : null;
        $VB = !empty($this->attrs[$this->shippingphoneNameKey]) ? $this->attrs[$this->shippingphoneNameKey][0] : null;
        $C0 = !empty($this->attrs[$this->shippingstreetAddressNameKey]) ? $this->attrs[$this->shippingstreetAddressNameKey][0] : null;
        $rB = !empty($this->attrs[$this->shippingmapStateNameKey]) ? $this->attrs[$this->shippingmapStateNameKey][0] : null;
        $Xx = !empty($this->attrs[$this->shippingzipCodeNameKey]) ? $this->attrs[$this->shippingzipCodeNameKey][0] : null;
        $Te = $this->getRequest()->getParams();
        if (!$this->spUtility->isBlank($this->checkIfMatchBy)) {
            goto sj;
        }
        $this->checkIfMatchBy = SPConstants::DEFAULT_MAP_BY;
        sj:
        $this->spUtility->log_debug("\x63\150\x65\143\153\111\x66\x4d\x61\x74\143\150\102\x79\x3a\40", $this->checkIfMatchBy);
        $tj = '';
        if (is_array($DE)) {
            goto ea;
        }
        $tj = $this->spUtility->isBlank(json_decode((string) $DE)) ? $DE : json_decode((string) $DE)[0];
        goto VS;
        ea:
        $tj = $DE;
        VS:
        $this->groupNameKey = $tj;
        $this->spUtility->log_debug("\x46\151\x72\163\164\107\x72\157\165\160\40\x66\162\157\x6d\40\111\x64\120\40\50\151\146\x20\x4d\165\154\x74\151\160\154\145\51\x3a\x20");
        $vA = null;
        $KA = null;
        if (!$KE) {
            goto LW;
        }
        $vA = $this->getCountryCodeBasedOnMapping($KE);
        LW:
        if (!$v1) {
            goto df;
        }
        $KA = $this->getCountryCodeBasedOnMapping($v1);
        df:
        $this->processUserAction($Dl, $FO, $Fo, $SD, $tj, $this->defaultGroup, $this->checkIfMatchBy, $this->attrs["\x4e\x61\x6d\x65\111\104"][0], $Te, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA);
    }
    public function getCountryCodeBasedOnMapping($VP)
    {
        $a5 = ["\101\x46\107\110\101\116\111\123\x54\x41\116" => "\x41\106", "\303\x85\x4c\101\x4e\x44\40\x49\x53\x4c\x41\x4e\x44\123" => "\x41\x58", "\101\114\102\101\116\x49\101" => "\x41\114", "\101\114\x47\x45\122\x49\x41" => "\x44\x5a", "\101\115\x45\x52\111\103\101\116\40\x53\x41\x4d\x4f\101" => "\101\x53", "\x41\116\x44\117\x52\122\x41" => "\101\104", "\x41\116\107\x4f\114\x41" => "\x41\117", "\x41\116\x47\125\111\x4c\x4c\101" => "\x41\x49", "\x41\x4e\x54\x41\122\103\x54\x49\103\x41" => "\101\121", "\101\x4e\x54\x49\107\125\101\x20\101\116\104\40\x42\101\x52\x42\x55\104\101" => "\x41\x47", "\101\122\x47\x45\116\124\x49\116\x41" => "\101\x52", "\101\122\x4d\x45\116\111\x41" => "\x41\x4d", "\101\122\125\x42\101" => "\x41\x57", "\101\x55\123\124\x52\101\114\111\x41" => "\x41\x55", "\x41\x55\123\x54\x52\111\101" => "\101\124", "\x41\x5a\105\122\x42\x41\111\112\101\x4e" => "\101\132", "\102\101\x48\101\x4d\x41\123" => "\102\x53", "\102\101\x48\122\x41\x49\x4e" => "\102\110", "\x42\x41\116\x47\x4c\x41\x44\x45\123\x48" => "\x42\x44", "\x42\x41\x52\x42\x41\104\117\x53" => "\102\102", "\102\x45\x4c\x41\x52\x55\123" => "\102\131", "\x42\105\x4c\x47\x49\x55\x4d" => "\102\x45", "\x42\105\x4c\111\132\105" => "\102\132", "\102\105\x4e\111\x4e" => "\102\x4a", "\102\105\122\x4d\x55\x44\101" => "\102\x4d", "\102\110\125\x54\101\x4e" => "\102\x54", "\102\117\x4c\x49\126\111\x41" => "\102\117", "\x42\x4f\x53\x4e\111\101\40\101\116\x44\x20\110\105\x52\x5a\x45\x47\x4f\x56\111\116\101" => "\x42\x41", "\102\x4f\124\123\127\101\116\101" => "\102\x57", "\102\117\125\126\105\124\x20\111\123\114\x41\116\x44" => "\x42\126", "\102\122\x41\132\x49\x4c" => "\x42\122", "\102\x52\x49\x54\111\x53\110\x20\111\116\x44\111\x41\116\x20\x4f\103\105\101\x4e\x20\124\x45\122\x52\111\124\117\122\x59" => "\111\117", "\x42\122\x49\124\111\123\x48\x20\x56\111\122\107\111\116\x20\x49\123\114\101\116\104\123" => "\126\x47", "\102\122\x55\x4e\105\x49" => "\102\116", "\102\x55\114\107\101\x52\x49\101" => "\x42\x47", "\x42\x55\122\113\x49\x4e\101\40\x46\101\123\x4f" => "\x42\106", "\102\125\x52\x55\116\104\111" => "\102\x49", "\103\x41\x4d\x42\117\104\111\x41" => "\113\x48", "\x43\x41\x4d\x45\x52\x4f\x4f\116" => "\x43\x4d", "\103\x41\x4e\x41\104\101" => "\x43\x41", "\103\x41\120\105\x20\x56\105\x52\x44\105" => "\103\126", "\103\x41\x52\111\102\x42\105\x41\116\40\x4e\105\124\x48\x45\122\x4c\x41\x4e\104\123" => "\102\121", "\x43\x41\131\115\101\x4e\x20\111\123\114\x41\116\x44\123" => "\113\x59", "\103\105\116\124\x52\x41\114\x20\x41\106\x52\x49\x43\x41\x4e\x20\x52\105\x50\125\102\114\x49\x43" => "\103\106", "\103\x48\x41\x44" => "\124\104", "\x43\110\111\114\x45" => "\103\114", "\103\110\111\x4e\x41" => "\x43\116", "\x43\110\x52\x49\123\124\x4d\101\x53\40\111\123\x4c\101\116\x44" => "\103\x58", "\103\x4f\103\x4f\123\x20\133\113\105\x45\114\111\116\x47\135\x20\x49\123\114\x41\x4e\x44\x53" => "\103\x43", "\103\117\114\117\115\x42\x49\x41" => "\x43\117", "\x43\x4f\115\117\122\117\x53" => "\113\115", "\x43\117\116\x47\x4f\x20\x2d\x20\x42\x52\101\132\x5a\101\126\x49\114\x4c\x45" => "\x43\x47", "\103\117\116\x47\117\x20\x2d\40\x4b\111\116\x53\110\x41\x53\101" => "\103\x44", "\103\117\117\113\40\x49\123\x4c\x41\x4e\104\x53" => "\x43\113", "\x43\117\x53\x54\101\x20\122\x49\103\101" => "\103\x52", "\103\303\x94\x54\105\40\x44\xe2\x80\231\111\x56\117\111\x52\105" => "\x43\x49", "\103\x52\117\x41\124\x49\101" => "\x48\x52", "\103\125\102\101" => "\x43\125", "\x43\125\x52\x41\303\207\101\117" => "\103\127", "\x43\x59\120\x52\x55\x53" => "\x43\x59", "\103\x5a\x45\103\x48\x20\x52\x45\x50\125\102\x4c\x49\x43" => "\x43\132", "\104\x45\x4e\x4d\101\x52\113" => "\104\113", "\104\112\111\102\117\125\124\x49" => "\104\x4a", "\104\117\x4d\111\x4e\111\103\101" => "\x44\115", "\x44\117\115\111\x4e\111\x43\101\116\x20\x52\x45\x50\125\x42\x4c\111\103" => "\104\x4f", "\105\103\125\x41\x44\117\x52" => "\105\103", "\105\x47\x59\120\124" => "\x45\107", "\105\x4c\40\123\101\x4c\126\x41\104\x4f\x52" => "\123\126", "\x45\x51\x55\x41\124\x4f\122\111\101\x4c\40\107\x55\111\116\105\101" => "\x47\121", "\x45\122\111\x54\x52\x45\101" => "\x45\x52", "\105\x53\x54\x4f\116\111\x41" => "\105\105", "\105\x54\110\111\117\120\x49\x41" => "\105\124", "\106\101\114\x4b\x4c\101\116\x44\40\111\123\x4c\x41\116\x44\123" => "\x46\113", "\x46\x41\x52\x4f\105\x20\111\123\x4c\101\x4e\104\x53" => "\x46\117", "\106\111\x4a\x49" => "\106\x4a", "\x46\x49\x4e\x4c\x41\116\x44" => "\106\111", "\x46\122\x41\x4e\x43\x45" => "\x46\122", "\x46\x52\105\116\103\110\40\x47\x55\x49\x41\x4e\101" => "\107\106", "\x46\122\x45\116\103\110\x20\120\x4f\114\x59\x4e\105\x53\x49\x41" => "\120\106", "\x46\x52\105\x4e\103\x48\x20\123\117\x55\x54\110\x45\122\116\40\124\x45\122\x52\111\124\117\x52\111\105\123" => "\x54\106", "\107\x41\x42\117\116" => "\107\101", "\x47\101\x4d\x42\111\101" => "\x47\x4d", "\x47\105\117\122\x47\111\x41" => "\107\x45", "\x47\x45\122\x4d\101\x4e\x59" => "\x44\105", "\x47\x48\101\116\101" => "\107\110", "\107\x49\x42\122\x41\114\x54\101\122" => "\x47\111", "\x47\122\105\x45\103\105" => "\x47\x52", "\107\x52\105\105\x4e\x4c\101\116\104" => "\x47\114", "\x47\122\105\116\x41\104\x41" => "\x47\104", "\107\125\x41\104\x45\x4c\117\125\120\x45" => "\107\120", "\107\125\101\x4d" => "\107\125", "\x47\125\x41\124\105\115\x41\114\x41" => "\107\x54", "\107\x55\105\122\x4e\123\x45\x59" => "\x47\107", "\x47\x55\x49\x4e\x45\x41" => "\107\x4e", "\107\125\111\116\105\101\55\x42\111\x53\123\x41\125" => "\107\x57", "\107\x55\x59\101\116\x41" => "\107\131", "\x48\x41\x49\x54\x49" => "\110\x54", "\110\x45\x41\x52\104\x20\x49\x53\x4c\101\116\104\x20\x41\x4e\x44\x20\x4d\x43\104\117\116\101\x4c\x44\x20\x49\x53\114\101\x4e\x44\x53" => "\x48\115", "\x48\x4f\116\104\125\x52\101\x53" => "\x48\116", "\x48\x4f\116\x47\x20\x4b\x4f\116\107\x20\x53\101\122\x20\x43\x48\111\116\101" => "\110\113", "\x48\125\116\107\x41\x52\131" => "\110\125", "\x49\x43\105\x4c\x41\x4e\104" => "\x49\x53", "\111\116\x44\x49\x41" => "\111\116", "\111\116\104\x4f\116\105\x53\111\101" => "\111\104", "\111\122\x41\x4e" => "\x49\122", "\x49\x52\x41\121" => "\111\121", "\x49\122\105\114\101\116\104" => "\x49\105", "\x49\123\114\x45\40\117\x46\40\x4d\x41\116" => "\x49\115", "\111\x53\x52\101\x45\x4c" => "\x49\x4c", "\111\124\x41\x4c\131" => "\x49\124", "\x4a\101\115\x41\111\103\101" => "\x4a\x4d", "\112\101\x50\101\x4e" => "\112\x50", "\x4a\x45\x52\x53\105\131" => "\112\105", "\x4a\117\x52\x44\101\x4e" => "\112\x4f", "\x4b\101\x5a\101\113\110\x53\124\101\x4e" => "\x4b\x5a", "\113\x45\116\131\101" => "\113\105", "\113\x49\x52\x49\x42\101\124\x49" => "\113\111", "\113\125\127\x41\111\124" => "\113\127", "\x4b\131\122\107\x59\x5a\x53\124\101\116" => "\113\107", "\x4c\101\x4f\x53" => "\x4c\x41", "\x4c\101\x54\x56\111\101" => "\x4c\126", "\x4c\x45\102\x41\116\117\x4e" => "\x4c\x42", "\114\105\x53\117\124\x48\x4f" => "\114\x53", "\114\111\x42\x45\x52\111\101" => "\114\122", "\x4c\111\102\x59\101" => "\114\131", "\x4c\111\105\x43\x48\124\x45\x4e\x53\x54\105\111\116" => "\x4c\111", "\114\x49\x54\110\125\101\116\111\x41" => "\x4c\x54", "\x4c\125\130\x45\x4d\x42\x4f\x55\122\107" => "\x4c\125", "\115\101\x43\x41\125\40\x53\101\122\40\x43\x48\111\x4e\x41" => "\115\117", "\115\x41\x43\105\104\x4f\x4e\x49\x41" => "\x4d\113", "\x4d\x41\x44\101\107\101\x53\x43\x41\x52" => "\115\x47", "\x4d\101\x4c\101\127\x49" => "\x4d\127", "\x4d\101\x4c\x41\x59\x53\x49\x41" => "\x4d\131", "\x4d\x41\114\x44\111\126\105\x53" => "\115\x56", "\115\101\114\x49" => "\x4d\x4c", "\x4d\101\114\x54\x41" => "\x4d\x54", "\x4d\101\x52\x53\x48\x41\114\114\x20\111\123\x4c\x41\116\104\123" => "\115\110", "\x4d\x41\x52\x54\111\x4e\x49\x51\x55\105" => "\115\x51", "\115\x41\x55\x52\111\x54\x41\x4e\111\101" => "\115\x52", "\x4d\101\125\x52\111\x54\x49\125\123" => "\x4d\125", "\115\x41\131\x4f\x54\x54\x45" => "\131\124", "\x4d\105\x58\111\x43\x4f" => "\115\x58", "\115\x49\103\x52\117\116\x45\123\111\x41" => "\x46\x4d", "\115\x4f\x4c\104\x4f\126\101" => "\x4d\104", "\x4d\x4f\x4e\x41\x43\117" => "\115\x43", "\x4d\117\116\107\117\114\x49\101" => "\x4d\x4e", "\x4d\117\116\124\105\x4e\x45\107\122\x4f" => "\x4d\x45", "\x4d\x4f\x4e\x54\x53\105\x52\x52\101\124" => "\115\x53", "\115\117\122\117\103\103\117" => "\115\101", "\115\x4f\132\101\x4d\x42\111\x51\x55\x45" => "\x4d\x5a", "\115\131\101\x4e\115\x41\x52\x20\x5b\102\125\x52\x4d\x41\135" => "\115\115", "\x4e\101\115\x49\x42\x49\x41" => "\116\x41", "\x4e\101\125\122\125" => "\x4e\122", "\x4e\105\x50\x41\114" => "\x4e\120", "\116\x45\124\110\105\x52\x4c\x41\x4e\x44\x53" => "\116\114", "\116\105\124\110\x45\x52\x4c\x41\x4e\x44\123\40\101\x4e\x54\x49\114\114\105\123" => "\x41\x4e", "\x4e\x45\x57\x20\103\x41\114\105\104\x4f\x4e\111\101" => "\x4e\103", "\x4e\x45\x57\x20\x5a\x45\101\x4c\101\x4e\104" => "\x4e\x5a", "\116\111\103\101\x52\101\x47\125\101" => "\116\x49", "\x4e\x49\x47\105\x52" => "\116\x45", "\x4e\x49\107\105\122\111\101" => "\116\107", "\x4e\111\125\x45" => "\116\x55", "\116\x4f\x52\106\x4f\x4c\113\x20\111\x53\114\x41\116\x44" => "\x4e\106", "\x4e\x4f\x52\x54\x48\x45\x52\116\x20\x4d\x41\122\111\x41\x4e\x41\40\x49\x53\x4c\x41\116\104\123" => "\115\120", "\116\117\122\x54\x48\40\x4b\x4f\122\105\x41" => "\x4b\x50", "\116\117\x52\x57\x41\131" => "\116\117", "\117\x4d\x41\116" => "\117\115", "\120\x41\x4b\111\x53\x54\x41\x4e" => "\120\x4b", "\120\101\114\101\x55" => "\120\127", "\x50\101\114\105\123\x54\111\x4e\x49\101\x4e\x20\124\x45\x52\122\111\x54\117\122\111\105\x53" => "\x50\123", "\x50\101\116\101\x4d\101" => "\120\101", "\x50\101\x50\x55\x41\40\x4e\x45\x57\x20\107\x55\x49\116\x45\x41" => "\x50\107", "\120\101\122\x41\x47\125\101\x59" => "\120\x59", "\120\x45\x52\x55" => "\120\x45", "\x50\110\111\x4c\111\120\120\111\x4e\105\123" => "\120\x48", "\120\x49\124\x43\101\x49\122\x4e\40\x49\x53\114\x41\x4e\104\123" => "\x50\x4e", "\x50\x4f\114\x41\116\104" => "\120\x4c", "\120\117\122\x54\125\107\101\x4c" => "\120\124", "\x51\101\x54\101\x52" => "\x51\101", "\122\xc3\x89\125\x4e\111\117\116" => "\122\105", "\x52\117\x4d\x41\116\111\x41" => "\122\x4f", "\122\x55\123\123\x49\101" => "\122\125", "\x52\x57\x41\x4e\x44\101" => "\122\127", "\123\x41\111\x4e\x54\40\102\101\x52\124\x48\303\211\114\105\x4d\131" => "\x42\x4c", "\x53\x41\111\x4e\124\40\110\x45\114\x45\x4e\x41" => "\x53\x48", "\x53\x41\111\116\124\40\x4b\111\x54\x54\x53\40\x41\x4e\104\x20\116\105\126\x49\x53" => "\x4b\x4e", "\x53\x41\x49\x4e\124\x20\114\x55\103\x49\101" => "\x4c\x43", "\x53\101\x49\x4e\124\x20\x4d\x41\x52\124\111\x4e" => "\x4d\106", "\x53\x41\x49\116\x54\40\x50\x49\105\122\122\105\40\101\116\104\40\115\111\121\x55\105\x4c\x4f\x4e" => "\x50\115", "\123\101\x49\116\124\40\x56\111\x4e\x43\105\x4e\x54\x20\101\x4e\x44\40\124\x48\x45\x20\107\x52\x45\x4e\101\x44\x49\x4e\105\123" => "\x56\x43", "\123\x41\115\x4f\x41" => "\127\123", "\123\101\116\40\115\x41\122\x49\116\117" => "\x53\115", "\123\xc3\203\117\40\124\x4f\x4d\303\x89\x20\101\x4e\x44\40\120\x52\303\215\x4e\103\x49\x50\x45" => "\123\x54", "\123\101\125\104\111\40\101\x52\101\x42\x49\x41" => "\x53\x41", "\123\x45\116\x45\x47\x41\114" => "\x53\x4e", "\x53\105\122\102\111\101" => "\x52\x53", "\123\105\131\103\110\x45\x4c\114\x45\123" => "\x53\103", "\123\x49\105\122\x52\101\40\x4c\x45\x4f\x4e\105" => "\123\114", "\x53\x49\116\107\x41\120\117\122\x45" => "\x53\107", "\123\x49\116\x54\40\x4d\101\101\x52\x54\105\116" => "\123\x58", "\123\114\x4f\x56\x41\113\111\x41" => "\123\113", "\123\114\x4f\126\105\116\111\x41" => "\x53\111", "\123\x4f\x4c\117\115\117\116\x20\111\123\x4c\101\116\104\123" => "\x53\x42", "\123\117\115\101\114\111\101" => "\123\x4f", "\x53\117\125\124\110\x20\x41\x46\x52\x49\x43\101" => "\x5a\101", "\x53\117\125\124\110\x20\107\105\x4f\x52\x47\111\x41\x20\x41\116\x44\40\124\110\x45\40\x53\117\125\124\110\40\123\x41\x4e\x44\127\111\103\110\x20\x49\x53\114\101\x4e\104\x53" => "\x47\123", "\123\x4f\x55\124\110\x20\x4b\x4f\x52\105\101" => "\x4b\122", "\x53\120\101\111\x4e" => "\x45\123", "\x53\122\x49\x20\x4c\101\x4e\113\x41" => "\114\113", "\x53\x55\x44\x41\x4e" => "\x53\x44", "\123\x55\x52\x49\x4e\x41\x4d\x45" => "\x53\122", "\x53\126\x41\x4c\102\101\122\x44\40\101\x4e\x44\x20\112\101\x4e\x20\x4d\101\131\x45\116" => "\x53\x4a", "\123\127\x41\x5a\x49\x4c\x41\116\104" => "\x53\x5a", "\x53\x57\105\104\x45\x4e" => "\123\105", "\123\x57\x49\x54\x5a\105\x52\x4c\x41\x4e\104" => "\x43\110", "\123\x59\122\x49\101" => "\x53\131", "\x54\101\111\127\x41\116\x2c\x20\x50\x52\117\x56\111\x4e\103\x45\40\117\106\x20\103\x48\111\x4e\101" => "\124\x57", "\x54\x41\x4a\111\x4b\111\x53\124\x41\x4e" => "\x54\112", "\124\101\116\x5a\x41\x4e\x49\101" => "\x54\x5a", "\124\110\x41\x49\x4c\101\116\x44" => "\124\x48", "\x54\111\115\117\x52\x2d\114\x45\123\x54\x45" => "\x54\x4c", "\124\117\107\x4f" => "\124\x47", "\x54\117\x4b\105\x4c\101\125" => "\124\x4b", "\x54\117\116\x47\x41" => "\124\x4f", "\124\x52\x49\116\x49\x44\x41\104\40\x41\x4e\104\40\x54\x4f\x42\101\x47\117" => "\x54\124", "\x54\x55\x4e\111\x53\111\101" => "\x54\116", "\x54\125\122\x4b\x45\x59" => "\124\x52", "\x54\125\122\x4b\115\105\x4e\111\123\x54\101\x4e" => "\124\115", "\x54\125\x52\113\123\x20\101\x4e\x44\x20\x43\101\x49\x43\117\123\40\111\x53\114\x41\116\104\123" => "\x54\x43", "\x54\x55\x56\101\x4c\125" => "\x54\x56", "\125\107\x41\x4e\104\101" => "\125\x47", "\x55\113\122\x41\111\116\105" => "\x55\101", "\x55\x4e\111\124\105\x44\40\x41\x52\101\x42\x20\x45\115\x49\122\101\124\105\123" => "\x41\x45", "\x55\x4e\x49\124\105\104\x20\x4b\111\x4e\107\104\x4f\x4d" => "\x47\x42", "\125\x4e\111\x54\105\x44\40\x53\124\x41\124\105\x53" => "\x55\123", "\125\122\125\x47\x55\101\131" => "\125\x59", "\125\56\123\x2e\40\x4f\125\x54\x4c\x59\x49\x4e\107\40\111\123\114\101\x4e\x44\123" => "\x55\x4d", "\125\x2e\123\56\x20\126\x49\x52\x47\x49\x4e\x20\111\x53\114\x41\x4e\x44\x53" => "\126\111", "\x55\132\102\x45\x4b\x49\123\124\101\116" => "\125\132", "\126\101\x4e\x55\x41\124\x55" => "\x56\x55", "\126\x41\124\111\103\x41\x4e\40\x43\x49\124\x59" => "\126\101", "\126\x45\116\x45\x5a\x55\x45\x4c\x41" => "\x56\105", "\x56\111\105\x54\x4e\101\115" => "\x56\x4e", "\127\101\114\x4c\x49\x53\40\101\116\x44\40\x46\125\x54\x55\116\101" => "\127\x46", "\127\105\x53\124\x45\x52\x4e\40\123\x41\x48\x41\122\x41" => "\x45\x48", "\x59\x45\115\x45\116" => "\131\105", "\132\x41\115\102\x49\101" => "\132\x4d", "\132\111\115\x42\101\x42\127\x45" => "\132\x57"];
        if (!(strlen($VP) <= 3)) {
            goto no;
        }
        if (in_array(strtoupper($VP), $a5)) {
            goto L8;
        }
        $this->messageManager->addErrorMessage("\x49\156\166\x61\154\151\144\40\x43\157\x75\x6e\x74\162\171\40\116\x61\x6d\145\x2e");
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl())->sendResponse();
        exit;
        L8:
        return $VP;
        no:
        if (array_key_exists(strtoupper($VP), $a5)) {
            goto xv;
        }
        $this->messageManager->addErrorMessage("\x49\x6e\x76\141\x6c\151\x64\40\x43\x6f\165\x6e\x74\162\x79\x20\x4e\141\x6d\x65\x2e");
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl())->sendResponse();
        exit;
        xv:
        $WI = $a5[strtoupper($VP)];
        return $WI;
    }
    private function processUserAction($Dl, $FO, $Fo, $SD, $VC, $YU, $Lq, $Au, $Te, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA)
    {
        $Dl = !$this->spUtility->isBlank($Dl) ? $Dl : $this->findUserEmail();
        $Ea = false;
        $this->spUtility->log_debug("\40\x69\156\x73\151\144\145\x20\160\x72\157\143\145\x73\163\125\163\x65\x72\x41\x63\x74\151\157\156\50\51\40");
        $Ea = $this->spUtility->checkIfFlowStartedFromBackend($this->relayState);
        $this->spUtility->log_debug("\x70\x72\157\143\x65\163\x73\x55\x73\145\162\x41\143\x74\151\157\x6e\50\x29\72\x20\x69\x73\101\x64\x6d\151\x6e\x3a\40" . json_encode($Ea));
        $user = null;
        $F_ = \Magento\Framework\App\ObjectManager::getInstance();
        $fD = $F_->get("\115\141\x67\145\156\x74\157\134\103\x75\163\x74\157\x6d\x65\162\x5c\115\x6f\x64\x65\x6c\x5c\123\x65\163\x73\151\x6f\156");
        if (!$fD->isLoggedIn()) {
            goto ue;
        }
        $this->spUtility->log_debug("\x70\162\x6f\x63\145\x73\x73\125\163\x65\x72\101\143\x74\151\157\156\x28\51\72\40\x55\163\145\x72\40\163\x65\163\x73\151\157\x6e\40\106\157\165\x6e\x64");
        $Rs = $this->spUtility->getBaseUrlFromUrl($this->relayState);
        if (!($Dl != $fD->getCustomer()->getEmail())) {
            goto JN;
        }
        $this->messageManager->addErrorMessage(__("\101\156\157\164\150\x65\162\40\x55\163\145\162\40\x69\x73\40\141\154\x72\x65\x61\144\x79\40\x6c\157\x67\147\x65\144\40\x69\x6e\56\x20\x50\154\145\x61\x73\x65\x20\154\157\x67\x6f\165\x74\x20\146\151\x72\163\x74\40\141\156\144\40\x74\x68\145\156\x20\x74\x72\171\x20\x61\x67\x61\151\x6e\x2e"));
        $this->spUtility->log_debug("\101\156\157\x74\150\x65\162\40\x55\x73\145\162\40\151\x73\x20\141\154\x72\x65\141\x64\171\40\154\x6f\147\x67\x65\x64\40\x69\x6e\56\x20\x50\154\x65\x61\x73\145\40\x6c\157\x67\x6f\x75\x74\40\146\151\x72\163\x74\40\141\156\144\40\x74\150\145\156\40\164\162\171\x20\141\x67\141\151\x6e\x2e");
        JN:
        $this->spUtility->log_debug("\x70\x72\157\x63\x65\x73\x73\x55\163\145\162\101\143\x74\151\157\156\50\51\x3a\40\122\145\144\151\x72\x65\143\164\151\156\147\x20\x74\x6f\x3a\x20" . $Rs);
        $this->responseFactory->create()->setRedirect($Rs)->sendResponse();
        exit;
        ue:
        $this->spUtility->log_debug("\x20\151\x6e\x73\151\x64\145\x20\160\162\157\143\x65\163\x73\x55\x73\x65\x72\x41\x63\164\151\157\x6e\x28\x29\72\x20\x4e\157\x20\x55\163\x65\162\40\x73\145\x73\163\x69\157\156\x20\x46\x6f\165\156\x64");
        if ($Ea) {
            goto RY;
        }
        $Uu = $this->defaultGroup;
        $Rb = $this->autoCreateCustomer;
        $user = $this->getCustomerFromAttributes($Dl);
        if ($user) {
            goto NH;
        }
        if (!$user && $Rb) {
            goto C6;
        }
        $this->spUtility->log_debug("\x54\150\x69\163\x20\103\x75\x73\164\x6f\155\x65\162\x20\x64\157\145\163\x20\x6e\157\164\40\145\170\151\x73\164\x73\x20\141\156\144\x20\x63\x61\156\156\x6f\164\x20\x62\x65\x20\x61\x75\164\x6f\55\x63\162\x65\141\x74\145\x64\56");
        $this->messageManager->addErrorMessage(__("\124\150\151\163\x20\103\165\x73\164\157\x6d\145\162\x20\x64\157\145\163\40\x6e\157\x74\x20\x65\x78\151\163\x74\x73\40\x61\156\x64\x20\143\141\x6e\156\157\x74\40\x62\145\x20\141\x75\x74\157\x2d\x63\x72\145\x61\164\x65\x64\x2e\120\154\145\x61\x73\145\40\143\157\156\164\x61\x63\x74\x20\171\157\165\x72\x20\x41\x64\x6d\x69\156\151\163\164\162\x61\164\157\x72\x2e"));
        if ($this->auto_redirect) {
            goto Hb;
        }
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl() . "\143\x75\x73\x74\157\x6d\x65\x72\x2f\x61\143\x63\x6f\x75\156\x74")->sendResponse();
        goto uq;
        Hb:
        $Te = $this->getRequest()->getParams();
        $eT = $this->storeManager->getStore()->getBaseUrl();
        if (empty($Te["\x52\145\x6c\141\x79\123\164\141\x74\145"])) {
            goto GZ;
        }
        $eT = $Te["\x52\145\x6c\x61\171\x53\164\x61\x74\145"];
        GZ:
        $this->responseFactory->create()->setRedirect($eT)->sendResponse();
        uq:
        exit("\x54\150\151\163\40\103\x75\163\x74\x6f\155\x65\x72\40\144\157\x65\x73\40\x6e\x6f\x74\40\145\170\151\163\164\x73\x20\x61\156\144\x20\143\141\x6e\x6e\x6f\164\x20\x62\x65\40\141\165\x74\x6f\x2d\143\162\x65\141\164\x65\x64\x2e\x50\x6c\x65\x61\x73\145\40\x63\x6f\x6e\164\141\143\x74\x20\171\x6f\x75\x72\x20\101\144\155\151\x6e\151\163\164\162\x61\164\157\162\56");
        goto WV;
        C6:
        $this->spUtility->log_debug("\x20\x70\x72\x6f\143\145\x73\x73\125\x73\x65\x72\x41\x63\x74\151\157\x6e\x28\51\x3a\40\103\165\x73\164\157\x6d\145\x72\40\116\x6f\164\x20\106\157\165\156\x64\x2c\40\103\x72\145\141\x74\151\x6e\x67\40\x4f\x6e\x65\72\x20");
        $user = $this->createNewUser($Dl, $FO, $Fo, $SD, $VC, $Uu, $Au, $user, $Ea, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA);
        WV:
        goto IH;
        NH:
        $this->spUtility->log_debug("\40\x70\162\x6f\143\x65\163\163\x55\x73\145\162\101\143\x74\151\157\x6e\50\51\40\72\40\125\x73\x65\162\40\106\157\165\156\x64\72\x20\125\160\144\x61\x74\x69\156\x67\40\101\x74\164\162\151\x62\x75\164\145\x73\x20");
        $user = $this->updateUserAttributes($SD, $Dl, $FO, $Fo, $VC, $Uu, $Au, $user, $Ea, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA);
        $user = $this->updateCustomAttributes($user, $Dl);
        IH:
        goto zs;
        RY:
        $YU = $this->defaultRole;
        $this->spUtility->log_debug("\160\162\x6f\x63\x65\163\x73\x55\163\x65\162\x41\x63\164\151\x6f\x6e\x28\51\40\72\40\x20\144\x65\146\141\x75\x6c\164\x52\x6f\154\145\40\146\x72\x6f\x6d\x20\163\x65\x74\x74\x69\156\147\x73\72\40" . json_encode($YU));
        $user = $this->getAdminUserFromAttributes($Dl);
        $Rb = $this->autoCreateAdminUser;
        if (!$this->spUtility->isBlank($user)) {
            goto fy;
        }
        if ($this->spUtility->isBlank($user) && $Rb) {
            goto dq;
        }
        echo "\x54\150\x69\163\x20\142\141\x63\153\145\156\144\40\x75\163\145\162\x20\144\157\145\x73\40\x6e\157\164\x20\145\170\151\x73\x74\x73\x20\x61\x6e\144\40\x63\141\x6e\156\x6f\164\x20\x62\145\40\x61\x75\x74\157\x2d\143\x72\145\x61\x74\x65\x64\56\40\120\154\x65\x61\163\x65\40\x63\157\x6e\164\141\x63\x74\x20\x79\157\165\162\x20\x61\x64\155\151\x6e\151\163\x74\x72\x61\164\157\162\x2e";
        exit;
        goto Hx;
        dq:
        $this->spUtility->log_debug("\160\x72\x6f\x63\145\163\163\125\x73\x65\x72\x41\143\164\151\157\x6e\50\51\72\40\x41\144\x6d\x69\x6e\125\163\x65\x72\x20\x4e\x6f\x74\x20\x46\157\165\x6e\x64\72\40\103\162\145\x61\164\151\156\147\40\117\156\x65");
        $user = $this->createNewUser($Dl, $FO, $Fo, $SD, $VC, $YU, $Au, $user, $Ea, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA);
        Hx:
        goto S8;
        fy:
        $this->spUtility->log_debug("\x70\162\157\143\x65\x73\x73\x55\163\x65\162\101\143\x74\x69\x6f\x6e\50\x29\x3a\40\x41\x64\x6d\151\x6e\x20\x55\x73\145\162\40\106\x6f\x75\x6e\144\72\40\x55\160\144\141\x74\151\156\x67\40\101\x74\164\x72\151\142\165\164\x65\163\x20");
        $user = $this->updateUserAttributes($SD, $Dl, $FO, $Fo, $VC, $YU, $Au, $user, $Ea, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA);
        S8:
        zs:
        $this->spUtility->log_debug("\120\162\x6f\x63\x65\163\163\x55\163\x65\x72\101\143\164\151\157\x6e\50\x29\x20\x3a\x20\x62\x65\146\x6f\162\x65\x20\162\145\x64\x69\x72\145\x63\164\x69\x6e\x67\40\x75\x73\145\x72\163\x3a\40\x72\x65\154\x61\x79\x53\x74\141\164\x65\x20\72" . $this->relayState);
        if (null == $user && $Ea) {
            goto lO;
        }
        if (null == $user) {
            goto y9;
        }
        if (null != $user && $Ea) {
            goto NM;
        }
        $this->spUtility->setSessionData("\143\165\x73\164\x6f\x6d\145\162\137\x70\157\163\164\x5f\154\x6f\x67\157\165\x74", 1);
        $user = $this->customerModel->load($user->getId());
        $this->spUtility->log_debug("\120\162\x6f\x63\145\x73\x73\x55\x73\x65\162\101\143\x74\x69\x6f\x6e\x28\51\40\72\x20\x72\145\x64\x69\x72\x65\x63\164\151\156\147\x20\143\x75\163\164\157\155\x65\162\x20\72");
        $this->customerLoginAction->setUser($user)->setCustomerId($user->getId())->setRelayState($this->relayState)->setAxCompanyId($this->accountId)->execute();
        goto FM;
        NM:
        $this->spUtility->setAdminSessionData("\141\x64\155\151\x6e\137\160\x6f\x73\x74\x5f\154\157\x67\157\165\164", 1);
        $this->spUtility->log_debug("\120\x72\157\x63\x65\x73\x73\x55\163\x65\162\x41\x63\164\x69\157\156\50\51\x20\x3a\40\162\x65\144\x69\162\145\x63\164\151\156\147\40\141\144\155\151\156\x20\x3a");
        $this->redirectToBackendAndLogin($user->getUsername(), $this->sessionIndex, $this->relayState);
        FM:
        goto Co;
        lO:
        echo "\x54\x68\151\x73\x20\x55\163\x65\162\40\144\157\x65\163\x20\x6e\x6f\x74\40\145\170\x69\x73\164\x73\40\x61\x6e\x64\40\143\141\156\x6e\x6f\164\x20\142\145\x20\141\165\164\157\55\x63\162\145\141\164\145\144\56\x20\120\154\x65\141\x73\x65\40\143\x6f\156\x74\141\x63\164\40\171\x6f\165\x72\40\101\144\155\x69\x6e\x69\x73\164\x72\141\x74\x6f\162\56";
        exit;
        goto Co;
        y9:
        $this->messageManager->addErrorMessage(__("\124\x68\151\x73\40\x55\163\145\x72\x20\144\x6f\145\x73\x20\x6e\x6f\164\x20\x65\170\x69\x73\164\163\40\x61\156\x64\40\x63\141\156\156\x6f\x74\40\142\145\x20\x61\165\x74\x6f\x2d\x63\162\x65\x61\x74\x65\x64\56\x20\x50\154\145\141\163\x65\x20\143\157\x6e\x74\x61\143\164\40\x79\x6f\165\x72\x20\x41\144\155\151\156\151\x73\x74\x72\141\x74\x6f\162\x2e"));
        if ($this->auto_redirect) {
            goto ta;
        }
        $Rs = $this->storeManager->getStore()->getBaseUrl();
        $this->responseFactory->create()->setRedirect($Rs . "\x63\x75\163\164\157\155\x65\162\57\141\143\x63\x6f\x75\x6e\x74")->sendResponse();
        exit;
        goto xY;
        ta:
        $Te = $this->getRequest()->getParams();
        $eT = $this->storeManager->getStore()->getBaseUrl();
        if (empty($Te["\x52\145\x6c\141\x79\123\x74\x61\164\x65"])) {
            goto kN;
        }
        $eT = $Te["\122\145\154\x61\x79\x53\x74\x61\x74\x65"];
        kN:
        $this->responseFactory->create()->setRedirect($eT)->sendResponse();
        exit;
        xY:
        Co:
    }
    private function findUserEmail()
    {
        $this->spUtility->log_debug("\160\x72\157\x63\145\x73\x73\122\x65\163\160\x6f\x6e\x73\145\101\x63\164\x69\157\156\72\x20\x66\151\156\144\x55\x73\x65\x72\x45\155\141\151\x6c");
        if (!$this->attrs) {
            goto F0;
        }
        foreach ($this->attrs as $VP) {
            if (!filter_var($VP[0], FILTER_VALIDATE_EMAIL)) {
                goto lT;
            }
            return $VP[0];
            lT:
            zX:
        }
        KM:
        return '';
        F0:
    }
    private function getAdminUserFromAttributes($Dl)
    {
        $e7 = false;
        $this->spUtility->log_debug("\x50\162\157\x63\x65\x73\x73\125\x73\x65\162\101\x63\x74\x69\157\x6e\72\x20\x67\x65\x74\x41\x64\155\151\156\125\163\x65\162\106\162\157\155\101\x74\x74\162\x69\142\x75\164\x65\163\50\x29\72\x20");
        $oD = $this->adminUserModel->getResource()->getConnection();
        $d8 = $oD->select()->from($this->adminUserModel->getResource()->getMainTable())->where("\145\x6d\141\151\x6c\75\x3a\145\x6d\141\x69\x6c");
        $vq = ["\145\x6d\141\151\x6c" => $Dl];
        $e7 = $oD->fetchRow($d8, $vq);
        $e7 = is_array($e7) ? $this->adminUserModel->loadByUsername($e7["\165\x73\145\x72\x6e\x61\x6d\x65"]) : $e7;
        $this->spUtility->log_debug("\147\x65\164\101\x64\155\151\156\x55\163\145\x72\106\162\157\155\101\164\164\x72\x69\x62\165\x74\x65\163\x28\51\72\40\x66\x65\x74\x63\x68\x65\144\40\x61\144\x6d\x69\x6e\x55\x73\x65\x72\x3a\x20");
        return $e7;
    }
    private function updateUserAttributes($SD, $Dl, $FO, $Fo, $VC, $YU, $Au, $user, $Ea, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA)
    {
        $U1 = $user->getId();
        if ($Ea) {
            goto m0;
        }
        $Sn = $this->spUtility->getCustomer($U1);
        $this->spUtility->log_debug("\165\160\144\141\164\145\125\163\145\x72\x41\164\164\162\x69\142\165\164\x65\x73\50\x29\x3a\40\x63\165\163\x74\157\155\145\162\x3a\x20" . $Au);
        if (!$this->spUtility->isBlank($FO)) {
            goto n9;
        }
        $FO = explode("\100", $Dl)[0];
        $FO = preg_replace("\x2f\133\136\x41\x2d\x5a\x61\x2d\172\60\55\x39\134\55\135\57", '', $FO);
        n9:
        if (!$this->spUtility->isBlank($Fo)) {
            goto ZC;
        }
        $Fo = explode("\100", $Dl)[1];
        $Fo = preg_replace("\57\x5b\x5e\x41\55\132\141\x2d\172\60\55\x39\134\x2d\x5d\x2f", '', $Fo);
        ZC:
        $dD = array("\116\x61\155\x65\111\104" => $Au, "\x53\145\163\x73\151\157\156\x49\x6e\x64\145\170" => $this->sessionIndex);
        $this->spUtility->saveConfig("\x65\170\x74\162\x61", $dD, $U1, $Ea);
        $pI = $this->group_mapping;
        $QB = $this->processRoles($YU, $Ea, $pI, $VC);
        if (!$this->spUtility->isBlank($QB)) {
            goto cR;
        }
        $QB = $this->processDefaultRole($Ea, $YU);
        cR:
        $vH = $this->spUtility->getCurrentStore();
        $Ed = $this->spUtility->getCurrentWebsiteId();
        $Sn = $this->customerFactory->create();
        $Sn->setWebsiteId($Ed)->loadByEmail($Dl);
        if (!(!$this->spUtility->isBlank($QB) && !empty($this->updateFrontendRole) && strcasecmp($this->updateFrontendRole, SPConstants::enable) === 0)) {
            goto ly;
        }
        $Sn->setWebsiteId($Ed)->setStore($vH)->setGroupId($QB)->setForceConfirmed(true)->save();
        ly:
        if (!(!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0)) {
            goto i6;
        }
        $Sn->setWebsiteId($Ed)->setStore($vH)->setFirstname($FO)->setLastname($Fo)->setEmail($Dl)->setForceConfirmed(true)->save();
        if (!($TE || $Kk || $eC || $jF || $vA || $Gb)) {
            goto ML;
        }
        $td = $Sn->getDefaultBilling();
        $gE = $this->_addressFactory->create()->load($td);
        if (is_null($td)) {
            goto Iq;
        }
        $gE->setFirstname($FO);
        $gE->setLastname($Fo);
        $this->spUtility->log_debug("\x20\x65\170\151\164\x69\156\147\x20\165\160\x64\x61\x74\x65\125\163\145\x72\101\164\164\x72\x69\x62\x75\164\145\x73\x3a\40\160\x68\157\156\145");
        if (empty($TE)) {
            goto rx;
        }
        $gE->setTelephone($TE);
        rx:
        if (empty($Kk)) {
            goto dh;
        }
        $gE->setStreet($Kk);
        dh:
        if (empty($eC)) {
            goto sv;
        }
        $gE->setCity($eC);
        sv:
        if (empty($jF)) {
            goto oP;
        }
        $M3 = $this->collectionFactory->create()->addRegionNameFilter($jF)->getFirstItem()->toArray();
        if (empty($M3["\162\x65\x67\x69\x6f\x6e\137\x69\x64"])) {
            goto x7;
        }
        $gE->setRegionId($M3["\162\145\x67\x69\x6f\156\137\151\144"]);
        x7:
        oP:
        if (empty($vA)) {
            goto Iz;
        }
        $gE->setCountryId($vA);
        Iz:
        if (empty($Gb)) {
            goto Iu;
        }
        $gE->setPostcode($Gb);
        Iu:
        $gE->save();
        goto QE;
        Iq:
        $nS = $this->dataAddressFactory->create();
        $nS->setFirstname($FO);
        $nS->setLastname($Fo);
        if (empty($TE)) {
            goto iG;
        }
        $nS->setTelephone($TE);
        iG:
        if (empty($Kk)) {
            goto zY;
        }
        $nS->setStreet($Kk);
        zY:
        if (empty($eC)) {
            goto IU;
        }
        $nS->setCity($eC);
        IU:
        if (empty($vA)) {
            goto GK;
        }
        $nS->setCountryId($vA);
        GK:
        if (empty($jF)) {
            goto Du;
        }
        $M3 = $this->collectionFactory->create()->addRegionNameFilter($jF)->getFirstItem()->toArray();
        if (empty($M3["\162\145\x67\151\157\156\x5f\151\x64"])) {
            goto Hv;
        }
        $nS->setRegionId($M3["\x72\x65\x67\x69\157\x6e\x5f\151\x64"]);
        Hv:
        Du:
        if (empty($Gb)) {
            goto FQ;
        }
        $nS->setPostcode($Gb);
        FQ:
        $nS->setIsDefaultBilling("\x31");
        $nS->setSaveInAddressBook("\61");
        $nS->setCustomerId($Sn->getId());
        try {
            $nS->save();
            $Sn = $nS->getCustomer();
        } catch (\Exception $cA) {
            $this->spUtility->log_debug("\101\156\x20\x65\162\x72\157\x72\x20\x6f\x63\x63\165\x72\x72\x65\x64\40\x77\150\x69\x6c\x65\x20\164\x72\171\151\156\147\x20\x74\157\40\163\145\164\x20\141\x64\144\x72\145\163\x73\x3a\x20{$cA->getMessage()}");
        }
        QE:
        ML:
        if (!($VB || $C0 || $iW || $rB || $KA || $Xx)) {
            goto MK;
        }
        $k7 = $Sn->getDefaultShipping();
        $Sb = $this->_addressFactory->create()->load($k7);
        if (is_null($k7)) {
            goto Ag;
        }
        $Sb->setFirstname($FO);
        $Sb->setLastname($Fo);
        $this->spUtility->log_debug("\40\145\x78\x69\164\151\x6e\x67\x20\x75\160\144\x61\x74\145\125\163\x65\162\101\x74\164\162\151\x62\x75\x74\x65\x73\x3a\x20\160\x68\x6f\x6e\x65");
        if (empty($VB)) {
            goto BD;
        }
        $Sb->setTelephone($VB);
        BD:
        if (empty($C0)) {
            goto lu;
        }
        $Sb->setStreet($C0);
        lu:
        if (empty($iW)) {
            goto UJ;
        }
        $Sb->setCity($iW);
        UJ:
        if (empty($rB)) {
            goto SY;
        }
        $M3 = $this->collectionFactory->create()->addRegionNameFilter($rB)->getFirstItem()->toArray();
        if (empty($M3["\x72\145\x67\x69\157\156\x5f\151\x64"])) {
            goto p0;
        }
        $Sb->setRegionId($M3["\162\x65\x67\151\157\x6e\x5f\x69\x64"]);
        p0:
        SY:
        if (empty($KA)) {
            goto cV;
        }
        $Sb->setCountryId($KA);
        cV:
        if (empty($Xx)) {
            goto iq;
        }
        $Sb->setPostcode($Xx);
        iq:
        $Sb->save();
        goto H8;
        Ag:
        $nS = $this->dataAddressFactory->create();
        $nS->setFirstname($FO);
        $nS->setLastname($Fo);
        if (empty($VB)) {
            goto Fu;
        }
        $nS->setTelephone($VB);
        Fu:
        if (empty($C0)) {
            goto GC;
        }
        $nS->setStreet($C0);
        GC:
        if (empty($iW)) {
            goto vZ;
        }
        $nS->setCity($iW);
        vZ:
        if (empty($rB)) {
            goto TA;
        }
        $M3 = $this->collectionFactory->create()->addRegionNameFilter($rB)->getFirstItem()->toArray();
        if (empty($M3["\162\145\x67\151\157\156\137\x69\x64"])) {
            goto Q2;
        }
        $nS->setRegionId($M3["\162\x65\147\151\x6f\156\x5f\151\x64"]);
        Q2:
        TA:
        if (empty($KA)) {
            goto ip;
        }
        $nS->setCountryId($KA);
        ip:
        if (empty($Xx)) {
            goto Zy;
        }
        $nS->setPostcode($Xx);
        Zy:
        $nS->setIsDefaultShipping("\x31");
        $nS->setSaveInAddressBook("\x31");
        $nS->setCustomerId($Sn->getId());
        try {
            $nS->save();
            $Sn = $nS->getCustomer();
        } catch (\Exception $cA) {
            $this->spUtility->log_debug("\x41\x6e\x20\145\x72\162\x6f\162\40\157\x63\143\165\x72\162\145\x64\x20\167\150\x69\154\x65\40\164\x72\171\x69\x6e\147\x20\x74\x6f\x20\x73\145\164\40\x61\x64\144\162\x65\x73\x73\72\40{$cA->getMessage()}");
        }
        H8:
        MK:
        i6:
        goto fR;
        m0:
        $QB = null;
        $e7 = $this->spUtility->getAdminUserById($U1);
        $this->spUtility->log_debug("\165\160\144\141\164\x65\x55\x73\145\x72\101\164\x74\162\151\142\165\164\145\163\x28\51\x3a\x20\x61\x64\x6d\x69\x6e\x3a\x20");
        if (!(!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0)) {
            goto XM;
        }
        if (!$this->spUtility->isBlank($FO)) {
            goto gT;
        }
        $FO = explode("\x40", $Dl)[0];
        $FO = preg_replace("\57\133\x5e\x41\x2d\132\x61\x2d\x7a\60\x2d\71\134\x2d\x5d\57", '', $FO);
        gT:
        if (!$this->spUtility->isBlank($Fo)) {
            goto tk;
        }
        $Fo = explode("\100", $Dl)[1];
        $Fo = preg_replace("\57\133\x5e\101\55\x5a\x61\x2d\x7a\60\55\71\134\x2d\135\x2f", '', $Fo);
        tk:
        if ($this->spUtility->isBlank($FO)) {
            goto Wm;
        }
        $e7->setFirstname($FO);
        Wm:
        if ($this->spUtility->isBlank($Fo)) {
            goto Ro;
        }
        $e7->setLastname($Fo);
        Ro:
        if ($this->spUtility->isBlank($FO)) {
            goto U7;
        }
        $this->spUtility->saveConfig(SPConstants::DB_FIRSTNAME, $FO, $U1, $Ea);
        U7:
        if ($this->spUtility->isBlank($Fo)) {
            goto CI;
        }
        $this->spUtility->saveConfig(SPConstants::DB_LASTNAME, $Fo, $U1, $Ea);
        CI:
        XM:
        $w5 = $this->role_mapping;
        $pI = $this->group_mapping;
        if ($this->spUtility->isBlank($w5)) {
            goto ah;
        }
        $QB = $this->processRoles($YU, $Ea, $w5, $VC);
        ah:
        if (!$this->spUtility->isBlank($QB)) {
            goto W1;
        }
        $QB = $this->processDefaultRole($Ea, $YU);
        W1:
        $QB = $this->spUtility->isBlank($QB) ? 1 : $QB;
        if (!(!$this->spUtility->isBlank($QB) && !empty($this->updateRole) && strcasecmp($this->updateRole, SPConstants::enable) === 0)) {
            goto Lb;
        }
        $e7->setRoleId($QB);
        Lb:
        if (!isset($SD)) {
            goto lr;
        }
        $e7->setUsername($SD);
        lr:
        $this->spUtility->log_debug("\40\145\x78\151\x74\151\156\147\40\165\x70\144\141\164\x65\125\163\145\162\x41\x74\x74\162\x69\142\x75\164\145\x73\x3a\x20\x61\144\x6d\x69\156\125\163\145\x72\40\105\155\x61\x69\x6c", $e7->getEmail());
        $e7->save();
        fR:
        if (!(!empty($QB) && !empty($this->dontAllowUnlistedUserRole) && $this->dontAllowUnlistedUserRole == SPConstants::enable)) {
            goto sZ;
        }
        return;
        sZ:
        return $user;
    }
    private function processRoles($YU, $Ea, $Nd, $VC)
    {
        if (!$Ea) {
            goto Om;
        }
        $YU = $this->defaultRole;
        Om:
        $this->spUtility->log_debug("\144\145\146\141\165\x6c\x74\40\122\157\x6c\x65\x3a\x20", $YU);
        if (!$Nd) {
            goto Ts;
        }
        $Nd = json_decode($Nd);
        Ts:
        $Ah = null;
        if (!(empty($VC) || empty($Nd))) {
            goto Jv;
        }
        return null;
        Jv:
        foreach ($Nd as $SP => $ec) {
            $DE = explode("\x3b", $ec);
            foreach ($DE as $sC) {
                if (empty($sC)) {
                    goto iP;
                }
                foreach ($VC as $tk) {
                    if (!($tk == $sC)) {
                        goto tF;
                    }
                    $Ah = $SP;
                    return $Ah;
                    tF:
                    I3:
                }
                wN:
                iP:
                C8:
            }
            hv:
            Fk:
        }
        fL:
        return $this->getGroupIdByName($YU);
    }
    private function getGroupIdByName($VC)
    {
        $DE = $this->userGroupModel->toOptionArray();
        foreach ($DE as $tk) {
            if (!($VC == $tk["\x6c\x61\142\145\x6c"])) {
                goto jG;
            }
            $this->spUtility->log_debug("\x67\145\164\x47\162\157\165\160\111\144\x42\171\116\141\x6d\x65\x28\x29\72\x20\x72\x65\x74\x75\162\x6e\151\x6e\x67\x20\147\162\157\165\160\x49\144\72\x20" . $tk["\x76\x61\x6c\165\145"] . "\40\x66\x6f\162\x20\162\x6f\x6c\x65\x3a\40" . $tk["\154\141\x62\145\x6c"]);
            return $tk["\x76\x61\x6c\165\x65"];
            jG:
            Ue:
        }
        OG:
        $this->spUtility->log_debug("\x67\145\164\x47\162\x6f\165\x70\x49\x64\102\171\116\x61\155\145\50\x29\72\x20\x53\157\155\145\164\x68\x69\x6e\x67\x20\x77\145\x6e\164\x20\167\162\x6f\156\x67\x2e\x20\104\x65\x66\x61\x75\154\x74\40\x47\x72\157\165\160\111\144\x20\143\x61\156\156\157\x74\x20\142\x65\x20\106\x6f\165\x6e\144\72\x20");
    }
    private function processDefaultRole($Ea, $YU)
    {
        if (!$this->spUtility->isBlank($YU)) {
            goto EY;
        }
        $this->spUtility->log_debug("\160\x72\157\x63\145\163\x73\x44\x65\x66\x61\x75\x6c\x74\x52\x6f\x6c\145\x28\51\x3a\40\x64\x65\146\141\165\154\164\122\x6f\x6c\x65\40\x69\x73\40\x42\154\141\x6e\153\56\x20\123\x65\164\164\x69\156\x67\x20\x50\162\x65\x44\x65\146\x69\x6e\145\x64\x52\157\x6c\145\x73");
        $YU = $Ea ? SPConstants::DEFAULT_ROLE : SPConstants::DEFAULT_GROUP;
        EY:
        $Nk = $Ea ? $this->getRoleIdByName($YU) : $this->getGroupIdByName($YU);
        $this->spUtility->log_debug("\160\162\157\143\x65\163\163\104\145\146\x61\165\x6c\164\122\x6f\x6c\x65\x28\x29\72\x20\x72\145\x74\x75\x72\x6e\151\156\x67\x20\x64\x65\146\x61\x75\x6c\x74\40\x52\x6f\x6c\x65\x2f\107\162\x6f\x75\x6f\40\x49\144\72\40" . $Nk . "\x20\146\157\162\40\x72\157\154\145\x2f\147\162\x6f\165\160\x20" . $YU);
        return $Nk;
    }
    private function getRoleIdByName($ZV)
    {
        $CP = $this->adminRoleModel->toOptionArray();
        foreach ($CP as $An) {
            if (!($ZV == $An["\x6c\141\x62\145\154"])) {
                goto Jm;
            }
            $this->spUtility->log_debug("\147\x65\164\122\x6f\x6c\x65\111\144\x42\x79\116\141\155\145\x28\51\x3a\40\162\x65\x74\x75\x72\156\151\156\x67\40\x72\x6f\154\x65\111\x64\x3a\40" . $An["\166\141\154\x75\x65"] . "\40\x66\157\x72\40\162\157\154\x65\72\x20" . $An["\x6c\x61\x62\x65\154"]);
            return $An["\166\141\x6c\x75\145"];
            Jm:
            by:
        }
        Oj:
        $this->spUtility->log_debug("\x67\x65\x74\x47\162\x6f\x75\x70\111\x64\x42\171\x4e\141\155\145\50\51\72\40\123\x6f\155\145\x74\x68\x69\156\x67\x20\x77\x65\156\x74\40\167\x72\157\156\x67\56\40\104\145\146\x61\x75\x6c\164\x20\x52\157\154\145\111\x64\40\143\141\156\156\157\x74\40\142\x65\40\x46\157\165\156\144\72\40");
    }
    private function createNewUser($Dl, $FO, $Fo, $SD, $VC, $YU, $Au, $user, $Ea, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA)
    {
        if (!$this->spUtility->check_license_plan(4)) {
            goto Oy;
        }
        $this->spUtility->flushCache();
        $this->spUtility->reinitConfig();
        if ($this->spUtility->getRemainingUsersCount()) {
            goto vy;
        }
        $ov = (int) AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::MAGENTO_COUNTER), SPConstants::DEFAULT_TOKEN_VALUE);
        $this->spUtility->setStoreConfig(SPConstants::MAGENTO_COUNTER, AESEncryption::encrypt_data($ov + 1, SPConstants::DEFAULT_TOKEN_VALUE));
        $this->spUtility->flushCache();
        $this->spUtility->reinitConfig();
        goto Et;
        vy:
        $this->spUtility->log_debug("\131\157\x75\162\x20\x41\165\164\x6f\40\103\162\x65\141\164\145\40\x55\x73\145\162\x20\x4c\151\x6d\x69\164\x20\146\x6f\162\40\164\x68\x65\x20\164\x72\x69\141\x6c\x20\115\151\x6e\x69\157\162\x61\x6e\x67\x65\x20\115\x61\147\x65\156\x74\157\40\x53\x41\x4d\x4c\x20\123\x50\x20\160\154\165\x67\x69\x6e\40\x69\163\x20\145\x78\x63\145\145\x64\x65\x64\56\x20\x50\154\x65\x61\163\145\x20\125\160\147\162\141\x64\145\x20\164\157\40\x61\156\x79\x20\157\x66\40\x74\x68\x65\x20\120\x72\x65\155\151\165\155\x20\x50\x6c\x61\x6e\x20\164\157\40\143\x6f\156\164\151\x6e\165\145\x20\x74\x68\x65\x20\x73\x65\162\x76\x69\143\145\x2e");
        print_r("\131\x6f\165\x72\x20\x41\165\164\x6f\40\x43\x72\x65\x61\x74\x65\40\125\x73\145\162\x20\114\151\x6d\151\x74\40\x66\x6f\162\x20\x74\150\145\40\164\x72\x69\141\x6c\x20\x4d\151\156\x69\157\162\x61\x6e\147\145\40\x4d\x61\147\x65\156\164\x6f\40\123\101\115\x4c\40\x53\120\40\x70\154\165\x67\151\156\x20\151\163\40\x65\170\143\x65\x65\144\x65\x64\56\40\x50\x6c\x65\x61\163\x65\40\x55\x70\x67\x72\x61\x64\145\x20\x74\157\x20\141\156\x79\x20\157\x66\x20\164\x68\x65\x20\x50\162\145\155\x69\165\x6d\x20\120\154\x61\x6e\40\164\x6f\x20\143\x6f\x6e\164\x69\156\165\x65\40\164\150\145\x20\x73\145\x72\166\x69\x63\x65\56");
        exit;
        Et:
        Oy:
        $this->spUtility->log_debug("\143\162\145\141\x74\145\116\x65\167\125\163\x65\162\50\x29\x3a\x20\145\x6d\x61\151\154\72\x20" . $Dl);
        $this->spUtility->log_debug("\x63\x72\145\x61\164\145\x4e\145\x77\125\x73\145\x72\50\x29\72\x20\x61\x64\x6d\151\x6e\72\40" . json_encode($Ea));
        $Ju = $this->generatePassword(16);
        $EK = !$this->spUtility->isBlank($Dl) ? $Dl : $this->findUserEmail();
        if (!empty($EK)) {
            goto cF;
        }
        if ($Ea) {
            goto BI;
        }
        $this->spUtility->log_debug("\x54\150\151\163\x20\103\165\x73\164\x6f\155\x65\x72\x20\145\155\141\151\x6c\40\156\x6f\x74\x20\x66\157\165\156\x64\x2e");
        $this->messageManager->addErrorMessage(__("\x45\x6d\141\x69\154\x20\156\x6f\164\40\146\x6f\x75\156\144\x2e\120\154\145\141\163\x65\x20\x63\x6f\x6e\164\141\x63\x74\40\x79\157\x75\x72\40\x41\144\x6d\151\156\x69\163\164\162\x61\x74\157\162\x2e"));
        $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl() . "\x2f\143\165\163\x74\157\x6d\145\x72\57\141\x63\143\157\x75\x6e\x74")->sendResponse();
        exit;
        goto xB;
        BI:
        $this->spUtility->log_debug("\124\x68\x69\x73\40\x61\144\x6d\x69\156\40\145\x6d\141\151\x6c\x20\156\157\164\40\x66\157\x75\x6e\144\x2e");
        echo "\x54\x68\x69\163\x20\x61\x64\155\151\x6e\x20\145\155\141\x69\154\40\x6e\157\164\x20\x66\x6f\165\156\144\56\56\x50\154\x65\x61\x73\145\40\143\x6f\x6e\164\141\143\164\x20\x79\157\165\x72\40\101\144\155\151\x6e\151\163\x74\162\141\164\x6f\x72\56";
        exit;
        xB:
        cF:
        if (!$this->spUtility->isBlank($FO)) {
            goto uZ;
        }
        $FO = explode("\100", $EK)[0];
        $FO = preg_replace("\x2f\x5b\x5e\x41\55\x5a\141\x2d\x7a\60\x2d\x39\134\x2d\135\57", '', $FO);
        $this->spUtility->log_debug("\x63\x72\145\141\164\145\116\145\167\x55\x73\145\x72\x28\51\72\x20\103\x68\x61\156\x67\x65\x64\x20\146\151\162\x73\164\116\141\155\x65\x3a\x20" . $FO);
        uZ:
        if (!$this->spUtility->isBlank($Fo)) {
            goto Mf;
        }
        $Fo = explode("\100", $EK)[1];
        $Fo = preg_replace("\57\x5b\136\x41\x2d\x5a\x61\55\x7a\60\x2d\x39\x5c\55\x5d\57", '', $Fo);
        $this->spUtility->log_debug("\143\162\x65\x61\x74\145\x4e\145\x77\125\x73\x65\162\50\x29\72\40\103\150\141\x6e\x67\x65\x64\x20\154\x61\163\164\116\141\x6d\145\x3a\40" . $Fo);
        Mf:
        if ($Ea) {
            goto Ss;
        }
        $Nd = $this->group_mapping;
        $this->spUtility->log_debug("\143\162\145\x61\164\145\116\x65\167\x55\x73\145\162\50\x29\40\x3a\40\x67\162\157\x75\x70\163\x4d\x61\160\160\145\144\x20" . $Nd);
        goto YX;
        Ss:
        $Nd = $this->role_mapping;
        $this->spUtility->log_debug("\143\x72\x65\x61\164\x65\x4e\145\x77\x55\x73\145\x72\x28\51\x20\72\40\162\x6f\x6c\x65\x73\x4d\141\x70\160\x65\x64\x20" . $Nd);
        YX:
        if (!(!empty($this->dontCreateUserIfRoleNotMapped) && strcasecmp($this->dontCreateUserIfRoleNotMapped, SPConstants::enable) === 0)) {
            goto p2;
        }
        if ($this->isRoleMappingConfiguredForUser($Nd, $VC)) {
            goto nT;
        }
        if (!$Ea) {
            goto hM;
        }
        echo "\127\x65\x20\x63\x61\156\x6e\x6f\164\x20\x6c\157\147\x20\x79\x6f\165\40\151\156\x2e\x20\x50\x6c\145\141\163\x65\40\x6d\x61\x70\x20\164\150\x65\40\x72\157\154\x65\163";
        exit;
        hM:
        $this->messageManager->addErrorMessage("\x57\x65\40\x63\141\x6e\x6e\x6f\164\40\x6c\157\x67\40\x79\x6f\x75\x20\x69\x6e\56\x20\x50\154\145\141\163\145\x20\x6d\141\x70\x20\x74\150\145\40\162\x6f\x6c\x65\163");
        if ($this->auto_redirect) {
            goto in;
        }
        $Rs = $this->storeManager->getStore()->getBaseUrl();
        $this->responseFactory->create()->setRedirect($Rs . "\x63\x75\x73\164\x6f\x6d\145\162\57\141\143\143\x6f\165\156\164")->sendResponse();
        exit;
        goto nZ;
        in:
        $Te = $this->getRequest()->getParams();
        $eT = $this->storeManager->getStore()->getBaseUrl();
        if (empty($Te["\122\145\154\141\171\123\164\141\x74\x65"])) {
            goto zz;
        }
        $eT = $Te["\122\145\x6c\x61\171\x53\164\x61\x74\x65"];
        zz:
        $this->responseFactory->create()->setRedirect($eT)->sendResponse();
        exit;
        nZ:
        nT:
        p2:
        $QB = $this->processRoles($YU, $Ea, $Nd, $VC);
        if (!$this->spUtility->isBlank($QB)) {
            goto u8;
        }
        $QB = $this->processDefaultRole($Ea, $YU);
        u8:
        $SD = !$this->spUtility->isBlank($SD) ? $SD : $FO;
        $user = $Ea ? $this->createAdminUser($SD, $FO, $Fo, $EK, $Ju, $QB) : $this->createCustomer($SD, $FO, $Fo, $EK, $Ju, $QB, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA);
        $U1 = $user->getId();
        $this->spUtility->log_debug("\x63\162\145\x61\164\145\116\145\x77\x55\x73\145\x72\x28\51\x3a\x20\x75\163\145\162\x20\x63\162\145\141\164\145\x64\x20\x77\x69\x74\x68\40\x69\x64\x3a\40" . $U1);
        $dD = array("\116\141\155\145\111\x44" => $Au, "\123\x65\x73\x73\x69\157\x6e\111\156\x64\x65\x78" => $this->sessionIndex);
        $this->spUtility->saveConfig("\x65\170\x74\162\x61", $dD, $U1, $Ea);
        $or = $this->spUtility->getCustomerStoreConfig("\145\170\x74\x72\x61", $U1);
        return $user;
    }
    public function generatePassword($E4 = 16)
    {
        $MO = \Magento\Framework\Math\Random::CHARS_LOWERS . \Magento\Framework\Math\Random::CHARS_UPPERS . \Magento\Framework\Math\Random::CHARS_DIGITS . "\43\x24\x25\x26\52\x2e\x3b\x3a\x28\x29\100\x21";
        $this->spUtility->log_debug("\143\x68\x61\x72\163\x3a\x20" . $MO);
        $XM = $this->randomUtility->getRandomString($E4, $MO);
        $this->spUtility->log_debug("\x50\x61\x73\163\x77\157\162\x64\72\40" . $XM);
        return $XM;
    }
    private function isRoleMappingConfiguredForUser($Nd, $VC)
    {
        $this->spUtility->log_debug("\151\163\122\157\x6c\145\115\141\160\160\x69\156\147\103\157\156\x66\151\147\165\x72\145\x64\106\x6f\x72\125\163\x65\162\x3a\x20\x72\x6f\154\x65\x5f\155\141\160\x70\x69\x6e\147\72\40", $Nd);
        $this->spUtility->log_debug("\x69\163\122\x6f\154\x65\x4d\141\x70\160\x69\156\x67\103\x6f\156\x66\x69\x67\165\162\145\x64\x46\x6f\162\x55\163\x65\162\x3a\x20\147\162\157\165\160\116\141\155\x65\72\40", $VC);
        if (!$Nd) {
            goto a2;
        }
        $Nd = json_decode($Nd);
        a2:
        $Ah = null;
        if (!(empty($VC) || empty($Nd))) {
            goto d5;
        }
        return null;
        d5:
        foreach ($Nd as $SP => $ec) {
            $DE = explode("\73", $ec);
            foreach ($DE as $sC) {
                if (empty($sC)) {
                    goto gl;
                }
                foreach ($VC as $tk) {
                    if (!($tk == $sC)) {
                        goto UB;
                    }
                    $Ah = $SP;
                    return $Ah;
                    UB:
                    w7:
                }
                cM:
                gl:
                XO:
            }
            uh:
            DR:
        }
        yN:
        return false;
    }
    private function createAdminUser($SD, $FO, $Fo, $EK, $Ju, $Kq)
    {
        if (!($Kq === "\x30" || $Kq == null)) {
            goto Tu;
        }
        $Kq = "\61";
        Tu:
        $this->spUtility->log_debug("\120\162\157\143\x65\163\x73\x55\x73\145\162\101\143\x74\151\x6f\156\40\x3a\40\143\x72\x65\x61\164\145\x41\144\155\151\x6e\125\x73\145\162\50\51\72\40");
        if (!(strlen($SD) >= 40)) {
            goto Kz;
        }
        $SD = substr($SD, 0, 40);
        Kz:
        $QF = ["\x75\163\145\x72\x6e\x61\x6d\145" => $SD, "\x66\151\162\163\x74\x6e\x61\155\x65" => $FO, "\154\141\x73\x74\x6e\x61\x6d\x65" => $Fo, "\x65\155\x61\151\x6c" => $EK, "\160\x61\x73\x73\167\157\162\x64" => $Ju, "\151\156\x74\145\162\146\141\143\x65\x5f\x6c\x6f\143\141\154\145" => "\x65\156\x5f\125\x53", "\x69\x73\x5f\141\143\164\151\x76\145" => 1];
        $Kq = $this->spUtility->isBlank($Kq) ? 1 : $Kq;
        $this->spUtility->log_debug("\120\162\157\143\145\x73\x73\125\163\145\x72\101\143\164\x69\x6f\x6e\x20\72\x20\x63\162\x65\141\164\x65\101\x64\155\151\156\125\163\x65\x72\40\x31\72\40");
        $user = $this->userFactory->create();
        $user->setData($QF);
        $user->setRoleId($Kq);
        $user->save();
        return $user;
    }
    private function createCustomer($SD, $FO, $Fo, $EK, $Ju, $Kq, $Kk, $jF, $Gb, $TE, $eC, $vA, $C0, $rB, $Xx, $VB, $iW, $KA)
    {
        if (!($Kq === "\60" || $Kq == null)) {
            goto KP;
        }
        $Kq = "\61";
        KP:
        $this->spUtility->log_debug("\x63\x72\x65\141\164\x65\103\165\163\164\x6f\155\145\x72\x28\51\x3a\x20\145\155\x61\151\154\x3a\x20" . $EK);
        $Kq = $this->spUtility->isBlank($Kq) ? 1 : $Kq;
        $this->spUtility->log_debug("\x63\x72\x65\x61\164\145\103\x75\x73\x74\x6f\x6d\x65\x72\x28\51\72\x20\162\x6f\x6c\145\x20\141\x73\x73\151\x67\156\x65\144\72\x20" . $Kq);
        $vH = $this->spUtility->getCurrentStore();
        $Ed = $this->spUtility->getCurrentWebsiteId();
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\x64\x70\x5f\156\141\155\145"] === $rq)) {
                goto Ab;
            }
            $hR = $ub->getData();
            Ab:
            cz:
        }
        pp:
        $Sn = $this->customerFactory->create()->setWebsiteId($Ed)->setStore($vH)->setFirstname($FO)->setLastname($Fo)->setEmail($EK)->setPassword($Ju)->setGroupId($Kq);
        $fs = array_Keys((array) $this->attrs);
        $th = json_decode($hR["\143\165\x73\164\x6f\155\137\141\x74\164\x72\x69\142\165\164\145\x73"]);
        $Tc = array_values((array) $th);
        $g_ = array_intersect($fs, $Tc);
        foreach ($g_ as $bs) {
            $FY = array_search($bs, (array) $th);
            $A2 = $this->attrs[$bs][0];
            $Sn->setData($FY, $A2);
            Hw:
        }
        Xj:
        $Sn->save();
        $fs = array_Keys((array) $this->attrs);
        $this->spUtility->log_debug("\x61\x74\x74\162\151\142\x75\x74\145\x73\x20\x6b\x65\x79\x73\x20", $fs);
        $th = json_decode((string) $this->spUtility->getStoreConfig(SPConstants::CUSTOM_MAPPED));
        $Tc = array_values((array) $th);
        $g_ = array_intersect($fs, $Tc);
        $this->spUtility->log_debug("\x20\125\160\x64\141\x74\145\40\165\x73\145\162\47\163\x20\143\x75\x73\164\x6f\x6d\40\141\164\x74\x72\151\x62\x75\164\x65\x73");
        $U1 = $Sn->getId();
        if (!($TE || $Kk || $eC || $jF || $vA || $Gb)) {
            goto yC;
        }
        $nS = $this->dataAddressFactory->create();
        $nS->setFirstname($FO);
        $nS->setLastname($Fo);
        if (empty($TE)) {
            goto mj;
        }
        $nS->setTelephone($TE);
        mj:
        if (empty($Kk)) {
            goto M7;
        }
        $nS->setStreet($Kk);
        M7:
        if (empty($eC)) {
            goto v7;
        }
        $nS->setCity($eC);
        v7:
        if (empty($jF)) {
            goto yh;
        }
        $M3 = $this->collectionFactory->create()->addRegionNameFilter($jF)->getFirstItem()->toArray();
        if (empty($M3["\x72\145\147\151\x6f\x6e\x5f\151\144"])) {
            goto kq;
        }
        $nS->setRegionId($M3["\x72\x65\147\151\157\156\x5f\x69\x64"]);
        kq:
        yh:
        if (empty($vA)) {
            goto qm;
        }
        $nS->setCountryId($vA);
        qm:
        if (empty($Gb)) {
            goto d8;
        }
        $nS->setPostcode($Gb);
        d8:
        $nS->setIsDefaultBilling("\61");
        $nS->setSaveInAddressBook("\x31");
        $nS->setCustomerId($Sn->getId());
        try {
            $nS->save();
            $Sn = $nS->getCustomer();
        } catch (\Exception $cA) {
            $this->spUtility->log_debug("\x41\156\40\145\162\162\157\x72\40\157\143\x63\x75\162\x72\145\x64\x20\x77\x68\x69\154\145\x20\164\162\171\151\156\x67\x20\164\157\x20\x73\145\164\40\x61\x64\144\x72\x65\x73\x73\72\40{$cA->getMessage()}");
        }
        yC:
        if (!($VB || $C0 || $iW || $rB || $KA || $Xx)) {
            goto ZY;
        }
        $nS = $this->dataAddressFactory->create();
        $nS->setFirstname($FO);
        $nS->setLastname($Fo);
        if (empty($VB)) {
            goto sb;
        }
        $nS->setTelephone($VB);
        sb:
        if (empty($C0)) {
            goto YU;
        }
        $nS->setStreet($C0);
        YU:
        if (empty($iW)) {
            goto xE;
        }
        $nS->setCity($iW);
        xE:
        if (empty($rB)) {
            goto be;
        }
        $M3 = $this->collectionFactory->create()->addRegionNameFilter($rB)->getFirstItem()->toArray();
        if (empty($M3["\162\x65\x67\x69\157\156\x5f\151\144"])) {
            goto Z_;
        }
        $nS->setRegionId($M3["\162\x65\147\151\157\156\x5f\x69\144"]);
        Z_:
        be:
        if (empty($KA)) {
            goto PE;
        }
        $nS->setCountryId($KA);
        PE:
        if (empty($Xx)) {
            goto LU;
        }
        $nS->setPostcode($Xx);
        LU:
        $nS->setIsDefaultShipping("\x31");
        $nS->setSaveInAddressBook("\61");
        $nS->setCustomerId($Sn->getId());
        try {
            $nS->save();
            $Sn = $nS->getCustomer();
        } catch (\Exception $cA) {
            $this->spUtility->log_debug("\101\x6e\40\x65\x72\162\x6f\162\40\157\143\143\x75\x72\162\x65\144\x20\x77\150\x69\154\x65\40\x74\x72\x79\x69\x6e\x67\x20\164\157\x20\x73\145\x74\x20\x61\144\144\x72\145\163\163\72\40{$cA->getMessage()}");
        }
        ZY:
        $this->updateCustomAttributes($Sn, $EK);
        return $Sn;
    }
    private function updateCustomAttributes($user, $Dl)
    {
        if (!(!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0)) {
            goto la;
        }
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x70\x72\x6f\143\145\x73\163\x55\163\145\162\101\143\164\x69\x6f\x6e", $rq);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\144\x70\137\156\141\155\145"] === $rq)) {
                goto UZ;
            }
            $hR = $ub->getData();
            UZ:
            L2:
        }
        xW:
        $this->spUtility->log_debug("\40\111\156\x20\x75\x70\x64\141\164\145\x43\165\163\164\157\155\x41\x74\164\x72\x69\142\165\164\x65\x20\146\165\x6e\143\164\151\x6f\156");
        $U1 = $user->getId();
        $this->spUtility->log_debug("\165\x73\145\162\x20\151\144", $U1);
        $Ea = is_a($user, "\134\115\x61\x67\145\156\164\157\x5c\125\x73\145\162\134\115\157\144\x65\x6c\134\125\x73\x65\x72") ? TRUE : FALSE;
        $fs = array_Keys((array) $this->attrs);
        $this->spUtility->log_debug("\x61\164\x74\x72\151\x62\x75\x74\145\163\x20\153\x65\171\163\x20", $fs);
        $th = json_decode($hR["\143\x75\x73\x74\157\x6d\137\x61\x74\164\x72\151\x62\x75\164\x65\x73"]);
        $Tc = array_values((array) $th);
        foreach ((array) $th as $pb => $VP) {
            $oC = $this->eavConfig->getEntityType("\x63\x75\x73\164\157\x6d\145\x72")->getId();
            $cX = $this->eavConfig->getAttribute($oC, $pb);
            if ($cX && $cX->getId()) {
                goto zO;
            }
            $this->spUtility->log_debug("{$pb}\x20\143\165\163\164\x6f\x6d\x20\141\164\x74\162\x69\142\165\x74\x65\40\x6e\157\164\x20\160\x72\145\x73\145\156\x74");
            zO:
            Vx:
        }
        i1:
        $g_ = array_intersect($fs, $Tc);
        $this->spUtility->log_debug("\40\125\160\x64\x61\x74\145\x20\165\x73\x65\162\47\163\x20\x63\165\163\164\157\155\40\141\164\164\x72\151\142\x75\164\145\163");
        foreach ($g_ as $bs) {
            $FY = array_search($bs, (array) $th);
            $A2 = $this->attrs[$bs][0];
            $Ed = $this->spUtility->getCurrentWebsiteId();
            $this->spUtility->log_debug("\x63\165\x73\x74\x6f\x6d\40\141\x74\164\162\x69\142\x75\x74\145\x20\x3d\x20{$FY}\x20\x61\156\144\x20\143\x75\x73\164\157\155\x20\166\141\154\x75\x65\x20\x3d\x20{$A2}\x20");
            $Sn = $this->customerFactory->create();
            $Sn->setWebsiteId($Ed);
            $Sn->loadByEmail($Dl);
            $Sn->setData($FY, $A2);
            $Sn->save();
            UV:
        }
        tq:
        la:
        return $user;
    }
    private function getCustomerFromAttributes($Dl)
    {
        $Ed = $this->spUtility->getCurrentWebsiteId();
        $RZ = $this->urlBuilder->getCurrentUrl();
        $this->spUtility->log_debug("\147\145\164\x43\x75\x73\x74\157\155\145\x72\x46\x72\x6f\x6d\101\x74\x74\x72\151\142\165\x74\145\x73\x28\x29\x3a\x20\x63\165\x72\162\x65\x6e\164\40\120\x61\147\145\40\125\162\x6c\x3a\x20" . $RZ);
        try {
            $this->spUtility->log_debug("\x67\x65\164\x43\165\163\x74\x6f\155\x65\x72\x46\x72\157\155\x41\164\x74\x72\x69\142\165\x74\145\163\50\51\x3a\40\143\x75\x73\x74\157\155\145\162\72\x20" . $Dl);
            $this->spUtility->log_debug("\x67\x65\x74\x43\x75\x73\x74\157\155\145\x72\106\x72\x6f\155\x41\164\164\162\151\142\165\164\x65\x73\50\x29\72\x20\127\x65\x62\163\151\164\x65\111\x64\x3a\x20" . $Ed);
            $Sn = $this->customerRepository->get($Dl, $Ed);
            return !is_null($Sn) ? $Sn : FALSE;
        } catch (NoSuchEntityException $IR) {
            $this->spUtility->log_debug("\147\145\x74\x43\x75\163\164\157\x6d\x65\162\x46\x72\157\155\x41\x74\x74\x72\x69\142\165\x74\145\163\50\x29\x3a\x20\x63\x61\164\143\150\40");
            return FALSE;
        }
    }
    private function redirectToBackendAndLogin($U1, $lr, $Nf)
    {
        $this->spUtility->setAdminSessionData("\x61\x64\x6d\151\156\137\x70\157\163\164\137\154\x6f\x67\x6f\165\164", 1);
        $bG = "\141\x64\x6d\x69\x6e\x68\x74\155\x6c";
        $p7 = $U1;
        $this->_request->setPathInfo("\x2f\x61\144\x6d\x69\156");
        $this->spUtility->log_debug("\162\145\144\151\162\145\143\164\124\157\102\141\143\153\145\156\144\x41\156\144\114\x6f\x67\151\x6e\72\x20\x75\x73\145\162\x3a\40" . $p7);
        try {
            $this->_state->setAreaCode($bG);
        } catch (LocalizedException $cA) {
        }
        $this->_objectManager->configure($this->_configLoader->load($bG));
        $user = $this->_objectManager->get("\x4d\141\147\145\x6e\164\x6f\134\125\163\145\162\134\115\157\x64\x65\x6c\x5c\125\163\145\162")->loadByUsername($p7);
        $yH = $this->_objectManager->get("\115\141\x67\145\x6e\x74\157\x5c\102\141\x63\153\x65\x6e\x64\134\115\x6f\144\145\154\134\x41\x75\164\x68\134\123\145\x73\163\x69\x6f\x6e");
        $yH->setUser($user);
        $yH->processLogin();
        $this->spUtility->reinitconfig();
        if (!$yH->isLoggedIn()) {
            goto Ra;
        }
        $this->spUtility->log_debug("\x72\x65\144\151\x72\x65\143\x74\124\x6f\x42\x61\x63\x6b\145\156\144\101\x6e\x64\x4c\157\147\x69\x6e\72\40\151\163\114\x6f\147\147\145\x64\111\156\x3a\x20\164\162\x75\x65");
        $Xn = $this->_objectManager->get("\115\141\x67\145\x6e\x74\157\x5c\x46\x72\141\x6d\x65\167\157\162\x6b\134\123\164\144\154\x69\142\134\x43\x6f\157\x6b\151\x65\x4d\141\156\x61\x67\x65\162\111\156\164\x65\162\x66\141\x63\x65");
        $Pl = $yH->getSessionId();
        if (!$Pl) {
            goto aQ;
        }
        $this->spUtility->log_debug("\x72\x65\x64\151\162\x65\143\164\124\157\x42\x61\x63\x6b\145\x6e\144\x41\x6e\144\114\157\x67\x69\x6e\72\x20\143\157\x6f\x6b\151\x65\x56\141\154\165\x65\72\x20\x74\x72\x75\x65");
        $ok = $this->_objectManager->get("\x4d\x61\x67\x65\x6e\x74\x6f\134\102\141\x63\x6b\x65\x6e\x64\134\x4d\157\144\145\x6c\x5c\123\145\163\163\151\x6f\x6e\x5c\x41\144\x6d\151\156\103\x6f\x6e\146\x69\147");
        $Ol = str_replace("\x61\165\164\157\x6c\x6f\x67\x69\156\56\x70\x68\x70", "\x69\156\x64\145\x78\56\160\150\x70", $ok->getCookiePath());
        $NV = $this->_objectManager->get("\x4d\141\x67\x65\x6e\x74\x6f\x5c\106\x72\141\x6d\145\167\157\x72\x6b\134\x53\x74\x64\x6c\x69\x62\134\103\x6f\157\153\x69\x65\x5c\103\157\x6f\x6b\151\145\x4d\145\164\141\x64\x61\164\x61\x46\x61\143\164\157\162\171")->createPublicCookieMetadata()->setDuration(3600)->setPath($Ol)->setDomain($ok->getCookieDomain())->setSecure($ok->getCookieSecure())->setHttpOnly($ok->getCookieHttpOnly());
        $Xn->setPublicCookie($ok->getName(), $Pl, $NV);
        if (!class_exists("\115\141\x67\x65\x6e\x74\157\x5c\x53\145\x63\x75\162\x69\x74\171\134\x4d\x6f\144\145\154\134\x41\144\x6d\x69\x6e\123\145\x73\163\151\x6f\156\163\x4d\141\x6e\141\x67\145\162")) {
            goto DQ;
        }
        $this->spUtility->log_debug("\162\x65\x64\x69\162\145\x63\x74\x54\x6f\x42\141\x63\153\145\156\x64\101\x6e\x64\x4c\157\147\151\x6e\x3a\x20\x63\154\141\x73\163\40\x65\170\x69\x73\164\x20\101\144\155\x69\156\x53\145\163\x73\x69\x6f\x6e\163\115\x61\156\x61\x67\x65\162\x3a\40\164\x72\x75\145");
        $aw = $this->_objectManager->get("\115\x61\x67\145\x6e\164\x6f\x5c\x53\145\x63\165\162\151\x74\x79\134\x4d\x6f\144\145\x6c\134\x41\x64\155\151\156\x53\145\163\163\x69\157\x6e\x73\x4d\141\x6e\x61\x67\x65\x72");
        $aw->processLogin();
        DQ:
        aQ:
        $Qi = $this->HelperBackend->getHomePageUrl();
        header("\x4c\157\x63\x61\164\x69\157\156\x3a\x20\40" . $Qi);
        exit;
        Ra:
        $this->_objectManager->configure($this->_configLoader->load($bG));
        $user = $this->_objectManager->get("\x4d\x61\147\145\x6e\164\x6f\x5c\x55\x73\x65\x72\134\115\157\x64\145\154\x5c\x55\163\x65\x72")->loadByUsername($p7);
        $yH = $this->_objectManager->get("\115\x61\147\145\x6e\164\x6f\134\102\x61\x63\x6b\x65\x6e\144\x5c\115\x6f\144\x65\154\x5c\101\165\164\x68\x5c\x53\145\x73\x73\151\x6f\156");
        $yH->setUser($user);
        $yH->processLogin();
        if (!$yH->isLoggedIn()) {
            goto Zg;
        }
        $this->spUtility->log_debug("\x72\x65\x64\151\x72\145\x63\x74\124\x6f\x42\x61\143\153\145\156\144\101\156\x64\x4c\157\147\151\x6e\x3a\40\151\x73\114\157\x67\x67\145\144\x49\x6e\72\x20\x74\162\x75\x65");
        $Xn = $this->_objectManager->get("\x4d\141\147\145\156\164\157\134\106\x72\141\155\145\x77\x6f\162\153\134\x53\164\144\x6c\x69\142\x5c\103\x6f\157\153\151\145\115\x61\156\141\147\x65\x72\x49\x6e\164\x65\162\x66\141\x63\x65");
        $Pl = $yH->getSessionId();
        if (!$Pl) {
            goto L0;
        }
        $this->spUtility->log_debug("\162\145\x64\x69\x72\145\143\x74\x54\157\x42\141\x63\x6b\x65\x6e\x64\101\x6e\x64\114\157\x67\151\156\72\x20\x63\157\157\x6b\x69\x65\x56\x61\154\165\145\x3a\x20\164\x72\x75\145");
        $ok = $this->_objectManager->get("\115\x61\147\145\x6e\x74\157\x5c\x42\141\143\153\x65\x6e\x64\134\x4d\x6f\x64\145\154\134\123\145\x73\163\x69\157\x6e\x5c\x41\144\x6d\151\x6e\103\157\156\x66\151\147");
        $Ol = str_replace("\x61\x75\x74\x6f\154\x6f\147\151\x6e\x2e\160\x68\160", "\151\156\x64\x65\x78\56\160\150\x70", $ok->getCookiePath());
        $NV = $this->_objectManager->get("\115\141\x67\x65\x6e\x74\x6f\x5c\x46\x72\141\x6d\x65\x77\157\x72\x6b\x5c\123\164\144\x6c\x69\x62\x5c\103\x6f\157\x6b\151\145\134\103\157\157\153\151\x65\115\145\x74\141\x64\141\164\141\106\x61\143\x74\157\162\x79")->createPublicCookieMetadata()->setDuration(3600)->setPath($Ol)->setDomain($ok->getCookieDomain())->setSecure($ok->getCookieSecure())->setHttpOnly($ok->getCookieHttpOnly());
        $Xn->setPublicCookie($ok->getName(), $Pl, $NV);
        L0:
        $At = $this->spUtility->getAdminUrl("\141\x64\x6d\151\x6e\57\x64\x61\x73\x68\142\157\x61\162\x64\x2f\x69\156\x64\145\x78");
        $this->spUtility->log_debug("\x72\x65\x64\x69\x72\145\x63\x74\x54\x6f\x42\141\143\153\x65\156\144\101\x6e\x64\114\157\x67\x69\156\x3a\40\x66\x69\156\x61\154\125\162\154\72\x20" . $At);
        $this->messageManager->addSuccessMessage("\131\157\x75\40\x61\162\145\x20\154\157\x67\147\x65\144\x20\x69\156\x20\x73\165\143\x63\145\163\x73\x66\165\154\x6c\x79\x2e");
        $this->responseFactory->create()->setRedirect($At)->sendResponse();
        Zg:
    }
    public function setRelayState($Nf)
    {
        $this->relayState = $Nf;
        return $this;
    }
    public function setAttrs($q_)
    {
        $this->attrs = $q_;
        return $this;
    }
    public function setSessionIndex($lr)
    {
        $this->sessionIndex = $lr;
        return $this;
    }
    private function checkIfB2BFlow()
    {
        $XC = $this->spUtility->getStoreConfig(SPConstants::B2B_STORE_URL);
        if ($this->spUtility->isBlank($XC)) {
            goto RE;
        }
        $X3 = strpos($this->relayState, $XC);
        if (!($X3 !== false && !$this->spUtility->isBlank($this->accountId))) {
            goto ZQ;
        }
        $this->spUtility->log_debug("\143\150\x65\143\153\x49\x66\x42\62\102\106\154\x6f\x77\x3a\x20\102\x32\x42\x20\106\154\x6f\x77\x20\x46\x6f\165\x6e\144\x3a\x20");
        return true;
        ZQ:
        RE:
        $this->spUtility->log_debug("\x63\150\145\x63\x6b\x49\146\102\62\x42\106\x6c\157\x77\x3a\x20\x4e\x6f\164\x20\102\x32\x42\x20\x66\154\x6f\x77\x2e\x2e\x20\103\x6f\x6e\164\151\x6e\165\x69\x6e\147\x20\167\x69\164\150\x20\102\x32\103\72\40");
        return false;
    }
    private function generateEmail($SD)
    {
        $hx = $this->spUtility->getBaseUrl();
        $hx = substr($hx, strpos($hx, "\x2f\57"), strlen($hx) - 1);
        return $SD . "\100" . $hx;
    }
}
