var CommonClient = require('./../common_client');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {CatalogPageBO} = require('../../selectors/BO/catalogpage/index');
var data = require('./../../datas/product-data');

class CheckProductBO extends CommonClient {

  goToCatalog() {
    return this.client
      .pause(2000)
      .waitForExistAndClick(CatalogPageBO.menu_button)
  }

  searchProductByName(productName) {
    return this.client
      .waitForExistAndClick(AddProductPage.catalogue_filter_by_name_input)
      .waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, productName)
      .waitForExistAndClick(AddProductPage.click_outside)
      .waitForExistAndClick(AddProductPage.catalogue_submit_filter_button)
  }

  checkProductPriceTE() {
    return this.client
      .waitForExist(AddProductPage.catalog_product_price, 60000)
      .then(() => this.client.getText(AddProductPage.catalog_product_price))
      .then((price) => price = price.replace('€', ''))
      .then((price) => expect(price).to.be.equal(data.common.priceTE + '.00'));
  }

}

module.exports = CheckProductBO;
