var Product = require('./product/product');


class Catalog extends Product {

  selectAllProducts(selector) {
    return this.client
      .refresh()
      .waitForExistAndClick(selector);
  }

  selectAction(CatalogPage, action) {
    return this.client
      .waitForExistAndClick(CatalogPage.action_group_button)
      .waitForExistAndClick(CatalogPage.action_button.replace('%ID', action));
  }

  checkExistenceProduct(selector, textToCheckWith, pause = 0) {
    return this.client
      .pause(pause)
      .waitForExist(selector, 9000)
      .then(() => this.client.getText(selector))
      .then((text) => expect(text.toLowerCase()).to.equal(textToCheckWith.toLowerCase()));
  }
}

module.exports = Catalog;
