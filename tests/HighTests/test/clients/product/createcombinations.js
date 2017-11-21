var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');


class CreateCombinations extends PrestashopClient {

  goToProductCombinationsForm() {
    return this.client
      .scroll(0,0)
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
  }

  getCombinationData(number) {
    return this.client
      .pause(2000)
      .waitForExist(selector.BO.AddProductPage.combination_panel.replace('%NUMBER',number), 60000)
      .then(() => this.client.getAttribute(selector.BO.AddProductPage.combination_panel.replace('%NUMBER',number),'data'))
      .then((text) => global.combinationId = text);
  }

  goToEditCombination(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_edit.replace('%NUMBER',global.combinationId), 60000)
      .click(selector.BO.AddProductPage.combination_edit.replace('%NUMBER',global.combinationId))
      .pause(2000)
  }

  editCombination(number) {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_quantity.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_quantity.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].quantity)
      .waitForExist(selector.BO.AddProductPage.combination_available_date.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_available_date.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].available_date)
      .waitForExist(selector.BO.AddProductPage.combination_min_quantity.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_min_quantity.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].minimal_quantity)
      .waitForExist(selector.BO.AddProductPage.combination_reference.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_reference.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].ref)
      .waitForExist(selector.BO.AddProductPage.combination_whole_sale.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_whole_sale.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].wholesale)
      .waitForExist(selector.BO.AddProductPage.combination_low_stock.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_low_stock.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].minimal_quantity)
      .waitForExist(selector.BO.AddProductPage.combination_priceTI.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_priceTI.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].priceTI)
      .waitForExist(selector.BO.AddProductPage.combination_attribute_unity.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_attribute_unity.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].unity)
      .waitForExist(selector.BO.AddProductPage.combination_attribute_weight.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_attribute_weight.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].weight)
      .waitForExist(selector.BO.AddProductPage.combination_attribute_isbn.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_attribute_isbn.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].isbn)
      .waitForExist(selector.BO.AddProductPage.combination_attribute_ean13.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_attribute_ean13.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].ean13)
      .waitForExist(selector.BO.AddProductPage.combination_attribut_upc.replace('%NUMBER',global.combinationId), 60000)
      .setValue(selector.BO.AddProductPage.combination_attribut_upc.replace('%NUMBER',global.combinationId),data.standard.variations[number-1].upc)
  }

  backToProduct() {
    return this.client
      .scroll(0,0)
      .waitForExist(selector.BO.AddProductPage.back_to_product.replace('%NUMBER',global.combinationId), 60000)
      .click(selector.BO.AddProductPage.back_to_product.replace('%NUMBER',global.combinationId))
      .pause(2000)
  }

  availabilityPreferences() {
    return this.client
      .scroll(0, 600)
      .waitForExist(selector.BO.AddProductPage.combination_availability_preferences, 90000)
      .click(selector.BO.AddProductPage.combination_availability_preferences)
  }

  availabilityLabelInStock() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_label_in_stock, 90000)
      .click(selector.BO.AddProductPage.combination_label_in_stock)
      .setValue(selector.BO.AddProductPage.combination_label_in_stock, data.common.qty_msg_stock)
  }

  availabilityLabelOutStock() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_label_out_stock, 90000)
      .click(selector.BO.AddProductPage.combination_label_out_stock)
      .setValue(selector.BO.AddProductPage.combination_label_out_stock, data.common.qty_msg_unstock)
  }
}

module.exports = CreateCombinations;
