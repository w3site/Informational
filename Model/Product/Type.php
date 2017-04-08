<?php

namespace WSite\Informational\Model\Product;

class Type extends \Magento\Catalog\Model\Product\Type\Virtual {
    public function isSalable($product)
    {
        return false;
    }
    
    public function getIsSalable($product)
    {
        return true;
    }
}