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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class SuppliersType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $supplierNameByIdChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $supplierNameByIdChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $supplierNameByIdChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->supplierNameByIdChoiceProvider = $supplierNameByIdChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $suppliers = $this->supplierNameByIdChoiceProvider->getChoices();

        $builder
            ->add('supplier_ids', ChoiceType::class, [
                'choices' => $suppliers,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'choice_attr' => function ($choice, $name) {
                    return ['data-label' => $name];
                },
                'label' => $this->trans('Choose the suppliers associated with this product', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h4',
            ])
            ->add('default_supplier_id', ChoiceType::class, [
                'choices' => $suppliers,
                'expanded' => true,
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'label' => $this->trans('Default supplier', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h4',
                'choice_attr' => [
                    'disabled' => true,
                ],
            ])
            ->add('product_suppliers', CollectionType::class, [
                'label' => $this->trans('Supplier reference(s)', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h4',
                'entry_type' => ProductSupplierType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__PRODUCT_SUPPLIER_INDEX__',
                'attr' => [
                    'class' => 'product-suppliers-collection',
                ],
                'alert_message' => $this->trans(
                    'You can specify product reference(s) for each associated supplier. Click "%save_label%" after changing selected suppliers to display the associated product references.',
                    'Admin.Catalog.Help',
                    [
                        '%save_label%' => $this->trans('Save', 'Admin.Actions'),
                    ]
                ),
                'alert_position' => 'prepend',
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
            'label' => $this->trans('Suppliers', 'Admin.Global'),
            'label_tag_name' => 'h2',
            'columns_number' => 2,
            'row_attr' => [
                'class' => 'product-suppliers-block',
            ],
            'alert_position' => 'prepend',
        ]);
    }
}
