var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');

class CreateCombinations extends PrestashopClient {

  goToProductCombinationsForm() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.product_combinations_tab, 60000)
      .click(selector.BO.AddProductPage.product_combinations_tab)
  }

  createFirstCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_size_s, 60000)
      .click(selector.BO.AddProductPage.combination_size_s)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.combination_color_gray, 60000)
      .click(selector.BO.AddProductPage.combination_color_gray)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.combination_generate_button, 60000)
      .click(selector.BO.AddProductPage.combination_generate_button)
      .pause(2000)
  }

  createSecondCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_size_m, 60000)
      .click(selector.BO.AddProductPage.combination_size_m)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.combination_color_beige, 60000)
      .click(selector.BO.AddProductPage.combination_color_beige)
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.combination_generate_button, 60000)
      .click(selector.BO.AddProductPage.combination_generate_button)
      .pause(2000)
  }

  goToEditFirstCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_edit_first_variation, 60000)
      .click(selector.BO.AddProductPage.combination_edit_first_variation)
      .pause(2000)
  }

  editFirstCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_first_details_qty, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_qty)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_qty, data.standard.variations.variation1.quantity)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_ref, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_ref)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_ref, data.standard.variations.variation1.ref)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_minimal_quantity, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_minimal_quantity)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_minimal_quantity, data.standard.variations.variation1.minimal_quantity)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_available_date, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_available_date)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_available_date, data.standard.variations.variation1.available_date)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_wholesale, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_wholesale)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_wholesale, data.standard.variations.variation1.wholesale)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_priceTI, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_priceTI)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_priceTI, data.standard.variations.variation1.priceTI)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_unity, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_unity)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_unity, data.standard.variations.variation1.unity)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_weight, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_weight)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_weight, data.standard.variations.variation1.weight)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_ISBN_code, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_ISBN_code)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_ISBN_code, data.standard.variations.variation1.isbn)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_EAN13, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_EAN13)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_EAN13, data.standard.variations.variation1.ean13)
      .waitForExist(selector.BO.AddProductPage.combination_first_details_UPC, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_UPC)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_first_details_UPC, data.standard.variations.variation1.upc)
  }

  backToProduct() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_first_details_back_to_product_btn, 60000)
      .click(selector.BO.AddProductPage.combination_first_details_back_to_product_btn)
      .pause(3000)
  }

  goToEditSecondCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_edit_second_variation, 60000)
      .click(selector.BO.AddProductPage.combination_edit_second_variation)
      .pause(2000)
  }

  editSecondCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_second_details_qty, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_qty)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_qty, data.standard.variations.variation2.quantity)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_ref, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_ref)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_ref, data.standard.variations.variation2.ref)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_minimal_quantity, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_minimal_quantity)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_minimal_quantity, data.standard.variations.variation2.minimal_quantity)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_available_date, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_available_date)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_available_date, data.standard.variations.variation2.available_date)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_wholesale, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_wholesale)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_wholesale, data.standard.variations.variation2.wholesale)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_priceTI, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_priceTI)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_priceTI, data.standard.variations.variation2.priceTI)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_unity, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_unity)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_unity, data.standard.variations.variation2.unity)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_weight, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_weight)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_weight, data.standard.variations.variation2.weight)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_ISBN_code, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_ISBN_code)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_ISBN_code, data.standard.variations.variation2.isbn)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_EAN13, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_EAN13)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_EAN13, data.standard.variations.variation2.ean13)
      .waitForExist(selector.BO.AddProductPage.combination_second_details_UPC, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_UPC)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_second_details_UPC, data.standard.variations.variation2.upc)
  }

  backToProductButton() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_second_details_back_to_product_btn, 60000)
      .click(selector.BO.AddProductPage.combination_second_details_back_to_product_btn)
      .pause(3000)
  }

  availabilityPreferences() {
    return this.client
      .scroll(0, 600)
      .waitForExist(selector.BO.AddProductPage.combination_availability_preferences, 90000)
      .click(selector.BO.AddProductPage.combination_availability_preferences)
      .pause(2000)
  }

  availabilityLabelInStock() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_label_in_stock, 90000)
      .click(selector.BO.AddProductPage.combination_label_in_stock)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_label_in_stock, data.common.qty_msg_stock)
      .pause(2000)
  }

  availabilityLabelOutStock() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_label_out_stock, 90000)
      .click(selector.BO.AddProductPage.combination_label_out_stock)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.combination_label_out_stock, data.common.qty_msg_unstock)
      .pause(2000)
  }
}

module.exports = CreateCombinations;
