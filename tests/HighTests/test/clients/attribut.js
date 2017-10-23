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

  searchForProduct(type) {
    return this.client
      .waitForExist(selector.FO.SearchProductPage.product_search_input, 90000)
      .setValue(selector.FO.SearchProductPage.product_search_input, 'test_nodejs_' + product_id)
      .click(selector.FO.SearchProductPage.product_search_button)
      .click(selector.FO.SearchProductPage.product_result_name)
    if (type === 'create') {
      return this.client
        .waitForExist(selector.FO.SearchProductPage.attribut_name, 90000)
        .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_name))
        .then((text) => expect(text).to.be.equal(global.attributeName));
    } else if ('update'){
      return this.client
        .waitForExist(selector.FO.SearchProductPage.attribut_name, 90000)
        .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_name))
        .then((text) => expect(text).to.be.equal(global.attributeName + 'update'));
    }

  }

  checkForProductAttributFO(type) {
    return this.client
    if (type === 'create') {
      return this.client
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[1]/label/span'))
        .then((text) => expect(text).to.be.equal('10'))
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[2]/label/span'))
        .then((text) => expect(text).to.be.equal('20'))
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[3]/label/span'))
        .then((text) => expect(text).to.be.equal('30'));
    } else if ('deleted') {
      return this.client
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[1]/label/span'))
        .then((text) => expect(text).to.be.equal('20'))
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[2]/label/span'))
        .then((text) => expect(text).to.be.equal('30'));
    } else {
      return this.client
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[1]/label/span'))
        .then((text) => expect(text).to.be.equal('40'))
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[2]/label/span'))
        .then((text) => expect(text).to.be.equal('20'))
        .then(() => this.client.getText('//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[3]/label/span'))
        .then((text) => expect(text).to.be.equal('30'));
    }
  }

  updateAttributName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.group_action_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.group_action_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.update_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.update_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.public_name_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.public_name_input, global.attributeName + 'update')
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.save_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.save_button)
  }

  updateAttributValue() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.update_value_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.update_value_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.value_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.value_input, "40")
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.save, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.save)
  }

  selectAttribut() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute)
  }

  deleteAttributValue() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.value_action_group_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.value_action_group_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.delete_value_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.delete_value_button)
      .alertAccept()
  }

  deleteAttribut() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.group_action_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.group_action_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.delete_attribut_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.delete_attribut_button)
      .alertAccept()
  }

  checkDeletedAttributFO() {
    return this.client
      .waitForExist(selector.FO.SearchProductPage.product_search_input, 90000)
      .setValue(selector.FO.SearchProductPage.product_search_input, 'test_nodejs_' + product_id)
      .click(selector.FO.SearchProductPage.product_search_button)
      .waitForExist(selector.FO.SearchProductPage.product_result_name, 90000)
      .click(selector.FO.SearchProductPage.product_result_name)
      .then(() => this.client.isExisting(selector.FO.SearchProductPage.attribut_name))
      .then((value) => expect(value).to.be.false);
  }

}

module.exports = Attribut;
