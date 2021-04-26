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

export default {
  bulks: {
    deleteCategories: '.js-delete-categories-bulk-action',
    deleteCategoriesModal: (id: string): string => `#${id}_grid_delete_categories_modal`,
    checkedCheckbox: '.js-bulk-action-checkbox:checked',
    deleteCustomers: '.js-delete-categories-bulk-action',
    deleteCustomerModal: (id: string): string => `#${id}_grid_delete_customers_modal`,
    submitDeleteCategories: '.js-submit-delete-categories',
    submitDeleteCustomers: '.js-submit-delete-customers',
    categoriesToDelete: '#delete_categories_categories_to_delete',
    customersToDelete: '#delete_customers_customers_to_delete',
  },
  rows: {
    categoryDeleteAction: '.js-delete-category-row-action',
    customerDeleteAction: '.js-delete-customer-row-action',
  },
  column: {},
  confirmModal: (id: string): string => `${id}-grid-confirm-modal`,
  gridTable: '.js-grid-table',
  dragHandler: 'js-drag-handle',
  specificGridTable: (id: string): string => `${id}_grid_table`,
  gridPosition: (id: string): string => `.js-${id}-position`,
  position: 'js-position',
};
