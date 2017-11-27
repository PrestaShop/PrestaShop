var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');

global.marqueName = 'PrestaShop' + new Date().getTime();

class Manufacturers extends CommonClient {

  goToManufacturersList() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .waitForExist(selector.BO.CatalogPage.Manufacturers.submenu, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.submenu)
  }

  addNewBrand() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.Brands.new_brand_button, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.Brands.new_brand_button)
  }

  addBrandName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.Brands.name_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.Brands.name_input, global.marqueName)
  }

  addShortDescription() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_textarea, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_textarea)
      .waitForVisible(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_source_code_modal, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_source_code_modal, "Short Description")
      .click(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_source_code_modal_confirmation)
  }

  addDescription() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.Brands.desc_textarea, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.Brands.desc_textarea)
      .waitForVisible(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_source_code_modal, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_source_code_modal, "Description")
      .click(selector.BO.CatalogPage.Manufacturers.Brands.short_desc_source_code_modal_confirmation)
  }

  addBrandLogo() {
    return this.client
      .execute(function () {
        document.getElementById("logo").style = "";
      })
      .chooseFile(selector.BO.CatalogPage.Manufacturers.Brands.image_input, global.brandsImage)
  }

  addMetaTitle() {
    return this.client
      .waitForVisible(selector.BO.CatalogPage.Manufacturers.Brands.meta_title_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.Brands.meta_title_input, "meta title")
  }

  addMetaDescription() {
    return this.client
      .waitForVisible(selector.BO.CatalogPage.Manufacturers.Brands.meta_description_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.Brands.meta_description_input, "meta description")
  }

  addMetaKeywords() {
    return this.client
      .waitForVisible(selector.BO.CatalogPage.Manufacturers.Brands.meta_keywords_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.Brands.meta_keywords_input, "key words")
      .keys('\uE007')
  }

  activeNewBrand() {
    return this.client
      .waitForVisible(selector.BO.CatalogPage.Manufacturers.Brands.active_button, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.Brands.active_button)

  }

  saveBrand() {
    return this.client
      .waitForVisible(selector.BO.CatalogPage.Manufacturers.Brands.save_button, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.Brands.save_button)
  }

  addNewBrandAddress() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.new_brand_address_button, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.BrandsAddress.new_brand_address_button)
  }

  chooseBrand() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.branch_select, 90000)
      .selectByVisibleText(selector.BO.CatalogPage.Manufacturers.BrandsAddress.branch_select, global.marqueName)
  }

  addLastName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.last_name_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.last_name_input, "Prestashop")
  }

  addFirstName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.first_name_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.first_name_input, "Prestashop")
  }

  addBrandAddress() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.address_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.address_input, "12 rue d'amesterdam")
  }

  addBrandSecondAddress() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.secondary_address, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.secondary_address, "RDC")
  }

  addBrandZipCode() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.postal_code_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.postal_code_input, "75009")
  }

  addBrandCity() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.city_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.city_input, "paris")
  }

  addBrandCountry() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.country, 90000)
      .selectByValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.country, "8")
  }

  addBrandPhone() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.phone_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.phone_input, "0140183004")
  }

  addBrandOtherInformation() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.other_input, 90000)
      .setValue(selector.BO.CatalogPage.Manufacturers.BrandsAddress.other_input, "azerty")
  }

  saveBrandAddress() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.Manufacturers.BrandsAddress.save_button, 90000)
      .click(selector.BO.CatalogPage.Manufacturers.BrandsAddress.save_button)
  }

}

module.exports = Manufacturers;
