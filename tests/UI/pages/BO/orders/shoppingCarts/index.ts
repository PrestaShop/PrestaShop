import BOBasePage from '@pages/BO/BObasePage';
import date from '@utils/date';
import {ShoppingCartDetails} from '@data/types/shoppingCart';

import type {Page} from 'playwright';

/**
 * Shopping carts page, contains functions that can be used on shopping carts page
 * @class
 * @extends BOBasePage
 */
class ShoppingCarts extends BOBasePage {
  public readonly pageTitle: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly selectAllRowsDiv: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterDateFromColumn: string;

  private readonly filterDateToColumn: string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableColumnId: (row: number) => string;

  private readonly tableColumnOrderId: (row: number) => string;

  private readonly tableColumStatus: (row: number) => string;

  private readonly tableColumnCustomer: (row: number) => string;

  private readonly tableColumnTotal: (row: number) => string;

  private readonly tableColumnCarrier: (row: number) => string;

  private readonly tableColumnDate: (row: number) => string;

  private readonly tableColumnOnline: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsViewLink: (row: number) => string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly gridActionButton: string;

  private readonly gridActionDropDownMenu: string;

  private readonly gridActionExportLink: string;

  private readonly bulkDeleteModal: string;

  private readonly bulkDeleteModalDeleteButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on shopping carts page
   */
  constructor() {
    super();

    this.pageTitle = `Shopping Carts • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.gridForm = '#cart_grid_panel';
    this.gridTableHeaderTitle = `${this.gridForm} .card-header`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} h3.card-header-title`;

    // Table selectors
    this.gridTable = '#cart_grid_table';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.column-filters`;
    this.selectAllRowsDiv = `${this.filterRow} .grid_bulk_action_select_all`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='cart[${filterBy}]']`;
    this.filterDateFromColumn = `${this.filterRow} #cart_date_add_from`;
    this.filterDateToColumn = `${this.filterRow} #cart_date_add_to`;
    this.filterSearchButton = `${this.filterRow} td[data-column-id="actions"] button[name="cart[actions][search]"]`;
    this.filterResetButton = 'div.js-grid-reset-button button';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = (row: number) => `${this.tableBodyColumn(row)}.column-id_cart`;
    this.tableColumnOrderId = (row: number) => `${this.tableBodyColumn(row)}.column-id_order`;
    this.tableColumStatus = (row: number) => `${this.tableBodyColumn(row)}.column-status span.badge`;
    this.tableColumnCustomer = (row: number) => `${this.tableBodyColumn(row)}.column-customer_name`;
    this.tableColumnTotal = (row: number) => `${this.tableBodyColumn(row)}.column-cart_total`;
    this.tableColumnCarrier = (row: number) => `${this.tableBodyColumn(row)}.column-carrier_name`;
    this.tableColumnDate = (row: number) => `${this.tableBodyColumn(row)}.column-date_add`;
    this.tableColumnOnline = (row: number) => `${this.tableBodyColumn(row)}.column-customer_online`;
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)}.column-actions`;
    this.tableColumnActionsViewLink = (row: number) => `${this.tableColumnActions(row)} a.grid-view-row-link`;

    // Bulk actions selectors
    this.bulkActionsToggleButton = `${this.gridForm} button.dropdown-toggle.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridForm} #cart_grid_bulk_action_delete_selection`;

    // Grid Actions
    this.gridActionButton = '#cart-grid-actions-button';
    this.gridActionDropDownMenu = '#cart-grid-actions-dropdown-menu';
    this.gridActionExportLink = '#cart-grid-action-export';

    // Modal Dialog
    this.bulkDeleteModal = '#cart-grid-confirm-modal.show';
    this.bulkDeleteModalDeleteButton = `${this.bulkDeleteModal} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridForm} .col-form-label`;
    this.paginationNextLink = `${this.gridForm} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridForm} [data-role=previous-page-link]`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Filter methods */

  /**
   * Click on lint to export carts to a csv file
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
   * Get all row information from shopping carts table
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<{ShoppingCartDetails}>}
   */
  async getCartFromTable(page: Page, row: number): Promise<ShoppingCartDetails> {
    return {
      id_cart: parseInt(await this.getTextColumn(page, row, 'id_cart'), 10),
      id_order: parseInt(await this.getTextColumn(page, row, 'id_order'), 10),
      status: await this.getTextColumn(page, row, 'status'),
      lastname: await this.getTextColumn(page, row, 'customer_name'),
      total: await this.getTextColumn(page, row, 'total'),
      carrier: await this.getTextColumn(page, row, 'carrier_name'),
      date: await this.getTextColumn(page, row, 'date_add'),
      online: await this.getTextColumn(page, row, 'customer_online'),
    };
  }

  /**
   * Get shopping cart from table in csv format
   * @param page {Page} Browser tab
   * @param row {number} Shopping cart row on table
   * @returns {Promise<string>}
   */
  async getCartInCsvFormat(page: Page, row: number): Promise<string> {
    const cart = await this.getCartFromTable(page, row);

    const cartDate = date.setDateFormat('yyyy-mm-dd', cart.date ?? '')
      .replace(' ', '');
    const lastName = cart.lastname !== '--' ? `"${cart.lastname?.replace(' ', '')}"` : '';
    const carrier = cart.carrier !== '--' ? `"${cart.carrier?.replace(' ', '')}"` : '';

    return `${cart.id_cart};`
      + `${cart.id_order};`
      + `${lastName};`
      + `${cart.total};`
      + `${carrier};`
      + `"${cartDate}";`
      + `${cart.online === 'No' ? 0 : 1}`;
  }

  /**
   * Get Number of shopping carts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Reset and get number of shopping carts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter shopping carts
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
   * @returns {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;

      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Filter by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
   * @returns {Promise<void>}
   */
  async filterByDate(page: Page, dateFrom: string, dateTo: string): Promise<void> {
    await page.locator(this.filterDateFromColumn).fill(dateFrom);
    await page.locator(this.filterDateToColumn).fill(dateTo);
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Shopping cart row on table
   * @param columnName {string} Column name of the value to return
   * @returns {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector;

    switch (columnName) {
      case 'id_cart':
        columnSelector = this.tableColumnId(row);
        break;

      case 'id_order':
        columnSelector = this.tableColumnOrderId(row);
        break;

      case 'status':
        columnSelector = this.tableColumStatus(row);
        break;

      case 'customer_name':
        columnSelector = this.tableColumnCustomer(row);
        break;

      case 'total':
        columnSelector = this.tableColumnTotal(row);
        break;

      case 'carrier_name':
        columnSelector = this.tableColumnCarrier(row);
        break;

      case 'date_add':
        columnSelector = this.tableColumnDate(row);
        break;

      case 'customer_online':
        columnSelector = this.tableColumnOnline(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Bulk actions methods */
  /**
   * Bulk delete shopping carts
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteShoppingCarts(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsDiv).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.bulkActionsDeleteButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkDeleteModal}.show`),
    ]);
    await this.clickAndWaitForLoadState(page, this.bulkDeleteModalDeleteButton);
    await this.elementNotVisible(page, this.bulkDeleteModal);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination limit number to select
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

  // Sort methods
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @returns {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Go to view page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToViewPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsViewLink(row));
  }
}

export default new ShoppingCarts();
