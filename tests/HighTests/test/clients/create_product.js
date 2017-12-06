var CommonClient = require('./common_client');

class CreateProduct extends CommonClient {

  setQuantity(selector) {
    return this.client
      .waitForExist(selector, 90000)
      .clearElement(selector)
      .addValue(selector, "10")
  }

  setPrice(selector) {
    return this.client
      .waitForExist(selector, 90000)
      .execute(function (selector) {
        document.querySelector(selector).value = "";
      }, selector)
      .setValue(selector, "5")
  }

  selectVariation(addProductPage, name) {
    return this.client
      .waitAndSetValue(addProductPage.variations_input, name + " : All")
      .waitForExistAndClick(addProductPage.variations_select)
  }

  setVariationsQuantity(addProductPage, value) {
    return this.client
      .pause(4000)
      .waitAndSetValue(addProductPage.var_selected_quantitie, value)
      .moveToObject(addProductPage.combinations_thead, 90000)
      .waitForExistAndClick(addProductPage.save_quantitie_button)
  }

  clickOnAddFeature(addProductPage) {
    return this.client
      .moveToObject(addProductPage.product_create_category_btn)
      .waitForExistAndClick(addProductPage.add_feature_to_product_button)
  }

  selectFeature(addProductPage, name, value) {
    return this.client
      .moveToObject(addProductPage.feature_select)
      .waitForExistAndClick(addProductPage.feature_select)
      .waitAndSetValue(addProductPage.select_feature_created, name)
      .waitForExistAndClick(addProductPage.result_feature_select.replace('%ID', 0))
      .pause(2000)
      .selectByVisibleText(addProductPage.feature_value_select, value)
  }

}

module.exports = CreateProduct;