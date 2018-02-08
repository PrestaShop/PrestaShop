<?php

namespace PrestaShopBundle\Form\Admin\ShopParameters\ProductPreferences;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('allow_ordering_oos', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'required' => true,
            ])
            ->add('stock_management', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'required' => true,
            ])
            ->add('in_stock_label', TextType::class)
            ->add('oos_label_with_backorders', TextType::class)
            ->add('oos_label_without_backorders', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'product_preferences_stock_block';
    }
}
