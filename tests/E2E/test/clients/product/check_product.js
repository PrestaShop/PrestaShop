var CommonClient = require('./../common_client');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
var data = require('./../../datas/product-data');

class CheckProductBO extends CommonClient {

  goToCatalog() {
    return this.client
      .pause(2000)
      .waitForExistAndClick(CatalogPage.menu_button)
  }

  searchProductByName(productName) {
    return this.client
      .waitForExistAndClick(AddProductPage.catalogue_filter_by_name_input)
      .waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, productName)
      .waitForExistAndClick(AddProductPage.click_outside)
      .waitForExistAndClick(AddProductPage.catalogue_submit_filter_button)
  }

  checkProductPriceTE(priceTE) {
    return this.client
      .waitForExist(AddProductPage.catalog_product_price, 60000)
      .then(() => this.client.getText(AddProductPage.catalog_product_price))
      .then((price) => expect(price.replace('€', '')).to.be.equal(priceTE + '.00'));
  }

}

module.exports = CheckProductBO;
