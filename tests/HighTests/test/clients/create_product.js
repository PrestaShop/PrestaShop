var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

class CreateProduct extends PrestashopClient {

  goToProductMenu() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.menu, 90000)
      .click(selector.BO.AddProductPage.products_subtab)
      .waitForExist(selector.BO.AddProductPage.new_product_button, 90000)
  }

  addNewProduct() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.new_product_button, 90000)
      .click(selector.BO.AddProductPage.new_product_button)
  }

  addProductName(name) {
    return this.client
      .waitForExist(selector.BO.AddProductPage.product_name_input, 90000)
      .setValue(selector.BO.AddProductPage.product_name_input, name +' '+ global.date_time)
  }

  addProductQuantity() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.quantity_shortcut_input, 90000)
      .clearElement(selector.BO.AddProductPage.quantity_shortcut_input)
      .addValue(selector.BO.AddProductPage.quantity_shortcut_input, "10")
  }

  addProductPrice() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.price_te_shortcut_input, 90000)
      .execute(function () {
        document.querySelector('#form_step1_price_shortcut').value = "";
      })
      .setValue(selector.BO.AddProductPage.price_te_shortcut_input, "5")
  }

  addProductTypeAttribute() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.variations_type_button, 90000)
      .click(selector.BO.AddProductPage.variations_type_button)
      .waitForExist(selector.BO.AddProductPage.variations_tab, 90000)
      .click(selector.BO.AddProductPage.variations_tab)
      .waitForExist(selector.BO.AddProductPage.variations_input, 90000)
      .setValue(selector.BO.AddProductPage.variations_input, global.attributeName + " : All")
      .waitForExist(selector.BO.AddProductPage.variations_select, 90000)
      .click(selector.BO.AddProductPage.variations_select)
      .waitForExist(selector.BO.AddProductPage.variations_generate, 90000)
      .click(selector.BO.AddProductPage.variations_generate)
      .waitForExist(selector.BO.AddProductPage.close_validation_button, 90000)
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.var_selected, 90000)
      .click(selector.BO.AddProductPage.var_selected)
      .pause(3000)
      .waitForExist(selector.BO.AddProductPage.var_selected_quantitie, 90000)
      .setValue(selector.BO.AddProductPage.var_selected_quantitie, "10")
      .moveToObject('//*[@id="combinations_thead"]/tr/th[7]', 90000)
      .click(selector.BO.AddProductPage.save_quantitie_button)
  }

  addProductTypeFeature() {
    return this.client
      .moveToObject('//*[@id="add-categories"]/h2')
      .click(selector.BO.AddProductPage.add_feature_to_product_button)
      .waitForExist(selector.BO.AddProductPage.add_feature_to_product_button, 90000)
      .moveToObject(selector.BO.AddProductPage.feature_select)
      .click(selector.BO.AddProductPage.feature_select)
      .waitForExist(selector.BO.AddProductPage.select_feature_created, 90000)
      .setValue(selector.BO.AddProductPage.select_feature_created, global.featureName)
      .click('//*[@id="select2-form_step1_features_0_feature-results"]/li')
      .pause(2000)
      .selectByVisibleText(selector.BO.AddProductPage.feature_value_select,'feature value')
      .pause(1000)
  }


  productEnligne() {
    return this.client
      .pause(1000)
      .click(selector.BO.AddProductPage.product_online_toggle)
  }

  saveProduct() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.save_product_button, 90000)
      .click(selector.BO.AddProductPage.save_product_button)
      .pause(3000)
  }

  closeGreenValidation() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.close_validation_button, 90000)
      .click(selector.BO.AddProductPage.close_validation_button)
  }

}

module.exports = CreateProduct;
