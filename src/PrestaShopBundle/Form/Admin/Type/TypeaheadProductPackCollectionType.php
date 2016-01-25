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

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * This form class is responsible to create a product pack collection
 */
class TypeaheadProductPackCollectionType extends TypeaheadCollectionType
{
    protected $productAdapter;

    /**
     * {@inheritdoc}
     *
     * @param string $remote_url The remote url to fetch datas
     * @param string $mapping_value The value to map
     * @param string $mapping_name The name to map
     * @param string $placeholder The placeholder for the searchbox
     * @param string $template_collection The template use by php/javascript to render a collection line (name, image). EX : <img src="%s" /><span>%s</span>
     * @param object $productAdapter
     */
    public function __construct($remote_url, $mapping_value = 'id', $mapping_name = 'name', $placeholder = '', $template_collection = '', $productAdapter)
    {
        parent::__construct($remote_url, $mapping_value, $mapping_name, $placeholder, $template_collection);
        $this->productAdapter = $productAdapter;
    }

    /**
     * {@inheritdoc}
     *
     * Add the vars to the view
     * Inject collection products
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        //if form is submitted, inject datas to display collection
        if (!empty($view->vars['value']) && !empty($view->vars['value']['data'])) {
            $view->vars['collection'] = $view->vars['value']['data'];
        }
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'typeahead_product_pack_collection';
    }
}
