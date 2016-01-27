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
        $this->router = $router;

        $this->suppliers = $this->formatDataChoicesList(
            $supplierDataProvider->getSuppliers(),
            'id_supplier'
        );

        $this->attachmentList = $this->formatDataChoicesList(
            $attachmentDataprovider->getAllAttachments($this->context->getLanguages()[0]['id_lang']),
            'id_attachment'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('visibility', FormType\ChoiceType::class, array(
            'choices'  => array(
                $this->translator->trans('Everywhere', [], 'AdminProducts') => 'both',
                $this->translator->trans('Catalog only', [], 'AdminProducts') => 'catalog',
                $this->translator->trans('Search only', [], 'AdminProducts') => 'search',
                $this->translator->trans('Nowhere', [], 'AdminProducts') => 'none',
            ),
            'choices_as_values' => true,
            'required' => true,
            'label' => $this->translator->trans('Visibility', [], 'AdminProducts'),
        ))
        ->add(
            $builder->create('display_options', FormType\FormType::class, array('required' => false, 'label' => $this->translator->trans('Display options', [], 'AdminProducts')))
                ->add('available_for_order', FormType\CheckboxType::class, array(
                    'label'    => $this->translator->trans('Available for order', [], 'AdminProducts'),
                    'required' => false,
                ))
                ->add('show_price', FormType\CheckboxType::class, array(
                    'label'    => $this->translator->trans('Show price', [], 'AdminProducts'),
                    'required' => false,
                ))
                ->add('online_only', FormType\CheckboxType::class, array(
                    'label'    => $this->translator->trans('Online only (not sold in your retail store)', [], 'AdminProducts'),
                    'required' => false,
                ))
        )
        ->add('upc', FormType\TextType::class, array(
            'required' => false,
            'label' => $this->translator->trans('UPC barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,12}$/"),
            )
        ))
        ->add('ean13', FormType\TextType::class, array(
            'required' => false,
            'error_bubbling' => true,
            'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\Regex("/^[0-9]{0,13}$/"),
            )
        ))
        ->add('isbn', FormType\TextType::class, array(
            'required' => false,
            'label' => $this->translator->trans('ISBN code', [], 'AdminProducts')
        ))
        ->add('reference', FormType\TextType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Reference code', [], 'AdminProducts')
        ))
        ->add('show_condition', FormType\CheckboxType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Display condition', [], 'AdminProducts'),
        ))
        ->add('condition', FormType\ChoiceType::class, array(
            'choices'  => array(
                 $this->translator->trans('New', [], 'AdminProducts') => 'new',
                 $this->translator->trans('Used', [], 'AdminProducts') => 'used',
                 $this->translator->trans('Refurbished', [], 'AdminProducts') => 'refurbished'
            ),
            'choices_as_values' => true,
            'required' => true,
            'label' => $this->translator->trans('Condition', [], 'AdminProducts')
        ))
        ->add('suppliers', FormType\ChoiceType::class, array(
            'choices' =>  $this->suppliers,
            'choices_as_values' => true,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Suppliers', [], 'AdminProducts')
        ))
        ->add('default_supplier', FormType\ChoiceType::class, array(
            'choices' =>  $this->suppliers,
            'choices_as_values' => true,
            'required' =>  true,
            'label' => $this->translator->trans('Default suppliers', [], 'AdminProducts')
        ));

        foreach ($this->suppliers as $supplier => $id) {
            $builder->add('supplier_combination_'.$id, FormType\CollectionType::class, array(
                'entry_type' => \PrestaShopBundle\Form\Admin\Product\ProductSupplierCombination::class,
                'entry_options'  => array(
                    'id_supplier' => $id,
                ),
                'prototype' => true,
                'allow_add' => true,
                'required' => false,
                'label' => $supplier,
            ));
        }

        $builder->add('custom_fields', FormType\CollectionType::class, array(
            'entry_type' => \PrestaShopBundle\Form\Admin\Product\ProductCustomField::class,
            'label' => $this->translator->trans('Customization', [], 'AdminProducts'),
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true
        ));

        //Add product attachment form
        $builder->add('attachment_product', \PrestaShopBundle\Form\Admin\Product\ProductAttachement::class, array(
            'required' => false,
            'label' => $this->translator->trans('Attachment', [], 'AdminProducts'),
            'attr' => ['data-action' => $this->router->generate('admin_product_attachement_add_action')]
        ));

        //Add attachment selectors
        $builder->add('attachments', FormType\ChoiceType::class, array(
            'expanded'  => false,
            'multiple'  => true,
            'choices'  => $this->attachmentList,
            'choices_as_values' => true,
            'required' => false,
            'label' => $this->translator->trans('Attachments for this product:', [], 'AdminProducts'),
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            //If not supplier selected, remove all supplier combinations collection form
            if (!isset($data['suppliers']) || count($data['suppliers']) == 0) {
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
