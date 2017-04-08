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

namespace WSite\Informational\Model\Product\Attribute\Backend;

class File extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     *
     * @deprecated
     */
    protected $_uploaderFactory;

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     *
     * @deprecated
     */
    protected $_filesystem;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     *
     * @deprecated
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     *
     * @deprecated
     */
    protected $_logger;

    protected $_mediaDirectory;
    protected $_coreFileStorageDatabase;
    
    /**
     * Image constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
    ) {
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->_logger = $logger;
    }
    
    protected $_isNewProduct = false;
    
    public function beforeSave($object) {
        if ($object->getId()) {
            $this->_saveFile($object);
            $this->_isNewProduct = false;
        }
        else{
            $this->_isNewProduct = true;
        }
        return $this;
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param \Magento\Framework\DataObject $object
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        if ($this->_isNewProduct) {
            $this->_saveFile($object);
            $object->save();
        }
        
        return $this;
    }
    
    protected function _saveFile($object)
    {
        $file = $object->getData($this->getAttribute()->getName(), null);

        if (!$object->getData('download_file') && $object->getOrigData('download_file')) {
            $fileData = json_decode($object->getOrigData('download_file'));
            
            $this->_mediaDirectory->delete($fileData->file);
            return $this;
        }
        
        if (!is_array($file)) {
            return $this;
        }
        $fileData = array_shift($file);
        
        if (!isset($fileData['uploaded'])) {
            return $this;
        }
        
        $tmpFilePath = 'informational_product/tmp/' . $fileData['uploaded'];
        $baseFilePath = 'informational_product/' . $object->getId() . '/' . $fileData['name'];
        
        $this->_coreFileStorageDatabase->copyFile(
            $tmpFilePath,
            $baseFilePath
        );
        $this->_mediaDirectory->renameFile(
            $tmpFilePath,
            $baseFilePath
        );
        
        $fileInfo = [
            'file' => $baseFilePath,
            'type' => $fileData['type']
        ];
        
        $object->setData($this->getAttribute()->getName(), json_encode($fileInfo));
        
        return $this;
    }
}
