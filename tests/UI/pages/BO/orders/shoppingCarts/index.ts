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

  private readonly exportLink: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

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

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly sortColumnSpanButton: (column: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on shopping carts page
   */
  constructor() {
    super();

    this.pageTitle = `Shopping Carts • ${global.INSTALL.SHOP_NAME}`;
    this.alertSuccessBlockParagraph = '.alert-success';

    // Selectors
    this.exportLink = '#desc-cart-export';

    // Form selectors
    this.gridForm = '#cart_grid_panel';
    this.gridTableHeaderTitle = `${this.gridForm} .card-header`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} h3.card-header-title`;

    // Table selectors
    this.gridTable = '#cart_grid_table';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.column-filters`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='cart[${filterBy}]']`;
    this.filterDateFromColumn = `${this.filterRow} #local_cartFilter_a__date_add_0`;
    this.filterDateToColumn = `${this.filterRow} #local_cartFilter_a__date_add_1`;
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
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_cart';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = (number: number) => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: number) => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = (column: number) => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Filter methods */

  /**
   * Click on lint to export carts to a csv file
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async exportDataToCsv(page: Page): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.exportLink);
  }

  /**
   * Get all row information from shopping carts table
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<{ShoppingCartDetails}>}
   */
  async getCartFromTable(page: Page, row: number): Promise<ShoppingCartDetails> {
    return {
      id_cart: parseFloat(await this.getTextColumn(page, row, 'id_cart')),
      status: await this.getTextColumn(page, row, 'status'),
      lastname: await this.getTextColumn(page, row, 'c!lastname'),
      total: await this.getTextColumn(page, row, 'total'),
      carrier: await this.getTextColumn(page, row, 'ca!name'),
      date: await this.getTextColumn(page, row, 'date'),
      online: await this.getTextColumn(page, row, 'id_guest'),
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

    const cartDate = date.setDateFormat('yyyy-mm-dd', cart.date ?? '');
    const lastName = cart.lastname !== '--' ? `"${cart.lastname}"` : '';
    const status = cart.status !== 'Abandoned cart' ? cart.status : `"${cart.status}"`;
    const carrier = cart.carrier !== '--' ? `"${cart.carrier}"` : '';

    return `${cart.id_cart};`
      + `${status};`
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

      case 'status':
        columnSelector = this.tableColumStatus(row);
        break;

      case 'c!lastname':
        columnSelector = this.tableColumnCustomer(row);
        break;

      case 'total':
        columnSelector = this.tableColumnTotal(row);
        break;

      case 'ca!name':
        columnSelector = this.tableColumnCarrier(row);
        break;

      case 'date':
        columnSelector = this.tableColumnDate(row);
        break;

      case 'id_guest':
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
    // To confirm bulk delete action with dialog
    await this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.locator(this.bulkActionMenuButton).click(),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.locator(this.selectAllLink).click(),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.locator(this.bulkActionMenuButton).click(),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForURL(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination limit number to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.waitForSelectorAndClick(page, this.paginationItems(number));
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
    let columnSelector: string;

    switch (sortBy) {
      case 'id_cart':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'status':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'c!lastname':
        columnSelector = this.sortColumnDiv(4);
        break;

      case 'ca!name':
        columnSelector = this.sortColumnDiv(6);
        break;

      case 'date':
        columnSelector = this.sortColumnDiv(7);
        break;

      case 'id_guest':
        columnSelector = this.sortColumnDiv(8);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForURL(page, sortColumnButton);
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
