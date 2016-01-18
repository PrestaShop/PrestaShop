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

 use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
 use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
 use PrestaShop\PrestaShop\Adapter\Supplier\SupplierProductSearchProvider;
 use PrestaShop\PrestaShop\Adapter\Translator;
 use PrestaShop\PrestaShop\Adapter\LegacyContext;

 class SupplierControllerCore extends ProductListingFrontController
 {
     public $php_self = 'supplier';

    /** @var Supplier */
    protected $supplier;

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
                $this->assignSupplier();
                $this->doProductSearch('catalog/supplier.tpl');
            } else {
                $this->assignAll();
                $this->setTemplate('catalog/suppliers.tpl');
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

     protected function getProductSearchQuery()
     {
         $query = new ProductSearchQuery;
         $query
            ->setIdSupplier($this->supplier->id)
            ->setSortOrder(new SortOrder('product', 'position', 'asc'))
        ;
         return $query;
     }

     protected function getDefaultProductSearchProvider()
     {
         $translator = new Translator(new LegacyContext);
         return new SupplierProductSearchProvider(
            $translator,
            $this->supplier
        );
     }

    /**
     * Assign template vars if displaying one supplier
     */
    protected function assignSupplier()
    {
        $this->context->smarty->assign([
            'supplier' => $this->objectSerializer->toArray($this->supplier)
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
