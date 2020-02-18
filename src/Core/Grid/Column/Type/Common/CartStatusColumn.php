<?php


namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;


use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartStatusColumn extends AbstractColumn
{

    /**
     * @inheritDoc
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
                'empty_value' => '',
                'clickable' => true,
                'text_align' => 'right',
            ])
            ->setAllowedTypes('field', 'string');
    }
}
