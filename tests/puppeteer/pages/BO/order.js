const BOBasePage = require('../BO/BObasePage');

module.exports = class Order extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Orders •';
    this.orderPageTitle = 'Order';

    // Orders page
    this.orderFilterIdInput = '#table-order th:nth-child(2) > input';
    this.orderFilterReferenceInput = '#table-order th:nth-child(3) > input';
    this.orderFilterStatusSelect = '#table-order th:nth-child(9) > select';
    this.searchButton = '#submitFilterButtonorder';
    this.resetButton = '#table-order button.btn.btn-warning';
    this.orderfirstLineIdTD = '#table-order  td:nth-child(2)';
    this.orderfirstLineReferenceTD = '#table-order td:nth-child(3)';
    this.orderfirstLineStatusTD = '#table-order td:nth-child(9)';

    //Order page
    this.editProductButton = '#orderProducts tr:nth-child(%ID) .edit_product_change_link';
    this.editProductQuantityInput = '#orderProducts tr:nth-child(%ID) span.product_quantity_edit > input';
    this.productQuantitySpan = '#orderProducts tr:nth-child(%ID) span.product_quantity_show.badge';
    this.UpdateProductButton = '#orderProducts tr:nth-child(%ID) .submitProductChange';
    this.orderStatusesSelect = '#id_order_state_chosen';
    this.orderStatusInput = '#id_order_state_chosen input[type="text"]';
    this.orderStatusSearchResult = '#id_order_state_chosen li:nth-child(1)';
    this.updateStatusButton = '#submit_state';
    this.statusValidation = '#status tr:nth-child(1) > td:nth-child(2)'
  }

  /*
  Methods
   */

  /**
   * Filter table with an input
   * @param selector, input to filter with
   * @param value, text to enter in the filter input
   * @param searchButton
   * @returns {Promise<void>}
   */
  async filterTableByInput(selector, value, searchButton) {
    await this.page.waitForSelector(selector);
    await this.page.type(selector, value);
    await this.page.click(searchButton)
  }

  /**
   * Filter table with a select option
   * @param selector
   * @param value, value to select in the filter select
   * @returns {Promise<void>}
   */
  async filterTableBySelect(selector, value) {
    await this.page.waitForSelector(selector);
    await this.page.select(selector, value);
  }

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
    await this.checkTextValue(this.productQuantitySpan.replace('%ID', id), quantity);
  }

  /**
   * Modify the order status
   * @param status
   * @returns {Promise<void>}
   */
  async modifyOrderStatus(status) {
    await this.waitForSelectorAndClick(this.orderStatusesSelect);
    await this.page.type(this.orderStatusInput, status);
    await this.page.click(this.orderStatusSearchResult);
    await this.page.click(this.updateStatusButton);
    await this.checkTextValue(this.statusValidation, status)
  }
};
