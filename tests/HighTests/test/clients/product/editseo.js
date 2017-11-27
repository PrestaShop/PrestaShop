var CommonClient = require('./../common_client');
var {selector} = require('../../globals.webdriverio.js');
var data = require('./../../datas/product-data');

class EditSEO extends CommonClient {

  goToSEOTab() {
    return this.client
      .scrollTo(selector.BO.AddProductPage.product_SEO_tab, 50)
      .waitForExistAndClick(selector.BO.AddProductPage.product_SEO_tab)
  }

  metaTitle() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.SEO_meta_title, data.common.metatitle)
  }

  metaDescription() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.SEO_meta_description, data.common.metadesc)
  }

  friendlyUrl() {
    return this.client.waitAndSetValue(selector.BO.AddProductPage.SEO_friendly_url, data.common.shortlink)
  }

}

module.exports = EditSEO;
