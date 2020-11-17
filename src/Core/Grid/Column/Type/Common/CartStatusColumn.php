<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CartStatusColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cart_status';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired([
                'field',
            ])
            ->setDefaults([
                'badge_type' => 'success',
                'empty_value' => '',
                'clickable' => true,
                'text_align' => 'right',
            ])
            ->setAllowedTypes('field', 'string');
    }
}
