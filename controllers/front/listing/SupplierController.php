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
use PrestaShop\PrestaShop\Adapter\Presenter\Supplier\SupplierPresenter;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class SupplierControllerCore extends ProductListingFrontController
{
    /** @var string */
    public $php_self = 'supplier';

    /** @var Supplier|null */
    protected $supplier;
    protected $label;

    /** @var SupplierPresenter */
    protected $supplierPresenter;

    public function canonicalRedirection(string $canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->supplier)) {
            parent::canonicalRedirection($this->context->link->getSupplierLink($this->supplier));
        } elseif ($canonicalURL) {
            parent::canonicalRedirection($canonicalURL);
        }
    }

    /**
     * Returns canonical URL for current supplier or a supplier list
     *
     * @return string
     */
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

        // Initialize presenter, we will use it for all cases
        $this->supplierPresenter = new SupplierPresenter($this->context->link);

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
     * Gets the product search query for the controller. This is a set of information that
     * a filtering module or the default provider will use to fetch our products.
     *
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
     * Default product search provider used if no filtering module stood up for the job
     *
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
        $supplierVar = $this->supplierPresenter->present(
            $this->supplier,
            $this->context->language
        );

        $filteredSupplier = Hook::exec(
            'filterSupplierContent',
            ['object' => $supplierVar],
            $id_module = null,
            $array_return = false,
            $check_exceptions = true,
            $use_push = false,
            $id_shop = null,
            $chain = true
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
                $filteredSupplier = Hook::exec(
                    'filterSupplierContent',
                    ['object' => $supplier],
                    $id_module = null,
                    $array_return = false,
                    $check_exceptions = true,
                    $use_push = false,
                    $id_shop = null,
                    $chain = true
                );
                if (!empty($filteredSupplier['object'])) {
                    $suppliersVar[$k] = $filteredSupplier['object'];
                }
            }
        }

        $this->context->smarty->assign([
            'suppliers' => $suppliersVar,
        ]);
    }

    public function getTemplateVarSuppliers()
    {
        $suppliers = Supplier::getSuppliers(true, $this->context->language->id, true);

        foreach ($suppliers as &$supplier) {
            $supplier = $this->supplierPresenter->present(
                $supplier,
                $this->context->language
            );
        }

        return $suppliers;
    }

    public function getListingLabel()
    {
        return $this->label;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->trans('Suppliers', [], 'Shop.Theme.Catalog'),
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

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
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
