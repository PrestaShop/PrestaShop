require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Order extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order';
    this.partialRefundValidationMessage = 'A partial refund was successfully created.';

    // Order page
    this.orderProductsTable = '#orderProductsTable';
    this.orderProductsRowTable = `${this.orderProductsTable} tbody tr:nth-child(%ROW)`;
    this.editProductButton = `${this.orderProductsRowTable} button[data-original-title='Edit']`;
    this.productQuantitySpan = `${this.orderProductsRowTable} td.cellProductQuantity span`;
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
    this.documentsTableRow = `${this.documentsTableDiv} table tbody tr:nth-child(%ROW)`;
    this.documentNumberLink = `${this.documentsTableRow} td:nth-child(3) a`;
    this.documentName = `${this.documentsTableRow} td:nth-child(2)`;
    // Refund form
    this.refundProductQuantity = `${this.orderProductsRowTable} input[id*='cancel_product_quantity']`;
    this.refundProductAmount = `${this.orderProductsRowTable} input[id*='cancel_product_amount']`;
    this.refundShippingCost = `${this.orderProductsRowTable} input[id*='cancel_product_shipping_amount']`;
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
      this.page.click(this.editProductButton.replace('%ROW', row)),
      this.waitForVisibleSelector(this.editProductQuantityInput),
    ]);
    await this.setValue(this.editProductQuantityInput, quantity.toString());
    await Promise.all([
      this.page.click(this.UpdateProductButton),
      this.waitForVisibleSelector(this.editProductQuantityInput),
    ]);
    return parseFloat(await this.getTextContent(this.productQuantitySpan.replace('%ROW', row)));
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
    return this.getTextContent(this.documentName.replace('%ROW', rowChild));
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
    const fileName = await this.getTextContent(this.documentNumberLink.replace('%ROW', rowChild));
    return fileName.replace('#', '').trim();
  }

  /**
   * Download invoice
   * @returns {Promise<void>}
   */
  async downloadInvoice() {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    await this.page.$eval(this.documentNumberLink.replace('%ROW', 1), el => el.target = '');
    await this.page.click(this.documentNumberLink.replace('%ROW', 1));
    /* eslint-enable no-return-assign, no-param-reassign */
  }

  /**
   * Click on partial refund button
   * @returns {Promise<void>}
   */
  async clickOnPartialRefund() {
    await this.page.click(this.partialRefundButton);
    await this.waitForVisibleSelector(this.refundProductQuantity.replace('%ROW', 1));
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
    await this.setValue(this.refundProductQuantity.replace('%ROW', productRow), quantity.toString());
    if (amount !== 0) {
      await this.setValue(this.refundProductAmount.replace('%ROW', productRow), amount.toString());
    }
    if (shipping !== 0) {
      await this.setValue(this.refundShippingCost.replace('%ROW', productRow), shipping.toString());
    }
    await this.clickAndWaitForNavigation(this.partialRefundSubmitButton.replace('%ROW', productRow));
    return this.getTextContent(this.alertTextBlock);
  }

  /**
   * Download delivery slip
   * @returns {Promise<void>}
   */
  async downloadDeliverySlip() {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    await this.page.$eval(this.documentNumberLink.replace('%ROW', 3), el => el.target = '');
    await this.page.click(this.documentNumberLink.replace('%ROW', 3));
    /* eslint-enable no-return-assign, no-param-reassign */
  }
};
