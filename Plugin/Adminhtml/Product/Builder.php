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

namespace WSite\Informational\Plugin\Adminhtml\Product;

use \Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use \Magento\Catalog\Model\Product;
use \Magento\Framework\App\Request\Http;

class Builder {
    public function aroundBuild(ProductBuilder $builder, callable $proceed, Http $request) {
        $product = $proceed($request);
        
        if ($product->getTypeId() != 'informational' || !$request->getParam('product')) {
            return $product;
        }
        
        if (!isset($request->getParam('product')['download_file'])) {
            $product->setData('download_file', null);
        }
        
        return $product;
    }
}