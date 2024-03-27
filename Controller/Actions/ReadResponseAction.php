<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\SAML2Assertion;
use MiniOrange\SP\Helper\Saml2\SAML2Response;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
class ReadResponseAction extends BaseAction implements HttpPostActionInterface, HttpGetActionInterface
{
    protected $spUtility;
    protected $logout;
    private $REQUEST;
    private $POST;
    private $processResponseAction;
    public function __construct(Context $gt, SPUtility $fR, ProcessResponseAction $HD, StoreManagerInterface $VO, ResultFactory $ps, ResponseFactory $Jv, Logout $bY)
    {
        $this->processResponseAction = $HD;
        $this->logout = $bY;
        parent::__construct($gt, $fR, $VO, $ps, $Jv);
    }
    public function execute()
    {
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x72\145\x61\x64\162\145\163\160\157\x6e\163\x65\101\x63\164\x69\x6f\x6e", $rq);
        $this->checkIfValidPlugin();
        $Te = $this->getRequest()->getParams();
        $yO = $this->REQUEST["\x53\101\115\x4c\x52\x65\163\160\157\x6e\163\145"];
        $iY = base64_decode($_POST["\x53\x41\115\114\x52\145\163\x70\x6f\156\163\145"]);
        $rT = new \DOMDocument();
        $rT->loadXML($iY);
        $Kh = $rT->firstChild;
        $wn = new SAML2Assertion($Kh, $this->spUtility);
        $wQ = $wn->getIssuer();
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        foreach ($yG as $ub) {
            if (!($ub->getData()["\x69\x64\x70\137\x65\x6e\164\x69\x74\x79\x5f\x69\x64"] === $wQ)) {
                goto lb;
            }
            $hR = $ub->getData();
            lb:
            k9:
        }
        ig:
        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $hR["\151\144\x70\x5f\156\x61\155\145"]);
        $this->spUtility->setAdminSessionData(SPConstants::IDP_NAME, $hR["\x69\144\160\x5f\x6e\141\x6d\x65"]);
        $Nf = !empty($this->REQUEST["\122\x65\x6c\x61\x79\x53\164\x61\x74\145"]) ? $this->REQUEST["\122\145\154\x61\x79\x53\x74\x61\164\145"] : "\x2f";
        $yO = base64_decode((string) $yO);
        $this->spUtility->log_debug("\163\141\155\x6c\x52\145\163\160\x6f\156\x73\x65", print_r($yO, true));
        if (!empty($this->POST["\x53\101\115\114\x52\145\163\160\x6f\x6e\163\x65"])) {
            goto bT;
        }
        $yO = gzinflate($yO);
        bT:
        $rT = new \DOMDocument();
        $rT->loadXML($yO);
        $dG = $rT->firstChild;
        if (!($dG->localName == "\114\x6f\147\157\165\164\x52\x65\163\x70\157\x6e\x73\145")) {
            goto mK;
        }
        $this->logout->setRequestParam($this->REQUEST)->setPostParam($this->POST)->execute();
        mK:
        $yO = new SAML2Response($dG, $this->spUtility);
        $this->spUtility->log_debug("\x62\x65\x66\157\162\x65\x20\x70\x72\x6f\143\x65\163\x73\165\163\x65\x72\x61\x63\x74\151\157\156");
        $this->processResponseAction->setSamlResponse($yO)->setRelayState($Nf)->execute();
    }
    public function setPostParam($post)
    {
        $this->POST = $post;
        return $this;
    }
    public function setRequestParam($E1)
    {
        $this->REQUEST = $E1;
        return $this;
    }
}
