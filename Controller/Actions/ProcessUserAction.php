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

/**
 * This action class processes the user attributes coming in
 * the SAML response to either log the customer or admin in
 * to their respective dashboard or create a customer or admin
 * based on the default role set by the admin and log them in
 * automatically.
 *
 * @todo refactor and optimize this class code
 */
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
    /**
     * @var \Magento\Backend\Helper\Data
     */
    private $HelperBackend;

    public function __construct(
        ManagerInterface                                           $messageManager,
        Context                                                    $context,
        SPUtility                                                  $spUtility,
        Index                                                      $index,
        \Magento\Customer\Model\ResourceModel\Group\Collection     $userGroupModel,
        \Magento\Authorization\Model\ResourceModel\Role\Collection $adminRoleModel,
        User                                                       $adminUserModel,
        Customer                                                   $customerModel,
        CustomerRepositoryInterface                                $customerRepository,
        StoreManagerInterface                                      $storeManager,
        ResponseFactory                                            $responseFactory,
        CustomerLoginAction                                        $customerLoginAction,
        CustomerFactory                                            $customerFactory,
        UserFactory                                                $userFactory,
        Random                                                     $randomUtility,
        State                                                      $_state,
        ConfigLoaderInterface                                      $_configLoader,
        Data                                                       $HelperBackend,
        AddressFactory                                             $dataAddressFactory,
        AddressFactory                                             $addressFactory,
        ResultFactory                                              $resultFactory,
        CollectionFactory                                          $collectionFactory,
        Config                                                     $eavConfig,
        UrlInterface                                               $urlBuilder
    )
    {
        //You can use dependency injection to get any class this observer may need.
        $this->customerModel = $customerModel;
        $this->index = $index;
        $this->messageManager = $messageManager;
        $this->userGroupModel = $userGroupModel;
        $this->adminRoleModel = $adminRoleModel;
        $this->adminUserModel = $adminUserModel;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->responseFactory = $responseFactory;
        $this->customerLoginAction = $customerLoginAction;
        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
        $this->randomUtility = $randomUtility;
        $this->_state = $_state;
        $this->HelperBackend = $HelperBackend;
        $this->_configLoader = $_configLoader;
        $this->dataAddressFactory = $dataAddressFactory;
        $this->_addressFactory = $addressFactory;
        $this->collectionFactory = $collectionFactory;
        $this->eavConfig = $eavConfig;
        $this->urlBuilder = $urlBuilder;
        $this->b2bUser = false;
        parent::__construct($context, $spUtility, $storeManager, $resultFactory, $responseFactory);
    }


    /**
     * Execute function to execute the classes function.
     *
     * @throws MissingAttributesException
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute()
    {


        $this->spUtility->log_debug(" inside class ProcessUserAction : execute: ");
        // throw an exception if attributes are empty
        if (empty($this->attrs)) throw new MissingAttributesException;
        // get and set all the necessary attributes

        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("processUserAction", $idp_name);
        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idp_name) {
                $idpDetails = $item->getData();
            }
        }

        $this->emailAttribute = $idpDetails['email_attribute'];
        $this->spUtility->log_debug("email attribute", $this->emailAttribute);
        $this->usernameAttribute = $idpDetails['username_attribute'];
        $this->firstNameKey = $idpDetails['firstname_attribute'];
        $this->lastNameKey = $idpDetails['lastname_attribute'];
        $this->groupNameKey = $idpDetails['group_attribute'];
        $this->companyIdKey = $idpDetails['b2b_attribute'];
        $this->checkIfMatchBy = $idpDetails['create_magento_account_by'];
        $this->dontCreateUserIfRoleNotMapped = $idpDetails['do_not_autocreate_if_roles_not_mapped'];
        $this->spUtility->log_debug("do_not_autocreate_if_roles_not_mapped: ", $this->dontCreateUserIfRoleNotMapped);

        $this->billingcountryNameKey = $idpDetails['billing_country_attribute'];
        $this->billingcountryNameKey = $this->spUtility->isBlank($this->billingcountryNameKey) ? SPConstants::MAP_COUNTRY_BILLING : $this->billingcountryNameKey;

        $this->spUtility->log_debug("billingcountryNameKey: ");

        $this->billingcityNameKey = $idpDetails['billing_city_attribute'];
        $this->billingcityNameKey = $this->spUtility->isBlank($this->billingcityNameKey) ? SPConstants::MAP_CITY_BILLING : $this->billingcityNameKey;

        $this->spUtility->log_debug("billingcityNameKey: ");

        $this->billingphoneNameKey = $idpDetails['billing_phone_attribute'];
        $this->billingphoneNameKey = $this->spUtility->isBlank($this->billingphoneNameKey) ? SPConstants::MAP_PHONE_BILLING : $this->billingphoneNameKey;

        $this->spUtility->log_debug("billingphoneNameKey: ", $this->billingphoneNameKey);

        $this->billingstreetAddressNameKey = $idpDetails['billing_address_attribute'];
        $this->billingstreetAddressNameKey = $this->spUtility->isBlank($this->billingstreetAddressNameKey) ? SPConstants::MAP_ADDRESS_BILLING : $this->billingstreetAddressNameKey;

        $this->spUtility->log_debug("billingstreetAddressNameKey: ", $this->billingstreetAddressNameKey);

        $this->billingmapStateNameKey = $idpDetails['billing_state_attribute'];
        $this->billingmapStateNameKey = $this->spUtility->isBlank($this->billingmapStateNameKey) ? SPConstants::MAP_STATE_BILLING : $this->billingmapStateNameKey;

        $this->spUtility->log_debug("billingmapStateNameKey: ", $this->billingmapStateNameKey);

        $this->billingzipCodeNameKey = $idpDetails['billing_zip_attribute'];
        $this->billingzipCodeNameKey = $this->spUtility->isBlank($this->billingzipCodeNameKey) ? SPConstants::MAP_ZIPCODE_BILLING : $this->billingzipCodeNameKey;

        $this->spUtility->log_debug("billingzipCodeNameKey: ", $this->billingzipCodeNameKey);


        $this->shippingcountryNameKey = $idpDetails['shipping_country_attribute'];
        $this->shippingcountryNameKey = $this->spUtility->isBlank($this->shippingcountryNameKey) ? SPConstants::MAP_COUNTRY_SHIPPING : $this->shippingcountryNameKey;

        $this->spUtility->log_debug("shippingcountryNameKey: ", $this->shippingcountryNameKey);

        $this->shippingcityNameKey = $idpDetails['shipping_city_attribute'];
        $this->shippingcityNameKey = $this->spUtility->isBlank($this->shippingcityNameKey) ? SPConstants::MAP_CITY_SHIPPING : $this->shippingcityNameKey;

        $this->spUtility->log_debug("shippingcityNameKey: ", $this->shippingcityNameKey);

        $this->shippingphoneNameKey = $idpDetails['shipping_phone_attribute'];
        $this->shippingphoneNameKey = $this->spUtility->isBlank($this->shippingphoneNameKey) ? SPConstants::MAP_PHONE_SHIPPING : $this->shippingphoneNameKey;

        $this->spUtility->log_debug("shippingphoneNameKey: ", $this->shippingphoneNameKey);

        $this->shippingstreetAddressNameKey = $idpDetails['shipping_address_attribute'];
        $this->shippingstreetAddressNameKey = $this->spUtility->isBlank($this->shippingstreetAddressNameKey) ? SPConstants::MAP_ADDRESS_SHIPPING : $this->shippingstreetAddressNameKey;

        $this->spUtility->log_debug("shippingstreetAddressNameKey: ", $this->shippingstreetAddressNameKey);

        $this->shippingmapStateNameKey = $idpDetails['shipping_state_attribute'];
        $this->shippingmapStateNameKey = $this->spUtility->isBlank($this->shippingmapStateNameKey) ? SPConstants::MAP_STATE_SHIPPING : $this->shippingmapStateNameKey;

        $this->spUtility->log_debug("shippingmapStateNameKey: ", $this->shippingmapStateNameKey);

        $this->shippingzipCodeNameKey = $idpDetails['shipping_zip_attribute'];
        $this->shippingzipCodeNameKey = $this->spUtility->isBlank($this->shippingzipCodeNameKey) ? SPConstants::MAP_ZIPCODE_SHIPPING : $this->shippingzipCodeNameKey;

        $this->custom_attributes = $idpDetails['custom_attributes'];
        $this->custom_tablename = $idpDetails['custom_tablename'];

        $this->spUtility->log_debug("custom_tablename: ", $this->custom_tablename);

        $this->defaultRole = $idpDetails['default_role'];
        $this->defaultGroup = $idpDetails['default_group'];

        $this->spUtility->log_debug("defaultGroup: ", $this->defaultGroup);

        $this->role_mapping = $idpDetails['roles_mapped'];
        $this->group_mapping = $idpDetails['groups_mapped'];
        $this->spUtility->log_debug("group_mapping: ", print_r($this->group_mapping, true));

        $this->updateRole = $idpDetails['update_backend_roles_on_sso'];
        $this->updateFrontendRole = $idpDetails['update_frontend_groups_on_sso'];
        $this->updateAttribute = $idpDetails['update_attributes_on_login'];
        $this->spUtility->log_debug("update_attributes_on_login: ", $this->updateAttribute);
        $this->autoCreateAdminUser = $idpDetails['auto_create_admin_users'];
        $this->autoCreateCustomer = $idpDetails['auto_create_customers'];
        $this->auto_redirect = $idpDetails['auto_redirect_to_idp'];


        $user_email = !empty($this->attrs[$this->emailAttribute]) ? $this->attrs[$this->emailAttribute][0] : null;
        $firstName = !empty($this->attrs[$this->firstNameKey]) ? $this->attrs[$this->firstNameKey][0] : null;
        $lastName = !empty($this->attrs[$this->lastNameKey]) ? $this->attrs[$this->lastNameKey][0] : null;
        $userName = !empty($this->attrs[$this->usernameAttribute]) ? $this->attrs[$this->usernameAttribute][0] : null;
        $groups = !empty($this->attrs[$this->groupNameKey]) ? $this->attrs[$this->groupNameKey] : null;
        $groupName = !empty($this->attrs[$this->groupNameKey]) ? $this->attrs[$this->groupNameKey] : null;

        $billingcountryName = !empty($this->attrs[$this->billingcountryNameKey]) ? $this->attrs[$this->billingcountryNameKey][0] : null;
        $billingcity = !empty($this->attrs[$this->billingcityNameKey]) ? $this->attrs[$this->billingcityNameKey][0] : null;
        $billingphone = !empty($this->attrs[$this->billingphoneNameKey]) ? $this->attrs[$this->billingphoneNameKey][0] : null;
        $billingstreetAddress = !empty($this->attrs[$this->billingstreetAddressNameKey]) ? $this->attrs[$this->billingstreetAddressNameKey][0] : null;
        $billingmapState = !empty($this->attrs[$this->billingmapStateNameKey]) ? $this->attrs[$this->billingmapStateNameKey][0] : null;
        $billingzipCode = !empty($this->attrs[$this->billingzipCodeNameKey]) ? $this->attrs[$this->billingzipCodeNameKey][0] : null;


        $shippingcountryName = !empty($this->attrs[$this->shippingcountryNameKey]) ? $this->attrs[$this->shippingcountryNameKey][0] : null;
        $shippingcity = !empty($this->attrs[$this->shippingcityNameKey]) ? $this->attrs[$this->shippingcityNameKey][0] : null;
        $shippingphone = !empty($this->attrs[$this->shippingphoneNameKey]) ? $this->attrs[$this->shippingphoneNameKey][0] : null;
        $shippingstreetAddress = !empty($this->attrs[$this->shippingstreetAddressNameKey]) ? $this->attrs[$this->shippingstreetAddressNameKey][0] : null;
        $shippingmapState = !empty($this->attrs[$this->shippingmapStateNameKey]) ? $this->attrs[$this->shippingmapStateNameKey][0] : null;
        $shippingzipCode = !empty($this->attrs[$this->shippingzipCodeNameKey]) ? $this->attrs[$this->shippingzipCodeNameKey][0] : null;

        $params = $this->getRequest()->getParams();

        if ($this->spUtility->isBlank($this->checkIfMatchBy)) $this->checkIfMatchBy = SPConstants::DEFAULT_MAP_BY;
        $this->spUtility->log_debug("checkIfMatchBy: ", $this->checkIfMatchBy);
        $firstGroup = "";

        if (is_array($groups))
            $firstGroup = $groups;
        else
            $firstGroup = $this->spUtility->isBlank(json_decode((string)$groups)) ? $groups : json_decode((string)$groups)[0];

        $this->groupNameKey = $firstGroup;
        $this->spUtility->log_debug("FirstGroup from IdP (if Multiple): ");

        $billingcountryCode = null;
        $shippingcountryCode = null;

        if ($billingcountryName)
            $billingcountryCode = $this->getCountryCodeBasedOnMapping($billingcountryName);


        if ($shippingcountryName)
            $shippingcountryCode = $this->getCountryCodeBasedOnMapping($shippingcountryName);

        // process the user
        $this->processUserAction($user_email, $firstName, $lastName, $userName, $firstGroup,
            $this->defaultGroup, $this->checkIfMatchBy, $this->attrs['NameID'][0], $params, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode);
    }

    public function getCountryCodeBasedOnMapping($value)
    {

        $countryIDArray = [
            "AFGHANISTAN" => "AF",
            "ÅLAND ISLANDS" => "AX",
            "ALBANIA" => "AL",
            "ALGERIA" => "DZ",
            "AMERICAN SAMOA" => "AS",
            "ANDORRA" => "AD",
            "ANGOLA" => "AO",
            "ANGUILLA" => "AI",
            "ANTARCTICA" => "AQ",
            "ANTIGUA AND BARBUDA" => "AG",
            "ARGENTINA" => "AR",
            "ARMENIA" => "AM",
            "ARUBA" => "AW",
            "AUSTRALIA" => "AU",
            "AUSTRIA" => "AT",
            "AZERBAIJAN" => "AZ",
            "BAHAMAS" => "BS",
            "BAHRAIN" => "BH",
            "BANGLADESH" => "BD",
            "BARBADOS" => "BB",
            "BELARUS" => "BY",
            "BELGIUM" => "BE",
            "BELIZE" => "BZ",
            "BENIN" => "BJ",
            "BERMUDA" => "BM",
            "BHUTAN" => "BT",
            "BOLIVIA" => "BO",
            "BOSNIA AND HERZEGOVINA" => "BA",
            "BOTSWANA" => "BW",
            "BOUVET ISLAND" => "BV",
            "BRAZIL" => "BR",
            "BRITISH INDIAN OCEAN TERRITORY" => "IO",
            "BRITISH VIRGIN ISLANDS" => "VG",
            "BRUNEI" => "BN",
            "BULGARIA" => "BG",
            "BURKINA FASO" => "BF",
            "BURUNDI" => "BI",
            "CAMBODIA" => "KH",
            "CAMEROON" => "CM",
            "CANADA" => "CA",
            "CAPE VERDE" => "CV",
            "CARIBBEAN NETHERLANDS" => "BQ",
            "CAYMAN ISLANDS" => "KY",
            "CENTRAL AFRICAN REPUBLIC" => "CF",
            "CHAD" => "TD",
            "CHILE" => "CL",
            "CHINA" => "CN",
            "CHRISTMAS ISLAND" => "CX",
            "COCOS [KEELING] ISLANDS" => "CC",
            "COLOMBIA" => "CO",
            "COMOROS" => "KM",
            "CONGO - BRAZZAVILLE" => "CG",
            "CONGO - KINSHASA" => "CD",
            "COOK ISLANDS" => "CK",
            "COSTA RICA" => "CR",
            "CÔTE D’IVOIRE" => "CI",
            "CROATIA" => "HR",
            "CUBA" => "CU",
            "CURAÇAO" => "CW",
            "CYPRUS" => "CY",
            "CZECH REPUBLIC" => "CZ",
            "DENMARK" => "DK",
            "DJIBOUTI" => "DJ",
            "DOMINICA" => "DM",
            "DOMINICAN REPUBLIC" => "DO",
            "ECUADOR" => "EC",
            "EGYPT" => "EG",
            "EL SALVADOR" => "SV",
            "EQUATORIAL GUINEA" => "GQ",
            "ERITREA" => "ER",
            "ESTONIA" => "EE",
            "ETHIOPIA" => "ET",
            "FALKLAND ISLANDS" => "FK",
            "FAROE ISLANDS" => "FO",
            "FIJI" => "FJ",
            "FINLAND" => "FI",
            "FRANCE" => "FR",
            "FRENCH GUIANA" => "GF",
            "FRENCH POLYNESIA" => "PF",
            "FRENCH SOUTHERN TERRITORIES" => "TF",
            "GABON" => "GA",
            "GAMBIA" => "GM",
            "GEORGIA" => "GE",
            "GERMANY" => "DE",
            "GHANA" => "GH",
            "GIBRALTAR" => "GI",
            "GREECE" => "GR",
            "GREENLAND" => "GL",
            "GRENADA" => "GD",
            "GUADELOUPE" => "GP",
            "GUAM" => "GU",
            "GUATEMALA" => "GT",
            "GUERNSEY" => "GG",
            "GUINEA" => "GN",
            "GUINEA-BISSAU" => "GW",
            "GUYANA" => "GY",
            "HAITI" => "HT",
            "HEARD ISLAND AND MCDONALD ISLANDS" => "HM",
            "HONDURAS" => "HN",
            "HONG KONG SAR CHINA" => "HK",
            "HUNGARY" => "HU",
            "ICELAND" => "IS",
            "INDIA" => "IN",
            "INDONESIA" => "ID",
            "IRAN" => "IR",
            "IRAQ" => "IQ",
            "IRELAND" => "IE",
            "ISLE OF MAN" => "IM",
            "ISRAEL" => "IL",
            "ITALY" => "IT",
            "JAMAICA" => "JM",
            "JAPAN" => "JP",
            "JERSEY" => "JE",
            "JORDAN" => "JO",
            "KAZAKHSTAN" => "KZ",
            "KENYA" => "KE",
            "KIRIBATI" => "KI",
            "KUWAIT" => "KW",
            "KYRGYZSTAN" => "KG",
            "LAOS" => "LA",
            "LATVIA" => "LV",
            "LEBANON" => "LB",
            "LESOTHO" => "LS",
            "LIBERIA" => "LR",
            "LIBYA" => "LY",
            "LIECHTENSTEIN" => "LI",
            "LITHUANIA" => "LT",
            "LUXEMBOURG" => "LU",
            "MACAU SAR CHINA" => "MO",
            "MACEDONIA" => "MK",
            "MADAGASCAR" => "MG",
            "MALAWI" => "MW",
            "MALAYSIA" => "MY",
            "MALDIVES" => "MV",
            "MALI" => "ML",
            "MALTA" => "MT",
            "MARSHALL ISLANDS" => "MH",
            "MARTINIQUE" => "MQ",
            "MAURITANIA" => "MR",
            "MAURITIUS" => "MU",
            "MAYOTTE" => "YT",
            "MEXICO" => "MX",
            "MICRONESIA" => "FM",
            "MOLDOVA" => "MD",
            "MONACO" => "MC",
            "MONGOLIA" => "MN",
            "MONTENEGRO" => "ME",
            "MONTSERRAT" => "MS",
            "MOROCCO" => "MA",
            "MOZAMBIQUE" => "MZ",
            "MYANMAR [BURMA]" => "MM",
            "NAMIBIA" => "NA",
            "NAURU" => "NR",
            "NEPAL" => "NP",
            "NETHERLANDS" => "NL",
            "NETHERLANDS ANTILLES" => "AN",
            "NEW CALEDONIA" => "NC",
            "NEW ZEALAND" => "NZ",
            "NICARAGUA" => "NI",
            "NIGER" => "NE",
            "NIGERIA" => "NG",
            "NIUE" => "NU",
            "NORFOLK ISLAND" => "NF",
            "NORTHERN MARIANA ISLANDS" => "MP",
            "NORTH KOREA" => "KP",
            "NORWAY" => "NO",
            "OMAN" => "OM",
            "PAKISTAN" => "PK",
            "PALAU" => "PW",
            "PALESTINIAN TERRITORIES" => "PS",
            "PANAMA" => "PA",
            "PAPUA NEW GUINEA" => "PG",
            "PARAGUAY" => "PY",
            "PERU" => "PE",
            "PHILIPPINES" => "PH",
            "PITCAIRN ISLANDS" => "PN",
            "POLAND" => "PL",
            "PORTUGAL" => "PT",
            "QATAR" => "QA",
            "RÉUNION" => "RE",
            "ROMANIA" => "RO",
            "RUSSIA" => "RU",
            "RWANDA" => "RW",
            "SAINT BARTHÉLEMY" => "BL",
            "SAINT HELENA" => "SH",
            "SAINT KITTS AND NEVIS" => "KN",
            "SAINT LUCIA" => "LC",
            "SAINT MARTIN" => "MF",
            "SAINT PIERRE AND MIQUELON" => "PM",
            "SAINT VINCENT AND THE GRENADINES" => "VC",
            "SAMOA" => "WS",
            "SAN MARINO" => "SM",
            "SÃO TOMÉ AND PRÍNCIPE" => "ST",
            "SAUDI ARABIA" => "SA",
            "SENEGAL" => "SN",
            "SERBIA" => "RS",
            "SEYCHELLES" => "SC",
            "SIERRA LEONE" => "SL",
            "SINGAPORE" => "SG",
            "SINT MAARTEN" => "SX",
            "SLOVAKIA" => "SK",
            "SLOVENIA" => "SI",
            "SOLOMON ISLANDS" => "SB",
            "SOMALIA" => "SO",
            "SOUTH AFRICA" => "ZA",
            "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS" => "GS",
            "SOUTH KOREA" => "KR",
            "SPAIN" => "ES",
            "SRI LANKA" => "LK",
            "SUDAN" => "SD",
            "SURINAME" => "SR",
            "SVALBARD AND JAN MAYEN" => "SJ",
            "SWAZILAND" => "SZ",
            "SWEDEN" => "SE",
            "SWITZERLAND" => "CH",
            "SYRIA" => "SY",
            "TAIWAN, PROVINCE OF CHINA" => "TW",
            "TAJIKISTAN" => "TJ",
            "TANZANIA" => "TZ",
            "THAILAND" => "TH",
            "TIMOR-LESTE" => "TL",
            "TOGO" => "TG",
            "TOKELAU" => "TK",
            "TONGA" => "TO",
            "TRINIDAD AND TOBAGO" => "TT",
            "TUNISIA" => "TN",
            "TURKEY" => "TR",
            "TURKMENISTAN" => "TM",
            "TURKS AND CAICOS ISLANDS" => "TC",
            "TUVALU" => "TV",
            "UGANDA" => "UG",
            "UKRAINE" => "UA",
            "UNITED ARAB EMIRATES" => "AE",
            "UNITED KINGDOM" => "GB",
            "UNITED STATES" => "US",
            "URUGUAY" => "UY",
            "U.S. OUTLYING ISLANDS" => "UM",
            "U.S. VIRGIN ISLANDS" => "VI",
            "UZBEKISTAN" => "UZ",
            "VANUATU" => "VU",
            "VATICAN CITY" => "VA",
            "VENEZUELA" => "VE",
            "VIETNAM" => "VN",
            "WALLIS AND FUTUNA" => "WF",
            "WESTERN SAHARA" => "EH",
            "YEMEN" => "YE",
            "ZAMBIA" => "ZM",
            "ZIMBABWE" => "ZW",
        ];

        if (strlen($value) <= 3) {
            if (!in_array(strtoupper($value), $countryIDArray)) {
                $this->messageManager->addErrorMessage("Invalid Country Name.");
                $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl())->sendResponse();
                exit;

            }
            return $value;
        }


        if (!array_key_exists(strtoupper($value), $countryIDArray)) {
            $this->messageManager->addErrorMessage("Invalid Country Name.");
            $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl())->sendResponse();
            exit;
        }

        $countryId = $countryIDArray[strtoupper($value)];
        return $countryId;
    }

    //Check if flow started from B2B URL

    /**
     * This function processes the user values to either create
     * a new user on the site and log him/her in or log an existing
     * user to the site. Mapping is done based on $checkIfMatchBy
     * variable. Either email or username.
     *
     * @param $user_email
     * @param $firstName
     * @param $lastName
     * @param $userName
     * @param $groupName
     * @param $defaultRole
     * @param $checkIfMatchBy
     * @param $nameId
     * @throws LocalizedException
     * @throws \Exception
     */
    private function processUserAction($user_email, $firstName, $lastName, $userName,
                                       $groupName, $defaultRole, $checkIfMatchBy, $nameId, $params, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode)
    {
        $user_email = !$this->spUtility->isBlank($user_email) ? $user_email : $this->findUserEmail();
        $admin = false;
        $this->spUtility->log_debug(" inside processUserAction() ");
        $admin = $this->spUtility->checkIfFlowStartedFromBackend($this->relayState);

        $this->spUtility->log_debug("processUserAction(): isAdmin: " . json_encode($admin));
        $user = null;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $this->spUtility->log_debug("processUserAction(): User session Found");
            $redirectUrl = $this->spUtility->getBaseUrlFromUrl($this->relayState);
            if ($user_email != $customerSession->getCustomer()->getEmail()) {
                $this->messageManager->addErrorMessage(__("Another User is already logged in. Please logout first and then try again."));
                $this->spUtility->log_debug("Another User is already logged in. Please logout first and then try again.");
            }
            $this->spUtility->log_debug("processUserAction(): Redirecting to: " . $redirectUrl);
            $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            exit;
        }

        $this->spUtility->log_debug(" inside processUserAction(): No User session Found");
        if ($admin) {
            $defaultRole = $this->defaultRole;
            $this->spUtility->log_debug("processUserAction() :  defaultRole from settings: " . json_encode($defaultRole));
            // $defaultRole = $this->spUtility->isBlank($defaultRole)? SPConstants::DEFAULT_ROLE : $defaultRole;

            $user = $this->getAdminUserFromAttributes($user_email);
            $autoCreateUser = $this->autoCreateAdminUser;
            if (!$this->spUtility->isBlank($user)) {
                $this->spUtility->log_debug("processUserAction(): Admin User Found: Updating Attributes ");
                $user = $this->updateUserAttributes($userName, $user_email, $firstName, $lastName, $groupName, $defaultRole, $nameId, $user, $admin, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode);
            } else if ($this->spUtility->isBlank($user) && $autoCreateUser) {
                $this->spUtility->log_debug("processUserAction(): AdminUser Not Found: Creating One");
                $user = $this->createNewUser($user_email, $firstName, $lastName, $userName, $groupName, $defaultRole, $nameId, $user, $admin, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode);
            } else {

                echo "This backend user does not exists and cannot be auto-created. Please contact your administrator.";
                exit;
            }
        } else {
            //----------If the flow is not for Admin SSO i.e; when it for customer SSO:
            $defaultGroup = $this->defaultGroup;
            $autoCreateUser = $this->autoCreateCustomer;
            $user = $this->getCustomerFromAttributes($user_email);
            if ($user) {
                $this->spUtility->log_debug(" processUserAction() : User Found: Updating Attributes ");
                $user = $this->updateUserAttributes($userName, $user_email, $firstName, $lastName, $groupName, $defaultGroup, $nameId, $user, $admin, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode);
                $user = $this->updateCustomAttributes($user, $user_email);
            } else if (!$user && $autoCreateUser) {
                $this->spUtility->log_debug(" processUserAction(): Customer Not Found, Creating One: ");
                $user = $this->createNewUser($user_email, $firstName, $lastName, $userName, $groupName, $defaultGroup, $nameId, $user, $admin, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode);
            } else {
                $this->spUtility->log_debug("This Customer does not exists and cannot be auto-created.");

                $this->messageManager->addErrorMessage(__("This Customer does not exists and cannot be auto-created.Please contact your Administrator."));
                if ($this->auto_redirect) {
                    $params = $this->getRequest()->getParams();
                    $redirecturl = $this->storeManager->getStore()->getBaseUrl();
                    if (!empty($params['RelayState'])) {
                        $redirecturl = $params['RelayState'];
                    }
                    $this->responseFactory->create()->setRedirect($redirecturl)->sendResponse();
                } else {
                    $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl() . 'customer/account')->sendResponse();
                }
                exit('This Customer does not exists and cannot be auto-created.Please contact your Administrator.');
            }
        }

        $this->spUtility->log_debug("ProcessUserAction() : before redirecting users: relayState :" . $this->relayState);

        // log the user in to it's respective dashboard
        if (null == $user && $admin) {
            echo "This User does not exists and cannot be auto-created. Please contact your Administrator.";
            exit;
        } elseif (null == $user) {
            $this->messageManager->addErrorMessage(__("This User does not exists and cannot be auto-created. Please contact your Administrator."));
            if ($this->auto_redirect) {
                $params = $this->getRequest()->getParams();
                $redirecturl = $this->storeManager->getStore()->getBaseUrl();
                if (!empty($params['RelayState'])) {
                    $redirecturl = $params['RelayState'];
                }
                $this->responseFactory->create()->setRedirect($redirecturl)->sendResponse();
                exit;
            } else {

                $redirectUrl = $this->storeManager->getStore()->getBaseUrl();
                $this->responseFactory->create()->setRedirect($redirectUrl . 'customer/account')->sendResponse();
                exit;
            }
        } else if (null != $user && $admin) {
            $this->spUtility->setAdminSessionData('admin_post_logout', 1);

            $this->spUtility->log_debug("ProcessUserAction() : redirecting admin :");
            $this->redirectToBackendAndLogin($user->getUsername(), $this->sessionIndex, $this->relayState);
        } else {
            $this->spUtility->setSessionData('customer_post_logout', 1);

            $user = $this->customerModel->load($user->getId());
            $this->spUtility->log_debug("ProcessUserAction() : redirecting customer :");
            $this->customerLoginAction->setUser($user)->setCustomerId($user->getId())->setRelayState($this->relayState)->setAxCompanyId($this->accountId)->execute();
        }
    }

    private function findUserEmail()
    {
        $this->spUtility->log_debug("processResponseAction: findUserEmail");
        if ($this->attrs) {
            foreach ($this->attrs as $value) {
                if (filter_var($value[0], FILTER_VALIDATE_EMAIL)) {
                    return $value[0];
                }
            }
            return "";
        }
    }

    /**
     * Get the Admin User from the Attributes in the SAML response.
     * Return False if the admin doesn't exist. The admin is fetched
     * by email or username based on the admin settings (checkifmatchby)
     *
     * @param $checkIfMatchBy
     * @param $user_email
     * @param $userName
     * @return array|\Magento\User\Model\User
     * @throws LocalizedException
     */
    private function getAdminUserFromAttributes($user_email)
    {
        $adminUser = false;
        $this->spUtility->log_debug("ProcessUserAction: getAdminUserFromAttributes(): ");

        $connection = $this->adminUserModel->getResource()->getConnection();
        $select = $connection->select()->from($this->adminUserModel->getResource()->getMainTable())->where('email=:email');
        $binds = ['email' => $user_email];
        $adminUser = $connection->fetchRow($select, $binds);
        $adminUser = is_array($adminUser) ? $this->adminUserModel->loadByUsername($adminUser['username']) : $adminUser;
        $this->spUtility->log_debug("getAdminUserFromAttributes(): fetched adminUser: ");
        return $adminUser;
    }

    /**
     * This function updates the user attributes based on the value
     * in the SAML Response. This function decides if the user is
     * a customer or an admin and update it's attribute accordingly
     *
     * @param $firstName
     * @param $lastName
     * @param $groupName
     * @param $defaultRole
     * @param $nameId
     * @param \Magento\Customer\Api\Data\CustomerInterface $user
     * @param $admin
     * @return \Magento\Customer\Api\Data\CustomerInterface|void
     * @throws \Exception
     */
    private function updateUserAttributes($userName, $user_email, $firstName, $lastName, $groupName, $defaultRole, $nameId, $user, $admin, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode)
    {
        $userId = $user->getId();
        if ($admin) {
            $setRole = null;
            $adminUser = $this->spUtility->getAdminUserById($userId);

            $this->spUtility->log_debug("updateUserAttributes(): admin: ");
            // update the attributes

            if (!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0) {
                if ($this->spUtility->isBlank($firstName)) {
                    $firstName = explode('@', $user_email)[0];
                    $firstName = preg_replace('/[^A-Za-z0-9\-]/', '', $firstName);
                }

                if ($this->spUtility->isBlank($lastName)) {
                    $lastName = explode('@', $user_email)[1];
                    $lastName = preg_replace('/[^A-Za-z0-9\-]/', '', $lastName);
                }

                if (!$this->spUtility->isBlank($firstName))
                    $adminUser->setFirstname($firstName);
                if (!$this->spUtility->isBlank($lastName))
                    $adminUser->setLastname($lastName);

                // update the attributes
                if (!$this->spUtility->isBlank($firstName))
                    $this->spUtility->saveConfig(SPConstants::DB_FIRSTNAME, $firstName, $userId, $admin);
                if (!$this->spUtility->isBlank($lastName))
                    $this->spUtility->saveConfig(SPConstants::DB_LASTNAME, $lastName, $userId, $admin);
            }

            $rolesMapped = $this->role_mapping;
            $groupMapped = $this->group_mapping;

            if (!$this->spUtility->isBlank($rolesMapped)) {
                $setRole = $this->processRoles($defaultRole, $admin, $rolesMapped, $groupName);
            }

            if ($this->spUtility->isBlank($setRole)) {
                $setRole = $this->processDefaultRole($admin, $defaultRole);
            }
            $setRole = $this->spUtility->isBlank($setRole) ? 1 : $setRole;

            if (!$this->spUtility->isBlank($setRole) && !empty($this->updateRole) && strcasecmp($this->updateRole, SPConstants::enable) === 0) {
                $adminUser->setRoleId($setRole);
            }

            if (isset($userName))
                $adminUser->setUsername($userName);

            $this->spUtility->log_debug(" exiting updateUserAttributes: adminUser Email", $adminUser->getEmail());
            $adminUser->save();

        } else {

            $customer = $this->spUtility->getCustomer($userId);
            // update the customer attributes
            $this->spUtility->log_debug("updateUserAttributes(): customer: " . $nameId);
            if ($this->spUtility->isBlank($firstName)) {
                $firstName = explode('@', $user_email)[0];
                $firstName = preg_replace('/[^A-Za-z0-9\-]/', '', $firstName);


            }

            if ($this->spUtility->isBlank($lastName)) {
                $lastName = explode('@', $user_email)[1];
                $lastName = preg_replace('/[^A-Za-z0-9\-]/', '', $lastName);


            }

            $session_details = array("NameID" => $nameId, "SessionIndex" => $this->sessionIndex);
            $this->spUtility->saveConfig('extra', $session_details, $userId, $admin);

            $groupMapped = $this->group_mapping;

            $setRole = $this->processRoles($defaultRole, $admin, $groupMapped, $groupName);

            if ($this->spUtility->isBlank($setRole)) {

                $setRole = $this->processDefaultRole($admin, $defaultRole);
            }

            $store = $this->spUtility->getCurrentStore();
            $websiteId = $this->spUtility->getCurrentWebsiteId();
            $customer = $this->customerFactory->create();

            //load existing customer to update its attribute
            $customer->setWebsiteId($websiteId)->loadByEmail($user_email);
            if (!$this->spUtility->isBlank($setRole) && !empty($this->updateFrontendRole) && strcasecmp($this->updateFrontendRole, SPConstants::enable) === 0) {
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setGroupId($setRole)
                    ->setForceConfirmed(true)
                    ->save();
            }

            if (!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0) {
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($firstName)
                    ->setLastname($lastName)
                    ->setEmail($user_email)
                    ->setForceConfirmed(true)
                    ->save();

                if ($billingphone || $billingstreetAddress || $billingcity || $billingmapState || $billingcountryCode || $billingzipCode) {
                    $billingAddressId = $customer->getDefaultBilling();
                    $billingAddress = $this->_addressFactory->create()->load($billingAddressId);

                    if (is_null($billingAddressId)) {
                        $address = $this->dataAddressFactory->create();
                        $address->setFirstname($firstName);
                        $address->setLastname($lastName);

                        if (!empty($billingphone))
                            $address->setTelephone($billingphone);

                        if (!empty($billingstreetAddress))
                            $address->setStreet($billingstreetAddress);

                        if (!empty($billingcity))
                            $address->setCity($billingcity);

                        if (!empty($billingcountryCode))
                            $address->setCountryId($billingcountryCode);

                        if (!empty($billingmapState)) {
                            $region = $this->collectionFactory->create()
                                ->addRegionNameFilter($billingmapState)
                                ->getFirstItem()
                                ->toArray();

                            if (!empty($region['region_id'])) {
                                $address->setRegionId($region['region_id']);
                            }
                        }

                        if (!empty($billingzipCode))
                            $address->setPostcode($billingzipCode);


                        $address->setIsDefaultBilling('1');
                        $address->setSaveInAddressBook('1');
                        $address->setCustomerId($customer->getId());
                        try {
                            $address->save();
                            $customer = $address->getCustomer();
                        } catch (\Exception $exception) {
                            $this->spUtility->log_debug("An error occurred while trying to set address: {$exception->getMessage()}");

                        }
                    } else {
                        //now you can update your address here and don't forget to save
                        $billingAddress->setFirstname($firstName);
                        $billingAddress->setLastname($lastName);
                        $this->spUtility->log_debug(" exiting updateUserAttributes: phone");

                        if (!empty($billingphone))
                            $billingAddress->setTelephone($billingphone);

                        if (!empty($billingstreetAddress))
                            $billingAddress->setStreet($billingstreetAddress);

                        if (!empty($billingcity))
                            $billingAddress->setCity($billingcity);

                        if (!empty($billingmapState)) {
                            $region = $this->collectionFactory->create()
                                ->addRegionNameFilter($billingmapState)
                                ->getFirstItem()
                                ->toArray();

                            if (!empty($region['region_id'])) {
                                $billingAddress->setRegionId($region['region_id']);
                            }
                        }

                        if (!empty($billingcountryCode))
                            $billingAddress->setCountryId($billingcountryCode);

                        if (!empty($billingzipCode))
                            $billingAddress->setPostcode($billingzipCode);

                        $billingAddress->save();
                    }

                }

                if ($shippingphone || $shippingstreetAddress || $shippingcity || $shippingmapState || $shippingcountryCode || $shippingzipCode) {
                    $shippingAddressId = $customer->getDefaultShipping();
                    $shippingAddress = $this->_addressFactory->create()->load($shippingAddressId);
                    if (is_null($shippingAddressId)) {

                        $address = $this->dataAddressFactory->create();
                        $address->setFirstname($firstName);
                        $address->setLastname($lastName);

                        if (!empty($shippingphone))
                            $address->setTelephone($shippingphone);

                        if (!empty($shippingstreetAddress))
                            $address->setStreet($shippingstreetAddress);

                        if (!empty($shippingcity))
                            $address->setCity($shippingcity);

                        if (!empty($shippingmapState)) {
                            $region = $this->collectionFactory->create()
                                ->addRegionNameFilter($shippingmapState)
                                ->getFirstItem()
                                ->toArray();

                            if (!empty($region['region_id'])) {
                                $address->setRegionId($region['region_id']);
                            }
                        }

                        if (!empty($shippingcountryCode))
                            $address->setCountryId($shippingcountryCode);

                        if (!empty($shippingzipCode))
                            $address->setPostcode($shippingzipCode);

                        $address->setIsDefaultShipping('1');
                        $address->setSaveInAddressBook('1');

                        $address->setCustomerId($customer->getId());

                        try {

                            $address->save();

                            $customer = $address->getCustomer();

                        } catch (\Exception $exception) {
                            $this->spUtility->log_debug("An error occurred while trying to set address: {$exception->getMessage()}");
                        }
                    } else {
                        //now you can update your address here and don't forget to save

                        $shippingAddress->setFirstname($firstName);
                        $shippingAddress->setLastname($lastName);
                        $this->spUtility->log_debug(" exiting updateUserAttributes: phone");

                        if (!empty($shippingphone))
                            $shippingAddress->setTelephone($shippingphone);

                        if (!empty($shippingstreetAddress))
                            $shippingAddress->setStreet($shippingstreetAddress);

                        if (!empty($shippingcity))
                            $shippingAddress->setCity($shippingcity);

                        if (!empty($shippingmapState)) {
                            $region = $this->collectionFactory->create()
                                ->addRegionNameFilter($shippingmapState)
                                ->getFirstItem()
                                ->toArray();

                            if (!empty($region['region_id'])) {
                                $shippingAddress->setRegionId($region['region_id']);
                            }
                        }

                        if (!empty($shippingcountryCode))
                            $shippingAddress->setCountryId($shippingcountryCode);

                        if (!empty($shippingzipCode))
                            $shippingAddress->setPostcode($shippingzipCode);

                        $shippingAddress->save();
                    }
                }

            }
        }

        if (!empty($setRole) && !empty($this->dontAllowUnlistedUserRole) && $this->dontAllowUnlistedUserRole == SPConstants::enable)
            return;

        return $user;

    }

    /**
     * Process the role that needs to be assigned to the user.
     * Fetch all the roles / groups and check admin mapping to
     * select which role needs to be assigned to the user
     *
     * @param $defaultRole
     * @param $admin
     * @param $role_mapping
     * @param $groupName
     *
     * @return array|string
     * @todo : remove the n2 complexity here
     */
    private function processRoles($defaultRole, $admin, $role_mapping, $groupName)
    {


        if ($admin) {
            $defaultRole = $this->defaultRole;
        }
        $this->spUtility->log_debug("default Role: ", $defaultRole);

        if ($role_mapping) {
            $role_mapping = json_decode($role_mapping);
        }

        $roleId = null;
        if (empty($groupName) || empty($role_mapping)) {
            return null;
        }
        foreach ($role_mapping as $role_value => $group_names) {
            $groups = explode(";", $group_names);

            foreach ($groups as $groupseparated) {
                if (!empty($groupseparated)) {

                    foreach ($groupName as $group) {
                        if ($group == $groupseparated) {
                            $roleId = $role_value;
                            return $roleId;
                        }

                    }
                }

            }
        }
        return $this->getGroupIdByName($defaultRole);
    }

    private function getGroupIdByName($groupName)
    {
        $groups = $this->userGroupModel->toOptionArray();
        foreach ($groups as $group) {
            if ($groupName == $group['label']) {
                $this->spUtility->log_debug("getGroupIdByName(): returning groupId: " . $group['value'] . " for role: " . $group['label']);
                return $group['value'];
            }
        }
        $this->spUtility->log_debug("getGroupIdByName(): Something went wrong. Default GroupId cannot be Found: ");
    }

    /**
     * Process the default role and figure out if it's for
     * an admin or user. Return the ID of the default Role.
     *
     * @param $admin
     * @param $defaultRole
     * @return string
     */
    private function processDefaultRole($admin, $defaultRole)
    {
        if ($this->spUtility->isBlank($defaultRole)) {
            $this->spUtility->log_debug("processDefaultRole(): defaultRole is Blank. Setting PreDefinedRoles");
            $defaultRole = $admin ? SPConstants::DEFAULT_ROLE : SPConstants::DEFAULT_GROUP;
        }

        $defaultRoleId = ($admin) ? $this->getRoleIdByName($defaultRole) : $this->getGroupIdByName($defaultRole);
        $this->spUtility->log_debug("processDefaultRole(): returning default Role/Grouo Id: " . $defaultRoleId . " for role/group " . $defaultRole);
        return $defaultRoleId;
    }

    private function getRoleIdByName($roleName)
    {
        $rolesCollection = $this->adminRoleModel->addFieldToFilter('role_type', 'G');
        $roles = $rolesCollection->toOptionArray();
        foreach ($roles as $role) {
            if ($roleName == $role['label']) {
                $this->spUtility->log_debug("getRoleIdByName(): returning roleId: " . $role['value'] . " for role: " . $role['label']);
                return $role['value'];
            }
        }
        $this->spUtility->log_debug("getGroupIdByName(): Something went wrong. Default RoleId cannot be Found: ");
    }

    /**
     * Create a new user based on the SAML response and attributes. Log the user in
     * to it's appropriate dashboard. This class handles generating both admin and
     * customer users.
     *
     * @param $user_email
     * @param $firstName
     * @param $lastName
     * @param $userName
     * @param $groupName
     * @param $defaultRole
     * @param $user
     * @return \Magento\User\Model\User|null
     * @throws LocalizedException
     * @throws \Exception
     */
    private function createNewUser($user_email, $firstName, $lastName, $userName, $groupName,
                                   $defaultRole, $nameId, $user, $admin, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode)
    {

        if ($this->spUtility->check_license_plan(4)) {
            $this->spUtility->flushCache();
            $this->spUtility->reinitConfig();

            if ($this->spUtility->getRemainingUsersCount()) {
                $this->spUtility->log_debug("Your Auto Create User Limit for the trial Miniorange Magento SAML SP plugin is exceeded. Please Upgrade to any of the Premium Plan to continue the service.");

                print_r("Your Auto Create User Limit for the trial Miniorange Magento SAML SP plugin is exceeded. Please Upgrade to any of the Premium Plan to continue the service.");
                exit;
            } else {
                $count = (int)AESEncryption::decrypt_data($this->spUtility->getStoreConfig(SPConstants::MAGENTO_COUNTER), SPConstants::DEFAULT_TOKEN_VALUE);
                $this->spUtility->setStoreConfig(SPConstants::MAGENTO_COUNTER, AESEncryption::encrypt_data($count + 1, SPConstants::DEFAULT_TOKEN_VALUE));
                $this->spUtility->flushCache();
                $this->spUtility->reinitConfig();
            }
        }

        $this->spUtility->log_debug("createNewUser(): email: " . $user_email);
        $this->spUtility->log_debug("createNewUser(): admin: " . json_encode($admin));

        $random_password = $this->generatePassword(16);// generate random string as a password

        $email = !$this->spUtility->isBlank($user_email) ? $user_email : $this->findUserEmail();

        if (empty($email)) {
            if ($admin) {

                $this->spUtility->log_debug("This admin email not found.");

                echo "This admin email not found..Please contact your Administrator.";
                exit;
            } else {
                $this->spUtility->log_debug("This Customer email not found.");

                $this->messageManager->addErrorMessage(__("Email not found.Please contact your Administrator."));
                $this->responseFactory->create()->setRedirect($this->storeManager->getStore()->getBaseUrl() . '/customer/account')->sendResponse();
                exit;
            }
        }

        if ($this->spUtility->isBlank($firstName)) {
            $firstName = explode('@', $email)[0];
            $firstName = preg_replace('/[^A-Za-z0-9\-]/', '', $firstName);

            $this->spUtility->log_debug("createNewUser(): Changed firstName: " . $firstName);
        }

        if ($this->spUtility->isBlank($lastName)) {
            $lastName = explode('@', $email)[1];
            $lastName = preg_replace('/[^A-Za-z0-9\-]/', '', $lastName);

            $this->spUtility->log_debug("createNewUser(): Changed lastName: " . $lastName);
        }
        if ($admin) {
            $role_mapping = $this->role_mapping;
            $this->spUtility->log_debug("createNewUser() : rolesMapped " . $role_mapping);
        } else {
            $role_mapping = $this->group_mapping;
            $this->spUtility->log_debug("createNewUser() : groupsMapped " . $role_mapping);
        }

        if (!empty($this->dontCreateUserIfRoleNotMapped) && strcasecmp($this->dontCreateUserIfRoleNotMapped, SPConstants::enable) === 0) {

            if (!$this->isRoleMappingConfiguredForUser($role_mapping, $groupName)) {
                if ($admin) {

                    echo "We cannot log you in. Please map the roles";
                    exit;
                }
                $this->messageManager->addErrorMessage("We cannot log you in. Please map the roles");


                if ($this->auto_redirect) {
                    $params = $this->getRequest()->getParams();
                    $redirecturl = $this->storeManager->getStore()->getBaseUrl();
                    if (!empty($params['RelayState'])) {
                        $redirecturl = $params['RelayState'];
                    }
                    $this->responseFactory->create()->setRedirect($redirecturl)->sendResponse();
                    exit;
                } else {

                    $redirectUrl = $this->storeManager->getStore()->getBaseUrl();
                    $this->responseFactory->create()->setRedirect($redirectUrl . 'customer/account')->sendResponse();
                    exit;
                }
            }

        }

        // process the roles
        $setRole = $this->processRoles($defaultRole, $admin, $role_mapping, $groupName);

        if ($this->spUtility->isBlank($setRole)) {
            $setRole = $this->processDefaultRole($admin, $defaultRole);
        }

        $userName = !$this->spUtility->isBlank($userName) ? $userName : $firstName;

        $user = $admin ? $this->createAdminUser($userName, $firstName, $lastName, $email, $random_password, $setRole)
            : $this->createCustomer($userName, $firstName, $lastName, $email, $random_password, $setRole, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode);

        $userId = $user->getId();
        $this->spUtility->log_debug("createNewUser(): user created with id: " . $userId);

        // update session index and nameID in the database for the user.
        $session_details = array("NameID" => $nameId, "SessionIndex" => $this->sessionIndex);
        $this->spUtility->saveConfig('extra', $session_details, $userId, $admin);
        $data = $this->spUtility->getCustomerStoreConfig('extra', $userId);

        return $user;
    }

    /**
     * Retrieve random password
     *
     * @param int $length
     * @return  string
     */
    public function generatePassword($length = 16)
    {
        $chars = \Magento\Framework\Math\Random::CHARS_LOWERS
            . \Magento\Framework\Math\Random::CHARS_UPPERS
            . \Magento\Framework\Math\Random::CHARS_DIGITS
            . "#$%&*.;:()@!";

        $this->spUtility->log_debug("chars: " . $chars);
        $password = $this->randomUtility->getRandomString($length, $chars);
        $this->spUtility->log_debug("Password: " . $password);
        return $password;
    }

    /**
     * Checks if the role coming in the response matches with
     * the mapping done in the plugin. This function is only
     * called if admin has enabled the option to not create
     * users if roles are not mapped.$_COOKIE
     * @param $role_mapping
     * @param $groupName
     * @return bool
     *
     * @todo : remove the n2 complexity here
     */
    private function isRoleMappingConfiguredForUser($role_mapping, $groupName)
    {
        $this->spUtility->log_debug("isRoleMappingConfiguredForUser: role_mapping: ", $role_mapping);
        $this->spUtility->log_debug("isRoleMappingConfiguredForUser: groupName: ", $groupName);
        if ($role_mapping) {
            $role_mapping = json_decode($role_mapping);
        }

        $roleId = null;
        if (empty($groupName) || empty($role_mapping)) {
            return null;
        }
        foreach ($role_mapping as $role_value => $group_names) {
            $groups = explode(";", $group_names);

            foreach ($groups as $groupseparated) {
                if (!empty($groupseparated)) {

                    foreach ($groupName as $group) {
                        if ($group == $groupseparated) {
                            $roleId = $role_value;
                            return $roleId;
                        }

                    }
                }

            }
        }
        return false;
    }

    /**
     * Create a New Admin User
     *
     * @param $email
     * @param $firstName
     * @param $lastName
     * @param $userName
     * @param $random_password
     * @param $role_assigned
     * @return \Magento\User\Model\User
     * @throws \Exception
     */
    private function createAdminUser($userName, $firstName, $lastName, $email, $random_password, $role_assigned)
    {
        if ($role_assigned === "0" || $role_assigned == null) {
            $role_assigned = "1";
        }

        $this->spUtility->log_debug("ProcessUserAction : createAdminUser(): ");

        if (strlen($userName) >= 40)
            $userName = substr($userName, 0, 40);

        $adminInfo = [
            'username' => $userName,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'password' => $random_password,
            'interface_locale' => 'en_US',
            'is_active' => 1
        ];

        $role_assigned = $this->spUtility->isBlank($role_assigned) ? 1 : $role_assigned;
        $this->spUtility->log_debug("ProcessUserAction : createAdminUser 1: ");

        $user = $this->userFactory->create();
        $user->setData($adminInfo);
        $user->setRoleId($role_assigned);
        $user->save();
        return $user;
    }

    /**
     * Create a new customer.
     *
     * @param $userName
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $random_password
     * @param $role_assigned
     * @return \Magento\Customer\Model\Customer
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createCustomer($userName, $firstName, $lastName, $email, $random_password, $role_assigned, $billingstreetAddress, $billingmapState, $billingzipCode, $billingphone, $billingcity, $billingcountryCode, $shippingstreetAddress, $shippingmapState, $shippingzipCode, $shippingphone, $shippingcity, $shippingcountryCode)
    {
        if ($role_assigned === "0" || $role_assigned == null) {
            $role_assigned = "1";
        }

        $this->spUtility->log_debug("createCustomer(): email: " . $email);

        $role_assigned = $this->spUtility->isBlank($role_assigned) ? 1 : $role_assigned;
        $this->spUtility->log_debug("createCustomer(): role assigned: " . $role_assigned);

        $store = $this->spUtility->getCurrentStore();
        $websiteId = $this->spUtility->getCurrentWebsiteId();

        $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $collection = $this->spUtility->getIDPApps();
        $idpDetails = null;
        foreach ($collection as $item) {
            if ($item->getData()["idp_name"] === $idp_name) {
                $idpDetails = $item->getData();
            }
        }

        $customer = $this->customerFactory->create()
            ->setWebsiteId($websiteId)
            ->setStore($store)
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setEmail($email)
            ->setPassword($random_password)
            ->setGroupId($role_assigned);

        $attrsKeys = array_Keys((array)$this->attrs);
        $mapped = json_decode($idpDetails['custom_attributes']);
        $mappedValues = array_values((array)$mapped);
        $results = array_intersect($attrsKeys, $mappedValues);
        foreach ($results as $result) {
            $column = array_search($result, (array)$mapped);
            $attrValue = $this->attrs[$result][0];
            $customer->setData($column, $attrValue);

        }
        $customer->save();   // customer cannot have multiple groups

        $attrsKeys = array_Keys((array)$this->attrs);
        $this->spUtility->log_debug("attributes keys ", $attrsKeys);
        $mapped = json_decode((string)$this->spUtility->getStoreConfig(SPConstants::CUSTOM_MAPPED));
        $mappedValues = array_values((array)$mapped);
        $results = array_intersect($attrsKeys, $mappedValues);

        $this->spUtility->log_debug(" Update user's custom attributes");
        $userId = $customer->getId();


        if ($billingphone || $billingstreetAddress || $billingcity || $billingmapState || $billingcountryCode || $billingzipCode) {
            $address = $this->dataAddressFactory->create();
            $address->setFirstname($firstName);
            $address->setLastname($lastName);

            if (!empty($billingphone))
                $address->setTelephone($billingphone);

            if (!empty($billingstreetAddress))
                $address->setStreet($billingstreetAddress);

            if (!empty($billingcity))
                $address->setCity($billingcity);

            if (!empty($billingmapState)) {
                $region = $this->collectionFactory->create()
                    ->addRegionNameFilter($billingmapState)
                    ->getFirstItem()
                    ->toArray();

                if (!empty($region['region_id'])) {
                    $address->setRegionId($region['region_id']);
                }
            }

            if (!empty($billingcountryCode))
                $address->setCountryId($billingcountryCode);

            if (!empty($billingzipCode))
                $address->setPostcode($billingzipCode);

            $address->setIsDefaultBilling('1');
            $address->setSaveInAddressBook('1');
            $address->setCustomerId($customer->getId());
            try {
                $address->save();
                $customer = $address->getCustomer();
            } catch (\Exception $exception) {
                $this->spUtility->log_debug("An error occurred while trying to set address: {$exception->getMessage()}");
            }
        }

        if ($shippingphone || $shippingstreetAddress || $shippingcity || $shippingmapState || $shippingcountryCode || $shippingzipCode) {
            $address = $this->dataAddressFactory->create();
            $address->setFirstname($firstName);
            $address->setLastname($lastName);

            if (!empty($shippingphone))
                $address->setTelephone($shippingphone);

            if (!empty($shippingstreetAddress))
                $address->setStreet($shippingstreetAddress);

            if (!empty($shippingcity))
                $address->setCity($shippingcity);


            if (!empty($shippingmapState)) {
                $region = $this->collectionFactory->create()
                    ->addRegionNameFilter($shippingmapState)
                    ->getFirstItem()
                    ->toArray();

                if (!empty($region['region_id'])) {
                    $address->setRegionId($region['region_id']);
                }
            }

            if (!empty($shippingcountryCode))
                $address->setCountryId($shippingcountryCode);

            if (!empty($shippingzipCode))
                $address->setPostcode($shippingzipCode);

            $address->setIsDefaultShipping('1');
            $address->setSaveInAddressBook('1');
            $address->setCustomerId($customer->getId());
            try {
                $address->save();
                $customer = $address->getCustomer();
            } catch (\Exception $exception) {
                $this->spUtility->log_debug("An error occurred while trying to set address: {$exception->getMessage()}");
            }
        }
        $this->updateCustomAttributes($customer, $email);

        return $customer;
    }

    /* Update Custom Attributes */
    private function updateCustomAttributes($user, $user_email)
    {


        if (!empty($this->updateAttribute) && strcasecmp($this->updateAttribute, SPConstants::enable) === 0) {

            $idp_name = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
            $this->spUtility->log_debug("processUserAction", $idp_name);
            $collection = $this->spUtility->getIDPApps();
            $idpDetails = null;
            foreach ($collection as $item) {
                if ($item->getData()["idp_name"] === $idp_name) {
                    $idpDetails = $item->getData();
                }
            }
            $this->spUtility->log_debug(" In updateCustomAttribute function");

            $userId = $user->getId();
            $this->spUtility->log_debug("user id", $userId);

            $admin = is_a($user, '\Magento\User\Model\User') ? TRUE : FALSE;


            $attrsKeys = array_Keys((array)$this->attrs);
            $this->spUtility->log_debug("attributes keys ", $attrsKeys);

            $mapped = json_decode($idpDetails['custom_attributes']);
            $mappedValues = array_values((array)$mapped);

            foreach ((array)$mapped as $keys => $value) {
                $customerEntityTypeId = $this->eavConfig->getEntityType('customer')->getId();
                $attribute = $this->eavConfig->getAttribute($customerEntityTypeId, $keys);
                if (!($attribute && $attribute->getId())) {
                    $this->spUtility->log_debug("$keys custom attribute not present");
                }
            }
            $results = array_intersect($attrsKeys, $mappedValues);


            $this->spUtility->log_debug(" Update user's custom attributes");
            $websiteId = $this->spUtility->getCurrentWebsiteId();
            foreach ($results as $result) {
                foreach($mapped as $mappedattrkey => $mappedattrvalue)
                {
                    if($mappedattrvalue == $result)
                    {
                        $this->spUtility->log_debug("custom attribute = $mappedattrkey and custom value = $mappedattrvalue ");
                        $customer = $this->customerFactory->create();
                        $customer->setWebsiteId($websiteId);
                        $customer->loadByEmail($user_email);
                        $attrValue = $this->attrs[$result][0];
                        $customer->setData($mappedattrkey, $attrValue);
                        $customer->save();
                    }
                }
            }


        }
        return $user;
    }

    /**
     * Get the Customer User from the Attributes in the SAML response
     * Return false if the customer doesn't exist. The customer is fetched
     * by email only. There are no usernames to set for a Magento Customer.
     *
     * @param $user_email
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     * @throws LocalizedException
     */
    private function getCustomerFromAttributes($user_email)
    {
        $websiteId = $this->spUtility->getCurrentWebsiteId();
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $this->spUtility->log_debug("getCustomerFromAttributes(): current Page Url: " . $currentUrl);
        try {
            $this->spUtility->log_debug("getCustomerFromAttributes(): customer: " . $user_email);
            $this->spUtility->log_debug("getCustomerFromAttributes(): WebsiteId: " . $websiteId);
            $customer = $this->customerRepository->get($user_email, $websiteId);
            return !is_null($customer) ? $customer : FALSE;
        } catch (NoSuchEntityException $e) {
            $this->spUtility->log_debug("getCustomerFromAttributes(): catch ");
            return FALSE;
        }
    }

    /**
     * Function redirects the user to the backend with appropriate parameters
     * in the URL which will be read in the backend portion of the code
     * and log the admin in. We can't directly log the admin in from anywhere
     * in the code as Magento doesn't allow it.
     *
     * @param $userId
     * @param $sessionIndex
     * @param $relayState
     */
    private function redirectToBackendAndLogin($userId, $sessionIndex, $relayState)
    {
        $this->spUtility->setAdminSessionData('admin_post_logout', 1);

        $areaCode = 'adminhtml';
        $username = $userId;
        $this->_request->setPathInfo('/admin');
        $this->spUtility->log_debug("redirectToBackendAndLogin: user: " . $username);

        try {
            $this->_state->setAreaCode($areaCode);
        } catch (LocalizedException $exception) {
            // do nothing
        }

        $this->_objectManager->configure($this->_configLoader->load($areaCode));

        $user = $this->_objectManager->get('Magento\User\Model\User')->loadByUsername($username);
        $session = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
        $session->setUser($user);
        $session->processLogin();
        $this->spUtility->reinitconfig();

        if ($session->isLoggedIn()) {
            $this->spUtility->log_debug("redirectToBackendAndLogin: isLoggedIn: true");
            $cookieManager = $this->_objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
            $cookieValue = $session->getSessionId();
            if ($cookieValue) {
                $this->spUtility->log_debug("redirectToBackendAndLogin: cookieValue: true");
                $sessionConfig = $this->_objectManager->get('Magento\Backend\Model\Session\AdminConfig');
                $cookiePath = str_replace('autologin.php', 'index.php', $sessionConfig->getCookiePath());
                $cookieMetadata = $this->_objectManager->get('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory')
                    ->createPublicCookieMetadata()
                    ->setDuration(3600)
                    ->setPath($cookiePath)
                    ->setDomain($sessionConfig->getCookieDomain())
                    ->setSecure($sessionConfig->getCookieSecure())
                    ->setHttpOnly($sessionConfig->getCookieHttpOnly());
                $cookieManager->setPublicCookie($sessionConfig->getName(), $cookieValue, $cookieMetadata);

                if (class_exists('Magento\Security\Model\AdminSessionsManager')) {
                    $this->spUtility->log_debug("redirectToBackendAndLogin: class exist AdminSessionsManager: true");
                    $adminSessionManager = $this->_objectManager->get('Magento\Security\Model\AdminSessionsManager');
                    $adminSessionManager->processLogin();
                }
            }

            $backendUrl = $this->HelperBackend->getHomePageUrl();
            header('Location:  ' . $backendUrl);
            exit;
        }

        $this->_objectManager->configure($this->_configLoader->load($areaCode));

        $user = $this->_objectManager->get('Magento\User\Model\User')->loadByUsername($username);
        $session = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
        $session->setUser($user);
        $session->processLogin();

        if ($session->isLoggedIn()) {

            $this->spUtility->log_debug("redirectToBackendAndLogin: isLoggedIn: true");
            $cookieManager = $this->_objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
            $cookieValue = $session->getSessionId();
            if ($cookieValue) {
                $this->spUtility->log_debug("redirectToBackendAndLogin: cookieValue: true");
                $sessionConfig = $this->_objectManager->get('Magento\Backend\Model\Session\AdminConfig');
                $cookiePath = str_replace('autologin.php', 'index.php', $sessionConfig->getCookiePath());
                $cookieMetadata = $this->_objectManager->get('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory')
                    ->createPublicCookieMetadata()
                    ->setDuration(3600)
                    ->setPath($cookiePath)
                    ->setDomain($sessionConfig->getCookieDomain())
                    ->setSecure($sessionConfig->getCookieSecure())
                    ->setHttpOnly($sessionConfig->getCookieHttpOnly());
                $cookieManager->setPublicCookie($sessionConfig->getName(), $cookieValue, $cookieMetadata);
            }

            $url = $this->spUtility->getAdminUrl('admin/dashboard/index');
            $this->spUtility->log_debug("redirectToBackendAndLogin: finalUrl: " . $url);
            $this->messageManager->addSuccessMessage("You are logged in successfully.");
            $this->responseFactory->create()->setRedirect($url)->sendResponse();
        }
    }

    /** The setter function for the RelayState Parameter */
    public function setRelayState($relayState)
    {
        $this->relayState = $relayState;
        return $this;
    }

    /** The setter function for the Attributes Parameter */
    public function setAttrs($attrs)
    {
        $this->attrs = $attrs;
        return $this;
    }

    /** The setter function for the SessionIndex Parameter */
    public function setSessionIndex($sessionIndex)
    {
        $this->sessionIndex = $sessionIndex;
        return $this;
    }

    private function checkIfB2BFlow()
    {
        $b2bStoreUrl = $this->spUtility->getStoreConfig(SPConstants::B2B_STORE_URL);
        if (!$this->spUtility->isBlank($b2bStoreUrl)) {
            $isB2BUrl = strpos($this->relayState, $b2bStoreUrl);
            if ($isB2BUrl !== false && !$this->spUtility->isBlank($this->accountId)) {
                $this->spUtility->log_debug("checkIfB2BFlow: B2B Flow Found: ");
                return true;
            }
        }
        $this->spUtility->log_debug("checkIfB2BFlow: Not B2B flow.. Continuing with B2C: ");
        return false;
    }

    /**
     * Create a temporary email address based on the username
     * in the SAML response. Email Address is a required so we
     * need to generate a temp/fake email if no email comes from
     * the IDP in the SAML response.
     *
     * @param $userName
     * @return string
     */
    private function generateEmail($userName)
    {
        $siteurl = $this->spUtility->getBaseUrl();
        $siteurl = substr($siteurl, strpos($siteurl, '//'), strlen($siteurl) - 1);
        return $userName . '@' . $siteurl;
    }

}
