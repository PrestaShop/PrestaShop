<?php

namespace PrestaShopBundle\Form\Admin\ShopParameters\ProductPreferences;

use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class generates "Products stock" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class StockType extends TranslatorAwareType
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
            ->add('in_stock_label', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('oos_allowed_backorders', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('oos_denied_backorders', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('delivery_time', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('oos_delivery_time', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('pack_stock_management', ChoiceType::class, [
                'choices' => [
                      'Decrement pack only' => 0,
                      'Decrement products in pack only' => 1,
                      'Decrement both' => 2,
                ],
            ])
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
