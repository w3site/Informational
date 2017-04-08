<?php

namespace WSite\Informational\Ui\DataProvider\Product\Form;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Model\Locator\LocatorInterface;

class Modifier extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    protected $_arrayManager;
    protected $_locator;
    protected $_mediaDirectory;
    protected $_storeManager;
    
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        ArrayManager $arrayManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        LocatorInterface $locator
    ) {
        $this->_arrayManager = $arrayManager;
        $this->_locator = $locator;
        $this->_storeManager = $storeManager;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }
    
    public function modifyData(array $data)
    {
        return $data;
    }
    
    public function modifyMeta(array $meta)
    {
        $productModel = $this->_locator->getProduct();
        if ($productModel->getTypeId() != 'informational') {
            return $meta;
        }
        
        //$meta['product-details']['children']['container_download_file']['children']['download_file']['arguments']['data']['config'];
        unset($meta['product-details']['children']['container_quantity_and_stock_status']);
        
        if (!isset($meta['product-details']['children']['container_download_file'])) {
            return $meta;
        }
        
        $downloadFile = null;
        $downloadFileInfo = [];
        if ($productModel->getData('download_file') && is_string($productModel->getData('download_file'))) {
            $downloadFileInfo = json_decode($productModel->getData('download_file'));
            $downloadFile = $this->_mediaDirectory->getAbsolutePath($downloadFileInfo->file);
        }
        
        if ($downloadFile && file_exists($downloadFile)) {
            $fileInfo = new \SplFileObject($downloadFile);
            
            $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) .
                'adminhtml/Magento/backend/uk_UA/WSite_Informational/images/file.png';
            
            $downloadLink = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
                $downloadFileInfo->file;
            
            $productModel->setData('download_file', [
                [
                    'name'         => $fileInfo->getFilename(),
                    'file'         => $downloadFileInfo->file,
                    'type'         => $downloadFileInfo->type,
                    'size'         => $fileInfo->getSize(),
                    'error'        => 0,
                    'url'          => $url,
                    'downloadLink' => $downloadLink
                ]
            ]);
        }
        elseif ($downloadFile) {
            $productModel->setData('download_file', null);
            $productModel->save();
        }
        else {
            $productModel->setData('download_file', []);
        }
        
        $previewTmpl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) .
            'adminhtml/Magento/backend/uk_UA/WSite_Informational/templates/form/element/uploader/preview.html';
        
        $meta = $this->_arrayManager->merge(
            'product-details/children/container_download_file',
            $meta,
            [
                'children' => [
                    'download_file' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'previewTmpl' => $previewTmpl,
                                    'uploaderConfig' => [
                                        'url' => 'wsite_informational/product/uploadFile'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        
        return $meta;
    }
}

