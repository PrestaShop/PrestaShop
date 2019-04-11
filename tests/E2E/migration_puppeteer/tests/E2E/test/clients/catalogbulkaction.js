var Product = require('./product/product');


class Catalog extends Product {

  async selectAllProducts(selector) {
    await page.$eval(selector, (selector) => {
      selector.click();
    });
  }

  async selectAction(CatalogPage, action) {
    await this.waitForExistAndClick(CatalogPage.action_group_button)
    await this.waitForExistAndClick(CatalogPage.action_button.replace('%ID', action));
  }

  checkExistenceProduct(selector, textToCheckWith, pause = 0) {
    return page
      .waitForSelector(selector)
      .then(() => this.getText(selector))
      .then((text) => expect(text.toLowerCase()).to.equal(textToCheckWith.toLowerCase()));
  }
}

module.exports = Catalog;
