require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class DeliverySlips extends BOBasePage {
  constructor(page) {
    super(page);

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
    this.deliveryEnableProductImage = `${this.deliverySlipForm} label[for='form_options_enable_product_image_%ID']`;
    this.saveDeliverySlipOptionsButton = `${this.deliverySlipForm} .btn.btn-primary`;
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
    }
    if (dateFrom) {
      await this.setValue(this.dateToInput, dateTo);
    }
    await this.page.click(this.generatePdfByDateButton);
  }

  /** Edit delivery slip Prefix
   * @param prefix
   * @return {Promise<void>}
   */
  async changePrefix(prefix) {
    await this.setValue(this.deliveryPrefixInput, prefix);
  }

  /** Edit delivery slip Prefix
   * @param number
   * @return {Promise<void>}
   */
  async changeNumber(number) {
    await this.setValue(this.deliveryNumberInput, number);
  }

  /**
   * Enable disable product image
   * @param enable
   * @return {Promise<void>}
   */
  async enableProductImage(enable = true) {
    await this.page.click(this.deliveryEnableProductImage.replace('%ID', enable ? 1 : 0));
  }

  /** Save delivery slip options
   * @return {Promise<textContent>}
   */
  async saveDeliverySlipOptions() {
    await this.clickAndWaitForNavigation(this.saveDeliverySlipOptionsButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
