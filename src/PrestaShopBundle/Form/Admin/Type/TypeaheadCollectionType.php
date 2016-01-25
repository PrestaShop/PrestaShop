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
namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * This form class is responsible to create generic typeahead field
 */
class TypeaheadCollectionType extends CommonAbstractType
{
    private $remote_url;
    private $mapping_value;
    private $mapping_name;
    private $placeholder;
    private $template_collection;
    private $limit;

    /**
     * Constructor
     *
     * @param string $remote_url The remote url to fetch datas
     * @param string $mapping_value The value to map
     * @param string $mapping_name The name to map
     * @param string $placeholder The placeholder for the searchbox
     * @param string $template_collection The template use by php/javascript to render a collection line (name, image). EX : <img src="%s" /><span>%s</span>
     * @param int $limit Limit the number of collection, if set to 0, collection is unlimited
     */
    public function __construct($remote_url, $mapping_value = 'id', $mapping_name = 'name', $placeholder = '', $template_collection = '', $limit = 0)
    {
        $this->remote_url = $remote_url;
        $this->mapping_value = $mapping_value;
        $this->mapping_name = $mapping_name;
        $this->placeholder = $placeholder;
        $this->limit = $limit;
        $this->template_collection = $template_collection ? $template_collection : '<div class="title col-xs-10">%s</div><button type="button" class="btn btn-default delete"><i class="icon-trash"></i></button>';
    }


    /**
     * {@inheritdoc}
     *
     * Add the var choices to the view
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['placeholder'] = $this->placeholder;
        $view->vars['remote_url'] = $this->remote_url;
        $view->vars['mapping_value'] = $this->mapping_value;
        $view->vars['mapping_name'] = $this->mapping_name;
        $view->vars['template_collection'] = $this->template_collection;
        $view->vars['limit'] = $this->limit;
    }

    /**
     * {@inheritdoc}
     *
     * Builds the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('data', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, array(
            'entry_type' => \Symfony\Component\Form\Extension\Core\Type\HiddenType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'required' => false,
            'prototype' => true,
        ));
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'typeahead_collection';
    }
}
