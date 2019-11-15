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
};
