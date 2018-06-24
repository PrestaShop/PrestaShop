<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Employee;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EmployeeNameWithAvatarColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'employee_name_with_avatar';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'filter_type' => TextType::class,
            'filter_type_options' => [
                'required' => false,
            ],
            'sortable' => true,
        ]);

        $resolver->setAllowedTypes('filter_type', 'string');
        $resolver->setAllowedTypes('filter_type_options', 'array');
        $resolver->setAllowedTypes('sortable', 'bool');
    }
}
