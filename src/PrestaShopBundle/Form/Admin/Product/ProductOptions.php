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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Form\Admin\Product\ProductAttachement;
use PrestaShopBundle\Form\Admin\Product\ProductCustomField;
use PrestaShopBundle\Form\Admin\Product\ProductSupplierCombination;

/**
 * This form class is responsible to generate the product options form
 */
class ProductOptions extends CommonAbstractType
{
    private $translator;
    private $suppliers;
    private $manufacturers;
    private $context;
    private $productAdapter;
    private $router;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     * @param object $productDataProvider
     * @param object $supplierDataProvider
     * @param object $currencyDataprovider
     * @param object $attachmentDataprovider
     * @param object $router
     * @param object $manufacturerDataProvider
     */
    public function __construct($translator, $legacyContext, $productDataProvider, $supplierDataProvider, $currencyDataprovider, $attachmentDataprovider, $router, $manufacturerDataProvider)
    {
        $this->context = $legacyContext;
        $this->translator = $translator;
        $this->productAdapter = $productDataProvider;
        $this->currencyDataprovider = $currencyDataprovider;
        $this->manufacturerDataProvider = $manufacturerDataProvider;
        $this->router = $router;

        $this->suppliers = $this->formatDataChoicesList(
            $supplierDataProvider->getSuppliers(),
            'id_supplier'
        );

        $this->attachmentList = $this->formatDataChoicesList(
            $attachmentDataprovider->getAllAttachments($this->context->getLanguages()[0]['id_lang']),
            'id_attachment'
        );

        $this->manufacturers = $this->formatDataChoicesList(
            $this->manufacturerDataProvider->getManufacturers(false, 0, true, false, false, false, true),
            'id_manufacturer'
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
            $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
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
        ->add('upc', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('UPC barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,12}$/"),
            )
        ))
        ->add('ean13', 'text', array(
            'required' => false,
            'error_bubbling' => true,
            'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,13}$/"),
            )
        ))
        ->add('isbn', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('ISBN code', [], 'AdminProducts')
        ))
        ->add('reference', 'text', array(
            'required' => false,
            'label' => $this->translator->trans('Reference code', [], 'AdminProducts')
        ))
        ->add('condition', 'choice', array(
            'choices'  => array(
                'new' => $this->translator->trans('New', [], 'AdminProducts'),
                'used' => $this->translator->trans('Used', [], 'AdminProducts'),
                'refurbished' => $this->translator->trans('Refurbished', [], 'AdminProducts')
            ),
            'required' => true,
            'label' => $this->translator->trans('Condition', [], 'AdminProducts')
        ))
        ->add('id_manufacturer', 'choice', array(
            'choices' => $this->manufacturers,
            'required' => false,
            'label' => $this->translator->trans('Manufacturer', [], 'AdminProducts')
        ))
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
                'type' => new ProductSupplierCombination($id, $this->translator, $this->context, $this->currencyDataprovider),
                'prototype' => true,
                'allow_add' => true,
                'required' => false,
                'label' => $supplier,
            ));
        }

        $builder->add('custom_fields', 'collection', array(
            'type' => new ProductCustomField(
                $this->translator,
                $this->context
            ),
            'label' => $this->translator->trans('Customization', [], 'AdminProducts'),
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true
        ));

        //Add product attachment form
        $builder->add('attachment_product', new ProductAttachement($this->translator, $this->context), array(
            'required' => false,
            'label' => $this->translator->trans('Attachment', [], 'AdminProducts'),
            'attr' => ['data-action' => $this->router->generate('admin_product_attachement_add_action')]
        ));

        //Add attachment selectors
        $builder->add('attachments', 'choice', array(
            'expanded'  => false,
            'multiple'  => true,
            'choices'  => $this->attachmentList,
            'required' => false,
            'label' => $this->translator->trans('Attachments for this product:', [], 'AdminProducts'),
        ));

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
