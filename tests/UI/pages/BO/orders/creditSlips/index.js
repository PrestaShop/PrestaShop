require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Credit slips page, contains functions that can be used on credit slips page
 * @class
 * @extends BOBasePage
 */
class CreditSlips extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on credit slips page
   */
  constructor() {
    super();

    this.pageTitle = 'Credit Slips •';
    this.pageTitleFR = 'Avoirs •';
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
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationBlock = '.pagination-block';
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.creditSlipGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.creditSlipGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.creditSlipGridPanel} .pagination .previous a.page-link`;

    this.creditSlipsFilterColumnInput = filterBy => `#credit_slip_${filterBy}`;
    this.creditSlipsTableRow = row => `${this.creditSlipGridTable} tbody tr:nth-child(${row})`;
    this.creditSlipsTableColumn = (row, column) => `${this.creditSlipsTableRow(row)} td.column-${column}`;
    this.creditSlipDownloadButton = id => `${this.creditSlipGridTable} tr:nth-child(${id}) td.link-type.column-pdf`;

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
  async resetFilter(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.creditSlipsGridTitle);
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
   * Filter credit slips
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @returns {Promise<void>}
   */
  async filterCreditSlips(page, filterBy, value = '') {
    await this.setValue(page, this.creditSlipsFilterColumnInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Filter credit slips by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
   * @returns {Promise<void>}
   */
  async filterCreditSlipsByDate(page, dateFrom, dateTo) {
    await page.type(this.creditSlipsFilterColumnInput('date_issued_from'), dateFrom);
    await page.type(this.creditSlipsFilterColumnInput('date_issued_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Credit slip row on table
   * @param column {string} Column name to get
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCreditSlips(page, row, column) {
    return this.getTextContent(page, this.creditSlipsTableColumn(row, column));
  }

  /**
   * Download credit slip
   * @param page {Page} Browser tab
   * @param row {number} Credit slip row on table
   * @returns {Promise<string>}
   */
  downloadCreditSlip(page, row = 1) {
    return this.clickAndWaitForDownload(page, this.creditSlipDownloadButton(row));
  }

  /**
   * Generate PDF by date and download it
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<string>}
   */
  async generatePDFByDateAndDownload(page, dateFrom = '', dateTo = '') {
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
  async generatePDFByDateAndFail(page, dateFrom = '', dateTo = '') {
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
  async setValuesForGeneratingPDFByDate(page, dateFrom = '', dateTo = '') {
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
  async changePrefix(page, prefixEN, prefixFR = prefixEN) {
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
  async deletePrefix(page) {
    await this.clearInput(page, this.invoicePrefixENInput);
    await this.waitForSelectorAndClick(page, this.languageDropDownButton);
    await this.waitForSelectorAndClick(page, this.invoicePrefixFrenchSelect);
    await this.clearInput(page, this.invoicePrefixFRInput);
  }

  /** Save credit slip options
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async saveCreditSlipOptions(page) {
    await this.clickAndWaitForNavigation(page, this.saveCreditSlipOptionsButton);
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

  /**
   * Get column content in all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    let rowContent;
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];
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
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForNavigation({waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }
}

module.exports = new CreditSlips();
