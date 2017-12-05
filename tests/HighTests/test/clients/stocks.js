var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');

global.productQuantity = '';

class ModifyQuantity extends CommonClient {

  goToCatalogStock() {
    return this.client
      .waitForExist(selector.CatalogPage.menu_button, 90000)
      .moveToObject(selector.CatalogPage.menu_button)
      .waitForExist(selector.Stock.submenu, 90000)
      .click(selector.Stock.submenu)
  }

  goToStock() {
    return this.client
      .waitForExist(selector.Stock.tabs, 90000)
      .click(selector.Stock.tabs)
  }

  goToStockMovements() {
    return this.client
      .waitForExist(selector.Movement.tabs, 90000)
      .click(selector.Movement.tabs)
      .waitForExist(selector.Movement.variation, 90000)
      .pause(1000)
  }

  modifyFirstProductQuantity() {
    return this.client
      .waitForExist(selector.Stock.first_product_quantity, 90000)
      .then(() => this.client.getText(selector.Stock.first_product_quantity))
      .then((text) => global.productQuantity = text)
      .setValue(selector.Stock.first_product_quantity_input, '15')
      .then(() => this.client.getText(selector.Stock.first_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 15).toString()))
  }

  modifySecondProductQuantity() {
    return this.client
      .waitForExist(selector.Stock.second_product_quantity, 90000)
      .then(() => this.client.getText(selector.Stock.second_product_quantity))
      .then((text) => global.productQuantity = text)
      .waitForExist(selector.Stock.second_product_quantity_input, 90000)
      .setValue(selector.Stock.second_product_quantity_input, '50')
      .then(() => this.client.getText(selector.Stock.second_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 50).toString()));
  }

  saveGroupProduct() {
    return this.client
      .waitForExist(selector.Stock.group_apply_button, 90000)
      .click(selector.Stock.group_apply_button)
  }

  getThirdProductQuantity() {
    return this.client
      .waitForExist(selector.Stock.third_product_quantity, 90000)
      .then(() => this.client.getText(selector.Stock.third_product_quantity))
      .then((text) => global.productQuantity = text)
  }

  modifyThirdProductQuantity() {
    return this.client
      .waitForExist(selector.Stock.third_product_quantity_input, 90000)
      .setValue(selector.Stock.third_product_quantity_input,1)
      .click(selector.Stock.add_quantity_button)
      .click(selector.Stock.add_quantity_button)
      .then(() => this.client.getText(selector.Stock.third_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) + 3).toString()))
  }

  saveThirdProduct() {
    return this.client
      .waitForExist(selector.Stock.save_third_product_quantity_button, 90000)
      .click(selector.Stock.save_third_product_quantity_button)
  }

  getFourthProductQuantity() {
    return this.client
      .waitForExist(selector.Stock.fourth_product_quantity, 90000)
      .then(() => this.client.getText(selector.Stock.fourth_product_quantity))
      .then((text) => global.productQuantity = text)
  }

  modifyFourthProductQuantity() {
    return this.client
      .pause(2000)
      .click(selector.Stock.fourth_product_quantity_input)
      .setValue(selector.Stock.fourth_product_quantity_input, '0')
      .click(selector.Stock.remove_quantity_button)
      .click(selector.Stock.remove_quantity_button)
      .click(selector.Stock.remove_quantity_button)
      .pause(1000)
      .then(() => this.client.getText(selector.Stock.fourth_product_quantity_modified))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.productQuantity) - 3).toString()))
  }

  saveFourthProduct() {
    return this.client
      .waitForExist(selector.Stock.save_fourth_product_quantity_button, 90000)
      .click(selector.Stock.save_fourth_product_quantity_button)
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
