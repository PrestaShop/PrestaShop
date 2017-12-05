var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');


class Catalog extends CommonClient {

  goToCatalog() {
    return this.client
      .waitForExist(selector.CatalogPage.menu_button, 90000)
      .moveToObject(selector.CatalogPage.menu_button)
      .click(selector.CatalogPage.menu_button)
  }

  selectAllProduct() {
    return this.client
      .refresh()
      .waitForExist(selector.CatalogPage.select_all_product_button, 90000)
      .click(selector.CatalogPage.select_all_product_button)
  }

  enableProductlist() {
    return this.client
      .waitForExist(selector.CatalogPage.action_group_button, 90000)
      .click(selector.CatalogPage.action_group_button)
      .waitForExist(selector.CatalogPage.enable_all_selected, 90000)
      .click(selector.CatalogPage.enable_all_selected)
      .waitForVisible(selector.CatalogPage.succes_panel_all_item_message, 90000);
  }

  disableProductlist() {
    return this.client
      .waitForExist(selector.CatalogPage.action_group_button, 90000)
      .click(selector.CatalogPage.action_group_button)
      .waitForExist(selector.CatalogPage.disable_all_selected, 90000)
      .click(selector.CatalogPage.disable_all_selected)
      .waitForVisible(selector.CatalogPage.succes_panel_all_item_message, 90000);
  }

  duplicateProductlist() {
    return this.client
      .waitForExist(selector.CatalogPage.action_group_button, 90000)
      .click(selector.CatalogPage.action_group_button)
      .waitForExist(selector.CatalogPage.duplicate_button, 90000)
      .click(selector.CatalogPage.duplicate_button)
      .waitForVisible(selector.CatalogPage.succes_panel_all_item_message, 90000);
  }

  checkProductListMsg(msg, etat) {
    return this.client
      .waitForExist(selector.CatalogPage.succes_panel_all_item_message, 90000)
      .then(() => this.client.getText(selector.CatalogPage.succes_panel_all_item_message))
      .then((text) => expect(text).to.be.equal(msg))
      .then(() => this.client.getText(selector.CatalogPage.etat_first_product))
      .then((text) => expect(text).to.be.equal(etat))
      .then(() => this.client.getText(selector.CatalogPage.etat_last_product))
      .then((text) => expect(text).to.be.equal(etat));
  }
}

module.exports = Catalog;
