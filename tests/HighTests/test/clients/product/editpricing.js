var CommonClient = require('./../common_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');

class EditPricing extends CommonClient {

  goToPricingTab() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.product_pricing_tab, 50)
      .waitForExistAndClick(selector.BO.AddProductPage.product_pricing_tab)
  }

  pricingUnity() {
    return this.client
      .waitAndSetValue(selector.BO.AddProductPage.unit_price, data.common.unitPrice)
      .waitAndSetValue(selector.BO.AddProductPage.unity, data.common.unity)
  }

  pricingWholesale() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.pricing_wholesale, data.common.wholesale)
  }

  pricingPriorities() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.pricing_first_priorities_select, 50)
      .waitAndSelectByValue(selector.BO.AddProductPage.pricing_first_priorities_select, 'id_shop')
      .waitAndSelectByValue(selector.BO.AddProductPage.pricing_second_priorities_select, 'id_currency')
      .waitAndSelectByValue(selector.BO.AddProductPage.pricing_third_priorities_select, 'id_country')
      .waitAndSelectByValue(selector.BO.AddProductPage.pricing_fourth_priorities_select, 'id_group')
  }

}

module.exports = EditPricing;
