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

namespace PrestaShop\PrestaShop\Core\Business\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * This form class is risponsible to create a product with attribute without attribute field
 */
class TypeaheadProductCollectionType extends \PrestaShop\PrestaShop\Core\Foundation\Form\Type\TypeaheadCollectionType
{
    protected $productAdapter;

    /**
     * {@inheritdoc}
     *
     * @param : Object $productAdapter
     */
    public function __construct($remote_url, $mapping_value = 'id', $mapping_name = 'name', $placeholder = '', $template_collection = '', $productAdapter)
    {
        parent::__construct($remote_url, $mapping_value, $mapping_name, $placeholder, $template_collection);
        $this->productAdapter = $productAdapter;
    }

    /**
     * {@inheritdoc}
     *
     * Add the var choices to the view
     * Inject collection products
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        //if form is submitted, inject datas to display collection
        if (!empty($view->vars['value']) && !empty($view->vars['value']['data'])) {
            $collection = array();

            foreach ($view->vars['value']['data'] as $id) {
                $product = $this->productAdapter->getProduct($id);
                $collection[] = array(
                    'id' => $id,
                    'name' => $product->name[1],
                );
            }
            $view->vars['collection'] = $collection;
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'typeahead_product_collection';
    }
}
