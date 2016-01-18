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
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductPackCollectionType;
use PrestaShopBundle\Form\Admin\Category\SimpleCategory as SimpleFormCategory;
use PrestaShopBundle\Form\Admin\Feature\ProductFeature;
use Symfony\Component\Form\FormError;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type_product', 'choice', array(
            'choices'  => array(
                0 => $this->translator->trans('Standard product', [], 'AdminProducts'),
                1 => $this->translator->trans('Pack of existing products', [], 'AdminProducts'),
                2 => $this->translator->trans('Virtual product (services, booking, downloadable products, etc.)', [], 'AdminProducts'),
            ),
            'label' =>  $this->translator->trans('Type', [], 'AdminProducts'),
            'required' => true,
        ))
        ->add('inputPackItems', new TypeaheadProductPackCollectionType(
            $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&excludeVirtuals=1&limit=20&q=%QUERY',
            'id',
            'name',
            $this->translator->trans('search in catalog...', [], 'AdminProducts'),
            '<div class="title col-xs-10">%s (ref: %s) X %s</div><button type="button" class="btn btn-default delete"><i class="icon-trash"></i></button>',
            $this->productAdapter
        ), array(
            'required' => false,
            'label' => $this->translator->trans('Add product in your pack', [], 'AdminProducts'),
        ))
        ->add('name', new TranslateType('text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 3, 'max' => 128))
                ),
                'attr' => ['placeholder' => $this->translator->trans('Name', [], 'AdminProducts')]
            ), $this->locales, true), array(
                'label' =>  $this->translator->trans('Name', [], 'AdminProducts')
            ))
        ->add('description', new TranslateType('textarea', array(
                'attr' => array('class' => 'autoload_rte'),
                'required' => false
            ), $this->locales, true), array(
                'label' =>  $this->translator->trans('Description', [], 'AdminProducts'),
                'required' => false
            ))
        ->add('description_short', new TranslateType('textarea', array(
            'attr' => array('class' => 'autoload_rte'),
            'constraints' => array(
                new Assert\Callback(function ($str, ExecutionContextInterface $context) {
                    $str = strip_tags($str);
                    $limit = (int)$this->configuration->get('PS_PRODUCT_SHORT_DESC_LIMIT') <=0 ? 800 : $this->configuration->get('PS_PRODUCT_SHORT_DESC_LIMIT');

                    if (strlen($str) > $limit) {
                        $context->addViolation(
                            $this->translator->trans('This value is too long. It should have {{ limit }} characters or less.', [], 'AdminProducts'),
                            array('{{ limit }}' => $limit)
                        );
                    }
                }),
            ),
            'required' => false
        ), $this->locales, true), array(
            'label' =>  $this->translator->trans('Short description', [], 'AdminProducts'),
            'required' => false
        ))

        //FEATURES & ATTRIBUTES
        ->add('features', 'collection', array(
            'type' => new ProductFeature(
                $this->translator,
                $this->context,
                $this->router,
                $this->featureDataProvider
            ),
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true
        ))
        ->add('id_manufacturer', 'choice', array(
            'choices' => $this->manufacturers,
            'required' => false,
            'label' => $this->translator->trans('Manufacturer', [], 'AdminProducts')
        ))

        //RIGHT COL
        ->add('active', 'checkbox', array(
            'label' => $this->translator->trans('Enabled', [], 'AdminProducts'),
            'required' => false,
        ))
        ->add('price_shortcut', 'money', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax retail price', [], 'AdminProducts'),
            'currency' => $this->currency->iso_code,
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            ),
            'attr' => []
        ))
        ->add('price_ttc_shortcut', 'money', array(
            'required' => false,
            'label' => $this->translator->trans('Retail price with tax', [], 'AdminProducts'),
            'mapped' => false,
            'currency' => $this->currency->iso_code,
        ))
        ->add('qty_0_shortcut', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Quantity', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric'))
            )
        ))
        ->add('categories', new ChoiceCategoriesTreeType('Catégories', $this->nested_categories, $this->categories), array(
            'label' => $this->translator->trans('Associated categories', [], 'AdminProducts')
        ))
        ->add('id_category_default', 'choice', array(
            'choices' =>  $this->categories,
            'required' =>  true,
            'label' => $this->translator->trans('Default category', [], 'AdminProducts')
        ))
        ->add('new_category', new SimpleFormCategory(
            $this->translator,
            $this->categoryDataProvider,
            true
        ), array(
            'required' => false,
            'mapped' => false,
            'constraints' => [],
            'label' => $this->translator->trans('Add a new category', [], 'AdminProducts'),
            'attr' => ['data-action' => $this->router->generate('admin_category_simple_add_form')]
        ))
        ->add('related_products', new TypeaheadProductCollectionType(
            $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
            'id',
            'name',
            $this->translator->trans('search in catalog...', [], 'AdminProducts'),
            '',
            $this->productAdapter
        ), array(
            'required' => false,
            'label' => $this->translator->trans('Accessories', [], 'AdminProducts')
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            //if product type is pack, check if inputPackItems is not empty
            if ($data['type_product'] == 1) {
                if (!isset($data['inputPackItems']) || empty($data['inputPackItems']['data'])) {
                    $error = $this->translator->trans('This pack is empty. You must add at least one product item.', [], 'AdminProducts');
                    $form->get('inputPackItems')->addError(new FormError($error));
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
        return 'product_step1';
    }
}
