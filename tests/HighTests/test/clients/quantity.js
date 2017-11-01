var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

global.productQuantity = '';

class ModifyQuantity extends PrestashopClient {

  goToCatalogStock() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.submenu, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.submenu)
  }

  goToStock() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.tabs, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.tabs)
  }

  goToStockMovements() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Movements.tabs, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.Movements.tabs)
  }

  modifyFirstProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.first_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.first_product_quantity))
      .then((text) => global.productQuantity = text)
      .setValue(selector.BO.CatalogPage.StockSubmenu.Stock.first_product_quantity_input, '15')
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.first_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 15).toString()))
  }

  modifySecondProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.second_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.second_product_quantity))
      .then((text) => global.productQuantity = text)
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.second_product_quantity_input, 90000)
      .setValue(selector.BO.CatalogPage.StockSubmenu.Stock.second_product_quantity_input, '50')
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.second_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 50).toString()));
  }

  saveGroupProduct() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.group_apply_button, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.group_apply_button)
      .pause(1000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.success_panel))
      .then((text) => expect(text.substring(2)).to.be.equal('Stock successfully updated'))
      .pause(3000)
  }

  getThirdProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.third_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.third_product_quantity))
      .then((text) => global.productQuantity = text)
  }

  modifyThirdProductQuantity() {
    return this.client
      .pause(4000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.third_product_quantity_input)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.add_quantity_button)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.add_quantity_button)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.add_quantity_button)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.third_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 3).toString()))
  }

  saveThirdProduct() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.save_third_product_quantity_button, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.save_third_product_quantity_button)
      .pause(1000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.success_panel))
      .then((text) => expect(text.substring(2)).to.be.equal('Stock successfully updated'))
  }

  getFourthProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity))
      .then((text) => global.productQuantity = text)
  }

  modifyFourthProductQuantity() {
    return this.client
      .pause(4000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity_input)
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity_input, 90000)
      .setValue(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity_input, '-50')
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) - 50).toString()))
  }

  saveFourthProduct() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.save_fourth_product_quantity_button, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.save_fourth_product_quantity_button)
      .pause(1000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.success_panel))
      .then((text) => expect(text.substring(2)).to.be.equal('Stock successfully updated'))
  }

  checkMovements(quantity, variation) {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Movements.variation, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Movements.variation))
      .then((text) => expect(text).to.be.equal(variation))
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Movements.quantity))
      .then((text) => expect(text.substring(2)).to.be.equal(quantity))
  }

}

module.exports = ModifyQuantity;
