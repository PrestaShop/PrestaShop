var CommonClient = require('./../common_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
var path = require('path');

class EditQuantity extends CommonClient {

  goToProductQuantity() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.product_quantities_tab, 50)
      .waitForExistAndClick(selector.BO.AddProductPage.product_quantities_tab)
  }

  productQuantity() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.product_quantity_input, data.common.quantity)
  }

  minQuantitySale() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.minimum_quantity_sale, data.common.qty_min)
  }

  packQuantity() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.pack_stock_type, 60000)
      .selectByValue(selector.BO.AddProductPage.pack_stock_type, '2')
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
      .waitForExist(selector.BO.AddProductPage.virtual_file_number_download, 90000)
      .click(selector.BO.AddProductPage.virtual_file_number_download)
      .setValue(selector.BO.AddProductPage.virtual_file_number_download, data.virtual.allowed_number_to_download)
      .waitForExist(selector.BO.AddProductPage.virtual_expiration_file_date, 90000)
      .click(selector.BO.AddProductPage.virtual_expiration_file_date)
      .setValue(selector.BO.AddProductPage.virtual_expiration_file_date, data.virtual.expiration_date)
      .waitForExist(selector.BO.AddProductPage.virtual_number_days, 90000)
      .click(selector.BO.AddProductPage.virtual_number_days)
      .setValue(selector.BO.AddProductPage.virtual_number_days, data.virtual.number_of_days)
      .waitForExist(selector.BO.AddProductPage.virtual_save_attached_file, 90000)
      .click(selector.BO.AddProductPage.virtual_save_attached_file)
  }

  selectAvailabilityPreferences(type) {
    if (type === 'virtual') {
      return this.client
        .scrollTo(selector.BO.AddProductPage.pack_availability_preferences, 50)
        .waitForExistAndClick(selector.BO.AddProductPage.pack_availability_preferences)
    } else {
      return this.client.waitForExistAndClick(selector.BO.AddProductPage.pack_availability_preferences)
    }
  }

  availableStock() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.pack_label_in_stock, data.common.qty_msg_stock)
  }

  availableOutOfStock() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.pack_label_out_stock, 50)
      .waitAndSetValue(selector.BO.AddProductPage.pack_label_out_stock, data.common.qty_msg_unstock)
  }

  availabilityDate() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.pack_availability_date, data.common.qty_date)
  }

}

module.exports = EditQuantity;
