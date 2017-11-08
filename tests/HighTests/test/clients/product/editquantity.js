var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
var path = require('path');

class EditQuantity extends PrestashopClient {

  goToProductQuantity() {
    return this.client
      .scroll(0, 0)
      .waitForExist(selector.BO.AddProductPage.product_quantities_tab, 60000)
      .click(selector.BO.AddProductPage.product_quantities_tab)
  }

  productQuantity() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.product_quantity_input, 60000)
      .click(selector.BO.AddProductPage.product_quantity_input)
      .setValue(selector.BO.AddProductPage.product_quantity_input, data.common.quantity)
  }

  minQuantitySale() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.minimum_quantity_sale, 60000)
      .click(selector.BO.AddProductPage.minimum_quantity_sale)
      .setValue(selector.BO.AddProductPage.minimum_quantity_sale, data.common.qty_min)
  }

  packQuantity() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.pack_stock_type, 60000)
      .click(selector.BO.AddProductPage.pack_stock_type)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.pack_stock_type_option, 60000)
      .click(selector.BO.AddProductPage.pack_stock_type_option)
      .pause(2000)
  }

  associatedFile() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.virtual_associated_file, 60000)
      .click(selector.BO.AddProductPage.virtual_associated_file)
      .pause(2000)
  }

  addFile() {
    return this.client
      .scroll(0, 1200)
      .waitForExist(selector.BO.AddProductPage.virtual_select_file, 90000)
      .chooseFile(selector.BO.AddProductPage.virtual_select_file, path.join(__dirname, '../..', 'datas', 'image_test.jpg'))
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.virtual_file_name, 90000)
      .click(selector.BO.AddProductPage.virtual_file_name)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.virtual_file_name, data.virtual.attached_file_name)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.virtual_file_number_download, 90000)
      .click(selector.BO.AddProductPage.virtual_file_number_download)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.virtual_file_number_download, data.virtual.allowed_number_to_download)
      .waitForExist(selector.BO.AddProductPage.virtual_expiration_file_date, 90000)
      .click(selector.BO.AddProductPage.virtual_expiration_file_date)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.virtual_expiration_file_date, data.virtual.expiration_date)
      .waitForExist(selector.BO.AddProductPage.virtual_number_days, 90000)
      .click(selector.BO.AddProductPage.virtual_number_days)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.virtual_number_days, data.virtual.number_of_days)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.virtual_save_attached_file, 90000)
      .click(selector.BO.AddProductPage.virtual_save_attached_file)
  }

  selectAvailabilityPreferences(type) {
    if (type === 'virtual') {
      return this.client
        .scroll(0, 1000)
        .waitForExist(selector.BO.AddProductPage.pack_availability_preferences, 60000)
        .click(selector.BO.AddProductPage.pack_availability_preferences)
        .pause(2000)
    } else {
    return this.client
      .waitForExist(selector.BO.AddProductPage.pack_availability_preferences, 60000)
      .click(selector.BO.AddProductPage.pack_availability_preferences)
      .pause(2000)
    }
  }

  availableStock(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.pack_label_in_stock, 90000)
      .click(selector.BO.AddProductPage.pack_label_in_stock)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.pack_label_in_stock, data.common.qty_msg_stock)
      .pause(2000)
  }

  availableOutOfStock(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.pack_label_out_stock, 90000)
      .click(selector.BO.AddProductPage.pack_label_out_stock)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.pack_label_out_stock, data.common.qty_msg_unstock)
      .pause(2000)
  }

  availabilityDate(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.pack_availability_date, 90000)
      .click(selector.BO.AddProductPage.pack_availability_date)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.pack_availability_date, data.common.qty_date)
      .pause(2000)
  }

}

module.exports = EditQuantity;
