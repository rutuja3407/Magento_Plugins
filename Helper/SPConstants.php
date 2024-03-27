<?php

namespace MiniOrange\SP\Helper;

/** This class lists down constant values used all over our Module. */
class SPConstants
{
    const MODULE_DIR = 'MiniOrange_SP';
    const MODULE_TITLE = 'SAML 2.0 SP';
    const MODULE_MULTISITE = '::multisite_settings';

    //ACL Settings
    const MODULE_BASE = '::SP';
    const MODULE_SPSETTINGS = '::sp_settings';
    const MODULE_IDPSETTINGS = '::idp_settings';
    const MODULE_SIGNIN = '::signin_settings';
    const MODULE_ATTR = '::attr_settings';
    const MODULE_ROLE = '::role_settings';
    const MODULE_FAQ = '::faq_settings';
    const METADATA_DOWNLOAD = '::metadata';
    const MODULE_ACCOUNT = '::account_settings';
    const MODULE_SUPPORT = '::support';
    const MODULE_UPGRADE = '::upgrade';

    const MODULE_IMAGES = '::images/';
    const MODULE_CERTS = '::certs/';
    const MODULE_CSS = '::css/';
    const MODULE_JS = '::js/';
    const MODULE_GUIDES = '::idpsetupguides/';
    const MODULE_METADATA = '::metadata/metadata.xml';

    // request option parameter values
    const LOGIN_ADMIN_OPT = 'loginAdminUser';
    const TEST_CONFIG_OPT = 'testConfig';
    const SAML_SSO_FALSE = 'saml_sso';

    //database keys
    const SESSION_INDEX = 'sessionIndex';
    const NAME_ID = 'nameId';
    const IDP_NAME = 'identityProviderName';
    const X509CERT = 'certificate';
    const RESPONSE_SIGNED = 'responseSigned';
    const ASSERTION_SIGNED = 'assertionSigned';
    const ISSUER = 'samlIssuer';
    const DB_FIRSTNAME = 'firstname';
    const DB_LASTNAME = 'lastname';
    const AUTO_REDIRECT = 'autoRedirect';
    const AUTO_REDIRECT_APP = 'autoRedirectappname';
    const SAML_SSO_URL = 'ssourl';
    const IDP_COUNT = 'idpcount';
    const SAML_SLO_URL = 'logouturl';
    const BINDING_TYPE = 'loginBindingType';
    const LOGOUT_BINDING = 'logoutBindingType';
    const FORCE_AUTHN = 'forceAuthn';
    const SAMLSP_KEY = 'customerKey';
    const SAMLSP_EMAIL = 'email';
    const SAMLSP_PHONE = 'phone';
    const SAMLSP_CNAME = 'cname';
    const SAMLSP_FIRSTNAME = 'customerFirstName';
    const SAMLSP_LASTNAME = 'customerLastName';
    const SAMLSP_CKL = 'ckl';
    const SAMLSP_LK = 'lk';
    const BACKDOOR = 'backdoor';
    const SHOW_ADMIN_LINK = 'showadminlink';
    const SHOW_CUSTOMER_LINK = 'showcustomerlink';
    const REG_STATUS = 'registrationStatus';
    const API_KEY = 'apiKey';
    const TOKEN = 'token';
    const BUTTON_TEXT = 'buttonText';
    const OTP_TYPE = 'otpType';
    const AUTO_CREATE_ADMIN = 'mo_saml_auto_create_admin';
    const AUTO_CREATE_CUSTOMER = 'mo_saml_auto_create_customer';
    const DISABLE_B2C = 'mo_saml_disable_b2c';
    const INSTALLATION_DATE = 'installation_date';
    const IS_TRIAL_EXTENDED = 'is_trial_extended';
    const SEND_EXPIRED_EMAIL = 'send_expired_email';
    const LICENSE_EXPIRY_DATE = 'license_expiry';
    const SAMLSP_LICENSE_ALERT_SENT = "samlsp_license_alert_sent";
    const DEFAULT_TOKEN = 'default_token';
    const DEFAULT_TOKEN_VALUE = 'E7XIXCVVUOYAIA2';
    const MAGENTO_COUNTER = 'magento_counter';
    const ADMIN_AUTO_REDIRECT = 'adminAutoRedirect';

    // B2B related constants;
    const B2B_STORE_URL = 'b2bStoreUrl';
    const B2C_STORE_URL = 'b2cStoreUrl';
    const B2B_ACCOUNT_ID = 'account_id';
    const COMPANY_TABLE = 'company';
    const AX_COMPANY_ATTR = 'ax_company_id';

    //Attribute Mapping Constants (names)
    const MAP_EMAIL = 'amEmail';
    const MAP_USERNAME = 'amUsername';
    const MAP_FIRSTNAME = 'amFirstName';
    const MAP_LASTNAME = 'amLastName';
    const MAP_GROUP = 'amGroupName';
    const MAP_MAP_BY = 'amAccountMatcher';
    const MAP_COMPANY_ID = 'amCompanyId';

    const MAP_COUNTRY_BILLING = 'ambillingCountry';
    const MAP_CITY_BILLING = 'ambillingCity';
    const MAP_ADDRESS_BILLING = 'ambillingAddress';
    const MAP_PHONE_BILLING = 'ambillingPhone';
    const MAP_STATE_BILLING = 'ambillingState';
    const MAP_ZIPCODE_BILLING = 'ambillingZipcode';

    const MAP_COUNTRY_SHIPPING = 'amshippingCountry';
    const MAP_CITY_SHIPPING = 'amshippingCity';
    const MAP_ADDRESS_SHIPPING = 'amshippingAddress';
    const MAP_PHONE_SHIPPING = 'amshippingPhone';
    const MAP_STATE_SHIPPING = 'amshippingState';
    const MAP_ZIPCODE_SHIPPING = 'amshippingZipcode';

    //Default Attributes values if no mapping found
    const DEFAULT_MAP_EMAIL = 'email';
    const DEFAULT_MAP_USERN = 'username';
    const DEFAULT_MAP_FN = 'firstName';
    const DEFAULT_MAP_LN = 'lastName';
    const DEFAULT_MAP_BY = 'email';
    const DEFAULT_GROUP = 'General';
    const DEFAULT_ROLE = 'Administrators';

    //default  attribute mapping constants
    const DEFAULT_MAP_CN = 'country';
    const DEFAULT_MAP_CITY = 'city';
    const DEFAULT_MAP_PHONE = 'phone';
    const DEFAULT_MAP_ADD = 'streetaddress';
    const DEFAULT_MAP_STATE = 'state';
    const DEFAULT_MAP_ZIP = 'zipcode';
    const CUSTOM_MAPPED = 'customAttributeMapping';


    const MAP_DEFAULT_ROLE = 'defaultRole';
    const MAP_DEFAULT_GROUP = 'defaultGroup';

    const TEST_RELAYSTATE = 'testvalidate';
    const UNLISTED_ROLE = 'unlistedRole';
    const CREATEIFNOTMAP = 'createUserIfRoleNotMapped';
    const ROLES_MAPPED = 'samlAdminRoleMapping';
    const GROUPS_MAPPED = 'samlCustomerRoleMapping';
    const UPDATEROLE = 'updateRole';
    const UPDATEFRONTENDROLE = 'updateFrontendRole';
    const UPDATEATTRIBUTE = 'updateAttibute';

    //Tables
    const MAP_TABLE = 'amTable';
    const COLUMN_ENTITY = "entity_id";

    //Column Not Found

    const COLUMN_NOT_FOUND = "Column Not Found for the configured table.Please enter correct column name";


    //SUFFIX_URLs
    const SUFFIX_ISSUER_URL_PATH = 'mospsaml/metadata/index';
    const SUFFIX_SAML_LOGIN_URL = 'mospsaml/actions/sendAuthnRequest';
    const SUFFIX_SPOBSERVER = 'mospsaml/actions/spObserver';
    const SUFFIX_ACCOUNT_LOGIN = 'customer/account/login';
    const LOGOUT_AUTO_REDIRECT_URL = 'saml_postlogout_url';


    //session data
    const USER_LOGOUT_DETAIL = 'userDetails';
    const SEND_RESPONSE = 'sendLogoutResponse';
    const LOGOUT_REQUEST_ID = 'logoutRequestId';
    const TXT_ID = 'miniorange/samlsp/transactionID';

    //images
    const IMAGE_RIGHT = 'right.png';
    const IMAGE_WRONG = 'wrong.png';

    //certs
    const SP_KEY = 'sp-key.key';

    const SP_KEYS = '-----BEGIN PRIVATE KEY-----
	MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAN6Wb3vWL0VEwAOt
	waY1SPqDyupjbxgs4dMWBvWZDMB/5y2XhqZ3utyaJcCt/sgTsHjk9AUC8/9UxlYO
	ljo9V0TT1gu3gVkHCzq1/2Y0tLyM5gszZZ4nmWfa4iD26RdCguZLGkda5BvGspsg
	LFoJJIGUAgvQHbz/C8VX/Dd875RVAgMBAAECgYEAvUonkqOJ3ZlixX4dgbAs2MX9
	aSiYUHHStcU0s+WtH4Nl4LLMkoKdiX8Zfes6EYIVACqMjjp9r3SzmnmbLfn+XHbc
	EZT+N40Cci+nedlDqhLgkfr2lg26DtZ3Sjk7kJnLHxhFVqWO4eaullcqn0wgqUyW
	sJ8vX0tEeCK7L8DCtJ0CQQDu1cFN8aiQdZLf5oeZ5GHnZbEiKJ3oOw4GJLu3Odq2
	dftZsdWHSSJ9pVD07+JuLUtV+vqdy1MKzDsJzbb2bETrAkEA7pXAP9PE5dzS4UPc
	XUisvsMAbkAm48ga6MCbj/7I2BScsZ9+/dWBI8B4hXQ1sAfm7sdpb1utKlOY8927
	EVI7vwJAP0aMfyz+Hr+3mPBHjsMOGTM8+bLPGx7COWhz/zgptNuPKxVNYBlFNQqe
	ZzZCxDPl2LK0wSeEKcEwBwnkZmcK3wJBAOcgKx2qCRSk16ViGBhGTxJ91ez4OLRx
	JaBU9l6IdAjf7uwjluJP8sqvqhGegmQFQ7INfBZkuVxHn+Se6JnfEAECQDJq0Fvi
	Ezbp2ziTla1MCJ2DAVAka2ZpRtgAX5tT1ES8lrtgBfsXggj0mz2xnE78WfY4CLYs
	CeDDPLctn+tuIns=
	-----END PRIVATE KEY-----';

    const ALTERNATE_KEY = 'miniorange_sp_priv_key.key';
    const PUBLIC_KEY = 'sp-certificate.crt';

    //SAML Constants
    const SAML = 'SAML';
    const AUTHN_REQUEST = 'AuthnRequest';
    const SAML_RESPONSE = 'SamlResponse';
    const WS_FED_RESPONSE = 'WsFedResponse';
    const HTTP_REDIRECT = 'HttpRedirect';
    const LOGOUT_REQUEST = 'LogoutRequest';

    //OTP Constants
    const OTP_TYPE_EMAIL = 'email';
    const OTP_TYPE_PHONE = 'sms';

    //Registration Status
    const STATUS_VERIFY_LOGIN = "MO_VERIFY_CUSTOMER";
    const STATUS_COMPLETE_LOGIN = "MO_VERIFIED";
    const STATUS_VERIFY_EMAIL = "MO_OTP_EMAIL_VALIDATE";

    //plugin constants
    const DEFAULT_CUSTOMER_KEY = "16555";
    const DEFAULT_API_KEY = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
    const APPLICATION_NAME = "MAGENTO_SAML_ENTERPRISE_EXTENSION";
    const HOSTNAME = "https://login.xecurify.com";
    const AREA_OF_INTEREST = 'Magento 2.0 Saml SP Enterprise Plugin'; //change according to license plan selected
    const DB_USER = 'user';
    const LICENSE_PLAN = 'magento_saml_enterprise_plan'; //change according to license plan selected

    //constants for multisite check
    const WEBSITE_COUNT = 'website count';
    const WEBSITE_IDS = 'website ids';
    const WEBSITES_LIMIT = 'websites limit';
    const enable = 'checked';
    const disable = 'unchecked';
    const DEFAULT_PROVIDER = 'default_provider';

    const ALL_PAGE_AUTO_REDIRECT = 'allPageAutoRedirect';
    const SAML_LOGIN_URL = 'mospsaml/actions/sendAuthnRequest';

    //Debug log
    const ENABLE_DEBUG_LOG = 'debug_log_saml_all_inclusive';
    const LOG_FILE_TIME = 'log_file_time';
    const SEND_EMAIL = 'send_email';
    const ADMINEMAIL = 'admin_email';
    const VERSION = 'v13.1.2';


}
