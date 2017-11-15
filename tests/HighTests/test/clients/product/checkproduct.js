var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
global.productName = '';
global.categoryName = '';

class CheckProductBO extends PrestashopClient {

  goToCatalog(type) {
    switch (type) {
      case 'virtual':
        productName = data.virtual.name + product_id;
        categoryName = data.virtual.new_category_name + product_id;
        break;
      case 'pack':
        productName = data.pack.name + product_id;
        categoryName = data.pack.new_category_name + product_id;
        break;
      case 'combination':
        productName = data.standard.name + 'Combination' + product_id;
        categoryName = data.standard.new_category_name + 'Combination' + product_id;
        break;
      default:
        productName = data.standard.name + product_id;
        categoryName = data.standard.new_category_name + product_id;
    }
    return this.client
      .pause(2000)
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .click(selector.BO.CatalogPage.menu_button)
  }

  searchProductByName() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.catalogue_filter_by_name_input, 90000)
      .click(selector.BO.AddProductPage.catalogue_filter_by_name_input)
      .pause(4000)
      .setValue(selector.BO.AddProductPage.catalogue_filter_by_name_input, productName)
      .pause(2000)
      .click(selector.BO.AddProductPage.click_outside)
      .waitForExist(selector.BO.AddProductPage.catalogue_submit_filter_button, 60000)
      .click(selector.BO.AddProductPage.catalogue_submit_filter_button)
      .pause(2000)
  }

  checkProductName() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_product_name, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_name))
      .then((text) => expect(text).to.be.equal(productName));
  }

  checkProductReference() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_product_reference, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_reference))
      .then((text) => expect(text).to.be.equal(data.common.product_reference));
  }

  checkProductCategory() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_product_category, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_category))
      .then((text) => expect(text).to.be.equal(categoryName));
  }

  checkProductPriceTE() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_product_price, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_price))
      .then((price) => price = price.replace('€', ''))
      .then((price) => expect(price).to.be.equal(data.common.priceTE + '.00'));
  }

  checkProductQuantity() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_product_quantity, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_quantity))
      .then((text) => expect(text).to.be.equal(data.common.quantity));
  }

  checkProductQuantityCombination() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_product_quantity, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_quantity))
      .then((text) => expect(text).to.be.equal((parseInt(data.standard.variations[0].quantity) + parseInt(data.standard.variations[1].quantity)).toString()));
  }

  checkProductStatus() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_product_online, 60000)
      .then(() => this.client.getText(selector.BO.AddProductPage.catalog_product_online))
      .then((text) => expect(text).to.be.equal('check'));
  }

  resetFilter() {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.catalog_reset_filter, 60000)
      .click(selector.BO.AddProductPage.catalog_reset_filter)
  }
}

module.exports = CheckProductBO;
