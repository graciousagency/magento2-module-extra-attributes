<?php

namespace Gracious\ExtraAttributes\Model\Resolver\Product\DataProvider;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class ProductAttributeDataProvider extends Template
{
    public function __construct(
        protected ProductRepository $productRepository,
        Context $context,
        protected StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getProductBySku(string $sku, int $storeId): ProductInterface|DataObject
    {
        return $this->productRepository->get($sku, $storeId);
    }


    public function getAttributesBySku(string $sku): array
    {
        $storeId = $this->storeManager->getStore()->getId();
        $product = $this->getProductBySku($sku, $storeId);
        $attributes = $product->getAttributes();

        $attributes_data = [];
        $x = 0;

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->getData('is_user_defined') || !$attribute->getData('is_visible_on_front')) {
                continue;
            }
            $frontend = $attribute->getFrontend();
            $attributes_data[$x]['code'] = $attribute->getAttributeCode();
            $attributes_data[$x]['label'] = $attribute->getStoreLabel($storeId);
            $attributes_data[$x]['value'] = $frontend->getValue($product);
            $x++;
        }

        return $attributes_data;

    }
}
