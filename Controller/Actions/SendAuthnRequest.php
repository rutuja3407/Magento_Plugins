<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use MiniOrange\SP\Helper\Saml2\AuthnRequest;
use MiniOrange\SP\Helper\SPConstants;
class SendAuthnRequest extends BaseAction
{
    public function __construct(\Magento\Backend\App\Action\Context $gt, \MiniOrange\SP\Helper\SPUtility $fR, StoreManagerInterface $VO, \Magento\Framework\Controller\ResultFactory $ps, RequestInterface $E1, \Magento\Framework\App\ResponseFactory $Jv)
    {
        $this->_request = $E1;
        parent::__construct($gt, $fR, $VO, $ps, $Jv);
    }
    public function execute()
    {
        if (!$this->spUtility->isTrialExpired()) {
            goto T0;
        }
        $this->spUtility->log_debug("\120\162\x6f\143\145\163\163\x55\163\145\162\101\143\164\151\157\x6e\x3a\40\145\x78\145\x63\165\164\145\40\72\x20\x59\x6f\x75\x72\x20\x64\x65\155\157\x20\x61\143\x63\157\x75\x6e\x74\x20\x68\141\x73\x20\x65\170\160\x69\162\145\x64\56");
        print_r("\x59\157\165\162\x20\104\x65\x6d\157\40\141\143\x63\157\165\x6e\164\40\x68\x61\x73\40\145\170\160\151\x72\145\x64\x2e\x20\x50\x6c\x65\141\163\145\x20\x63\x6f\156\164\141\143\x74\x20\x6d\x61\147\145\156\x74\x6f\163\x75\160\x70\x6f\x72\164\x40\170\x65\x63\x75\x72\x69\x66\x79\56\143\157\x6d");
        exit;
        T0:
        $this->spUtility->log_debug("\123\x65\x6e\x64\101\165\164\x68\x6e\x52\145\161\x75\x65\163\x74\72\40\x65\x78\x65\143\x75\164\x65");
        $Te = $this->getRequest()->getParams();
        $this->checkIfValidPlugin();
        $rq = $Te["\151\x64\160\137\x6e\141\155\x65"];
        $this->spUtility->setSessionData(SPConstants::IDP_NAME, $rq);
        $yG = $this->spUtility->getIDPApps();
        $hR = null;
        $Ks = $this->_redirect->getRefererUrl();
        $this->spUtility->log_debug("\x53\145\x6e\x64\x41\x75\164\x68\157\x72\151\x7a\x61\164\x69\157\156\x52\x65\x71\x75\145\163\x74\x3a\x20\x63\157\x6c\154\x65\143\x74\151\x6f\x6e\40\72", count($yG));
        foreach ($yG as $ub) {
            if (!($ub->getData()["\151\144\160\x5f\156\141\x6d\145"] === $rq)) {
                goto oW;
            }
            $hR = $ub->getData();
            oW:
            pu:
        }
        Jt:
        $Nf = !empty($Te["\x72\x65\x6c\x61\x79\123\164\141\164\145"]) ? $Te["\x72\x65\154\x61\171\x53\164\141\164\145"] : $Ks;
        $Ea = $this->spUtility->checkIfFlowStartedFromBackend($Nf);
        if (!($Nf != SPConstants::TEST_RELAYSTATE && !$Ea)) {
            goto DE;
        }
        $eb = false;
        $u9 = $this->spUtility->getCurrentWebsiteId();
        $hq = $this->spUtility->getStoreConfig(SPConstants::WEBSITE_IDS);
        $Oe = $this->spUtility->getStoreConfig(SPConstants::WEBSITE_COUNT);
        $L_ = $this->spUtility->isBlank($hq) ? array() : json_decode($hq);
        $lx = $this->spUtility->getWebsiteLimit();
        if ($this->spUtility->isBlank($L_)) {
            goto Z2;
        }
        foreach ($L_ as $On => $Kt) {
            if (!($u9 == $On)) {
                goto T9;
            }
            $eb = true;
            goto wB;
            T9:
            Dx:
        }
        wB:
        Z2:
        if (!($eb == false || $Oe > $lx)) {
            goto ns;
        }
        print_r("\x59\x6f\x75\40\x68\x61\166\x65\x20\156\157\x74\40\163\145\154\x65\x63\x74\145\x64\x20\164\150\151\x73\x20\167\x65\x62\x73\x69\164\145\40\x66\x6f\x72\40\x53\x53\117");
        $this->messageManager->addErrorMessage(__("\x59\157\165\40\150\141\x76\145\40\156\x6f\x74\x20\163\145\154\145\x63\164\x65\x64\40\164\x68\151\163\x20\167\x65\x62\x73\x69\x74\x65\x20\x66\157\x72\x20\x53\x53\117"));
        return;
        ns:
        DE:
        $gR = $this->spUtility->isAutoRedirectEnabled($rq);
        $DG = $this->spUtility->isAllPageAutoRedirectEnabled($rq);
        if (!($Nf != SPConstants::TEST_RELAYSTATE && $gR && ($DG == NULL || $DG == 0))) {
            goto eT;
        }
        $Nf = $this->_request->getServer("\110\124\x54\x50\x5f\x52\x45\x46\x45\x52\x45\122");
        if (!$Nf) {
            goto yI;
        }
        $Nf = preg_replace("\x2f\134\x2f\x24\57", '', $Nf);
        yI:
        $this->spUtility->flushCache();
        eT:
        $s9 = $hR["\x73\141\x6d\154\137\x6c\157\x67\x69\x6e\x5f\165\x72\154"];
        $H2 = $hR["\x73\141\155\154\137\x6c\x6f\147\x69\x6e\x5f\x62\151\x6e\x64\x69\x6e\147"];
        $KM = $hR["\x66\x6f\x72\143\145\x5f\x61\x75\164\150\145\156\164\151\143\x61\x74\x69\x6f\x6e\x5f\x77\151\x74\x68\x5f\x69\144\160"];
        $X9 = $this->spUtility->getAcsUrl();
        $wQ = $this->spUtility->getIssuerUrl();
        $this->spUtility->log_debug("\x53\145\x6e\144\x41\x75\x74\x68\x6e\x52\145\x71\x75\x65\x73\x74\72\x20\x65\x78\145\x63\x75\164\145\x3a\x20\x69\144\160\40\143\x6f\156\x66\151\147\x75\x72\141\x74\x69\157\156\40\146\145\164\143\x68\x65\x64\x20");
        $ML = (new AuthnRequest($X9, $wQ, $s9, $KM, $H2))->build();
        $rq = $this->spUtility->getSessionData(SPConstants::IDP_NAME);
        $this->spUtility->log_debug("\x62\145\x66\x6f\162\145\40\163\x65\156\144\x69\156\x67\40\163\141\x6d\x6c\40\162\145\161\165\x65\163\164", $rq);
        if (empty($H2) || $H2 == SPConstants::HTTP_REDIRECT) {
            goto No;
        }
        $this->sendHTTPPostRequest($ML, $Nf, $s9);
        goto C3;
        No:
        $this->sendHTTPRedirectRequest($ML, $Nf, $s9);
        C3:
    }
}
