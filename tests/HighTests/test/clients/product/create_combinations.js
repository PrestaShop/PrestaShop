var CommonClient = require('./../common_client');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
var data = require('./../../datas/product-data');
var path = require('path');

class CreateCombinations extends CommonClient {

  createCombination(size, color) {
    return this.client
      .waitForExistAndClick(size)
      .pause(2000)
      .waitForExistAndClick(color)
      .pause(2000)
      .waitForExistAndClick(AddProductPage.combination_generate_button)
  }

  getCombinationData(number) {
    return this.client
      .pause(2000)
      .waitForExist(AddProductPage.combination_panel.replace('%NUMBER', number), 60000)
      .then(() => this.client.getAttribute(AddProductPage.combination_panel.replace('%NUMBER', number), 'data'))
      .then((text) => global.combinationId = text);
  }

  goToEditCombination() {
    return this.client
      .waitForExistAndClick(AddProductPage.combination_edit.replace('%NUMBER', global.combinationId))
      .pause(2000)
  }

  editCombination(number) {
    return this.client
      .waitAndSetValue(AddProductPage.combination_quantity.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].quantity)
      .waitAndSetValue(AddProductPage.combination_available_date.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].available_date)
      .waitAndSetValue(AddProductPage.combination_min_quantity.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].minimal_quantity)
      .waitAndSetValue(AddProductPage.combination_reference.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].ref)
      .waitAndSetValue(AddProductPage.combination_whole_sale.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].wholesale)
      .waitAndSetValue(AddProductPage.combination_low_stock.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].minimal_quantity)
      .waitAndSetValue(AddProductPage.combination_priceTI.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].priceTI)
      .waitAndSetValue(AddProductPage.combination_attribute_unity.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].unity)
      .waitAndSetValue(AddProductPage.combination_attribute_weight.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].weight)
      .waitAndSetValue(AddProductPage.combination_attribute_isbn.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].isbn)
      .waitAndSetValue(AddProductPage.combination_attribute_ean13.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].ean13)
      .waitAndSetValue(AddProductPage.combination_attribut_upc.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].upc)
  }

  backToProduct() {
    return this.client
      .scrollTo(AddProductPage.back_to_product.replace('%NUMBER', global.combinationId), 50)
      .waitForExistAndClick(AddProductPage.back_to_product.replace('%NUMBER', global.combinationId))
      .pause(2000)
  }

}

module.exports = CreateCombinations;
