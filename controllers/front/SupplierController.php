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

class SupplierControllerCore extends ProductPresentingFrontControllerCore
{
    public $php_self = 'supplier';

    /** @var Supplier */
    protected $supplier;
    protected $supplier_products;
    protected $nbProducts;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->supplier)) {
            parent::canonicalRedirection($this->context->link->getSupplierLink($this->supplier));
        }
    }

    /**
     * Initialize supplier controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if ($id_supplier = (int)Tools::getValue('id_supplier')) {
            $this->supplier = new Supplier($id_supplier, $this->context->language->id);

            if (!Validate::isLoadedObject($this->supplier) || !$this->supplier->active) {
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

            if (Validate::isLoadedObject($this->supplier) && $this->supplier->active && $this->supplier->isAssociatedToShop()) {
                $this->productSort();
                $this->assignOne();
                $this->setTemplate('catalog/supplier.tpl');
            } else {
                $this->assignAll();
                $this->setTemplate('catalog/suppliers.tpl');
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    /**
     * Assign template vars if displaying one supplier
     */
    protected function assignOne()
    {
        $this->productSort();
        $this->assignSortOptions();
        $this->assignProductList();

        $products = array_map(function (array $product) {
            return $this->prepareProductForTemplate($product);
        }, $this->supplier_products);

        if ($this->nbProducts <= 0) {
            $this->warning[] = $this->l('No products for this supplier.');
        }

        $this->context->smarty->assign([
            'supplier' => $this->objectSerializer->toArray($this->supplier),
            'products' => $products,
        ]);
    }

    /**
     * Assign template vars if displaying the supplier list
     */
    protected function assignAll()
    {
        $this->context->smarty->assign([
            'suppliers' => $this->getTemplateVarSuppliers(),
        ]);
    }

    public function assignProductList()
    {
        $this->nbProducts = $this->supplier->getProducts($this->supplier->id, null, null, null, $this->orderBy, $this->orderWay, true);
        $this->pagination((int)$this->nbProducts);
        $this->supplier_products = $this->supplier->getProducts($this->supplier->id, $this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);

        $this->addColorsToProductList($this->supplier_products);

        foreach ($this->supplier_products as &$product) {
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

        $pageURL = $this->context->link->getSupplierLink(
            $this->supplier
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

    public function getTemplateVarSuppliers()
    {
        $suppliers = Supplier::getSuppliers(true, $this->context->language->id, true);
        $suppliers_for_display = [];

        foreach ($suppliers as $supplier) {
            $suppliers_for_display[$supplier['id_supplier']] = $supplier;
            $suppliers_for_display[$supplier['id_supplier']]['text'] = $supplier['description'];
            $suppliers_for_display[$supplier['id_supplier']]['image'] = _THEME_SUP_DIR_.$supplier['id_supplier'].'-medium_default.jpg';
            $suppliers_for_display[$supplier['id_supplier']]['url'] = $this->context->link->getsupplierLink($supplier['id_supplier']);
            $suppliers_for_display[$supplier['id_supplier']]['nb_products'] = $supplier['nb_products'] > 1 ? sprintf($this->l('%s products'), $supplier['nb_products']) : sprintf($this->l('% product'), $supplier['nb_products']);
        }

        return $suppliers_for_display;
    }
}
