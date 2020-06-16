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
    this.generateByDateForm = '#form-generate-invoices-by-date';
    this.dateFromInput = `${this.generateByDateForm} #form_date_from`;
    this.dateToInput = `${this.generateByDateForm} #form_date_to`;
    this.generatePdfByDateButton = `${this.generateByDateForm} button`;

    // By order status form
    this.generateByStatusForm = '#form-generate-invoices-by-status';
    this.statusOrderStateSpan = `${this.generateByStatusForm} span:not(.badge)`;
    this.generatePdfByStatusButton = `${this.generateByStatusForm} button`;

    // Invoice options form
    this.invoiceOptionsForm = '#form-invoices-options';
    this.invoiceOptionsEnable = id => `${this.invoiceOptionsForm} label[for='form_enable_invoices_${id}']`;
    this.taxBreakdownEnable = id => `${this.invoiceOptionsForm} label[for='form_enable_tax_breakdown_${id}']`;
    this.invoiceOptionEnableProductImage = id => `${this.invoiceOptionsForm}`
      + ` label[for='form_enable_product_images_${id}']`;
    this.invoiceNumberInput = '#form_invoice_number';
    this.legalFreeTextInput = '#form_legal_free_text_1';
    this.footerTextInput = '#form_footer_text_1';
    this.saveInvoiceOptionsButton = `${this.invoiceOptionsForm} .btn.btn-primary`;
    this.invoicePrefixInput = '#form_invoice_prefix_1';
    this.invoiceAddCurrentYear = id => `${this.invoiceOptionsForm} label[for='form_add_current_year_${id}']`;
    this.optionYearPositionRadioButton = id => `#form_year_position_${id}`;
  }

  /*
  Methods
   */

  /**
   *
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<null|*>}
   */
  async generatePDFByDateAndDownload(dateFrom = '', dateTo = '') {
    await this.setValuesForGeneratingPDFByDate(dateFrom, dateTo);
    const [download] = await Promise.all([
      this.page.waitForEvent('download'),
      this.page.click(this.generatePdfByDateButton),
    ]);
    return download.path();
  }

  /**
   * Get message error after generate invoice by status fail
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<string>}
   */
  async generatePDFByDateAndFail(dateFrom = '', dateTo = '') {
    await this.setValuesForGeneratingPDFByDate(dateFrom, dateTo);
    await this.page.click(this.generatePdfByDateButton);

    return this.getTextContent(
      this.alertTextBlock,
    );
  }

  /**
   * Set values to generate pdf by date
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<void>}
   */
  async setValuesForGeneratingPDFByDate(dateFrom = '', dateTo = '') {
    if (dateFrom) {
      await this.page.fill(this.dateFromInput, dateFrom);
      await this.page.fill(this.dateToInput, dateTo);
    }
  }

  /**
   * Click on the Status
   * @param statusName
   * @return {Promise<void>}
   */
  async chooseStatus(statusName) {
    const statusElements = await this.page.$$(this.statusOrderStateSpan);
    for (let i = 0; i < statusElements.length; i++) {
      if (await this.page.evaluate(element => element.textContent, statusElements[i]) === statusName) {
        await statusElements[i].click();
        break;
      }
    }
    //
  }

  /** Generate PDF by status
   * @return {Promise<void>}
   */
  async generatePDFByStatusAndDownload() {
    const [download] = await Promise.all([
      this.page.waitForEvent('download'), // wait for download to start
      this.page.click(this.generatePdfByStatusButton),
    ]);
    return download.path();
  }

  /**
   * Get message error after generate invoice by status fail
   * @return {Promise<string>}
   */
  async generatePDFByStatusAndFail() {
    await this.page.click(this.generatePdfByStatusButton);
    return this.getTextContent(this.alertTextBlock);
  }

  /**
   * Enable disable invoices
   * @param enable
   * @return {Promise<void>}
   */
  async enableInvoices(enable = true) {
    await this.page.click(this.invoiceOptionsEnable(enable ? 1 : 0));
  }

  /** Save invoice options
   * @return {Promise<void>}
   */
  async saveInvoiceOptions() {
    await this.clickAndWaitForNavigation(this.saveInvoiceOptionsButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Enable disable product image
   * @param enable
   * @return {Promise<void>}
   */
  async enableProductImage(enable = true) {
    await this.page.click(this.invoiceOptionEnableProductImage(enable ? 1 : 0));
  }

  /**
   * Enable tax breakdown
   * @param enable
   * @return {Promise<void>}
   */
  async enableTaxBreakdown(enable = true) {
    await this.page.click(this.taxBreakdownEnable(enable ? 1 : 0));
  }

  /**
   * Set invoiceNumber, LegalFreeText, footerText
   * @param data
   * @return {Promise<void>}
   */
  async setInputOptions(data) {
    await this.setValue(this.invoiceNumberInput, data.invoiceNumber);
    await this.setValue(this.footerTextInput, data.footerText);
  }

  /**
   * Enable add current year to invoice
   * @param enable
   * @return {Promise<void>}
   */
  async enableAddCurrentYearToInvoice(enable = true) {
    await this.page.click(this.invoiceAddCurrentYear(enable ? 1 : 0));
  }

  /**
   * Choose the position of the year
   * @param id
   * @return {Promise<void>}
   */
  async chooseInvoiceOptionsYearPosition(id) {
    await this.page.click(this.optionYearPositionRadioButton(id));
  }

  /** Edit invoice Prefix
   * @param prefix
   * @return {Promise<void>}
   */
  async changePrefix(prefix) {
    await this.setValue(this.invoicePrefixInput, prefix);
  }
};
