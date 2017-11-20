var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');

global.productIdElement=[];

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
  getElementID(){
    return this.client
      .waitForExist(selector.BO.ProductList.first_product_id, 90000)
      .then(() => this.client.getText(selector.BO.ProductList.first_product_id))
      .then((text) => global.productIdElement[0]=text)
      .then(() => this.client.getText(selector.BO.ProductList.second_product_id))
      .then((text) => global.productIdElement[1]=text)
      .then(() => this.client.getText(selector.BO.ProductList.third_product_id))
      .then((text) => global.productIdElement[2]=text)
      .then((text) => expect(Number(global.productIdElement[0])).to.be.above(Number(global.productIdElement[1])))
      .then((text) => expect(Number(global.productIdElement[1])).to.be.above(Number(global.productIdElement[2])));
  }

  expandArrow(){
    return this.client
      .scroll(0,600)
      .waitForVisible(selector.BO.AddProductPage.catalog_home, 90000)
      .click(selector.BO.AddProductPage.catalog_home)
  }

  checkCategoryRadioButton(i,j) {
    return this.client
      .waitForVisible('//*[@id="form_step1_categories"]/ul/li/ul/li[1]/ul/li['+i+']/ul/li['+j+']/div/div/input')
      .scroll(0, 1000)
      .isVisible('//*[@id="form_step1_categories"]/ul/li/ul/li[1]/ul/li['+i+']/ul/li['+j+']/div/div/input', 60000)
      .then((text) => expect(text).to.be.true);
  }

  openAllCategory(){
    return this.client
      .scroll(0, 1000)
      .waitForExist(selector.BO.AddProductPage.catalog_home, 90000)
      .click(selector.BO.AddProductPage.catalog_home)
      .waitForExist(selector.BO.AddProductPage.catalog_first_element_radio, 90000)
      .click(selector.BO.AddProductPage.catalog_first_element_radio)
      .waitForExist(selector.BO.AddProductPage.catalog_second_element_radio, 90000)
      .click(selector.BO.AddProductPage.catalog_second_element_radio)
      .scroll(0, 1000)
      .waitForExist(selector.BO.AddProductPage.catalog_third_element_radio, 90000)
      .click(selector.BO.AddProductPage.catalog_third_element_radio)
  }
}

module.exports = Product;
