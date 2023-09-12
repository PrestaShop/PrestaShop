<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class SupplierControllerCore extends ProductListingFrontController
{
    /**
     * @var string
     */
    public $php_self = 'supplier';

    /** @var Supplier|null */
    protected $supplier;
    protected $label;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->supplier)) {
            parent::canonicalRedirection($this->context->link->getSupplierLink($this->supplier));
        } elseif ($canonicalURL) {
            parent::canonicalRedirection($canonicalURL);
        }
    }

    public function getCanonicalURL(): string
    {
        if (Validate::isLoadedObject($this->supplier)) {
            return $this->buildPaginatedUrl($this->context->link->getSupplierLink($this->supplier));
        }

        return $this->context->link->getPageLink('supplier');
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
                    'List of products by supplier %supplier_name%',
                    [
                        '%supplier_name%' => $this->supplier->name,
                    ],
                    'Shop.Theme.Catalog'
                );
                $this->doProductSearch(
                    'catalog/listing/supplier',
                    ['entity' => 'supplier', 'id' => $this->supplier->id]
                );
            } else {
                $this->assignAll();
                $this->label = $this->trans(
                    'List of all suppliers',
                    [],
                    'Shop.Theme.Catalog'
                );
                $this->setTemplate('catalog/suppliers', ['entity' => 'suppliers']);
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    /**
     * @return ProductSearchQuery
     */
    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('supplier')
            ->setIdSupplier($this->supplier->id)
            ->setSortOrder(new SortOrder('product', 'position', 'asc'));

        return $query;
    }

    /**
     * @return SupplierProductSearchProvider
     */
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
        $supplierVar = $this->objectPresenter->present($this->supplier);

        // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
        $filteredSupplier = Hook::exec(
            'filterSupplierContent',
            ['object' => $supplierVar],
            null,
            false,
            true,
            false,
            null,
            true
        );
        if (!empty($filteredSupplier['object'])) {
            $supplierVar = $filteredSupplier['object'];
        }

        $this->context->smarty->assign([
            'supplier' => $supplierVar,
        ]);
    }

    /**
     * Assign template vars if displaying the supplier list.
     */
    protected function assignAll()
    {
        $suppliersVar = $this->getTemplateVarSuppliers();

        if (!empty($suppliersVar)) {
            foreach ($suppliersVar as $k => $supplier) {
                // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
                $filteredSupplier = Hook::exec(
                    'filterSupplierContent',
                    ['object' => $supplier],
                    null,
                    false,
                    true,
                    false,
                    null,
                    true
                );
                if (!empty($filteredSupplier['object'])) {
                    $suppliersVar[$k] = $filteredSupplier['object'];
                }
            }
        }

        $this->context->smarty->assign([
            'brands' => $suppliersVar,
        ]);
    }

    public function getTemplateVarSuppliers()
    {
        $suppliers = Supplier::getSuppliers(true, $this->context->language->id, true);
        $suppliers_for_display = [];

        foreach ($suppliers as $supplier) {
            $suppliers_for_display[$supplier['id_supplier']] = $supplier;
            $suppliers_for_display[$supplier['id_supplier']]['text'] = $supplier['description'];
            $suppliers_for_display[$supplier['id_supplier']]['image'] = $this->context->link->getSupplierImageLink($supplier['id_supplier'], 'small_default');
            $suppliers_for_display[$supplier['id_supplier']]['url'] = $this->context->link->getSupplierLink($supplier['id_supplier']);
            $suppliers_for_display[$supplier['id_supplier']]['nb_products'] = $supplier['nb_products'] > 1
                ? $this->trans('%number% products', ['%number%' => $supplier['nb_products']], 'Shop.Theme.Catalog')
                : $this->trans('%number% product', ['%number%' => $supplier['nb_products']], 'Shop.Theme.Catalog');
        }

        return $suppliers_for_display;
    }

    public function getListingLabel()
    {
        return $this->label;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('All suppliers', [], 'Shop.Theme.Catalog'),
            'url' => $this->context->link->getPageLink('supplier', true),
        ];

        if (!empty($this->supplier)) {
            $breadcrumb['links'][] = [
                'title' => $this->supplier->name,
                'url' => $this->context->link->getSupplierLink($this->supplier),
            ];
        }

        return $breadcrumb;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        if (!empty($this->supplier)) {
            $page['body_classes']['supplier-id-' . $this->supplier->id] = true;
            $page['body_classes']['supplier-' . $this->supplier->name] = true;
        }

        return $page;
    }

    /**
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }
}
