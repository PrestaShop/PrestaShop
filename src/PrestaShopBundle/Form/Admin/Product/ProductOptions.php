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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Form\Admin\Product\ProductSupplierCombination;

/**
 * This form class is responsible to generate the product options form
 */
class ProductOptions extends CommonModelAbstractType
{
    private $translator;
    private $suppliers;
    private $container;
    private $context;
    private $productAdapter;

    /**
     * Constructor
     *
     * @param object $container The SF2 container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->context = $container->get('prestashop.adapter.legacy.context');
        $this->translator = $container->get('prestashop.adapter.translator');
        $this->productAdapter = $container->get('prestashop.adapter.data_provider.product');
        $this->suppliers = $this->formatDataChoicesList(
            $container->get('prestashop.adapter.data_provider.supplier')->getSuppliers(),
            'id_supplier'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('redirect_type', 'choice', array(
            'choices'  => array(
                '404' => $this->translator->trans('No redirect (404)', [], 'AdminProducts'),
                '301' => $this->translator->trans('Catalog Redirected permanently (301)', [], 'AdminProducts'),
                '302' => $this->translator->trans('Redirected temporarily (302)', [], 'AdminProducts'),
            ),
            'required' => true,
            'label' => $this->translator->trans('Redirect when disabled', [], 'AdminProducts'),
        ))
        ->add('id_product_redirected', new TypeaheadProductCollectionType(
            $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
            'id',
            'name',
            $this->translator->trans('search in catalog...', [], 'AdminProducts'),
            '',
            $this->productAdapter,
            1
        ), array(
            'required' => false,
            'label' => $this->translator->trans('Related product:', [], 'AdminProducts')
        ))
        ->add('visibility', 'choice', array(
            'choices'  => array(
                'both' => $this->translator->trans('Everywhere', [], 'AdminProducts'),
                'catalog' => $this->translator->trans('Catalog only', [], 'AdminProducts'),
                'search' => $this->translator->trans('Search only', [], 'AdminProducts'),
                'none' => $this->translator->trans('Nowhere', [], 'AdminProducts'),
            ),
            'required' => true,
            'label' => $this->translator->trans('Visibility', [], 'AdminProducts'),
        ))
        ->add(
            $builder->create('display_options', 'form', array('required' => false, 'label' => $this->translator->trans('Display options', [], 'AdminProducts')))
                ->add('available_for_order', 'checkbox', array(
                    'label'    => $this->translator->trans('Available for order', [], 'AdminProducts'),
                    'required' => false,
                ))
                ->add('show_price', 'checkbox', array(
                    'label'    => $this->translator->trans('Show price', [], 'AdminProducts'),
                    'required' => false,
                ))
                ->add('online_only', 'checkbox', array(
                    'label'    => $this->translator->trans('Online only (not sold in your retail store)', [], 'AdminProducts'),
                    'required' => false,
                ))
        )
        ->add('suppliers', 'choice', array(
            'choices' =>  $this->suppliers,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Suppliers', [], 'AdminProducts')
        ))
        ->add('default_supplier', 'choice', array(
            'choices' =>  $this->suppliers,
            'required' =>  true,
            'label' => $this->translator->trans('Default suppliers', [], 'AdminProducts')
        ));

        foreach ($this->suppliers as $id => $supplier) {
            $builder->add('supplier_combination_'.$id, 'collection', array(
                'type' => new ProductSupplierCombination($id, $this->container),
                'prototype' => true,
                'allow_add' => true,
                'required' => false,
                'label' => $supplier,
            ));
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            //If not supplier selected, remove all supplier combinations collection form
            if (!isset($data['suppliers']) || count($data['suppliers']) == 0) {
                foreach ($this->suppliers as $id => $supplier) {
                    $form->remove('supplier_combination_'.$id);
                }
            }
        });
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_options';
    }
}
