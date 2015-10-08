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

class ManufacturerControllerCore extends ProductPresentingFrontControllerCore
{
    public $php_self = 'manufacturer';

    protected $manufacturer;
    protected $manufacturer_products;
    protected $nbProducts;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->manufacturer)) {
            parent::canonicalRedirection($this->context->link->getManufacturerLink($this->manufacturer));
        }
    }

    /**
     * Initialize manufaturer controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if ($id_manufacturer = Tools::getValue('id_manufacturer')) {
            $this->manufacturer = new Manufacturer((int)$id_manufacturer, $this->context->language->id);

            if (!Validate::isLoadedObject($this->manufacturer) || !$this->manufacturer->active || !$this->manufacturer->isAssociatedToShop()) {
                $this->redirect_after = '404';
                $this->redirect();
            } else {
                $this->canonicalRedirection();
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            parent::initContent();

            if (Validate::isLoadedObject($this->manufacturer) && $this->manufacturer->active && $this->manufacturer->isAssociatedToShop()) {
                $this->productSort();
                $this->assignOne();
                $this->setTemplate('catalog/manufacturer.tpl');
            } else {
                $this->assignAll();
                $this->setTemplate('catalog/manufacturers.tpl');
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    /**
     * Assign template vars if displaying one manufacturer
     */
    protected function assignOne()
    {
        $this->productSort();
        $this->assignSortOptions();
        $this->assignProductList();

        $products = array_map(function (array $product) {
            return $this->prepareProductForTemplate($product);
        }, $this->manufacturer_products);

        if ($this->nbProducts <= 0) {
            $this->warning[] = $this->l('No products for this manufacturer.');
        }

        $this->context->smarty->assign([
            'manufacturer' => $this->objectSerializer->toArray($this->manufacturer),
            'products' => $products,
        ]);
    }

    /**
     * Assign template vars if displaying the manufacturer list
     */
    protected function assignAll()
    {
        $this->context->smarty->assign([
            'manufacturers' => $this->getTemplateVarManufacturers(),
        ]);
    }

    public function assignProductList()
    {
        $this->nbProducts = $this->manufacturer->getProducts($this->manufacturer->id, null, null, null, $this->orderBy, $this->orderWay, true);
        $this->pagination((int)$this->nbProducts);
        $this->manufacturer_products = $this->manufacturer->getProducts($this->manufacturer->id, $this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);

        $this->addColorsToProductList($this->manufacturer_products);

        foreach ($this->manufacturer_products as &$product) {
            if (isset($product['id_product_attribute']) && $product['id_product_attribute'] && isset($product['product_attribute_minimal_quantity'])) {
                $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
            }
        }
    }

    protected function getSortOptions()
    {
        $settings = $this->getProductPresentationSettings();

        if ($settings->catalog_mode) {
            $options = [];
        } else {
            $options = [
                ['orderBy' => 'price', 'sortOrder' => 'asc', 'label' => $this->l('Increasing price')],
                ['orderBy' => 'price', 'sortOrder' => 'desc', 'label' => $this->l('Decreasing price')],
            ];
        }

        $options[] = ['orderBy' => 'name', 'sortOrder' => 'asc', 'label' => $this->l('Product name, A to Z')];
        $options[] = ['orderBy' => 'name', 'sortOrder' => 'desc', 'label' => $this->l('Product name, Z to A')];

        if (!$settings->catalog_mode && $settings->stock_management_enabled) {
            $options[] = ['orderBy' => 'quantity', 'sortOrder' => 'desc', 'label' => $this->l('In stock')];
        }

        $options[] = ['orderBy' => 'reference', 'sortOrder' => 'asc', 'label' => $this->l('Product reference, A to Z')];
        $options[] = ['orderBy' => 'reference', 'sortOrder' => 'desc', 'label' => $this->l('Product reference, Z to A')];

        $pageURL = $this->context->link->getManufacturerLink(
            $this->manufacturer
        );

        $options = array_map(function ($option) use ($pageURL) {
            $option['url'] = $pageURL . '?orderby=' . $option['orderBy'] . '&orderway=' . $option['sortOrder'];
            $option['current'] = ($option['orderBy'] === Tools::getValue('orderby')) &&
                                 ($option['sortOrder'] === Tools::getValue('orderway'))
            ;
            return $option;
        }, $options);

        return $options;
    }

    public function assignSortOptions()
    {
        $this->context->smarty->assign('sort_options', $this->getSortOptions());
    }

    public function prepareProductForTemplate(array $product)
    {
        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        return $presenter->presentForListing(
            $settings,
            $product,
            $this->context->language
        );
    }

    public function getTemplateVarManufacturers()
    {
        $manufacturers = Manufacturer::getManufacturers(true, $this->context->language->id, true, $this->p, $this->n, false);
        $manufacturers_for_display = [];

        foreach ($manufacturers as $manufacturer) {
            $manufacturers_for_display[$manufacturer['id_manufacturer']] = $manufacturer;
            $manufacturers_for_display[$manufacturer['id_supplier']]['text'] = $manufacturer['short_description'];
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['image'] = _THEME_MANU_DIR_.$manufacturer['id_manufacturer'].'-medium_default.jpg';
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['url'] = $this->context->link->getmanufacturerLink($manufacturer['id_manufacturer']);
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['nb_products'] = $manufacturer['nb_products'] > 1 ? sprintf($this->l('%s products'), $manufacturer['nb_products']) : sprintf($this->l('% product'), $manufacturer['nb_products']);
        }

        return $manufacturers_for_display;
    }
}
