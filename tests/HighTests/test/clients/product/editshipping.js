var CommonClient = require('./../common_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');

class EditShipping extends CommonClient {

  goToProductShipping() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.product_shipping_tab, 50)
      .waitForExistAndClick(selector.BO.AddProductPage.product_shipping_tab)
  }

  shippingWidth() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.shipping_width, data.common.cwidth)
  }

  shippingHeight() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.shipping_height, data.common.cheight)
  }

  shippingDepth() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.shipping_depth, data.common.cdepth)
  }

  shippingWeight() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.shipping_weight, data.common.cweight)
  }

  shippingCosts() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.shipping_fees, data.common.cadd_ship_coast)
  }

  selectAvailableCarrier() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.shipping_available_carriers, 50)
      .waitForExistAndClick(selector.BO.AddProductPage.shipping_available_carriers)
  }

}

module.exports = EditShipping;
