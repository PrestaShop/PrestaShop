import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Outstanding page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Outstanding extends BOBasePage {
  public readonly pageTitle: string;

  private readonly gridTable: string;

  private readonly outstandingFilterColumnInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly tableColumnActionType: (row: number, column: string) => string;

  private readonly cardHeaderTitle: string;

  private readonly paginationLabel: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on outstanding page
   */
  constructor() {
    super();

    this.pageTitle = `Outstanding • ${global.INSTALL.SHOP_NAME}`;
    this.gridTable = '#outstanding_grid_table';

    // Filters
    this.outstandingFilterColumnInput = (filterBy: string) => `#outstanding_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;
    this.tableColumnActionType = (row: number, column: string) => `${this.tableColumn(row, column)} a`;

    this.cardHeaderTitle = '#outstanding_grid_panel .card-header-title';
    this.paginationLabel = '#outstanding_grid .col-form-label';
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationNextLink = '#outstanding_grid_panel [data-role=next-page-link]';
    this.paginationPreviousLink = '#outstanding_grid_panel [data-role=previous-page-link]';
  }

  /* Methods */
  /**
   * Reset filter in outstanding
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForURL(page, this.filterResetButton);
    }
  }

  /**
   * Get text from Column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Outstanding row in table
   * @returns {Promise<string>}
   */
  async getTextColumn(page: Page, columnName: string, row: number): Promise<string> {
    if (columnName === 'id_invoice') {
      return (await this.getNumberFromText(page, this.tableColumn(row, 'id_invoice'))).toString();
    }

    return this.getTextContent(page, this.tableColumn(row, columnName));
  }

  /**
   * Click on view order
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Outstanding row in table
   * @returns {Promise<void>}
   */
  async viewOrder(page: Page, columnName: string, row: number = 1): Promise<void> {
    await this.waitForSelectorAndClick(page, this.tableColumnActionType(row, columnName));
  }

  /**
   * Click on view invoice
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Outstanding row in table
   * @returns {Promise<string|null>}
   */
  async viewInvoice(page: Page, columnName: string, row: number = 1): Promise<string|null> {
    return this.clickAndWaitForDownload(page, this.tableColumnActionType(row, 'invoice'));
  }

  /**
   * Get the number of outstanding
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOutstanding(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.cardHeaderTitle);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
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

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Get number of outstanding in page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfOutstandingInPage(page: Page): Promise<number> {
    return (await page.$$(`${this.tableBody} tr`)).length;
  }

  /**
   * Get outstanding allowance total price
   * @param page {Page} Browser tab
   * @param row {number} Outstanding row on table
   * @returns {Promise<number>}
   */
  async getOutstandingAllowancePrice(page: Page, row: number): Promise<number> {
    // Delete the first character (currency symbol) before getting price ATI
    return parseFloat((await this.getTextColumn(page, 'outstanding_allow_amount', row)).substring(1));
  }

  /**
   * Get column content in all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    let rowContent: string;
    const rowsNumber = await this.getNumberOfOutstandingInPage(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      if (column === 'outstanding_allow_amount') {
        rowContent = (await this.getOutstandingAllowancePrice(page, i)).toString();
      } else {
        rowContent = await this.getTextColumn(page, column, i);
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Filter methods */
  /**
   * Filter table outstanding
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param columnName {string} Column name on table
   * @param value {?string|number} Column name on table
   * @returns {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, columnName: string, value: string): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.outstandingFilterColumnInput(columnName), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          page,
          this.outstandingFilterColumnInput(columnName),
          value,
        );
        break;
      default:
      // Do nothing
    }

    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Filter outstanding by date from and date to
   * @param page {Page} Browser tab
   * @param dateFrom {string} Date from to filter with
   * @param dateTo {string} Date to to filter with
   * @returns {Promise<void>}
   */
  async filterOutstandingByDate(page: Page, dateFrom: string, dateTo: string): Promise<void> {
    await page.type(this.outstandingFilterColumnInput('date_add_from'), dateFrom);
    await page.type(this.outstandingFilterColumnInput('date_add_to'), dateTo);
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }
}

export default new Outstanding();
