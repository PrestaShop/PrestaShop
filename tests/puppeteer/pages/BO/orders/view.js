require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Order extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order';

    // Order page
    this.orderProductsTable = '#orderProducts';
    this.orderProductsRowTable = `${this.orderProductsTable} tr:nth-child(%ID)`;
    this.editProductButton = `${this.orderProductsRowTable} .edit_product_change_link`;
    this.editProductQuantityInput = `${this.orderProductsRowTable} span.product_quantity_edit > input`;
    this.productQuantitySpan = `${this.orderProductsRowTable} span.product_quantity_show.badge`;
    this.UpdateProductButton = `${this.orderProductsRowTable} .submitProductChange`;
    this.orderStatusesSelect = '#id_order_state_chosen';
    this.orderStatusesSearchInput = `${this.orderStatusesSelect} input[type='text']`;
    this.orderStatusSearchResult = `${this.orderStatusesSelect} li:nth-child(1)`;
    this.updateStatusButton = '#submit_state';
    this.statusValidation = '#status tr:nth-child(1) > td:nth-child(2)';
    this.documentTab = '#tabOrder a[href=\'#documents\']';
    this.documentName = '#documents_table tr td:nth-child(2)';
    this.documentNumberLink = '#documents_table tr td:nth-child(3) a';
  }

  /*
  Methods
   */

  /**
   * Modify the product quantity
   * @param id, product id
   * @param quantity, new quantity
   * @returns {Promise<void>}
   */
  async modifyProductQuantity(id, quantity) {
    await this.dialogListener();
    await this.waitForSelectorAndClick(this.editProductButton.replace('%ID', id));
    await this.setValue(this.editProductQuantityInput.replace('%ID', id), quantity);
    await this.waitForSelectorAndClick(this.UpdateProductButton.replace('%ID', id));
    return this.checkTextValue(this.productQuantitySpan.replace('%ID', id), quantity);
  }

  /**
   * Modify the order status
   * @param status
   * @returns {Promise<void>}
   */
  async modifyOrderStatus(status) {
    await this.waitForSelectorAndClick(this.orderStatusesSelect);
    await this.page.type(this.orderStatusesSearchInput, status);
    await this.page.click(this.orderStatusSearchResult);
    await this.page.click(this.updateStatusButton);
    return this.checkTextValue(this.statusValidation, status);
  }

  /**
   * Get document name
   * @returns {Promise<void>}
   */
  async getDocumentName() {
    await this.page.click(this.documentTab);
    return this.getTextContent(this.documentName);
  }

  /**
   * Get file name
   * @returns fileName
   */
  async getFileName() {
    await this.page.click(this.documentTab);
    const fileName = await this.getTextContent(this.documentNumberLink);
    return fileName.replace('#', '').trim();
  }

  /**
   * Download invoice
   * @returns {Promise<void>}
   */
  async downloadInvoice() {
    /* eslint-disable */
    // Delete the target because a new tab is opened when downloading the file
    await this.page.$eval(this.documentNumberLink, el => el.target = '');
    await this.page.click(this.documentNumberLink);
    /* eslint-enable */
  }
};
