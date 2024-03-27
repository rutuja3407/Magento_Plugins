<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Exception\InvalidNumberOfNameIDsException;
use MiniOrange\SP\Helper\Exception\InvalidRequestVersionException;
use MiniOrange\SP\Helper\Exception\MissingIDException;
use MiniOrange\SP\Helper\Exception\MissingNameIdException;
use MiniOrange\SP\Helper\SPConstants;
class LogoutRequest
{
    protected $spUtility;
    private $xml;
    private $tagName;
    private $id;
    private $issuer;
    private $destination;
    private $issueInstant;
    private $certificates;
    private $validators;
    private $notOnOrAfter;
    private $encryptedNameId;
    private $nameId;
    private $sessionIndexes;
    private $bindingType;
    private $requestType = SPConstants::LOGOUT_REQUEST;
    public function __construct(\MiniOrange\SP\Helper\SPUtility $fR, \DOMElement $xa = NULL)
    {
        $this->spUtility = $fR;
        $this->xml = new \DOMDocument("\61\56\60", "\x75\x74\146\55\70");
        if (!($xa === NULL)) {
            goto gR;
        }
        return;
        gR:
        $this->xml = $xa;
        $this->tagName = "\x4c\157\x67\157\x75\x74\x52\x65\161\x75\x65\x73\164";
        $this->id = $this->generateUniqueID(40);
        $this->issueInstant = time();
        $this->certificates = array();
        $this->validators = array();
        $this->issueInstant = SAML2Utilities::xsDateTimeToTimestamp($xa->getAttribute("\x49\x73\x73\x75\145\x49\x6e\163\x74\141\x6e\x74"));
        $this->parseID($xa);
        $this->checkSAMLVersion($xa);
        if (!$xa->hasAttribute("\x44\x65\x73\x74\x69\156\141\164\151\157\x6e")) {
            goto Wr;
        }
        $this->destination = $xa->getAttribute("\104\x65\163\164\x69\x6e\x61\164\151\157\x6e");
        Wr:
        $this->parseIssuer($xa);
        $this->parseAndValidateSignature($xa);
        if (!$xa->hasAttribute("\x4e\157\164\x4f\156\117\x72\101\x66\x74\x65\162")) {
            goto Mz;
        }
        $this->notOnOrAfter = SAML2Utilities::xsDateTimeToTimestamp($xa->getAttribute("\116\x6f\x74\x4f\156\x4f\162\101\x66\164\145\162"));
        Mz:
        $this->parseNameId($xa);
        $this->parseSessionIndexes($xa);
    }
    private function generateUniqueID($E4)
    {
        return SAML2Utilities::generateRandomAlphanumericValue($E4);
    }
    protected function parseID($xa)
    {
        if ($xa->hasAttribute("\111\x44")) {
            goto VZ;
        }
        throw new MissingIDException();
        VZ:
        $this->id = $xa->getAttribute("\x49\x44");
    }
    protected function checkSAMLVersion($xa)
    {
        if (!($xa->getAttribute("\x56\x65\162\x73\x69\x6f\156") !== "\x32\56\60")) {
            goto Z3;
        }
        throw InvalidRequestVersionException();
        Z3:
    }
    protected function parseIssuer($xa)
    {
        $wQ = SAML2Utilities::xpQuery($xa, "\56\x2f\163\141\x6d\x6c\137\141\163\x73\x65\162\164\x69\x6f\x6e\72\111\163\x73\x75\x65\162");
        if (empty($wQ)) {
            goto lM;
        }
        $this->issuer = trim($wQ[0]->textContent);
        lM:
    }
    protected function parseAndValidateSignature($xa)
    {
        $Ud = SAML2Utilities::validateElement($xa);
        if (!($Ud !== FALSE)) {
            goto pc;
        }
        $this->certificates = $Ud["\x43\x65\x72\x74\x69\146\151\143\x61\164\145\163"];
        $this->validators[] = array("\x46\x75\156\143\x74\x69\x6f\156" => array("\x53\x41\115\x4c\x55\164\x69\154\x69\164\151\145\163", "\x76\141\154\x69\144\141\x74\145\x53\151\x67\x6e\141\x74\x75\162\145"), "\x44\141\x74\x61" => $Ud);
        pc:
    }
    protected function parseNameId($xa)
    {
        $Au = SAML2Utilities::xpQuery($xa, "\x2e\57\x73\141\x6d\154\137\141\x73\x73\x65\162\x74\x69\157\x6e\72\x4e\141\x6d\145\111\x44\x20\x7c\40\x2e\x2f\163\141\x6d\x6c\137\141\163\x73\x65\162\x74\151\x6f\156\x3a\105\156\x63\162\x79\160\164\x65\144\111\x44\57\170\145\x6e\143\x3a\105\156\x63\162\x79\x70\x74\x65\x64\x44\141\x74\x61");
        if (empty($Au)) {
            goto xd;
        }
        if (count($Au) > 1) {
            goto YF;
        }
        goto DT;
        xd:
        throw new MissingNameIdException();
        goto DT;
        YF:
        throw new InvalidNumberOfNameIDsException();
        DT:
        $Au = $Au[0];
        if ($Au->localName === "\105\156\x63\x72\x79\160\164\145\144\104\141\164\x61") {
            goto Z0;
        }
        $this->nameId = SAML2Utilities::parseNameId($Au);
        goto R3;
        Z0:
        $this->encryptedNameId = $Au;
        R3:
    }
    protected function parseSessionIndexes($xa)
    {
        $this->sessionIndexes = array();
        $ja = SAML2Utilities::xpQuery($xa, "\56\x2f\163\141\155\154\x5f\x70\162\x6f\x74\157\x63\157\x6c\x3a\x53\145\163\163\x69\x6f\156\111\x6e\144\145\170");
        foreach ($ja as $lr) {
            $this->sessionIndexes[] = trim($lr->textContent);
            AG:
        }
        dZ:
    }
    public function build()
    {
        $S5 = $this->generateRequest();
        if (!(empty($this->bindingType) || $this->bindingType == SPConstants::HTTP_REDIRECT)) {
            goto FX;
        }
        $a1 = gzdeflate($S5);
        $CN = base64_encode($a1);
        $AB = urlencode($CN);
        $S5 = $AB;
        FX:
        return $S5;
    }
    private function generateRequest()
    {
        $QW = $this->createSAMLLogoutRequest();
        $this->xml->appendChild($QW);
        $wQ = $this->buildIssuer();
        $QW->appendChild($wQ);
        $Au = $this->buildNameId();
        $QW->appendChild($Au);
        $this->spUtility->log_debug("\x49\156\x20\x4c\157\147\x6f\165\164\x52\x65\x71\165\145\163\x74\x3a\x20\142\x65\x66\157\162\145\x20\102\165\x69\x6c\x64\x20\123\145\x73\163\x69\x6f\x6e\x49\156\x64\145\170\72\40");
        $lr = $this->buildSessionIndex();
        $QW->appendChild($lr);
        $this->spUtility->log_debug("\111\156\40\114\x6f\x67\157\165\164\x52\145\161\165\x65\x73\x74\72\40\141\x66\164\145\x72\x20\x42\165\x69\154\x64\40\x53\145\163\163\151\157\156\111\x6e\144\145\170\72\x20");
        $W6 = $this->xml->saveXML();
        return $W6;
    }
    protected function createSAMLLogoutRequest()
    {
        $QW = $this->xml->createElementNS("\165\162\156\x3a\157\x61\x73\151\x73\x3a\x6e\x61\155\145\x73\72\164\143\x3a\x53\x41\x4d\x4c\x3a\62\x2e\60\72\x70\162\157\164\x6f\x63\157\x6c", "\x73\x61\x6d\154\160\x3a\x4c\157\x67\x6f\x75\164\x52\145\161\165\x65\x73\x74");
        $QW->setAttribute("\111\x44", $this->generateUniqueID(40));
        $QW->setAttribute("\x56\x65\x72\x73\x69\x6f\156", "\x32\x2e\60");
        $QW->setAttribute("\111\163\163\x75\145\x49\x6e\163\x74\141\x6e\164", str_replace("\53\60\60\x3a\60\60", "\132", gmdate("\143", time())));
        $QW->setAttribute("\x44\x65\x73\164\151\156\x61\164\151\157\156", $this->destination);
        return $QW;
    }
    protected function buildIssuer()
    {
        return $this->xml->createElementNS("\165\x72\x6e\72\157\x61\x73\151\x73\72\x6e\141\155\145\163\72\164\143\x3a\x53\101\115\x4c\x3a\62\x2e\60\72\x61\163\163\x65\x72\x74\151\157\x6e", "\x73\141\155\154\72\111\x73\x73\165\x65\162", $this->issuer);
    }
    protected function buildNameId()
    {
        return $this->xml->createElementNS("\165\162\x6e\72\157\x61\163\151\x73\72\156\141\155\145\x73\72\x74\143\x3a\x53\x41\x4d\114\x3a\62\x2e\60\72\x61\163\163\x65\x72\x74\151\x6f\x6e", "\163\x61\155\154\x3a\x4e\x61\x6d\145\x49\104", $this->nameId);
    }
    protected function buildSessionIndex()
    {
        $ja = $this->spUtility->getSessionData("\163\x65\x73\163\x69\157\156\151\x6e");
        $this->spUtility->unsetSessionData("\x73\x65\163\x73\151\157\x6e\x69\156");
        return $this->xml->createElement("\x73\141\x6d\154\x70\72\123\145\x73\163\x69\x6f\x6e\111\156\x64\145\170", $ja);
    }
    public function toUnsignedXML()
    {
        $OC = toUnsignedXML();
        if (!($this->notOnOrAfter !== NULL)) {
            goto m4;
        }
        $OC->setAttribute("\x4e\157\x74\x4f\x6e\117\x72\x41\146\164\145\x72", gmdate("\131\x2d\155\x2d\x64\x5c\124\110\x3a\x69\72\163\134\132", $this->notOnOrAfter));
        m4:
        if ($this->encryptedNameId === NULL) {
            goto d9;
        }
        $Q5 = $OC->ownerDocument->createElementNS(SAML2_Const::NS_SAML, "\163\141\155\154\x3a" . "\x45\x6e\x63\x72\171\x70\x74\145\144\x49\104");
        $OC->appendChild($Q5);
        $Q5->appendChild($OC->ownerDocument->importNode($this->encryptedNameId, TRUE));
        goto Lt;
        d9:
        SAML2_Utils::addNameId($OC, $this->nameId);
        Lt:
        foreach ($this->sessionIndexes as $lr) {
            SAML2_Utils::addString($OC, SAML2_Const::NS_SAMLP, "\x53\x65\x73\163\151\157\156\x49\156\x64\x65\170", $lr);
            MO:
        }
        Qv:
        return $OC;
    }
    public function __toString()
    {
        $SZ = "\114\x4f\107\117\125\x54\40\122\105\121\125\x45\123\124\x20\120\101\x52\101\115\123\40\x5b";
        $SZ .= "\124\141\x67\x4e\141\155\x65\40\x3d\x20" . $this->tagName;
        $SZ .= "\54\x20\x76\141\154\x69\x64\141\x74\157\x72\163\x20\75\40\x20" . implode("\54", $this->validators);
        $SZ .= "\54\x20\x49\x44\40\x3d\40" . $this->id;
        $SZ .= "\x2c\x20\x49\x73\163\165\145\x72\40\x3d\x20" . $this->issuer;
        $SZ .= "\54\40\116\x6f\164\x20\x4f\156\40\117\x72\x20\x41\146\x74\x65\162\x20\x3d\40" . $this->notOnOrAfter;
        $SZ .= "\x2c\x20\104\145\163\164\151\x6e\x61\x74\x69\x6f\x6e\40\75\x20" . $this->destination;
        $SZ .= "\54\40\x45\x6e\x63\162\x79\x70\x74\145\x64\x20\x4e\x61\x6d\x65\111\104\40\75\x20" . $this->encryptedNameId;
        $SZ .= "\x2c\x20\111\x73\163\x75\145\x20\x49\156\x73\x74\141\156\x74\x20\x3d\x20" . $this->issueInstant;
        $SZ .= "\54\40\x53\x65\163\x73\151\x6f\156\40\x49\156\144\x65\x78\x65\163\x20\75\x20" . implode("\x2c", $this->sessionIndexes);
        $SZ .= "\x5d";
        return $SZ;
    }
    public function getXml()
    {
        return $this->xml;
    }
    public function setXml($xa)
    {
        $this->xml = $xa;
        return $this;
    }
    public function getTagName()
    {
        return $this->tagName;
    }
    public function setTagName($rJ)
    {
        $this->tagName = $rJ;
        return $this;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($Gh)
    {
        $this->id = $Gh;
        return $this;
    }
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($wQ)
    {
        $this->issuer = $wQ;
        return $this;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function setDestination($Uc)
    {
        $this->destination = $Uc;
        return $this;
    }
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }
    public function setIssueInstant($Tf)
    {
        $this->issueInstant = $Tf;
        return $this;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function setCertificates($T4)
    {
        $this->certificates = $T4;
        return $this;
    }
    public function getValidators()
    {
        return $this->validators;
    }
    public function setValidators($UX)
    {
        $this->validators = $UX;
        return $this;
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($Pc)
    {
        $this->notOnOrAfter = $Pc;
        return $this;
    }
    public function getEncryptedNameId()
    {
        return $this->encryptedNameId;
    }
    public function setEncryptedNameId($Ad)
    {
        $this->encryptedNameId = $Ad;
        return $this;
    }
    public function getNameId()
    {
        return $this->nameId;
    }
    public function setNameId($Au)
    {
        $this->nameId = $Au;
        return $this;
    }
    public function getSessionIndexes()
    {
        return $this->sessionIndexes;
    }
    public function setSessionIndexes($ja)
    {
        $this->sessionIndexes = $ja;
        return $this;
    }
    public function getRequestType()
    {
        return $this->requestType;
    }
    public function setRequestType($tp)
    {
        $this->requestType = $tp;
        return $this;
    }
    public function getBindingType()
    {
        return $this->bindingType;
    }
    public function setBindingType($H2)
    {
        $this->bindingType = $H2;
        return $this;
    }
}
