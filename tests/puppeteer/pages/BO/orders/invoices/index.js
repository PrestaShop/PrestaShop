require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Invoice extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Invoices';
    this.errorMessageWhenGenerateFileByDate = 'No invoice has been found for this period.';
    this.errorMessageWhenGenerateFileByStatus = 'No invoice has been found for this status.';
    this.errorMessageWhenNotSelectStatus = 'You must select at least one order status.';
    this.successfulUpdateMessage = 'Update successful';

    // Invoices page
    // By date form
    this.generateByDateForm = '[name="generate_by_date"]';
    this.dateFromInput = `${this.generateByDateForm} #form_generate_by_date_date_from`;
    this.dateToInput = `${this.generateByDateForm} #form_generate_by_date_date_to`;
    this.generatePdfByDateButton = `${this.generateByDateForm} .btn.btn-primary`;
    // By order status form
    this.generateByStatusForm = '[name="generate_by_status"]';
    this.formGenerateByStatus = '#form_generate_by_status_order_states';
    this.statusOrderStateInput = `${this.formGenerateByStatus} input#form_generate_by_status_order_states_%ID`;
    this.statusCheckbox = `${this.statusOrderStateInput}:first-of-type + i`;
    this.generatePdfByStatusButton = `${this.generateByStatusForm} .btn.btn-primary`;
    // Invoice options form
    this.invoiceOptionsForm = '[name="invoice_options"]';
    this.invoiceOptionsEnable = `${this.invoiceOptionsForm} label[for="form_invoice_options_enable_invoices_%ID"]`;
    this.saveInvoiceOptionsButton = `${this.invoiceOptionsForm} .btn.btn-primary`;
  }

  /*
  Methods
   */

  /**
   * Generate PDF by date
   * @param dateFrom
   * @param dateTo
   * @return {Promise<void>}
   */
  async generatePDFByDate(dateFrom = '', dateTo = '') {
    if (dateFrom) {
      await this.setValue(this.dateFromInput, dateFrom);
      await this.setValue(this.dateToInput, dateTo);
    }
    await this.page.click(this.generatePdfByDateButton);
  }

  /**
   * Click on the Status
   * @param statusID
   * @return {Promise<void>}
   */
  async chooseStatus(statusID) {
    await this.page.click(this.statusCheckbox.replace('%ID', statusID));
  }

  /** Generate PDF by status
   * @return {Promise<void>}
   */
  async generatePDFByStatus() {
    await this.page.click(this.generatePdfByStatusButton);
  }

  /**
   * Enable disable invoices
   * @param enable
   * @return {Promise<void>}
   */
  async enableInvoices(enable = true) {
    await this.page.click(this.invoiceOptionsEnable.replace('%ID', enable ? 1 : 0));
  }

  /** Save invoice options
   * @return {Promise<void>}
   */
  async saveInvoiceOptions() {
    await this.clickAndWaitForNavigation(this.saveInvoiceOptionsButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
