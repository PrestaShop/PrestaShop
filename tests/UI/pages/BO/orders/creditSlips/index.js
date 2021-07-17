require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CreditSlips extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Credit Slips •';
    this.errorMessageWhenGenerateFileByDate = 'No order slips were found for this period.';
    this.successfulUpdateMessage = 'Update successful';

    // Credit slips page
    // List of credit slips
    this.creditSlipGridPanel = '#credit_slip_grid_panel';
    this.creditSlipsGridTitle = `${this.creditSlipGridPanel} h3.card-header-title`;
    this.creditSlipGridTable = '#credit_slip_grid_table';
    this.filterResetButton = `${this.creditSlipGridTable} button[name='credit_slip[actions][reset]']`;
    this.filterSearchButton = `${this.creditSlipGridTable} button[name='credit_slip[actions][search]']`;
    this.creditSlipsFilterColumnInput = filterBy => `#credit_slip_${filterBy}`;
    this.creditSlipsTableRow = row => `${this.creditSlipGridTable} tbody tr:nth-child(${row})`;
    this.creditSlipsTableColumn = (row, column) => `${this.creditSlipsTableRow(row)} td.column-${column}`;
    this.creditSlipDownloadButton = id => `${this.creditSlipGridTable} tr:nth-child(${id}) td.link-type.column-pdf`;
    // By date form
    this.generateByDateForm = '[name=\'generate_pdf_by_date\']';
    this.dateFromInput = '#generate_pdf_by_date_from';
    this.dateToInput = '#generate_pdf_by_date_to';
    this.generatePdfByDateButton = `${this.generateByDateForm} .btn.btn-primary`;
    // Credit slip options form
    this.creditSlipOptionsForm = '[name=\'form\']';
    this.invoicePrefixInput = '#form_options_slip_prefix_1';
    this.saveCreditSlipOptionsButton = `${this.creditSlipOptionsForm} .btn.btn-primary`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.creditSlipsGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter credit slips
   * @param page
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCreditSlips(page, filterBy, value = '') {
    await this.setValue(page, this.creditSlipsFilterColumnInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Filter credit slips by date
   * @param page
   * @param dateFrom
   * @param dateTo
   * @return {Promise<void>}
   */
  async filterCreditSlipsByDate(page, dateFrom, dateTo) {
    await page.type(this.creditSlipsFilterColumnInput('date_issued_from'), dateFrom);
    await page.type(this.creditSlipsFilterColumnInput('date_issued_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCreditSlips(page, row, column) {
    return this.getTextContent(page, this.creditSlipsTableColumn(row, column));
  }

  /**
   * Download credit slip
   * @param page
   * @param lineNumber
   * @return {Promise<*>}
   */
  async downloadCreditSlip(page, lineNumber = 1) {
    const [download] = await Promise.all([
      page.waitForEvent('download'), // wait for download to start
      page.click(this.creditSlipDownloadButton(lineNumber)),
    ]);
    return download.path();
  }

  /**
   * Generate PDF by date and download it
   * @param page
   * @param dateFrom
   * @param dateTo
   * @return {Promise<*>}
   */
  async generatePDFByDateAndDownload(page, dateFrom = '', dateTo = '') {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);

    const [download] = await Promise.all([
      page.waitForEvent('download'), // wait for download to start
      page.click(this.generatePdfByDateButton),
    ]);
    return download.path();
  }

  /**
   * Get message error after generate credit slip fail
   * @param page
   * @param dateFrom
   * @param dateTo
   * @return {Promise<string>}
   */
  async generatePDFByDateAndFail(page, dateFrom = '', dateTo = '') {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);
    await page.click(this.generatePdfByDateButton);
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Set values to generate pdf by date
   * @param page
   * @param dateFrom
   * @param dateTo
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

  /** Edit credit slip Prefix
   * @param page
   * @param prefix
   * @return {Promise<void>}
   */
  async changePrefix(page, prefix) {
    await this.setValue(page, this.invoicePrefixInput, prefix);
  }

  /** Save credit slip options
   * @param page
   * @return {Promise<void>}
   */
  async saveCreditSlipOptions(page) {
    await this.clickAndWaitForNavigation(page, this.saveCreditSlipOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}
module.exports = new CreditSlips();
