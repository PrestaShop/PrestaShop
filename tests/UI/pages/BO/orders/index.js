require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Order extends BOBasePage {
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
    this.filterSearchButton = `${this.gridTable} button[name='order[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='order[actions][reset]']`;
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
    this.viewRowLink = row => `${this.actionsColumn(row)} a[data-original-title='View']`;
    this.viewInvoiceRowLink = row => `${this.actionsColumn(row)} a[data-original-title='View invoice']`;
    this.viewDeliverySlipsRowLink = row => `${this.actionsColumn(row)} a[data-original-title='View delivery slip']`;
    // Grid Actions
    this.gridActionButton = '#order-grid-actions-button';
    this.gridActionDropDownMenu = 'div.dropdown-menu[aria-labelledby=\'order-grid-actions-button\']';
    this.gridActionExportLink = `${this.gridActionDropDownMenu} a[href*='/export']`;
    // Bulk actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
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
   * @param page
   * @return {Promise<void>}
   */
  async goToCreateOrderPage(page) {
    await this.clickAndWaitForNavigation(page, this.createNewOrderButton);
  }


  /**
   * Click on lint to export orders to a csv file
   * @param page
   * @return {Promise<void>}
   */
  async exportDataToCsv(page) {
    await Promise.all([
      page.click(this.gridActionButton),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.gridActionExportLink),
      this.waitForHiddenSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);
    return download.path();
  }

  /**
   * Filter Orders
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @return {Promise<void>}
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
   * Reset filter in orders
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Get number of orders in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Go to orders Page
   * @param page
   * @param orderRow
   * @return {Promise<void>}
   */
  async goToOrder(page, orderRow) {
    await this.clickAndWaitForNavigation(page, this.viewRowLink(orderRow));
  }

  /**
   * Get text from Column
   * @param page
   * @param columnName
   * @param row
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
   * @param page
   * @param row
   * @returns {Promise<{reference: string, newClient: string, delivery: string,
   * totalPaid: string, payment: string, id: *, customer: string, status: string}>}
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
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, column, i);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Get order from table in csv format
   * @param page
   * @param row
   * @return {Promise<string>}
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
   * @param page
   * @param row, order row in table
   * @param status, object{id, status} from demo orderStatuses
   * @return {Promise<string>}
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
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async downloadInvoice(page, row) {
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.viewInvoiceRowLink(row)),
    ]);
    return download.path();
  }

  /**
   * Click on view delivery slip to download it
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async downloadDeliverySlip(page, row) {
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.viewDeliverySlipsRowLink(row)),
    ]);
    return download.path();
  }


  /**
   * Click on customer link to open view page in a new tab
   * @param page
   * @param row
   * @return {Promise<*>}, new browser tab to work with
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
   * @param page
   * @param row
   * @return {number}
   */
  async getOrderATIPrice(page, row) {
    // Delete the first character (currency symbol) before getting price ATI
    return parseFloat((await this.getTextColumn(page, 'total_paid_tax_incl', row)).substring(1));
  }


  /* Bulk actions methods */

  /**
   * Bulk change orders status
   * @param page
   * @param status, new status to give to orders
   * @param allOrders, true if want to change all selectors
   * @param rows, array of which orders rows to change (if allOrders = false)
   * @return {Promise<string>}
   */
  async bulkUpdateOrdersStatus(page, status, allOrders = true, rows = []) {
    // Select all orders or some
    if (allOrders) {
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
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
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
