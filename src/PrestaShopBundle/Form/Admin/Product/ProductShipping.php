<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
        $builder->add('width', FormType\NumberType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Width', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('height', FormType\NumberType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Height', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('depth', FormType\NumberType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Depth', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('weight', FormType\NumberType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Weight', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('additional_shipping_cost', FormType\MoneyType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Shipping fees', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('selectedCarriers', FormType\ChoiceType::class, array(
            'choices' =>  $this->carriersChoices,
            'choices_as_values' => true,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Available carriers', [], 'AdminProducts')
        ));

        foreach ($this->warehouses as $warehouse) {
            $builder->add('warehouse_combination_'.$warehouse['id_warehouse'], FormType\CollectionType::class, array(
                'entry_type' => \PrestaShopBundle\Form\Admin\Product\ProductWarehouseCombination::class,
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
