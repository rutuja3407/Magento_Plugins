<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\SAML2Response;
use MiniOrange\SP\Helper\Saml2\SAML2Assertion;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPUtility;
use MiniOrange\SP\Controller\Actions\ProcessResponseAction;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseFactory;
use MiniOrange\SP\Controller\Actions\Logout;
class ReadResponseAction extends BaseAction implements HttpPostActionInterface, HttpGetActionInterface
{
    private $REQUEST;
    private $POST;
    private $processResponseAction;
    protected $spUtility;
    protected $logout;
    public function __construct(Context $Gc, SPUtility $Kx, ProcessResponseAction $H0, StoreManagerInterface $Wl, ResultFactory $UZ, ResponseFactory $XF, Logout $I2)
    {
        $this->processResponseAction = $H0;
        $this->logout = $I2;
        parent::__construct($Gc, $Kx, $Wl, $UZ, $XF);
    }
    public function execute()
    {
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\162\145\x61\144\x72\x65\163\x70\157\x6e\163\145\101\x63\x74\151\157\x6e", $rQ);
        $this->checkIfValidPlugin();
        $As = $this->getRequest()->getParams();
        $CN = $this->REQUEST["\123\101\115\114\x52\x65\x73\x70\157\x6e\163\145"];
        $aM = base64_decode($_POST["\x53\x41\115\114\x52\145\163\160\x6f\x6e\x73\145"]);
        $L5 = new \DOMDocument();
        $L5->loadXML($aM);
        $nc = $L5->firstChild;
        $Rx = new SAML2Assertion($nc, $this->spUtility);
        $wz = $Rx->getIssuer();
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\151\144\160\x5f\145\156\164\x69\x74\x79\x5f\151\x64"] === $wz)) {
                goto zp;
            }
            $ft = $fR->getData();
            zp:
            zI:
        }
        h1:
        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $ft["\151\144\x70\137\156\x61\x6d\145"]);
        $this->spUtility->setAdminSessionData(SPConstants::IDP_NAME, $ft["\x69\144\x70\x5f\x6e\x61\155\x65"]);
        $qY = !empty($this->REQUEST["\122\x65\154\141\171\x53\x74\141\x74\x65"]) ? $this->REQUEST["\x52\x65\154\141\171\123\164\141\164\145"] : "\57";
        $CN = base64_decode((string) $CN);
        $this->spUtility->log_debug("\x73\141\155\x6c\x52\145\163\x70\157\x6e\x73\x65", print_r($CN, true));
        if (!empty($this->POST["\123\101\115\114\x52\145\163\x70\x6f\x6e\x73\x65"])) {
            goto A3;
        }
        $CN = gzinflate($CN);
        A3:
        $L5 = new \DOMDocument();
        $L5->loadXML($CN);
        $aB = $L5->firstChild;
        if (!($aB->localName == "\x4c\x6f\x67\x6f\165\x74\122\145\x73\x70\157\x6e\163\x65")) {
            goto ED;
        }
        $this->logout->setRequestParam($this->REQUEST)->setPostParam($this->POST)->execute();
        ED:
        $CN = new SAML2Response($aB, $this->spUtility);
        $this->spUtility->log_debug("\x62\x65\146\x6f\162\145\40\160\x72\157\x63\x65\x73\163\x75\x73\x65\x72\x61\x63\x74\x69\x6f\x6e");
        $this->processResponseAction->setSamlResponse($CN)->setRelayState($qY)->execute();
    }
    public function setRequestParam($nD)
    {
        $this->REQUEST = $nD;
        return $this;
    }
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }
}
