<?php


namespace MiniOrange\SP\Helper\Saml2;

use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\Exception\MissingIDException;
use MiniOrange\SP\Helper\Exception\InvalidRequestVersionException;
use MiniOrange\SP\Helper\Exception\MissingNameIdException;
use MiniOrange\SP\Helper\Exception\InvalidNumberOfNameIDsException;
class LogoutRequest
{
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
    protected $spUtility;
    private $requestType = SPConstants::LOGOUT_REQUEST;
    public function __construct(\MiniOrange\SP\Helper\SPUtility $Kx, \DOMElement $BY = NULL)
    {
        $this->spUtility = $Kx;
        $this->xml = new \DOMDocument("\x31\56\60", "\x75\x74\x66\55\70");
        if (!($BY === NULL)) {
            goto n9s;
        }
        return;
        n9s:
        $this->xml = $BY;
        $this->tagName = "\114\157\x67\x6f\x75\x74\122\x65\161\165\145\x73\164";
        $this->id = $this->generateUniqueID(40);
        $this->issueInstant = time();
        $this->certificates = array();
        $this->validators = array();
        $this->issueInstant = SAML2Utilities::xsDateTimeToTimestamp($BY->getAttribute("\x49\x73\x73\165\145\x49\156\x73\164\x61\156\164"));
        $this->parseID($BY);
        $this->checkSAMLVersion($BY);
        if (!$BY->hasAttribute("\x44\145\x73\164\x69\156\141\x74\151\157\x6e")) {
            goto a_m;
        }
        $this->destination = $BY->getAttribute("\104\145\163\164\151\x6e\141\164\x69\157\x6e");
        a_m:
        $this->parseIssuer($BY);
        $this->parseAndValidateSignature($BY);
        if (!$BY->hasAttribute("\x4e\x6f\x74\x4f\x6e\117\162\101\146\x74\145\x72")) {
            goto Vr_;
        }
        $this->notOnOrAfter = SAML2Utilities::xsDateTimeToTimestamp($BY->getAttribute("\116\157\x74\x4f\156\117\x72\101\x66\x74\x65\162"));
        Vr_:
        $this->parseNameId($BY);
        $this->parseSessionIndexes($BY);
    }
    public function build()
    {
        $Yb = $this->generateRequest();
        if (!(empty($this->bindingType) || $this->bindingType == SPConstants::HTTP_REDIRECT)) {
            goto o8Z;
        }
        $zy = gzdeflate($Yb);
        $k1 = base64_encode($zy);
        $Og = urlencode($k1);
        $Yb = $Og;
        o8Z:
        return $Yb;
    }
    private function generateRequest()
    {
        $nw = $this->createSAMLLogoutRequest();
        $this->xml->appendChild($nw);
        $wz = $this->buildIssuer();
        $nw->appendChild($wz);
        $Q5 = $this->buildNameId();
        $nw->appendChild($Q5);
        $this->spUtility->log_debug("\x49\156\x20\x4c\157\x67\157\165\x74\x52\x65\161\x75\145\x73\164\72\40\142\x65\x66\157\162\x65\40\x42\x75\x69\x6c\x64\x20\123\x65\163\163\x69\157\x6e\111\x6e\x64\145\170\x3a\40");
        $SK = $this->buildSessionIndex();
        $nw->appendChild($SK);
        $this->spUtility->log_debug("\111\x6e\x20\114\157\147\x6f\165\164\122\145\x71\165\145\x73\x74\72\x20\x61\146\x74\x65\162\x20\102\165\151\154\144\40\123\145\163\163\151\157\156\x49\156\144\145\170\x3a\x20");
        $Am = $this->xml->saveXML();
        return $Am;
    }
    protected function createSAMLLogoutRequest()
    {
        $nw = $this->xml->createElementNS("\x75\162\156\x3a\157\141\x73\x69\x73\x3a\156\141\x6d\x65\163\72\x74\x63\72\123\x41\x4d\114\x3a\x32\x2e\60\72\x70\x72\x6f\x74\x6f\143\x6f\x6c", "\x73\x61\x6d\154\x70\72\x4c\x6f\x67\x6f\x75\x74\122\x65\161\x75\x65\x73\164");
        $nw->setAttribute("\x49\104", $this->generateUniqueID(40));
        $nw->setAttribute("\126\x65\x72\163\151\157\156", "\x32\x2e\x30");
        $nw->setAttribute("\111\163\163\165\x65\111\156\163\x74\x61\x6e\164", str_replace("\x2b\x30\x30\x3a\x30\60", "\132", gmdate("\143", time())));
        $nw->setAttribute("\104\x65\163\x74\x69\156\x61\164\x69\157\x6e", $this->destination);
        return $nw;
    }
    protected function buildIssuer()
    {
        return $this->xml->createElementNS("\x75\x72\156\72\157\x61\x73\x69\x73\x3a\x6e\x61\x6d\x65\x73\x3a\x74\143\x3a\x53\101\115\114\x3a\62\56\60\72\141\163\x73\145\x72\x74\151\x6f\156", "\163\x61\155\154\x3a\111\x73\x73\x75\x65\162", $this->issuer);
    }
    protected function buildNameId()
    {
        return $this->xml->createElementNS("\x75\162\x6e\72\x6f\141\163\x69\x73\72\x6e\141\x6d\x65\x73\72\164\x63\x3a\123\101\115\x4c\x3a\x32\56\60\x3a\141\x73\163\x65\x72\164\x69\157\156", "\163\141\155\x6c\x3a\116\141\155\x65\x49\104", $this->nameId);
    }
    protected function buildSessionIndex()
    {
        $sY = $this->spUtility->getSessionData("\163\145\x73\163\151\157\x6e\151\156");
        $this->spUtility->unsetSessionData("\x73\x65\163\x73\151\x6f\156\x69\156");
        return $this->xml->createElement("\163\141\x6d\x6c\x70\x3a\123\x65\x73\163\x69\157\156\x49\156\x64\145\170", $sY);
    }
    protected function parseID($BY)
    {
        if ($BY->hasAttribute("\x49\104")) {
            goto b2w;
        }
        throw new MissingIDException();
        b2w:
        $this->id = $BY->getAttribute("\x49\x44");
    }
    protected function checkSAMLVersion($BY)
    {
        if (!($BY->getAttribute("\x56\x65\162\x73\151\x6f\156") !== "\62\x2e\x30")) {
            goto lLM;
        }
        throw InvalidRequestVersionException();
        lLM:
    }
    protected function parseIssuer($BY)
    {
        $wz = SAML2Utilities::xpQuery($BY, "\56\x2f\163\x61\155\x6c\x5f\x61\163\x73\x65\162\164\x69\x6f\156\x3a\111\x73\163\x75\145\x72");
        if (empty($wz)) {
            goto nm6;
        }
        $this->issuer = trim($wz[0]->textContent);
        nm6:
    }
    protected function parseSessionIndexes($BY)
    {
        $this->sessionIndexes = array();
        $sY = SAML2Utilities::xpQuery($BY, "\x2e\57\163\x61\155\154\x5f\x70\x72\157\164\157\143\157\x6c\72\123\x65\163\x73\x69\x6f\156\x49\156\144\145\x78");
        foreach ($sY as $SK) {
            $this->sessionIndexes[] = trim($SK->textContent);
            oxK:
        }
        YE3:
    }
    protected function parseAndValidateSignature($BY)
    {
        $GG = SAML2Utilities::validateElement($BY);
        if (!($GG !== FALSE)) {
            goto tDT;
        }
        $this->certificates = $GG["\103\145\x72\164\x69\x66\151\x63\141\164\145\163"];
        $this->validators[] = array("\x46\165\x6e\x63\x74\x69\x6f\x6e" => array("\x53\101\x4d\114\x55\x74\151\154\x69\164\x69\145\x73", "\166\141\x6c\x69\144\x61\x74\x65\123\x69\x67\x6e\x61\x74\x75\162\x65"), "\104\x61\x74\141" => $GG);
        tDT:
    }
    protected function parseNameId($BY)
    {
        $Q5 = SAML2Utilities::xpQuery($BY, "\56\x2f\163\x61\x6d\x6c\137\141\163\163\x65\162\164\151\157\x6e\72\116\141\155\x65\111\x44\x20\x7c\x20\x2e\x2f\x73\141\x6d\154\x5f\x61\x73\163\x65\162\164\151\x6f\x6e\x3a\105\156\143\162\x79\160\x74\x65\x64\x49\x44\57\170\145\x6e\143\x3a\105\156\143\x72\171\160\164\x65\x64\x44\x61\164\141");
        if (empty($Q5)) {
            goto M_b;
        }
        if (count($Q5) > 1) {
            goto W7w;
        }
        goto dQw;
        M_b:
        throw new MissingNameIdException();
        goto dQw;
        W7w:
        throw new InvalidNumberOfNameIDsException();
        dQw:
        $Q5 = $Q5[0];
        if ($Q5->localName === "\x45\156\143\x72\x79\160\x74\145\x64\x44\141\164\141") {
            goto dm5;
        }
        $this->nameId = SAML2Utilities::parseNameId($Q5);
        goto y5A;
        dm5:
        $this->encryptedNameId = $Q5;
        y5A:
    }
    public function toUnsignedXML()
    {
        $N7 = toUnsignedXML();
        if (!($this->notOnOrAfter !== NULL)) {
            goto YQZ;
        }
        $N7->setAttribute("\x4e\157\x74\x4f\156\117\x72\x41\146\x74\145\x72", gmdate("\x59\x2d\155\55\x64\x5c\124\110\x3a\x69\72\163\x5c\x5a", $this->notOnOrAfter));
        YQZ:
        if ($this->encryptedNameId === NULL) {
            goto FmO;
        }
        $Eo = $N7->ownerDocument->createElementNS(SAML2_Const::NS_SAML, "\163\141\x6d\x6c\72" . "\105\156\143\162\x79\x70\x74\x65\144\111\x44");
        $N7->appendChild($Eo);
        $Eo->appendChild($N7->ownerDocument->importNode($this->encryptedNameId, TRUE));
        goto iYL;
        FmO:
        SAML2_Utils::addNameId($N7, $this->nameId);
        iYL:
        foreach ($this->sessionIndexes as $SK) {
            SAML2_Utils::addString($N7, SAML2_Const::NS_SAMLP, "\123\x65\163\163\151\x6f\156\111\156\x64\145\x78", $SK);
            xYI:
        }
        Bsw:
        return $N7;
    }
    private function generateUniqueID($vS)
    {
        return SAML2Utilities::generateRandomAlphanumericValue($vS);
    }
    public function __toString()
    {
        $He = "\114\x4f\x47\117\x55\x54\x20\122\105\x51\x55\105\x53\124\x20\x50\x41\122\x41\115\x53\x20\x5b";
        $He .= "\x54\141\147\116\141\x6d\x65\x20\75\x20" . $this->tagName;
        $He .= "\54\40\x76\x61\154\151\144\x61\164\x6f\162\163\x20\x3d\x20\x20" . implode("\54", $this->validators);
        $He .= "\x2c\40\111\x44\40\75\x20" . $this->id;
        $He .= "\x2c\x20\111\163\x73\165\x65\x72\x20\75\x20" . $this->issuer;
        $He .= "\54\x20\x4e\157\x74\x20\117\156\40\117\162\x20\101\x66\x74\145\x72\40\x3d\40" . $this->notOnOrAfter;
        $He .= "\54\40\x44\145\x73\164\151\x6e\x61\x74\x69\x6f\156\40\75\x20" . $this->destination;
        $He .= "\54\40\105\156\x63\162\x79\160\164\145\x64\40\x4e\141\x6d\x65\111\104\40\75\x20" . $this->encryptedNameId;
        $He .= "\54\40\x49\x73\163\165\x65\x20\111\x6e\163\x74\141\156\x74\x20\75\x20" . $this->issueInstant;
        $He .= "\x2c\40\x53\x65\x73\x73\151\x6f\156\40\111\x6e\144\x65\170\145\x73\x20\75\40" . implode("\54", $this->sessionIndexes);
        $He .= "\x5d";
        return $He;
    }
    public function getXml()
    {
        return $this->xml;
    }
    public function setXml($BY)
    {
        $this->xml = $BY;
        return $this;
    }
    public function getTagName()
    {
        return $this->tagName;
    }
    public function setTagName($Kr)
    {
        $this->tagName = $Kr;
        return $this;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($lA)
    {
        $this->id = $lA;
        return $this;
    }
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($wz)
    {
        $this->issuer = $wz;
        return $this;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function setDestination($XS)
    {
        $this->destination = $XS;
        return $this;
    }
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }
    public function setIssueInstant($KQ)
    {
        $this->issueInstant = $KQ;
        return $this;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function setCertificates($fp)
    {
        $this->certificates = $fp;
        return $this;
    }
    public function getValidators()
    {
        return $this->validators;
    }
    public function setValidators($lo)
    {
        $this->validators = $lo;
        return $this;
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($pz)
    {
        $this->notOnOrAfter = $pz;
        return $this;
    }
    public function getEncryptedNameId()
    {
        return $this->encryptedNameId;
    }
    public function setEncryptedNameId($Do)
    {
        $this->encryptedNameId = $Do;
        return $this;
    }
    public function getNameId()
    {
        return $this->nameId;
    }
    public function setNameId($Q5)
    {
        $this->nameId = $Q5;
        return $this;
    }
    public function getSessionIndexes()
    {
        return $this->sessionIndexes;
    }
    public function setSessionIndexes($sY)
    {
        $this->sessionIndexes = $sY;
        return $this;
    }
    public function getRequestType()
    {
        return $this->requestType;
    }
    public function setRequestType($gJ)
    {
        $this->requestType = $gJ;
        return $this;
    }
    public function getBindingType()
    {
        return $this->bindingType;
    }
    public function setBindingType($CR)
    {
        $this->bindingType = $CR;
        return $this;
    }
}
