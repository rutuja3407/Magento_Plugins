<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Authorization\Model\ResourceModel\Role\Collection;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Math\Random;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Helper\Exception\NotRegisteredException;
use MiniOrange\SP\Helper\Exception\RequiredFieldsException;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
abstract class BaseAction extends \Magento\Framework\App\Action\Action
{
    protected $spUtility;
    protected $context;
    protected $resultFactory;
    protected $storeManager;
    protected $responseFactory;
    public function __construct(Context $gt, SPUtility $fR, StoreManagerInterface $VO, ResultFactory $ps, ResponseFactory $Jv)
    {
        $this->spUtility = $fR;
        $this->resultFactory = $ps;
        $this->storeManager = $VO;
        $this->responseFactory = $Jv;
        parent::__construct($gt);
    }
    public abstract function execute();
    protected function checkIfRequiredFieldsEmpty($AI)
    {
        foreach ($AI as $On => $VP) {
            if (!(is_array($VP) && (empty($VP[$On]) || $this->spUtility->isBlank($VP[$On])) || $this->spUtility->isBlank($VP))) {
                goto V0;
            }
            throw new RequiredFieldsException();
            V0:
            Wx:
        }
        P1:
    }
    protected function sendHTTPRedirectResponse($yO, $eA, $s9)
    {
        $pW = $s9;
        $pW .= strpos($s9, "\x3f") !== false ? "\46" : "\x3f";
        $pW .= "\x53\x41\x4d\x4c\x52\145\x73\x70\157\156\x73\145\75" . $yO . "\x26\122\145\154\141\x79\x53\164\141\x74\145\75" . urlencode($eA);
        return $this->resultRedirectFactory->create()->setUrl($pW);
    }
    protected function checkIfValidPlugin()
    {
        if (!(!$this->spUtility->micr() || !$this->spUtility->mclv())) {
            goto Jg;
        }
        throw new NotRegisteredException();
        Jg:
    }
    protected function sendHTTPRedirectRequest($ML, $eA, $ft)
    {
        $this->spUtility->log_debug("\102\141\x73\x65\101\143\164\151\x6f\156\72\x20\x73\145\156\x64\110\124\124\120\122\x65\144\x69\162\x65\x63\164\122\145\161\x75\x65\x73\164");
        $ML = "\x53\101\x4d\114\x52\145\161\x75\x65\163\x74\x3d" . $ML . "\x26\122\x65\154\141\x79\x53\x74\x61\x74\145\x3d" . urlencode($eA) . "\46\x53\151\147\101\154\147\75" . urlencode(XMLSecurityKey::RSA_SHA256);
        $Cv = array("\164\171\160\145" => "\160\162\151\166\x61\164\145");
        $On = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $Cv);
        $up = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));
        $On->loadKey($up);
        $wR = $On->signData($ML);
        $wR = base64_encode($wR);
        $pW = $ft;
        $this->spUtility->log_debug("\x42\x61\x73\x65\x41\x63\164\x69\157\156\x3a\40\151\144\160\x55\x72\x6c\x3a" . $ft);
        $pW .= strpos($ft, "\x3f") !== false ? "\46" : "\x3f";
        $pW .= $ML . "\x26\123\151\147\156\141\x74\165\162\145\75" . urlencode($wR);
        $this->spUtility->log_debug("\x42\x61\x73\x65\x41\143\x74\151\x6f\x6e\x3a\40\163\x65\156\x64\110\x54\124\120\x52\145\144\x69\x72\x65\x63\x74\122\x65\x71\165\145\163\x74\72\x20\x72\x65\144\151\162\145\143\164\75" . $pW);
        header("\x4c\x6f\143\x61\x74\151\157\156\72\40\x20" . $pW);
        exit;
    }
    protected function sendHTTPRedirectAuthRequest($ML, $eA, $ft, $Te)
    {
        $this->spUtility->log_debug("\x42\141\163\x65\x41\x63\164\x69\x6f\x6e\72\x20\163\145\156\x64\110\124\124\x50\x52\145\144\x69\x72\x65\x63\164\x41\x75\164\x68\122\145\161\165\145\163\164");
        $ML = "\123\x41\x4d\x4c\122\145\161\x75\x65\x73\x74\75" . $ML . "\46\x52\145\x6c\141\171\123\164\x61\x74\x65\x3d" . urlencode($eA) . "\x26\x53\151\147\101\154\x67\x3d" . urlencode(XMLSecurityKey::RSA_SHA256);
        foreach ($Te as $On => $VP) {
            if ($On == "\x72\145\x6c\141\x79\x53\x74\141\x74\x65") {
                goto jN;
            }
            $ML = $ML . "\x26" . "{$On}" . "\x3d" . urlencode($VP);
            jN:
            oQ:
        }
        Ji:
        $Cv = array("\x74\171\x70\x65" => "\x70\x72\x69\166\141\164\x65");
        $On = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $Cv);
        $up = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));
        $On->loadKey($up);
        $wR = $On->signData($ML);
        $wR = base64_encode($wR);
        $pW = $ft;
        $this->spUtility->log_debug("\102\x61\x73\145\101\x63\164\151\x6f\156\x3a\x20\x69\x64\x70\125\162\154\x3a" . $ft);
        $pW .= strpos($ft, "\x3f") !== false ? "\46" : "\77";
        $pW .= $ML . "\46\123\x69\147\156\x61\164\165\x72\x65\75" . urlencode($wR);
        $this->spUtility->log_debug("\102\x61\x73\145\101\x63\x74\151\x6f\x6e\x3a\40\x73\145\x6e\144\x48\124\x54\x50\122\x65\x64\151\162\x65\x63\164\101\x75\x74\150\x52\x65\161\165\x65\x73\164\72\x20\162\145\x64\x69\x72\x65\x63\164\75" . $pW);
        header("\114\157\x63\141\164\x69\x6f\156\x3a\40\x20" . $pW);
        exit;
    }
    protected function sendHTTPPostRequest($ML, $eA, $Md)
    {
        $St = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $this->spUtility->log_debug("\x42\141\x73\x65\x41\x63\x74\x69\x6f\x6e\x3a\40\163\x65\x6e\x64\110\124\124\120\120\x6f\x73\x74\122\x65\161\165\x65\163\164\x3a\x20\x73\x74\141\x72\x74");
        $xn = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $i5 = SAML2Utilities::signXML($ML, $this->spUtility->getFileContents($xn), $this->spUtility->getFileContents($St), "\116\141\155\145\x49\x44\120\x6f\154\151\x63\171");
        $TY = base64_encode($i5);
        ob_clean();
        print_r("\40\40\x3c\150\164\155\154\x3e\x3c\150\145\141\144\x3e\x3c\163\143\x72\151\160\164\x20\x73\x72\x63\75\47\150\x74\x74\x70\x73\72\x2f\x2f\143\157\144\145\56\152\x71\x75\x65\x72\x79\56\143\x6f\x6d\x2f\x6a\161\165\x65\x72\x79\x2d\x31\56\x31\x31\56\x33\56\x6d\x69\x6e\x2e\152\x73\47\x3e\74\x2f\163\x63\162\151\160\164\76\74\163\x63\x72\151\160\x74\40\164\x79\160\145\x3d\42\x74\145\x78\x74\x2f\x6a\x61\x76\x61\163\x63\x72\x69\x70\164\42\76\xd\12\40\x20\x20\x20\x20\x20\40\x20\40\x20\x20\40\40\x20\x20\x20\x20\x20\40\x20\44\x28\146\165\x6e\143\x74\x69\x6f\156\x28\x29\x7b\144\x6f\143\x75\155\x65\x6e\x74\56\146\157\x72\155\x73\x5b\x27\x73\141\155\x6c\55\x72\145\x71\x75\x65\163\164\x2d\x66\x6f\162\155\47\x5d\56\x73\165\x62\x6d\x69\164\50\51\x3b\x7d\51\73\x3c\x2f\163\x63\162\151\x70\x74\76\74\x2f\150\145\x61\144\76\15\xa\x20\x20\x20\40\x20\x20\40\40\x20\40\40\40\x20\40\40\40\x20\x20\x20\40\x3c\x62\x6f\x64\x79\76\15\xa\x20\x20\x20\40\40\x20\x20\x20\x20\40\x20\40\x20\40\40\x20\x20\x20\x20\40\x20\40\40\x20\120\x6c\145\141\163\x65\x20\x77\x61\x69\x74\x2e\x2e\56\xd\xa\x20\40\40\x20\40\40\40\40\40\x20\x20\x20\40\x20\x20\x20\40\40\40\40\40\x20\40\x20\74\146\157\162\155\40\x61\143\x74\151\x6f\156\75\42" . $Md . "\42\x20\x6d\145\x74\150\157\144\x3d\x22\160\157\x73\164\42\40\x69\144\x3d\x22\x73\x61\x6d\x6c\55\x72\145\x71\165\145\x73\164\x2d\146\x6f\x72\x6d\x22\40\x73\164\x79\x6c\145\x3d\x22\144\151\163\x70\154\x61\x79\72\156\157\x6e\145\73\x22\76\15\12\40\40\40\x20\40\40\40\40\40\40\40\40\x20\40\x20\x20\40\x20\40\40\x20\x20\x20\x20\x20\x20\x20\x20\74\x69\156\x70\x75\x74\x20\x74\x79\x70\145\x3d\x22\x68\151\144\x64\x65\156\x22\40\x6e\x61\155\145\75\x22\123\x41\115\x4c\x52\x65\x71\x75\145\163\x74\42\x20\x76\141\x6c\x75\145\x3d\x22" . $TY . "\42\40\x2f\76\15\12\x20\40\x20\40\40\40\40\x20\x20\40\40\40\40\x20\x20\x20\x20\x20\x20\x20\40\40\x20\x20\40\x20\x20\x20\x3c\151\156\x70\165\164\x20\164\171\160\145\75\x22\150\151\x64\x64\145\156\42\x20\156\141\x6d\145\75\42\122\x65\x6c\x61\x79\x53\x74\141\164\145\x22\x20\x76\x61\x6c\165\145\75\42" . htmlentities($eA) . "\x22\x20\57\x3e\xd\xa\40\40\x20\40\40\x20\x20\x20\x20\40\40\40\40\40\40\x20\x20\40\40\40\x20\x20\x20\x20\74\x2f\x66\157\162\x6d\76\15\xa\x20\x20\x20\40\x20\x20\40\x20\x20\40\x20\x20\40\40\x20\40\x20\x20\40\40\x3c\x2f\x62\x6f\x64\171\76\xd\xa\x20\x20\x20\40\x20\x20\x20\x20\40\x20\x20\40\x20\40\40\40\74\57\150\x74\x6d\x6c\x3e");
        return;
    }
    protected function sendHTTPPostAuthRequest($ML, $eA, $Md, $Te)
    {
        $St = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $this->spUtility->log_debug("\102\141\163\x65\x41\143\164\151\x6f\x6e\x3a\x20\x73\145\x6e\144\110\x54\124\120\x50\x6f\163\x74\101\165\164\150\122\x65\161\165\x65\x73\164\x3a\40\x73\x74\141\x72\164");
        $xn = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $i5 = SAML2Utilities::signXML($ML, $this->spUtility->getFileContents($xn), $this->spUtility->getFileContents($St), "\x4e\x61\155\x65\111\x44\x50\x6f\154\151\x63\171");
        $TY = base64_encode($i5);
        ob_clean();
        print_r("\x20\40\74\x68\164\155\154\76\x3c\x68\145\x61\x64\76\x3c\x73\143\162\x69\x70\164\40\163\x72\143\x3d\47\x68\164\x74\x70\x73\72\x2f\57\143\x6f\144\145\56\x6a\x71\165\145\x72\171\x2e\x63\157\155\x2f\152\x71\x75\145\162\171\55\x31\56\x31\x31\56\63\x2e\155\151\156\x2e\x6a\x73\x27\76\x3c\57\x73\x63\x72\x69\160\x74\76\74\163\143\x72\151\160\x74\40\x74\x79\x70\145\x3d\42\164\x65\x78\164\57\x6a\141\x76\x61\163\143\x72\151\160\164\42\x3e\15\12\40\x20\x20\40\x20\x20\40\40\40\x20\40\40\40\40\40\40\40\x20\40\40\44\50\146\x75\156\x63\x74\x69\x6f\x6e\50\x29\173\x64\157\143\x75\x6d\145\x6e\x74\x2e\146\157\162\x6d\163\x5b\x27\163\141\155\154\x2d\162\x65\161\165\145\163\164\55\x66\157\x72\x6d\47\x5d\56\163\165\142\x6d\x69\164\x28\x29\x3b\175\x29\x3b\74\57\x73\x63\x72\151\160\x74\x3e\74\x2f\150\145\x61\144\76\xd\12\40\x20\40\x20\x20\40\40\x20\x20\x20\x20\40\40\40\x20\x20\40\40\40\x20\74\142\x6f\x64\x79\x3e\15\12\x20\x20\x20\x20\x20\40\x20\40\40\40\40\x20\x20\40\x20\40\x20\40\x20\x20\40\x20\x20\40\120\x6c\145\141\x73\x65\x20\x77\141\151\x74\x2e\x2e\56\xd\xa\40\x20\40\40\x20\40\40\40\x20\x20\40\x20\x20\40\40\40\40\40\40\40\40\x20\40\40\x3c\x66\x6f\162\x6d\x20\141\x63\x74\151\x6f\x6e\x3d\42" . $Md . "\42\x20\x6d\145\164\x68\x6f\x64\75\x22\160\x6f\x73\164\x22\40\151\144\x3d\x22\x73\141\155\x6c\55\162\x65\161\165\145\163\164\55\146\157\162\x6d\42\40\163\164\171\154\x65\75\x22\144\x69\163\160\x6c\x61\x79\72\x6e\157\x6e\x65\x3b\x22\76\xd\12\40\x20\40\x20\40\40\x20\40\40\x20\x20\x20\x20\x20\40\40\40\40\x20\x20\40\x20\40\40\x20\40\x20\40\x3c\x69\x6e\160\165\x74\40\164\x79\x70\x65\x3d\x22\x68\x69\144\x64\x65\x6e\x22\40\156\x61\x6d\x65\75\42\123\x41\x4d\114\x52\x65\x71\165\x65\163\x74\x22\x20\166\x61\x6c\x75\145\x3d\42" . $TY . "\42\x20\57\76\xd\xa\40\x20\40\40\40\40\x20\40\x20\40\x20\40\40\x20\40\40\40\40\40\x20\40\x20\40\40\x20\x20\x20\x20\74\x69\x6e\x70\x75\164\40\164\171\x70\x65\75\x22\x68\151\x64\144\145\156\42\40\x6e\x61\x6d\145\x3d\x22\122\x65\154\x61\x79\123\x74\x61\164\145\x22\x20\166\x61\x6c\165\145\x3d\x22" . htmlentities($eA) . "\42\x20\57\76\15\xa\x20\40\x20\40\40\x20\40\40\x20\40\x20\40\x20\x20\x20\x20\x20\40\40\x20\x20\x20\40\40\74\57\146\x6f\162\x6d\76\xd\xa\40\40\x20\40\40\x20\x20\40\x20\x20\x20\40\40\40\x20\40\x20\40\x20\40\74\57\142\x6f\x64\171\76\xd\xa\40\x20\40\x20\40\x20\40\x20\x20\x20\40\x20\40\40\40\40\x3c\x2f\x68\x74\155\x6c\76");
        return;
    }
    protected function sendHTTPPostResponse($yO, $eA, $s9)
    {
        $this->spUtility->log_debug("\x42\141\163\x65\x41\x63\164\x69\x6f\x6e\x3a\x20\163\x65\x6e\x64\110\124\124\120\120\x6f\x73\x74\x52\145\x73\x70\157\x6e\x73\145\72\x20\x73\x74\x61\x72\x74");
        $St = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $xn = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $i5 = SAML2Utilities::signXML($yO, $this->spUtility->getFileContents($xn), $this->spUtility->getFileContents($St), "\x53\x74\141\x74\x75\x73");
        $TY = base64_encode($i5);
        ob_clean();
        print_r("\x20\40\x3c\x68\164\155\x6c\76\74\x68\x65\x61\144\x3e\74\163\143\x72\x69\160\x74\x20\x73\x72\x63\75\47\150\x74\164\160\163\x3a\x2f\x2f\x63\157\x64\x65\56\152\161\x75\145\x72\171\56\143\x6f\x6d\57\152\x71\x75\145\x72\171\x2d\x31\56\x31\x31\x2e\x33\56\x6d\x69\156\56\x6a\163\x27\76\74\57\x73\x63\162\x69\160\x74\x3e\x3c\163\143\x72\151\160\164\40\164\x79\x70\145\75\42\164\145\170\x74\57\x6a\141\x76\141\x73\143\x72\x69\160\164\x22\x3e\xd\xa\x20\x20\40\x20\40\40\x20\x20\40\x20\40\40\40\40\40\40\40\x20\40\x20\x24\x28\x66\x75\156\x63\164\x69\157\x6e\x28\x29\x7b\x64\157\x63\x75\155\x65\x6e\x74\x2e\x66\157\x72\x6d\x73\133\47\163\x61\x6d\x6c\55\162\x65\x71\165\145\x73\164\55\146\x6f\x72\155\x27\135\56\163\x75\x62\155\151\164\x28\x29\73\175\51\73\x3c\x2f\x73\x63\162\151\x70\164\x3e\x3c\57\x68\145\141\x64\76\15\xa\x20\40\40\x20\40\40\40\x20\x20\x20\x20\40\x20\x20\x20\x20\x20\x20\40\40\x3c\x62\157\144\171\76\15\12\x20\x20\40\x20\40\40\40\x20\x20\x20\40\x20\x20\40\x20\x20\40\x20\40\x20\x20\40\x20\x20\x50\154\145\x61\x73\145\40\167\x61\x69\x74\56\x2e\56\15\12\40\40\40\x20\x20\40\40\x20\x20\40\40\x20\x20\40\40\x20\x20\x20\x20\40\40\x20\x20\x20\x3c\146\x6f\x72\155\40\x61\143\x74\151\x6f\156\75\42" . $s9 . "\42\40\x6d\145\x74\x68\x6f\144\x3d\42\x70\x6f\x73\x74\x22\40\151\144\x3d\42\163\141\x6d\154\55\162\145\161\165\x65\163\x74\x2d\x66\x6f\x72\x6d\42\40\163\x74\x79\x6c\x65\x3d\x22\x64\151\x73\160\x6c\141\171\x3a\156\x6f\156\145\x3b\x22\x3e\15\xa\x20\x20\x20\40\x20\40\40\40\x20\x20\x20\x20\40\40\40\x20\x20\x20\40\40\x20\40\x20\x20\40\x20\40\x20\x3c\151\x6e\x70\165\x74\x20\x74\x79\160\x65\x3d\x22\150\x69\144\144\145\x6e\42\40\156\141\x6d\x65\75\42\x53\x41\x4d\114\122\x65\163\160\157\156\x73\145\x22\40\166\x61\154\x75\145\x3d\x22" . $TY . "\42\x20\57\x3e\xd\xa\40\x20\40\40\40\40\40\x20\x20\x20\40\x20\x20\40\x20\x20\40\40\40\x20\40\40\40\x20\40\40\x20\40\74\x69\156\160\x75\x74\x20\x74\171\160\x65\x3d\42\x68\151\x64\x64\145\x6e\x22\40\156\141\x6d\x65\75\x22\122\x65\154\141\171\123\x74\141\164\145\42\40\x76\141\154\165\x65\x3d\x22" . htmlentities($eA) . "\x22\x20\57\x3e\15\xa\x20\40\40\x20\40\x20\40\40\x20\40\40\x20\40\40\x20\40\40\40\x20\x20\40\40\x20\x20\x3c\57\x66\x6f\x72\155\x3e\15\12\x20\40\40\x20\40\40\x20\40\x20\40\40\x20\x20\40\40\x20\x20\x20\40\40\x3c\57\x62\x6f\x64\x79\76\xd\xa\x20\x20\x20\40\40\x20\x20\40\40\40\40\x20\x20\x20\40\x20\74\57\x68\164\x6d\154\76");
        return;
    }
}
