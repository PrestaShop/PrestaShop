<?php
/**
 * 2007-2018 PrestaShop.
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
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the product options form.
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
     * Constructor.
     *
     * @param object $translator
     * @param object $legacyContext
     * @param object $productDataProvider
     * @param object $supplierDataProvider
     * @param object $currencyDataprovider
     * @param object $attachmentDataprovider
     * @param object $router
     */
    public function __construct(
        $translator,
        $legacyContext,
        $productDataProvider,
        $supplierDataProvider,
        $currencyDataprovider,
        $attachmentDataprovider,
        $router
    ) {
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

        $this->fullAttachmentList = $attachmentDataprovider->getAllAttachments(
            $this->context->getLanguages()[0]['id_lang']
        );
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
        $builder->add('visibility', FormType\ChoiceType::class, array(
            'choices' => array(
                $this->translator->trans('Everywhere', array(), 'Admin.Catalog.Feature') => 'both',
                $this->translator->trans('Catalog only', array(), 'Admin.Catalog.Feature') => 'catalog',
                $this->translator->trans('Search only', array(), 'Admin.Catalog.Feature') => 'search',
                $this->translator->trans('Nowhere', array(), 'Admin.Catalog.Feature') => 'none',
            ),
            'attr' => array(
                'class' => 'custom-select',
            ),
            'required' => true,
            'label' => $this->translator->trans('Visibility', array(), 'Admin.Catalog.Feature'),
        ))
            ->add('tags', TranslateType::class, array(
                'type' => FormType\TextType::class,
                'options' => array(
                    'attr' => array(
                        'class' => 'tokenfield',
                        'placeholder' => $this->translator->trans('Use a comma to create separate tags. E.g.: dress, cotton, party dresses.', array(), 'Admin.Catalog.Help'),
                    ),
                ),
                'locales' => $this->locales,
                'label' => $this->translator->trans('Tags', array(), 'Admin.Catalog.Feature'),
            ))
            ->add(
                $builder->create(
                    'display_options',
                    FormType\FormType::class,
                    array(
                        'required' => false,
                        'label' => $this->translator->trans('Display options', array(), 'Admin.Catalog.Feature'),
                    )
                )
                    ->add(
                        'available_for_order',
                        FormType\CheckboxType::class,
                        array(
                            'label' => $this->translator->trans('Available for order', array(), 'Admin.Catalog.Feature'),
                            'required' => false,
                        )
                    )
                    ->add(
                        'show_price',
                        FormType\CheckboxType::class,
                        array(
                            'label' => $this->translator->trans('Show price', array(), 'Admin.Catalog.Feature'),
                            'required' => false,
                        )
                    )
                    ->add(
                        'online_only',
                        FormType\CheckboxType::class,
                        array(
                            'label' => $this->translator->trans(
                                'Web only (not sold in your retail store)',
                                array(),
                                'Admin.Catalog.Feature'
                            ),
                            'required' => false,
                        )
                    )
            )
            ->add('upc', FormType\TextType::class, array(
                'required' => false,
                'label' => $this->translator->trans('UPC barcode', array(), 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\Regex('/^[0-9]{0,12}$/'),
                ),
                'empty_data' => '',
            ))
            ->add('ean13', FormType\TextType::class, array(
                'required' => false,
                'error_bubbling' => true,
                'label' => $this->translator->trans('EAN-13 or JAN barcode', array(), 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\Regex('/^[0-9]{0,13}$/'),
                ),
                'empty_data' => '',
            ))
            ->add('isbn', FormType\TextType::class, array(
                'required' => false,
                'label' => $this->translator->trans('ISBN', array(), 'Admin.Catalog.Feature'),
                'constraints' => array(
                    new Assert\Regex('/^[0-9-]{0,32}$/'),
                ),
                'empty_data' => '',
            ))
            ->add('reference', FormType\TextType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Reference', array(), 'Admin.Global'),
                'empty_data' => '',
            ))
            ->add('show_condition', FormType\CheckboxType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Display condition on product page', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('condition', FormType\ChoiceType::class, array(
                'choices' => array(
                    $this->translator->trans('New', array(), 'Shop.Theme.Catalog') => 'new',
                    $this->translator->trans('Used', array(), 'Shop.Theme.Catalog') => 'used',
                    $this->translator->trans('Refurbished', array(), 'Shop.Theme.Catalog') => 'refurbished',
                ),
                'attr' => array(
                    'class' => 'custom-select',
                ),
                'required' => true,
                'label' => $this->translator->trans('Condition', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('suppliers', FormType\ChoiceType::class, array(
                'choices' => $this->suppliers,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'attr' => array(
                    'class' => 'custom-select',
                ),
                'label' => $this->translator->trans('Suppliers', array(), 'Admin.Global'),
            ))
            ->add('default_supplier', FormType\ChoiceType::class, array(
                'choices' => $this->suppliers,
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'custom-select',
                ),
                'label' => $this->translator->trans('Default suppliers', array(), 'Admin.Catalog.Feature'),
            ))
        ;

        foreach ($this->suppliers as $supplier => $id) {
            $builder->add(
                'supplier_combination_'.$id,
                FormType\CollectionType::class,
                array(
                    'entry_type' => ProductSupplierCombination::class,
                    'entry_options' => array(
                        'id_supplier' => $id,
                    ),
                    'prototype' => true,
                    'allow_add' => true,
                    'required' => false,
                    'label' => $supplier,
                )
            );
        }

        $builder->add('custom_fields', FormType\CollectionType::class, array(
            'entry_type' => ProductCustomField::class,
            'label' => $this->translator->trans('Customization', array(), 'Admin.Catalog.Feature'),
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true,
        ));

        //Add product attachment form
        $builder->add('attachment_product', ProductAttachement::class, array(
            'required' => false,
            'label' => $this->translator->trans('Attachment', array(), 'Admin.Catalog.Feature'),
            'attr' => array('data-action' => $this->router->generate('admin_product_attachement_add_action', array('idProduct' => 1))),
        ));

        //Add attachment selectors
        $builder->add('attachments', FormType\ChoiceType::class, array(
            'expanded' => true,
            'multiple' => true,
            'choices' => $this->attachmentList,
            'choice_label' => function ($choice, $key, $value) {
                $attachmentKey = array_search($key, array_column($this->fullAttachmentList, 'file'), true);

                return $this->fullAttachmentList[$attachmentKey]['name'];
            },
            'required' => false,
            'attr' => array(
                'class' => 'custom-select',
                'data' => $this->fullAttachmentList,
            ),
            'label' => $this->translator->trans('Attachments for this product:', array(), 'Admin.Catalog.Feature'),
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            //If not supplier selected, remove all supplier combinations collection form
            if (!isset($data['suppliers']) || 0 == count($data['suppliers'])) {
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
