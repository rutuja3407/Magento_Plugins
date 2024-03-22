<?php


namespace MiniOrange\SP\Controller\Actions;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use MiniOrange\SP\Helper\SPConstants;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Backend\Model\Session\AdminConfig;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Security\Model\AdminSessionsManager;
use Magento\Backend\Model\UrlInterface;
use Magento\User\Model\UserFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseFactory;
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
    public function __construct(Context $Gc, SPUtility $Kx, Session $hL, CookieManagerInterface $Vj, AdminConfig $Yj, CookieMetadataFactory $YD, AdminSessionsManager $Qy, UrlInterface $WU, UserFactory $t2, RequestInterface $nD, StoreManagerInterface $Wl, ResultFactory $UZ, ResponseFactory $XF)
    {
        $this->adminSession = $hL;
        $this->cookieManager = $Vj;
        $this->adminConfig = $Yj;
        $this->cookieMetadataFactory = $YD;
        $this->adminSessionManager = $Qy;
        $this->urlInterface = $WU;
        $this->userFactory = $t2;
        $this->request = $nD;
        parent::__construct($Gc, $Kx, $Wl, $UZ, $XF);
    }
    public function execute()
    {
        $this->spUtility->log_debug("\40\151\x6e\163\151\x64\x65\40\101\x64\155\151\x6e\114\157\x67\151\x6e\x41\143\164\151\x6f\x6e\40\x3a\x20\145\x78\x65\143\165\164\x65\x28\x29\x3a\x20\x4c\x6f\x67\x67\x69\x6e\x67\x20\101\x64\x6d\151\156\x20\151\156\x20");
        $As = $this->request->getParams();
        $SK = $this->spUtility->getAdminStoreConfig(SPConstants::SESSION_INDEX, $As["\165\x73\145\x72\151\144"]);
        $F6 = $As["\x73\145\x73\163\151\157\x6e\x69\x6e\x64\145\x78"];
        if (!(strcasecmp($SK, $F6) != 0)) {
            goto HW;
        }
        return;
        HW:
        $user = $this->userFactory->create()->load($As["\x75\163\x65\x72\151\x64"]);
        $this->adminSession->setUser($user);
        $this->adminSession->processLogin();
        if (!$this->adminSession->isLoggedIn()) {
            goto XU;
        }
        $S_ = $this->adminSession->getSessionId();
        if (!$S_) {
            goto AK;
        }
        $D4 = str_replace("\141\x75\164\157\x6c\x6f\x67\151\x6e\x2e\160\150\160", "\x69\156\144\145\x78\x2e\x70\x68\x70", $this->adminConfig->getCookiePath());
        $dp = $this->cookieMetadataFactory->createPublicCookieMetadata()->setDuration(3600)->setPath($D4)->setDomain($this->adminConfig->getCookieDomain())->setSecure($this->adminConfig->getCookieSecure())->setHttpOnly($this->adminConfig->getCookieHttpOnly());
        $this->cookieManager->setPublicCookie($this->adminSession->getName(), $S_, $dp);
        $this->adminSessionManager->processLogin();
        AK:
        XU:
        $W1 = !$this->spUtility->isBlank($As["\x72\145\154\141\171\163\x74\x61\164\x65"]) ? $As["\x72\145\154\x61\171\163\164\141\x74\145"] : $this->urlInterface->getStartupPageUrl();
        $JY = $this->urlInterface->getUrl($W1);
        $JY = str_replace("\141\x75\x74\157\154\x6f\147\x69\156\x2e\160\x68\x70", "\x69\156\144\145\170\x2e\x70\x68\160", $JY);
        return $this->resultRedirectFactory->create()->setUrl($JY);
    }
}
