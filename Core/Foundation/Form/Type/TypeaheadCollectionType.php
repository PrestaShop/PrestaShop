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

namespace PrestaShop\PrestaShop\Core\Foundation\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * This form class is risponsible to create generic typeahed field
 */
class TypeaheadCollectionType extends AbstractType
{
    private $remote_url;
    private $mapping_value;
    private $mapping_name;
    private $placeholder;
    private $template_collection;

    /**
     * Constructor
     *
     * @param string $remote_url The remote url to fetch datas
     * @param string $mapping_value The value to map
     * @param string $mapping_name The name to map
     * @param string $placeholder The placeholder for the searchbox
     * @param string $template_collection The template use by php/javascript to render a collection line (name, image). EX : <img src="%s" /><span>%s</span>
     *
     */
    public function __construct($remote_url, $mapping_value = 'id', $mapping_name = 'name', $placeholder = '', $template_collection = '')
    {
        $this->remote_url = $remote_url;
        $this->mapping_value = $mapping_value;
        $this->mapping_name = $mapping_name;
        $this->placeholder = $placeholder;
        $this->template_collection = $template_collection ? $template_collection : '<span>%s</span> - <a href="" class="delete">X</a>';
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
    }

    /**
     * {@inheritdoc}
     *
     * Builds the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('data', 'collection', array(
            'type' => 'hidden',
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'required' => false,
            'prototype' => true,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'typeahead_collection';
    }
}
