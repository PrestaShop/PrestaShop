var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

global.attributeName = 'attribute' + new Date().getTime();

class Attribut extends PrestashopClient {

  goToAttributeList() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.submenu, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.submenu)
  }

  createAttribute() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.add_new_attribute, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.add_new_attribute)
  }

  addAttributeName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.name_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.name_input, global.attributeName)
  }

  addAttributePublicName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.public_name_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.public_name_input, global.attributeName)
  }

  addAttributeType() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.type_select, 90000)
      .selectByValue(selector.BO.CatalogPage.AttributeSubmenu.type_select, 'radio')
  }

  saveNewAttribute() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.save_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.save_button)
  }

  searchAttribute() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.search_input, 90000)
      .setValue(selector.BO.CatalogPage.AttributeSubmenu.search_input, global.attributeName)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.search_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.search_button)
  }

  selectAttribute() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute)
  }

  addValueToAttribute() {
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
      .setValue(selector.FO.SearchProductPage.product_search_input, product_id)
      .click(selector.FO.SearchProductPage.product_search_button)
      .click(selector.FO.SearchProductPage.product_result_name)

  }

  checkCreatedAttributeName() {
    return this.client
      .waitForExist(selector.FO.SearchProductPage.attribut_name, 90000)
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_name))
      .then((text) => expect(text).to.be.equal(global.attributeName));
  }

  checkUpdatedAttributeName() {
    return this.client
      .waitForExist(selector.FO.SearchProductPage.attribut_name, 90000)
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_name))
      .then((text) => expect(text).to.be.equal(global.attributeName + 'update'));
  }

  checkCreatedAttributeValue() {
    return this.client
      .waitForExist(selector.FO.SearchProductPage.attribut_value_1, 90000)
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_1))
      .then((text) => expect(text).to.be.equal('10'))
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_2))
      .then((text) => expect(text).to.be.equal('20'))
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_3))
      .then((text) => expect(text).to.be.equal('30'));
  }

  checkUpdatedAttributeValue() {
    return this.client
      .waitForExist(selector.FO.SearchProductPage.attribut_value_1, 90000)
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_1))
      .then((text) => expect(text).to.be.equal('40'))
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_2))
      .then((text) => expect(text).to.be.equal('20'))
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_3))
      .then((text) => expect(text).to.be.equal('30'));
  }

  checkdeletedAttributeValue() {
    return this.client
      .waitForExist(selector.FO.SearchProductPage.attribut_value_1, 90000)
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_1))
      .then((text) => expect(text).to.be.equal('20'))
      .then(() => this.client.getText(selector.FO.SearchProductPage.attribut_value_2))
      .then((text) => expect(text).to.be.equal('30'));
  }

  updateAttributeName() {
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

  updateAttributeValue() {
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

  selectAttribute() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.selected_attribute)
  }

  deleteAttributeValue() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.value_action_group_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.value_action_group_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.delete_value_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.delete_value_button)
      .alertAccept()
  }

  deleteAttribute() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.group_action_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.group_action_button)
      .waitForExist(selector.BO.CatalogPage.AttributeSubmenu.delete_attribut_button, 90000)
      .click(selector.BO.CatalogPage.AttributeSubmenu.delete_attribut_button)
      .alertAccept()
  }

  checkDeletedAttributeFO() {
    return this.client
      .waitForExist(selector.FO.ProductPage.title, 90000)
      .then(() => this.client.isExisting(selector.FO.SearchProductPage.attribut_name))
      .then((value) => expect(value).to.be.false);
  }

}

module.exports = Attribut;
