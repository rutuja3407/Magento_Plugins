<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\Session\AdminConfig;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Security\Model\AdminSessionsManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\UserFactory;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
class AdminLoginAction extends BaseAction
{
    private $relayState;
    private $user;
    private $adminSession;
    private $cookieManager;
    private $adminConfig;
    private $cookieMetadataFactory;
    private $adminSessionManager;
    private $urlInterface;
    private $userFactory;
    private $request;
    public function __construct(Context $gt, SPUtility $fR, Session $y6, CookieManagerInterface $Xn, AdminConfig $Af, CookieMetadataFactory $HY, AdminSessionsManager $aw, UrlInterface $kL, UserFactory $Do, RequestInterface $E1, StoreManagerInterface $VO, ResultFactory $ps, ResponseFactory $Jv)
    {
        $this->adminSession = $y6;
        $this->cookieManager = $Xn;
        $this->adminConfig = $Af;
        $this->cookieMetadataFactory = $HY;
        $this->adminSessionManager = $aw;
        $this->urlInterface = $kL;
        $this->userFactory = $Do;
        $this->request = $E1;
        parent::__construct($gt, $fR, $VO, $ps, $Jv);
    }
    public function execute()
    {
        $this->spUtility->log_debug("\40\151\x6e\x73\151\144\x65\x20\x41\x64\x6d\151\x6e\x4c\157\x67\x69\x6e\x41\143\164\x69\x6f\x6e\40\72\40\145\170\145\x63\x75\164\x65\x28\51\x3a\40\114\x6f\x67\x67\151\x6e\x67\x20\x41\x64\155\151\156\40\x69\x6e\x20");
        $Te = $this->request->getParams();
        $lr = $this->spUtility->getAdminStoreConfig(SPConstants::SESSION_INDEX, $Te["\165\163\x65\x72\151\x64"]);
        $Ot = $Te["\x73\x65\x73\x73\151\x6f\x6e\151\x6e\144\x65\x78"];
        if (!(strcasecmp($lr, $Ot) != 0)) {
            goto Xg;
        }
        return;
        Xg:
        $user = $this->userFactory->create()->load($Te["\165\163\x65\x72\x69\x64"]);
        $this->adminSession->setUser($user);
        $this->adminSession->processLogin();
        if (!$this->adminSession->isLoggedIn()) {
            goto cd;
        }
        $Pl = $this->adminSession->getSessionId();
        if (!$Pl) {
            goto Vu;
        }
        $Ol = str_replace("\x61\165\164\157\x6c\157\x67\151\156\x2e\160\x68\x70", "\x69\x6e\144\145\170\56\x70\x68\160", $this->adminConfig->getCookiePath());
        $NV = $this->cookieMetadataFactory->createPublicCookieMetadata()->setDuration(3600)->setPath($Ol)->setDomain($this->adminConfig->getCookieDomain())->setSecure($this->adminConfig->getCookieSecure())->setHttpOnly($this->adminConfig->getCookieHttpOnly());
        $this->cookieManager->setPublicCookie($this->adminSession->getName(), $Pl, $NV);
        $this->adminSessionManager->processLogin();
        Vu:
        cd:
        $Wg = !$this->spUtility->isBlank($Te["\162\145\154\x61\171\163\164\141\164\145"]) ? $Te["\x72\x65\x6c\141\171\x73\164\141\x74\145"] : $this->urlInterface->getStartupPageUrl();
        $At = $this->urlInterface->getUrl($Wg);
        $At = str_replace("\141\165\x74\x6f\154\x6f\x67\151\x6e\56\160\x68\160", "\151\x6e\x64\145\x78\x2e\160\150\160", $At);
        return $this->resultRedirectFactory->create()->setUrl($At);
    }
}
