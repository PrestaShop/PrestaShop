<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * This form class is responsible to create a product, with or without attribute field
 */
class TypeaheadProductCollectionType extends CommonAbstractType
{
    protected $productAdapter;
    protected $categoryAdapter;

    /**
     * {@inheritdoc}
     *
     * @param object $productAdapter
     */
    public function __construct($productAdapter, $categoryAdapter)
    {
        $this->productAdapter = $productAdapter;
        $this->categoryAdapter = $categoryAdapter;
    }

    /**
     * {@inheritdoc}
     *
     * Add the vars to the view
     * Inject collection products
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['remote_url'] = $options['remote_url'];
        $view->vars['mapping_value'] = $options['mapping_value'];
        $view->vars['mapping_name'] = $options['mapping_name'];
        $view->vars['mapping_type'] = $options['mapping_type'];
        $view->vars['template_collection'] = $options['template_collection'];
        $view->vars['limit'] = $options['limit'];

        //if form is submitted, inject datas to display collection
        if (!empty($view->vars['value']) && !empty($view->vars['value']['data'])) {
            $collection = [];

            $i = 0;
            foreach ($view->vars['value']['data'] as $id) {
                if (!$id) {
                    continue;
                }

                switch ($view->vars['mapping_type']) {
                    case 'category':
                        $category = $this->categoryAdapter->getCategory($id);
                        $collection[] = [
                            'id' => $id,
                            'name' => $this->categoryAdapter->getBreadCrumb($category->id),
                            'image' => $category->image,
                        ];
                        break;

                    default:
                        $product = $this->productAdapter->getProduct($id);
                        $collection[] = [
                            'id' => $id,
                            'name' => reset($product->name) . ' (ref:' . $product->reference . ')',
                            'image' => $product->image,
                        ];
                        break;
                }
                $i++;

                //if collection length is up to limit, break
                if ($options['limit'] != 0 && $i >= $options['limit']) {
                    break;
                }
            }
            $view->vars['collection'] = $collection;
        }
    }

    /**
     * {@inheritdoc}
     *
     * Builds the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('data', CollectionType::class, [
            'entry_type' => HiddenType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'required' => false,
            'prototype' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'remote_url' => '',
            'mapping_value' => 'id',
            'mapping_name' => 'name',
            'mapping_type' => 'product',
            'placeholder' => '',
            'template_collection' => '',
            'limit' => 0,
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'typeahead_product_collection';
    }
}
