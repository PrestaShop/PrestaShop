<?php

namespace PrestaShopBundle\Form\Admin\ShopParameters\ProductPreferences;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class generates "Pagination" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class PaginationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products_per_page', IntegerType::class)
            ->add('default_order_by', ChoiceType::class, [
                'choices' => [
                    'Product name' => 0,
                    'Product price' => 1,
                    'Product add date' => 2,
                    'Product modified date' => 3,
                    'Position inside category' => 4,
                    'Brand' => 5,
                    'Product quantity' => 6,
                    'Product reference' => 7,
                ],
                'required' => true,
            ])
            ->add('default_order_way', ChoiceType::class, [
                'choices' => [
                    'Ascending' => 0,
                    'Descending' => 1,
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
        return 'product_preferences_pagination_block';
    }
}
