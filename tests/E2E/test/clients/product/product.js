var CommonClient = require('./../common_client');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {ProductList} = require('../../selectors/BO/add_product_page');
var data = require('./../../datas/product-data');
var path = require('path');

global.productIdElement = [];
global.productsTable = [];
global.productsSortedTable = [];

class Product extends CommonClient {

  getElementID() {
    return this.client
      .waitForExist(ProductList.first_product_id, 90000)
      .then(() => this.client.getText(ProductList.first_product_id))
      .then((text) => global.productIdElement[0] = text)
      .then(() => this.client.getText(ProductList.second_product_id))
      .then((text) => global.productIdElement[1] = text)
      .then(() => this.client.getText(ProductList.third_product_id))
      .then((text) => global.productIdElement[2] = text)
      .then((text) => expect(Number(global.productIdElement[0])).to.be.above(Number(global.productIdElement[1])))
      .then((text) => expect(Number(global.productIdElement[1])).to.be.above(Number(global.productIdElement[2])));
  }

  checkCategoryRadioButton(categoryValue) {
    return this.client
      .waitForVisible(AddProductPage.category_radio_button.replace('%VALUE', categoryValue))
      .scroll(0, 1000)
      .isVisible(AddProductPage.category_radio_button.replace('%VALUE', categoryValue), 60000)
      .then((text) => expect(text).to.be.true);
  }

  openAllCategory() {
    return this.client
      .scrollTo(AddProductPage.catalog_home, 50)
      .waitForExistAndClick(AddProductPage.catalog_home)
      .waitForExistAndClick(AddProductPage.catalog_first_element_radio)
      .waitForExistAndClick(AddProductPage.catalog_second_element_radio)
  }

  associatedFile() {
    return this.client
      .waitForExistAndClick(AddProductPage.virtual_associated_file)
      .pause(2000)
  }

  availability() {
    return this.client
      .scrollTo(AddProductPage.pack_label_out_stock, 50)
      .waitAndSetValue(AddProductPage.pack_label_out_stock, data.common.qty_msg_unstock)
  }

  selectPricingPriorities() {
    return this.client
      .scrollTo(AddProductPage.pricing_first_priorities_select, 50)
      .waitAndSelectByValue(AddProductPage.pricing_first_priorities_select, 'id_shop')
      .waitAndSelectByValue(AddProductPage.pricing_second_priorities_select, 'id_currency')
      .waitAndSelectByValue(AddProductPage.pricing_third_priorities_select, 'id_country')
      .waitAndSelectByValue(AddProductPage.pricing_fourth_priorities_select, 'id_group')
  }

  selectCondition() {
    return this.client
      .scrollTo(AddProductPage.options_condition_select, 50)
      .waitAndSelectByValue(AddProductPage.options_condition_select, 'refurbished')
  }

  UPCEntry() {
    return this.client
      .scrollTo(AddProductPage.options_upc, 50)
      .waitAndSetValue(AddProductPage.options_upc, data.common.upc)
  }

  addPackProduct(search, quantity) {
    return this.client
      .waitAndSetValue(AddProductPage.search_product_pack, search)
      .waitForExistAndClick(AddProductPage.product_item_pack)
      .waitAndSetValue(AddProductPage.product_pack_item_quantity, quantity)
      .waitForExistAndClick(AddProductPage.product_pack_add_button)
  }

  createCategory() {
    return this.client
      .scrollTo(AddProductPage.category_create_btn, 50)
      .waitForExistAndClick(AddProductPage.category_create_btn)
      .pause(4000)
  }

  searchAndAddRelatedProduct() {
    var search_products = data.common.search_related_products.split('//');
    return this.client
      .waitAndSetValue(AddProductPage.search_add_related_product_input, search_products[0])
      .waitForExistAndClick(AddProductPage.related_product_item)
      .waitAndSetValue(AddProductPage.search_add_related_product_input, search_products[1])
      .waitForExistAndClick(AddProductPage.related_product_item)
  }

  addFeatureHeight(type) {
    if (type === 'pack') {
      this.client
        .scrollTo(AddProductPage.product_add_feature_btn, 50)
    }
    return this.client
      .scrollTo(AddProductPage.product_add_feature_btn, 150)
      .waitForExistAndClick(AddProductPage.product_add_feature_btn)
      .waitForExistAndClick(AddProductPage.feature_select_button)
      .waitForExistAndClick(AddProductPage.feature_select_option_height)
      .waitAndSetValue(AddProductPage.feature_custom_value_height, data.standard.features.feature1.custom_value)
  }

  setPrice(selector, price) {
    return this.client
      .scrollTo(selector, 50)
      .waitAndSetValue(selector, price)
  }

  setVariationsQuantity(addProductPage, value) {
    return this.client
      .pause(4000)
      .waitAndSetValue(addProductPage.var_selected_quantitie, value)
      .scrollTo(addProductPage.combinations_thead)
      .waitForExistAndClick(addProductPage.save_quantitie_button)
  }

  selectFeature(addProductPage, name, value) {
    return this.client
      .moveToObject(addProductPage.feature_select)
      .waitForExistAndClick(addProductPage.feature_select)
      .waitAndSetValue(addProductPage.select_feature_created, name)
      .waitForExistAndClick(addProductPage.result_feature_select.replace('%ID', 0))
      .pause(2000)
      .selectByVisibleText(addProductPage.feature_value_select, value)
  }

  clickPageNext(selector) {
    if (global.isVisible) {
      return this.client
        .click(selector)
        .pause(2000)
    } else {
      return this.client.pause(1000)
    }
  }

  /**
   * This function allows to get the number of all products in Back Office
   * @param selector
   * @returns {*}
   */
  getProductsNumber(selector) {
    return this.client
      .execute(function (selector) {
        var count = document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
        return count;
      }, selector)
      .then((count) => {
        global.productsNumber = count.value;
      })
  }

  /**
   * This function allows to get the data of all products in Back Office
   * @param product_list: selector or every id, name or reference
   * @param i: index of element in the products table
   * @param sorted: to get the data after sort
   * @returns {*}
   */
  getProductsInformation(product_list, i, sorted = false) {
    return this.client
      .getText(product_list.replace("%ID", i + 1)).then(function (name) {
        if (sorted) {
          productsSortedTable[i] = name.toLowerCase();
        } else {
          productsTable[i] = name.toLowerCase();
        }
      });
  }

  sortByAsc(sortBy) {
    if (sortBy === 'id_product') {
      return productsTable.sort(function (a, b) {
        return b - a;
      }).reverse();
    } else {
      return productsTable.sort();
    }
  }

  sortByDesc(sortBy) {
    if (sortBy === 'id_product') {
      return productsTable.sort(function (a, b) {
        return a - b;
      }).reverse();
    } else {
      return productsTable.sort().reverse();
    }
  }

  /**
   * This function allows to sort the table of all products
   * @param sort_mode: to sort the table by ascendant(ASC) or descendant(DESC)
   * @param type: sort by number or string
   * @returns {*}
   */
  sortTable(sort_mode, type = 'id_product') {
    return this.client
      .pause(2000)
      .then(() => {
        this.client
          .waitUntil(function () {
            sort_mode === 'ASC' ? this.sortByAsc(type) : this.sortByDesc(type);
          }, 1000 * productsNumber)
      })
  }

  /**
   * This function allows to check the sort of all products data
   * @returns {*}
   */
  checkSortProduct() {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(productsTable).to.deep.equal(productsSortedTable);
          }, 1000 * productsNumber)
      })
  }

}

module.exports = Product;
