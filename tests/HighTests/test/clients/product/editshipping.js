var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');


class EditShipping extends PrestashopClient {

  // for standar / pack / combination

  goToProductShipping(){
    return this.client
      .scroll(500, 0)
      .waitForExist(selector.BO.AddProductPage.product_shipping_tab, 90000)
      .click(selector.BO.AddProductPage.product_shipping_tab)
  }

  shippingWidth(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.shipping_width, 90000)
      .click(selector.BO.AddProductPage.shipping_width)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.shipping_width, data.common.cwidth)
  }

  shippingHeight(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.shipping_height, 90000)
      .click(selector.BO.AddProductPage.shipping_height)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.shipping_height, data.common.cheight)
  }

  shippingDepth(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.shipping_depth, 90000)
      .click(selector.BO.AddProductPage.shipping_depth)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.shipping_depth, data.common.cdepth)
  }

  shippingWeight(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.shipping_weight, 90000)
      .click(selector.BO.AddProductPage.shipping_weight)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.shipping_weight, data.common.cweight)
  }

  shippingcosts(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.shipping_fees, 90000)
      .click(selector.BO.AddProductPage.shipping_fees)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.shipping_fees, data.common.cadd_ship_coast)
  }

  selectAvailableCarrier(){
    return this.client
      .waitForExist(selector.BO.AddProductPage.shipping_available_carriers, 90000)
      .click(selector.BO.AddProductPage.shipping_available_carriers)
      .pause(2000)
  }

}

module.exports = EditShipping;
