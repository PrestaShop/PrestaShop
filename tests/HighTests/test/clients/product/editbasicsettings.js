var CommonClient = require('./../common_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
var path = require('path');

class EditBasicSettings extends CommonClient {

  setProductName(type) {
    if (type === 'virtual') {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_name_input, data.virtual.name + date_time)
    } else if (type === 'pack') {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_name_input, data.pack.name + date_time)
    } else if (type === 'combination') {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_name_input, data.standard.name + 'Combination' + date_time)
    } else {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_name_input, data.standard.name + date_time)
    }
  }

  setProductType(type) {
    if (type === 'virtual') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_type, 90000)
        .selectByValue(selector.BO.AddProductPage.product_type, 2)
    } else if (type === 'pack') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_type, 90000)
        .selectByValue(selector.BO.AddProductPage.product_type, 1)
    } else if (type === 'combination') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_combinations, 90000)
        .click(selector.BO.AddProductPage.product_combinations)
    }
  }

  addPackProduct1() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.search_product_pack, 60000)
      .click(selector.BO.AddProductPage.search_product_pack)
      .setValue(selector.BO.AddProductPage.search_product_pack, data.pack.pack.pack1.search)
      .waitForExist(selector.BO.AddProductPage.product_item_pack, 60000)
      .click(selector.BO.AddProductPage.product_item_pack)
      .waitForExist(selector.BO.AddProductPage.product_pack_item_quantity, 60000)
      .click(selector.BO.AddProductPage.product_pack_item_quantity)
      .setValue(selector.BO.AddProductPage.product_pack_item_quantity, data.pack.pack.pack1.quantity)
      .waitForExist(selector.BO.AddProductPage.product_pack_add_button, 60000)
      .click(selector.BO.AddProductPage.product_pack_add_button)
  }

  addPackProduct2() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.search_product_pack, 60000)
      .click(selector.BO.AddProductPage.search_product_pack)
      .setValue(selector.BO.AddProductPage.search_product_pack, data.pack.pack.pack2.search)
      .waitForExist(selector.BO.AddProductPage.product_item_pack, 60000)
      .click(selector.BO.AddProductPage.product_item_pack)
      .waitForExist(selector.BO.AddProductPage.product_pack_item_quantity, 60000)
      .click(selector.BO.AddProductPage.product_pack_item_quantity)
      .setValue(selector.BO.AddProductPage.product_pack_item_quantity, data.pack.pack.pack2.quantity)
      .waitForExist(selector.BO.AddProductPage.product_pack_add_button, 60000)
      .click(selector.BO.AddProductPage.product_pack_add_button)
  }

  setQuantity() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.quantity_shortcut_input, "10")
  }

  uploadPicture(fileName) {
    return this.client
      .execute(function () {
        document.getElementsByClassName("dz-hidden-input").style = "";
      })
      .chooseFile(selector.BO.AddProductPage.picture, path.join(__dirname, '../..', 'datas', fileName))
  }

  setSummary() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.add_summary_textarea, 90000)
      .click(selector.BO.AddProductPage.add_summary_textarea)
      .waitForExist(selector.BO.AddProductPage.add_summary_textarea, 90000)
      .setValue(selector.BO.AddProductPage.add_summary_textarea, "this is summary")
      .click(selector.BO.AddProductPage.save_summary_button)
  }

  setDescription() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.tab_description, 90000)
      .moveToObject(selector.BO.AddProductPage.add_summary_textarea)
      .click(selector.BO.AddProductPage.add_summary_textarea)
      .waitForExist(selector.BO.AddProductPage.add_summary_textarea, 90000)
      .setValue(selector.BO.AddProductPage.add_summary_textarea, "this is desciption")
      .click(selector.BO.AddProductPage.save_summary_button)

  }

  addCategory() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.product_create_category_btn,50)
      .waitForExistAndClick(selector.BO.AddProductPage.product_create_category_btn)
  }

  setCategoryName(type) {
    if (type === 'virtual') {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_category_name_input, data.virtual.new_category_name + date_time)
    } else if (type === 'pack') {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_category_name_input,  data.pack.new_category_name + date_time)
    } else if (type === 'combination') {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_category_name_input,  data.standard.new_category_name + 'Combination' + date_time)
    } else {
      return this.client.waitAndSetValue(selector.BO.AddProductPage.product_category_name_input,  data.standard.new_category_name + date_time)
    }
  }

  createCategory() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.category_create_btn,50)
      .waitForExistAndClick(selector.BO.AddProductPage.category_create_btn)
      .pause(3000)
  }

  removeHomeCategory() {
    return this.client
    .waitForVisibleAndClick(selector.BO.AddProductPage.category_home)
  }

  addBrand(type) {
      return this.client
        .scrollTo(selector.BO.AddProductPage.product_add_brand_btn,50)
        .waitForExistAndClick(selector.BO.AddProductPage.product_add_brand_btn)
  }

  selectBrand() {
    return this.client
      .waitForExistAndClick(selector.BO.AddProductPage.product_brand_select)
      .waitForExistAndClick(selector.BO.AddProductPage.product_brand_select_option)
  }

  productOnline() {
    return this.client.waitForExistAndClick(selector.BO.AddProductPage.product_online_toggle)
  }

  addRelatedProduct(type) {
    if (type === 'pack') {
      return this.client
        .scrollTo(selector.BO.AddProductPage.add_related_product_btn,50)
        .waitForExistAndClick(selector.BO.AddProductPage.add_related_product_btn)
    } else {
      return this.client.waitForExistAndClick(selector.BO.AddProductPage.add_related_product_btn)
    }
  }

  searchAndAddRelatedProduct() {
    var search_products = data.common.search_related_products.split('//');
    return this.client
      .waitAndSetValue(selector.BO.AddProductPage.search_add_related_product_input, search_products[0])
      .waitForExistAndClick(selector.BO.AddProductPage.related_product_item)
      .waitAndSetValue(selector.BO.AddProductPage.search_add_related_product_input, search_products[1])
      .waitForExistAndClick(selector.BO.AddProductPage.related_product_item)
  }

  addFeatureHeight(type) {
    if (type === 'pack') {
      return this.client
        .scrollTo(selector.BO.AddProductPage.product_add_feature_btn,50)
        .waitForExistAndClick(selector.BO.AddProductPage.product_add_feature_btn)
        .waitForExistAndClick(selector.BO.AddProductPage.feature_select_button)
        .waitForExistAndClick(selector.BO.AddProductPage.feature_select_option_height)
        .waitAndSetValue(selector.BO.AddProductPage.feature_custom_value_height, data.standard.features.feature1.custom_value)
    } else {
      return this.client
        .waitForExistAndClick(selector.BO.AddProductPage.product_add_feature_btn)
        .waitForExistAndClick(selector.BO.AddProductPage.feature_select_button)
        .waitForExistAndClick(selector.BO.AddProductPage.feature_select_option_height)
        .waitAndSetValue(selector.BO.AddProductPage.feature_custom_value_height, data.standard.features.feature1.custom_value)
    }
  }

  addProductPriceTaxExcluded() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.priceTE_shortcut,50)
      .waitAndSetValue(selector.BO.AddProductPage.priceTE_shortcut, data.common.priceTE)
  }

  addProductReference() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.product_reference,data.common.product_reference)
  }

}

module.exports = EditBasicSettings;
