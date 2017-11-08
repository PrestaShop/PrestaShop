var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');

class Product extends PrestashopClient {

  goToProductMenu() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.menu, 90000)
      .click(selector.BO.AddProductPage.products_subtab)
  }

  addNewProduct() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.new_product_button, 90000)
      .click(selector.BO.AddProductPage.new_product_button)
  }

  closeGreenValidation() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.close_validation_button, 90000)
      .click(selector.BO.AddProductPage.close_validation_button)
  }

  saveProduct() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.save_product_button, 90000)
      .click(selector.BO.AddProductPage.save_product_button)
  }
}

module.exports = Product;
