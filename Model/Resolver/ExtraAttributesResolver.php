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
    public function __construct(
        protected readonly ProductAttributeDataProvider $productDataProvider,
        protected readonly OrderItemAttributeDataProvider $orderItemDataProvider
    ) {
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
        $result = match(true) {
            $model instanceof ProductInterface => $this->productDataProvider->getAttributesBySku($model->getSku()),
            $model instanceof OrderItemInterface => $this->orderItemDataProvider->getAttributesBySku($model->getSku()),
            default => throw new LocalizedException(__('"model" should be instance of ProductInterface or OrderItemInterface')),
        };

        return array_filter(
            $result,
            static fn(array $attribute) => !empty($attribute['value']),
        );
    }
}

