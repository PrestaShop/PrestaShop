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
 * This form class is responsible to create a product, with or without attribute field
 */
class TypeaheadProductCollectionType extends TypeaheadCollectionType
{
    protected $productAdapter;
    protected $limit;

    /**
     * {@inheritdoc}
     *
     * @param string $remote_url The remote url to fetch datas
     * @param string $mapping_value The value to map
     * @param string $mapping_name The name to map
     * @param string $placeholder The placeholder for the searchbox
     * @param string $template_collection The template use by php/javascript to render a collection line (name, image). EX : <img src="%s" /><span>%s</span>
     * @param object $productAdapter
     * @param int $limit Limit the number of collection, if set to 0, collection is unlimited
     */
    public function __construct($remote_url, $mapping_value = 'id', $mapping_name = 'name', $placeholder = '', $template_collection = '', $productAdapter, $limit = 0)
    {
        parent::__construct($remote_url, $mapping_value, $mapping_name, $placeholder, $template_collection, $limit);
        $this->productAdapter = $productAdapter;
        $this->limit = $limit;
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
            $collection = array();

            $i = 0;
            foreach ($view->vars['value']['data'] as $id) {
                if (!$id) {
                    continue;
                }
                $product = $this->productAdapter->getProduct($id);
                $collection[] = array(
                    'id' => $id,
                    'name' => $product->name[1].' (ref:'.$product->reference.')',
                    'image' => $product->image,
                );
                $i++;

                //if collection length is up to limit, break
                if ($this->limit != 0 && $i >= $this->limit) {
                    break;
                }
            }
            $view->vars['collection'] = $collection;
        }
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
