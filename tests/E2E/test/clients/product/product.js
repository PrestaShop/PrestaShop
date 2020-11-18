let CommonClient = require('./../common_client');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {ProductList} = require('../../selectors/BO/add_product_page');
let data = require('./../../datas/product-data');
let path = require('path');

global.productIdElement = [];
global.productStatus = [];


class Product extends CommonClient {

  getElementID() {
    return this.client
      .waitForExist(ProductList.product_id.replace('%ID', 1), 90000)
      .then(() => this.client.getText(ProductList.product_id.replace('%ID', 1)))
      .then((text) => global.productIdElement[0] = text)
      .then(() => this.client.getText(ProductList.product_id.replace('%ID', 2)))
      .then((text) => global.productIdElement[1] = text)
      .then(() => this.client.getText(ProductList.product_id.replace('%ID', 3)))
      .then((text) => global.productIdElement[2] = text)
      .then(() => expect(Number(global.productIdElement[1])).to.be.below(Number(global.productIdElement[0])))
      .then(() => expect(Number(global.productIdElement[2])).to.be.below(Number(global.productIdElement[1])));
  }

  checkCategoryRadioButton(categoryValue) {
    return this.client
      .waitForVisible(AddProductPage.category_radio_button.replace('%VALUE', categoryValue))
      .scroll(0, 1000)
      .isVisible(AddProductPage.category_radio_button.replace('%VALUE', categoryValue))
      .then((text) => expect(text).to.be.true);
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

  selectPricingPriorities(firstPriority = 'id_shop', secondPriority = 'id_currency', thirdPriority = 'id_country', fourthPriority = 'id_group') {
    return this.client
      .scrollTo(AddProductPage.pricing_first_priorities_select, 50)
      .waitAndSelectByValue(AddProductPage.pricing_first_priorities_select, firstPriority)
      .waitAndSelectByValue(AddProductPage.pricing_second_priorities_select, secondPriority)
      .waitAndSelectByValue(AddProductPage.pricing_third_priorities_select, thirdPriority)
      .waitAndSelectByValue(AddProductPage.pricing_fourth_priorities_select, fourthPriority);
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

  addPackProduct(search, quantity) {
    return this.client
      .waitAndSetValue(AddProductPage.search_product_pack, search)
      .waitForExistAndClick(AddProductPage.product_item_pack)
      .waitAndSetValue(AddProductPage.product_pack_item_quantity, quantity)
      .waitForExistAndClick(AddProductPage.product_pack_add_button);
  }

  createCategory() {
    return this.client
      .scrollTo(AddProductPage.category_create_btn, 50)
      .waitForExistAndClick(AddProductPage.category_create_btn)
      .pause(4000);

  }

  searchAndAddRelatedProduct() {
    let search_products = data.common.search_related_products.split('//');
    return this.client
      .scrollTo(AddProductPage.search_add_related_product_input)
      .waitAndSetValue(AddProductPage.search_add_related_product_input, search_products[0])
      .waitForVisibleAndClick(AddProductPage.related_product_item.replace('%I', 1))
      .waitAndSetValue(AddProductPage.search_add_related_product_input, search_products[1])
      .waitForVisibleAndClick(AddProductPage.related_product_item.replace('%I', 1));
  }

  addFeature(type, id = '0') {
    if (type === 'pack') {
      this.client
        .scrollTo(AddProductPage.product_add_feature_btn, 50);
    }
    return this.client
      .scrollTo(AddProductPage.product_add_feature_btn, 150)
      .waitForExistAndClick(AddProductPage.product_add_feature_btn)
      .waitForExistAndClick(AddProductPage.feature_select_button.replace("%ID", id))
      .waitForExistAndClick(AddProductPage.feature_select_option)
      .waitAndSetValue(AddProductPage.feature_custom_value_height, data.standard.features.feature1.custom_value);
  }

  setPrice(selector, price) {
    return this.client
      .scrollTo(selector, 50)
      .waitAndSetValue(selector, price);
  }

  setVariationsQuantity(addProductPage, value) {
    return this.client
      .pause(4000)
      .waitAndSetValue(addProductPage.var_selected_quantitie, value)
      .scrollTo(addProductPage.combinations_thead)
      .waitForExistAndClick(addProductPage.save_quantitie_button);
  }

  selectFeature(addProductPage, name, value, number) {
    return this.client
      .scrollWaitForExistAndClick(addProductPage.feature_select.replace('%NUMBER', number + 1))
      .selectByVisibleText(addProductPage.select_feature_created.replace('%ID', number), name)
      .waitForVisibleAndClick(addProductPage.result_feature_select.replace('%ID', number))
      .pause(4000)
      .selectByVisibleText(addProductPage.feature_value_select.replace('%ID', number).replace('%V', 'not(@disabled)'), value);
  }

  selectFeatureCustomizedValue(addProductPage, name, customizedValue, number) {
    return this.client
      .scrollWaitForExistAndClick(addProductPage.feature_select.replace('%NUMBER', number + 1))
      .selectByVisibleText(addProductPage.select_feature_created.replace('%ID', number), name)
      .waitForVisibleAndClick(addProductPage.result_feature_select.replace('%ID', number))
      .waitAndSetValue(addProductPage.customized_value_input.replace('%ID', number), customizedValue)
  }

  clickNextOrPrevious(selector) {
    if (global.isVisible) {
      return this.client
        .click(selector)
        .pause(2000);
    } else {
      return this.client.pause(0)
    }
  }

  /**
   * This function allows to get the number of all products in Back Office
   * @param selector
   * @returns {*}
   */
  getProductsNumber(selector) {
    if (global.isVisible) {
      return this.client
        .getText(selector)
        .then((count) => {
          global.productsNumber = count.match(/[0-9]+/g)[2];
        });
    } else {
      this.getProductPageNumber('product_catalog_list');
    }
  }

  getProductPageNumber(selector, pause = 0) {
    return this.client
      .pause(pause)
      .execute(function (selector) {
        return document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
      }, selector)
      .then((count) => {
        if (count.value !== 1) {
          global.productsNumber = count.value;
        }
        else {
          return this.client.isVisible(CatalogPage.search_result_message)
            .then((isVisible) => {
              if (isVisible) {
                global.productsNumber = 0;
              } else {
                global.productsNumber = count.value;
              }
            });
        }
      })
  }

  clickPageNext(selector, pause = 0) {
    return this.client
      .pause(pause)
      .scrollWaitForExistAndClick(selector);
  }

  getProductName(selector, i) {
    return this.client
      .getText(selector).then(function (name) {
        global.productInfo.push({'name': name, 'status': 'false'})
      });
  }

  UrlModification(globalVar, productName) {
    return this.client
      .pause(1000)
      .then(() => global.tab[globalVar] = global.URL + "/" + (global.tab[globalVar].split("/"))[(global.tab[globalVar].split("/")).length - 1].replace(".jpg", "/" + productName + ".jpg"));
  }

  getProductStatus(selector, i) {
    return this.client
      .getText(selector).then(function (status) {
        global.productStatus[i] = status;
      });
  }

  checkFeatureValue(predefinedValueSelector, customValueSelector, featureData) {
    if (global.isVisible) {
      if (featureData.predefined_value !== '') {
        return this.client
          .isSelected(predefinedValueSelector)
          .then((value) => expect(value).to.be.equal(true));
      } else if (featureData.customized_value !== '') {
        return this.client
          .getAttribute(customValueSelector, 'value')
          .then((value) => expect(value).to.be.equal(featureData.customized_value));
      } else {
        return this.client
          .pause(0)
          .then(() => expect(featureData.predefined_value !== '' && featureData.customized_value !== '', "You must choose a pre-defined value or set the customized value").to.be.equal(true));
      }
    }
  }

  checkProductCategory(i) {
    return this.client
      .scrollWaitForExistAndClick(AddProductPage.catalog_product_name.replace("%ID", global.positionTable[i - 1], 50, 50000))
      .waitForVisible(AddProductPage.product_name_input)
  }

  getSubCategoryNumber(selector, i) {
    return this.client
      .execute(function (selector, i) {
        let count;
        try {
          count = document.getElementById(selector).getElementsByTagName("ul")[i + 1].children.length;
          return count;
        }
        catch (err) {
          count = 0;
          return count;
        }
      }, selector, i)
      .then((count) => {
        global.subCatNumber = count.value;
      })
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
    return this.client
      .pause(2000)
      .then(() => {
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
              expect(elementsTable[k]).to.be.equal("art");
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
      });
  }

  checkCategoryProduct() {
    return this.client
      .pause(1000)
      .then(() => {
        expect(global.productCategories.HOME.Accessories).to.contains(tab['categoryName'])
      });
  }

  getCategoriesPageNumber(selector) {
    return this.client
      .waitForVisible('#' + selector)
      .execute(function (selector) {
        return document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
      }, selector)
      .then((count) => {
        global.categoriesPageNumber = count.value;
      });
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
