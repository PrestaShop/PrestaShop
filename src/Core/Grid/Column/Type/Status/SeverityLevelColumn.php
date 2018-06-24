<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Status;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeverityLevelColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'severity_level';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'with_message' => false,
        ]);

        $resolver->setAllowedTypes('with_message', 'bool');
    }
}
