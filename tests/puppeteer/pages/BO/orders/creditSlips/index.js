require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class CreditSlips extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Credit Slips •';
    this.errorMessageWhenGenerateFileByDate = 'No invoice has been found for this period.';
    this.errorMessageWhenGenerateFileByStatus = 'No invoice has been found for this status.';
    this.errorMessageWhenNotSelectStatus = 'You must select at least one order status.';
    this.successfulUpdateMessage = 'Update successful';

    // Credit slips page
    // Credit slips table
    this.creditSlipGridTable = '#credit_slip_grid_table';
    this.creditSlipIdInput = '#credit_slip_id_credit_slip';
    this.creditSlipOrderIdInput = '#credit_slip_id_order';
    this.creditSlipDateFrom = '#credit_slip_date_issued_from';
    this.creditSlipDateTo = '#credit_slip_date_issued_to';
    this.creditSlipDownloadButton = `${this.creditSlipGridTable} td.link-type.column-pdf`;
  }

  /*
  Methods
   */
};
