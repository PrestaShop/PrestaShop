require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class DeliverySlips extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Delivery Slips';
    this.errorMessageWhenGenerateFileByDate = 'No delivery slip was found for this period.';
    this.successfulUpdateMessage = 'Update successful';

    // Delivery slips page
    // By date form
    this.generateByDateForm = '[name=\'slip_pdf_form\']';
    this.dateFromInput = '#slip_pdf_form_pdf_date_from';
    this.dateToInput = '#slip_pdf_form_pdf_date_to';
    this.generatePdfByDateButton = `${this.generateByDateForm} .btn.btn-primary`;
    // Delivery slip options form
    this.deliverySlipForm = '#delivery_options_fieldset';
    this.deliveryPrefixInput = '#form_options_prefix_1';
    this.deliveryNumberInput = '#form_options_number';
    this.deliveryEnableProductImage = id => `${this.deliverySlipForm
    } label[for='form_options_enable_product_image_${id}']`;
    this.saveDeliverySlipOptionsButton = `${this.deliverySlipForm} .btn.btn-primary`;
  }

  /*
  Methods
   */

  /**
   * Generate PDF by date and download
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
   * Get message error after generate delivery slip fail
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

  /** Edit delivery slip Prefix
   * @param page
   * @param prefix
   * @return {Promise<void>}
   */
  async changePrefix(page, prefix) {
    await this.setValue(page, this.deliveryPrefixInput, prefix);
  }

  /** Edit delivery slip Prefix
   * @param page
   * @param number
   * @return {Promise<void>}
   */
  async changeNumber(page, number) {
    await this.setValue(page, this.deliveryNumberInput, number);
  }

  /**
   * Enable disable product image
   * @param page
   * @param enable
   * @return {Promise<void>}
   */
  async setEnableProductImage(page, enable = true) {
    await page.click(this.deliveryEnableProductImage(enable ? 1 : 0));
  }

  /** Save delivery slip options
   * @param page
   * @return {Promise<string>}
   */
  async saveDeliverySlipOptions(page) {
    await this.clickAndWaitForNavigation(page, this.saveDeliverySlipOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}
module.exports = new DeliverySlips();
