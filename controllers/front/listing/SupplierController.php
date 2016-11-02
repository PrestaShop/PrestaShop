<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierProductSearchProvider;

class SupplierControllerCore extends ProductListingFrontController
{
    public $php_self = 'supplier';

    /** @var Supplier */
    protected $supplier;
    private $label;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->supplier)) {
            parent::canonicalRedirection($this->context->link->getSupplierLink($this->supplier));
        } elseif ($canonicalURL) {
            parent::canonicalRedirection($canonicalURL);
        }
    }

    /**
     * Initialize supplier controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        if ($id_supplier = (int) Tools::getValue('id_supplier')) {
            $this->supplier = new Supplier($id_supplier, $this->context->language->id);

            if (!Validate::isLoadedObject($this->supplier) || !$this->supplier->active) {
                $this->redirect_after = '404';
                $this->redirect();
            } else {
                $this->canonicalRedirection();
            }
        }

        parent::init();
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            parent::initContent();

            if (Validate::isLoadedObject($this->supplier) && $this->supplier->active && $this->supplier->isAssociatedToShop()) {
                $this->assignSupplier();
                $this->label = $this->trans(
                    'List of products by supplier %s', array($this->supplier->name), 'Shop.Theme.Catalog'
                );
                $this->doProductSearch(
                    'catalog/listing/supplier',
                    array('entity' => 'supplier', 'id' => $this->supplier->id)
                );
            } else {
                $this->assignAll();
                $this->label = $this->trans(
                    'List of all suppliers', array(), 'Shop.Theme.Catalog'
                );
                $this->setTemplate('catalog/suppliers', array('entity' => 'suppliers'));
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setIdSupplier($this->supplier->id)
            ->setSortOrder(new SortOrder('product', 'position', 'asc'))
        ;

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new SupplierProductSearchProvider(
            $this->getTranslator(),
            $this->supplier
        );
    }

    /**
     * Assign template vars if displaying one supplier.
     */
    protected function assignSupplier()
    {
        $this->context->smarty->assign(array(
            'supplier' => $this->objectPresenter->present($this->supplier),
        ));
    }

    /**
     * Assign template vars if displaying the supplier list.
     */
    protected function assignAll()
    {
        $this->context->smarty->assign(array(
            'brands' => $this->getTemplateVarSuppliers(),
        ));
    }

    public function getTemplateVarSuppliers()
    {
        $suppliers = Supplier::getSuppliers(true, $this->context->language->id, true);
        $suppliers_for_display = array();

        foreach ($suppliers as $supplier) {
            $suppliers_for_display[$supplier['id_supplier']] = $supplier;
            $suppliers_for_display[$supplier['id_supplier']]['text'] = $supplier['description'];
            $suppliers_for_display[$supplier['id_supplier']]['image'] = $this->context->link->getSupplierImageLink($supplier['id_supplier'], 'small_default');
            $suppliers_for_display[$supplier['id_supplier']]['url'] = $this->context->link->getsupplierLink($supplier['id_supplier']);
            $suppliers_for_display[$supplier['id_supplier']]['nb_products'] = $supplier['nb_products'] > 1
                ? $this->trans('%number% products', array('%number%' => $supplier['nb_products']), 'Shop.Theme.Catalog')
                : $this->trans('%number% product', array('%number%' => $supplier['nb_products']), 'Shop.Theme.Catalog');
        }

        return $suppliers_for_display;
    }

    public function getListingLabel()
    {
        return $this->label;
    }
}
