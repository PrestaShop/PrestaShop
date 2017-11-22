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
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Movements.variation, 90000)
      .pause(1000)
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
  }

  getThirdProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.third_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.third_product_quantity))
      .then((text) => global.productQuantity = text)
  }

  modifyThirdProductQuantity() {
    return this.client
      .pause(2000)
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
  }

  getFourthProductQuantity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity))
      .then((text) => global.productQuantity = text)
  }

  modifyFourthProductQuantity() {
    return this.client
      .pause(2000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity_input)
      .setValue(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity_input, '0')
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.remove_quantity_button)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.remove_quantity_button)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.remove_quantity_button)
      .pause(1000)
      .then(() => this.client.getText(selector.BO.CatalogPage.StockSubmenu.Stock.fourth_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) - 3).toString()))
  }

  saveFourthProduct() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.StockSubmenu.Stock.save_fourth_product_quantity_button, 90000)
      .click(selector.BO.CatalogPage.StockSubmenu.Stock.save_fourth_product_quantity_button)
      .pause(2000)
  }

  checkMovement(order, quantity, variation, type) {
    return this.client
      .waitForVisible('//*[@id="app"]/div[3]/section/table/tbody/tr[' + order + ']/td[4]/span/span', 90000)
      .then(() => this.client.getText('//*[@id="app"]/div[3]/section/table/tbody/tr[' + order + ']/td[4]/span/span'))
      .then((text) => expect(text).to.be.equal(variation))
      .then(() => this.client.getText('//*[@id="app"]/div[3]/section/table/tbody/tr[' + order + ']/td[4]/span'))
      .then((text) => expect(text.substring(2)).to.be.equal(quantity))
      .then(() => this.client.getText('//*[@id="app"]/div[3]/section/table/tbody/tr[' + order + ']/td[3]'))
      .then((text) => expect(text.indexOf(type)).to.not.equal(-1))
  }

}

module.exports = ModifyQuantity;
