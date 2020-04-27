require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Order extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order';
    this.partialRefundValidationMessage = 'A partial refund was successfully created.';

    // Order page
    this.orderProductsTable = '#orderProductsTable';
    this.orderProductsRowTable = row => `${this.orderProductsTable} tbody tr:nth-child(${row})`;
    this.editProductButton = row => `${this.orderProductsRowTable(row)} button[data-original-title='Edit']`;
    this.productQuantitySpan = row => `${this.orderProductsRowTable(row)} td.cellProductQuantity span`;
    this.orderProductsEditRowTable = `${this.orderProductsTable} tbody tr.editProductRow`;
    this.editProductQuantityInput = `${this.orderProductsEditRowTable} input#edit_product_row_quantity`;
    this.UpdateProductButton = `${this.orderProductsEditRowTable} button#edit_product_row_save`;
    this.partialRefundButton = 'button.partial-refund-display';
    // Status tab
    this.orderStatusesSelect = '#update_order_status_action_input';
    this.updateStatusButton = '#update_order_status_action_btn';
    // Document tab
    this.documentTab = 'a#orderDocumentsTab';
    this.documentsTableDiv = '#orderDocumentsTabContent';
    this.documentsTableRow = row => `${this.documentsTableDiv} table tbody tr:nth-child(${row})`;
    this.documentNumberLink = row => `${this.documentsTableRow(row)} td:nth-child(3) a`;
    this.documentName = row => `${this.documentsTableRow(row)} td:nth-child(2)`;
    // Refund form
    this.refundProductQuantity = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_quantity']`;
    this.refundProductAmount = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_amount']`;
    this.refundShippingCost = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_shipping_amount']`;
    this.partialRefundSubmitButton = 'button#cancel_product_save';
  }

  /*
  Methods
   */

  /**
   * Modify the product quantity
   * @param row, product row
   * @param quantity, new quantity
   * @returns {Promise<void>}
   */
  async modifyProductQuantity(row, quantity) {
    this.dialogListener();
    await Promise.all([
      this.page.click(this.editProductButton(row)),
      this.waitForVisibleSelector(this.editProductQuantityInput),
    ]);
    await this.setValue(this.editProductQuantityInput, quantity.toString());
    await Promise.all([
      this.page.click(this.UpdateProductButton),
      this.waitForVisibleSelector(this.editProductQuantityInput),
    ]);
    return parseFloat(await this.getTextContent(this.productQuantitySpan(row)));
  }

  /**
   * Modify the order status
   * @param status
   * @returns {Promise<string>}
   */
  async modifyOrderStatus(status) {
    const actualStatus = await this.getOrderStatus();
    if (status !== actualStatus) {
      await this.selectByVisibleText(this.orderStatusesSelect, status);
      await this.clickAndWaitForNavigation(this.updateStatusButton);
      return this.getOrderStatus();
    }
    return actualStatus;
  }

  /**
   * Get order status
   * @return {Promise<string>}
   */
  async getOrderStatus() {
    return this.getTextContent(`${this.orderStatusesSelect} option[selected='selected']`, false);
  }

  /**
   * Get document name
   * @param rowChild
   * @returns {Promise<string>}
   */
  async getDocumentName(rowChild = 1) {
    await Promise.all([
      this.page.click(this.documentTab),
      this.waitForVisibleSelector(`${this.documentTab}.active`),
    ]);
    return this.getTextContent(this.documentName(rowChild));
  }

  /**
   * Get file name
   * @param rowChild
   * @returns fileName
   */
  async getFileName(rowChild = 1) {
    await Promise.all([
      this.page.click(this.documentTab),
      this.waitForVisibleSelector(`${this.documentTab}.active`),
    ]);
    const fileName = await this.getTextContent(this.documentNumberLink(rowChild));
    return fileName.replace('#', '').trim();
  }

  /**
   * Download invoice
   * @returns {Promise<void>}
   */
  async downloadInvoice() {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    await this.page.$eval(this.documentNumberLink(1), el => el.target = '');
    await this.page.click(this.documentNumberLink(1));
    /* eslint-enable no-return-assign, no-param-reassign */
  }

  /**
   * Click on partial refund button
   * @returns {Promise<void>}
   */
  async clickOnPartialRefund() {
    await this.page.click(this.partialRefundButton);
    await this.waitForVisibleSelector(this.refundProductQuantity(1));
  }

  /**
   * Add partial refund product
   * @param productRow
   * @param quantity
   * @param amount
   * @param shipping
   * @returns {Promise<textContent>}
   */
  async addPartialRefundProduct(productRow, quantity = 0, amount = 0, shipping = 0) {
    await this.setValue(this.refundProductQuantity(productRow), quantity.toString());
    if (amount !== 0) {
      await this.setValue(this.refundProductAmount(productRow), amount.toString());
    }
    if (shipping !== 0) {
      await this.setValue(this.refundShippingCost(productRow), shipping.toString());
    }
    await this.clickAndWaitForNavigation(this.partialRefundSubmitButton);
    return this.getTextContent(this.alertTextBlock);
  }

  /**
   * Download delivery slip
   * @returns {Promise<void>}
   */
  async downloadDeliverySlip() {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    await this.page.$eval(this.documentNumberLink(3), el => el.target = '');
    await this.page.click(this.documentNumberLink(3));
    /* eslint-enable no-return-assign, no-param-reassign */
  }
};
