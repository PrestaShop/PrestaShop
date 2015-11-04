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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\DropFilesType;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Form\Admin\Category\SimpleCategory as SimpleFormCategory;
use PrestaShopBundle\Form\Admin\Feature\ProductFeature;

/**
 * This form class is risponsible to generate the basic product informations form
 */
class ProductInformation extends AbstractType
{
    private $router;
    private $context;
    private $translator;
    private $manufacturers;
    private $locales;
    private $nested_categories;
    private $productAdapter;
    private $container;

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
        $this->router = $container->get("router");
        $this->categories = $this->formatDataChoicesList($container->get('prestashop.adapter.data_provider.category')->getAllCategoriesName(), 'id_category');
        $this->nested_categories = $container->get('prestashop.adapter.data_provider.category')->getNestedCategories();
        $this->productAdapter = $container->get('prestashop.adapter.data_provider.product');
        $this->locales = $container->get('prestashop.adapter.legacy.context')->getLanguages();
        $this->manufacturers = $this->formatDataChoicesList(
            $container->get('prestashop.adapter.data_provider.manufacturer')->getManufacturers(false, 0, true, false, false, false, true),
            'id_manufacturer'
        );
    }

    /**
     * Format legacy data list to mapping SF2 form filed choice
     *
     * @param array $list
     * @param string $mapping_value
     * @param string $mapping_name
     * @return array
     */
    private function formatDataChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        $new_list = array();
        foreach ($list as $item) {
            $new_list[$item[$mapping_value]] = $item[$mapping_name];
        }
        return $new_list;
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
        ->add('name', new TranslateType('text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 3))
                )
            ), $this->locales), array(
                'label' =>  $this->translator->trans('Name', [], 'AdminProducts')
            ))
        ->add('description', new TranslateType('textarea', array(
                'attr' => array('class' => 'autoload_rte'),
                'required' => false
            ), $this->locales), array(
                'label' =>  $this->translator->trans('Description', [], 'AdminProducts'),
                'required' => false
            ))
        ->add('images', new DropFilesType($this->translator->trans('Images', [], 'AdminProducts'), $this->router->generate('admin_common_upload'), array(
            'maxFiles' => '10',
            'dictRemoveFile' => $this->translator->trans('Delete', [], 'AdminProducts')
        )))
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

        //FEATURES & ATTRIBUTES
        ->add('features', 'collection', array(
            'type' => new ProductFeature($this->container),
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true
        ))

        //RIGHT COL
        ->add('active', 'choice', array(
            'choices'  => array( 1 => $this->translator->trans('Yes', [], 'AdminProducts'), 0 => $this->translator->trans('No', [], 'AdminProducts')),
            'expanded' => true,
            'label' => $this->translator->trans('Enabled', [], 'AdminProducts'),
            'required' => true,
            'multiple' => false,
        ))
        ->add('price_shortcut', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Pre-tax retail price', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
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
        ->add('new_category', new SimpleFormCategory($this->container, true), array(
            'required' => false,
            'mapped' => false,
            'constraints' => [],
            'label' => $this->translator->trans('Add a new category', [], 'AdminProducts'),
            'attr' => ['data-action' => $this->router->generate('admin_category_simple_add_form')]
        ))
        ->add('id_manufacturer', 'choice', array(
            'choices' => $this->manufacturers,
            'required' => false,
            'label' => $this->translator->trans('Manufacturer', [], 'AdminProducts')
        ))
        ->add('related_products', new TypeaheadProductCollectionType(
            $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&exclude_packs=0&excludeVirtuals=0&excludeIds='.urlencode('1,').'&limit=20&q=%QUERY',
            'id',
            'name',
            $this->translator->trans('search in catalog...', [], 'AdminProducts'),
            '',
            $this->productAdapter
        ), array(
            'required' => false,
            'label' => $this->translator->trans('Accessories', [], 'AdminProducts')
        ));
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
