require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Invoices page, contains functions that can be used on invoices page
 * @class
 * @extends BOBasePage
 */
class Invoice extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on invoices page
   */
  constructor() {
    super();

    this.pageTitle = 'Invoices';
    this.errorMessageWhenGenerateFileByDate = 'No invoice has been found for this period.';
    this.errorMessageWhenGenerateFileByStatus = 'No invoice has been found for this status.';
    this.errorMessageWhenNotSelectStatus = 'You must select at least one order status.';
    this.successfulUpdateMessage = 'Update successful';

    // Invoices page

    // By date form
    this.generateByDateForm = '#form-generate-invoices-by-date';
    this.dateFromInput = `${this.generateByDateForm} #form_date_from`;
    this.dateToInput = `${this.generateByDateForm} #form_date_to`;
    this.generatePdfByDateButton = `${this.generateByDateForm} #generate-pdf-by-date-button`;

    // By order status form
    this.generateByStatusForm = '#form-generate-invoices-by-status';
    this.statusOrderStateSpan = `${this.generateByStatusForm} span.status-name`;
    this.generatePdfByStatusButton = `${this.generateByStatusForm} #generate-pdf-by-status-button`;

    // Invoice options form
    this.invoiceOptionsForm = '#form-invoices-options';
    this.invoiceOptionsStatusToggleInput = toggle => `#form_enable_invoices_${toggle}`;
    this.taxBreakdownStatusToggleInput = toggle => `#form_enable_tax_breakdown_${toggle}`;
    this.invoiceOptionStatusToggleInput = toggle => `#form_enable_product_images_${toggle}`;
    this.invoiceNumberInput = '#form_invoice_number';
    this.legalFreeTextInput = '#form_legal_free_text_1';
    this.footerTextInput = '#form_footer_text_1';
    this.saveInvoiceOptionsButton = `${this.invoiceOptionsForm} #save-invoices-options-button`;
    this.invoicePrefixInput = '#form_invoice_prefix_1';
    this.invoiceAddCurrentYearToggleInput = toggle => `#form_add_current_year_${toggle}`;
    this.optionYearPositionRadioButton = id => `#form_year_position_${id} + i`;
  }

  /*
  Methods
   */

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
   * Get message error after generate invoice by status fail
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
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
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
   * @returns {Promise<void>}
   */
  async setValuesForGeneratingPDFByDate(page, dateFrom = '', dateTo = '') {
    if (dateFrom) {
      await page.fill(this.dateFromInput, dateFrom);
      await page.fill(this.dateToInput, dateTo);
    }
  }

  /**
   * Choose order status to generate
   * @param page {Page} Browser tab
   * @param statusName {string} Status name to select
   * @returns {Promise<void>}
   */
  async chooseStatus(page, statusName) {
    const statusElements = await page.$$(this.statusOrderStateSpan);

    for (let i = 0; i < statusElements.length; i++) {
      if (await page.evaluate(element => element.textContent, statusElements[i]) === statusName) {
        await statusElements[i].click();
        break;
      }
    }
  }

  /**
   * Generate PDF by status
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  generatePDFByStatusAndDownload(page) {
    return this.clickAndWaitForDownload(page, this.generatePdfByStatusButton);
  }

  /**
   * Get message error after generate invoice by status fail
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async generatePDFByStatusAndFail(page) {
    await page.click(this.generatePdfByStatusButton);
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Enable disable invoices
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable invoices
   * @returns {Promise<void>}
   */
  async enableInvoices(page, enable = true) {
    await page.check(this.invoiceOptionsStatusToggleInput(enable ? 1 : 0));
  }

  /**
   * Save invoice options
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async saveInvoiceOptions(page) {
    await this.clickAndWaitForNavigation(page, this.saveInvoiceOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable disable product image
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable product image in the invoice
   * @returns {Promise<void>}
   */
  async enableProductImage(page, enable = true) {
    await page.check(this.invoiceOptionStatusToggleInput(enable ? 1 : 0));
  }

  /**
   * Enable tax breakdown
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable tax breakdown in the invoice
   * @returns {Promise<void>}
   */
  async enableTaxBreakdown(page, enable = true) {
    await page.check(this.taxBreakdownStatusToggleInput(enable ? 1 : 0));
  }

  /**
   * Set invoiceNumber, LegalFreeText, footerText
   * @param page {Page} Browser tab
   * @param data {object} Values to set on invoice option inputs
   * @returns {Promise<void>}
   */
  async setInputOptions(page, data) {
    await this.setValue(page, this.invoiceNumberInput, data.invoiceNumber);
    await this.setValue(page, this.footerTextInput, data.footerText);
  }

  /**
   * Enable add current year to invoice
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable add current year to invoice
   * @returns {Promise<void>}
   */
  async enableAddCurrentYearToInvoice(page, enable = true) {
    await page.check(this.invoiceAddCurrentYearToggleInput(enable ? 1 : 0));
  }

  /**
   * Choose the position of the year
   * @param page {Page} Browser tab
   * @param id {number} Radio button id for position of the year date
   * @returns {Promise<void>}
   */
  async chooseInvoiceOptionsYearPosition(page, id) {
    await page.click(this.optionYearPositionRadioButton(id));
  }

  /**
   * Edit invoice Prefix
   * @param page {Page} Browser tab
   * @param prefix {string} Prefix value to change
   * @returns {Promise<void>}
   */
  async changePrefix(page, prefix) {
    await this.setValue(page, this.invoicePrefixInput, prefix);
  }
}

module.exports = new Invoice();
