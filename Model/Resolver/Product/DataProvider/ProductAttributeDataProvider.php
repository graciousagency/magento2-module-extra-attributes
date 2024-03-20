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
    protected StoreManagerInterface $storeManager;
    protected ProductRepository $productRepository;

    public function __construct(
        ProductRepository $productRepository,
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return ProductInterface|DataObject
     */
    public function getProductBySku(string $sku, int $storeId): object
    {
        return $this->productRepository->get($sku, $storeId);
    }


    public function getAttributesBySku(string $sku): array
    {
        $storeId = $this->storeManager->getStore()->getId();
        try{
            $product = $this->getProductBySku($sku, $storeId);
        }catch (\Throwable $e){
            return [];
        }
        $attributes = $product->getAttributes();

        $attributeData = [];

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {

            if (!$attribute->getData('is_user_defined') || !$attribute->getData('is_visible_on_front')) {
                continue;
            }

            $frontend = $attribute->getFrontend();
            $type = $frontend->getInputType();
            $code = $attribute->getAttributeCode();
            $value = 'boolean' === $type  ? (bool)$product->getData($code) ? 'Yes' : 'No' : $frontend->getValue($product);

            $attributeData[] = [
                'code' => $code,
                'label' => $attribute->getStoreLabel($storeId),
                'type' => $type,
                'value' => $value
            ];
        }

        return $attributeData;
    }

}
