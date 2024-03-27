<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\App\ResponseInterface;
use MiniOrange\SP\Helper\Exception\InvalidDestinationException;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\Exception\InvalidAudienceException;
use MiniOrange\SP\Helper\Exception\InvalidIssuerException;
use MiniOrange\SP\Helper\Exception\InvalidSignatureInResponseException;
use MiniOrange\SP\Helper\Exception\InvalidSamlStatusCodeException;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Actions\CheckAttributeMappingAction;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseFactory;
class ProcessResponseAction extends BaseAction
{
    private $samlResponse;
    protected $certfpFromPlugin;
    private $acsUrl;
    private $relayState;
    private $responseSigned;
    private $assertionSigned;
    private $issuer;
    private $spEntityId;
    private $attrMappingAction;
    protected $x509_certificate;
    public function __construct(Context $Gc, SPUtility $Kx, CheckAttributeMappingAction $q4, StoreManagerInterface $Wl, ResultFactory $UZ, ResponseFactory $XF)
    {
        $this->attrMappingAction = $q4;
        parent::__construct($Gc, $Kx, $Wl, $UZ, $XF);
    }
    public function execute()
    {
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\144\160\x5f\x6e\141\x6d\x65"] === $rQ)) {
                goto eu;
            }
            $ft = $fR->getData();
            eu:
            cW:
        }
        mx:
        $this->spUtility->log_debug("\160\162\157\x63\145\163\x73\x72\145\163\160\x6f\x6e\x73\145\101\143\x74\151\x6f\156", print_r($ft, true));
        $this->x509_certificate = $ft["\x78\65\x30\x39\137\143\145\x72\164\151\x66\151\x63\141\164\145"];
        $this->responseSigned = $ft["\162\x65\163\x70\157\x6e\x73\x65\137\x73\x69\147\x6e\x65\144"];
        $this->assertionSigned = $ft["\141\163\163\145\x72\164\x69\157\x6e\137\x73\x69\147\x6e\145\x64"];
        $this->issuer = $ft["\151\x64\x70\137\x65\156\164\151\x74\171\137\151\x64"];
        $this->spUtility->log_debug("\40\x69\x6e\x20\160\x72\x6f\143\x65\x73\163\122\145\x73\x70\157\156\x73\145\x41\143\x74\151\x6f\156\x20\x3a");
        $this->spEntityId = $this->spUtility->getIssuerUrl();
        $this->acsUrl = $this->spUtility->getAcsUrl();
        $this->spUtility->log_debug("\x20\x70\x72\157\143\145\163\163\122\145\x73\x70\x6f\156\x73\145\101\143\164\x69\x6f\156\40\x3a\40\145\x78\x65\143\x75\x74\x65\x3a\40\166\141\154\x69\144\141\164\145\144\40\163\164\x61\164\x75\x73\40\x63\x6f\144\145");
        $QD = $this->samlResponse->getSignatureData();
        $this->spUtility->log_debug("\x20\x70\162\x6f\143\x65\x73\x73\x52\x65\163\x70\157\x6e\163\x65\x41\143\164\x69\157\x6e\x20\72\x20\141\x66\164\145\x72\x20\162\145\163\160\x6f\x6e\x73\145\x53\x69\147\x6e\141\x74\165\x72\x65\x44\x61\164\x61");
        $fH = current($this->samlResponse->getAssertions())->getSignatureData();
        $this->spUtility->log_debug("\40\x70\162\157\143\x65\x73\163\x52\x65\x73\x70\x6f\156\163\145\101\x63\x74\x69\x6f\156\40\x3a\40\141\x66\x74\145\x72\40\x61\163\163\x65\x72\164\x69\157\156\123\x69\x67\x6e\141\x74\165\x72\x65\104\141\x74\141");
        $this->certfpFromPlugin = XMLSecurityKey::getRawThumbprint($ft["\x78\x35\60\71\x5f\143\145\162\x74\151\x66\151\143\141\164\145"]);
        $this->certfpFromPlugin = iconv("\125\124\106\x2d\x38", "\x43\x50\61\62\65\62\57\x2f\x49\x47\x4e\x4f\x52\x45", $this->certfpFromPlugin);
        $this->spUtility->log_debug("\40\x70\x72\x6f\x63\145\x73\163\122\x65\163\160\x6f\156\163\x65\x41\x63\164\x69\157\x6e\40\x3a\40\x61\x66\164\145\x72\40\143\x65\x72\x74\146\160\106\x72\157\x6d\x50\154\x75\x67\151\156");
        $this->certfpFromPlugin = preg_replace("\x2f\x5c\163\53\57", '', $this->certfpFromPlugin);
        $this->spUtility->log_debug("\40\160\162\x6f\143\x65\163\x73\122\145\163\x70\157\x6e\x73\145\x41\143\x74\151\x6f\x6e\40\x3a\40\141\146\x74\145\162\x20\x63\x65\162\164\146\x70\x46\162\157\155\x50\x6c\165\147\x69\x6e\x20\61");
        $PO = FALSE;
        if (empty($QD)) {
            goto lC;
        }
        $hj = $ft["\x78\65\x30\71\137\143\x65\162\164\x69\146\151\x63\141\164\145"];
        $this->spUtility->log_debug("\40\x70\x72\x6f\143\x65\163\163\122\145\x73\x70\157\156\x73\145\101\x63\x74\x69\x6f\156\40\x3a\x20\x62\x65\x66\157\x72\145\40\x76\141\x6c\151\144\x61\x74\145\x52\145\x73\x70\157\x6e\x73\145\123\x69\147\156\141\164\x75\162\x65\x20\146\165\x6e\143\x74\151\x6f\x6e");
        $PO = $this->validateResponseSignature($QD, $hj);
        $this->spUtility->log_debug("\x20\x70\162\x6f\x63\x65\x73\163\x52\145\163\x70\157\156\163\145\101\x63\164\x69\x6f\156\40\72\40\40\x61\x66\x74\145\x72\x20\166\x61\x6c\x69\x64\141\x74\145\x52\145\x73\160\x6f\156\x73\145\123\151\147\x6e\141\164\x75\162\145\x20\x66\x75\x6e\143\164\x69\157\x6e");
        lC:
        if (empty($fH)) {
            goto BW;
        }
        $hj = $ft["\x78\x35\60\x39\137\x63\145\x72\164\x69\146\x69\143\141\164\145"];
        $this->spUtility->log_debug("\x20\160\162\x6f\143\x65\x73\x73\122\145\x73\x70\x6f\x6e\163\145\101\x63\x74\x69\x6f\x6e\40\x3a\40\x62\145\x66\157\162\145\x20\166\x61\154\151\144\x61\164\x65\101\x73\x73\x65\x72\x74\x69\x6f\x6e\x53\151\147\x6e\x61\x74\165\x72\145\40\146\x75\156\x63\164\151\157\x6e");
        $PO = $this->validateAssertionSignature($fH, $hj);
        $this->spUtility->log_debug("\x20\x70\x72\157\143\x65\x73\163\122\145\x73\x70\157\156\x73\x65\x41\x63\164\151\x6f\156\x20\x3a\40\141\146\x74\x65\x72\x20\x76\141\x6c\x69\144\x61\164\145\x41\x73\x73\x65\x72\x74\151\x6f\x6e\123\151\x67\x6e\141\x74\x75\162\145\40\x66\165\156\143\164\x69\157\156");
        BW:
        if ($PO) {
            goto qf;
        }
        $rO = "\x57\x65\40\x63\157\x75\154\144\40\156\x6f\x74\x20\163\151\147\x6e\40\x79\157\165\40\x69\x6e\x2e";
        $by = "\x50\154\x65\141\x73\145\40\x43\157\156\x74\141\x63\164\40\x79\x6f\x75\162\40\x61\x64\x6d\x69\x6e\x69\x73\x74\162\x61\164\157\x72\56";
        $h5 = '';
        $Hv = FALSE;
        if ($PO) {
            goto e_;
        }
        $rO = "\111\x6e\166\x61\x6c\x69\x64\40\123\151\x67\156\x61\164\165\162\x65\40\x69\x6e\x20\x53\x41\115\114\x20\x72\145\163\160\x6f\x6e\x73\x65";
        $by = '';
        $h5 = "\x4e\x65\x69\x74\150\145\x72\x20\162\x65\x73\x70\157\x6e\163\145\x20\156\x6f\162\40\141\163\x73\x65\x72\x74\x69\x6f\156\40\151\x73\x20\163\151\147\156\145\x64\40\x62\171\x20\111\104\x50";
        $Hv = TRUE;
        e_:
        self::showErrorMessage($rO, $by, $h5, $Hv);
        exit;
        qf:
        $this->attrMappingAction->setSamlResponse($this->samlResponse)->setRelayState($this->relayState)->execute();
    }
    public static function showErrorMessage($rO, $by, $h5, $Hv = FALSE)
    {
        $gn = $Hv === TRUE ? "\157\x6e\103\x6c\x69\143\x6b\x3d\42\163\145\154\x66\56\x63\x6c\157\163\145\x28\51\x3b\x22" : "\x68\x72\x65\146\75\42" . $base_url . "\57\165\163\x65\162\57\x6c\157\x67\x69\x6e\x22";
        echo "\74\x64\x69\166\x20\163\x74\x79\x6c\145\75\x22\146\x6f\x6e\x74\x2d\x66\x61\155\x69\154\x79\x3a\x43\x61\154\151\142\x72\151\73\x70\141\x64\144\x69\x6e\147\72\60\40\63\45\x3b\x22\x3e";
        echo "\x3c\144\151\166\x20\163\164\171\x6c\x65\x3d\x22\143\157\x6c\x6f\x72\72\x20\x23\141\71\64\64\64\62\73\142\141\143\x6b\x67\x72\x6f\165\x6e\x64\55\x63\157\x6c\x6f\x72\x3a\40\43\x66\62\144\145\x64\145\73\x70\x61\x64\144\x69\x6e\147\72\40\61\x35\160\170\73\155\141\x72\147\151\156\x2d\142\157\164\x74\157\x6d\x3a\40\62\x30\160\170\73\x74\x65\x78\164\55\x61\x6c\x69\x67\x6e\x3a\x63\x65\156\x74\x65\162\73\142\157\x72\144\x65\x72\x3a\x31\x70\170\x20\163\157\x6c\151\x64\x20\43\x45\x36\x42\x33\x42\x32\x3b\x66\x6f\x6e\164\x2d\x73\151\172\145\x3a\x31\70\160\164\73\42\x3e\40\105\122\x52\x4f\x52\x3c\x2f\x64\x69\166\x3e\15\12\x20\40\40\x20\x20\40\40\40\40\x20\x20\x20\40\40\x20\x20\40\x20\x20\x20\x20\40\x20\40\40\40\x20\40\74\x64\x69\x76\x20\163\x74\171\154\145\x3d\x22\143\157\x6c\157\162\x3a\x20\43\141\x39\64\64\x34\x32\73\146\157\156\164\55\x73\x69\172\145\72\61\x34\160\164\73\x20\155\141\162\147\151\x6e\55\x62\157\x74\164\157\155\72\62\x30\160\170\73\42\76\x3c\160\x3e\74\x73\164\162\x6f\156\x67\76\x45\x72\162\157\162\72\40\74\57\x73\x74\162\x6f\x6e\x67\76" . $rO . "\x3c\57\160\76\xd\12\x20\40\40\40\x20\x20\x20\x20\40\x20\x20\40\40\x20\40\x20\40\40\x20\40\40\40\x20\40\40\x20\40\40\x20\40\40\40\74\x70\x3e" . $by . "\74\57\160\x3e\xd\12\40\x20\40\40\x20\x20\40\40\40\x20\x20\x20\x20\x20\x20\40\x20\x20\40\x20\x20\40\40\40\40\40\x20\x20\x20\x20\x20\40\74\160\76\74\163\164\162\x6f\156\x67\x3e\x50\x6f\163\163\x69\142\x6c\x65\x20\103\x61\165\163\x65\72\x20\x3c\57\x73\x74\x72\x6f\156\147\x3e" . $h5 . "\x3c\x2f\160\x3e\15\xa\x20\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\40\40\40\x20\40\x20\x20\x20\40\40\40\x20\x20\40\x20\x20\x3c\57\x64\x69\166\x3e\xd\xa\x20\x20\40\x20\x20\40\40\x20\40\x20\40\x20\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\40\40\40\x20\40\x3c\x64\151\166\x20\x73\164\171\154\x65\x3d\x22\155\x61\162\147\x69\156\x3a\63\45\x3b\144\x69\163\x70\x6c\x61\171\72\142\154\157\143\153\73\x74\145\x78\164\x2d\141\x6c\x69\x67\156\x3a\143\145\156\x74\x65\x72\x3b\x22\x3e\74\x2f\144\x69\166\x3e\xd\12\x20\40\40\40\x20\x20\40\x20\40\40\x20\40\x20\x20\x20\x20\40\40\x20\40\x20\40\40\40\x20\x20\40\40\74\144\151\x76\40\x73\x74\171\154\x65\75\x22\155\x61\x72\x67\151\156\72\x33\45\73\x64\x69\x73\x70\154\141\x79\72\x62\154\x6f\143\153\x3b\164\145\170\164\x2d\141\154\x69\147\x6e\72\x63\145\x6e\164\x65\x72\73\x22\x3e\15\12\x20\40\40\x20\40\40\40\40\x20\x20\40\40\x20\40\40\40\40\40\x20\40\40\40\x20\x20\x20\x20\40\x20\40\40\x20\40\74\x61\40\163\x74\171\x6c\x65\x3d\42\160\141\144\144\151\156\x67\x3a\61\45\x3b\167\151\144\x74\x68\x3a\61\x30\60\x70\x78\x3b\x62\x61\x63\153\147\x72\157\x75\156\144\x3a\x20\x23\60\x30\71\x31\x43\x44\40\x6e\x6f\x6e\145\x20\162\x65\x70\x65\141\x74\x20\163\x63\162\x6f\154\154\x20\60\45\x20\60\x25\73\143\165\x72\163\157\x72\72\x20\x70\157\151\x6e\164\145\x72\x3b\x66\x6f\x6e\x74\55\x73\x69\x7a\x65\x3a\61\x35\160\x78\x3b\x62\157\x72\144\145\x72\x2d\167\x69\x64\164\x68\72\x20\61\160\170\x3b\x62\x6f\x72\x64\x65\x72\55\x73\164\171\x6c\145\x3a\x20\x73\x6f\154\x69\x64\x3b\142\x6f\162\144\145\x72\55\162\141\x64\x69\x75\163\72\40\63\160\170\73\x77\150\x69\x74\145\55\163\160\141\x63\145\x3a\x20\x6e\x6f\167\x72\141\160\73\x62\157\170\x2d\x73\151\x7a\151\x6e\x67\72\x20\x62\157\162\x64\x65\162\55\142\157\170\73\x62\x6f\162\144\145\162\x2d\x63\157\x6c\157\162\72\40\x23\x30\60\67\x33\101\101\x3b\x62\157\170\55\163\x68\141\144\x6f\167\x3a\x20\x30\160\170\x20\x31\160\x78\40\x30\x70\170\x20\x72\147\x62\141\50\61\x32\x30\x2c\40\x32\x30\60\54\x20\x32\63\x30\x2c\x20\60\x2e\66\x29\x20\151\x6e\x73\145\x74\x3b\143\x6f\154\157\162\72\x20\x23\x46\x46\106\73\x20\164\145\170\164\x2d\144\145\x63\x6f\162\x61\164\x69\x6f\x6e\x3a\x20\x6e\x6f\156\x65\x3b\42\164\171\160\145\x3d\42\142\165\x74\164\x6f\x6e\42\40\40" . $gn . "\40\76\x44\157\x6e\x65\x3c\57\x61\76\15\xa\40\40\40\40\40\x20\x20\x20\x20\40\40\x20\40\x20\40\40\x20\40\x20\x20\40\x20\40\x20\40\40\x20\x20\x3c\x2f\x64\151\166\76";
        exit;
    }
    private function validateResponseSignature($QD, $hj)
    {
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $R6 = $this->spUtility->getAcsUrl();
        $this->spUtility->log_debug("\x20\x70\162\157\143\145\163\163\x52\x65\163\x70\x6f\x6e\x73\x65\x41\143\164\151\157\x6e\x20\x3a\40\166\141\154\151\x64\141\x74\x65\x52\x65\163\x70\x6f\156\x73\x65\123\151\147\x6e\x61\164\x75\x72\x65\x3a\x20\162\145\x73\x70\x6f\x6e\163\x65\40\x73\151\x67\x6e\145\x64\x3a\40", $this->responseSigned);
        if (!empty($QD)) {
            goto I0;
        }
        return;
        I0:
        $XB = SAML2Utilities::processResponse($rQ, $R6, $this->certfpFromPlugin, $QD, $this->samlResponse, $hj);
        return $XB;
    }
    private function validateStatusCode()
    {
        $AY = $this->samlResponse->getStatusCode();
        if (!(strpos($AY, "\x53\x75\143\143\x65\163\163") === false)) {
            goto nR;
        }
        throw new InvalidSamlStatusCodeException($AY, $this->{$CN}->getXML());
        nR:
    }
    private function validateAssertionSignature($fH, $hj)
    {
        $this->spUtility->log_debug("\x20\x70\162\157\143\145\163\x73\x52\145\x73\x70\x6f\156\x73\x65\101\x63\164\x69\157\x6e\x20\x3a\x20\x49\156\40\166\x61\154\x69\144\141\164\145\x41\x73\x73\145\x72\164\151\157\x6e\x53\x69\x67\156\141\164\x75\162\145\40\146\x75\156\143\164\x69\x6f\156");
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $R6 = $this->spUtility->getAcsUrl();
        if (!empty($fH)) {
            goto UJ;
        }
        return;
        UJ:
        $XB = SAML2Utilities::processResponse($rQ, $R6, $this->certfpFromPlugin, $fH, $this->samlResponse, $hj);
        return $XB;
    }
    private function validateIssuerAndAudience()
    {
        $wz = current($this->samlResponse->getAssertions())->getIssuer();
        $JI = current(current($this->samlResponse->getAssertions())->getValidAudiences());
        if (!(strcmp($this->issuer, $wz) != 0)) {
            goto BB;
        }
        throw new InvalidIssuerException($this->issuer, $wz, $this->samlResponse->getXML());
        BB:
        if (!(strcmp($JI, $this->spEntityId) != 0)) {
            goto eO;
        }
        throw new InvalidAudienceException($this->spEntityId, $JI, $this->samlResponse->getXML());
        eO:
    }
    public function setSamlResponse($CN)
    {
        $this->samlResponse = $CN;
        return $this;
    }
    public function setRelayState($qY)
    {
        $this->spUtility->log_debug("\x73\x65\164\164\x69\156\147\40\162\145\154\141\171\163\x74\141\164\x65\40\x74\157\72\x20", $qY);
        $this->relayState = $qY;
        return $this;
    }
}
