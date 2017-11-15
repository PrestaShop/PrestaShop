var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');

class EditPricing extends PrestashopClient {

  goToPricingTab() {
    return this.client
      .scroll(0, 0)
      .waitForExist(selector.BO.AddProductPage.product_pricing_tab, 90000)
      .click(selector.BO.AddProductPage.product_pricing_tab)
  }

  pricingUnity() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.unit_price, 60000)
      .clearElement(selector.BO.AddProductPage.unit_price)
      .setValue(selector.BO.AddProductPage.unit_price, data.common.unitPrice)
      .setValue(selector.BO.AddProductPage.unity, data.common.unity)
  }

  pricingWholesale() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.pricing_wholesale, 60000)
      .clearElement(selector.BO.AddProductPage.pricing_wholesale)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.pricing_wholesale, data.common.wholesale)
  }

  pricingPriorities() {
    return this.client
      .scroll(0, 250)
      .waitForExist(selector.BO.AddProductPage.pricing_first_priorities_select, 60000)
      .selectByValue(selector.BO.AddProductPage.pricing_first_priorities_select,'id_shop')
      .pause(1000)
      .waitForExist(selector.BO.AddProductPage.pricing_second_priorities_select, 60000)
      .selectByValue(selector.BO.AddProductPage.pricing_second_priorities_select,'id_currency')
      .pause(1000)
      .waitForExist(selector.BO.AddProductPage.pricing_third_priorities_select, 60000)
      .selectByValue(selector.BO.AddProductPage.pricing_third_priorities_select,'id_country')
      .pause(1000)
      .waitForExist(selector.BO.AddProductPage.pricing_fourth_priorities_select, 60000)
      .selectByValue(selector.BO.AddProductPage.pricing_third_priorities_select,'id_group')
      .pause(1000)
  }

}

module.exports = EditPricing;
