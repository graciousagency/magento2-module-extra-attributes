<?php

namespace Gracious\ExtraAttributes\Model\Resolver\Product\DataProvider;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class OrderItemAttributeDataProvider extends Template
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

        $attributeData = [];
        $x = 0;

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->getData('is_user_defined') || !$attribute->getData('show_in_order_item')) {
                continue;
            }
            $frontend = $attribute->getFrontend();
            $attributeData[$x]['code'] = $attribute->getAttributeCode();
            $attributeData[$x]['label'] = $attribute->getStoreLabel($storeId);
            $attributeData[$x]['value'] = $frontend->getValue($product);
            $attributeData[$x]['type'] = $frontend->getInputType();
            $x++;
        }

        return $attributeData;
    }
}
