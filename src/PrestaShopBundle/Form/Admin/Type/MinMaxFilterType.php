<?php

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type defines to numeric input fields range from minimum to maximum for grid actions.
 */
class MinMaxFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'min_field_options' => [],
            'max_field_options' => [],
        ]);

        $resolver->setRequired([
            'min_field',
            'max_field'
        ]);

        $resolver->setAllowedTypes('min_field', 'string');
        $resolver->setAllowedTypes('max_field', 'string');
        $resolver->setAllowedTypes('min_field_options', 'array');
        $resolver->setAllowedTypes('max_field_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('min_field', $options['min_field'], $options['min_field_options']);
        $builder->add('max_field', $options['max_field'], $options['max_field_options']);
    }
}
