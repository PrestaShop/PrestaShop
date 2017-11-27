var CommonClient = require('./../common_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
var path = require('path');

class EditOptions extends CommonClient {

  goToOptionsForm() {
    return this.client.waitForExistAndClick(selector.BO.AddProductPage.product_options_tab)
  }

  selectVisibility() {
    return this.client.waitAndSelectByValue(selector.BO.AddProductPage.options_visibility,'search')
  }

  webOnlyVisibility() {
    return this.client.waitForExistAndClick(selector.BO.AddProductPage.options_online_only)
  }

  selectCondition() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.options_condition_select,50)
      .waitAndSelectByValue(selector.BO.AddProductPage.options_condition_select,'refurbished')
  }

  ISBNEntry() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.options_isbn, data.common.isbn)
  }

  EAN13Entry() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.options_ean13, data.common.ean13)
  }

  UPCEntry() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.options_upc,50)
      .waitAndSetValue(selector.BO.AddProductPage.options_upc, data.common.upc)
  }

  AddCustomFieldButton() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.options_add_customization_field_button,50)
      .waitForExistAndClick(selector.BO.AddProductPage.options_add_customization_field_button)
  }

  createCustomField() {
    return this.client
      .waitAndSetValue(selector.BO.AddProductPage.options_first_custom_field_label, data.common.personalization.perso_text.name)
      .waitAndSelectByValue(selector.BO.AddProductPage.options_first_custom_field_type,'0')
      .waitForExistAndClick(selector.BO.AddProductPage.options_first_custom_field_require)
  }

  newCustomField() {
    return this.client
      .waitAndSetValue(selector.BO.AddProductPage.options_second_custom_field_label, data.common.personalization.perso_file.name)
      .waitAndSelectByValue(selector.BO.AddProductPage.options_second_custom_field_type,'0')
  }

  attachNewFile() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.options_add_new_file_button,50)
      .waitForExistAndClick(selector.BO.AddProductPage.options_add_new_file_button)
  }

  addFile(fileName) {
    return this.client
      .scroll(0, 1200)
      .waitForExist(selector.BO.AddProductPage.options_select_file, 90000)
      .chooseFile(selector.BO.AddProductPage.options_select_file, path.join(__dirname, '../..', 'datas', fileName))
      .pause(2000)

      .waitAndSetValue(selector.BO.AddProductPage.options_file_name, data.common.document_attach.name)
      .waitAndSetValue(selector.BO.AddProductPage.options_file_description, data.common.document_attach.desc)
  }

  selectPreviousAddFile() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.options_file_add_button,50)
      .waitForExistAndClick(selector.BO.AddProductPage.options_file_add_button)
  }
}

module.exports = EditOptions;
