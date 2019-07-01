<?php

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IntegerMinMaxFilterType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'min_field_options' => [
                'attr' => [
                    'min' => 0,
                    'step' => 1,
                    'placeholder' => $this->trans('Min', [], 'Admin.Global'),
                ],
            ],
            'max_field_options' => [
                'attr' => [
                    'min' => 0,
                    'step' => 1,
                    'placeholder' => $this->trans('Max', [], 'Admin.Global'),
                ],
            ],
        ]);

        $resolver->setAllowedTypes('min_field_options', 'array');
        $resolver->setAllowedTypes('max_field_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('min_field', IntegerType::class, $options['min_field_options']);
        $builder->add('max_field', IntegerType::class, $options['max_field_options']);
    }
}
