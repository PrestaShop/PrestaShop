<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

        $carriers = $carrierDataProvider->getCarriers($this->locales[0]['id_lang'], false, false, false, null, $carrierDataProvider->getAllCarriersConstant());
        $this->carriersChoices = array();
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
        $builder->add('width', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Width', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric'))
            )
        ))
        ->add('height', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Height', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric'))
            )
        ))
        ->add('depth', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Depth', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric'))
            )
        ))
        ->add('weight', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Weight', array(), 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric'))
            )
        ))
        ->add('additional_shipping_cost', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Shipping fees', array(), 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('selectedCarriers', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->carriersChoices,
            'choices_as_values' => true,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Available carriers', array(), 'Admin.Catalog.Feature')
        ))
        ->add('additional_delivery_times', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  array(
                '0' => $this->translator->trans('None', array(), 'Admin.Catalog.Feature'),
                '1'   => $this->translator->trans('Default delivery time', array(), 'Admin.Catalog.Feature'),
                '2'   => $this->translator->trans('Specific delivery time to this product', array(), 'Admin.Catalog.Feature'),
            ),
            'expanded' =>  true,
            'multiple' =>  false,
            'required' =>  false,
            'empty_value' => false,
            'preferred_choices' => array('default'),
            'label' => $this->translator->trans('Delivery Time', array(), 'Admin.Catalog.Feature')
        ))
        ->add('delivery_out_stock', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'options' => array(
                'attr' => array(
                    'placeholder' => $this->translator->trans('Delivered within 5-7 days', array(), 'Admin.Catalog.Feature'),
                )
            ),
            'locales' => $this->locales,
            'hideTabs' => true,
            'required' => false,
            'label' => $this->translator->trans(
                'Delivery time of out-of-stock products with allowed orders:',
                array(),
                'Admin.Catalog.Feature'
            ),
        ))
        ->add('delivery_in_stock', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'options' => array(
                'attr' => array(
                    'placeholder' => $this->translator->trans('Delivered within 3-4 days', array(), 'Admin.Catalog.Feature'),
                 )
            ),
            'locales' => $this->locales,
            'hideTabs' => true,
            'required' => false,
            'label' => $this->translator->trans('Delivery time of in-stock products:', array(), 'Admin.Catalog.Feature'),
        ));


        foreach ($this->warehouses as $warehouse) {
            $builder->add('warehouse_combination_'.$warehouse['id_warehouse'], 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' =>'PrestaShopBundle\Form\Admin\Product\ProductWarehouseCombination',
                'entry_options' => array(
                    'id_warehouse' => $warehouse['id_warehouse'],
                ),
                'prototype' => true,
                'allow_add' => true,
                'required' => false,
                'label' => $warehouse['name'],
            ));
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
