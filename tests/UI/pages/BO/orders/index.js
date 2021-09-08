require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Orders page, contains functions that can be used on orders page
 * @class
 * @extends BOBasePage
 */
class Order extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on orders page
   */
  constructor() {
    super();

    this.pageTitle = 'Orders •';

    // Header selectors
    this.createNewOrderButton = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#order_grid_panel';
    this.gridTable = '#order_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Filters
    this.filterColumn = filterBy => `${this.gridTable} #order_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    this.tableColumnStatus = row => `${this.tableRow(row)} td.column-osname`;
    this.updateStatusInTableButton = row => `${this.tableColumnStatus(row)} button`;
    this.updateStatusInTableDropdown = row => `${this.tableColumnStatus(row)} div.js-choice-options`;
    this.updateStatusInTableDropdownChoice = (row, statusId) => `${this.updateStatusInTableDropdown(row)}`
      + ` button[data-value='${statusId}']`;

    // Column actions selectors
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.viewRowLink = row => `${this.actionsColumn(row)} a.grid-view-row-link`;
    this.viewInvoiceRowLink = row => `${this.actionsColumn(row)} a.grid-view-invoice-row-link`;
    this.viewDeliverySlipsRowLink = row => `${this.actionsColumn(row)} a.grid-view-delivery-slip-row-link`;

    // Grid Actions
    this.gridActionButton = '#order-grid-actions-button';
    this.gridActionDropDownMenu = '#order-grid-actions-dropdown-menu';
    this.gridActionExportLink = '#order-grid-action-export';

    // Bulk actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkUpdateOrdersStatusButton = '#order_grid_bulk_action_change_order_status';
    this.tableColumnOrderBulk = row => `${this.tableRow(row)} td.column-orders_bulk`;
    this.tableColumnOrderBulkCheckboxLabel = row => `${this.tableColumnOrderBulk(row)} .md-checkbox`;

    // Order status modal
    this.updateOrdersStatusModal = '#changeOrdersStatusModal';
    this.updateOrdersStatusModalSelect = '#change_orders_status_new_order_status_id';
    this.updateOrdersStatusModalButton = `${this.updateOrdersStatusModal} .modal-footer .js-submit-modal-form-btn`;
  }

  /*
  Methods
   */
  /**
   * Go to create new order page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCreateOrderPage(page) {
    await this.clickAndWaitForNavigation(page, this.createNewOrderButton);
  }

  /**
   * Click on lint to export orders to a csv file
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async exportDataToCsv(page) {
    await Promise.all([
      page.click(this.gridActionButton),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);

    const [downloadPath] = await Promise.all([
      this.clickAndWaitForDownload(page, this.gridActionExportLink),
      this.waitForHiddenSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);

    return downloadPath;
  }

  /**
   * Filter Orders
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
   * @returns {Promise<void>}
   */
  async filterOrders(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Filter orders by date from and date to
   * @param page {Page} Browser tab
   * @param dateFrom {string} Date from to filter with
   * @param dateTo {string} Date to to filter with
   * @returns {Promise<void>}
   */
  async filterOrdersByDate(page, dateFrom, dateTo) {
    await page.type(this.filterColumn('date_add_from'), dateFrom);
    await page.type(this.filterColumn('date_add_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Reset filter in orders
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Get number of orders in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Go to orders Page
   * @param page {Page} Browser tab
   * @param orderRow {number} Order row on table
   * @returns {Promise<void>}
   */
  async goToOrder(page, orderRow) {
    await this.clickAndWaitForNavigation(page, this.viewRowLink(orderRow));
  }

  /**
   * Get text from Column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Order row in table
   * @returns {Promise<string>}
   */
  async getTextColumn(page, columnName, row) {
    if (columnName === 'osname') {
      return this.getTextContent(page, this.updateStatusInTableButton(row));
    }
    return this.getTextContent(page, this.tableColumn(row, columnName));
  }

  /**
   * Get all row information from orders table
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<{id: number, reference: string, newClient:string, delivery: string, customer: string,
   * totalPaid: string, payment: string, status: string}>}
   */
  async getOrderFromTable(page, row) {
    return {
      id: parseFloat(await this.getTextColumn(page, 'id_order', row)),
      reference: await this.getTextColumn(page, 'reference', row),
      newClient: await this.getTextColumn(page, 'new', row),
      delivery: await this.getTextColumn(page, 'country_name', row),
      customer: await this.getTextColumn(page, 'customer', row),
      totalPaid: await this.getTextColumn(page, 'total_paid_tax_incl', row),
      payment: await this.getTextColumn(page, 'payment', row),
      status: await this.getTextColumn(page, 'osname', row),
    };
  }

  /**
   * Get column content in all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, column, i);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Get order from table in csv format
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<string>}
   */
  async getOrderInCsvFormat(page, row) {
    const order = await this.getOrderFromTable(page, row);

    return `${order.id};`
      + `${order.reference};`
      + `${order.newClient === 'Yes' ? 1 : 0};`
      + `${order.delivery.split(' ').length > 1 ? `"${order.delivery}";` : `${order.delivery};`}`
      + `"${order.customer}";`
      + `${order.totalPaid};`
      + `${order.payment.split(' ').length > 1 ? `"${order.payment}";` : `${order.payment};`}`
      + `${order.status.split(' ').length > 1 ? `"${order.status}";` : `${order.status};`}`;
  }

  /**
   * Set order status
   * @param page {Page} Browser tab
   * @param row {number} Order row in table
   * @param status {{id: number, name: string}} Order status on table
   * @returns {Promise<string>}
   */
  async setOrderStatus(page, row, status) {
    await Promise.all([
      page.click(this.updateStatusInTableButton(row)),
      this.waitForVisibleSelector(page, `${this.updateStatusInTableDropdown(row)}.show`),
    ]);
    await this.clickAndWaitForNavigation(
      page,
      this.updateStatusInTableDropdownChoice(row, status.id),
    );
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Click on view invoice to download it
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<string>}
   */
  downloadInvoice(page, row) {
    return this.clickAndWaitForDownload(page, this.viewInvoiceRowLink(row));
  }

  /**
   * Click on view delivery slip to download it
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<string>}
   */
  downloadDeliverySlip(page, row) {
    return this.clickAndWaitForDownload(page, this.viewDeliverySlipsRowLink(row));
  }

  /**
   * Click on customer link to open view page in a new tab
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<Page>} New browser tab to work with
   */
  viewCustomer(page, row) {
    return this.openLinkWithTargetBlank(
      page,
      `${this.tableColumn(row, 'customer')} a`,
      this.userProfileIcon,
    );
  }

  /**
   * Get order total price
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {number}
   */
  async getOrderATIPrice(page, row) {
    // Delete the first character (currency symbol) before getting price ATI
    return parseFloat((await this.getTextColumn(page, 'total_paid_tax_incl', row)).substring(1));
  }


  /* Bulk actions methods */

  /**
   * Bulk change orders status
   * @param page {Page} Browser tab
   * @param status {string} New status to give to orders
   * @param isAllOrders {boolean} True if want to update all orders status
   * @param rows {Array<number>|boolean} Array of which orders rows to change (if allOrders = false)
   * @returns {Promise<string>}
   */
  async bulkUpdateOrdersStatus(page, status, isAllOrders = true, rows = []) {
    // Select all orders or some
    if (isAllOrders) {
      await Promise.all([
        page.click(this.selectAllRowsLabel),
        this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
      ]);
    } else {
      for (let i = 0; i < rows.length; i++) {
        await page.click(this.tableColumnOrderBulkCheckboxLabel(rows[i]));
      }
      await this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`);
    }
    // Open bulk actions button
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on change order status button
    await Promise.all([
      page.click(this.bulkUpdateOrdersStatusButton),
      this.waitForVisibleSelector(page, `${this.updateOrdersStatusModal}:not([aria-hidden='true'])`),
    ]);
    // Select new orders status in modal and confirm update
    await this.selectByVisibleText(page, this.updateOrdersStatusModalSelect, status);
    await this.clickAndWaitForNavigation(page, this.updateOrdersStatusModalButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }


  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @returns {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }
}

module.exports = new Order();
