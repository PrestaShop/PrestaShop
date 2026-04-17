<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShopBundle\Form\Admin\Sell\Product\Options\CustomizationFieldType;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomizationsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customization_fields', CollectionType::class, [
                'entry_type' => CustomizationFieldType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__CUSTOMIZATION_FIELD_INDEX__',
            ])
            ->add('add_customization_field', IconButtonType::class, [
                'label' => $this->trans('Add a customization field', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle',
                'attr' => [
                    'class' => 'btn-outline-secondary add-customization-btn',
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Customization', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'label_subtitle' => $this->trans('Customers can personalize the product by entering some text or by providing custom image files.', 'Admin.Catalog.Feature'),
            'attr' => [
                'class' => 'product-customizations-collection',
            ],
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/customizations.html.twig',
        ]);
    }
}
