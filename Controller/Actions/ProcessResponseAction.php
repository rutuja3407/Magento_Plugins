<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Exception\InvalidAudienceException;
use MiniOrange\SP\Helper\Exception\InvalidDestinationException;
use MiniOrange\SP\Helper\Exception\InvalidIssuerException;
use MiniOrange\SP\Helper\Exception\InvalidSamlStatusCodeException;
use MiniOrange\SP\Helper\Exception\InvalidSignatureInResponseException;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
class ProcessResponseAction extends BaseAction
{
    protected $certfpFromPlugin;
    protected $x509_certificate;
    private $samlResponse;
    private $acsUrl;
    private $relayState;
    private $responseSigned;
    private $assertionSigned;
    private $issuer;
    private $spEntityId;
    private $attrMappingAction;
    public function __construct(Context $gt, SPUtility $fR, CheckAttributeMappingAction $xD, StoreManagerInterface $VO, ResultFactory $ps, ResponseFactory $Jv)
    {
        $this->attrMappingAction = $xD;
        parent::__construct($gt, $fR, $VO, $ps, $Jv);
    }
    public function execute()
    {
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\x64\x70\x5f\156\141\155\145"] === $rq)) {
                goto nI;
            }
            $hR = $ub->getData();
            nI:
            K8:
        }
        jC:
        $this->spUtility->log_debug("\160\162\157\x63\x65\x73\x73\162\x65\x73\160\157\156\163\145\x41\143\x74\151\157\x6e", print_r($hR, true));
        $this->x509_certificate = $hR["\170\x35\60\x39\x5f\x63\x65\162\164\x69\x66\151\x63\141\x74\145"];
        $this->responseSigned = $hR["\x72\x65\x73\x70\157\156\163\145\x5f\163\x69\147\156\145\x64"];
        $this->assertionSigned = $hR["\141\x73\163\145\x72\164\x69\x6f\156\137\163\151\147\x6e\145\144"];
        $this->issuer = $hR["\151\144\160\x5f\145\x6e\x74\x69\164\171\x5f\x69\x64"];
        $this->spUtility->log_debug("\40\x69\x6e\40\x70\x72\x6f\143\x65\x73\x73\x52\145\163\160\157\156\163\x65\101\x63\x74\151\157\x6e\x20\x3a");
        $this->spEntityId = $this->spUtility->getIssuerUrl();
        $this->acsUrl = $this->spUtility->getAcsUrl();
        $this->spUtility->log_debug("\x20\x70\x72\x6f\143\145\163\163\x52\145\x73\x70\x6f\x6e\x73\145\x41\x63\164\x69\x6f\156\40\72\40\145\x78\145\x63\x75\164\145\72\x20\166\141\154\x69\x64\x61\x74\x65\x64\40\x73\x74\141\164\x75\x73\40\143\x6f\144\x65");
        $U_ = $this->samlResponse->getSignatureData();
        $this->spUtility->log_debug("\40\x70\162\157\x63\145\163\163\x52\x65\163\x70\x6f\x6e\163\x65\x41\x63\164\151\157\156\x20\72\x20\141\x66\164\x65\162\x20\162\x65\x73\x70\x6f\156\x73\x65\123\151\x67\156\x61\164\165\162\145\104\x61\x74\x61");
        $sl = current($this->samlResponse->getAssertions())->getSignatureData();
        $this->spUtility->log_debug("\x20\x70\162\157\143\145\x73\163\x52\x65\163\160\157\x6e\163\x65\x41\x63\x74\x69\x6f\156\x20\72\40\x61\146\164\x65\162\40\x61\163\x73\145\162\x74\151\x6f\156\123\151\x67\x6e\x61\x74\x75\162\x65\104\141\164\x61");
        $this->certfpFromPlugin = XMLSecurityKey::getRawThumbprint($hR["\x78\x35\x30\x39\x5f\143\x65\x72\164\x69\146\x69\x63\x61\x74\x65"]);
        $this->certfpFromPlugin = iconv("\125\124\106\55\x38", "\103\x50\x31\62\x35\62\x2f\57\x49\107\116\x4f\122\x45", $this->certfpFromPlugin);
        $this->spUtility->log_debug("\x20\160\x72\157\x63\x65\163\x73\x52\145\x73\x70\x6f\156\163\145\101\143\x74\151\x6f\x6e\40\72\40\141\146\x74\145\x72\x20\143\145\162\x74\146\x70\x46\162\x6f\x6d\120\154\x75\147\151\156");
        $this->certfpFromPlugin = preg_replace("\57\x5c\x73\53\57", '', $this->certfpFromPlugin);
        $this->spUtility->log_debug("\x20\160\x72\157\143\145\163\x73\x52\145\x73\160\x6f\x6e\163\x65\x41\143\164\x69\157\x6e\40\x3a\x20\x61\x66\164\145\162\x20\143\145\162\164\x66\160\x46\x72\157\155\120\x6c\x75\147\x69\156\x20\x31");
        $hS = FALSE;
        if (empty($U_)) {
            goto Lc;
        }
        $d2 = $hR["\x78\65\x30\71\x5f\x63\145\162\164\151\146\151\143\141\x74\x65"];
        $this->spUtility->log_debug("\40\160\x72\157\143\145\x73\163\x52\x65\163\x70\x6f\x6e\x73\x65\x41\143\x74\x69\157\156\40\x3a\40\142\145\x66\157\x72\145\x20\x76\141\154\151\144\x61\164\145\122\x65\163\160\x6f\x6e\163\x65\123\x69\x67\156\x61\164\165\162\x65\x20\146\165\x6e\143\x74\151\x6f\x6e");
        $hS = $this->validateResponseSignature($U_, $d2);
        $this->spUtility->log_debug("\x20\x70\x72\x6f\143\x65\163\x73\x52\145\163\x70\x6f\x6e\163\x65\101\143\x74\x69\157\x6e\40\72\40\40\141\146\x74\145\x72\x20\x76\x61\x6c\x69\x64\141\x74\x65\x52\145\x73\160\157\156\163\145\x53\x69\147\156\x61\x74\165\162\145\x20\146\x75\x6e\x63\164\x69\x6f\x6e");
        Lc:
        if (empty($sl)) {
            goto y6;
        }
        $d2 = $hR["\170\x35\60\71\137\143\145\162\164\151\146\151\x63\141\x74\145"];
        $this->spUtility->log_debug("\40\160\x72\x6f\x63\x65\x73\x73\x52\x65\163\160\157\156\x73\145\101\x63\164\x69\x6f\156\40\x3a\40\142\145\146\157\x72\145\40\166\141\154\x69\x64\141\164\x65\101\x73\x73\x65\x72\x74\151\157\x6e\x53\151\x67\x6e\141\164\x75\x72\145\x20\x66\165\156\x63\x74\x69\x6f\x6e");
        $hS = $this->validateAssertionSignature($sl, $d2);
        $this->spUtility->log_debug("\40\160\x72\x6f\x63\x65\163\x73\x52\x65\163\160\x6f\x6e\x73\x65\x41\x63\x74\151\x6f\156\40\72\x20\x61\x66\x74\x65\162\x20\x76\141\x6c\x69\x64\x61\164\145\101\x73\163\x65\x72\x74\x69\157\156\x53\151\147\x6e\x61\x74\165\x72\x65\40\x66\165\156\x63\x74\151\x6f\156");
        y6:
        if ($hS) {
            goto Qg;
        }
        $Np = "\x57\145\40\x63\157\165\154\144\40\156\x6f\164\40\x73\x69\147\156\x20\171\157\x75\40\x69\156\x2e";
        $qx = "\x50\154\145\141\x73\x65\40\x43\x6f\156\x74\x61\143\x74\x20\171\x6f\x75\x72\x20\141\144\155\x69\156\x69\x73\164\x72\141\164\x6f\162\56";
        $rc = '';
        $Kc = FALSE;
        if ($hS) {
            goto Kq;
        }
        $Np = "\x49\x6e\166\141\154\x69\144\40\x53\x69\x67\x6e\141\164\165\x72\145\40\x69\x6e\40\123\101\115\114\40\162\x65\163\160\x6f\x6e\x73\145";
        $qx = '';
        $rc = "\x4e\x65\151\x74\150\x65\162\40\162\x65\163\x70\157\x6e\163\145\x20\156\157\162\x20\x61\163\163\x65\162\x74\x69\157\x6e\x20\151\163\x20\x73\151\x67\x6e\145\x64\x20\x62\x79\x20\111\104\x50";
        $Kc = TRUE;
        Kq:
        $this->showErrorMessage($Np, $qx, $rc, $Kc);
        exit;
        Qg:
        $this->attrMappingAction->setSamlResponse($this->samlResponse)->setRelayState($this->relayState)->execute();
    }
    private function validateResponseSignature($U_, $d2)
    {
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $X9 = $this->spUtility->getAcsUrl();
        $this->spUtility->log_debug("\40\160\162\157\x63\145\x73\x73\x52\145\x73\160\157\156\163\145\101\143\164\151\157\x6e\x20\72\x20\x76\141\154\151\144\141\164\x65\122\x65\x73\x70\x6f\x6e\x73\x65\123\x69\x67\156\x61\x74\x75\x72\145\x3a\40\x72\x65\x73\160\x6f\156\x73\x65\x20\x73\151\147\x6e\145\144\x3a\x20", $this->responseSigned);
        if (!empty($U_)) {
            goto vD;
        }
        return;
        vD:
        $u1 = SAML2Utilities::processResponse($rq, $X9, $this->certfpFromPlugin, $U_, $this->samlResponse, $d2);
        return $u1;
    }
    private function validateAssertionSignature($sl, $d2)
    {
        $this->spUtility->log_debug("\x20\x70\x72\x6f\x63\145\163\x73\122\145\x73\160\x6f\156\x73\x65\101\143\164\x69\157\x6e\x20\x3a\x20\111\x6e\40\166\141\x6c\x69\144\x61\x74\145\x41\x73\163\145\162\164\151\157\156\123\x69\147\x6e\141\164\x75\162\145\x20\x66\x75\x6e\x63\164\151\x6f\156");
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $X9 = $this->spUtility->getAcsUrl();
        if (!empty($sl)) {
            goto DI;
        }
        return;
        DI:
        $u1 = SAML2Utilities::processResponse($rq, $X9, $this->certfpFromPlugin, $sl, $this->samlResponse, $d2);
        return $u1;
    }
    public function showErrorMessage($Np, $qx, $rc, $Kc = FALSE)
    {
        $wd = $Kc === TRUE ? "\x6f\156\103\154\151\x63\x6b\75\42\163\x65\154\146\56\143\154\x6f\x73\145\50\51\73\x22" : "\150\x72\x65\x66\75\x22" . $this->spUtility->getBaseUrl() . "\57\165\x73\145\x72\57\x6c\x6f\x67\x69\156\42";
        echo "\74\x64\151\166\40\x73\164\x79\154\x65\x3d\42\x66\157\156\x74\55\146\x61\x6d\151\154\171\72\x43\x61\x6c\151\x62\x72\151\73\160\141\x64\x64\151\156\x67\x3a\x30\x20\63\45\x3b\x22\76";
        echo "\x3c\144\x69\166\40\x73\x74\171\154\145\75\x22\x63\x6f\154\157\x72\72\x20\43\x61\x39\x34\x34\x34\62\x3b\x62\141\x63\153\x67\x72\157\165\156\x64\x2d\143\x6f\x6c\157\x72\72\x20\x23\146\62\x64\x65\x64\145\73\x70\141\x64\x64\151\x6e\x67\72\x20\61\65\x70\x78\x3b\155\x61\162\147\151\156\55\142\157\x74\164\157\155\x3a\40\x32\60\x70\170\x3b\x74\145\170\x74\x2d\x61\x6c\151\x67\x6e\72\x63\145\156\164\x65\x72\73\x62\x6f\162\x64\145\162\72\61\160\170\40\163\x6f\154\x69\144\x20\43\105\x36\102\x33\102\62\73\146\x6f\156\x74\x2d\x73\151\172\x65\x3a\x31\70\x70\164\73\42\76\x20\x45\x52\122\117\122\x3c\x2f\144\151\166\76\xd\12\x20\x20\40\x20\x20\x20\40\x20\40\x20\x20\40\40\x20\x20\x20\40\40\x20\40\x20\40\x20\40\x20\40\x20\40\x3c\x64\x69\166\40\163\x74\x79\154\x65\x3d\42\143\157\154\157\x72\x3a\x20\x23\141\x39\x34\x34\x34\x32\x3b\146\x6f\156\x74\x2d\x73\x69\172\145\x3a\61\x34\x70\x74\73\x20\155\141\162\147\151\156\x2d\x62\157\x74\x74\157\155\x3a\x32\x30\x70\x78\73\42\x3e\74\160\76\x3c\x73\x74\x72\157\x6e\x67\x3e\x45\162\162\157\162\72\40\x3c\x2f\x73\x74\x72\x6f\x6e\147\76" . $Np . "\x3c\57\x70\x3e\xd\xa\x20\40\40\40\40\x20\x20\40\40\40\x20\40\x20\40\40\40\x20\x20\40\40\x20\x20\x20\40\40\x20\x20\40\x20\40\40\40\74\x70\76" . $qx . "\x3c\57\x70\76\xd\xa\x20\x20\x20\40\x20\40\x20\40\x20\40\x20\40\40\x20\40\40\x20\40\40\40\40\40\40\40\40\x20\x20\x20\40\x20\40\x20\74\160\x3e\x3c\163\164\162\157\156\x67\x3e\120\157\163\x73\151\x62\x6c\x65\40\103\141\x75\163\x65\x3a\40\x3c\x2f\x73\164\x72\157\156\147\76" . $rc . "\74\x2f\160\76\15\12\40\40\40\40\40\40\x20\x20\x20\x20\40\40\x20\x20\x20\x20\40\x20\x20\x20\x20\x20\x20\x20\x20\x20\40\40\74\57\144\151\166\76\xd\12\x20\x20\40\x20\40\40\x20\40\40\40\x20\40\40\40\40\40\40\x20\40\x20\x20\x20\40\40\x20\40\40\x20\74\x64\151\166\x20\x73\164\171\x6c\x65\75\x22\155\141\x72\x67\x69\x6e\x3a\63\45\x3b\144\151\x73\x70\x6c\141\x79\x3a\x62\x6c\157\143\x6b\x3b\164\x65\x78\164\55\141\154\151\147\156\72\143\145\x6e\164\145\162\x3b\42\76\74\57\144\151\166\x3e\xd\12\x20\40\40\40\x20\x20\x20\40\40\40\40\x20\40\x20\40\40\40\40\x20\x20\x20\40\x20\40\40\x20\40\x20\x3c\x64\x69\x76\x20\163\164\x79\x6c\145\x3d\x22\155\141\162\147\151\156\x3a\x33\45\73\144\151\163\x70\154\x61\x79\x3a\x62\154\x6f\143\153\73\164\x65\170\164\55\x61\x6c\x69\147\156\x3a\x63\x65\156\164\145\162\x3b\42\76\15\xa\40\x20\40\40\40\40\x20\x20\40\40\40\x20\x20\40\x20\40\x20\40\x20\40\x20\x20\40\x20\40\40\x20\x20\x20\40\x20\40\74\x61\40\163\164\171\154\145\75\x22\160\141\144\x64\151\156\147\72\x31\x25\73\x77\x69\144\x74\150\x3a\x31\x30\60\160\170\73\142\x61\x63\x6b\x67\x72\157\165\x6e\144\72\x20\43\x30\60\71\x31\x43\104\x20\156\x6f\156\x65\x20\x72\145\160\145\141\x74\x20\x73\x63\x72\x6f\154\154\x20\60\45\x20\60\x25\73\143\165\162\163\x6f\162\x3a\40\x70\157\x69\x6e\164\145\x72\73\x66\157\156\164\x2d\x73\x69\x7a\145\x3a\x31\65\160\x78\73\142\x6f\162\x64\145\x72\55\167\151\x64\x74\x68\x3a\x20\x31\160\x78\73\142\157\x72\x64\145\x72\55\x73\164\171\x6c\x65\x3a\40\x73\157\x6c\151\x64\x3b\142\x6f\162\144\145\162\x2d\162\141\144\151\165\x73\x3a\x20\x33\x70\x78\73\x77\150\151\x74\145\x2d\x73\160\x61\x63\x65\x3a\x20\156\x6f\167\162\141\160\x3b\142\157\170\x2d\x73\151\172\151\156\147\72\40\142\x6f\x72\x64\x65\162\55\x62\x6f\170\73\x62\157\x72\144\x65\x72\55\143\x6f\x6c\157\x72\x3a\40\x23\x30\x30\x37\63\x41\101\73\x62\157\x78\x2d\x73\150\x61\x64\x6f\167\x3a\40\x30\160\x78\x20\61\x70\x78\40\60\x70\170\x20\162\x67\x62\141\50\x31\x32\x30\54\x20\x32\x30\x30\54\40\62\x33\x30\x2c\40\60\x2e\66\x29\x20\151\156\163\145\x74\73\143\157\154\157\x72\72\x20\43\106\x46\x46\x3b\40\x74\145\x78\164\x2d\144\145\143\157\x72\x61\x74\151\157\156\72\x20\x6e\157\x6e\x65\x3b\x22\164\171\x70\x65\x3d\x22\142\x75\164\164\157\156\x22\x20\x20" . $wd . "\40\x3e\104\x6f\x6e\145\x3c\57\x61\76\15\12\40\40\x20\40\x20\x20\40\40\x20\40\40\40\x20\x20\x20\40\40\x20\40\40\x20\40\x20\x20\40\x20\x20\40\74\57\x64\151\x76\76";
        exit;
    }
    public function setRelayState($Nf)
    {
        $this->spUtility->log_debug("\163\145\x74\164\151\x6e\x67\40\162\145\154\141\x79\163\164\x61\x74\x65\x20\x74\x6f\x3a\x20", $Nf);
        $this->relayState = $Nf;
        return $this;
    }
    public function setSamlResponse($yO)
    {
        $this->samlResponse = $yO;
        return $this;
    }
    private function validateStatusCode()
    {
        $pj = $this->samlResponse->getStatusCode();
        if (!(strpos($pj, "\x53\165\x63\x63\x65\163\163") === false)) {
            goto Jx;
        }
        throw new InvalidSamlStatusCodeException($pj, $this->samlResponse->getXML());
        Jx:
    }
    private function validateIssuerAndAudience()
    {
        $wQ = current($this->samlResponse->getAssertions())->getIssuer();
        $GZ = current(current($this->samlResponse->getAssertions())->getValidAudiences());
        if (!(strcmp($this->issuer, $wQ) != 0)) {
            goto Ft;
        }
        throw new InvalidIssuerException($this->issuer, $wQ, $this->samlResponse->getXML());
        Ft:
        if (!(strcmp($GZ, $this->spEntityId) != 0)) {
            goto WA;
        }
        throw new InvalidAudienceException($this->spEntityId, $GZ, $this->samlResponse->getXML());
        WA:
    }
}
