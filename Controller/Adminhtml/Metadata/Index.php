<?php


namespace MiniOrange\SP\Controller\Adminhtml\Metadata;

use Magento\Backend\App\Action\Context;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPMessages;
use MiniOrange\SP\Helper\Saml2\SAML2Utilities;
use MiniOrange\SP\Helper\Saml2\MetadataGenerator;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Helper\SPUtility;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\Driver\File;
class Index extends BaseAdminAction
{
    private $fileSystem;
    public function __construct(Context $Gc, PageFactory $VM, SPUtility $Kx, ManagerInterface $c0, LoggerInterface $hI, File $PC, Sp $vT)
    {
        parent::__construct($Gc, $VM, $Kx, $c0, $hI, $vT);
        $this->fileSystem = $PC;
        $this->sp = $vT;
    }
    public function execute()
    {
        $gP = $this->spUtility->getIssuerUrl();
        $pW = $this->spUtility->getAcsUrl();
        $Vv = $this->spUtility->getFileContents($this->spUtility->getResourcePath("\163\x70\55\143\x65\x72\164\151\x66\151\x63\x61\164\145\56\143\x72\x74"));
        $Vv = $this->spUtility->desanitizeCert($Vv);
        $Xe = new MetadataGenerator($gP, TRUE, TRUE, $Vv, $pW, $pW, $pW, $pW, $pW);
        $Xe = $Xe->generateSPMetadata();
        $this->fileSystem->filePutContents($this->spUtility->getMetadataFilePath(), $Xe);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::METADATA_DOWNLOAD);
    }
}
