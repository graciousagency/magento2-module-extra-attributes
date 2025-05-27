<?php

namespace Gracious\ExtraAttributes\Model\Resolver;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;


class AttributeMetaDataFactory
{
    public function getAttributeMetaDataStructure(Attribute $attribute, object $entity, int $storeId): array
    {
        return [
            'storefront_properties' => $this->getStorefrontProperties($attribute),
        ];
    }

    protected function getStorefrontProperties(Attribute $attribute): array
    {
        return [
            'position' => $attribute->getPosition(),
            'input_type' => $attribute->getFrontendInput(),
        ];
    }
}
