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

use PrestaShopBundle\Form\Admin\Type\CommonModelAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the product shipping form
 */
class ProductShipping extends CommonModelAbstractType
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
        $this->locales = $this->legacyContext->getLanguages();
        $this->warehouses = $warehouseDataProvider->getWarehouses();

        $carriers = $carrierDataProvider->getCarriers($this->locales[0]['id_lang'], false, false, false, null, $carrierDataProvider->getAllCarriersConstant());
        $this->carriersChoices = [];
        foreach ($carriers as $carrier) {
            $this->carriersChoices[$carrier['id_reference']] = $carrier['name'].' ('.$carrier['delay'].')';
        }
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('width', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package width', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('height', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package height', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('depth', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package depth', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('weight', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package weight', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('additional_shipping_cost', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Additional shipping fees (for a single item)', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('selectedCarriers', 'choice', array(
            'choices' =>  $this->carriersChoices,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Carriers', [], 'AdminProducts')
        ));

        foreach ($this->warehouses as $warehouse) {
            $builder->add('warehouse_combination_'.$warehouse['id_warehouse'], 'collection', array(
                'type' => new ProductWarehouseCombination($warehouse['id_warehouse'], $this->translator, $this->legacyContext),
                'prototype' => true,
                'allow_add' => true,
                'required' => false,
                'label' => $warehouse['name'],
            ));
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_shipping';
    }
}
