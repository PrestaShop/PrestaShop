import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Movements page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Movements extends BOBasePage {
  public readonly pageTitle: string;

  private readonly stocksNavItemLink: string;

  private readonly searchForm: string;

  private readonly searchInput: string;

  private readonly searchButton: string;

  private readonly gridTable: string;

  private readonly tableBody: string;

  private readonly tableRows: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableProductId: (row: number) => string;

  private readonly tableProductNameColumn: (row: number) => string;

  private readonly tableProductReferenceColumn: (row: number) => string;

  private readonly tableProductDateColumn: (row: number) => string;

  private readonly tableQuantityColumn: (row: number) => string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly productListLoading: string;

  private readonly paginationList: string;

  private readonly paginationListItem: string;

  private readonly paginationListItemLink: (id: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on movements page
   */
  constructor() {
    super();

    this.pageTitle = 'Stock •';

    // Header selectors
    this.stocksNavItemLink = '#head_tabs li:nth-child(1) > a';

    // Simple filter selectors
    this.searchForm = 'form.search-form';
    this.searchInput = `${this.searchForm} .search-input input.input`;
    this.searchButton = `${this.searchForm} button.search-button`;

    // Table selectors
    this.gridTable = '.stock-movements table.table';
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = (row: number) => `${this.tableRows}:nth-child(${row})`;
    this.tableProductId = (row: number) => `${this.tableRow(row)} td:nth-child(1)`;
    this.tableProductNameColumn = (row: number) => `${this.tableRow(row)} td:nth-child(2) div.media-body p`;
    this.tableProductReferenceColumn = (row: number) => `${this.tableRow(row)} td:nth-child(3)`;
    this.tableQuantityColumn = (row: number) => `${this.tableRow(row)} td:nth-child(5) span.qty-number`;
    this.tableProductDateColumn = (row: number) => `${this.tableRow(row)} td:nth-child(6)`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Loader
    this.productListLoading = `${this.tableRow(1)} td:nth-child(1) div.ps-loader`;

    // Pagination
    this.paginationList = 'nav ul.pagination';
    this.paginationListItem = `${this.paginationList} li.page-item`;
    this.paginationListItemLink = (id: number) => `${this.paginationListItem}:nth-child(${id}) a`;
  }

  /* Header methods */
  /**
   * Go to stocks page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabStocks(page: Page): Promise<void> {
    await page.click(this.stocksNavItemLink);
    await this.waitForVisibleSelector(page, `${this.stocksNavItemLink}.active`);
  }

  /**
   * Filter by a word
   * @param page {Page} Browser tab
   * @param value {string} Value to set on filter input
   * @returns {Promise<void>}
   */
  async simpleFilter(page: Page, value: string): Promise<void> {
    await page.type(this.searchInput, value);
    await Promise.all([
      page.click(this.searchButton),
      this.waitForVisibleSelector(page, this.productListLoading),
    ]);
    await this.waitForHiddenSelector(page, this.productListLoading);
  }

  /* Table methods */
  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    switch (column) {
      case 'product_id':
        return this.getTextContent(page, this.tableProductId(row));
      case 'product_name':
        // @ts-ignore
        return page.evaluate(
          (selector) => document.querySelector(selector)!.childNodes[0].textContent,
          this.tableProductNameColumn(row),
        );
      case 'reference':
        return this.getTextContent(page, this.tableProductReferenceColumn(row));
      case 'quantity':
        return (await this.getTextContent(page, this.tableQuantityColumn(row))).replace(' ', '');
      case 'date_add':
        return this.getTextContent(page, this.tableProductDateColumn(row));
      default:
        throw new Error(`${column} was not find as column in this table`);
    }
  }

  /**
   * Get number of element in movements grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return (await page.$$(this.tableRows)).length;
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    await this.waitForHiddenSelector(page, this.productListLoading);
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    await this.waitForHiddenSelector(page, this.productListLoading);
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.waitForSelectorAndClick(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForHiddenSelector(page, this.productListLoading);
  }

  /**
   * Paginate to page
   * @param page {Page} Browser tab
   * @param pageNumber {number} Value of page to go
   * @return {Promise<number>}
   */
  async paginateTo(page: Page, pageNumber: number = 1): Promise<number> {
    await page.click(this.paginationListItemLink(pageNumber));
    if (await this.elementVisible(page, this.productListLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }

    return this.getNumberFromText(page, `${this.paginationListItem}.active`);
  }
}

export default new Movements();
