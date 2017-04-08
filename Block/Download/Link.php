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

namespace WSite\Informational\Block\Download;

class Link extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Construct
     *
     * @param Template\Context $context
     * @param Registry $registry 
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return type
     */
    protected function _construct() {
        $this->setTemplate('WSite_Informational::download/link.phtml');
        return parent::_construct();
    }
    
    /**
     * Returns saleable item instance
     *
     * @return Product
     */
    protected function _getProduct()
    {
        $parentBlock = $this->getParentBlock();

        $product = $parentBlock && $parentBlock->getProductItem()
            ? $parentBlock->getProductItem()
            : $this->_registry->registry('product');
        return $product;
    }
    
    public function getDownloadLink()
    {
        $productModel = $this->_getProduct();
        
        $downloadLink = $productModel->getDownloadLink();
        $downloadFile = $productModel->getDownloadFile();
        if ($downloadFile && !$downloadLink) {
            $downloadFile = json_decode($downloadFile);
            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $downloadLink = $mediaUrl . $downloadFile->file;
        }
        
        return $downloadLink;
    }
}
