var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');
var path = require('path');

class EditBasicSettings extends PrestashopClient {

  setProductName(type) {
    if (type === 'VirtualProduct') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_name_input, data.virtual.name + product_id);
    } else if (type === 'pack') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_name_input, data.pack.name + product_id);
    } else if (type === 'combination') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_name_input, data.standard.name + 'Combination' + product_id);
    } else {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_name_input, data.standard.name + product_id);
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
        .pause(3000)
    }
  }

  addPackProduct1() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.search_product_pack, 60000)
      .click(selector.BO.AddProductPage.search_product_pack)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.search_product_pack, data.pack.pack.pack1.search)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.product_item_pack, 60000)
      .click(selector.BO.AddProductPage.product_item_pack)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.product_pack_item_quantity, 60000)
      .click(selector.BO.AddProductPage.product_pack_item_quantity)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.product_pack_item_quantity, data.pack.pack.pack1.quantity)
      .waitForExist(selector.BO.AddProductPage.product_pack_add_button, 60000)
      .click(selector.BO.AddProductPage.product_pack_add_button)
      .pause(2000)
  }

  addPackProduct2() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.search_product_pack, 60000)
      .click(selector.BO.AddProductPage.search_product_pack)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.search_product_pack, data.pack.pack.pack2.search)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.product_item_pack, 60000)
      .click(selector.BO.AddProductPage.product_item_pack)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.product_pack_item_quantity, 60000)
      .click(selector.BO.AddProductPage.product_pack_item_quantity)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.product_pack_item_quantity, data.pack.pack.pack2.quantity)
      .waitForExist(selector.BO.AddProductPage.product_pack_add_button, 60000)
      .click(selector.BO.AddProductPage.product_pack_add_button)
      .pause(2000)
  }


  setQuantity() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.quantity_shortcut_input, 90000)
      .clearElement(selector.BO.AddProductPage.quantity_shortcut_input)
      .addValue(selector.BO.AddProductPage.quantity_shortcut_input, "10")
  }

  setPrice() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.price_te_shortcut_input, 90000)
      .execute(function () {
        document.querySelector('#form_step1_price_shortcut').value = "";
      })
      .setValue(selector.BO.AddProductPage.price_te_shortcut_input, "5")
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
      .scroll(0, 600)
      .waitForExist(selector.BO.AddProductPage.product_create_category_btn, 90000)
      .click(selector.BO.AddProductPage.product_create_category_btn)
  }

  setCategoryName(type) {
    if (type === 'VirtualProduct') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_category_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_category_name_input, data.virtual.name + product_id);
    } else if (type === 'pack') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_category_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_category_name_input, data.pack.name + product_id);
    } else if (type === 'ProductCombination') {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_category_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_category_name_input, data.standard.name + 'Combination' + product_id);
    } else {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_category_name_input, 90000)
        .setValue(selector.BO.AddProductPage.product_category_name_input, data.standard.name + product_id);
    }
  }

  createCategory() {
    return this.client
      .scroll(0, 1000)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.category_create_btn, 90000)
      .click(selector.BO.AddProductPage.category_create_btn)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.category_home, 90000)
      .click(selector.BO.AddProductPage.category_home)
  }

  removeHomeCategory() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.category_home, 90000)
      .click(selector.BO.AddProductPage.category_home)
      .pause(2000)
  }

  addBrand(type) {
    if (type === 'pack') {
      return this.client
        .scroll(0, 1000)
        .waitForExist(selector.BO.AddProductPage.product_add_brand_btn, 90000)
        .click(selector.BO.AddProductPage.product_add_brand_btn)
        .pause(2000)
    } else {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_add_brand_btn, 90000)
        .click(selector.BO.AddProductPage.product_add_brand_btn)
        .pause(2000)
    }
  }

  selectBrand() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.product_brand_select, 90000)
      .click(selector.BO.AddProductPage.product_brand_select)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.product_brand_select_option, 90000)
      .click(selector.BO.AddProductPage.product_brand_select_option)
      .pause(2000)
  }

  productEnligne() {
    return this.client
      .pause(1000)
      .click(selector.BO.AddProductPage.product_online_toggle)
  }



  addRelatedProduct(type) {
    if (type === 'pack') {
      return this.client
        .scroll(0, 1000)
        .waitForExist(selector.BO.AddProductPage.add_related_product_btn, 90000)
        .click(selector.BO.AddProductPage.add_related_product_btn)
        .pause(2000)
    } else {
      return this.client
        .waitForExist(selector.BO.AddProductPage.add_related_product_btn, 90000)
        .click(selector.BO.AddProductPage.add_related_product_btn)
        .pause(2000)
    }

  }

  searchAndAddRelatedProduct() {
    var search_products = data.common.search_related_products.split('//');
    return this.client
      .waitForExist(selector.BO.AddProductPage.search_add_related_product_input, 90000)
      .setValue(selector.BO.AddProductPage.search_add_related_product_input, search_products[0])
      .waitForExist(selector.BO.AddProductPage.related_product_item, 90000)
      .click(selector.BO.AddProductPage.related_product_item)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.search_add_related_product_input, search_products[1])
      .waitForExist(selector.BO.AddProductPage.related_product_item, 90000)
      .click(selector.BO.AddProductPage.related_product_item)
      .pause(2000)
  }

  addFeatureHeight(type) {
    if (type === 'pack') {
      return this.client
        .scroll(0, 1000)
        .waitForExist(selector.BO.AddProductPage.product_add_feature_btn, 90000)
        .click(selector.BO.AddProductPage.product_add_feature_btn)
        .pause(2000)
        .waitForExist(selector.BO.AddProductPage.feature_select_button, 90000)
        .click(selector.BO.AddProductPage.feature_select_button)
        .waitForExist(selector.BO.AddProductPage.feature_select_option_height, 90000)
        .click(selector.BO.AddProductPage.feature_select_option_height)
        .waitForExist(selector.BO.AddProductPage.feature_custom_value_height, 90000)
        .setValue(selector.BO.AddProductPage.feature_custom_value_height, data.standard.features.feature1.custom_value)
        .pause(2000)
    } else {
      return this.client
        .waitForExist(selector.BO.AddProductPage.product_add_feature_btn, 90000)
        .click(selector.BO.AddProductPage.product_add_feature_btn)
        .pause(2000)
        .waitForExist(selector.BO.AddProductPage.feature_select_button, 90000)
        .click(selector.BO.AddProductPage.feature_select_button)
        .waitForExist(selector.BO.AddProductPage.feature_select_option_height, 90000)
        .click(selector.BO.AddProductPage.feature_select_option_height)
        .waitForExist(selector.BO.AddProductPage.feature_custom_value_height, 90000)
        .setValue(selector.BO.AddProductPage.feature_custom_value_height, data.standard.features.feature1.custom_value)
        .pause(2000)
    }
  }

  addProductPriceTaxExcluded() {
    return this.client
      .scroll(800, 0)
      .waitForExist(selector.BO.AddProductPage.priceTE_shortcut, 60000)
      .clearElement(selector.BO.AddProductPage.priceTE_shortcut)
      .setValue(selector.BO.AddProductPage.priceTE_shortcut, data.common.priceTE)
  }

  addProductReference() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.product_reference, 60000)
      .click(selector.BO.AddProductPage.product_reference)
      .setValue(selector.BO.AddProductPage.product_reference, data.common.product_reference)
  }

  pauseCustum(){
    return this.client
      .pause(5000)
  }
}

module.exports = EditBasicSettings;
