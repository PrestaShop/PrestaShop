var PrestashopClient = require('./../prestashop_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');

class EditSEO extends PrestashopClient {

  goToSEOTab() {
    return this.client
      .scroll(800, 0)
      .waitForExist(selector.BO.AddProductPage.product_SEO_tab, 90000)
      .click(selector.BO.AddProductPage.product_SEO_tab)
  }

  metaTitle() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.SEO_meta_title, 90000)
      .click(selector.BO.AddProductPage.SEO_meta_title)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.SEO_meta_title, data.common.metatitle)
  }

  metaDescription() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.SEO_meta_description, 90000)
      .click(selector.BO.AddProductPage.SEO_meta_description)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.SEO_meta_description, data.common.metadesc)
  }

  friendlyUrl() {
    return this.client
      .waitForExist(selector.BO.AddProductPage.SEO_friendly_url, 90000)
      .click(selector.BO.AddProductPage.SEO_friendly_url)
      .pause(2000)
      .setValue(selector.BO.AddProductPage.SEO_friendly_url, data.common.shortlink)
  }



}

module.exports = EditSEO;
