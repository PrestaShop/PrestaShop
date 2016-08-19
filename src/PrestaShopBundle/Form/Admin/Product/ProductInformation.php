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
use Symfony\Component\Form\FormError;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the basic product information form
 */
class ProductInformation extends CommonAbstractType
{
    private $router;
    private $context;
    private $translator;
    private $locales;
    private $nested_categories;
    private $manufacturers;
    private $productAdapter;
    private $configuration;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     * @param object $router
     * @param object $categoryDataProvider
     * @param object $productDataProvider
     * @param object $featureDataProvider
     * @param object $manufacturerDataProvider
     */
    public function __construct($translator, $legacyContext, $router, $categoryDataProvider, $productDataProvider, $featureDataProvider, $manufacturerDataProvider)
    {
        $this->context = $legacyContext;
        $this->translator = $translator;
        $this->router = $router;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->productDataProvider = $productDataProvider;
        $this->featureDataProvider = $featureDataProvider;
        $this->manufacturerDataProvider = $manufacturerDataProvider;
        $this->configuration = new Configuration();

        $this->categories = $this->formatDataChoicesList($this->categoryDataProvider->getAllCategoriesName(), 'id_category');
        $this->nested_categories = $this->categoryDataProvider->getNestedCategories();
        $this->productAdapter = $this->productDataProvider;
        $this->locales = $this->context->getLanguages();
        $this->currency = $this->context->getContext()->currency;
        $this->manufacturers = $this->formatDataChoicesList(
            $this->manufacturerDataProvider->getManufacturers(false, 0, true, false, false, false, true),
            'id_manufacturer'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     *
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type_product', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices'  => array(
                $this->translator->trans('Standard product', [], 'Admin.Catalog.Feature') => 0,
                $this->translator->trans('Pack of products', [], 'Admin.Catalog.Feature') => 1,
                $this->translator->trans('Virtual product', [], 'Admin.Catalog.Feature') => 2,
            ),
            'choices_as_values' => true,
            'label' =>  $this->translator->trans('Type', [], 'Admin.Catalog.Feature'),
            'required' => true,
        ))
        ->add('inputPackItems', 'PrestaShopBundle\Form\Admin\Type\TypeaheadProductPackCollectionType', array(
            'remote_url' => $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&excludeVirtuals=1&limit=20&q=%QUERY',
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
        ))
        ->add('name', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'options' => [
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/[<>;=#{}]/',
                        'match'   => false,
                    )),
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 3, 'max' => 128))
                ), 'attr' => ['placeholder' => $this->translator->trans('Enter your product name', [], 'Admin.Catalog.Help'), 'class' => 'edit js-edit']
            ],
            'locales' => $this->locales,
            'hideTabs' => true,
            'label' => $this->translator->trans('Name', [], 'Admin.Global')
        ))
        ->add('description', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
            'options' => [
                'attr' => array('class' => 'autoload_rte'),
                'required' => false
            ],
            'locales' => $this->locales,
            'hideTabs' => true,
            'label' =>  $this->translator->trans('Description', [], 'Admin.Global'),
            'required' => false
        ))
        ->add('description_short', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextareaType', // https://github.com/symfony/symfony/issues/5906
            'options' => [
                'attr' => array(
                    'class' => 'autoload_rte',
                    'placeholder' => $this->translator->trans('The summary is a short sentence describing your product.<br />It will appears at the top of your shop\'s product page, in product lists, and in search engines\' results page (so it\'s important for SEO). To give more details about your product, use the "Description" tab.', [], 'Admin.Catalog.Help')
                ),
                'constraints' => array(
                    new Assert\Callback(function ($str, ExecutionContextInterface $context) {
                        $str = strip_tags($str);
                        $limit = (int)$this->configuration->get('PS_PRODUCT_SHORT_DESC_LIMIT') <=0 ? 800 : $this->configuration->get('PS_PRODUCT_SHORT_DESC_LIMIT');

                        if (strlen($str) > $limit) {
                            $context->addViolation(
                                $this->translator->trans('This value is too long. It should have %limit% characters or less.', [], 'Admin.Catalog.Notification'),
                                array('%limit%' => $limit)
                            );
                        }
                    }),
                ),
                'required' => false
            ],
            'locales' => $this->locales,
            'hideTabs' => true,
            'label' =>  $this->translator->trans('Short description', [], 'Admin.Catalog.Feature'),
            'required' => false
        ))

        //FEATURES & ATTRIBUTES
        ->add('features', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
            'entry_type' =>'PrestaShopBundle\Form\Admin\Feature\ProductFeature',
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true
        ))
        ->add('id_manufacturer', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' => $this->manufacturers,
            'choices_as_values' => true,
            'required' => false,
            'label' => $this->translator->trans('Brand', [], 'Admin.Catalog.Feature')
        ))

        //RIGHT COL
        ->add('active', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'label' => $this->translator->trans('Enabled', [], 'Admin.Global'),
            'required' => false,
        ))
        ->add('price_shortcut', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax retail price', [], 'Admin.Catalog.Feature'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            ),
            'attr' => []
        ))
        ->add('price_ttc_shortcut', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Retail price with tax', [], 'Admin.Catalog.Feature'),
            'mapped' => false,
            'currency' => $this->currency->iso_code,
        ))
        ->add('qty_0_shortcut', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'required' => false,
            'label' => $this->translator->trans('Quantity', [], 'Admin.Catalog.Feature'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric'))
            )
        ))
        ->add('categories', 'PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType', array(
            'label' => $this->translator->trans('Associated categories', [], 'Admin.Catalog.Feature'),
            'list' => $this->nested_categories,
            'valid_list' => $this->categories,
            'multiple' => true,
        ))
        ->add('id_category_default', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'choices' =>  $this->categories,
            'choices_as_values' => true,
            'expanded' => true,
            'multiple' => false,
            'required' =>  true,
            'label' => $this->translator->trans('Default category', [], 'Admin.Catalog.Feature')
        ))
        ->add('new_category', 'PrestaShopBundle\Form\Admin\Category\SimpleCategory', array(
            'ajax' => true,
            'required' => false,
            'mapped' => false,
            'constraints' => [],
            'label' => $this->translator->trans('Add a new category', [], 'Admin.Catalog.Feature'),
        ))
        ->add('ignore', null, [
            'mapped' => false
        ])
        ->add('related_products', 'PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType', array(
            'remote_url' => $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
            'mapping_value' => 'id',
            'mapping_name' => 'name',
            'placeholder' => $this->translator->trans('Search and add a related product', [], 'Admin.Catalog.Help'),
            'template_collection' => '<span class="label">%s</span><i class="material-icons delete">clear</i>',
            'required' => false,
            'label' =>  $this->translator->trans('Accessories', [], 'Admin.Catalog.Feature')
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!isset($data['type_product'])) {
                $data['type_product'] = 0;
                $event->setData($data);
            }

            //if product type is pack, check if inputPackItems is not empty
            if ($data['type_product'] == 1) {
                if (!isset($data['inputPackItems']) || empty($data['inputPackItems']['data'])) {
                    $form = $event->getForm();
                    $error = $this->translator->trans('This pack is empty. You must add at least one product item.', [], 'Admin.Catalog.Notification');
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
