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

namespace PrestaShopBundle\Translation\Api;

class StockApi extends AbstractApi
{
    /**
     * @return string[] List of translations
     */
    public function getTranslations()
    {
        return [
            'alert_bulk_edit' => $this->translator->trans('Use checkboxes to bulk edit quantities', [], 'Admin.Catalog.Feature'),
            'button_advanced_filter' => $this->translator->trans('Advanced filters', [], 'Admin.Catalog.Feature'),
            'button_apply_advanced_filter' => $this->translator->trans('Apply advanced filters', [], 'Admin.Catalog.Feature'),
            'button_movement_type' => $this->translator->trans('Apply new quantity', [], 'Admin.Catalog.Feature'),
            'button_search' => $this->translator->trans('Search', [], 'Admin.Actions'),
            'filter_categories' => $this->translator->trans('Filter by categories', [], 'Admin.Actions'),
            'filter_datepicker_from' => $this->translator->trans('From', [], 'Admin.Global'),
            'filter_datepicker_to' => $this->translator->trans('To', [], 'Admin.Global'),
            'filter_low_stock' => $this->translator->trans('Display products below low stock level first', [], 'Admin.Catalog.Feature'),
            'filter_movements_type' => $this->translator->trans('Filter by movement type', [], 'Admin.Catalog.Feature'),
            'filter_movements_employee' => $this->translator->trans('Filter by employee', [], 'Admin.Catalog.Feature'),
            'filter_movements_period' => $this->translator->trans('Filter by period', [], 'Admin.Catalog.Feature'),
            'filter_search_category' => $this->translator->trans('Search a category', [], 'Admin.Catalog.Feature'),
            'filter_search_suppliers' => $this->translator->trans('Search a supplier', [], 'Admin.Catalog.Feature'),
            'filter_status' => $this->translator->trans('Filter by status', [], 'Admin.Catalog.Feature'),
            // 'All' refers to statuses as if "All statuses". Please adapt as necessary in your language
            'filter_status_all' => $this->translator->trans('All', [], 'Admin.Catalog.Feature'),
            'filter_status_disable' => $this->translator->trans('Disabled', [], 'Admin.Global'),
            'filter_status_enable' => $this->translator->trans('Enabled', [], 'Admin.Global'),
            'filter_suppliers' => $this->translator->trans('Filter by supplier', [], 'Admin.Catalog.Feature'),
            'head_title' => $this->translator->trans('Stock management', [], 'Admin.Catalog.Feature'),
            'link_catalog' => $this->translator->trans('Catalog', [], 'Admin.Navigation.Menu'),
            'link_movements' => $this->translator->trans('Movements', [], 'Admin.Catalog.Feature'),
            'link_overview' => $this->translator->trans('Stock', [], 'Admin.Global'),
            'link_stock' => $this->translator->trans('Stock management', [], 'Admin.Catalog.Feature'),
            'menu_stock' => $this->translator->trans('Stock', [], 'Admin.Global'),
            'menu_movements' => $this->translator->trans('Movements', [], 'Admin.Catalog.Feature'),
            'no_product' => $this->translator->trans('No product matches your search. Try changing search terms.', [], 'Admin.Catalog.Notification'),
            'none' => $this->translator->trans('None', [], 'Admin.Catalog.Feature'),
            'notification_stock_updated' => $this->translator->trans('Stock successfully updated', [], 'Admin.Catalog.Notification'),
            'product_search' => $this->translator->trans('Search products (search by name, reference, supplier)', [], 'Admin.Catalog.Feature'),
            'product_low_stock' => $this->translator->trans('This product is below the low stock level you have defined.', [], 'Admin.Catalog.Feature'),
            'product_low_stock_level' => $this->translator->trans('Low stock level:', [], 'Admin.Catalog.Feature'),
            'product_low_stock_alert' => $this->translator->trans('Low stock alert:', [], 'Admin.Catalog.Feature'),
            'title_available' => $this->translator->trans('Available', [], 'Admin.Global'),
            'title_bulk' => $this->translator->trans('Bulk edit quantity', [], 'Admin.Catalog.Feature'),
            'title_date' => $this->translator->trans('Date & Time', [], 'Admin.Catalog.Feature'),
            'title_edit_quantity' => $this->translator->trans('Edit quantity', [], 'Admin.Catalog.Feature'),
            'title_employee' => $this->translator->trans('Employee', [], 'Admin.Global'),
            'title_import' => $this->translator->trans('Go to the import system', [], 'Admin.Catalog.Feature'),
            'title_export' => $this->translator->trans('Export data into CSV', [], 'Admin.Catalog.Feature'),
            'title_movements_type' => $this->translator->trans('Type', [], 'Admin.Global'),
            'title_physical' => $this->translator->trans('Physical', [], 'Admin.Catalog.Feature'),
            'title_product' => $this->translator->trans('Product', [], 'Admin.Global'),
            'title_reference' => $this->translator->trans('Reference', [], 'Admin.Global'),
            'title_reserved' => $this->translator->trans('Reserved', [], 'Admin.Catalog.Feature'),
            'title_status' => $this->translator->trans('Status', [], 'Admin.Global'),
            'title_supplier' => $this->translator->trans('Supplier', [], 'Admin.Global'),
            'title_quantity' => $this->translator->trans('Quantity', [], 'Admin.Global'),
            'tree_expand' => $this->translator->trans('Expand', [], 'Admin.Actions'),
            'tree_reduce' => $this->translator->trans('Collapse', [], 'Admin.Actions'),
        ];
    }
}
