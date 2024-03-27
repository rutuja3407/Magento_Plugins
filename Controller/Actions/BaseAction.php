<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\Saml2\Lib\XMLSecurityKey;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\Exception\NotRegisteredException;
use MiniOrange\SP\Helper\Exception\RequiredFieldsException;
use Magento\Backend\App\Action\Context;
use Magento\Authorization\Model\ResourceModel\Role\Collection;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Math\Random;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Helper\Exception\MissingAttributesException;
use Magento\Customer\Model\AddressFactory;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\Controller\ResultFactory;
abstract class BaseAction extends \Magento\Framework\App\Action\Action
{
    protected $spUtility;
    protected $context;
    protected $resultFactory;
    protected $storeManager;
    protected $responseFactory;
    public function __construct(Context $Gc, SPUtility $Kx, StoreManagerInterface $Wl, ResultFactory $UZ, ResponseFactory $XF)
    {
        $this->spUtility = $Kx;
        $this->resultFactory = $UZ;
        $this->storeManager = $Wl;
        $this->responseFactory = $XF;
        parent::__construct($Gc);
    }
    protected function checkIfRequiredFieldsEmpty($JX)
    {
        foreach ($JX as $zg => $Yk) {
            if (!(is_array($Yk) && (empty($Yk[$zg]) || $this->spUtility->isBlank($Yk[$zg])) || $this->spUtility->isBlank($Yk))) {
                goto la;
            }
            throw new RequiredFieldsException();
            la:
            SH:
        }
        Ts:
    }
    protected function sendHTTPRedirectResponse($CN, $p_, $Iy)
    {
        $an = $Iy;
        $an .= strpos($Iy, "\77") !== false ? "\x26" : "\77";
        $an .= "\x53\101\x4d\114\122\145\x73\x70\x6f\x6e\163\x65\x3d" . $CN . "\46\x52\x65\x6c\x61\171\123\164\x61\x74\x65\x3d" . urlencode($p_);
        return $this->resultRedirectFactory->create()->setUrl($an);
    }
    public abstract function execute();
    protected function checkIfValidPlugin()
    {
        if (!(!$this->spUtility->micr() || !$this->spUtility->mclv())) {
            goto Eh;
        }
        throw new NotRegisteredException();
        Eh:
    }
    protected function sendHTTPRedirectRequest($KJ, $p_, $RJ)
    {
        $this->spUtility->log_debug("\x42\x61\x73\x65\x41\x63\x74\x69\157\156\72\40\163\x65\x6e\x64\x48\124\124\x50\x52\x65\x64\x69\162\x65\x63\164\x52\x65\x71\x75\145\x73\164");
        $KJ = "\123\101\115\x4c\122\145\161\165\x65\163\x74\75" . $KJ . "\x26\122\x65\x6c\x61\x79\x53\164\x61\x74\145\x3d" . urlencode($p_) . "\x26\123\151\147\x41\x6c\x67\x3d" . urlencode(XMLSecurityKey::RSA_SHA256);
        $fT = array("\164\171\160\x65" => "\160\162\x69\x76\x61\164\145");
        $zg = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $fT);
        $ZW = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));
        $zg->loadKey($ZW);
        $Ne = $zg->signData($KJ);
        $Ne = base64_encode($Ne);
        $an = $RJ;
        $this->spUtility->log_debug("\102\141\x73\x65\101\143\x74\x69\157\156\72\40\151\x64\x70\x55\x72\154\x3a" . $RJ);
        $an .= strpos($RJ, "\77") !== false ? "\46" : "\x3f";
        $an .= $KJ . "\46\x53\x69\x67\156\x61\x74\x75\162\145\75" . urlencode($Ne);
        $this->spUtility->log_debug("\x42\x61\x73\145\x41\x63\x74\x69\157\156\72\40\x73\145\x6e\144\110\x54\x54\120\x52\145\x64\151\162\145\143\x74\122\145\x71\x75\145\x73\x74\72\x20\x72\x65\144\x69\162\x65\x63\x74\x3d" . $an);
        header("\114\x6f\143\141\164\x69\x6f\x6e\x3a\x20\x20" . $an);
        exit;
    }
    protected function sendHTTPRedirectAuthRequest($KJ, $p_, $RJ, $As)
    {
        $this->spUtility->log_debug("\x42\x61\163\x65\x41\143\164\151\x6f\x6e\72\x20\163\x65\x6e\x64\x48\124\124\x50\122\145\144\x69\x72\x65\143\x74\x41\x75\x74\x68\122\x65\161\x75\145\163\x74");
        $KJ = "\x53\101\x4d\x4c\122\x65\161\x75\145\163\x74\x3d" . $KJ . "\46\122\x65\154\x61\171\123\164\141\164\145\x3d" . urlencode($p_) . "\x26\123\x69\x67\x41\x6c\x67\x3d" . urlencode(XMLSecurityKey::RSA_SHA256);
        foreach ($As as $zg => $Yk) {
            if ($zg == "\162\x65\154\x61\x79\x53\164\141\x74\145") {
                goto I7;
            }
            $KJ = $KJ . "\x26" . "{$zg}" . "\75" . urlencode($Yk);
            I7:
            iO:
        }
        DC:
        $fT = array("\164\171\160\145" => "\160\x72\x69\166\141\x74\x65");
        $zg = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $fT);
        $ZW = $this->spUtility->getFileContents($this->spUtility->getResourcePath(SPConstants::SP_KEY));
        $zg->loadKey($ZW);
        $Ne = $zg->signData($KJ);
        $Ne = base64_encode($Ne);
        $an = $RJ;
        $this->spUtility->log_debug("\102\141\x73\145\x41\x63\164\x69\157\x6e\x3a\40\151\144\160\125\162\x6c\72" . $RJ);
        $an .= strpos($RJ, "\x3f") !== false ? "\x26" : "\77";
        $an .= $KJ . "\46\x53\151\x67\156\141\x74\x75\x72\x65\x3d" . urlencode($Ne);
        $this->spUtility->log_debug("\x42\141\x73\145\101\143\x74\x69\157\156\72\40\x73\x65\x6e\x64\110\x54\124\x50\x52\x65\144\x69\162\x65\143\164\x41\x75\164\150\x52\145\161\x75\x65\163\x74\x3a\40\162\x65\x64\x69\x72\x65\x63\x74\x3d" . $an);
        header("\x4c\157\x63\x61\x74\x69\157\x6e\72\x20\x20" . $an);
        exit;
    }
    protected function sendHTTPPostRequest($KJ, $p_, $w1)
    {
        $zk = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $this->spUtility->log_debug("\x42\x61\163\x65\x41\x63\164\x69\157\x6e\x3a\x20\163\x65\x6e\x64\110\124\x54\x50\120\157\163\164\x52\145\x71\165\145\163\x74\72\40\163\164\x61\162\x74");
        $je = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $x8 = SAML2Utilities::signXML($KJ, $this->spUtility->getFileContents($je), $this->spUtility->getFileContents($zk), "\116\x61\x6d\x65\111\104\120\x6f\x6c\151\x63\171");
        $Vg = base64_encode($x8);
        ob_clean();
        print_r("\x20\x20\74\150\164\x6d\x6c\76\74\x68\145\141\144\76\x3c\163\x63\162\x69\160\164\40\x73\x72\x63\x3d\x27\150\164\164\x70\163\x3a\x2f\x2f\x63\x6f\x64\x65\56\x6a\161\x75\x65\162\x79\56\x63\x6f\x6d\57\152\x71\165\145\162\x79\55\x31\x2e\x31\61\56\x33\56\155\151\156\x2e\152\163\x27\x3e\x3c\x2f\x73\143\x72\151\x70\x74\76\x3c\163\143\162\151\x70\164\40\x74\171\x70\x65\75\x22\x74\145\170\x74\x2f\x6a\141\x76\x61\x73\143\162\151\x70\164\42\x3e\xd\xa\40\40\x20\40\40\x20\40\x20\40\40\x20\40\x20\x20\x20\40\x20\40\40\x20\x24\x28\x66\x75\156\x63\x74\x69\x6f\156\50\51\x7b\x64\157\143\x75\155\x65\156\164\x2e\x66\157\x72\155\163\133\47\x73\x61\155\x6c\x2d\x72\x65\x71\x75\x65\x73\x74\x2d\146\157\x72\x6d\x27\x5d\56\163\165\142\x6d\x69\x74\50\51\x3b\175\x29\73\x3c\57\163\x63\x72\151\x70\x74\x3e\74\x2f\150\x65\141\x64\x3e\15\12\40\40\40\40\x20\40\x20\40\x20\40\40\40\x20\x20\40\40\x20\40\40\x20\x3c\142\157\x64\171\x3e\15\xa\40\40\40\x20\40\x20\40\40\40\40\x20\40\x20\x20\40\40\40\x20\40\x20\40\x20\40\40\x50\154\145\x61\163\145\x20\167\x61\x69\x74\56\56\x2e\15\xa\x20\40\x20\x20\40\x20\x20\x20\x20\40\x20\x20\40\40\40\40\x20\x20\40\40\x20\40\x20\40\74\x66\157\x72\x6d\40\x61\x63\164\x69\157\156\x3d\42" . $w1 . "\x22\x20\155\145\164\150\157\x64\75\x22\160\x6f\x73\x74\42\40\x69\144\75\x22\163\x61\155\x6c\55\162\x65\x71\x75\145\x73\164\55\x66\x6f\x72\x6d\x22\x20\163\164\x79\154\x65\75\42\144\151\163\x70\154\141\x79\72\x6e\x6f\156\x65\x3b\42\x3e\15\12\40\40\40\x20\x20\x20\40\40\x20\40\40\40\40\x20\x20\40\x20\x20\x20\40\x20\40\x20\40\40\x20\x20\40\x3c\151\156\x70\x75\164\40\164\171\x70\x65\75\42\150\x69\x64\x64\145\156\42\x20\156\x61\155\x65\75\x22\x53\101\x4d\x4c\122\145\161\165\x65\163\164\42\x20\166\x61\154\165\145\x3d\42" . $Vg . "\42\40\x2f\x3e\xd\12\40\40\40\40\x20\x20\x20\x20\x20\x20\x20\40\40\40\40\x20\x20\x20\40\40\x20\40\x20\x20\x20\40\x20\x20\74\x69\x6e\160\x75\164\x20\x74\x79\x70\145\x3d\42\150\x69\144\144\145\x6e\x22\40\x6e\141\x6d\145\75\x22\122\x65\x6c\141\x79\123\164\x61\164\145\x22\x20\x76\x61\x6c\x75\x65\75\x22" . htmlentities($p_) . "\x22\x20\57\x3e\15\xa\40\40\40\40\40\x20\40\40\x20\40\x20\x20\40\x20\x20\x20\40\x20\40\x20\x20\x20\x20\x20\74\57\146\x6f\x72\155\x3e\15\xa\40\40\x20\40\x20\x20\40\40\x20\40\x20\40\40\40\x20\x20\x20\x20\40\40\x3c\x2f\142\x6f\x64\171\x3e\15\xa\40\x20\40\40\x20\40\x20\x20\x20\40\x20\x20\x20\x20\x20\x20\74\x2f\x68\164\x6d\154\x3e");
        return;
    }
    protected function sendHTTPPostAuthRequest($KJ, $p_, $w1, $As)
    {
        $zk = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $this->spUtility->log_debug("\102\x61\x73\x65\x41\x63\x74\x69\x6f\156\72\x20\163\145\156\144\110\124\124\120\x50\x6f\x73\x74\101\165\164\150\x52\x65\x71\x75\145\x73\164\x3a\x20\x73\164\x61\x72\164");
        $je = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $x8 = SAML2Utilities::signXML($KJ, $this->spUtility->getFileContents($je), $this->spUtility->getFileContents($zk), "\x4e\141\155\x65\111\x44\x50\157\154\151\143\171");
        $Vg = base64_encode($x8);
        ob_clean();
        print_r("\x20\40\x3c\x68\164\x6d\x6c\x3e\x3c\x68\x65\x61\x64\x3e\74\x73\143\x72\151\160\164\40\163\x72\x63\75\47\x68\164\164\160\x73\72\57\57\143\157\144\145\56\x6a\x71\165\x65\162\x79\x2e\143\157\155\x2f\152\x71\165\x65\x72\x79\x2d\61\x2e\61\x31\56\x33\56\x6d\x69\156\x2e\152\x73\x27\x3e\x3c\57\163\143\x72\151\x70\164\76\74\x73\143\x72\151\x70\x74\40\164\171\160\145\75\42\164\145\170\164\57\x6a\x61\x76\141\163\143\x72\x69\160\164\42\76\15\12\40\40\x20\40\40\40\40\x20\x20\x20\40\x20\x20\40\40\x20\x20\40\x20\x20\44\50\146\x75\x6e\x63\x74\151\157\x6e\x28\51\x7b\144\157\143\165\x6d\x65\x6e\x74\56\x66\157\162\x6d\x73\x5b\47\163\141\x6d\x6c\x2d\162\145\161\x75\145\163\164\55\146\x6f\x72\155\x27\x5d\x2e\x73\x75\x62\155\151\164\x28\51\x3b\175\x29\73\x3c\x2f\163\143\162\x69\x70\x74\x3e\x3c\57\150\145\141\144\76\15\12\x20\x20\x20\x20\x20\40\x20\x20\x20\40\40\x20\x20\x20\x20\40\x20\x20\40\x20\74\x62\157\x64\x79\x3e\15\xa\40\x20\40\x20\40\40\40\x20\40\x20\x20\40\x20\x20\x20\x20\x20\40\x20\40\40\40\40\40\120\x6c\x65\141\x73\145\x20\167\141\x69\164\x2e\x2e\x2e\15\xa\x20\40\40\40\40\x20\x20\40\x20\40\x20\40\40\x20\40\40\40\40\40\40\x20\x20\40\x20\x3c\x66\x6f\x72\155\x20\141\143\x74\151\157\156\75\x22" . $w1 . "\42\40\x6d\145\x74\x68\157\144\x3d\42\160\x6f\x73\164\x22\x20\151\144\75\x22\163\x61\155\154\55\162\145\161\165\x65\163\164\x2d\146\x6f\162\155\42\40\x73\164\171\154\x65\75\x22\x64\151\163\160\154\x61\171\x3a\x6e\x6f\156\x65\x3b\x22\x3e\15\12\40\40\x20\40\x20\x20\40\x20\x20\40\x20\40\x20\40\x20\x20\40\x20\x20\40\x20\x20\x20\x20\40\x20\40\40\x3c\x69\x6e\x70\x75\x74\40\x74\171\x70\145\x3d\x22\x68\x69\x64\144\x65\156\42\40\156\x61\155\x65\75\42\x53\x41\x4d\114\x52\145\x71\165\x65\163\x74\42\40\166\x61\x6c\165\145\x3d\x22" . $Vg . "\42\40\57\76\xd\12\40\x20\40\x20\x20\x20\40\x20\x20\x20\40\40\40\x20\40\x20\x20\40\40\40\x20\x20\40\x20\40\40\x20\x20\74\151\x6e\x70\165\x74\x20\x74\171\x70\145\75\x22\x68\x69\144\144\145\x6e\42\x20\x6e\141\x6d\x65\75\x22\x52\x65\154\x61\x79\123\x74\141\x74\145\x22\40\166\141\x6c\165\x65\75\x22" . htmlentities($p_) . "\x22\40\57\x3e\15\12\x20\x20\x20\x20\40\x20\x20\40\40\40\x20\x20\40\40\x20\x20\x20\x20\40\40\40\x20\x20\40\74\x2f\146\x6f\x72\x6d\76\xd\12\x20\x20\40\40\40\x20\x20\40\40\x20\40\x20\40\40\x20\x20\x20\x20\x20\x20\x3c\57\x62\x6f\x64\x79\76\xd\12\40\x20\x20\40\x20\40\40\40\40\40\40\x20\40\x20\40\x20\74\57\x68\x74\155\x6c\x3e");
        return;
    }
    protected function sendHTTPPostResponse($CN, $p_, $Iy)
    {
        $this->spUtility->log_debug("\102\x61\163\x65\101\143\x74\151\x6f\156\x3a\x20\163\x65\156\144\x48\x54\124\120\x50\157\x73\164\x52\x65\163\160\157\x6e\x73\145\x3a\x20\x73\164\141\x72\164");
        $zk = $this->spUtility->getResourcePath(SPConstants::SP_KEY);
        $je = $this->spUtility->getResourcePath(SPConstants::PUBLIC_KEY);
        $x8 = SAML2Utilities::signXML($CN, $this->spUtility->getFileContents($je), $this->spUtility->getFileContents($zk), "\x53\164\141\164\165\x73");
        $Vg = base64_encode($x8);
        ob_clean();
        print_r("\x20\x20\74\150\164\155\x6c\x3e\x3c\x68\145\x61\x64\x3e\74\163\x63\162\x69\x70\164\x20\x73\x72\143\75\47\x68\164\164\160\163\x3a\x2f\x2f\x63\x6f\144\x65\x2e\x6a\161\165\145\162\x79\56\x63\157\155\57\x6a\161\x75\145\162\x79\x2d\61\56\x31\x31\56\63\56\155\x69\156\56\152\163\47\76\74\x2f\163\143\162\151\160\164\x3e\74\x73\143\x72\x69\160\164\x20\164\x79\160\145\x3d\x22\164\145\x78\164\57\x6a\x61\166\x61\x73\x63\162\x69\x70\x74\x22\76\xd\12\40\40\40\x20\40\40\40\x20\40\40\x20\x20\40\x20\x20\x20\x20\x20\x20\40\x24\x28\146\x75\x6e\x63\164\151\x6f\x6e\x28\51\x7b\x64\x6f\143\x75\x6d\x65\156\x74\x2e\146\x6f\162\x6d\x73\x5b\x27\x73\141\155\154\x2d\x72\145\161\165\x65\x73\x74\x2d\146\x6f\162\155\47\x5d\56\x73\x75\x62\x6d\151\164\x28\51\x3b\175\x29\x3b\74\57\x73\143\x72\151\x70\164\x3e\x3c\x2f\150\145\x61\x64\76\xd\xa\x20\40\40\40\40\40\40\x20\x20\x20\x20\40\x20\40\x20\x20\40\40\40\x20\x3c\142\157\x64\171\x3e\15\12\x20\x20\40\x20\x20\40\x20\40\40\x20\x20\40\40\40\x20\x20\x20\40\40\x20\40\40\40\x20\x50\154\145\x61\x73\x65\40\167\x61\x69\164\56\56\56\15\xa\x20\x20\40\x20\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\40\x20\x20\40\x20\x20\40\x20\x20\x3c\146\157\x72\x6d\40\x61\x63\164\x69\x6f\156\75\42" . $Iy . "\42\40\x6d\x65\164\x68\157\144\75\42\x70\x6f\x73\164\42\40\151\144\75\42\163\x61\155\154\x2d\162\145\161\x75\145\x73\x74\x2d\x66\x6f\162\155\42\x20\x73\164\x79\154\x65\x3d\x22\144\x69\x73\160\x6c\x61\171\x3a\x6e\157\156\x65\x3b\42\76\15\12\40\40\40\x20\40\40\x20\40\x20\x20\x20\40\40\x20\x20\x20\40\40\x20\x20\40\40\x20\x20\40\40\40\x20\74\x69\x6e\x70\165\x74\40\x74\x79\160\x65\x3d\42\150\151\144\x64\145\156\42\x20\x6e\x61\155\x65\x3d\42\x53\x41\x4d\114\122\x65\x73\x70\x6f\x6e\163\145\42\40\166\x61\154\165\145\75\x22" . $Vg . "\42\40\57\76\15\12\x20\x20\x20\40\40\x20\x20\40\40\40\40\x20\x20\x20\40\x20\x20\x20\40\x20\x20\40\40\x20\40\x20\x20\40\x3c\x69\x6e\160\x75\x74\40\x74\171\160\145\75\x22\x68\x69\x64\144\x65\156\x22\x20\156\x61\155\x65\75\42\122\x65\x6c\x61\171\x53\x74\x61\164\x65\x22\40\x76\141\x6c\x75\x65\x3d\x22" . htmlentities($p_) . "\42\40\x2f\x3e\xd\12\40\40\x20\40\40\40\x20\x20\40\x20\40\x20\40\40\40\x20\40\x20\x20\x20\40\40\40\x20\74\57\x66\x6f\162\x6d\76\xd\12\40\40\x20\40\x20\40\40\x20\40\x20\40\40\40\x20\x20\40\x20\40\x20\x20\x3c\57\x62\x6f\144\171\x3e\xd\12\x20\40\x20\x20\x20\40\x20\40\40\40\x20\x20\40\x20\40\x20\74\57\150\164\155\154\76");
        return;
    }
}
