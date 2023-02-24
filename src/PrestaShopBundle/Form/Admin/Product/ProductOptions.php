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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
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
    private $router;
    private $locales;
    private $fullAttachmentList;
    private $attachmentList;

    /**
     * Constructor.
     *
     * @param object $translator
     * @param object $legacyContext
     * @param object $supplierDataProvider
     * @param object $attachmentDataprovider
     * @param object $router
     */
    public function __construct(
        $translator,
        $legacyContext,
        $supplierDataProvider,
        $attachmentDataprovider,
        $router
    ) {
        $this->context = $legacyContext;
        $this->translator = $translator;
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
        $builder->add('visibility', FormType\ChoiceType::class, [
            'choices' => [
                $this->translator->trans('Everywhere', [], 'Admin.Catalog.Feature') => 'both',
                $this->translator->trans('Catalog only', [], 'Admin.Catalog.Feature') => 'catalog',
                $this->translator->trans('Search only', [], 'Admin.Catalog.Feature') => 'search',
                $this->translator->trans('Nowhere', [], 'Admin.Catalog.Feature') => 'none',
            ],
            'attr' => [
                'class' => 'custom-select',
            ],
            'required' => true,
            'label' => $this->translator->trans('Visibility', [], 'Admin.Catalog.Feature'),
        ])
            ->add('tags', TranslateType::class, [
                'type' => FormType\TextType::class,
                'options' => [
                    'attr' => [
                        'class' => 'tokenfield',
                        'placeholder' => $this->translator->trans('Use a comma to create separate tags. E.g.: dress, cotton, party dresses.', [], 'Admin.Catalog.Help'),
                    ],
                ],
                'locales' => $this->locales,
                'label' => $this->translator->trans('Tags', [], 'Admin.Catalog.Feature'),
            ])
            ->add(
                $builder->create(
                    'display_options',
                    FormType\FormType::class,
                    [
                        'required' => false,
                        'label' => $this->translator->trans('Display options', [], 'Admin.Catalog.Feature'),
                    ]
                )
                    ->add(
                        'available_for_order',
                        FormType\CheckboxType::class,
                        [
                            'label' => $this->translator->trans('Available for order', [], 'Admin.Catalog.Feature'),
                            'required' => false,
                        ]
                    )
                    ->add(
                        'show_price',
                        FormType\CheckboxType::class,
                        [
                            'label' => $this->translator->trans('Show price', [], 'Admin.Catalog.Feature'),
                            'required' => false,
                        ]
                    )
                    ->add(
                        'online_only',
                        FormType\CheckboxType::class,
                        [
                            'label' => $this->translator->trans(
                                'Web only (not sold in your retail store)',
                                [],
                                'Admin.Catalog.Feature'
                            ),
                            'required' => false,
                        ]
                    )
            )
            ->add('mpn', FormType\TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('MPN', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Length(['max' => 40]),
                ],
                'empty_data' => '',
            ])
            ->add('upc', FormType\TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('UPC barcode', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Regex('/^[0-9]{0,12}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('ean13', FormType\TextType::class, [
                'required' => false,
                'error_bubbling' => true,
                'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Regex('/^[0-9]{0,13}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('isbn', FormType\TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('ISBN', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Regex(Isbn::VALID_PATTERN),
                ],
                'empty_data' => '',
            ])
            ->add('reference', FormType\TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('Reference', [], 'Admin.Global'),
                'empty_data' => '',
            ])
            ->add('show_condition', FormType\CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans('Display condition on product page', [], 'Admin.Catalog.Feature'),
            ])
            ->add('condition', FormType\ChoiceType::class, [
                'choices' => [
                    $this->translator->trans('New', [], 'Admin.Global') => 'new',
                    $this->translator->trans('Used', [], 'Shop.Theme.Catalog') => 'used',
                    $this->translator->trans('Refurbished', [], 'Shop.Theme.Catalog') => 'refurbished',
                ],
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => true,
                'label' => $this->translator->trans('Condition', [], 'Admin.Catalog.Feature'),
            ])
            ->add('suppliers', FormType\ChoiceType::class, [
                'choices' => $this->suppliers,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'custom-select',
                ],
                'label' => $this->translator->trans('Suppliers', [], 'Admin.Global'),
            ])
            ->add('default_supplier', FormType\ChoiceType::class, [
                'choices' => $this->suppliers,
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'custom-select',
                ],
                'label' => $this->translator->trans('Default suppliers', [], 'Admin.Catalog.Feature'),
            ]);

        foreach ($this->suppliers as $supplier => $id) {
            $builder->add(
                'supplier_combination_' . $id,
                FormType\CollectionType::class,
                [
                    'entry_type' => ProductSupplierCombination::class,
                    'entry_options' => [
                        'id_supplier' => $id,
                    ],
                    'prototype' => true,
                    'allow_add' => true,
                    'required' => false,
                    'label' => $supplier,
                ]
            );
        }

        $builder->add('custom_fields', FormType\CollectionType::class, [
            'entry_type' => ProductCustomField::class,
            'label' => $this->translator->trans('Customization', [], 'Admin.Catalog.Feature'),
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true,
        ]);

        //Add product attachment form
        $builder->add('attachment_product', ProductAttachement::class, [
            'required' => false,
            'label' => $this->translator->trans('Attachment', [], 'Admin.Catalog.Feature'),
            'attr' => ['data-action' => $this->router->generate('admin_product_attachement_add_action', ['idProduct' => 1])],
        ]);

        //Add attachment selectors
        $builder->add('attachments', FormType\ChoiceType::class, [
            'expanded' => true,
            'multiple' => true,
            'choices' => $this->attachmentList,
            'choice_label' => function ($choice, $key, $value) {
                $attachmentKey = array_search($key, array_column($this->fullAttachmentList, 'file'));

                return $this->fullAttachmentList[$attachmentKey]['name'];
            },
            'required' => false,
            'attr' => [
                'class' => 'custom-select',
                'data' => $this->fullAttachmentList,
            ],
            'label' => $this->translator->trans('Attachments for this product:', [], 'Admin.Catalog.Feature'),
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            //If not supplier selected, remove all supplier combinations collection form
            if (!isset($data['suppliers']) || count($data['suppliers']) == 0) {
                $form = $event->getForm();
                foreach ($this->suppliers as $supplier => $id) {
                    $form->remove('supplier_combination_' . $id);
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
