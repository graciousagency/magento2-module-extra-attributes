<?php

namespace Gracious\ExtraAttributes\Plugin\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Registry;


class Front
{
    public function __construct(
        protected readonly Yesno $yesno,
        protected readonly PropertyLocker $propertyLocker,
        protected readonly Registry $registry,
    ) {
    }

    public function aroundGetFormHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front $subject,
        \Closure $proceed
    ) {
        $options = $this->yesno->toOptionArray();
        $form = $subject->getForm();
        $fieldset = $form->getElement('front_fieldset');
        $fieldset?->addField(
            'show_in_order_item',
            'select',
            [
                'name' => 'show_in_order_item',
                'label' => __('Visible on Order Item In GraphQL'),
                'title' => __('Visible on Order Item In GraphQL'),
                'values' => $options,
            ]
        );
        $attributeObject = $this->registry->registry('entity_attribute');
        $form->setValues($attributeObject->getData());
        $this->propertyLocker->lock($form);

        return $proceed();
    }
}
