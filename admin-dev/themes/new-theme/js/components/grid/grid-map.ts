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
    deleteCustomers: '.js-delete-customers-bulk-action',
    deleteCustomerModal: (id: string): string => `#${id}_grid_delete_customers_modal`,
    submitDeleteCategories: '.js-submit-delete-categories',
    submitDeleteCustomers: '.js-submit-delete-customers',
    categoriesToDelete: '#delete_categories_categories_to_delete',
    customersToDelete: '#delete_customers_customers_to_delete',
    actionSelectAll: '.js-bulk-action-select-all',
    bulkActionCheckbox: '.js-bulk-action-checkbox',
    bulkActionBtn: '.js-bulk-actions-btn',
    openTabsBtn: '.js-bulk-action-btn.open_tabs',
    tableChoiceOptions: 'table.table .js-choice-options',
    choiceOptions: '.js-choice-options',
    modalFormSubmitBtn: '.js-bulk-modal-form-submit-btn',
    submitAction: '.js-bulk-action-submit-btn',
    ajaxAction: '.js-bulk-action-ajax-btn',
    gridSubmitAction: '.js-grid-action-submit-btn',
  },
  rows: {
    categoryDeleteAction: '.js-delete-category-row-action',
    customerDeleteAction: '.js-delete-customer-row-action',
    linkRowAction: '.js-link-row-action',
    linkRowActionClickableFirst:
      '.js-link-row-action[data-clickable-row=1]:first',
    clickableTd: 'td.clickable',
    imageTypeDeleteAction: '.js-delete-image-type-row-action',
    deleteImageTypeModal: (id: string): string => `#${id}_grid_delete_image_type_modal`,
    submitDeleteImageType: '.js-submit-delete-image-type',
  },
  actions: {
    showQuery: '.js-common_show_query-grid-action',
    exportQuery: '.js-common_export_sql_manager-grid-action',
    showModalForm: (id: string): string => `#${id}_common_show_query_modal_form`,
    showModalGrid: (id: string): string => `#${id}_grid_common_show_query_modal`,
    modalFormSubmitBtn: '.js-bulk-modal-form-submit-btn',
    submitModalFormBtn: '.js-submit-modal-form-btn',
    bulkInputsBlock: (id: string): string => `#${id}`,
    tokenInput: (id: string): string => `input[name="${id}[_token]"]`,
    ajaxBulkActionConfirmModal: (id: string, bulkAction: string): string => `${id}-ajax-${bulkAction}-confirm-modal`,
    ajaxBulkActionProgressModal: (id: string, bulkAction: string): string => `${id}-ajax-${bulkAction}-progress-modal`,
  },
  position: (id: string): string => `.js-${id}-position:first`,
  confirmModal: (id: string): string => `${id}-grid-confirm-modal`,
  gridTable: '.js-grid-table',
  dragHandler: '.js-drag-handle',
  dragHandlerClass: 'js-drag-handle',
  specificGridTable: (id: string): string => `${id}_grid_table`,
  grid: (id: string): string => `#${id}_grid`,
  gridPanel: '.js-grid-panel',
  gridHeader: '.js-grid-header',
  gridPosition: (id: string): string => `.js-${id}-position`,
  gridTablePosition: (id: string): string => `.js-grid-table .js-${id}-position`,
  gridPositionFirst: (id: string): string => `.js-${id}-position:first`,
  selectPosition: 'js-position',
  togglableRow: '.ps-togglable-row',
  dropdownItem: '.js-dropdown-item',
  table: 'table.table',
  headerToolbar: '.header-toolbar',
  breadcrumbItem: '.breadcrumb-item',
  resetSearch: '.js-reset-search',
  expand: '.js-expand',
  collapse: '.js-collapse',
  columnFilters: '.column-filters',
  gridSearchButton: '.grid-search-button',
  gridResetButton: '.grid-reset-button',
  inputAndSelect: 'input:not(.js-bulk-action-select-all), select',
  previewToggle: '.preview-toggle',
  previewRow: '.preview-row',
  gridTbody: '.grid-table tbody',
  trNotPreviewRow: 'tr:not(.preview-row)',
  commonRefreshListAction: '.js-common_refresh_list-grid-action',
  filterForm: (id: string): string => `#${id}_filter_form`,
  onDragClass: 'position-row-while-drag',
  sqlSubmit: '.btn-sql-submit',
};
