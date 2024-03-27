<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use MiniOrange\SP\Helper\Data;
use MiniOrange\SP\Helper\SPUtility;
class Metadata extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $messageManager;
    protected $spUtility;
    protected $request;
    protected $formkey;
    protected $acsUrl;
    protected $customerSession;
    protected $session;
    protected $data;
    protected $urlInterface;
    protected $eventManager;
    public function __construct(ManagerInterface $b_, Context $gt, SPUtility $fR, RequestInterface $E1, FormKey $MW, Session $fD, SessionManagerInterface $yH, Data $or, \Magento\Framework\Event\ManagerInterface $oR, \Magento\Framework\UrlInterface $kL)
    {
        $this->messageManager = $b_;
        $this->spUtility = $fR;
        $this->request = $E1;
        $this->customerSession = $fD;
        $this->session = $yH;
        $this->data = $or;
        $this->eventManager = $oR;
        $this->urlInterface = $kL;
        parent::__construct($gt);
        $this->formkey = $MW;
        $this->getRequest()->setParam("\x66\x6f\162\x6d\x5f\x6b\x65\x79", $this->formkey->getFormKey());
    }
    public function createCsrfValidationException(RequestInterface $E1) : ?InvalidRequestException
    {
        return null;
    }
    public function validateForCsrf(RequestInterface $E1) : ?bool
    {
        return true;
    }
    public function execute()
    {
        $base_url = $this->spUtility->getBaseUrl();
        $db = "\103\157\x6e\164\145\x6e\x74\x2d\124\x79\160\145\x3a\40\x74\x65\170\x74\57\170\x6d\154";
        header($db);
        echo "\74\77\170\x6d\154\40\x76\x65\162\163\x69\x6f\156\x3d\42\x31\x2e\60\x22\x3f\76\15\12\40\x20\40\x20\40\40\x20\x20\x20\40\x20\x20\x3c\x6d\x64\72\105\x6e\x74\151\x74\x79\x44\145\x73\x63\162\x69\160\164\157\x72\40\170\x6d\154\x6e\163\72\155\144\x3d\42\165\162\x6e\72\x6f\141\x73\x69\x73\72\156\x61\155\145\x73\x3a\164\143\x3a\x53\101\115\x4c\x3a\62\x2e\60\72\x6d\x65\x74\x61\144\x61\164\x61\42\40\145\x6e\x74\151\164\x79\x49\x44\x3d\x22" . $base_url . "\x6d\157\163\160\x73\141\x6d\154\57\155\145\x74\141\144\x61\164\x61\x2f\x69\x6e\144\x65\170\42\76\xd\xa\x20\x20\40\40\x20\40\x20\40\x20\x20\40\40\74\155\x64\x3a\123\x50\123\123\117\x44\x65\163\x63\162\x69\x70\x74\x6f\x72\x20\x57\x61\156\x74\x41\x75\x74\150\x6e\x52\x65\161\165\145\163\x74\163\x53\x69\147\156\145\144\75\x22\164\x72\165\x65\42\40\160\x72\x6f\164\157\x63\x6f\154\123\165\x70\x70\x6f\x72\164\105\156\x75\x6d\145\162\141\164\151\157\156\x3d\42\165\162\x6e\72\157\141\163\x69\x73\72\156\x61\155\x65\163\x3a\x74\143\72\123\101\x4d\x4c\72\62\56\60\72\x70\162\157\x74\157\x63\157\x6c\x22\76\15\xa\x20\40\40\40\x20\40\40\x20\x20\40\40\x20\74\155\x64\x3a\113\145\x79\104\145\163\x63\x72\151\x70\x74\157\x72\40\x75\163\x65\75\42\163\x69\x67\156\151\156\x67\x22\x3e\15\xa\40\x20\40\x20\x20\x20\40\x20\40\x20\x20\40\74\x4b\145\x79\x49\x6e\x66\157\40\170\155\154\156\163\x3d\x22\150\164\x74\x70\x3a\x2f\57\x77\x77\x77\56\167\x33\x2e\x6f\x72\147\x2f\62\60\60\x30\57\60\x39\x2f\170\155\x6c\x64\163\x69\x67\43\x22\x3e\15\12\40\40\40\40\x20\x20\40\x20\40\40\40\x20\74\x58\65\x30\x39\104\x61\x74\141\x3e\xd\12\40\x20\x20\40\x20\x20\x20\40\x20\x20\x20\x20\74\x58\65\x30\x39\x43\x65\162\x74\151\146\x69\x63\x61\x74\x65\x3e\15\12\40\40\x20\40\x20\x20\x20\40\40\x20\x20\x20\x4d\x49\111\105\105\x7a\103\x43\101\166\165\147\x41\167\111\x42\101\147\x49\125\x50\156\x46\x35\170\x69\155\x52\x6a\66\161\x61\105\67\62\x70\155\132\x61\x45\x61\x2f\x76\71\x6c\164\x73\x77\104\121\131\x4a\x4b\157\x5a\111\150\166\143\x4e\x41\x51\x45\x4c\102\121\101\167\147\x5a\147\170\103\172\101\x4a\102\147\x4e\x56\102\x41\131\124\101\153\154\x4f\x4d\x52\x51\x77\x45\147\x59\104\126\121\121\111\x44\101\x74\116\131\127\150\150\143\155\106\172\x61\x48\x52\171\x59\x54\105\x4e\115\x41\163\x47\101\x31\125\105\102\167\x77\x45\125\106\x56\x4f\122\x54\x45\124\x4d\x42\105\x47\x41\61\x55\x45\103\147\167\x4b\x62\x57\154\x75\141\125\x39\171\x59\x57\x35\x6e\x5a\x54\x45\x51\x4d\101\64\x47\101\61\125\x45\103\167\167\x48\x54\x57\106\x6e\132\x57\x35\60\x62\x7a\x45\122\x4d\101\x38\x47\x41\x31\x55\x45\101\167\167\111\145\x47\x56\x6a\144\x58\112\x70\x5a\156\153\170\x4b\x6a\101\x6f\102\x67\153\161\x68\153\151\107\x39\x77\60\102\x43\x51\x45\127\107\62\61\150\132\x32\126\x75\144\107\x39\x7a\144\x58\102\167\142\63\x4a\x30\121\110\150\x6c\x59\63\126\171\141\127\x5a\x35\x4c\155\x4e\x76\x62\124\x41\x65\106\x77\60\x79\115\172\101\x78\115\124\147\x78\x4f\104\121\x34\x4e\124\x6c\141\x46\167\x30\x79\x4f\x44\101\x78\x4d\x54\x63\x78\x4f\104\121\64\x4e\x54\154\x61\115\x49\x47\x59\115\x51\x73\167\x43\121\x59\x44\x56\x51\121\107\105\167\112\112\x54\152\105\x55\115\102\111\x47\101\x31\x55\x45\103\x41\x77\114\x54\127\106\x6f\x59\x58\x4a\150\143\x32\x68\x30\143\x6d\105\x78\x44\x54\x41\x4c\102\147\x4e\x56\x42\101\x63\115\x42\106\x42\126\124\153\125\170\105\x7a\101\122\102\x67\116\x56\102\101\x6f\115\x43\x6d\x31\x70\x62\x6d\x6c\120\x63\x6d\106\165\x5a\x32\x55\x78\x45\x44\x41\x4f\x42\x67\x4e\x56\x42\101\x73\x4d\x42\60\x31\150\x5a\x32\x56\x75\x64\107\70\x78\x45\x54\101\x50\x42\x67\x4e\126\102\x41\115\x4d\103\110\150\154\131\63\x56\171\141\x57\x5a\x35\x4d\123\x6f\x77\x4b\101\x59\112\x4b\157\132\x49\150\166\x63\x4e\x41\121\153\x42\106\150\164\x74\131\127\144\154\x62\156\x52\x76\x63\63\126\x77\143\x47\71\x79\x64\105\102\64\132\127\116\61\143\x6d\154\x6d\145\x53\65\x6a\x62\x32\60\167\x67\147\105\151\115\101\x30\107\x43\123\x71\107\x53\x49\x62\x33\104\121\x45\x42\x41\121\125\x41\101\64\x49\102\x44\167\x41\167\147\x67\105\x4b\101\157\111\x42\101\121\104\x41\167\155\152\x69\x32\x35\x37\57\x34\x6a\x6c\164\117\143\x4d\151\166\x30\165\x45\160\125\161\164\x43\x6f\x65\115\x52\x48\123\x78\x53\107\172\111\166\164\x79\155\155\153\x45\150\x5a\x77\53\62\x78\163\104\x38\101\125\123\x54\165\x62\171\x48\x2b\132\113\131\153\x5a\x49\x6d\63\x5a\x6c\141\156\153\x39\x72\170\x76\x44\112\101\x35\163\x77\x37\x30\61\x7a\151\x54\120\166\101\122\x54\x49\141\66\x72\164\156\107\143\172\141\x70\x37\x78\x65\x54\x41\x74\153\x62\x54\127\145\x65\167\146\x45\x4c\x2f\x54\150\147\x47\121\130\112\64\x65\142\x39\x6c\x61\163\x71\x39\111\53\x56\144\x69\x32\63\x71\165\150\141\x55\61\152\x42\x4f\165\x78\x4c\53\63\66\x61\x65\147\154\172\x45\131\107\126\127\66\x4a\160\142\161\121\103\x52\152\x6d\160\114\166\57\x32\156\112\x61\x51\132\66\x67\x4d\x61\171\112\57\x32\152\103\x4c\x71\x34\x2b\x37\111\x33\x6b\x58\x31\x72\x67\x56\125\130\x73\70\111\57\x61\x74\x4b\161\x79\104\163\x59\x46\57\x51\x46\105\x57\x46\122\x6a\x49\124\164\130\150\x39\64\x62\170\161\x39\x6b\162\x58\161\156\110\121\x4d\x61\x30\x59\x52\x6e\122\125\x76\x70\x49\156\64\x45\x44\170\120\x54\x45\x78\x56\x58\170\x46\x62\x36\x4e\x51\53\x33\127\141\x71\71\x64\113\x6d\170\x4a\x75\x68\x31\x4d\127\66\x75\101\151\155\x46\114\170\132\163\x44\145\125\155\x69\x64\60\x74\126\170\x6b\x66\x67\x48\141\x31\x6a\146\x78\x53\x4f\x65\x70\x48\153\70\165\x72\166\x72\102\x41\147\x4d\x42\101\101\x47\152\x55\172\x42\x52\x4d\102\x30\x47\x41\61\x55\144\104\147\x51\127\102\102\124\x4b\106\x6a\x2b\53\132\x6d\153\142\x38\x52\172\x6f\103\131\71\117\x67\x37\144\67\x4a\x6f\x77\x67\123\152\x41\146\102\147\116\126\x48\123\115\x45\107\x44\101\127\147\x42\x54\113\x46\152\53\53\x5a\155\153\142\70\122\172\x6f\103\131\x39\x4f\147\x37\x64\x37\x4a\x6f\167\147\x53\152\101\x50\102\x67\116\126\110\x52\x4d\x42\x41\x66\x38\x45\x42\x54\101\104\101\121\x48\57\x4d\101\60\107\x43\x53\x71\107\123\111\x62\63\104\x51\x45\102\x43\167\125\101\x41\64\x49\x42\101\x51\x42\152\155\x44\70\106\162\x36\x6a\x55\x51\x62\x4d\166\x41\x71\x63\x36\142\114\123\63\x34\106\x65\x2f\x66\x46\101\x67\x4c\x77\65\x6a\x37\x2f\111\x53\x68\157\131\120\x4f\123\x5a\x6a\162\x6f\153\161\172\125\x71\155\x31\x6d\x52\103\x36\x58\162\x6d\x7a\x72\130\x32\x71\156\x72\x63\x74\167\121\x30\x64\62\x53\71\x77\114\104\x73\x48\155\x48\x39\x4c\x6b\64\121\x78\53\x51\151\x36\x59\x78\x7a\113\x6e\x43\160\154\x75\x4c\67\x76\164\x67\x45\165\61\125\142\61\x4a\x48\x75\x43\x45\171\132\145\67\x4d\111\x4b\130\x54\x59\x2b\x59\71\x39\x31\x54\113\x4b\104\x63\x62\153\x41\x41\x6f\150\163\127\x75\x2b\151\x57\x37\x73\132\143\70\101\x64\163\x58\144\x6d\65\115\x6b\160\170\121\141\x72\x32\x36\x44\113\155\x63\x34\64\150\x75\101\x6e\x34\122\156\x49\123\x75\166\150\63\103\164\x6f\x59\x64\x53\x38\x59\53\151\120\x47\107\x53\143\x33\x46\x52\x56\x38\160\160\131\x57\x34\x45\57\145\104\62\111\147\127\x34\163\130\65\x41\x4d\x2f\x72\57\x39\61\x74\157\x35\x30\x56\x54\170\x55\x41\111\106\162\x48\147\x57\105\165\x6f\x2f\x6b\x68\161\x37\141\x77\105\x77\143\112\x2b\165\106\153\171\x68\167\144\171\x52\125\x62\x31\121\141\x61\x70\167\130\x2b\151\x75\123\x34\x47\70\x41\x4f\x4c\x4f\x56\x47\x32\x75\166\x6c\x47\x75\x37\x67\x78\x51\x53\x47\120\x2b\x76\160\125\164\122\153\x31\123\x2f\110\71\60\143\155\x73\167\x64\122\x30\x43\125\x5a\x64\x77\x78\70\x75\x62\x55\x6f\x43\15\12\x20\x20\x20\x20\x20\40\40\40\x20\x20\40\x20\74\x2f\x58\x35\60\x39\103\145\162\164\x69\146\151\143\x61\164\x65\76\15\12\x20\x20\x20\40\x20\x20\x20\x20\x20\x20\40\x20\x3c\57\x58\65\x30\x39\x44\141\164\141\76\15\xa\40\40\40\x20\40\40\40\x20\x20\40\40\40\74\57\113\145\x79\111\x6e\x66\x6f\76\xd\12\x20\40\x20\40\40\40\x20\x20\40\40\x20\40\74\x2f\155\144\x3a\x4b\145\171\x44\145\x73\143\162\151\160\164\157\162\76\xd\xa\x20\x20\x20\x20\x20\40\x20\40\x20\40\x20\40\74\155\x64\x3a\116\141\x6d\x65\111\104\x46\157\162\x6d\141\164\76\x75\x72\x6e\x3a\157\x61\163\x69\163\72\156\141\155\x65\163\x3a\x74\x63\72\x53\x41\x4d\114\72\x31\x2e\61\72\156\141\x6d\145\151\x64\55\x66\x6f\x72\155\x61\x74\x3a\145\155\x61\x69\x6c\x41\x64\144\162\x65\x73\x73\x3c\x2f\x6d\144\x3a\x4e\141\155\x65\111\x44\106\x6f\x72\155\x61\164\x3e\15\12\40\40\40\x20\x20\x20\x20\x20\x20\40\40\40\74\155\144\x3a\x4e\x61\155\145\x49\x44\106\157\x72\155\x61\x74\x3e\x75\x72\x6e\72\x6f\x61\x73\x69\163\72\156\x61\155\145\163\x3a\164\x63\72\123\x41\115\x4c\72\x31\x2e\61\72\x6e\141\x6d\145\x69\144\x2d\146\x6f\x72\155\141\164\72\x75\x6e\x73\x70\x65\x63\151\146\x69\x65\x64\74\x2f\x6d\144\x3a\116\141\x6d\145\111\104\x46\x6f\162\x6d\141\x74\x3e\15\xa\x20\40\x20\40\x20\40\x20\x20\40\x20\40\x20\x3c\155\144\x3a\123\x69\x6e\x67\x6c\x65\123\x69\147\156\117\156\123\145\x72\x76\x69\143\145\x20\x42\151\156\144\x69\156\x67\75\42\165\162\x6e\x3a\157\141\163\x69\x73\x3a\x6e\x61\x6d\145\163\72\164\x63\72\x53\101\115\114\x3a\62\56\60\x3a\142\x69\156\x64\151\x6e\x67\x73\x3a\110\x54\x54\x50\55\x50\117\123\x54\42\x20\x4c\x6f\x63\141\164\x69\157\x6e\x3d\x22" . $base_url . "\155\157\x73\x70\163\141\155\154\57\x61\143\164\x69\x6f\x6e\x73\57\x73\160\x4f\142\163\x65\162\x76\x65\x72\x22\57\x3e\15\xa\x20\40\x20\40\x20\40\40\40\40\40\x20\x20\74\x6d\x64\x3a\x53\x69\156\x67\154\x65\123\x69\x67\156\x4f\x6e\123\145\x72\x76\151\143\x65\x20\x42\x69\x6e\x64\151\156\x67\75\42\165\x72\156\72\x6f\x61\163\151\163\x3a\x6e\x61\x6d\x65\x73\x3a\x74\143\72\x53\x41\115\114\72\x32\x2e\60\x3a\142\x69\x6e\x64\151\x6e\147\x73\72\110\124\x54\120\x2d\x52\x65\x64\x69\x72\x65\143\164\x22\40\114\x6f\143\x61\164\x69\157\x6e\x3d\x22" . $base_url . "\155\x6f\163\x70\x73\141\155\x6c\x2f\141\143\x74\x69\157\x6e\163\x2f\x73\160\117\x62\x73\145\x72\166\145\x72\x22\x2f\x3e\15\xa\x20\40\x20\x20\40\x20\40\40\40\40\x20\40\x3c\57\x6d\144\72\x53\120\123\x53\x4f\x44\145\x73\x63\x72\x69\x70\164\x6f\x72\x3e\xd\12\x20\x20\40\x20\x20\40\40\x20\40\40\x20\x20\74\x2f\155\x64\x3a\x45\156\x74\x69\x74\x79\x44\145\163\143\162\x69\160\x74\157\162\x3e\xd\xa\40\40\x20\40\x20\40\40\40\x20\x20\40\40";
        return;
    }
}
