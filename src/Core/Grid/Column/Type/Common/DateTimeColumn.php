<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'date_time';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'format' => 'Y-m-d H:i:s',
                'filter_type' => DateRangeType::class,
            ])
            ->setAllowedTypes('format', 'string')
        ;
    }
}
