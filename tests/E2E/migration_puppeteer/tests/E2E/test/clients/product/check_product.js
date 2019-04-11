const Product = require('./product');
const {AddProductPage} = require('../../selectors/BO/add_product_page');

class CheckProductBO extends Product {

  async searchProductByName(productName) {
    await this.waitForExistAndClick(AddProductPage.catalogue_filter_by_name_input);
    await this.waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, productName);
    await this.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button);
  }
  async checkProductPriceTE(priceTE) {
    await this.waitForExist(AddProductPage.catalog_product_price, 6000);
    await page.$eval(AddProductPage.catalog_product_price, el => el.innerText).then((price) => {
      expect(price.replace('€', '')).to.be.equal(priceTE + '.00');
    });
  }
}

module.exports = CheckProductBO;
