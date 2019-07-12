<?php

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the number type two inputs of min and max value - designed to fit grid in grid filter.
 */
final class NumberMinMaxFilterType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'min_field_options' => [],
            'max_field_options' => [],
        ]);

        $resolver->setAllowedTypes('min_field_options', 'array');
        $resolver->setAllowedTypes('max_field_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['min_field_options']['attr']['placeholder'])) {
            $options['min_field_options']['attr']['placeholder'] = $this->trans('Min', [], 'Admin.Global');
        }

        if (!isset($options['max_field_options']['attr']['placeholder'])) {
            $options['max_field_options']['attr']['placeholder'] = $this->trans('Max', [], 'Admin.Global');
        }

        $builder->add('min_field', NumberType::class, $options['min_field_options']);
        $builder->add('max_field', NumberType::class, $options['max_field_options']);
    }
}
