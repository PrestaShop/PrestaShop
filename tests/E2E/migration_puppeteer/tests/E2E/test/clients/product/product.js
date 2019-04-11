let CommonClient = require('./../common_client');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {ProductList} = require('../../selectors/BO/add_product_page');
let data = require('./../../datas/product-data');
let path = require('path');

global.productIdElement = [];
global.productStatus = [];


class Product extends CommonClient {

  async getElementID() {
    page.waitForSelector(ProductList.product_id.replace('%ID', 1),{visible:true});
    for(var i=0;i<3;i++){
      const element = await page.$(ProductList.product_id.replace('%ID', i+1));
      global.productIdElement[i] = await page.evaluate(el => el.textContent, element);
      console.log(global.productIdElement[i]);
    }
    expect(Number(global.productIdElement[1])).to.be.below(Number(global.productIdElement[0]));
    expect(Number(global.productIdElement[2])).to.be.below(Number(global.productIdElement[1]));
  }

  async checkCategoryRadioButton(categoryValue) {
    var el_selector = AddProductPage.category_radio_button.replace('%VALUE', categoryValue);
    return this.isVisible(el_selector);
  }

  openAllCategories() {
    return this.client
      .scrollTo(AddProductPage.catalog_home, 50)
      .waitForExistAndClick(AddProductPage.catalog_home)
      .waitForExistAndClick(AddProductPage.catalog_first_element_radio)
      .waitForExistAndClick(AddProductPage.catalog_second_element_radio);
  }

  associatedFile(id) {
    return this.client
      .waitForExistAndClick(AddProductPage.virtual_associated_file.replace('%ID', id), 1000)
      .pause(2000);
  }

  availability() {
    return this.client
      .scrollTo(AddProductPage.pack_label_out_stock, 50)
      .waitAndSetValue(AddProductPage.pack_label_out_stock, data.common.qty_msg_unstock);
  }

  async selectPricingPriorities(firstPriority = 'id_shop', secondPriority = 'id_currency', thirdPriority = 'id_country', fourthPriority = 'id_group') {
    await this.waitAndSelectByValue(AddProductPage.pricing_first_priorities_select, firstPriority);
    await this.waitAndSelectByValue(AddProductPage.pricing_second_priorities_select, secondPriority);
    await this.waitAndSelectByValue(AddProductPage.pricing_third_priorities_select, thirdPriority);
    await this.waitAndSelectByValue(AddProductPage.pricing_fourth_priorities_select, fourthPriority);
  }

  selectCondition() {
    return this.client
      .scrollTo(AddProductPage.options_condition_select, 50)
      .waitAndSelectByValue(AddProductPage.options_condition_select, 'refurbished');
  }

  UPCEntry() {
    return this.client
      .scrollTo(AddProductPage.options_upc, 50)
      .waitAndSetValue(AddProductPage.options_upc, data.common.upc);
  }

  async addPackProduct(search, quantity) {
    await page.click(AddProductPage.search_product_pack, { waitUntil: 'domcontentloaded' })
    await this.fillInputText(AddProductPage.search_product_pack, search);
    await page.waitForSelector("div.tt-menu.tt-open",{visible : true});
    await page.$eval(AddProductPage.product_item_pack, elem => elem.click());
    await this.fillInputNumber(AddProductPage.product_pack_item_quantity, quantity);
    await page.$eval(AddProductPage.product_pack_add_button, elem => elem.click());
  }

  createCategory() {
    return this.client
      .scrollTo(AddProductPage.category_create_btn, 50)
      .waitForExistAndClick(AddProductPage.category_create_btn)
      .pause(4000);

  }

  async searchAndAddRelatedProduct() {
    //get all search_products
    let search_products = data.common.search_related_products.split('//');

    //Add all related products
    for(var i = 0; i<search_products.length;i++){
      await page.click(AddProductPage.search_add_related_product_input, { waitUntil: 'domcontentloaded' });
      await page.keyboard.type(search_products[i]);
      await page.waitForSelector("div.tt-menu.tt-open", {visible: true});
      await page.click(AddProductPage.related_product_item);
    }
  }

  async addFeature(type, id = '0') {
    await page.click(AddProductPage.product_add_feature_btn);
    await page.waitForSelector(AddProductPage.feature_select_button.replace("%ID", id));
    await page.click(AddProductPage.feature_select_button.replace("%ID", id));
    await page.click(AddProductPage.feature_select_option);
    await this.fillInputText(AddProductPage.feature_custom_value_height, data.standard.features.feature1.custom_value);
  }

  async setPrice(selector, price) {
    await this.waitAndSetValue(selector, price);
  }

  async setVariationsQuantity(addProductPage, value) {
    await page.waitFor(4000);
    await this.waitAndSetValue(addProductPage.var_selected_quantitie, value);
    await this.scrollTo(addProductPage.combinations_thead);
    await this.waitForExistAndClick(addProductPage.save_quantitie_button);
  }

  async selectFeature(addProductPage, name, value, number) {
    await this.scrollWaitForExistAndClick(addProductPage.feature_select.replace('%NUMBER', number*2 + 1));
    await this.waitAndSetValue(addProductPage.select_feature_created, name);
    await this.waitForVisibleAndClick(addProductPage.result_feature_select.replace('%ID', number));
    await page.waitForSelector(addProductPage.feature_value_select.replace('%ID', number).replace('%V', 'not(disabled)') + ' option:nth-child(2)');
    await this.selectByVisibleText(addProductPage.feature_value_select.replace('%ID', number).replace('%V', 'not(disabled)'), value);
  }

  selectFeatureCustomizedValue(addProductPage, name, customizedValue, number) {
    this.scrollWaitForExistAndClick(addProductPage.feature_select.replace('%NUMBER', number + 1));
    this.waitAndSetValue(addProductPage.select_feature_created, name);
    this.waitForVisibleAndClick(addProductPage.result_feature_select.replace('%ID', number));
    this.waitAndSetValue(addProductPage.customized_value_input.replace('%ID', number), customizedValue);
  }

  async clickNextOrPrevious(selector) {
      await page.click(selector);
  }

  /**
   * This function allows to get the number of all products in Back Office
   * @param selector
   * @returns {*}
   */
  async getProductsNumber(selector) {
    if (global.isVisible) {
      await page.$eval(selector, el => el.innerText).then((text) => {
        global.productsNumber = text.match(/[0-9]+/g)[2];
      });
    } else {
      this.getProductPageNumber('#product_catalog_list');
    }
  }

  async getProductPageNumber(selector, pause = 0) {
    await page.waitForSelector(selector,{visible:'true'});
    const count = await page.evaluate((selector) => {
        const element = document.querySelectorAll(selector+ " tbody tr[data-uniturl]");
        return element.length;
      }, selector);
    if (count >= 0) {
      global.productsNumber = count;
    }
  }

  async clickPageNext(selector, pause = 0) {
    await this.pause(pause);
    await this.scrollWaitForExistAndClick(selector);
  }

  async getProductName(selector) {
    let product_name = await page.evaluate((selector) => {return document.querySelector(selector).textContent;}, selector);
    global.productInfo.push({'name': product_name, 'status': 'false'});
  }

  UrlModification(globalVar, productName) {
    return this.client
      .pause(1000)
      .then(() => global.tab[globalVar] = global.URL + "/" + (global.tab[globalVar].split("/"))[(global.tab[globalVar].split("/")).length - 1].replace(".jpg", "/" + productName + ".jpg"));
  }

  getProductStatus(selector, i) {
    return this
      .getText(selector).then(function (status) {
        global.productStatus[i] = status;
      });
  }

  async checkFeatureValue(predefinedValueSelector, customValueSelector, featureData) {
    if (global.isVisible) {
      if(featureData.predefined_value !== ''){
          const selectedIndex = await page.evaluate(async (selector) => await document.querySelector(selector).selectedIndex, predefinedValueSelector);
          this.checkTextContent(predefinedValueSelector + ' option:nth-child(' + String(selectedIndex+1) + ')',featureData.predefined_value);
      } else if(featureData.customized_value !== ''){
          this.checkInputValue(customValueSelector,featureData.customized_value);
      } else {
          expect(featureData.predefined_value === '' && featureData.customized_value === '', "You must choose a pre-defined value or set the customized value").to.be.equal(true);
      }
    }
  }

  checkProductCategory(i) {
    return this.client
      .scrollWaitForExistAndClick(AddProductPage.catalog_product_name.replace("%ID", global.positionTable[i - 1], 50000))
      .waitForVisible(AddProductPage.product_name_input)
  }

  async getSubCategoryNumber(selector, i) {
      const count = await page.evaluate((selector,i) => {
          const element = document.querySelectorAll(selector+ " > li:nth-child("+ String(i) +") li");
      return element.length;
        }, selector,i);
      if (count !== 1) {
          global.subCatNumber = count;
      }
  }

  checkValuesFeature(selector, value) {
    return this.client
      .execute(function (selector) {
        return document.querySelector(selector).innerText;
      }, selector)
      .then((values) => {
        expect(values.value).to.contains(value)
      });
  }

  checkSearchProduct(searchBy, min, max) {
      switch (searchBy) {
          case 'name':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k]).to.contains("mug");
              }
              break;
          case 'reference':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k]).to.contains("demo_1");
              }
              break;
          case 'category':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k]).to.be.contains("art");
              }
              break;
          case 'price':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k] >= min && elementsTable[k] <= max).to.be.true;
              }
              break;
          case 'min_quantity':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k] >= min).to.be.true;
              }
              break;
          case 'quantity':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k] >= min && elementsTable[k] <= max).to.be.true;
              }
              break;
          case 'id':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k] >= min && elementsTable[k] <= max).to.be.true;
              }
              break;
          case 'active_status':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k]).to.be.equal("check");
              }
              break;
          case 'inactive_status':
              for (let k = 0; k < (elementsTable.length); k++) {
                  expect(elementsTable[k]).to.be.equal("clear");
              }
              break;
      }
  }

  checkCategoryProduct() {
      expect(global.productCategories.HOME.Accessories).to.contains(tab['categoryName']);
  }

  async getCategoriesPageNumber(selector) {
    const count = await page.evaluate((selector) => {
        const element = document.querySelectorAll(selector+ " tbody tr");
    return element.length;
    }, selector);
    if (count !== 1) {
        global.categoriesPageNumber = count;
    }
  }

  checkTitleValue(selector, value, pause = 0) {
    return this.client
      .pause(pause)
      .execute(function (selector) {
        return document.querySelector(selector).textContent;
      }, selector)
      .then((values) => {
        expect(values.value).to.contain(value)
      });
  }


}

module.exports = Product;
