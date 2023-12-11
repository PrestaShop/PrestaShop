import BOBasePage from '@pages/BO/BObasePage';

import type OrderStatusData from '@data/faker/orderStatus';

import type {Page} from 'playwright';

/**
 * Orders page, contains functions that can be used on orders page
 * @class
 * @extends BOBasePage
 */
class Order extends BOBasePage {
  public readonly pageTitle: string;

  private readonly createNewOrderButton: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly gridHeaderTitle: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly tableColumnStatus: (row: number) => string;

  private readonly updateStatusInTableButton: (row: number) => string;

  private readonly updateStatusInTableDropdown: (row: number) => string;

  private readonly updateStatusInTableDropdownChoice: (row: number, statusId: number) => string;

  private readonly expandIcon: (row: number) => string;

  private readonly previewRow: string;

  private readonly shippingDetails: string;

  private readonly customerEmail: string;

  private readonly invoiceDetails: string;

  private readonly productTable: string;

  private readonly productsNumber: string;

  private readonly productRowFromTable: (row: number) => string;

  private readonly previewMoreProductsLink: (row: number) => string;

  private readonly previewOrderButton: string;

  private readonly actionsColumn: (row: number) => string;

  private readonly viewRowLink: (row: number) => string;

  private readonly viewInvoiceRowLink: (row: number) => string;

  private readonly viewDeliverySlipsRowLink: (row: number) => string;

  private readonly gridActionButton: string;

  private readonly gridActionDropDownMenu: string;

  private readonly gridActionExportLink: string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkUpdateOrdersStatusButton: string;

  private readonly bulkOpenInNewTabsButton: string;

  private readonly tableColumnOrderBulk: (row: number) => string;

  private readonly tableColumnOrderBulkCheckboxLabel: (row: number) => string;

  private readonly updateOrdersStatusModal: string;

  private readonly updateOrdersStatusModalSelect: string;

  private readonly updateOrdersStatusModalButton: string;

  private readonly paginationBlock: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

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
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Filters
    this.filterColumn = (filterBy: string) => `${this.gridTable} #order_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;
    this.tableColumnStatus = (row: number) => `${this.tableRow(row)} td.column-osname`;
    this.updateStatusInTableButton = (row: number) => `${this.tableColumnStatus(row)}.choice-type.text-left > div > button`;
    this.updateStatusInTableDropdown = (row: number) => `${this.tableColumnStatus(row)} div.js-choice-options`;
    this.updateStatusInTableDropdownChoice = (row: number, statusId: number) => `${this.updateStatusInTableDropdown(row)}`
      + ` button[data-value='${statusId}']`;
    // Preview row
    this.expandIcon = (row: number) => `${this.tableRow(row)} span.preview-toggle`;
    this.previewRow = `${this.tableBody} tr.preview-row td div[data-role=preview-row]`;
    this.shippingDetails = `${this.previewRow} div[data-role=shipping-details]`;
    this.customerEmail = `${this.previewRow} div[data-role=email]`;
    this.invoiceDetails = `${this.previewRow} div[data-role=invoice-details]`;
    this.productTable = `${this.previewRow} table[data-role=product-table]`;
    this.productsNumber = `${this.productTable} thead tr:nth-child(1)`;
    this.productRowFromTable = (row: number) => `${this.productTable} tbody tr:nth-child(${row})`;
    this.previewMoreProductsLink = (row: number) => `${this.productRowFromTable(row)} td a.js-preview-more-products-btn`;
    this.previewOrderButton = `${this.gridTable} tr.preview-row a.btn-primary`;

    // Column actions selectors
    this.actionsColumn = (row: number) => `${this.tableRow(row)} td.column-actions`;
    this.viewRowLink = (row: number) => `${this.actionsColumn(row)} a.grid-view-row-link`;
    this.viewInvoiceRowLink = (row: number) => `${this.actionsColumn(row)} a.grid-view-invoice-row-link`;
    this.viewDeliverySlipsRowLink = (row: number) => `${this.actionsColumn(row)} a.grid-view-delivery-slip-row-link`;

    // Grid Actions
    this.gridActionButton = '#order-grid-actions-button';
    this.gridActionDropDownMenu = '#order-grid-actions-dropdown-menu';
    this.gridActionExportLink = '#order-grid-action-export';

    // Bulk actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkUpdateOrdersStatusButton = '#order_grid_bulk_action_change_order_status';
    this.bulkOpenInNewTabsButton = '#order_grid_bulk_action_open_tabs';
    this.tableColumnOrderBulk = (row: number) => `${this.tableRow(row)} td.column-orders_bulk`;
    this.tableColumnOrderBulkCheckboxLabel = (row: number) => `${this.tableColumnOrderBulk(row)} .md-checkbox`;

    // Order status modal
    this.updateOrdersStatusModal = '#changeOrdersStatusModal';
    this.updateOrdersStatusModalSelect = '#change_orders_status_new_order_status_id';
    this.updateOrdersStatusModalButton = `${this.updateOrdersStatusModal} .modal-footer .js-submit-modal-form-btn`;

    // Pagination selectors
    this.paginationBlock = '.pagination-block';
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role=previous-page-link]`;
  }

  /*
  Methods
   */
  /**
   * Go to create new order page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCreateOrderPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.createNewOrderButton);
  }

  /**
   * Click on lint to export orders to a csv file
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async exportDataToCsv(page: Page): Promise<string | null> {
    await Promise.all([
      page.locator(this.gridActionButton).click(),
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
  async filterOrders(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    await this.resetFilter(page);
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await page.locator(this.filterSearchButton).click();
    await page.waitForURL(`**/?order**filters**${filterBy}**`, {waitUntil: 'domcontentloaded'});
  }

  /**
   * Filter orders by date from and date to
   * @param page {Page} Browser tab
   * @param dateFrom {string} Date from to filter with
   * @param dateTo {string} Date to filter with
   * @returns {Promise<void>}
   */
  async filterOrdersByDate(page: Page, dateFrom: string, dateTo: string): Promise<void> {
    await page.locator(this.filterColumn('date_add_from')).fill(dateFrom);
    await page.locator(this.filterColumn('date_add_to')).fill(dateTo);
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Reset filter in orders
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForURL(page, this.filterResetButton);
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Get number of orders in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Go to orders Page
   * @param page {Page} Browser tab
   * @param orderRow {number} Order row on table
   * @returns {Promise<void>}
   */
  async goToOrder(page: Page, orderRow: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.viewRowLink(orderRow));
  }

  /**
   * Get text from Column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Order row in table
   * @returns {Promise<string>}
   */
  async getTextColumn(page: Page, columnName: string, row: number = 1): Promise<string> {
    if (columnName === 'osname') {
      return this.getTextContent(page, this.updateStatusInTableButton(row));
    }
    if (columnName === 'id_order') {
      return (await this.getNumberFromText(page, this.tableColumn(row, 'id_order'))).toString();
    }

    return this.getTextContent(page, this.tableColumn(row, columnName));
  }

  /**
   * Get order ID from table
   * @param page {Page} Browser tab
   * @param row {number} Order row in table
   * @returns {Promise<number>}
   */
  async getOrderIDNumber(page: Page, row: number = 1): Promise<number> {
    return this.getNumberFromText(page, this.tableColumn(row, 'id_order'));
  }

  /**
   * Get all row information from orders table
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<{id: number, reference: string, newClient:string, delivery: string, customer: string,
   * totalPaid: string, payment: string, status: string}>}
   */
  async getOrderFromTable(page: Page, row: number) {
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
   * Get number of orders in page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfOrdersInPage(page: Page): Promise<number> {
    return (await page.$$(`${this.tableBody} tr`)).length;
  }

  /**
   * Get column content in all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    let rowContent: string;
    const rowsNumber: number = await this.getNumberOfOrdersInPage(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      if (column === 'total_paid_tax_incl') {
        rowContent = (await this.getOrderATIPrice(page, i)).toString();
      } else {
        rowContent = await this.getTextColumn(page, column, i);
      }
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
  async getOrderInCsvFormat(page: Page, row: number): Promise<string> {
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
   * @param status {OrderStatusData} Order status on table
   * @returns {Promise<string>}
   */
  async setOrderStatus(page: Page, row: number, status: OrderStatusData): Promise<string> {
    await Promise.all([
      page.locator(this.updateStatusInTableButton(row)).click(),
      this.waitForVisibleSelector(page, `${this.updateStatusInTableDropdown(row)}.show`),
    ]);
    await this.clickAndWaitForURL(page, this.updateStatusInTableDropdownChoice(row, status.id));
    await this.elementNotVisible(page, this.updateStatusInTableDropdownChoice(row, status.id), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Click on view invoice to download it
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<string|null>}
   */
  async downloadInvoice(page: Page, row: number): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.viewInvoiceRowLink(row));
  }

  /**
   * Click on view delivery slip to download it
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<string|null>}
   */
  async downloadDeliverySlip(page: Page, row: number): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.viewDeliverySlipsRowLink(row));
  }

  /**
   * Click on customer link to open view page in a new tab
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<Page>} New browser tab to work with
   */
  async viewCustomer(page: Page, row: number): Promise<Page> {
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
   * @returns {Promise<number>}
   */
  async getOrderATIPrice(page: Page, row: number = 1): Promise<number> {
    // Delete the first character (currency symbol) before getting price ATI
    return parseFloat((await this.getTextColumn(page, 'total_paid_tax_incl', row)).substring(1));
  }

  /* Bulk actions methods */
  /**
   * Select all orders
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async selectAllOrders(page: Page): Promise<void> {
    await Promise.all([
      this.waitForSelectorAndClick(page, this.selectAllRowsLabel),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
  }

  /**
   * Select some orders
   * @param page {Page} Browser tab
   * @param rows {Array<number>} Array of which orders rows to change
   * @returns {Promise<void>}
   */
  async selectOrdersRows(page: Page, rows: number[] = []): Promise<void> {
    for (let i = 0; i < rows.length; i++) {
      await page.locator(this.tableColumnOrderBulkCheckboxLabel(rows[i])).click();
    }
    await this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`);
  }

  /**
   * Click on bulk actions button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnBulkActionsButton(page: Page): Promise<void> {
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
  }

  /**
   * Bulk open in new tabs
   * @param page {Page} Browser tab
   * @param isAllOrders {boolean} True if want to update all orders status
   * @param rows {Array<number>} Array of which orders rows to change (if allOrders = false)
   * @returns {Promise<Page>}
   */
  async bulkOpenInNewTabs(page: Page, isAllOrders: boolean = true, rows: number[] = []): Promise<Page> {
    // Select all orders or some
    if (isAllOrders) {
      await this.selectAllOrders(page);
    } else {
      await this.selectOrdersRows(page, rows);
    }

    // Open bulk actions button
    await this.clickOnBulkActionsButton(page);

    // Click on open in new tabs
    return this.openLinkWithTargetBlank(page, this.bulkOpenInNewTabsButton);
  }

  /**
   * Bulk change orders status
   * @param page {Page} Browser tab
   * @param status {string} New status to give to orders
   * @param isAllOrders {boolean} True if want to update all orders status
   * @param rows {Array<number>|boolean} Array of which orders rows to change (if allOrders = false)
   * @returns {Promise<string>}
   */
  async bulkUpdateOrdersStatus(page: Page, status: string, isAllOrders: boolean = true, rows: number[] = []): Promise<string> {
    // Select all orders or some
    if (isAllOrders) {
      await this.selectAllOrders(page);
    } else {
      await this.selectOrdersRows(page, rows);
    }

    // Open bulk actions button
    await this.clickOnBulkActionsButton(page);

    // Click on change order status button
    await Promise.all([
      page.locator(this.bulkUpdateOrdersStatusButton).click(),
      this.waitForVisibleSelector(page, `${this.updateOrdersStatusModal}:not([aria-hidden='true'])`),
    ]);

    // Select new orders status in modal and confirm update
    await this.selectByVisibleText(page, this.updateOrdersStatusModalSelect, status);
    await page.locator(this.updateOrdersStatusModalButton).click();
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
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  /* Preview order methods */
  /**
   * Preview order
   * @param page {Page} Browser tab
   * @param row {number} Row in orders table
   * @returns {Promise<boolean>}
   */
  async previewOrder(page: Page, row: number = 1): Promise<boolean> {
    await page.hover(this.tableColumn(row, 'id_order'));
    await this.waitForSelectorAndClick(page, this.expandIcon(row));

    return this.elementVisible(page, this.previewRow, 2000);
  }

  /**
   * Get shipping details
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getShippingDetails(page: Page): Promise<string> {
    return this.getTextContent(page, this.shippingDetails);
  }

  /**
   * Get customer email
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCustomerEmail(page: Page): Promise<string> {
    return this.getTextContent(page, this.customerEmail);
  }

  /**
   * Get customer invoice address
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCustomerInvoiceAddressDetails(page: Page): Promise<string> {
    return this.getTextContent(page, this.invoiceDetails);
  }

  /**
   * Get products number from table
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductsNumberFromTable(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.productsNumber);
  }

  /**
   * Get product details from table
   * @param page {Page} Browser tab
   * @param row {number} Row in products table
   * @returns {Promise<string>}
   */
  async getProductDetailsFromTable(page: Page, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.productRowFromTable(row));
  }

  /**
   * Click on more link
   * @param page {Page} Browser tab
   * @param row {number} Row in Products table
   * @returns {Promise<void>}
   */
  async clickOnMoreLink(page: Page, row: number = 12): Promise<void> {
    await this.waitForSelectorAndClick(page, this.previewMoreProductsLink(row));
    await this.waitForVisibleSelector(page, this.productRowFromTable(row - 1));
  }

  /**
   * Open orders details
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async openOrderDetails(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.previewOrderButton);
  }
}

export default new Order();
