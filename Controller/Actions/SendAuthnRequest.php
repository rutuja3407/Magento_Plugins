<?php


namespace MiniOrange\SP\Controller\Actions;

use MiniOrange\SP\Helper\Saml2\AuthnRequest;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\Lib\AESEncryption;
class SendAuthnRequest extends BaseAction
{
    public function __construct(\Magento\Backend\App\Action\Context $Gc, \MiniOrange\SP\Helper\SPUtility $Kx, StoreManagerInterface $Wl, \Magento\Framework\Controller\ResultFactory $UZ, RequestInterface $nD, \Magento\Framework\App\ResponseFactory $XF)
    {
        $this->_request = $nD;
        parent::__construct($Gc, $Kx, $Wl, $UZ, $XF);
    }
    public function execute()
    {
        if (!$this->spUtility->isTrialExpired()) {
            goto Js;
        }
        $this->spUtility->log_debug("\x50\x72\x6f\143\145\x73\163\125\163\x65\x72\101\143\164\x69\157\x6e\72\40\145\170\x65\143\x75\x74\145\40\72\x20\x59\x6f\x75\x72\x20\x64\145\155\157\x20\141\143\143\x6f\x75\x6e\164\x20\150\x61\163\x20\145\170\160\x69\162\145\x64\56");
        print_r("\x59\x6f\165\x72\x20\104\145\155\157\x20\141\x63\x63\157\165\156\164\40\150\141\163\40\x65\170\160\151\162\145\144\x2e\x20\120\154\x65\141\x73\x65\x20\x63\x6f\x6e\x74\x61\143\164\40\x6d\x61\147\145\156\x74\157\x73\x75\x70\160\x6f\x72\164\x40\170\x65\143\x75\x72\x69\x66\x79\x2e\x63\157\x6d");
        exit;
        Js:
        $this->spUtility->log_debug("\x53\x65\156\144\101\165\164\x68\x6e\x52\145\161\165\145\x73\164\x3a\x20\145\x78\145\x63\165\x74\145");
        $As = $this->getRequest()->getParams();
        $this->checkIfValidPlugin();
        $rQ = $As["\151\x64\x70\137\156\141\155\145"];
        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $rQ);
        $Lw = $this->spUtility->getIDPApps();
        $ft = null;
        $s2 = $this->_redirect->getRefererUrl();
        $this->spUtility->log_debug("\x53\145\156\x64\x41\165\x74\150\x6f\162\151\x7a\141\x74\x69\x6f\156\122\145\x71\165\x65\x73\x74\72\x20\143\157\154\154\x65\x63\164\151\x6f\x6e\x20\x3a", count($Lw));
        foreach ($Lw as $fR) {
            if (!($fR->getData()["\x69\x64\x70\x5f\x6e\141\155\x65"] === $rQ)) {
                goto Va;
            }
            $ft = $fR->getData();
            Va:
            Lo:
        }
        dt:
        $qY = !empty($As["\162\145\154\141\171\x53\164\141\x74\145"]) ? $As["\x72\145\154\141\x79\x53\x74\x61\164\x65"] : $s2;
        $zc = $this->spUtility->checkIfFlowStartedFromBackend($qY);
        if (!($qY != SPConstants::TEST_RELAYSTATE && !$zc)) {
            goto FE;
        }
        $pO = false;
        $n6 = $this->spUtility->getCurrentWebsiteId();
        $rJ = $this->spUtility->getStoreConfig(SPConstants::WEBSITE_IDS);
        $no = $this->spUtility->getStoreConfig(SPConstants::WEBSITE_COUNT);
        $gj = $this->spUtility->isBlank($rJ) ? array() : json_decode($rJ);
        $Zm = $this->spUtility->getWebsiteLimit();
        if ($this->spUtility->isBlank($gj)) {
            goto Ac;
        }
        foreach ($gj as $zg => $XW) {
            if (!($n6 == $zg)) {
                goto io;
            }
            $pO = true;
            goto mK;
            io:
            yT:
        }
        mK:
        Ac:
        if (!($pO == false || $no > $Zm)) {
            goto Q7;
        }
        print_r("\131\x6f\x75\40\x68\x61\x76\x65\x20\x6e\157\x74\40\x73\145\x6c\145\143\x74\x65\x64\40\x74\x68\151\163\40\167\145\x62\163\x69\164\x65\40\146\x6f\162\x20\x53\x53\117");
        $this->messageManager->addErrorMessage(__("\131\x6f\x75\40\x68\141\166\x65\x20\x6e\157\x74\40\x73\145\x6c\x65\x63\x74\145\144\40\164\150\x69\x73\x20\x77\x65\142\163\151\164\145\x20\146\157\x72\x20\x53\x53\117"));
        return;
        Q7:
        FE:
        $Xv = $this->spUtility->isAutoRedirectEnabled($rQ);
        $xy = $this->spUtility->isAllPageAutoRedirectEnabled($rQ);
        if (!($qY != SPConstants::TEST_RELAYSTATE && $Xv && ($xy == NULL || $xy == 0))) {
            goto sX;
        }
        $qY = $this->_request->getServer("\x48\x54\x54\120\137\122\x45\x46\x45\x52\105\122");
        if (!$qY) {
            goto gA;
        }
        $qY = preg_replace("\57\x5c\57\44\x2f", '', $qY);
        gA:
        $this->spUtility->flushCache();
        sX:
        $Iy = $ft["\x73\x61\x6d\154\137\154\x6f\x67\151\x6e\x5f\x75\162\154"];
        $CR = $ft["\163\x61\155\154\x5f\x6c\x6f\x67\x69\156\137\x62\x69\x6e\144\x69\x6e\147"];
        $cI = $ft["\x66\157\162\x63\x65\x5f\141\x75\x74\150\x65\156\x74\151\x63\141\164\x69\x6f\156\x5f\167\x69\164\x68\x5f\x69\x64\160"];
        $R6 = $this->spUtility->getAcsUrl();
        $wz = $this->spUtility->getIssuerUrl();
        $this->spUtility->log_debug("\123\145\x6e\x64\x41\165\164\x68\x6e\x52\x65\x71\x75\x65\163\164\x3a\x20\145\170\x65\x63\165\x74\145\72\40\151\x64\160\40\143\157\156\x66\151\x67\x75\162\141\x74\x69\157\156\40\146\145\x74\x63\x68\x65\x64\40");
        $KJ = (new AuthnRequest($R6, $wz, $Iy, $cI, $CR))->build();
        $rQ = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x62\145\x66\x6f\x72\x65\x20\x73\145\156\x64\151\156\x67\40\x73\141\155\154\40\162\145\x71\x75\145\163\x74", $rQ);
        if (empty($CR) || $CR == SPConstants::HTTP_REDIRECT) {
            goto CJ;
        }
        $this->sendHTTPPostRequest($KJ, $qY, $Iy);
        goto zT;
        CJ:
        $this->sendHTTPRedirectRequest($KJ, $qY, $Iy);
        zT:
    }
}
