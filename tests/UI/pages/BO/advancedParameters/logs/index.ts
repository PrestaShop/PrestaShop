import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';

/**
 * Logs page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Logs extends BOBasePage {
  public readonly pageTitle: string;

  private readonly gridPanel: string;

  private readonly gridTitle: string;

  private readonly listForm: string;

  private readonly listTableRow: (row: number) => string;

  private readonly listTableColumn: (row: number, column: string) => string;

  private readonly gridActionButton: string;

  private readonly eraseAllButton: string;

  private readonly filterColumnInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly severityLevelSelect: string;

  private readonly sendEmailToInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on logs page
   */
  constructor() {
    super();

    this.pageTitle = 'Logs •';

    // List of logs
    this.gridPanel = '#logs_grid_panel';
    this.gridTitle = `${this.gridPanel} h3.card-header-title`;
    this.listForm = '#logs_grid';
    this.listTableRow = (row: number) => `${this.listForm} tbody tr:nth-child(${row})`;
    this.listTableColumn = (row: number, column: string) => `${this.listTableRow(row)} td.column-${column}`;
    this.gridActionButton = '#logs-grid-actions-button';
    this.eraseAllButton = '#logs_grid_action_delete_all_email_logs';

    // Filters
    this.filterColumnInput = (filterBy: string) => `${this.listForm} #logs_${filterBy}`;
    this.filterSearchButton = `${this.listForm} .grid-search-button`;
    this.filterResetButton = `${this.listForm} .grid-reset-button`;

    // Sort Selectors
    this.tableHead = `${this.listForm} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role='previous-page-link']`;

    // Logs by email selectors
    this.severityLevelSelect = '#form_logs_by_email';
    this.sendEmailToInput = '#form_logs_email_receivers';
    this.saveButton = '#main-div div.card-footer button';
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForURL(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTitle);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of logs
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterLogs(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumnInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          page,
          this.filterColumnInput(filterBy),
          value,
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row in table
   * @param column {string} Column name to get text content
   * @returns {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.listTableColumn(row, column));
  }

  /**
   * Erase all logs
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async eraseAllLogs(page: Page): Promise<string> {
    // Add listener to dialog to accept erase
    await this.dialogListener(page);

    await page.locator(this.gridActionButton).click();
    await this.waitForSelectorAndClick(page, this.eraseAllButton);

    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent: string = await this.getTextColumn(page, i, column);

      if (column === 'employee' && rowContent === 'N/A') {
        rowContent = '';
      }

      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.locator(this.sortColumnDiv(sortBy)).hover();
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  // Pagination methods
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

  /**
   * Filter logs by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value of date from to set on filter input
   * @param dateTo {string} Value of date to to set on filter input
   * @returns {Promise<void>}
   */
  async filterLogsByDate(page: Page, dateFrom: string, dateTo: string): Promise<void> {
    await page.locator(this.filterColumnInput('date_add_from')).fill(dateFrom);
    await page.locator(this.filterColumnInput('date_add_to')).fill(dateTo);
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  // Methods for logs by email form

  /**
   * Set minimum severity level
   * @param page {Page} Browser tab
   * @param severityLevel {string} Severity to select
   * @returns {Promise<string>}
   */
  async setMinimumSeverityLevel(page: Page, severityLevel: string): Promise<string> {
    await this.selectByVisibleText(page, this.severityLevelSelect, severityLevel);
    await this.waitForSelectorAndClick(page, this.saveButton);

    return this.getAlertBlockContent(page);
  }

  /**
   * Set email
   * @param page {Page} Browser tab
   * @param email {string} Email to set in the input
   * @returns {Promise<string>}
   */
  async setEmail(page: Page, email: string): Promise<string> {
    await this.setValue(page, this.sendEmailToInput, email);
    await this.waitForSelectorAndClick(page, this.saveButton);

    return this.getAlertBlockContent(page);
  }
}

export default new Logs();
