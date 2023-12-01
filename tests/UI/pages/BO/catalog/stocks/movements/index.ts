import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import type {PageFunction} from 'playwright-core/types/structs';

/**
 * Movements page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Movements extends BOBasePage {
  public readonly pageTitle: string;

  public readonly emptyTableMessage: string;

  private readonly stocksNavItemLink: string;

  private readonly searchForm: string;

  private readonly searchInput: string;

  private readonly searchButton: string;

  private readonly advFiltersContainer: string;

  private readonly advFiltersBlock: string;

  private readonly advFiltersButton: string;

  private readonly advFiltersFilterMvtTypeSelect: string;

  private readonly advFiltersFilterEmployeeSelect: string;

  private readonly advFiltersFilterDateSupInput: string;

  private readonly advFiltersFilterDateInfInput: string;

  private readonly advFiltersFilterCategoriesExpandBtn: string;

  private readonly advFiltersFilterCategoriesLabel: string;

  private readonly advFiltersFilterStatusEnabled: string;

  private readonly advFiltersFilterStatusDisabled: string;

  private readonly advFiltersFilterStatusAll: string;

  private readonly gridTable: string;

  private readonly tableBody: string;

  private readonly tableRows: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableRowEmpty: string;

  private readonly tableProductId: (row: number) => string;

  private readonly tableProductNameColumn: (row: number) => string;

  private readonly tableProductReferenceColumn: (row: number) => string;

  private readonly tableTypeColumn: (row: number) => string;

  private readonly tableTypeColumnLink: (row: number) => string;

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

    this.pageTitle = `Stock • ${global.INSTALL.SHOP_NAME}`;
    this.emptyTableMessage = 'No product matches your search. Try changing search terms.';

    // Header selectors
    this.stocksNavItemLink = '#head_tabs li:nth-child(1) > a';

    // Simple filter selectors
    this.searchForm = 'form.search-form';
    this.searchInput = `${this.searchForm} .search-input input.input`;
    this.searchButton = `${this.searchForm} button.search-button`;

    // Advanced filter
    this.advFiltersContainer = '#filters-container';
    this.advFiltersBlock = '#filters';
    this.advFiltersButton = `${this.advFiltersContainer} button[data-target="${this.advFiltersBlock}"]`;
    this.advFiltersFilterMvtTypeSelect = `${this.advFiltersBlock} #id_stock_mvt_reason select`;
    this.advFiltersFilterEmployeeSelect = `${this.advFiltersBlock} #id_employee select`;
    this.advFiltersFilterDateSupInput = `${this.advFiltersBlock} div.date input.datepicker-sup`;
    this.advFiltersFilterDateInfInput = `${this.advFiltersBlock} div.date input.datepicker-inf`;
    this.advFiltersFilterCategoriesExpandBtn = `${this.advFiltersBlock} .filter-categories button[data-action="expand"]`;
    this.advFiltersFilterCategoriesLabel = `${this.advFiltersBlock} .filter-categories ul.tree li.tree-item span.tree-label`;
    this.advFiltersFilterStatusEnabled = `${this.advFiltersBlock} label[for="enable"]`;
    this.advFiltersFilterStatusDisabled = `${this.advFiltersBlock} label[for="disable"]`;
    this.advFiltersFilterStatusAll = `${this.advFiltersBlock} label[for="all"]`;

    // Table selectors
    this.gridTable = '.stock-movements table.table';
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = (row: number) => `${this.tableRows}:nth-child(${row})`;
    this.tableRowEmpty = `${this.tableRow(1)} div.ps-alert.alert.alert-warning`;
    this.tableProductId = (row: number) => `${this.tableRow(row)} td:nth-child(1)`;
    this.tableProductNameColumn = (row: number) => `${this.tableRow(row)} td:nth-child(2) div.media-body p`;
    this.tableProductReferenceColumn = (row: number) => `${this.tableRow(row)} td:nth-child(3)`;
    this.tableTypeColumn = (row: number) => `${this.tableRow(row)} td:nth-child(4)`;
    this.tableTypeColumnLink = (row: number) => `${this.tableTypeColumn(row)} a`;
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
    await page.locator(this.searchInput).fill(value);
    await Promise.all([
      page.click(this.searchButton),
      this.waitForVisibleSelector(page, this.productListLoading),
    ]);
    await this.waitForHiddenSelector(page, this.productListLoading);
  }

  /**
   * Display advanced filter
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async setAdvancedFiltersVisible(page: Page): Promise<void> {
    if (await this.elementNotVisible(page, this.advFiltersBlock, 2000)) {
      await page.click(this.advFiltersButton);
      await this.elementVisible(page, this.advFiltersBlock, 2000);
    }
  }

  /**
   * Return if Advanced Filter block is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isAdvancedFiltersVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.advFiltersBlock, 2000);
  }

  /**
   * Get choices from the advanced filter "Movement Type"
   * @param page {Page} Browser tab
   * @return {Promise<string[]>}
   */
  async getAdvancedFiltersMovementTypeChoices(page: Page): Promise<string[]> {
    await this.elementVisible(page, this.advFiltersFilterMvtTypeSelect, 5000);

    return page
      .locator(`${this.advFiltersFilterMvtTypeSelect} option`)
      .allTextContents();
  }

  /**
   * Set Filter "Categories"
   * @param page {Page} Browser tab
   * @param categoryName {string} Name of the category
   * @param status {boolean} Status of the checkbox
   * @return {Promise<void>}
   */
  async setAdvancedFiltersCategory(page: Page, categoryName: string, status: boolean = true): Promise<void> {
    await page.click(this.advFiltersFilterCategoriesExpandBtn);

    // Choose category to filter with
    const args = {
      selector: this.advFiltersFilterCategoriesLabel,
      categoryName,
      status,
    };
    const fn: {
      categoryClick: PageFunction<{
        selector: string,
        categoryName: string,
        status: boolean
        // eslint-disable-next-line no-eval
      }, boolean>
    } = eval(`({
      async categoryClick(args) {
        /* eslint-env browser */
        const allCategories = [...await document.querySelectorAll(args.selector)];
        const category = await allCategories.find((el) => el.textContent === args.categoryName);

        if (category === undefined) {
          return false;
        }

        const checkbox = await category.parentNode.querySelector('input');
        if (checkbox.checked !== args.status) {
          checkbox.click();
        }

        return true;
      }
    })`);
    const found = await page.evaluate(fn.categoryClick, args);

    if (!found) {
      throw new Error(`${categoryName} not found as a category`);
    }
    if (await this.elementVisible(page, this.productListLoading, 5000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }
    await page.waitForTimeout(10000);
  }

  /**
   * Set Filter "Date"
   * @param page {Page} Browser tab
   * @param type {'inf'|'sup'} Type
   * @param date {string} Date
   * @param onChange {boolean} Dispatch event change
   * @return {Promise<void>}
   */
  async setAdvancedFiltersDate(page: Page, type: 'inf' | 'sup', date: string, onChange: boolean = false): Promise<void> {
    const selector: string = type === 'inf' ? this.advFiltersFilterDateInfInput : this.advFiltersFilterDateSupInput;

    await this.waitForVisibleSelector(page, selector);
    await this.setValueOnDateTimePickerInput(page, selector, date, onChange);
    if (onChange) {
      if (await this.elementVisible(page, this.productListLoading, 5000)) {
        await this.waitForHiddenSelector(page, this.productListLoading);
      }
      await page.waitForTimeout(10000);
    }
  }

  /**
   * Set Filter "Employee"
   * @param page {Page} Browser tab
   * @param employeeName {string} Employee Name
   * @return {Promise<void>}
   */
  async setAdvancedFiltersEmployee(page: Page, employeeName: string): Promise<void> {
    await this.waitForVisibleSelector(page, this.advFiltersFilterEmployeeSelect);
    await this.selectByVisibleText(page, this.advFiltersFilterEmployeeSelect, employeeName, true);
    await page.waitForResponse('**/api/stock-movements/**');
    if (await this.elementVisible(page, this.productListLoading, 5000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }
    await page.waitForTimeout(10000);
  }

  /**
   * Set Filter "Movement Type"
   * @param page {Page} Browser tab
   * @param movementType {'None'|'Employee Edition'|'Customer Order'} Movement type
   * @return {Promise<void>}
   */
  async setAdvancedFiltersMovementType(page: Page, movementType: 'None' | 'Employee Edition' | 'Customer Order'): Promise<void> {
    await this.waitForVisibleSelector(page, this.advFiltersFilterMvtTypeSelect);
    await this.selectByVisibleText(page, this.advFiltersFilterMvtTypeSelect, movementType);
    if (await this.elementVisible(page, this.productListLoading, 5000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }
    await page.waitForTimeout(10000);
  }

  /**
   * Set Filter "Status"
   * @param page {Page} Browser tab
   * @param status {boolean|null} Status
   * @return {Promise<void>}
   */
  async setAdvancedFiltersStatus(page: Page, status: boolean | null): Promise<void> {
    let selector: string;

    if (status === null) {
      selector = this.advFiltersFilterStatusAll;
    } else {
      selector = status ? this.advFiltersFilterStatusEnabled : this.advFiltersFilterStatusDisabled;
    }

    await this.waitForVisibleSelector(page, selector);
    await page.click(selector);
    if (await this.elementVisible(page, this.productListLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }
    await page.waitForTimeout(10000);
  }

  /**
   * Reset Advanced filter
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetAdvancedFilter(page: Page): Promise<void> {
    await this.reloadPage(page);
    await page.waitForResponse('**/api/stock-movements/**');
    await this.waitForHiddenSelector(page, this.productListLoading, 30000);
  }

  /**
   * Click on edit feature
   * @param page {Page} Browser tab
   * @param row {number} Feature row in table
   * @return {Promise<Page>}
   */
  async clickOnMovementTypeLink(page: Page, row: number): Promise<Page> {
    await this.waitForVisibleSelector(page, this.tableTypeColumnLink(row));
    return this.openLinkWithTargetBlank(page, this.tableTypeColumnLink(row));
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
    let productAttribute = '';

    if (await this.elementVisible(page, `${this.tableProductNameColumn(row)} small`, 1000)) {
      productAttribute = await this.getTextContent(page, `${this.tableProductNameColumn(row)} small`);
    }
    switch (column) {
      case 'product_id':
        return this.getTextContent(page, this.tableProductId(row));
      case 'product_name':
        return (await this.getTextContent(page, this.tableProductNameColumn(row))).replace(productAttribute, '');
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
   Get text for empty table
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getTextForEmptyTable(page: Page): Promise<string> {
    return this.getTextContent(page, this.tableRowEmpty);
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
