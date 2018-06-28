<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Status;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'with_message' => false,
                'filter_type' => TextType::class,
                'filter_type_options' => [
                    'required' => false,
                ],
            ])
            ->setAllowedTypes('with_message', 'bool')
        ;
    }
}
