<?php
/**
 * ╔╗╔╗╔╦══╦══╦══╦════╦═══╗╔══╦═══╦═══╗
 * ║║║║║╠═╗║╔═╩╗╔╩═╗╔═╣╔══╝║╔╗║╔═╗║╔══╝
 * ║║║║║╠═╝║╚═╗║║──║║─║╚══╗║║║║╚═╝║║╔═╗
 * ║║║║║╠═╗╠═╗║║║──║║─║╔══╝║║║║╔╗╔╣║╚╗║
 * ║╚╝╚╝╠═╝╠═╝╠╝╚╗─║║─║╚══╦╣╚╝║║║║║╚═╝║
 * ╚═╝╚═╩══╩══╩══╝─╚╝─╚═══╩╩══╩╝╚╝╚═══╝
 * 
 * Examples and documentation at the: http://w3site.org
 * 
 * @copyright   Copyright (c) 2015-2016 Tereta Alexander. (http://www.w3site.org)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace WSite\Informational\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;

class UploadFile extends \Magento\Backend\App\Action
{
    protected $_mediaDirectory;
    protected $_uploaderFactory;
    protected $_storeManager;
    
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_uploaderFactory = $uploaderFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $uploader = $this->_uploaderFactory->create(['fileId' => 'product[download_file]']);
        $uploader->setAllowRenameFiles(true);
        
        $uniqfileName = uniqid() . '.' . $uploader->getFileExtension();
        $result = $uploader->save($this->_mediaDirectory->getAbsolutePath('informational_product/tmp/'), $uniqfileName);
        $result['uploaded'] = $result['file'];
        $result['url'] = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC)
            . 'adminhtml/Magento/backend/uk_UA/WSite_Informational/images/file.png';
        
        $downloadLink = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
            $result['file'];
        
        $result['downloadLink'] = $downloadLink;

        $result = array_intersect_key(
            $result,
            array_flip(['error', 'size', 'name', 'type', 'file', 'uploaded', 'url', 'downloadLink'])
        );
        
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
