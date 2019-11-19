require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Invoice extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Invoices';
    this.errorMessageWhenGenerateFileByDate = 'No invoice has been found for this period.';

    // Invoices page
    this.generateByDateForm = '[name="generate_by_date"]';
    this.dateFromInput = `${this.generateByDateForm} #form_generate_by_date_date_from`;
    this.dateToInput = `${this.generateByDateForm} #form_generate_by_date_date_to`;
    this.generatePdfByDateButton = `${this.generateByDateForm} .btn.btn-primary`;
  }

  /*
  Methods
   */

  async generatePDFByDate(dateFrom = '', dateTo = '') {
    if (dateFrom) {
      await this.setValue(this.dateFromInput, dateFrom);
      await this.setValue(this.dateToInput, dateTo);
    }
    await this.page.click(this.generatePdfByDateButton);
  }
};
