require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class CreditSlips extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Credit Slips •';
    this.errorMessageWhenGenerateFileByDate = 'No order slips were found for this period.';
    this.successfulUpdateMessage = 'Update successful';

    // Credit slips page
    // List of credit slips
    this.creditSlipGridPanel = '#credit_slip_grid_panel';
    this.creditSlipsGridTitle = `${this.creditSlipGridPanel} h3.card-header-title`;
    this.creditSlipGridTable = '#credit_slip_grid_table';
    this.filterResetButton = `${this.creditSlipGridTable} .grid-reset-button`;
    this.filterSearchButton = `${this.creditSlipGridTable} .grid-search-button`;
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
    this.invoicePrefixInput = '#form_slip_prefix_1';
    this.saveCreditSlipOptionsButton = `${this.creditSlipOptionsForm} #save-credit-slip-options-button`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.creditSlipsGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /**
   * Filter credit slips
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCreditSlips(filterBy, value = '') {
    await this.setValue(this.creditSlipsFilterColumnInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Filter credit slips by date
   * @param dateFrom
   * @param dateTo
   * @return {Promise<void>}
   */
  async filterCreditSlipsByDate(dateFrom, dateTo) {
    await this.page.type(this.creditSlipsFilterColumnInput('date_issued_from'), dateFrom);
    await this.page.type(this.creditSlipsFilterColumnInput('date_issued_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableCreditSlips(row, column) {
    return this.getTextContent(this.creditSlipsTableColumn(row, column));
  }

  /**
   * Download credit slip
   * @param lineNumber
   * @return {Promise<*>}
   */
  async downloadCreditSlip(lineNumber = 1) {
    const [download] = await Promise.all([
      this.page.waitForEvent('download'), // wait for download to start
      this.page.click(this.creditSlipDownloadButton(lineNumber)),
    ]);
    return download.path();
  }

  /**
   * Generate PDF by date and download it
   * @param dateFrom
   * @param dateTo
   * @return {Promise<*>}
   */
  async generatePDFByDateAndDownload(dateFrom = '', dateTo = '') {
    await this.setValuesForGeneratingPDFByDate(dateFrom, dateTo);

    const [download] = await Promise.all([
      this.page.waitForEvent('download'), // wait for download to start
      this.page.click(this.generatePdfByDateButton),
    ]);
    return download.path();
  }

  /**
   * Get message error after generate credit slip fail
   * @param dateFrom
   * @param dateTo
   * @return {Promise<string>}
   */
  async generatePDFByDateAndFail(dateFrom = '', dateTo = '') {
    await this.setValuesForGeneratingPDFByDate(dateFrom, dateTo);
    await this.page.click(this.generatePdfByDateButton);
    return this.getTextContent(this.alertTextBlock);
  }

  /**
   * Set values to generate pdf by date
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<void>}
   */
  async setValuesForGeneratingPDFByDate(dateFrom = '', dateTo = '') {
    if (dateFrom) {
      await this.setValue(this.dateFromInput, dateFrom);
    }

    if (dateTo) {
      await this.setValue(this.dateToInput, dateTo);
    }
  }

  /** Edit credit slip Prefix
   * @param prefix
   * @return {Promise<void>}
   */
  async changePrefix(prefix) {
    await this.setValue(this.invoicePrefixInput, prefix);
  }

  /** Save credit slip options
   * @return {Promise<void>}
   */
  async saveCreditSlipOptions() {
    await this.clickAndWaitForNavigation(this.saveCreditSlipOptionsButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
