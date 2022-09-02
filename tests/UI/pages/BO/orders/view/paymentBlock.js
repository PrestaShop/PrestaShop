require('module-alias/register');
const ViewOrderBasePage = require('@pages/BO/orders/view/viewOrderBasePage');

/**
 * Payment block, contains functions that can be used on view/edit payment block on view order page
 * @class
 * @extends ViewOrderBasePage
 */
class PaymentBlock extends ViewOrderBasePage.constructor {
  /**
   * @constructs
   * Setting up texts and selectors to use on payment block
   */
  constructor() {
    super();

    // Payment block
    this.orderPaymentsBlock = '#view_order_payments_block';
    this.orderPaymentsTitle = `${this.orderPaymentsBlock} .card-header-title`;
    this.paymentDateInput = '#order_payment_date';
    this.paymentMethodInput = '#order_payment_payment_method';
    this.transactionIDInput = '#order_payment_transaction_id';
    this.paymentAmountInput = '#order_payment_amount_currency_amount';
    this.paymentCurrencySelect = '#order_payment_amount_currency_id_currency';
    this.paymentInvoiceSelect = '#order_payment_id_invoice';
    this.paymentAddButton = `${this.orderPaymentsBlock} .btn.btn-primary.btn-sm`;
    this.paymentWarning = `${this.orderPaymentsBlock} .alert-danger`;
    this.paymentsGridTable = 'table[data-role=\'payments-grid-table\']';
    this.paymentsTableBody = `${this.paymentsGridTable} tbody`;
    this.paymentsTableRow = row => `${this.paymentsTableBody} tr:nth-child(${row})`;
    this.paymentsTableColumn = (row, column) => `${this.paymentsTableRow(row)} td[data-role='${column}-column']`;
    this.paymentsTableDetailsButton = row => `${this.paymentsTableRow(row)} button.js-payment-details-btn`;
    this.paymentTableRowDetails = row => `${this.paymentsTableRow(row)}[data-role='payment-details']`;
  }

  /*
  Methods
   */
  /**
   * Get payments number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getPaymentsNumber(page) {
    return this.getNumberFromText(page, this.orderPaymentsTitle);
  }

  /**
   * Get payment input value
   * @param page {Page} Browser tab
   * @returns {*}
   */
  getPaymentAmountInputValue(page) {
    return page.$eval(this.paymentAmountInput, el => el.value);
  }

  /**
   * Add payment
   * @param page {Page} Browser tab
   * @param paymentData {object} Data to set on payment line
   * @param invoice {string} Invoice number to select
   * @returns {Promise<string>}
   */
  async addPayment(page, paymentData, invoice = '') {
    await this.setValue(page, this.paymentDateInput, paymentData.date);
    await this.setValue(page, this.paymentMethodInput, paymentData.paymentMethod);
    await this.setValue(page, this.transactionIDInput, paymentData.transactionID);
    await this.setValue(page, this.paymentAmountInput, paymentData.amount);
    if (paymentData.currency !== '€') {
      await this.selectByVisibleText(page, this.paymentCurrencySelect, paymentData.currency);
    }

    if (invoice !== '') {
      await this.selectByVisibleText(page, this.paymentInvoiceSelect, invoice);
    }

    await this.clickAndWaitForNavigation(page, this.paymentAddButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get invoice ID
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<number>}
   */
  getInvoiceID(page, row = 1) {
    return this.getNumberFromText(page, this.paymentsTableColumn(row, 'invoice'));
  }

  /**
   * Get payment warning
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPaymentWarning(page) {
    return this.getTextContent(page, this.paymentWarning);
  }

  /**
   * Get payment details
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<{date: string, amount: string, paymentMethod: string, invoice: string, transactionID: string}>}
   */
  async getPaymentsDetails(page, row = 1) {
    return {
      date: await this.getTextContent(page, this.paymentsTableColumn(row, 'date')),
      paymentMethod: await this.getTextContent(page, this.paymentsTableColumn(row, 'payment-method')),
      transactionID: await this.getTextContent(page, this.paymentsTableColumn(row, 'transaction-id')),
      amount: await this.getTextContent(page, this.paymentsTableColumn(row, 'amount')),
      invoice: await this.getTextContent(page, this.paymentsTableColumn(row, 'invoice')),
    };
  }

  /**
   * Display payment details
   * @param page {Page} Browser tab
   * @param row {number} Row on table - Start by 2
   * @returns {Promise<string>}
   */
  async displayPaymentDetail(page, row = 2) {
    await this.waitForSelectorAndClick(page, this.paymentsTableDetailsButton(row - 1));

    return this.getTextContent(page, this.paymentTableRowDetails(row));
  }

  /**
   * Get currency select options
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCurrencySelectOptions(page) {
    return this.getTextContent(page, this.paymentCurrencySelect);
  }
}

module.exports = new PaymentBlock();
