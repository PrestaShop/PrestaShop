var CommonClient = require('./../common_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
global.productName = '';
global.categoryName = '';

class CheckProductBO extends CommonClient {

  goToCatalog(type) {
    switch (type) {
      case 'virtual':
        productName = data.virtual.name + date_time;
        categoryName = data.virtual.new_category_name + date_time;
        break;
      case 'pack':
        productName = data.pack.name + date_time;
        categoryName = data.pack.new_category_name + date_time;
        break;
      case 'combination':
        productName = data.standard.name + 'Combination' + date_time;
        categoryName = data.standard.new_category_name + 'Combination' + date_time;
        break;
      default:
        productName = data.standard.name + date_time;
        categoryName = data.standard.new_category_name + date_time;
    }
    return this.client
      .pause(2000)
      .waitForExistAndClick(selector.BO.CatalogPage.menu_button)
  }

  searchProductByName() {
    return this.client
      .waitForExistAndClick(selector.BO.AddProductPage.catalogue_filter_by_name_input)
      .waitAndSetValue(selector.BO.AddProductPage.catalogue_filter_by_name_input,productName)
      .waitForExistAndClick(selector.BO.AddProductPage.click_outside)
      .waitForExistAndClick(selector.BO.AddProductPage.catalogue_submit_filter_button)
  }

  checkProductName() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_product_name, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_name))
      .then((text) => expect(text).to.be.equal(productName));
  }

  checkProductReference() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_product_reference, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_reference))
      .then((text) => expect(text).to.be.equal(data.common.product_reference));
  }

  checkProductCategory() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_product_category, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_category))
      .then((text) => expect(text).to.be.equal(categoryName));
  }

  checkProductPriceTE() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_product_price, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_price))
      .then((price) => price = price.replace('€', ''))
      .then((price) => expect(price).to.be.equal(data.common.priceTE + '.00'));
  }

  checkProductQuantity() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_product_quantity, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_quantity))
      .then((text) => expect(text).to.be.equal(data.common.quantity));
  }

  checkProductQuantityCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_product_quantity, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_quantity))
      .then((text) => expect(text).to.be.equal((parseInt(data.standard.variations[0].quantity) + parseInt(data.standard.variations[1].quantity)).toString()));
  }

  checkProductStatus() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_product_online, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_online))
      .then((text) => expect(text).to.be.equal('check'));
  }

  resetFilter() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalog_reset_filter, 60000)
      .click(selector.BO.AddProductPage.catalog_reset_filter)
  }
}

module.exports = CheckProductBO;
