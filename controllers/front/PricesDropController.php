<?php
/*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PricesDropControllerCore extends FrontController
{
    public $php_self = 'prices-drop';

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'product_list.css');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $hook_executed = false;
        $nbProducts = 0;
        $products = array();

        Hook::exec('actionProductListOverride', array(
            'nbProducts'   => &$nbProducts,
            'catProducts'  => &$products,
            'hookExecuted' => &$hook_executed,
        ));

        // The hook was not executed, standard working
        if (!$hook_executed) {
            $this->productSort();
            $nbProducts = Product::getPricesDrop($this->context->language->id, null, null, true);
            $this->pagination($nbProducts);

            $products = Product::getPricesDrop($this->context->language->id, (int)$this->p - 1, (int)$this->n, false, $this->orderBy, $this->orderWay);
        } else {
            // Hook executed, use the override
            // Pagination must be call after "getProducts"
            $this->pagination($nbProducts);
        }

        $this->addColorsToProductList($products);

        Hook::exec('actionProductListModifier', array(
            'nb_products'  => &$nbProducts,
            'cat_products' => &$products,
        ));

        $this->context->smarty->assign(array(
            'products' => $products,
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'nbProducts' => $nbProducts,
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
        ));

        $this->setTemplate(_PS_THEME_DIR_.'prices-drop.tpl');
    }
}
