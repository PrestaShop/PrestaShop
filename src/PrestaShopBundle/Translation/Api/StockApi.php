<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Api;

class StockApi extends AbstractApi
{
    /**
     * @return string[] List of translations
     */
    public function getTranslations()
    {
        return array(
            'alert_bulk_edit' => $this->translator->trans('Use checkboxes to bulk edit quantities', array(), 'Admin.Catalog.Feature'),
            'button_advanced_filter' => $this->translator->trans('Advanced filters', array(), 'Admin.Catalog.Feature'),
            'button_apply_advanced_filter' => $this->translator->trans('Apply advanced filters', array(), 'Admin.Catalog.Feature'),
            'button_movement_type' => $this->translator->trans('Apply new quantity', array(), 'Admin.Catalog.Feature'),
            'button_search' => $this->translator->trans('Search', array(), 'Admin.Actions'),
            'filter_categories' => $this->translator->trans('Filter by categories', array(), 'Admin.Actions'),
            'filter_datepicker_from' => $this->translator->trans('From', array(), 'Admin.Global'),
            'filter_datepicker_to' => $this->translator->trans('To', array(), 'Admin.Global'),
            'filter_low_stock' => $this->translator->trans('Display products below low stock level first', array(), 'Admin.Catalog.Feature'),
            'filter_movements_type' => $this->translator->trans('Filter by movement type', array(), 'Admin.Catalog.Feature'),
            'filter_movements_employee' => $this->translator->trans('Filter by employee', array(), 'Admin.Catalog.Feature'),
            'filter_movements_period' => $this->translator->trans('Filter by period', array(), 'Admin.Catalog.Feature'),
            'filter_search_category' => $this->translator->trans('Search a category', array(), 'Admin.Catalog.Feature'),
            'filter_search_suppliers' => $this->translator->trans('Search a supplier', array(), 'Admin.Catalog.Feature'),
            'filter_status' => $this->translator->trans('Filter by status', array(), 'Admin.Catalog.Feature'),
            // 'All' refers to statuses as if "All statuses". Please adapt as necessary in your language
            'filter_status_all' => $this->translator->trans('All', array(), 'Admin.Catalog.Feature'),
            'filter_status_disable' => $this->translator->trans('Disabled', array(), 'Admin.Global'),
            'filter_status_enable' => $this->translator->trans('Enabled', array(), 'Admin.Global'),
            'filter_suppliers' => $this->translator->trans('Filter by supplier', array(), 'Admin.Catalog.Feature'),
            'head_title' => $this->translator->trans('Stock management', array(), 'Admin.Catalog.Feature'),
            'link_catalog' => $this->translator->trans('Catalog', array(), 'Admin.Navigation.Menu'),
            'link_movements' => $this->translator->trans('Movements', array(), 'Admin.Catalog.Feature'),
            'link_overview' => $this->translator->trans('Stock', array(), 'Admin.Global'),
            'link_stock' => $this->translator->trans('Stock management', array(), 'Admin.Catalog.Feature'),
            'menu_stock' => $this->translator->trans('Stock', array(), 'Admin.Global'),
            'menu_movements' => $this->translator->trans('Movements', array(), 'Admin.Catalog.Feature'),
            'no_product' => $this->translator->trans('No product matches your search. Try changing search terms.', array(), 'Admin.Catalog.Notification'),
            'none' => $this->translator->trans('None', array(), 'Admin.Catalog.Feature'),
            'notification_stock_updated' => $this->translator->trans('Stock successfully updated', array(), 'Admin.Catalog.Notification'),
            'product_search' => $this->translator->trans('Search products (search by name, reference, supplier)', array(), 'Admin.Catalog.Feature'),
            'product_low_stock' => $this->translator->trans('This product is below the low stock level you have defined.', array(), 'Admin.Catalog.Feature'),
            'product_low_stock_level' => $this->translator->trans('Low stock level:', array(), 'Admin.Catalog.Feature'),
            'product_low_stock_alert' => $this->translator->trans('Low stock alert:', array(), 'Admin.Catalog.Feature'),
            'title_available' => $this->translator->trans('Available', array(), 'Admin.Global'),
            'title_bulk' => $this->translator->trans('Bulk edit quantity', array(), 'Admin.Catalog.Feature'),
            'title_date' => $this->translator->trans('Date & Time', array(), 'Admin.Catalog.Feature'),
            'title_edit_quantity' => $this->translator->trans('Edit quantity', array(), 'Admin.Catalog.Feature'),
            'title_employee' => $this->translator->trans('Employee', array(), 'Admin.Global'),
            'title_import' => $this->translator->trans('Go to the import system', array(), 'Admin.Catalog.Feature'),
            'title_export' => $this->translator->trans('Export data into CSV', array(), 'Admin.Catalog.Feature'),
            'title_movements_type' => $this->translator->trans('Type', array(), 'Admin.Global'),
            'title_physical' => $this->translator->trans('Physical', array(), 'Admin.Catalog.Feature'),
            'title_product' => $this->translator->trans('Product', array(), 'Admin.Global'),
            'title_reference' => $this->translator->trans('Reference', array(), 'Admin.Global'),
            'title_reserved' => $this->translator->trans('Reserved', array(), 'Admin.Catalog.Feature'),
            'title_status' => $this->translator->trans('Status', array(), 'Admin.Global'),
            'title_supplier' => $this->translator->trans('Supplier', array(), 'Admin.Global'),
            'title_quantity' => $this->translator->trans('Quantity', array(), 'Admin.Global'),
            'tree_expand' => $this->translator->trans('Expand', array(), 'Admin.Actions'),
            'tree_reduce' => $this->translator->trans('Collapse', array(), 'Admin.Actions'),
        );
    }
}
