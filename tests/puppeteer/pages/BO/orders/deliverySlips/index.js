require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class DeliverySlips extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Delivery Slips';
    this.errorMessageWhenGenerateFileByDate = 'No delivery slip was found for this period.';

    // Delivery slips page
    // By date form
    this.generateByDateForm = '[name=\'slip_pdf_form\']';
    this.dateFromInput = '#slip_pdf_form_pdf_date_from';
    this.dateToInput = '#slip_pdf_form_pdf_date_to';
    this.generatePdfByDateButton = `${this.generateByDateForm} .btn.btn-primary`;
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
};
