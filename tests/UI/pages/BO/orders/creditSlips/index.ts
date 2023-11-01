import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Credit slips page, contains functions that can be used on credit slips page
 * @class
 * @extends BOBasePage
 */
class CreditSlips extends BOBasePage {
  public readonly pageTitle: string;

  public readonly pageTitleFR: string;

  public readonly errorMessageWhenGenerateFileByDate: string;

  private readonly creditSlipGridPanel: string;

  private readonly creditSlipsGridTitle: string;

  private readonly creditSlipGridTable: string;

  private readonly filterResetButton: string;

  private readonly filterSearchButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationBlock: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly creditSlipsFilterColumnInput: (filterBy: string) => string;

  private readonly creditSlipsTableRow: (row: number) => string;

  private readonly creditSlipsTableColumn: (row: number, column: string) => string;

  private readonly creditSlipDownloadButton: (id: number) => string;

  private readonly generateByDateForm: string;

  private readonly dateFromInput: string;

  private readonly dateToInput: string;

  private readonly generatePdfByDateButton: string;

  private readonly creditSlipOptionsForm: string;

  private readonly invoicePrefixENInput: string;

  private readonly invoicePrefixFRInput: string;

  private readonly languageDropDownButton: string;

  private readonly invoicePrefixFrenchSelect: string;

  private readonly saveCreditSlipOptionsButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on credit slips page
   */
  constructor() {
    super();

    this.pageTitle = `Credit slips • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleFR = `Avoirs • ${global.INSTALL.SHOP_NAME}`;
    this.errorMessageWhenGenerateFileByDate = 'No order slips were found for this period.';
    this.successfulUpdateMessage = 'Update successful';

    // Credit slips page
    // List of credit slips
    this.creditSlipGridPanel = '#credit_slip_grid_panel';
    this.creditSlipsGridTitle = `${this.creditSlipGridPanel} h3.card-header-title`;
    this.creditSlipGridTable = '#credit_slip_grid_table';
    this.filterResetButton = `${this.creditSlipGridTable} .grid-reset-button`;
    this.filterSearchButton = `${this.creditSlipGridTable} .grid-search-button`;

    // Sort Credit Slip Selectors
    this.tableHead = `${this.creditSlipGridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationBlock = '.pagination-block';
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.creditSlipGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.creditSlipGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.creditSlipGridPanel} .pagination .previous a.page-link`;

    this.creditSlipsFilterColumnInput = (filterBy: string) => `#credit_slip_${filterBy}`;
    this.creditSlipsTableRow = (row: number) => `${this.creditSlipGridTable} tbody tr:nth-child(${row})`;
    this.creditSlipsTableColumn = (row: number, column: string) => `${this.creditSlipsTableRow(row)} td.column-${column}`;
    this.creditSlipDownloadButton = (id: number) => `${this.creditSlipGridTable} tr:nth-child(${id}) td.link-type.column-pdf`;

    // By date form
    this.generateByDateForm = '#form-generate-credit-slips-by-date';
    this.dateFromInput = '#generate_pdf_by_date_from';
    this.dateToInput = '#generate_pdf_by_date_to';
    this.generatePdfByDateButton = `${this.generateByDateForm} #generate-credit-slip-by-date`;

    // Credit slip options form
    this.creditSlipOptionsForm = '#form-credit-slips-options';
    this.invoicePrefixENInput = '#form_slip_prefix_1';
    this.invoicePrefixFRInput = '#form_slip_prefix_2';
    this.languageDropDownButton = '#form_slip_prefix_dropdown';
    this.invoicePrefixFrenchSelect = 'div.dropdown.show span[data-locale="fr"]';
    this.saveCreditSlipOptionsButton = `${this.creditSlipOptionsForm} #save-credit-slip-options-button`;
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
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.creditSlipsGridTitle);
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
   * Filter credit slips
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @returns {Promise<void>}
   */
  async filterCreditSlips(page: Page, filterBy: string, value: string = ''): Promise<void> {
    await this.setValue(page, this.creditSlipsFilterColumnInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Filter credit slips by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
   * @returns {Promise<void>}
   */
  async filterCreditSlipsByDate(page: Page, dateFrom: string, dateTo: string): Promise<void> {
    await page.locator(this.creditSlipsFilterColumnInput('date_issued_from')).fill(dateFrom);
    await page.locator(this.creditSlipsFilterColumnInput('date_issued_to')).fill(dateTo);
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Credit slip row on table
   * @param column {string} Column name to get
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCreditSlips(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.creditSlipsTableColumn(row, column));
  }

  /**
   * Download credit slip
   * @param page {Page} Browser tab
   * @param row {number} Credit slip row on table
   * @returns {Promise<string|null>}
   */
  downloadCreditSlip(page: Page, row: number = 1): Promise<string|null> {
    return this.clickAndWaitForDownload(page, this.creditSlipDownloadButton(row));
  }

  /**
   * Generate PDF by date and download it
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<string>}
   */
  async generatePDFByDateAndDownload(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<string|null> {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);

    return this.clickAndWaitForDownload(page, this.generatePdfByDateButton);
  }

  /**
   * Get message error after generate credit slip fail
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<string>}
   */
  async generatePDFByDateAndFail(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<string> {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);
    await page.click(this.generatePdfByDateButton);
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Set values to generate pdf by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<void>}
   */
  async setValuesForGeneratingPDFByDate(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<void> {
    if (dateFrom) {
      await this.setValue(page, this.dateFromInput, dateFrom);
    }

    if (dateTo) {
      await this.setValue(page, this.dateToInput, dateTo);
    }
  }

  /** Edit credit slip Prefix on FR and on EN
   * @param page {Page} Browser tab
   * @param prefixEN {string} Prefix on english language value to change
   * @param prefixFR {string} Prefix on french language value to change
   * @returns {Promise<void>}
   */
  async changePrefix(page: Page, prefixEN: string, prefixFR: string = prefixEN): Promise<void> {
    await this.setValue(page, this.invoicePrefixENInput, prefixEN);
    await this.waitForSelectorAndClick(page, this.languageDropDownButton);
    await this.waitForSelectorAndClick(page, this.invoicePrefixFrenchSelect);
    await this.setValue(page, this.invoicePrefixFRInput, prefixFR);
  }

  /**
   * Delete prefix
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async deletePrefix(page: Page): Promise<void> {
    await this.clearInput(page, this.invoicePrefixENInput);
    await this.waitForSelectorAndClick(page, this.languageDropDownButton);
    await this.waitForSelectorAndClick(page, this.invoicePrefixFrenchSelect);
    await this.clearInput(page, this.invoicePrefixFRInput);
  }

  /** Save credit slip options
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveCreditSlipOptions(page: Page): Promise<string> {
    await page.click(this.saveCreditSlipOptionsButton);
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

  /**
   * Get column content in all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    let rowContent: string;
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      rowContent = await this.getTextColumnFromTableCreditSlips(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
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
}

export default new CreditSlips();
