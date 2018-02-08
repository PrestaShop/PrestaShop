<?php

namespace PrestaShopBundle\Form\Admin\ShopParameters\ProductPreferences;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('display_quantities', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
               'required' => true,
           ])
           ->add('display_last_quantities', IntegerType::class)
           ->add('display_unavailable_attributes', ChoiceType::class, [
               'choices' => [
                   'Yes' => 1,
                   'No' => 0,
               ],
               'required' => true,
           ])
           ->add('allow_add_variant_to_cart_from_listing', ChoiceType::class, [
               'choices' => [
                   'Yes' => 1,
                   'No' => 0,
               ],
               'required' => true,
           ])
           ->add('attribute_anchor_separator', ChoiceType::class, [
               'choices' => [
                   '-' => '-',
                   ',' => ',',
               ],
               'required' => true,
           ])
           ->add('display_discount_price', ChoiceType::class, [
               'choices' => [
                   'Yes' => 1,
                   'No' => 0,
               ],
               'required' => true,
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
        return 'product_preferences_page_block';
    }
}
