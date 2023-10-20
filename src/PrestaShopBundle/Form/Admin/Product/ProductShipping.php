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

namespace PrestaShopBundle\Form\Admin\Product;

use Currency;
use PrestaShop\PrestaShop\Adapter\Carrier\CarrierDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Warehouse\WarehouseDataProvider;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * This form class is responsible to generate the product shipping form.
 */
class ProductShipping extends CommonAbstractType
{
    /**
     * @var array<string, string>
     */
    private $carriersChoices;
    /**
     * @var Currency
     */
    public $currency;
    /**
     * @var LegacyContext
     */
    public $legacyContext;
    /**
     * @var array<int|array>
     */
    public $locales;
    /**
     * @var TranslatorInterface
     */
    public $translator;
    /**
     * @var array
     */
    private $warehouses;
    /**
     * @var string
     */
    private $dimensionUnit;
    /**
     * @var string
     */
    private $weightUnit;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     * @param LegacyContext $legacyContext
     * @param WarehouseDataProvider $warehouseDataProvider
     * @param CarrierDataProvider $carrierDataProvider
     * @param string $dimensionUnit
     * @param string $weightUnit
     */
    public function __construct($translator, $legacyContext, $warehouseDataProvider, $carrierDataProvider, string $dimensionUnit, string $weightUnit)
    {
        $this->translator = $translator;
        $this->legacyContext = $legacyContext;
        $this->currency = $legacyContext->getContext()->currency;
        $this->locales = $this->legacyContext->getLanguages();
        $this->warehouses = $warehouseDataProvider->getWarehouses();

        $carriers = $carrierDataProvider->getCarriers(
            $this->locales[0]['id_lang'],
            false,
            false,
            false,
            null,
            $carrierDataProvider->getAllCarriersConstant()
        );
        $this->carriersChoices = [];
        foreach ($carriers as $carrier) {
            $choiceId = $carrier['id_carrier'] . ' - ' . $carrier['name'];
            if (!empty($carrier['delay'])) {
                $choiceId .= ' (' . $carrier['delay'] . ')';
            }

            $this->carriersChoices[$choiceId] = $carrier['id_reference'];
        }
        $this->dimensionUnit = $dimensionUnit;
        $this->weightUnit = $weightUnit;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'width',
            FormType\NumberType::class,
            [
                'unit' => $this->dimensionUnit,
                'required' => false,
                'label' => $this->translator->trans('Width', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                ],
            ]
        )
            ->add(
                'height',
                FormType\NumberType::class,
                [
                    'unit' => $this->dimensionUnit,
                    'required' => false,
                    'label' => $this->translator->trans('Height', [], 'Admin.Catalog.Feature'),
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'numeric']),
                    ],
                ]
            )
            ->add(
                'depth',
                FormType\NumberType::class,
                [
                    'unit' => $this->dimensionUnit,
                    'required' => false,
                    'label' => $this->translator->trans('Depth', [], 'Admin.Catalog.Feature'),
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'numeric']),
                    ],
                ]
            )
            ->add(
                'weight',
                FormType\NumberType::class,
                [
                    'unit' => $this->weightUnit,
                    'scale' => static::PRESTASHOP_WEIGHT_DECIMALS,
                    'required' => false,
                    'label' => $this->translator->trans('Weight', [], 'Admin.Catalog.Feature'),
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'numeric']),
                    ],
                ]
            )
            ->add(
                'additional_shipping_cost',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Shipping fees', [], 'Admin.Catalog.Feature'),
                    'currency' => $this->currency->iso_code,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'float']),
                    ],
                ]
            )
            ->add(
                'selectedCarriers',
                FormType\ChoiceType::class,
                [
                    'choices' => $this->carriersChoices,
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                    'label' => $this->translator->trans('Available carriers', [], 'Admin.Catalog.Feature'),
                ]
            )
            ->add(
                'additional_delivery_times',
                FormType\ChoiceType::class,
                [
                    'choices' => [
                        $this->translator->trans('None', [], 'Admin.Catalog.Feature') => 0,
                        $this->translator->trans('Default delivery time', [], 'Admin.Catalog.Feature') => 1,
                        $this->translator->trans('Specific delivery time for this product', [], 'Admin.Catalog.Feature') => 2,
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'required' => false,
                    'placeholder' => null,
                    'preferred_choices' => ['default'],
                    'label' => $this->translator->trans('Delivery time', [], 'Admin.Catalog.Feature'),
                ]
            )
            ->add(
                'delivery_out_stock',
                TranslateType::class,
                [
                    'type' => FormType\TextType::class,
                    'options' => [
                        'attr' => [
                            'placeholder' => $this->translator->trans('Delivered within 5-7 days', [], 'Admin.Catalog.Feature'),
                        ],
                    ],
                    'locales' => $this->locales,
                    'hideTabs' => true,
                    'required' => false,
                    'label' => $this->translator->trans(
                        'Delivery time of out-of-stock products with allowed orders:',
                        [],
                        'Admin.Catalog.Feature'
                    ),
                ]
            )
            ->add(
                'delivery_in_stock',
                TranslateType::class,
                [
                    'type' => FormType\TextType::class,
                    'options' => [
                        'attr' => [
                            'placeholder' => $this->translator->trans('Delivered within 3-4 days', [], 'Admin.Catalog.Feature'),
                        ],
                    ],
                    'locales' => $this->locales,
                    'hideTabs' => true,
                    'required' => false,
                    'label' => $this->translator->trans('Delivery time of in-stock products:', [], 'Admin.Catalog.Feature'),
                ]
            );

        foreach ($this->warehouses as $warehouse) {
            $builder->add(
                'warehouse_combination_' . $warehouse['id_warehouse'],
                CollectionType::class,
                [
                    'entry_type' => 'PrestaShopBundle\Form\Admin\Product\ProductWarehouseCombination',
                    'entry_options' => [
                        'id_warehouse' => $warehouse['id_warehouse'],
                    ],
                    'prototype' => true,
                    'allow_add' => true,
                    'required' => false,
                    'label' => $warehouse['name'],
                ]
            );
        }
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_shipping';
    }
}
