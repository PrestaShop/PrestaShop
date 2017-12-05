var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');

global.marqueName = 'PrestaShop' + new Date().getTime();

class Manufacturers extends CommonClient {

  goToManufacturersList() {
    return this.client
      .waitForExist(selector.CatalogPage.menu_button, 90000)
      .moveToObject(selector.CatalogPage.menu_button)
      .waitForExist(selector.Manufacturers.submenu, 90000)
      .click(selector.Manufacturers.submenu)
  }

  addNewBrand() {
    return this.client
      .waitForExist(selector.Brands.new_brand_button, 90000)
      .click(selector.Brands.new_brand_button)
  }

  addBrandName() {
    return this.client
      .waitForExist(selector.Brands.name_input, 90000)
      .setValue(selector.Brands.name_input, global.marqueName)
  }

  addShortDescription() {
    return this.client
      .waitForExist(selector.Brands.short_desc_textarea, 90000)
      .click(selector.Brands.short_desc_textarea)
      .waitForVisible(selector.Brands.short_desc_source_code_modal, 90000)
      .setValue(selector.Brands.short_desc_source_code_modal, "Short Description")
      .click(selector.Brands.short_desc_source_code_modal_confirmation)
  }

  addDescription() {
    return this.client
      .waitForExist(selector.Brands.desc_textarea, 90000)
      .click(selector.Brands.desc_textarea)
      .waitForVisible(selector.Brands.short_desc_source_code_modal, 90000)
      .setValue(selector.Brands.short_desc_source_code_modal, "Description")
      .click(selector.Brands.short_desc_source_code_modal_confirmation)
  }

  addBrandLogo() {
    return this.client
      .execute(function () {
        document.getElementById("logo").style = "";
      })
      .chooseFile(selector.Brands.image_input, global.brandsImage)
  }

  addMetaTitle() {
    return this.client
      .waitForVisible(selector.Brands.meta_title_input, 90000)
      .setValue(selector.Brands.meta_title_input, "meta title")
  }

  addMetaDescription() {
    return this.client
      .waitForVisible(selector.Brands.meta_description_input, 90000)
      .setValue(selector.Brands.meta_description_input, "meta description")
  }

  addMetaKeywords() {
    return this.client
      .waitForVisible(selector.Brands.meta_keywords_input, 90000)
      .setValue(selector.Brands.meta_keywords_input, "key words")
      .keys('\uE007')
  }

  activeNewBrand() {
    return this.client
      .waitForVisible(selector.Brands.active_button, 90000)
      .click(selector.Brands.active_button)

  }

  saveBrand() {
    return this.client
      .waitForVisible(selector.Brands.save_button, 90000)
      .click(selector.Brands.save_button)
  }

  addNewBrandAddress() {
    return this.client
      .waitForExist(selector.BrandAddress.new_brand_address_button, 90000)
      .click(selector.BrandAddress.new_brand_address_button)
  }

  chooseBrand() {
    return this.client
      .waitForExist(selector.BrandAddress.branch_select, 90000)
      .selectByVisibleText(selector.BrandAddress.branch_select, global.marqueName)
  }

  addLastName() {
    return this.client
      .waitForExist(selector.BrandAddress.last_name_input, 90000)
      .setValue(selector.BrandAddress.last_name_input, "Prestashop")
  }

  addFirstName() {
    return this.client
      .waitForExist(selector.BrandAddress.first_name_input, 90000)
      .setValue(selector.BrandAddress.first_name_input, "Prestashop")
  }

  addBrandAddress() {
    return this.client
      .waitForExist(selector.BrandAddress.address_input, 90000)
      .setValue(selector.BrandAddress.address_input, "12 rue d'amesterdam")
  }

  addBrandSecondAddress() {
    return this.client
      .waitForExist(selector.BrandAddress.secondary_address, 90000)
      .setValue(selector.BrandAddress.secondary_address, "RDC")
  }

  addBrandZipCode() {
    return this.client
      .waitForExist(selector.BrandAddress.postal_code_input, 90000)
      .setValue(selector.BrandAddress.postal_code_input, "75009")
  }

  addBrandCity() {
    return this.client
      .waitForExist(selector.BrandAddress.city_input, 90000)
      .setValue(selector.BrandAddress.city_input, "paris")
  }

  addBrandCountry() {
    return this.client
      .waitForExist(selector.BrandAddress.country, 90000)
      .selectByValue(selector.BrandAddress.country, "8")
  }

  addBrandPhone() {
    return this.client
      .waitForExist(selector.BrandAddress.phone_input, 90000)
      .setValue(selector.BrandAddress.phone_input, "0140183004")
  }

  addBrandOtherInformation() {
    return this.client
      .waitForExist(selector.BrandAddress.other_input, 90000)
      .setValue(selector.BrandAddress.other_input, "azerty")
  }

  saveBrandAddress() {
    return this.client
      .waitForExist(selector.BrandAddress.save_button, 90000)
      .click(selector.BrandAddress.save_button)
  }

}

module.exports = Manufacturers;
