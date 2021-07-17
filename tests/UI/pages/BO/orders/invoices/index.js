require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Invoice extends BOBasePage {
  constructor() {
    super();

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
    this.statusOrderStateSpan = `${this.formGenerateByStatus} span:not(.badge)`;
    this.generatePdfByStatusButton = `${this.generateByStatusForm} .btn.btn-primary`;
    // Invoice options form
    this.invoiceOptionsForm = '[name="invoice_options"]';
    this.invoiceOptionsEnable = id => `${this.invoiceOptionsForm
    } label[for='form_invoice_options_enable_invoices_${id}']`;
    this.taxBreakdownEnable = id => `${this.invoiceOptionsForm
    } label[for='form_invoice_options_enable_tax_breakdown_${id}']`;
    this.invoiceOptionEnableProductImage = id => `${this.invoiceOptionsForm
    } label[for='form_invoice_options_enable_product_images_${id}']`;
    this.invoiceNumberInput = '#form_invoice_options_invoice_number';
    this.legalFreeTextInput = '#form_invoice_options_legal_free_text_1';
    this.footerTextInput = '#form_invoice_options_footer_text_1';
    this.saveInvoiceOptionsButton = `${this.invoiceOptionsForm} .btn.btn-primary`;
    this.invoicePrefixInput = '#form_invoice_options_invoice_prefix_1';
    this.invoiceAddCurrentYear = id => `${this.invoiceOptionsForm
    } label[for='form_invoice_options_add_current_year_${id}']`;
    this.optionYearPositionRadioButton = id => `#form_invoice_options_year_position_${id}`;
  }

  /*
  Methods
   */

  /**
   * Generate PDF by date and download it
   * @param page
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<*>}
   */
  async generatePDFByDateAndDownload(page, dateFrom = '', dateTo = '') {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.generatePdfByDateButton),
    ]);
    return download.path();
  }

  /**
   * Get message error after generate invoice by status fail
   * @param page
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<string>}
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
      await page.fill(this.dateFromInput, dateFrom);
      await page.fill(this.dateToInput, dateTo);
    }
  }

  /**
   * Click on the Status
   * @param page
   * @param statusName
   * @return {Promise<void>}
   */
  async chooseStatus(page, statusName) {
    const statusElements = await page.$$(this.statusOrderStateSpan);
    for (let i = 0; i < statusElements.length; i++) {
      if (await page.evaluate(element => element.textContent, statusElements[i]) === statusName) {
        await statusElements[i].click();
        break;
      }
    }
    //
  }

  /** Generate PDF by status
   * @param page
   * @return {Promise<void>}
   */
  async generatePDFByStatusAndDownload(page) {
    const [download] = await Promise.all([
      page.waitForEvent('download'), // wait for download to start
      page.click(this.generatePdfByStatusButton),
    ]);
    return download.path();
  }

  /**
   * Get message error after generate invoice by status fail
   * @param page
   * @return {Promise<string>}
   */
  async generatePDFByStatusAndFail(page) {
    await page.click(this.generatePdfByStatusButton);
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Enable disable invoices
   * @param page
   * @param enable
   * @return {Promise<void>}
   */
  async enableInvoices(page, enable = true) {
    await page.click(this.invoiceOptionsEnable(enable ? 1 : 0));
  }

  /** Save invoice options
   * @param page
   * @return {Promise<void>}
   */
  async saveInvoiceOptions(page) {
    await this.clickAndWaitForNavigation(page, this.saveInvoiceOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable disable product image
   * @param page
   * @param enable
   * @return {Promise<void>}
   */
  async enableProductImage(page, enable = true) {
    await page.click(this.invoiceOptionEnableProductImage(enable ? 1 : 0));
  }

  /**
   * Enable tax breakdown
   * @param page
   * @param enable
   * @return {Promise<void>}
   */
  async enableTaxBreakdown(page, enable = true) {
    await page.click(this.taxBreakdownEnable(enable ? 1 : 0));
  }

  /**
   * Set invoiceNumber, LegalFreeText, footerText
   * @param page
   * @param data
   * @return {Promise<void>}
   */
  async setInputOptions(page, data) {
    await this.setValue(page, this.invoiceNumberInput, data.invoiceNumber);
    await this.setValue(page, this.footerTextInput, data.footerText);
  }

  /**
   * Enable add current year to invoice
   * @param page
   * @param enable
   * @return {Promise<void>}
   */
  async enableAddCurrentYearToInvoice(page, enable = true) {
    await page.click(this.invoiceAddCurrentYear(enable ? 1 : 0));
  }

  /**
   * Choose the position of the year
   * @param page
   * @param id
   * @return {Promise<void>}
   */
  async chooseInvoiceOptionsYearPosition(page, id) {
    await page.click(this.optionYearPositionRadioButton(id));
  }

  /** Edit invoice Prefix
   * @param page
   * @param prefix
   * @return {Promise<void>}
   */
  async changePrefix(page, prefix) {
    await this.setValue(page, this.invoicePrefixInput, prefix);
  }
}

module.exports = new Invoice();
