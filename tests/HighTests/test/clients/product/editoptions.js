var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
var path = require('path');

class EditOptions extends PrestashopClient {

  goToOptionsForm() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.product_options_tab, 90000)
      .click(selector.BO.AddProductPage.product_options_tab)
  }

  selectVisibility() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.options_visibility, 90000)
      .selectByValue(selector.BO.AddProductPage.options_visibility,'search')
  }

  webOnlyVisibility() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.options_online_only, 90000)
      .click(selector.BO.AddProductPage.options_online_only)
  }

  selectCondition() {
    return this.client
      .scroll(0, 500)
      .waitForExist(selector.BO.AddProductPage.options_condition_select, 90000)
      .selectByValue(selector.BO.AddProductPage.options_condition_select,'refurbished')
  }

  ISBNEntry() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.options_isbn, 90000)
      .click(selector.BO.AddProductPage.options_isbn)

      .setValue(selector.BO.AddProductPage.options_isbn, data.common.isbn)
  }

  EAN13Entry() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.options_ean13, 90000)
      .click(selector.BO.AddProductPage.options_ean13)
      .setValue(selector.BO.AddProductPage.options_ean13, data.common.ean13)
  }

  UPCEntry() {
    return this.client
      .scroll(0.600)
      .waitForExist(selector.BO.AddProductPage.options_upc, 90000)
      .setValue(selector.BO.AddProductPage.options_upc, data.common.upc)
  }

  customizationButton() {
    return this.client
      .scroll(0, 800)
      .waitForExist(selector.BO.AddProductPage.options_add_customization_field_button, 90000)
      .click(selector.BO.AddProductPage.options_add_customization_field_button)
  }

  createCustomField() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.options_first_custom_field_label, 90000)
      .click(selector.BO.AddProductPage.options_first_custom_field_label)
      .setValue(selector.BO.AddProductPage.options_first_custom_field_label, data.common.personalization.perso_text.name)
      .waitForExist(selector.BO.AddProductPage.options_first_custom_field_type, 90000)
      .click(selector.BO.AddProductPage.options_first_custom_field_type)
      .waitForExist(selector.BO.AddProductPage.options_first_custom_field_require, 90000)
      .click(selector.BO.AddProductPage.options_first_custom_field_require)
  }

  AddCustomFieldButton() {
    return this.client
      .scroll(0, 800)
      .waitForExist(selector.BO.AddProductPage.options_add_customization_field_button, 90000)
      .click(selector.BO.AddProductPage.options_add_customization_field_button)
  }

  newCustomField() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.options_second_custom_field_label, 90000)
      .click(selector.BO.AddProductPage.options_second_custom_field_label)
      .setValue(selector.BO.AddProductPage.options_second_custom_field_label, data.common.personalization.perso_file.name)
      .waitForExist(selector.BO.AddProductPage.options_second_custom_field_type, 90000)
      .selectByValue(selector.BO.AddProductPage.options_second_custom_field_type, 0)
  }

  attachNewFile() {
    return this.client
      .scroll(0, 1200)
      .waitForExist(selector.BO.AddProductPage.options_add_new_file_button, 90000)
      .click(selector.BO.AddProductPage.options_add_new_file_button)
  }

  addFile(fileName) {
    return this.client
      .scroll(0, 1200)
      .waitForExist(selector.BO.AddProductPage.options_select_file, 90000)
      .chooseFile(selector.BO.AddProductPage.options_select_file, path.join(__dirname, '../..', 'datas', fileName))
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.options_file_name, 90000)
      .click(selector.BO.AddProductPage.options_file_name)
      .setValue(selector.BO.AddProductPage.options_file_name, data.common.document_attach.name)
      .waitForExist(selector.BO.AddProductPage.options_file_description, 90000)
      .click(selector.BO.AddProductPage.options_file_description)
      .setValue(selector.BO.AddProductPage.options_file_description, data.common.document_attach.desc)
  }

  selectPreviousAddFile() {
    return this.client
      .scroll(0, 1200)
      .waitForExist(selector.BO.AddProductPage.options_file_add_button, 90000)
      .scroll(0, 1200)
      .click(selector.BO.AddProductPage.options_file_add_button)
  }
}

module.exports = EditOptions;
