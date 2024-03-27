<?php


namespace MiniOrange\SP\Controller\Adminhtml\Metadata;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use MiniOrange\SP\Block\Sp;
use MiniOrange\SP\Controller\Actions\BaseAdminAction;
use MiniOrange\SP\Helper\Saml2\MetadataGenerator;
use MiniOrange\SP\Helper\SPConstants;
use MiniOrange\SP\Helper\SPUtility;
use Psr\Log\LoggerInterface;
class Index extends BaseAdminAction
{
    private $fileSystem;
    public function __construct(Context $gt, PageFactory $Jq, SPUtility $fR, ManagerInterface $b_, LoggerInterface $kU, File $B1, Sp $ou)
    {
        parent::__construct($gt, $Jq, $fR, $b_, $kU, $ou);
        $this->fileSystem = $B1;
        $this->sp = $ou;
    }
    public function execute()
    {
        $o6 = $this->spUtility->getIssuerUrl();
        $g0 = $this->spUtility->getAcsUrl();
        $UY = $this->spUtility->getFileContents($this->spUtility->getResourcePath("\163\x70\x2d\x63\145\162\x74\x69\x66\x69\143\141\164\145\56\143\x72\164"));
        $UY = $this->spUtility->desanitizeCert($UY);
        $BF = new MetadataGenerator($o6, TRUE, TRUE, $UY, $g0, $g0, $g0, $g0, $g0);
        $BF = $BF->generateSPMetadata();
        $this->fileSystem->filePutContents($this->spUtility->getMetadataFilePath(), $BF);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(SPConstants::MODULE_DIR . SPConstants::METADATA_DOWNLOAD);
    }
}
