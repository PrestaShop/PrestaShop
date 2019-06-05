const Product = require('./product');
const {AddProductPage} = require('../../selectors/BO/add_product_page');

class CheckProductBO extends Product {

  searchProductByName(productName) {
    return this.client
      .waitForExistAndClick(AddProductPage.catalogue_filter_by_name_input)
      .waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, productName)
      .waitForExistAndClick(AddProductPage.catalogue_submit_filter_button);
  }
  checkProductPriceTE(priceTE) {
    return this.client
      .waitForExist(AddProductPage.catalog_product_price, 60000)
      .then(() => this.client.getText(AddProductPage.catalog_product_price))
      .then((price) => expect(price.replace('€', '')).to.be.equal(priceTE + '.00'));
  }
}

module.exports = CheckProductBO;
