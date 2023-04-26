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

use Currency;
use Language;
use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Feature\FeatureDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerDataProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Category\SimpleCategory;
use PrestaShopBundle\Form\Admin\Feature\ProductFeature;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductPackCollectionType;
use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLength;
use PrestaShopBundle\Service\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * This form class is responsible to generate the basic product information form.
 */
class ProductInformation extends CommonAbstractType
{
    /**
     * @var array
     */
    public $categories;
    /**
     * @var CategoryDataProvider
     */
    public $categoryDataProvider;
    /**
     * @var Configuration
     */
    public $configuration;
    /**
     * @var LegacyContext
     */
    private $context;
    /**
     * @var Currency
     */
    public $currency;
    /**
     * @var FeatureDataProvider
     */
    public $featureDataProvider;
    /**
     * @var array<int|Language>
     */
    private $locales;
    /**
     * @var ManufacturerDataProvider
     */
    private $manufacturerDataProvider;
    /**
     * @var array
     */
    private $manufacturers;
    /**
     * @var array
     */
    public $nested_categories;
    /**
     * @var ProductDataProvider
     */
    public $productDataProvider;
    /**
     * @var Router
     */
    public $router;
    /**
     * @var TranslatorInterface
     */
    public $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     * @param LegacyContext $legacyContext
     * @param Router $router
     * @param CategoryDataProvider $categoryDataProvider
     * @param ProductDataProvider $productDataProvider
     * @param FeatureDataProvider $featureDataProvider
     * @param ManufacturerDataProvider $manufacturerDataProvider
     */
    public function __construct(
        $translator,
        $legacyContext,
        $router,
        $categoryDataProvider,
        $productDataProvider,
        $featureDataProvider,
        $manufacturerDataProvider
    ) {
        $this->context = $legacyContext;
        $this->translator = $translator;
        $this->router = $router;
        $this->productDataProvider = $productDataProvider;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->manufacturerDataProvider = $manufacturerDataProvider;
        $this->featureDataProvider = $featureDataProvider;

        $this->configuration = new Configuration();
        $this->locales = $this->context->getLanguages();
        $this->currency = $this->context->getContext()->currency;

        $this->categories = $this->formatDataChoicesList(
            $this->categoryDataProvider->getAllCategoriesName(
                $root_category = null,
                $id_lang = false,
                $active = false
            ),
            'id_category'
        );

        $this->nested_categories = $this->categoryDataProvider->getNestedCategories(
            $root_category = null,
            $id_lang = false,
            $active = false
        );

        $this->manufacturers = $this->formatDataChoicesList(
            $this->manufacturerDataProvider->getManufacturers(
                $get_nb_products = false,
                $id_lang = 0,
                $active = true,
                $p = false,
                $n = false,
                $all_group = false,
                $group_by = true
            ),
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
        $is_stock_management = $this->configuration->get('PS_STOCK_MANAGEMENT');
        $shortDescriptionLimit = (int) $this->configuration->get('PS_PRODUCT_SHORT_DESC_LIMIT');
        if ($shortDescriptionLimit <= 0) {
            $shortDescriptionLimit = ProductSettings::MAX_DESCRIPTION_SHORT_LENGTH;
        }

        $builder->add('type_product', FormType\ChoiceType::class, [
            'choices' => [
                $this->translator->trans('Standard product', [], 'Admin.Catalog.Feature') => 0,
                $this->translator->trans('Pack of products', [], 'Admin.Catalog.Feature') => 1,
                $this->translator->trans('Virtual product', [], 'Admin.Catalog.Feature') => 2,
            ],
            'attr' => [
                'class' => 'custom-select',
            ],
            'label' => $this->translator->trans('Type', [], 'Admin.Catalog.Feature'),
            'required' => true,
        ])
            ->add('inputPackItems', TypeaheadProductPackCollectionType::class, [
                'remote_url' => $this->context->getLegacyAdminLink('AdminProducts', true, ['ajax' => 1, 'action' => 'productsList', 'forceJson' => 1, 'excludeVirtuals' => 1, 'limit' => 20]) . '&q=%QUERY',
                'mapping_value' => 'id',
                'mapping_name' => 'name',
                'placeholder' => $this->translator->trans('Search for a product', [], 'Admin.Catalog.Help'),
                'template_collection' => '
              <h4>%s</h4>
              <div class="ref">REF: %s</div>
              <div class="quantity text-md-right">x%s</div>
              <button type="button" class="btn btn-danger btn-sm delete"><i class="material-icons">delete</i></button>
            ',
                'required' => false,
                'label' => $this->translator->trans('Add products to your pack', [], 'Admin.Catalog.Feature'),
            ])
            ->add('name', TranslateType::class, [
                'type' => FormType\TextType::class,
                'help' => $this->translator->trans(
                    'Invalid characters are: %invalidCharacters%',
                    ['%invalidCharacters%' => '<>;=#{}'],
                    'Admin.Catalog.Feature'
                ),
                'options' => [
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/[<>;=#{}]/',
                            'match' => false,
                            'message' => $this->translator->trans(
                                'This field contains invalid characters: %invalidCharacters%',
                                ['%invalidCharacters%' => '<>;=#{}'],
                                'Admin.Catalog.Feature'
                            ),
                        ]),
                        new Assert\NotBlank(),
                        new Assert\Length(['min' => 3, 'max' => 128]),
                    ],
                    'attr' => [
                        'placeholder' => $this->translator->trans('Enter your product name', [], 'Admin.Catalog.Help'),
                        'class' => 'edit js-edit serp-default-title',
                    ],
                ],
                'locales' => $this->locales,
                'hideTabs' => true,
                'label' => $this->translator->trans('Name', [], 'Admin.Global'),
            ])
            ->add('description', TranslateType::class, [
                'type' => FormattedTextareaType::class,
                'options' => [
                    'required' => false,
                    'attr' => [
                        'class' => 'serp-default-description',
                    ],
                    'constraints' => [
                        new TinyMceMaxLength([
                            'max' => FormattedTextareaType::LIMIT_TEXT_UTF8,
                        ]),
                    ],
                ],
                'locales' => $this->locales,
                'hideTabs' => true,
                'label' => $this->translator->trans('Description', [], 'Admin.Global'),
                'required' => false,
            ])
            ->add('description_short', TranslateType::class, [
                'type' => FormType\TextareaType::class, // https://github.com/symfony/symfony/issues/5906
                'options' => [
                    'attr' => [
                        'class' => 'autoload_rte',
                        'placeholder' => $this->translator->trans('The summary is a short sentence describing your product.<br />It will appears at the top of your shop\'s product page, in product lists, and in search engines\' results page (so it\'s important for SEO). To give more details about your product, use the "Description" tab.', [], 'Admin.Catalog.Help'),
                        'counter' => $shortDescriptionLimit,
                    ],
                    'constraints' => [
                        new TinyMceMaxLength([
                            'max' => $shortDescriptionLimit,
                        ]),
                    ],
                    'required' => false,
                ],
                'locales' => $this->locales,
                'hideTabs' => true,
                'label' => $this->translator->trans('Short description', [], 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            //FEATURES & ATTRIBUTES
            ->add('features', FormType\CollectionType::class, [
                'entry_type' => ProductFeature::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('id_manufacturer', FormType\ChoiceType::class, [
                'choices' => $this->manufacturers,
                'required' => false,
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
                'label' => $this->translator->trans('Brand', [], 'Admin.Catalog.Feature'),
                'placeholder' => $this->translator->trans('Choose a brand', [], 'Admin.Catalog.Feature'),
            ])
            //RIGHT COL
            ->add('active', FormType\CheckboxType::class, [
                'label' => $this->translator->trans('Enabled', [], 'Admin.Global'),
                'required' => false,
            ])
            ->add('price_shortcut', FormType\MoneyType::class, [
                'required' => false,
                'label' => $this->translator->trans('Pre-tax retail price', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'float']),
                ],
                'attr' => [],
            ])
            ->add('price_ttc_shortcut', FormType\MoneyType::class, [
                'required' => false,
                'label' => $this->translator->trans('Retail price with tax', [], 'Admin.Catalog.Feature'),
                'mapped' => false,
                'currency' => $this->currency->iso_code,
            ]);
        if ($is_stock_management) {
            $builder->add('qty_0_shortcut', FormType\NumberType::class, [
                'required' => false,
                'label' => $this->translator->trans('Quantity', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                ],
            ]);
        }
        $builder->add('categories', ChoiceCategoriesTreeType::class, [
            'label' => $this->translator->trans('Associated categories', [], 'Admin.Catalog.Feature'),
            'list' => $this->nested_categories,
            'valid_list' => $this->categories,
            'multiple' => true,
        ])
            ->add('id_category_default', FormType\ChoiceType::class, [
                'choices' => $this->categories,
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => $this->translator->trans('Default category', [], 'Admin.Catalog.Feature'),
            ])
            ->add('new_category', SimpleCategory::class, [
                'ajax' => true,
                'required' => false,
                'mapped' => false,
                'constraints' => [],
                'label' => $this->translator->trans('Add a new category', [], 'Admin.Catalog.Feature'),
            ])
            ->add('ignore', null, [
                'mapped' => false,
            ])
            ->add('related_products', TypeaheadProductCollectionType::class, [
                'remote_url' => $this->context->getLegacyAdminLink('AdminProducts', true, ['ajax' => 1, 'action' => 'productsList', 'forceJson' => 1, 'disableCombination' => 1, 'exclude_packs' => 0, 'excludeVirtuals' => 0, 'limit' => 20]) . '&q=%QUERY',
                'mapping_value' => 'id',
                'mapping_name' => 'name',
                'placeholder' => $this->translator->trans('Search and add a related product', [], 'Admin.Catalog.Help'),
                'template_collection' => '<span class="label">%s</span><i class="material-icons delete">clear</i>',
                'required' => false,
                'label' => $this->translator->trans('Accessories', [], 'Admin.Catalog.Feature'),
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!isset($data['type_product'])) {
                $data['type_product'] = 0;
                $event->setData($data);
            }

            /*
             * Remove duplicates to prevent SQL errors.
             */
            if (isset($data['features'])) {
                $ids = [];
                foreach ($data['features'] as $idx => $feature) {
                    if (empty($feature['value'])) {
                        continue;
                    }

                    $id = sprintf('%d-%d', $feature['feature'], $feature['value']);
                    if (in_array($id, $ids)) {
                        unset($data['features'][$idx]);
                    } else {
                        $ids[] = $id;
                    }
                }

                $event->setData($data);
            }

            //if product type is pack, check if inputPackItems is not empty
            if ($data['type_product'] == 1) {
                if (!isset($data['inputPackItems']) || empty($data['inputPackItems']['data'])) {
                    $form = $event->getForm();
                    $error = $this->translator->trans(
                        'This pack is empty. You must add at least one product item.',
                        [],
                        'Admin.Catalog.Notification'
                    );
                    $form->get('inputPackItems')->addError(new FormError($error));
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
        return 'product_step1';
    }
}
