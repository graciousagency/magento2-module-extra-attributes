<?php

namespace Gracious\ExtraAttributes\Plugin\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Registry;


class Front
{
    protected Yesno $yesno;
    protected PropertyLocker $propertyLocker;
    protected Registry $registry;

    public function __construct(
        Yesno $yesno,
        PropertyLocker $propertyLocker,
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->propertyLocker = $propertyLocker;
        $this->yesno = $yesno;
    }

    public function aroundGetFormHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front $subject,
        \Closure $proceed
    ) {
        $options = $this->yesno->toOptionArray();
        $form = $subject->getForm();
        $fieldset = $form->getElement('front_fieldset');

        if ($fieldset) {
            $fieldset->addField(
                'show_in_order_item',
                'select',
                [
                    'name' => 'show_in_order_item',
                    'label' => __('Visible on Order Item In GraphQL'),
                    'title' => __('Visible on Order Item In GraphQL'),
                    'values' => $options,
                ]
            );
        }

        $attributeObject = $this->registry->registry('entity_attribute');
        $form->setValues($attributeObject->getData());
        $this->propertyLocker->lock($form);

        return $proceed();
    }
}
