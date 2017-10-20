const {getClient} = require('../common.webdriverio.js');
const {selector} = require('../globals.webdriverio.js');
const PrestashopClient = require('./prestashop_client');

global.attributeName = 'attribute' + new Date().getTime();

class Attribut extends PrestashopClient {

  goToAttributList() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.submenu, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.submenu)
  }

  createAttribut() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.add_new_attribute, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.add_new_attribute)
  }

  addAttributName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.name_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.name_input, global.attributeName)
  }

  addAttributPublicName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.public_name_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.public_name_input, global.attributeName)
  }

  addAttributType() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.type_select, 90000)
      .selectByValue(selector.BO.CatalogPage.AttributeSubmenu.type_select, 'radio')
  }

  saveNewAttribut() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.save_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.save_button)
  }

// new

  searchAttribut() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.search_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.search_input, global.attributeName)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.search_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.search_button)
  }

  selectAttribut() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute)
  }

  addValueToAttribut() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.add_value_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.add_value_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.value_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.value_input, "10")
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.save_and_add, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.save_and_add)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.value_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.value_input, "20")
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.save_and_add, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.save_and_add)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.value_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.value_input, "30")
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.save, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.save)
  }
}

module.exports = Attribut;
