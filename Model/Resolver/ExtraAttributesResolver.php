<?php

namespace Gracious\ExtraAttributes\Model\Resolver;

use Gracious\ExtraAttributes\Model\Resolver\Product\DataProvider\OrderItemAttributeDataProvider;
use Gracious\ExtraAttributes\Model\Resolver\Product\DataProvider\ProductAttributeDataProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderItemInterface;

class ExtraAttributesResolver implements ResolverInterface
{
    protected ProductAttributeDataProvider $productDataProvider;
    protected OrderItemAttributeDataProvider $orderItemDataProvider;

    public function __construct(
        ProductAttributeDataProvider $productDataProvider,
        OrderItemAttributeDataProvider $orderItemDataProvider
    ) {
        $this->orderItemDataProvider = $orderItemDataProvider;
        $this->productDataProvider = $productDataProvider;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        $model = $value['model'];

        if ($model instanceof ProductInterface::class) {
            $result = $this->productDataProvider->getAttributesBySku($model->getSku());
        } elseif ($model instanceof OrderItemInterface::class) {
            $result = $this->orderItemDataProvider->getAttributesBySku($model->getSku());
        } else {
            throw new LocalizedException(__('"model" should be instance of ProductInterface or OrderItemInterface'));
        }

        return array_filter(
            $result,
            static fn(array $attribute) => !empty($attribute['value']),
        );
    }
}

