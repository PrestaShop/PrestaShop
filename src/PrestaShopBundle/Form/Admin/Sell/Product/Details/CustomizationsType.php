<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
