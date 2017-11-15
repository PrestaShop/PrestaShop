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
      .pause(1000)
      .waitForExist(selector.BO.AddProductPage.combination_color_gray, 60000)
      .click(selector.BO.AddProductPage.combination_color_gray)
      .pause(1000)
      .waitForExist(selector.BO.AddProductPage.combination_generate_button, 60000)
      .click(selector.BO.AddProductPage.combination_generate_button)
      .pause(1000)
  }

  createSecondCombination() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.combination_size_m, 60000)
      .click(selector.BO.AddProductPage.combination_size_m)
      .pause(1000)
      .waitForExist(selector.BO.AddProductPage.combination_color_beige, 60000)
      .click(selector.BO.AddProductPage.combination_color_beige)
      .pause(1000)
      .waitForExist(selector.BO.AddProductPage.combination_generate_button, 60000)
      .click(selector.BO.AddProductPage.combination_generate_button)
  }

  getCombinationData(number) {
    return this.client
      .pause(2000)
      .waitForExist("//*[@id='accordion_combinations']/tr["+number+"]", 60000)
      .then(() => this.client.getAttribute("//*[@id='accordion_combinations']/tr["+number+"]",'data'))
      .then((text) => global.combinationId = text);
  }

  goToEditCombination(){
    return this.client
      .waitForExist("//*[@id='attribute_"+ global.combinationId +"']/td[7]/div[1]/a", 60000)
      .click("//*[@id='attribute_"+ global.combinationId +"']/td[7]/div[1]/a")
      .pause(2000)
  }

  editCombination(number) {
    return this.client
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_quantity"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_quantity"]',data.standard.variations[number-1].quantity)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_available_date_attribute"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_available_date_attribute"]',data.standard.variations[number-1].available_date)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_minimal_quantity"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_minimal_quantity"]',data.standard.variations[number-1].minimal_quantity)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_reference"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_reference"]',data.standard.variations[number-1].ref)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_wholesale_price"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_wholesale_price"]',data.standard.variations[number-1].wholesale)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_low_stock_threshold"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_low_stock_threshold"]',data.standard.variations[number-1].minimal_quantity)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_priceTI"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_priceTI"]',data.standard.variations[number-1].priceTI)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_unity"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_unity"]',data.standard.variations[number-1].unity)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_weight"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_weight"]',data.standard.variations[number-1].weight)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_isbn"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_isbn"]',data.standard.variations[number-1].isbn)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_ean13"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_ean13"]',data.standard.variations[number-1].ean13)
      .pause(2000)
      .waitForExist('//*[@id="combination_'+global.combinationId+'_attribute_upc"]', 60000)
      .setValue('//*[@id="combination_'+global.combinationId+'_attribute_upc"]',data.standard.variations[number-1].upc)
      .pause(2000)
  }

  backToProduct() {
    return this.client
      .scroll(0,0)
      .waitForExist('//*[@id="combination_form_'+global.combinationId+'"]/div[2]/div[1]/button', 60000)
      .click('//*[@id="combination_form_'+global.combinationId+'"]/div[2]/div[1]/button')
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
