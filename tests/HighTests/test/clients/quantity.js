var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

global.productQuantity = '';

class ModifyQuantity extends PrestashopClient {

  goToCatalogStock() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.submenu, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.submenu)
  }

  modifyFirstProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.first_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.first_product_quantity))
      .then((text) => global.productQuantity = text)
      .setValue(selector.BO.CatalogPage.StockSubmenu.first_product_quantity_input, '15')
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.first_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 15).toString()))
  }

  modifySecondProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.second_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.second_product_quantity))
      .then((text) => global.productQuantity = text)
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.second_product_quantity_input, 90000)
      .setValue(selector.BO.CatalogPage.StockSubmenu.second_product_quantity_input, '50')
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.second_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 50).toString()));
  }

  modifyThirdProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.third_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.third_product_quantity))
      .then((text) => global.productQuantity = text)

      .click(selector.BO.CatalogPage.StockSubmenu.submenu)
  }

  save() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.save_button, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.save_button)
  }

}

module.exports = ModifyQuantity;
