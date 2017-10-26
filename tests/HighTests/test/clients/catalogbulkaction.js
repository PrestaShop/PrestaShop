var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');


class Catalog extends PrestashopClient {

  goToCatalog() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .click(selector.BO.CatalogPage.menu_button)
  }

  selectAllProduct() {
    return this.client
      .refresh()
      .waitForExist(selector.BO.CatalogPage.select_all_product_button, 90000)
      .click(selector.BO.CatalogPage.select_all_product_button)
  }

  enableProductlist() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.action_group_button, 90000)
      .click(selector.BO.CatalogPage.action_group_button)
      .waitForExist(selector.BO.CatalogPage.enable_all_selected, 90000)
      .click(selector.BO.CatalogPage.enable_all_selected)
      .waitForVisible(selector.BO.CatalogPage.succes_panel_all_item_message, 90000);
  }

  disableProductlist() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.action_group_button, 90000)
      .click(selector.BO.CatalogPage.action_group_button)
      .waitForExist(selector.BO.CatalogPage.disable_all_selected, 90000)
      .click(selector.BO.CatalogPage.disable_all_selected)
      .waitForVisible(selector.BO.CatalogPage.succes_panel_all_item_message, 90000);
  }

  duplicateProductlist() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.action_group_button, 90000)
      .click(selector.BO.CatalogPage.action_group_button)
      .waitForExist(selector.BO.CatalogPage.duplicate_button, 90000)
      .click(selector.BO.CatalogPage.duplicate_button)
      .waitForVisible(selector.BO.CatalogPage.succes_panel_all_item_message, 90000);
  }

  checkProductListMsg(msg, etat) {
    return this.client
      .waitForExist(selector.BO.CatalogPage.succes_panel_all_item_message, 90000)
      .then(() => this.client.getText(selector.BO.CatalogPage.succes_panel_all_item_message))
      .then((text) => expect(text).to.be.equal(msg))
      .then(() => this.client.getText(selector.BO.CatalogPage.etat_first_product))
      .then((text) => expect(text).to.be.equal(etat))
      .then(() => this.client.getText(selector.BO.CatalogPage.etat_last_product))
      .then((text) => expect(text).to.be.equal(etat));
  }
}

module.exports = Catalog;
