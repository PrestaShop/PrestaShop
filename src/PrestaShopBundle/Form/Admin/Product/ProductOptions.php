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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the product options form
 */
class ProductOptions extends CommonAbstractType
{
    private $translator;
    private $suppliers;
    private $context;
    private $productAdapter;
    private $router;
    private $locales;
    private $currencyDataprovider;
    private $fullAttachmentList;
    private $attachmentList;

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
     */
    public function __construct($translator, $legacyContext, $productDataProvider, $supplierDataProvider, $currencyDataprovider, $attachmentDataprovider, $router)
    {
        $this->context = $legacyContext;
        $this->translator = $translator;
        $this->productAdapter = $productDataProvider;
        $this->currencyDataprovider = $currencyDataprovider;
        $this->locales = $legacyContext->getLanguages();
        $this->router = $router;

        $this->suppliers = $this->formatDataChoicesList(
            $supplierDataProvider->getSuppliers(),
            'id_supplier'
        );

        $this->fullAttachmentList = $attachmentDataprovider->getAllAttachments($this->context->getLanguages()[0]['id_lang']);
        $this->attachmentList = $this->formatDataChoicesList(
            $this->fullAttachmentList,
            'id_attachment',
            'file'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('visibility', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices'  => array(
                $this->translator->trans('Everywhere', [], 'Admin.Catalog.Feature') => 'both',
                $this->translator->trans('Catalog only', [], 'Admin.Catalog.Feature') => 'catalog',
                $this->translator->trans('Search only', [], 'Admin.Catalog.Feature') => 'search',
                $this->translator->trans('Nowhere', [], 'Admin.Catalog.Feature') => 'none',
            ),
            'choices_as_values' => true,
            'required' => true,
            'label' => $this->translator->trans('Visibility', [], 'Admin.Catalog.Feature'),
        ))
        ->add('tags', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'options' => [
                'attr' => [
                    'class' => 'tokenfield',
                    'placeholder' => $this->translator->trans('Use a comma to create seperate tags. E.g.: dress, cotton, party dresses.', [], 'Admin.Catalog.Help')
                ]
            ],
            'locales' => $this->locales,
            'label' => $this->translator->trans('Tags', [], 'Admin.Catalog.Feature')
        ))
        ->add(
            $builder->create('display_options', 'Symfony\Component\Form\Extension\Core\Type\FormType', array('required' => false, 'label' => $this->translator->trans('Display options', [], 'Admin.Catalog.Feature')))
                ->add('available_for_order', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                    'label'    => $this->translator->trans('Available for order', [], 'Admin.Catalog.Feature'),
                    'required' => false,
                ))
                ->add('show_price', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                    'label'    => $this->translator->trans('Show price', [], 'Admin.Catalog.Feature'),
                    'required' => false,
                ))
                ->add('online_only', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                    'label'    => $this->translator->trans('Web only (not sold in your retail store)', [], 'Admin.Catalog.Feature'),
                    'required' => false,
                ))
        )
        ->add('upc', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('UPC barcode', [], 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,12}$/"),
            )
        ))
        ->add('ean13', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'error_bubbling' => true,
            'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,13}$/"),
            )
        ))
        ->add('isbn', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('ISBN', [], 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\Regex("/^[0-9-]{0,32}$/"),
            ),
        ))
        ->add('reference', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
            'label' => $this->translator->trans('Reference', [], 'Admin.Global')
        ))
        ->add('show_condition', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'required' => false,
            'label' => $this->translator->trans('Display condition on product page', [], 'Admin.Catalog.Feature'),
        ))
        ->add('condition', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices'  => array(
                 $this->translator->trans('New', [], 'Shop.Theme.Catalog') => 'new',
                 $this->translator->trans('Used', [], 'Shop.Theme.Catalog') => 'used',
                 $this->translator->trans('Refurbished', [], 'Shop.Theme.Catalog') => 'refurbished'
            ),
            'choices_as_values' => true,
            'required' => true,
            'label' => $this->translator->trans('Condition', [], 'Admin.Catalog.Feature')
        ))
        ->add('suppliers', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->suppliers,
            'choices_as_values' => true,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Suppliers', [], 'Admin.Global')
        ))
        ->add('default_supplier', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->suppliers,
            'choices_as_values' => true,
            'expanded' =>  true,
            'multiple' =>  false,
            'required' =>  true,
            'label' => $this->translator->trans('Default suppliers', [], 'Admin.Catalog.Feature')
        ));

        foreach ($this->suppliers as $supplier => $id) {
            $builder->add('supplier_combination_'.$id, 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' =>'PrestaShopBundle\Form\Admin\Product\ProductSupplierCombination',
                'entry_options'  => array(
                    'id_supplier' => $id,
                ),
                'prototype' => true,
                'allow_add' => true,
                'required' => false,
                'label' => $supplier,
            ));
        }

        $builder->add('custom_fields', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
            'entry_type' =>'PrestaShopBundle\Form\Admin\Product\ProductCustomField',
            'label' => $this->translator->trans('Customization', [], 'Admin.Catalog.Feature'),
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true
        ));

        //Add product attachment form
        $builder->add('attachment_product', 'PrestaShopBundle\Form\Admin\Product\ProductAttachement', array(
            'required' => false,
            'label' => $this->translator->trans('Attachment', [], 'Admin.Catalog.Feature'),
            'attr' => ['data-action' => $this->router->generate('admin_product_attachement_add_action', array('idProduct' => 1))]
        ));

        //Add attachment selectors
        $builder->add('attachments', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'expanded'  => true,
            'multiple'  => true,
            'choices'  => $this->attachmentList,
            'choices_as_values' => true,
            'choice_label' => function ($choice, $key, $value) {
                $attachmentKey = array_search($key, array_column($this->fullAttachmentList, 'file'));
                return $this->fullAttachmentList[$attachmentKey]['name'];
            },
            'required' => false,
            'attr' => ['data' => $this->fullAttachmentList],
            'label' => $this->translator->trans('Attachments for this product:', [], 'Admin.Catalog.Feature')
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            //If not supplier selected, remove all supplier combinations collection form
            if (!isset($data['suppliers']) || count($data['suppliers']) == 0) {
                $form = $event->getForm();
                foreach ($this->suppliers as $supplier => $id) {
                    $form->remove('supplier_combination_'.$id);
                }
            }
        });
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_options';
    }
}
