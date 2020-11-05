require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Order extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order';
    this.partialRefundValidationMessage = 'A partial refund was successfully created.';

    // Customer card
    this.shippingAddressBlock = '#addressShipping';
    this.invoiceAddressBlock = '#addressInvoice';

    // Order card
    this.orderProductsTable = '#orderProductsTable';
    this.orderProductsRowTable = row => `${this.orderProductsTable} tbody tr:nth-child(${row})`;
    this.orderProductsTableNameColumn = row => `${this.orderProductsRowTable(row)} td.cellProductName`;
    this.orderProductsTableNameNameParagraph = row => `${this.orderProductsTableNameColumn(row)} p.productName`;
    this.editProductButton = row => `${this.orderProductsRowTable(row)} button.js-order-product-edit-btn`;
    this.productQuantitySpan = row => `${this.orderProductsRowTable(row)} td.cellProductQuantity span`;
    this.orderProductsEditRowTable = `${this.orderProductsTable} tbody tr.editProductRow`;
    this.editProductQuantityInput = `${this.orderProductsEditRowTable} input.editProductQuantity`;
    this.UpdateProductButton = `${this.orderProductsEditRowTable} button.productEditSaveBtn`;
    this.partialRefundButton = 'button.partial-refund-display';
    this.orderTotalPriceSpan = '#orderTotal';

    // Status card
    this.orderStatusesSelect = '#update_order_status_action_input';
    this.updateStatusButton = '#update_order_status_action_btn';

    // Documents card
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
   * Get shipping address from customer card
   * @param page
   * @return {Promise<string>}
   */
  getShippingAddress(page) {
    return this.getTextContent(page, this.shippingAddressBlock);
  }

  /**
   * Get invoice address from customer card
   * @param page
   * @return {Promise<string>}
   */
  getInvoiceAddress(page) {
    return this.getTextContent(page, this.invoiceAddressBlock);
  }

  /**
   * Get product name from products table
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  getProductNameFromTable(page, row) {
    return this.getTextContent(page, this.orderProductsTableNameNameParagraph(row));
  }

  /**
   * Modify the product quantity
   * @param page
   * @param row
   * @param quantity
   * @returns {Promise<number>}
   */
  async modifyProductQuantity(page, row, quantity) {
    this.dialogListener(page);
    await Promise.all([
      page.click(this.editProductButton(row)),
      this.waitForVisibleSelector(page, this.editProductQuantityInput),
    ]);
    await this.setValue(page, this.editProductQuantityInput, quantity.toString());
    await Promise.all([
      page.click(this.UpdateProductButton),
      this.waitForVisibleSelector(page, this.editProductQuantityInput),
    ]);
    return parseFloat(await this.getTextContent(page, this.productQuantitySpan(row)));
  }

  /**
   * Modify the order status
   * @param page
   * @param status
   * @returns {Promise<string>}
   */
  async modifyOrderStatus(page, status) {
    const actualStatus = await this.getOrderStatus(page);
    if (status !== actualStatus) {
      await this.selectByVisibleText(page, this.orderStatusesSelect, status);
      await this.clickAndWaitForNavigation(page, this.updateStatusButton);
      return this.getOrderStatus(page);
    }
    return actualStatus;
  }

  /**
   * Get order status
   * @param page
   * @return {Promise<string>}
   */
  async getOrderStatus(page) {
    return this.getTextContent(page, `${this.orderStatusesSelect} option[selected='selected']`, false);
  }

  /**
   * Does status exist
   * @param page
   * @param statusName
   * @returns {Promise<boolean>}
   */
  async doesStatusExist(page, statusName) {
    let options = await page.$$eval(
      `${this.orderStatusesSelect} option`,
      all => all.map(
        option => ({
          textContent: option.textContent,
          value: option.value,
        })),
    );

    options = await options.filter(option => statusName === option.textContent);
    return options.length !== 0;
  }

  /**
   * Get total price from products tab
   * @param page
   * @return {Promise<number>}
   */
  getOrderTotalPrice(page) {
    return this.getPriceFromText(page, this.orderTotalPriceSpan);
  }

  /**
   * Get document name
   * @param page
   * @param rowChild
   * @returns {Promise<string>}
   */
  async getDocumentName(page, rowChild = 1) {
    await this.goToDocumentsTab(page);

    return this.getTextContent(page, this.documentName(rowChild));
  }

  /**
   * Go to documents tab
   * @param page
   * @return {Promise<void>}
   */
  async goToDocumentsTab(page) {
    await Promise.all([
      page.click(this.documentTab),
      this.waitForVisibleSelector(page, `${this.documentTab}.active`),
    ]);
  }

  /**
   * Get file name
   * @param page
   * @param rowChild
   * @returns fileName
   */
  async getFileName(page, rowChild = 1) {
    await this.goToDocumentsTab(page);

    const fileName = await this.getTextContent(page, this.documentNumberLink(rowChild));
    return fileName.replace('#', '').trim();
  }

  /**
   * Download a document in document tab
   * @param page
   * @param row
   * @return {Promise<*>}
   */
  async downloadDocument(page, row) {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    await page.$eval(this.documentNumberLink(row), el => el.target = '');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.documentNumberLink(row)),
    ]);

    return download.path();
    /* eslint-enable no-return-assign, no-param-reassign */
  }

  /**
   * Download invoice
   * @param page
   * @returns {Promise<void>}
   */
  async downloadInvoice(page) {
    await this.goToDocumentsTab(page);

    return this.downloadDocument(page, 1);
  }

  /**
   * Click on partial refund button
   * @param page
   * @returns {Promise<void>}
   */
  async clickOnPartialRefund(page) {
    await page.click(this.partialRefundButton);
    await this.waitForVisibleSelector(page, this.refundProductQuantity(1));
  }

  /**
   * Add partial refund product
   * @param page
   * @param productRow
   * @param quantity
   * @param amount
   * @param shipping
   * @returns {Promise<string>}
   */
  async addPartialRefundProduct(page, productRow, quantity = 0, amount = 0, shipping = 0) {
    await this.setValue(page, this.refundProductQuantity(productRow), quantity.toString());
    if (amount !== 0) {
      await this.setValue(page, this.refundProductAmount(productRow), amount.toString());
    }
    if (shipping !== 0) {
      await this.setValue(page, this.refundShippingCost(productRow), shipping.toString());
    }
    await this.clickAndWaitForNavigation(page, this.partialRefundSubmitButton);
    return this.getTextContent(page, this.alertTextBlock);
  }

  /**
   * Download delivery slip
   * @param page
   * @returns {Promise<*>}
   */
  async downloadDeliverySlip(page) {
    /* eslint-disable no-return-assign, no-param-reassign */
    await this.goToDocumentsTab(page);

    // Delete the target because a new tab is opened when downloading the file
    return this.downloadDocument(page, 3);
    /* eslint-enable no-return-assign, no-param-reassign */
  }
}

module.exports = new Order();
