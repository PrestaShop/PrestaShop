<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the product shipping form
 */
class ProductShipping extends CommonAbstractType
{
    private $translator;
    private $carriersChoices;
    private $warehouses;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     * @param object $warehouseDataProvider
     * @param object $carrierDataProvider
     */
    public function __construct($translator, $legacyContext, $warehouseDataProvider, $carrierDataProvider)
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
            $this->carriersChoices[$carrier['name'].' ('.$carrier['delay'].')'] = $carrier['id_reference'];
        }
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
            array(
                'required' => false,
                'label' => $this->translator->trans('Width', [], 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'numeric'))
                )
            )
        )
        ->add(
            'height',
            FormType\NumberType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('Height', [], 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'numeric'))
                )
            )
        )
        ->add(
            'depth',
            FormType\NumberType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('Depth', [], 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'numeric'))
                )
            )
        )
        ->add(
            'weight',
            FormType\NumberType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('Weight', [], 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'numeric'))
                )
            )
        )
        ->add(
            'additional_shipping_cost',
            FormType\MoneyType::class,
            array(
                'required' => false,
                'label' => $this->translator->trans('Shipping fees', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'float'))
                )
            )
        )
        ->add(
            'selectedCarriers',
            FormType\ChoiceType::class,
            array(
                'choices' =>  $this->carriersChoices,
                'expanded' =>  true,
                'multiple' =>  true,
                'required' =>  false,
                'label' => $this->translator->trans('Available carriers', [], 'Admin.Catalog.Feature')
            )
        )
        ->add(
            'additional_delivery_times',
            FormType\ChoiceType::class,
            array(
                'choices' =>  array(
                    $this->translator->trans('None', [], 'Admin.Catalog.Feature') => 0,
                    $this->translator->trans('Default delivery time', [], 'Admin.Catalog.Feature') => 1,
                    $this->translator->trans('Specific delivery time to this product', [], 'Admin.Catalog.Feature') => 2,
                ),
                'expanded' =>  true,
                'multiple' =>  false,
                'required' =>  false,
                'placeholder' => null,
                'preferred_choices' => array('default'),
                'label' => $this->translator->trans('Delivery Time', [], 'Admin.Catalog.Feature'),
            )
        )
        ->add(
            'delivery_out_stock',
            TranslateType::class,
            array(
                'type' => FormType\TextType::class,
                'options' => array(
                    'attr' => array(
                        'placeholder' => $this->translator->trans('Delivered within 5-7 days', [], 'Admin.Catalog.Feature'),
                    )
                ),
                'locales' => $this->locales,
                'hideTabs' => true,
                'required' => false,
                'label' => $this->translator->trans(
                    'Delivery time of out-of-stock products with allowed orders:',
                    [],
                    'Admin.Catalog.Feature'
                ),
            )
        )
        ->add(
            'delivery_in_stock',
            TranslateType::class,
            array(
                'type' => FormType\TextType::class,
                'options' => array(
                    'attr' => array(
                        'placeholder' => $this->translator->trans('Delivered within 3-4 days', [], 'Admin.Catalog.Feature'),
                    )
                ),
                'locales' => $this->locales,
                'hideTabs' => true,
                'required' => false,
                'label' => $this->translator->trans('Delivery time of in-stock products:', [], 'Admin.Catalog.Feature'),
            )
        );


        foreach ($this->warehouses as $warehouse) {
            $builder->add(
                'warehouse_combination_'.$warehouse['id_warehouse'],
                CollectionType::class,
                [
                    'entry_type' =>'PrestaShopBundle\Form\Admin\Product\ProductWarehouseCombination',
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
