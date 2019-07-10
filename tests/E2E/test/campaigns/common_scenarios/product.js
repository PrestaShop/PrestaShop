const {Menu} = require('../../selectors/BO/menu.js');
let promise = Promise.resolve();
const {ProductList, AddProductPage} = require('../../selectors/BO/add_product_page');
const {CategorySubMenu} = require('../../selectors/BO/catalogpage/category_submenu');
const {TrafficAndSeo} = require('../../selectors/BO/shopParameters/shop_parameters');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {HomePage} = require('../../selectors/FO/home_page');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');
const {productPage} = require('../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../selectors/FO/order_page');
const {accountPage} = require('../../selectors/FO/add_account_page');
const {CategoryPageFO} = require('../../selectors/FO/category_page');
const {ProductSettings} = require('../../selectors/BO/shopParameters/product_settings.js');
const {ModulePage} = require('../../selectors/BO/module_page');
const commonManufacturers = require('../common_scenarios/suppliers');
const commonModules = require('../common_scenarios/module');

let data = require('../../datas/product-data');
global.productVariations = [];
global.productCategories = {HOME: {}};
global.categories = {HOME: {}};
const dateFormat = require('dateformat');
global.dateNow = new Date();
let common = require('../../common.webdriverio');

/**** Example of product data ****
 * var productData = {
 *  name: 'product_name',
 *  reference: 'product_reference',
 *  quantity: 'product_quantity',
 *  price: 'product_price',
 *  image_name: 'picture_file_name',
 *  type: "product_type(standard, pack, virtual)",
 *  attribute: {
 *      1: {
 *        name: 'attribute_name',
 *        variation_quantity: 'product_variation_quantity'
 *      }
 *  },
 *  feature: [
 *   {
 *     name: 'Feature',
 *     value: 'Value 1'
 *   }, {
 *     name: 'Feature',
 *     value: 'Value 2'
 *   }
 * ]
 *  pricing: {
 *      unitPrice: "product_unit_price",
 *      unity: "product_unity",
 *      wholesale: "product_wholesale",
 *      type: 'percentage',
 *      discount: 'product_discount'
 *  },
 *  categories :{
 *      0: {
 *          name:"name category",
 *          main_category: true/false
 *      }
 *  },
 *  options: {
 *      filename: "attached_filename"
 *  }
 * };
 */
module.exports = {
  createProduct: function (AddProductPage, productData, attributeData = {}) {
    scenario('Create a new product in the Back Office', client => {
      test('should go to "Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on "New Product" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(AddProductPage.new_product_button))
          .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
      });
      test('should set the "Name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, productData["name"] + date_time));
      test('should set the "Reference" input', () => client.waitAndSetValue(AddProductPage.product_reference, productData["reference"]));
      test('should set the "Quantity" input', () => client.waitAndSetValue(AddProductPage.quantity_shortcut_input, productData["quantity"]));
      test('should set the "Price" input', () => client.setPrice(AddProductPage.priceTE_shortcut, productData["price"]));
      test('should upload the first product picture', () => client.uploadPicture(productData["image_name"], AddProductPage.picture));

      if (productData.hasOwnProperty('type') && productData.type === 'pack') {
        scenario('Add the created product to pack', client => {
          test('should select the "Pack of products"', () => client.waitAndSelectByValue(AddProductPage.product_type, 1));
          test('should add products to the pack', () => client.addPackProduct(productData['product']['name'] + date_time, productData['product']['quantity']));
        }, 'product/product');
      }

      if (productData.hasOwnProperty('attribute')) {
        scenario('Add Attribute', client => {
          test('should select the "Product with combination" radio button', () => client.scrollWaitForExistAndClick(AddProductPage.variations_type_button));
          test('should go to "Combinations" tab', () => client.scrollWaitForExistAndClick(AddProductPage.variations_tab));
          test('should select the variation', () => {
            if (productData.type === 'combination') {
              return promise
                .then(() => client.createCombination(AddProductPage.combination_size_m, AddProductPage.combination_color_beige));
            } else {
              if (Object.keys(productData.attribute).length === 1) {
                return promise
                  .then(() => client.waitAndSetValue(AddProductPage.variations_input, productData.attribute[1].name + date_time + " : All"))
                  .then(() => client.waitForExistAndClick(AddProductPage.variations_select));
              } else {
                Object.keys(productData.attribute).forEach(function (key) {
                  if (productData.attribute[key].name === attributeData[key - 1].name) {
                    promise = client.scrollWaitForExistAndClick(AddProductPage.attribute_group_name.replace('%NAME', productData.attribute[key].name + date_time), 150, 3000);
                    Object.keys(attributeData[key - 1].values).forEach(function (index) {
                      client.waitForExistAndClickJs(AddProductPage.attribute_value_checkbox.replace('%ID', global.tab[productData.attribute[key].name + '_id']).replace('%S', index), 300);
                    });
                  }
                });
                return promise.then(() => client.pause(5000));
              }
            }
          });
          test('should click on "Generate" button', () => client.scrollWaitForExistAndClick(AddProductPage.variations_generate));
          test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.'));

          /**
           * Should refresh the page because of the issue here
           * https://github.com/PrestaShop/PrestaShop/issues/9826
           **/
          test('should refresh the page if "Debug" mode is active because of the issue here  "#9826" ', () => {
            return promise
              .then(() => client.isVisible(AddProductPage.var_selected))
              .then(() => {
                if (global.ps_mode_dev && !global.isVisible) {
                  client.refresh();
                } else {
                  client.pause(0);
                }
              })
              .then(() => client.getCombinationData(1, 7000));
          });
          test('should select all the generated variations', () => client.waitForVisibleAndClick(AddProductPage.var_selected, 2000));
          test('should set the "Variations quantity" input', () => {
            return promise
              .then(() => client.pause(4000))
              .then(() => client.setVariationsQuantity(AddProductPage, productData.attribute[1].variation_quantity))
              .then(() => client.waitForSymfonyToolbar(AddProductPage, 3000))
          });

        }, 'product/create_combinations');
      }

      if (productData.hasOwnProperty('feature')) {
        for (let f = 0; f < productData['feature'].length; f++) {
          scenario('Add Feature', client => {
            test('should click on "Add feature" button', () => {
              return promise
                .then(() => client.scrollWaitForExistAndClick(AddProductPage.add_feature_to_product_button));
            });
            test('should select the created feature', () => client.selectFeature(AddProductPage, productData['feature'][f].name + date_time, productData['feature'][f].value, f));
          }, 'product/product');
        }
      }

      if (productData.hasOwnProperty('quantities')) {
        scenario('Edit availibility preferences', client => {
          test('should click on "Quantities"', () => client.scrollWaitForExistAndClick(AddProductPage.variations_tab, 50));
          if (productData.quantities.stock === 'deny') {
            test('should check "Deny orders"', () => client.waitForExistAndClick(AddProductPage.combination_availability_preferences.replace("%NUMBER", 0)));
          }
          else {
            test('should check "Allow orders"', () => client.waitForExistAndClick(AddProductPage.combination_availability_preferences.replace("%NUMBER", 1)));
          }
        }, 'product/product');
      }

      if (productData.hasOwnProperty('pricing')) {
        scenario('Edit product pricing', client => {
          test('should click on "Pricing"', () => client.scrollWaitForExistAndClick(AddProductPage.product_pricing_tab, 50));
          test('should set the "Price per unit (tax excl.)"', () => client.waitAndSetValue(AddProductPage.unit_price, productData['pricing']['unitPrice']));
          test('should set the "Unit"', () => client.waitAndSetValue(AddProductPage.unity, productData['pricing']['unity']));
          test('should set the "Price (tax excl.)"', () => client.waitAndSetValue(AddProductPage.pricing_wholesale, productData['pricing']['wholesale']));
          test('should click on "Add specific price" button', () => client.scrollWaitForExistAndClick(AddProductPage.pricing_add_specific_price_button));
          test('should change the reduction type to "Percentage"', () => {
            return promise
              .then(() => client.pause(3000))
              .then(() => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, productData['pricing']['type']));
          });
          test('should set the "Discount" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, productData['pricing']['discount']));
          test('should click on "Apply" button', () => client.waitForExistAndClick(AddProductPage.specific_price_save_button));
        }, 'product/product');
      }

      if (productData.hasOwnProperty('options')) {
        scenario('Edit product options', client => {
          test('should click on "Options"', () => client.scrollWaitForExistAndClick(AddProductPage.product_options_tab));
          test('should select the attached file to the product', () => {
            promise = client.scrollTo(AddProductPage.options_add_new_file_button);
            for (let i = 0; i < productData.options.filename.length; i++) {
              promise = client.waitForExistAndClick(AddProductPage.attached_file_checkbox.replace('%FileName', productData.options.filename[i] + global.date_time), 1000);
            }
            return promise
              .then(() => client.waitForSymfonyToolbar(AddProductPage, 1000))
              .then(() => client.pause(1000));
          });
        }, 'product/product');
      }

      if (productData.hasOwnProperty('categories')) {
        scenario('Add category', client => {
          test('should search for the category', () => client.waitAndSetValue(AddProductPage.search_categories, productData.categories['1']['name'] + date_time));
          test('should select the category', () => client.waitForVisibleAndClick(AddProductPage.list_categories));
          if (Object.keys(productData.categories).length > 1) {
            Object.keys(productData.categories).forEach(function (key) {
              if (productData.categories[key]["main_category"] && productData.categories[key]["name"] !== 'home') {
                test('should choose the created category as default', () => {
                  return promise
                    .then(() => client.scrollTo(AddProductPage.category_radio.replace('%S', productData.categories[key]["name"] + date_time)))
                    .then(() => client.waitForExistAndClick(AddProductPage.category_radio.replace('%S', productData.categories[key]["name"] + date_time), 4000));
                });
              }
            });
          } else {
            test('should delete the home category', () => client.waitForExistAndClick(AddProductPage.default_category));
          }
        }, 'product/product');
      }

      if (productData.hasOwnProperty('tax_rule')) {
        test('should select the "Tax rule" to "' + productData.tax_rule + '"', () => {
          return promise
            .then(() => client.scrollWaitForExistAndClick(AddProductPage.tax_rule))
            .then(() => client.waitForVisibleAndClick(AddProductPage.tax_option.replace('%V', productData.tax_rule)));
        });
      }

      scenario('Save the created product', client => {
        test('should check then close symfony toolbar', () => client.waitForSymfonyToolbar(AddProductPage, 1000));
        test('should switch the product online and verify the appearance of the green validation', () => {
          return promise
            .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000))
            .then(() => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.', 3000));
        });
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
        test('should check and close the green validation', async () => {
          return promise
            .then(() => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.', 'equal', 2000))
            .then(() => client.waitForExistAndClick(AddProductPage.close_validation_button, 1000));
        });
      }, 'product/product');
    }, 'product/product');

  },
  checkProductBO(AddProductPage, productData) {
    scenario('Check the product creation in the Back Office', client => {
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for product by name', () => client.searchProductByName(productData.name + date_time));
      test('should check the existence of product name', () => client.checkTextValue(AddProductPage.catalog_product_name, productData.name + date_time));
      test('should check the existence of product reference', () => client.checkTextValue(AddProductPage.catalog_product_reference, productData.reference));
      test('should check the existence of product category', () => client.checkTextValue(AddProductPage.catalog_product_category, 'Home'));
      test('should check the existence of product price TE', () => client.checkProductPriceTE(productData.price));
      test('should check the existence of product quantity', () => client.checkTextValue(AddProductPage.catalog_product_quantity, productData.quantity));
      test('should check the existence of product status', () => client.checkTextValue(AddProductPage.catalog_product_online, 'check'));
      test('should click on "Reset button"', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/check_product');
  },
  sortProduct: async function (selector, sortBy, isNumber = false, priceWithCurrency = false) {
    global.elementsSortedTable = [];
    global.elementsTable = [];
    scenario('Check the sort of products by "' + sortBy.toUpperCase() + '"', client => {
      test('should click on "Sort by ASC" icon', async () => {
        for (let j = 0; j < (parseInt(global.productsNumber)); j++) {
          await client.getTableField(selector, j, false, priceWithCurrency);
        }
        if (sortBy === 'id_product') {
          await client.moveToObject(ProductList.sort_by_icon.replace("%B", sortBy).replace("%W", "desc"));
          await client.scrollWaitForExistAndClick(ProductList.sort_by_icon.replace("%B", sortBy).replace("%W", "desc"));
        } else if (sortBy === 'price_included') {
          await client.moveToObject(ProductList.price_tax_included_sort_button);
          await client.waitForExistAndClick(ProductList.price_tax_included_sort_button);
        } else {
          await client.moveToObject(ProductList.sort_button.replace("%B", sortBy));
          await client.waitForExistAndClick(ProductList.sort_button.replace("%B", sortBy));
        }
      });
      test('should check that the products are well sorted by ASC', async () => {
        for (let j = 0; j < (parseInt(global.productsNumber)); j++) {
          await client.getTableField(selector, j, true, priceWithCurrency);
        }
        await client.checkSortTable(isNumber, 'ASC');
      });
      test('should click on "Sort by DESC" icon', async () => {
        if (sortBy === 'id_product') {
          await client.moveToObject(ProductList.sort_by_icon.replace("%B", sortBy).replace("%W", "asc"));
          await client.waitForExistAndClick(ProductList.sort_by_icon.replace("%B", sortBy).replace("%W", "asc"));
        } else if (sortBy === 'price_included') {
          await client.moveToObject(ProductList.price_tax_included_sort_button);
          await client.waitForExistAndClick(ProductList.price_tax_included_sort_button);
        } else {
          await client.moveToObject(ProductList.sort_button.replace("%B", sortBy));
          await client.waitForExistAndClick(ProductList.sort_button.replace("%B", sortBy));
        }
      });
      test('should check that the products are well sorted by DESC', async () => {
        for (let j = 0; j < (parseInt(global.productsNumber)); j++) {
          await client.getTableField(selector, j, true, priceWithCurrency);
        }
        await client.checkSortTable(isNumber, 'DESC');
      });
    }, 'product/product');
  },
  sortProductByStatus: async function () {
    scenario('Check the sort of products by "STATUS"', client => {
      test('should select "Inactive" in Status list then click on "Search" button', async () => {
        await client.waitAndSelectByValue(ProductList.status_filter, "0");
        await client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button);
      });
      test('should get the number of inactive products', async () => {
        await client.isVisible(ProductList.pagination_products);
        await client.getProductsNumber(ProductList.pagination_products);
      });
      test('should click on "Reset" button', async () => {
        await client.pause(1000);
        global.inactiveProductsNumber = await global.productsNumber;
        await client.waitForExistAndClick(AddProductPage.catalog_reset_filter);
      });
      test('should select "Active" in Status list then click on "Search" button', async () => {
        await client.waitAndSelectByValue(ProductList.status_filter, "1");
        await client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button);
      });
      test('should get the number of active products', async () => {
        await client.isVisible(ProductList.pagination_products);
        await client.getProductsNumber(ProductList.pagination_products);
      });
      test('should click on "Reset" button', async () => {
        await client.pause(1000);
        global.activeProductsNumber = await global.productsNumber;
        await client.waitForExistAndClick(AddProductPage.catalog_reset_filter);
      });
      test('should check that the products are well sorted by ASC', async () => {
        await client.moveToObject(ProductList.sort_button.replace("%B", 'active'));
        await client.waitForExistAndClick(ProductList.sort_button.replace("%B", 'active'));
        for (let j = 0; j < (parseInt(global.inactiveProductsNumber)); j++) {
          await client.isExisting(ProductList.product_status_icon.replace('%TR', j + 1).replace('%STATUS', 'disabled'));
        }
        for (let j = parseInt(global.inactiveProductsNumber); j < (parseInt(global.inactiveProductsNumber) + parseInt(global.activeProductsNumber)); j++) {
          await client.isExisting(ProductList.product_status_icon.replace('%TR', j + 1).replace('%STATUS', 'enabled'));
        }
      });
      test('should check that the products are well sorted by DESC', async () => {
        await client.moveToObject(ProductList.sort_button.replace("%B", 'active'));
        await client.waitForExistAndClick(ProductList.sort_button.replace("%B", 'active'));
        for (let j = 0; j < (parseInt(global.activeProductsNumber)); j++) {
          await client.isExisting(ProductList.product_status_icon.replace('%TR', j + 1).replace('%STATUS', 'enabled'));
        }
        for (let j = parseInt(global.activeProductsNumber); j < (parseInt(global.inactiveProductsNumber) + parseInt(global.activeProductsNumber)); j++) {
          await client.isExisting(ProductList.product_status_icon.replace('%TR', j + 1).replace('%STATUS', 'disabled'));
        }
      });
    }, 'product/product');
  },
  productList: function (AddProductPage, selector, searchBy, client, min = 0, max = 0) {
    test('should check the list of products by "' + searchBy + '"', async () => {
      global.elementsTable = [];
      await client.pause(1000);
      for (let j = 0; j < global.productsNumber; j++) {
        await client.getTableField(selector, j, false, true);
      }
      await client.checkSearchProduct(searchBy, min, max);
    });
    test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
  },

  checkPaginationFO(client, productPage, buttonName, pageNumber) {
    let selectorButton = buttonName === 'Next' ? productPage.pagination_next : productPage.pagination_previous;
    test('should click on "' + buttonName + '" button', () => {
      return promise
        .then(() => client.isVisible(selectorButton))
        .then(() => client.clickNextOrPrevious(selectorButton));
    });
    test('should check that the current page number is equal to "' + pageNumber + '"', () => client.checkTextValue(productPage.current_page, pageNumber));
    test('should check that the page value in the URL is equal to "' + pageNumber + '"', () => client.checkParamFromURL('page', pageNumber));
  },

  checkPaginationBO(nextOrPrevious, pageNumber, itemPerPage, close = false, paginateBetweenPages = false) {
    scenario('Navigate between catalog pages and set the paginate limit equal to "' + itemPerPage + '"', client => {
      let selectorButton = nextOrPrevious === 'Next' ? ProductList.pagination_next : ProductList.pagination_previous;
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should set the "item per page" to "' + itemPerPage + '"', () => client.waitAndSelectByValue(ProductList.item_per_page, itemPerPage));
      test('should check that the current page is equal to "' + pageNumber + '"', () => client.checkAttributeValue(ProductList.page_active_number, 'value', pageNumber, 'contain', 3000));
      test('should check that the number of products is less or equal to "' + itemPerPage + '"', () => {
        return promise
          .then(() => client.getProductPageNumber('product_catalog_list'))
          .then(() => expect(global.productsNumber).to.be.at.most(itemPerPage))
      });
      if (paginateBetweenPages) {
        test('should close the symfony toolbar if exists', async () => await client.waitForSymfonyToolbar(AddProductPage, 3000));
        test('should click on "' + nextOrPrevious + '" button', () => {
          return promise
            .then(() => client.isVisible(selectorButton))
            .then(() => client.clickNextOrPrevious(selectorButton));
        });
        test('should check that the current page is equal to 2', () => client.checkAttributeValue(ProductList.page_active_number, 'value', '2', 'contain', 3000));
        test('should set the "Page value" input to "' + pageNumber + '"', () => {
          return promise
            .then(() => client.waitAndSetValue(ProductList.page_active_number, pageNumber))
            .then(() => client.keys('Enter'));
        });
        test('should check that the current page is equal to "' + pageNumber + '"', () => client.checkAttributeValue(ProductList.page_active_number, 'value', pageNumber, 'contain', 3000));
      }
      if (close)
        test('should set the "item per page" to 20 (back to normal)', () => client.waitAndSelectByValue(ProductList.item_per_page, 20));
    }, 'product/product');
  },

  deleteProduct(AddProductPage, productData) {
    scenario('Delete the created product', client => {
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for the created product', () => client.searchProductByName(productData.name + date_time));
      test('should click on "Dropdown toggle" button', () => client.waitForExistAndClick(ProductList.dropdown_button.replace('%POS', '1')));
      test('should click on "Delete" action', () => client.waitForExistAndClick(ProductList.action_delete_button.replace('%POS', '1')));
      test('should click on "Delete now" modal button', () => client.waitForVisibleAndClick(ProductList.delete_now_modal_button, 1000));
      test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.success_panel, 'Product successfully deleted.'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/check_product');
  },

  addProductFeature(client, feature, id, predefinedValue = '', customValue = '', option = "predefined_value") {
    test('should click on "Add a feature" button', () => {
      return promise
        .then(() => client.scrollTo(AddProductPage.add_related_product_btn))
        .then(() => client.waitForExistAndClick(AddProductPage.product_add_feature_btn, 3000))
    });
    test('should choose "' + feature + '" feature from the dropdown list', () => {
      return promise
        .then(() => client.scrollWaitForExistAndClick(AddProductPage.feature_select_button.replace('%ID', id)))
        .then(() => client.waitForVisibleAndClick(AddProductPage.feature_select_option_text.replace('%ID', id).replace('%V', feature), 1000));
    });
    if (option === "predefined_value") {
      test('should choose "Cotton" pre-defined value from the dropdown list', () => client.waitAndSelectByVisibleText(AddProductPage.feature_value_select.replace('%ID', id).replace('%V', 'not(@disabled)'), predefinedValue, 2000));
    } else {
      test('should set the "Custom value" input', () => client.waitAndSetValue(AddProductPage.feature_custom_value.replace('%ID', 1), customValue));
    }
  },

  async checkProductInListFO(AccessPageFO, productPage, productData, client) {
    await client.signInFO(AccessPageFO);
    await client.changeLanguage();
    await client.scrollWaitForExistAndClick(productPage.see_all_products);
    for (let i = 0; i <= global.pagination; i++) {
      for (let j = 0; j < 4; j++) {
        await client.pause(4000);
        await client.isVisible(productPage.productLink.replace('%PRODUCTNAME', productData[j].name + date_time));
        await client.middleClick(productPage.productLink.replace('%PRODUCTNAME', productData[j].name + date_time), global.isVisible);
      }
      if (i !== global.pagination) {
        await client.isVisible(productPage.pagination_next);
        if (global.isVisible) {
          await client.clickPageNext(productPage.pagination_next);
        }
      }
    }
    //Check "standard" product information
    await client.switchWindow(4);
    await client.checkTextValue(productPage.product_name, (productData[0].name + date_time).toUpperCase());
    await client.checkTextValue(productPage.product_price, "€12.00", "contain");
    await client.scrollTo(productPage.product_reference);
    await client.checkTextValue(productPage.product_reference, productData[0].reference);
    await client.checkAttributeValue(productPage.product_quantity, 'data-stock', productData[0].quantity);
    //Check "pack" product information
    await client.switchWindow(3);
    await client.checkTextValue(productPage.product_name, (productData[1].name + date_time).toUpperCase());
    await client.checkTextValue(productPage.product_price, "€12.00", "contain");
    await client.checkTextValue(productPage.pack_product_name.replace('%P', 1), productData[0].name + date_time);
    await client.checkTextValue(productPage.pack_product_price.replace('%P', 1), '€12.00');
    await client.checkTextValue(productPage.pack_product_quantity.replace('%P', 1), 'x 1');
    await client.scrollTo(productPage.product_reference);
    await client.checkTextValue(productPage.product_reference, productData[1].reference);
    await client.checkAttributeValue(productPage.product_quantity, 'data-stock', productData[1].quantity);
    //Check "combination" product information
    await client.switchWindow(2);
    await client.checkTextValue(productPage.product_name, (productData[2].name + date_time).toUpperCase());
    await client.checkTextValue(productPage.product_price, "€12.00", "contain");
    await client.scrollTo(productPage.product_reference);
    await client.checkTextValue(productPage.product_reference, productData[2].reference);
    await client.checkAttributeValue(productPage.product_quantity, 'data-stock', productData[2].quantity);
    //Check "virtual" product information
    await client.switchWindow(1);
    await client.checkTextValue(productPage.product_name, (productData[3].name + date_time).toUpperCase());
    await client.checkTextValue(productPage.product_price, "€12.00", "contain");
    await client.scrollTo(productPage.product_reference);
    await client.checkTextValue(productPage.product_reference, productData[3].reference);
    await client.checkAttributeValue(productPage.product_quantity, 'data-stock', productData[3].quantity);

  },
  async checkAllProduct(AccessPageFO, productPage, client) {
    await client.signInFO(AccessPageFO);
    await client.changeLanguage();
    await client.scrollWaitForExistAndClick(productPage.see_all_products);
    for (let i = 0; i < global.pagination; i++) {
      for (let j = 0; j < global.productInfo.length; j++) {
        await client.pause(500);
        await client.isVisible(AccessPageFO.product_name.replace('%PAGENAME', global.productInfo[j].name.substring(0, 23)));
        if (global.isVisible) {
          global.productInfo[j].status = await true;
        }
      }
      if (i + 1 < pagination) {
        await client.isVisible(productPage.pagination_next);
        await client.clickPageNext(productPage.pagination_next, 3000);
      }
    }
    for (let i = 0; i < global.productInfo.length; i++) {
      await expect(global.productInfo[i].status, 'the product ' + global.productInfo[i].name + ' doesn\'t in the Front Office').to.equal(true);
    }
    await client.pause(2000);
  },

  /**** Example of demo product data ****
   * var productData = {
   *  name: 'product_name',
   *  type: "product_type(standard, pack, virtual, combination, customizable)", // (required)
   *  picture: 'picture_file_name',  // (required)
   *  quantity: 'product_quantity',  // (optional)
   *  priceHT: 'product_price_with_tax_excluded',  // (required)
   *  priceTTC: 'product_price_with_tax_included',  // (required)
   *  summary: 'product_summary',  // (required)
   *  description: 'product_description',//if product type is equal to Pack so the description can be empty
   *  feature: {  // (optional)
   *      name: 'feature_name',
   *      predefined_value: 'feature_predefined_value',//if custom_value is empty so you must set this value
   *      custom_value: 'feature_custom_value',//if you didn't choose a predefined_value so you must set this value
   *  },
   *  combination: {
   *      exist: true //(required) must check the appearance of generated combination when the product type is equal to combination
   *  }
   * };
   */

  checkDemoProductBO(AddProductPage, productData) {
    scenario('Check the basic information of "' + productData.name + '"', client => {
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for the product by name', () => client.searchProductByName(productData.name));
      test('should click on "Edit" button', () => client.waitForExistAndClick(ProductList.edit_button));
      if (productData.hasOwnProperty('type')) {
        if (productData.type === 'standard' || productData.type === 'combination' || productData.type === 'customizable') {
          if (productData.type === 'customizable') {
            test('should check that the product name contains Customizable', () => client.checkAttributeValue(AddProductPage.product_name_input, 'value', 'Customizable', 'contain'));
          }
          test('should check that "Standard product" type is well selected', () => client.isSelected(AddProductPage.product_type_option.replace('%POS', 1)));
        } else if (productData.type === 'pack') {
          test('should check that the product name contains Pack', () => client.checkAttributeValue(AddProductPage.product_name_input, 'value', 'Pack', 'contain'));
          test('should check that "Pack of product" type is well selected', () => client.isSelected(AddProductPage.product_type_option.replace('%POS', 2)));
          test('should check that the "List of products for this pack" is well displayed', () => client.isExisting(AddProductPage.product_pack_items));
          test('should check that the "Add products to your pack" is well displayed', () => client.isExisting(AddProductPage.add_products_to_pack));
        } else {
          test('should check that "Virtual product" type is well selected', () => client.isSelected(AddProductPage.product_type_option.replace('%POS', 3)));
        }
      }
      if (productData.hasOwnProperty('picture')) {
        test('should check the appearance of the product picture', () => client.checkAttributeValue(AddProductPage.background_picture, 'style', productData.picture, 'contain'));
      }
      test('should check that the product quantity is equal to "' + productData.quantity + '"', () => client.checkAttributeValue(AddProductPage.product_quantity_input, 'value', productData.quantity));
      test('should check that the product price HT is equal to "' + productData.priceHT + '"', () => client.checkAttributeValue(AddProductPage.priceTE_shortcut, 'value', productData.priceHT));
      test('should check that the product price TTC is equal to "' + productData.priceTTC + '"', () => client.checkAttributeValue(AddProductPage.priceTTC_shortcut, 'value', productData.priceTTC));
      test('should check that "' + productData.tax_rule + '" of tax rule is well selected', () => client.isSelected(AddProductPage.tax_rule_taux_standard_option));
      test('should check that the product summary is well filled', () => client.checkTextEditor(AddProductPage.summary_textarea, productData.summary, 2000));
      if (productData.hasOwnProperty('type') && productData.type !== 'pack') {
        test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.description_tab));
        test('should check that the product description is well filled', () => client.checkTextEditor(AddProductPage.description_textarea, productData.description, 2000));
      }
      if (productData.hasOwnProperty('feature')) {
        test('should check that the product feature is well filled', () => {
          return promise
            .then(() => client.isVisible(AddProductPage.feature_select_button))
            .then(() => client.checkFeatureValue(AddProductPage.predefined_value_option.replace('%V', productData.feature.predefined_value), AddProductPage.custom_value_input, productData.feature));
        });
      }
      if (productData.hasOwnProperty('combination') && productData.type === 'combination') {
        test('should check that "Product with combination" is well selected', () => client.checkAttributeValue(AddProductPage.product_combinations.replace('%I', 2), 'value', '1'));
        test('should click on "Combinations" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab));
        test('should check the appearance of the first generated combination ', () => client.waitForExist(AddProductPage.combination_table));
      }
      if (productData.type === 'virtual') {
        test('should click on "Virtual" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab));
      }
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/check_product');
  },

  clickOnPreviewLink(client, selector, productSelector) {
    test('should click on the "Preview" link', async () => {
      await client.isVisible(productSelector, 6000);
      if (global.ps_mode_dev && global.isVisible === false) {
        await client.waitForExistAndClick(selector)
      } else {
        await client.pause(0);
      }
      await client.pause(5000);
    });
  },

  async checkPaginationThenCreateProduct(client, productData) {
    await client.getProductPageNumber('product_catalog_list', 5000);
    let productNumber = await 20 - global.productsNumber;
    if (productNumber !== 0) {
      for (let i = 0; i < productNumber + 1; i++) {
        await client.waitForExistAndClick(Menu.Sell.Catalog.products_submenu, 1000);
        await client.waitForExistAndClick(AddProductPage.new_product_button, 2000);
        await client.waitAndSetValue(AddProductPage.product_name_input, productData["name"] + date_time);
        await client.waitAndSetValue(AddProductPage.product_reference, productData["reference"]);
        await client.waitAndSetValue(AddProductPage.quantity_shortcut_input, productData["quantity"]);
        await client.setPrice(AddProductPage.priceTE_shortcut, productData["price"]);
        await client.uploadPicture(productData["image_name"], AddProductPage.picture);
        await client.waitForSymfonyToolbar(AddProductPage, 2000);
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 1000);
        await client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.');
        await client.waitForExistAndClick(AddProductPage.save_product_button, 4000);
        await client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.');
      }
    }
  },

  getCategories: async function (client) {
    if (global.categoriesPageNumber !== 0) {
      for (let i = 1; i <= global.categoriesPageNumber; i++) {
        await client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu);
        await client.getTextInVar(CategorySubMenu.category_name.replace('%ID', i), "category");
        global.categories.HOME[tab["category"]] = await [tab["category"]];
        await client.isVisible(CategorySubMenu.category_view_button.replace('%ID', i));
        if (global.isVisible) {
          await client.waitForExistAndClick(CategorySubMenu.category_view_button.replace('%ID', i));
          await client.getProductPageNumber('table-category');
          for (let j = 1; j <= global.productsNumber; j++) {
            await client.getTextInVar(CategorySubMenu.category_name.replace('%ID', j), "subCategory");
            global.categories.HOME[tab["category"]][j] = await tab["subCategory"];
          }
        }

      }
    }
  },

  checkCategories: async function (client) {
    await client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu);
    await client.waitForExistAndClick(ProductList.filter_by_category_button);
    await client.waitForExistAndClick(ProductList.expand_filter_link);
    for (let i = 1; i <= global.categoriesPageNumber; i++) {
      await client.getTextInVar(ProductList.filter_list_category_label.replace('%ID', i), "productCategory");
      global.productCategories.HOME[tab["productCategory"]] = await [tab["productCategory"]];
      await client.getSubCategoryNumber('product_categories_categories', i);
      if (global.subCatNumber !== 0) {
        for (let j = 1; j <= global.subCatNumber; j++) {
          await client.getTextInVar(ProductList.sub_category_label.replace('%I', i).replace('%J', j), 'psubCategory');
          global.productCategories.HOME[tab["productCategory"]][j] = await tab["psubCategory"];
        }
      }
    }
  },

  checkFiltersCategories: async function (client) {
    await client.waitForExistAndClick(ProductList.category_radio.replace('%CATEGORY', 'Accessories'));
    await client.getProductPageNumber('product_catalog_list');
    for (let i = 1; i <= global.productsNumber; i++) {
      await client.getTextInVar(ProductList.products_column.replace('%ID', i).replace('%COL', 6), 'categoryName');
      await client.checkCategoryProduct();
    }
  },

  checkProductQuantity(Menu, AddProductPage, productName, quantity) {
    scenario('Check the quantity of the "' + productName + '"', client => {
      test('should go to "Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for product by name', () => client.searchProductByName(productName));
      test('should check the existence of product quantity', () => client.checkTextValue(AddProductPage.catalog_product_quantity, quantity));
    }, 'product/check_product');
  },
  checkProductPaginationFO(client, productPage, enableOrDisable, url, window = 1) {
    test('should go to "Shop Parameters > Traffic & SEO" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.traffic_seo_submenu));
    test('should close symfony toolbar if it exists', () => client.waitForSymfonyToolbar(AddProductPage, 3000));
    test('should ' + enableOrDisable + ' the "Friendly URL"', () => client.waitForExistAndClick(TrafficAndSeo.SeoAndUrls.friendly_url_button.replace('%s', url)));
    test('should click on "Save" button', () => client.waitForExistAndClick(TrafficAndSeo.SeoAndUrls.save_button, 1000));
    test('should go to the Front Office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(window))
        .then(() => client.changeLanguage());
    });
    test('should click on "Display all products" link', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
    test('should verify that we are redirected to the "Home" category', () => client.checkTextValue(AccessPageFO.category_title, 'HOME'));
    test('should click on "Next" link', () => client.clickPageNext(productPage.pagination_next, 3000));
    test('should click on "Previous" link', () => client.clickPageNext(productPage.pagination_previous, 3000));
    test('should click on "2" link', () => client.clickPageNext(productPage.pagination_number_link.replace('%NUM', 2), 3000));
    test('should click on "1" link', () => client.clickPageNext(productPage.pagination_number_link.replace('%NUM', 1), 3000));
    test('should go back to the Back Office', () => client.switchWindow(0));
  },

  CheckButtonsInHeaderProduct(productType, productData, secondProductData) {
    scenario('Check product name and language selector', client => {
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on "New product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
      if (productType === 'virtual') {
        test('should select the "virtual product" type', () => client.waitAndSelectByValue(AddProductPage.product_type, 2));
      }
      test('should check the "product name" placeholder', () => client.checkAttributeValue(AddProductPage.product_name_input, 'placeholder', 'Enter your product name'));
      test('should check that the "product name" is empty', () => client.checkAttributeValue(AddProductPage.product_name_input, 'value', ''));
      test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, productData.name + date_time));
      test('should check then close symfony toolbar', () => client.waitForSymfonyToolbar(AddProductPage, 1000));
      test('should switch the product online', () => {
        return promise
          .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000))
          .then(() => client.waitForExistAndClick(AddProductPage.close_validation_button, 1000));
      });
      test('should click on "Save" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(AddProductPage.save_product_button, 5000))
          .then(() => client.waitForExistAndClick(AddProductPage.close_validation_button, 1000));
      });
      test('should check that the "product name" input is equal to "' + productData.name + date_time + '"', () => client.checkAttributeValue(AddProductPage.product_name_input, 'value', productData.name + date_time));
      test('should select "fr" in language list', () => client.waitAndSelectByValue(AddProductPage.product_language, 'fr'));
      test('should check that the "product name" input is equal to "' + productData.name + date_time + '"', () => client.checkAttributeValue(AddProductPage.product_name_input, 'value', productData.name + date_time));
      test('should select "en" in language list', () => client.waitAndSelectByValue(AddProductPage.product_language, 'en'));
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname, 1000))
          .then(() => client.switchWindow(1))
          .then(() => client.changeLanguage('fr'));
      });
      test('should search for the product ' + productData.name + date_time + ' then go back to the Back Office', async () => {
        await client.waitAndSetValue(HomePage.search_input, productData.name + date_time);
        await client.waitForExistAndClick(HomePage.search_icon, 2000);
        await client.waitForExist(productPage.productLink.replace('%PRODUCTNAME', productData.name + date_time));
        await client.pause(1000);
        await client.closeOtherWindow(1);
        await client.switchWindow(0);
      });
    }, 'common_client');
    scenario('Check product type', client => {
      test('should check the existence of 3 types of product', async () => {
        for (let i = 0; i < 3; i++) {
          await client.waitForExist(AddProductPage.product_type_value_option.replace('%TYPE', i));
        }
      });
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for the product by name', () => client.searchProductByName(secondProductData.name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(ProductList.edit_button));
      test('should click on "Simple product" radio button', () => client.waitForExistAndClick(AddProductPage.product_combinations.replace('%I', 1)));
      test('should verify the appearance of the warning modal', () => client.checkTextValue(AddProductPage.confirmation_modal_content, 'This will delete all the combinations. Do you wish to proceed?', 'equal', 3000));
      test('should click on "Yes" button from the modal', () => {
        return promise
          .then(() => client.waitForExistAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'No')))
          .then(() => client.refresh());
      });
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on reset button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/check_product');
    scenario('Check "Sales", "Quick navigation" and "Help" buttons in the header', client => {
      test('should click on "New product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
      test('should click on "Sales" button', () => client.waitForExistAndClick(AddProductPage.sales_button));
      test('should check that "Product details" block is displayed in stats page', async () => {
        await client.switchWindow(1);
        await client.isExisting(AddProductPage.calendar_form, 2000);
        await client.closeOtherWindow(1);
        await client.switchWindow(0);
      });
      test('should click on "Quick navigation" button', () => client.waitForExistAndClick(AddProductPage.product_list_button));
      test('should check the existence of id, name, price and quantity columns', async () => {
        await client.waitForExist(AddProductPage.quick_navigation_column.replace('%TEXT', 'ID'));
        await client.waitForExist(AddProductPage.quick_navigation_column.replace('%TEXT', 'Name'));
        await client.waitForExist(AddProductPage.quick_navigation_column.replace('%TEXT', 'Price'));
        await client.waitForExist(AddProductPage.quick_navigation_column.replace('%TEXT', 'Quantity'));
      });
      test('should click on "' + productData.name + date_time + '" link', () => client.waitForExistAndClick(AddProductPage.quick_navigation_product_name_link.replace('%TEXT', productData.name + date_time)));
      test('should check that we are redirected to "' + productData.name + date_time + '" page', () => client.checkAttributeValue(AddProductPage.product_name_input, 'value', productData.name + date_time));
      test('should click on "Help" button', () => client.waitForExistAndClick(AddProductPage.help_button));
      test('should check that the "Help" sidebar is opened', () => client.isExisting(AddProductPage.right_sidebar, 2000));
      test('should close the "Help" sidebar', () => client.waitForExistAndClick(AddProductPage.help_button));
    }, 'common_client');
  },

  CheckButtonsInFooterProduct(productType, productData, client) {
    test('should click on "Delete" icon', async () => {
      await client.waitForExistAndClick(AddProductPage.delete_button);
      await client.waitForVisible(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'No'))
    });
    test('should click on "No" of the confirmation modal', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'No')));
    test('should go to "Catalog > products" page', () => {
      return promise
        .then(() => client.pause(2000))
        .then(() => client.waitForVisibleAndClick(Menu.Sell.Catalog.products_submenu));
    });
    test('should search for product by name', () => client.searchProductByName(productData.name + date_time));
    test('should click on the product name', () => client.waitForExistAndClick(AddProductPage.catalog_product_name));
    test('should click on "Delete" icon', async () => {
      await client.waitForExistAndClick(AddProductPage.delete_button);
      await client.waitForVisible(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes'))
    });
    test('should click on "Yes" of the confirmation modal', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
    test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.success_panel, 'Product successfully deleted.', 'equal', 2000));
    test('should click on "New product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
    if (productType === 'virtual') {
      test('should select the "virtual product" type', () => client.waitAndSelectByValue(AddProductPage.product_type, 2));
    }
    test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, productData.name + date_time));
    test('should check then close symfony toolbar', () => client.waitForSymfonyToolbar(AddProductPage, 1000));
    test('should click on "Save" button', async () => {
      await client.waitForExistAndClick(AddProductPage.save_product_button, 5000);
      await client.waitForExistAndClick(AddProductPage.close_validation_button, 1000);
    });
    test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
    test('should switch to the Preview page in the Front Office', () => client.switchWindow(1));
    this.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
    test('should check the offline warning message', () => client.checkTextValue(productPage.offline_warning_message, "This product is not visible to your customers.", "contain"));
    test('should go back to the Back Office', async () => {
      await client.closeOtherWindow(1);
      await client.switchWindow(0);
    });
    test('should switch the product online then offline', async () => {
      await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
      await client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.');
      await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
      await client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.');
    });
    test('should save then duplicate product with "ALT + SHIFT + D"', async () => {
      await client.keys(["\uE00A", "\uE008", "\u0044"]);
      await client.checkTextValue(AddProductPage.success_panel, 'Product successfully duplicated.');
      await client.pause(3000);
    });
    if (productType === 'virtual') {
      test('should select the "virtual product" type', () => client.waitAndSelectByValue(AddProductPage.product_type, 2));
    }
    test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, 'secondVirtualProduct' + date_time));
    test('should save and open new product form with "ALT+ SHIFT + P"', async () => {
      await client.keys(["\uE00A", "\uE008", "\u0050"]);
      await client.pause(3000);
      await client.checkAttributeValue(AddProductPage.product_name_input, 'value', '');
      await client.keys(["\uE00A", "\uE008", "\u0041"]);
    });
    test('should set the "product name" input', async () => await client.waitAndSetValue(AddProductPage.product_name_input, 'thirdVirtualProduct' + date_time));
    if (productType === 'virtual') {
      test('should select the "virtual product" type', () => client.waitAndSelectByValue(AddProductPage.product_type, 2));
    }
    test('should save the product with "ALT+ SHIFT + S"', async () => {
      await client.keys(["\uE00A", "\uE008", "\u0053"]);
      await client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.', 1000);
      await client.pause(2000);
      await client.keys(["\uE00A", "\uE008", "\u0041"]);
    });
    test('should check the click on tooltips "?" icon for "Quantity" and "type"', async () => {
      await client.waitForExistAndClick(AddProductPage.type_help_box_icon);
      await client.waitForVisible(AddProductPage.help_box.replace('%N', 1));
      await client.waitForExistAndClick(AddProductPage.type_help_box_icon);
      await client.waitForExistAndClick(AddProductPage.quantity_help_box_icon);
      await client.waitForVisible(AddProductPage.help_box.replace('%N', 1));
      await client.waitForExistAndClick(AddProductPage.quantity_help_box_icon);
    });
    test('should check the click on tooltips "?" icon for "Price" then check that the help box of price appear', async () => {
      await client.waitForExistAndClick(AddProductPage.price_help_box_icon);
      await client.waitForVisible(AddProductPage.help_box.replace('%N', 1));
    });

    /** Here according to the test link, when we click otherwise,
     * the opened help box will disappear but here we must click again on the tooltips "?"
     * to disappear the opened box.
     **/
    test('should check the click on tooltips "?" icon for "Price" then check that the help box of price disappear', async () => {
      await client.waitForExistAndClick(AddProductPage.price_help_box_icon, 1000);
      await client.isNotExisting(AddProductPage.help_box.replace('%N', 1), 2000);
    });
    test('should click on "Pricing" tab then check that the tooltip disappear', async () => {
      await client.waitForExistAndClick(AddProductPage.price_help_box_icon);
      await client.waitForExistAndClick(AddProductPage.product_pricing_tab);
      await client.isNotExisting(AddProductPage.help_box.replace('%N', 1), 2000);
    });
  },

  clickOnCoverAndSave(client) {
    test('should click on "Cover image" checkbox', () => client.waitForExistAndClick(AddProductPage.picture_cover_checkbox));
    test('should click on "Save image settings" button', () => client.waitForExistAndClick(AddProductPage.picture_save_image_settings_button));

    /**
     * This error is due to the bug described in this issue
     * https://github.com/PrestaShop/PrestaShop/issues/9631
     **/
    test('should verify the appearance of the green validation (issue #9631)', () => client.checkTextValue(AddProductPage.validation_msg, "Settings updated."));
  },

  checkTinyMceButtons(client, id) {
    test('should check the appearance of "Source" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 1)));
    test('should check the appearance of "Color picker" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 2)));
    test('should check the appearance of "Bold" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 3)));
    test('should check the appearance of "Italic" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 4)));
    test('should check the appearance of "Underline" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 5)));
    test('should check the appearance of "Strikethrough" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 6)));
    test('should check the appearance of "Blockquote" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 7)));
    test('should check the appearance of "Insert/edit link" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 8)));
    test('should check the appearance of "Text format" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 9)));
    test('should check the appearance of "Bullet list" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 10)));
    test('should check the appearance of "Numbered list" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 11)));
    test('should check the appearance of "Table" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 12)));
    test('should check the appearance of "Insert/edit image" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 13)));
    test('should check the appearance of "Insert/edit video" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 14)));
    test('should check the appearance of "Presentation" button', () => client.isExisting(AddProductPage.tiny_mce_buttons.replace('%ID', id + 15)));
  },

  CheckBasicSettingsTab() {
    scenario('Check the image of the product in basic settings tab', client => {
      test('should upload the first product picture', async () => {
        await client.checkIsNotVisible(AddProductPage.prodcut_picture_bloc);
        await client.uploadPicture('image_test.jpg', AddProductPage.picture);
      });
      test('should check that the "Product picture" is well displayed', () => client.isExisting(AddProductPage.picture_background.replace('%POS', 1)));
      test('should click on "First image" of product', async () => {
        await client.waitForExistAndClick(AddProductPage.picture_background.replace('%POS', 1), 4000);
        await client.getAttributeInVar(AddProductPage.picture_background.replace('%POS', 1), 'data-id', 'firstPictureId');
      });
      this.clickOnCoverAndSave(client);
      this.clickOnCoverAndSave(client);
      test('should set the "Legend picture"', () => client.waitAndSetValue(AddProductPage.picture_legend_en_textarea, data.common.first_picture_legend));
      test('should click on "Save image settings" button', () => client.waitForExistAndClick(AddProductPage.picture_save_image_settings_button));
      test('should click on "Close" button', () => client.waitForExistAndClick(AddProductPage.picture_close_button));
      test('should upload the second product picture', async () => {
        await client.checkIsVisible(AddProductPage.prodcut_picture_bloc);
        await client.uploadPicture('2.jpg', AddProductPage.picture)
      });
      test('should change the order of pictures', () => client.dragAndDrop(AddProductPage.picture_element.replace('%ID', 4), AddProductPage.picture_element.replace('%ID', 5)));
      test('should click on "Second image" of product', () => client.waitForExistAndClick(AddProductPage.picture_background.replace('%POS', 1)));
      test('should set the "Legend picture"', () => client.waitAndSetValue(AddProductPage.picture_legend_en_textarea, data.common.second_picture_legend));
      test('should click on "Save image settings" button', () => client.waitForExistAndClick(AddProductPage.picture_save_image_settings_button));
      test('should check then close symfony toolbar', () => client.waitForSymfonyToolbar(AddProductPage, 1000));
      test('should switch the product online', () => {
        return promise
          .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000))
          .then(() => client.waitForExistAndClick(AddProductPage.close_validation_button, 1000));
      });
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the "Product pictures" is well ordered', async () => {
        await client.checkAttributeValue(productPage.product_pictures.replace('%ID', 1).replace('%LEGEND', data.common.second_picture_legend), 'title', data.common.second_picture_legend);
        await client.checkAttributeValue(productPage.product_pictures.replace('%ID', 2).replace('%LEGEND', data.common.first_picture_legend), 'title', data.common.first_picture_legend);
      });
      test('should go back to the Back Office', async () => {
        await client.closeOtherWindow(1);
        await client.switchWindow(0);
      });
      test('should click on "First image" of product', async () => {
        await client.waitForExistAndClick(AddProductPage.picture_background.replace('%POS', 1));
        await client.getAttributeInVar(AddProductPage.picture_background.replace('%POS', 1), 'data-id', 'secondPictureId');
      });
      test('should click on "Zoom" button', () => client.waitForExistAndClick(AddProductPage.picture_zoom_button));
      test('should check that the "Product picture" is well displayed in zoom out', () => client.checkAttributeValue(AddProductPage.zoom_picture_img, 'style', 'max-height', 'contain'));
      test('should click on "Close" button', () => client.waitForExistAndClick(AddProductPage.zoom_picture_close_button, 1000));
      test('should click on "Delete" button', () => client.waitForExistAndClick(AddProductPage.picture_delete_button));
      test('should click on "Yes" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
      test('should check that the "Product picture" is well deleted', () => client.checkAttributeValue(AddProductPage.picture_background.replace('%POS', 1), 'data-id', tab['secondPictureId'], 'notequal', 5000));
      test('should check that the first image is the cover picture of the product', () => client.isExisting(AddProductPage.picture_cover_bloc.replace('%POS', 1)));
    }, 'common_client');
    scenario('Check the "Summary" and the " Description" tab in the basic settings tab', client => {
      test('should check that the "Summary" field is well displayed', () => client.isExisting(AddProductPage.summary_textarea));
      test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.tab_description, 3000));
      test('should check that the "Description" field is well displayed', () => client.isExisting(AddProductPage.description_textarea));
      test('should click on "Summary" tab', () => client.waitForExistAndClick(AddProductPage.tab_summary));
      this.checkTinyMceButtons(client, 11);
      test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.tab_description));
      this.checkTinyMceButtons(client, 51);
      test('should click on "Summary" tab', () => client.waitForExistAndClick(AddProductPage.tab_summary));
      test('should set the "Summary" text', () => client.setEditorText(AddProductPage.summary_textarea, "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. \n Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."));
      test('should click on "save"', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check the appearance of red validation', () => client.checkTextValue(AddProductPage.red_validation_notice, '×\nUnable to update settings.', 'equal', 2000));
      test('should check that the "Error message" is well displayed', () => client.checkTextValue(AddProductPage.tiny_mce_validation_message, 'This value is too long. It should have 800 characters or less.'));
      test('should set the "Summary" text', () => client.setEditorText(AddProductPage.summary_textarea, data.common.summary));
      test('should click on "Description" tab', () => client.scrollWaitForExistAndClick(AddProductPage.tab_description, 50));
      test('should set the "Description" text', () => client.setEditorText(AddProductPage.description_textarea, data.common.description));
      test('should click on "SAVE"', () => client.waitForExistAndClick(AddProductPage.save_product_button, 2000));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.pause(2000);
      });
      test('should check that the "summary" is equal to "' + data.common.summary + '"', () => client.checkTextValue(productPage.product_summary, data.common.summary, 'equal', 1000));
      test('should check that the "description" is equal to "' + data.common.description + '"', () => client.checkTextValue(productPage.product_description, data.common.description));
      test('should select "Français" in language list', () => client.changeLanguage('fr'));
      test('should check that the "summary" is empty', () => client.isNotExisting(productPage.product_summary));
      test('should check that the "description" is empty', () => client.checkTextValue(productPage.product_description, ''));
      test('should go back to the Back Office', async () => {
        await client.closeOtherWindow(1);
        await client.switchWindow(0);
      });
    }, 'common_client');
    scenario('Check features, brand and related product in the basic settings tab', client => {
      this.addProductFeature(client, "Composition", 0, "Cotton");
      this.addProductFeature(client, "Property", 1, '', "Short sleeves", "custom_value");
      test('should click on "Delete" icon of the second feature', () => client.waitForExistAndClick(AddProductPage.delete_feature_icon.replace('%POS', 2)));
      test('should click on "Yes" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
      this.addProductFeature(client, 'Composition', 2, 'Wool');
      test('should click on "SAVE"', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.pause(2000);
      });
      test('should click on "Product details" tab', () => client.waitForExistAndClick(productPage.product_detail_tab));
      test('should verify that "Composition" exist', () => {
        return promise
          .then(() => client.pause(3000))
          .then(() => client.checkTextValue(productPage.product_feature_text.replace('%B', 'last'), 'Composition'));
      });
      test('should go back to the Back Office', async () => {
        await client.closeOtherWindow(1);
        await client.switchWindow(0);
      });
      test('should click on "Add a brand" button', () => client.scrollWaitForExistAndClick(AddProductPage.product_add_brand_btn, 50, 2000));
      test('should select brand', () => {
        return promise
          .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select))
          .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select_option));
      });
      test('should check that we can add only one brand ', () => client.checkIsNotVisible(AddProductPage.product_add_brand_btn));
      test('should click on "Delete" icon of the selected brand', () => client.waitForExistAndClick(AddProductPage.delete_brand_product_button));
      test('should click on "No" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'No')));
      test('should click on "Delete" icon of the selected brand', () => client.waitForExistAndClick(AddProductPage.delete_brand_product_button, 3000));
      test('should click on "yes" modal button', async () => {
        await client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes'));
        await client.waitForVisible(AddProductPage.product_add_brand_btn);
      });
      test('should click on "Add related product" button', () => client.waitForExistAndClick(AddProductPage.add_related_product_btn, 2000));
      for (let j = 0; j < 3; j++) {
        test('should search and add a related product', async () => {
          await client.waitAndSetValue(AddProductPage.search_add_related_product_input, 'mug');
          await client.waitForVisibleAndClick(AddProductPage.related_product_item.replace('%I', (j + 1)));
        });
      }
      test('should click on "Delete" icon of the first related product', () => client.scrollWaitForExistAndClick(AddProductPage.related_product_delete_icon.replace('%I', 1), 50, 2000));
      test('should click on "Yes" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
      test('should click on "Delete" icon of the second related product', () => client.scrollWaitForExistAndClick(AddProductPage.related_product_delete_icon.replace('%I', 2), 50, 2000));
      test('should click on "Yes" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
      test('should click on "Delete" icon of the third related product', () => client.scrollWaitForExistAndClick(AddProductPage.related_product_delete_icon.replace('%I', 1), 50, 2000));
      test('should click on "Yes" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
      test('should click on "Delete" icon of the all related product', () => client.scrollWaitForExistAndClick(AddProductPage.delete_related_product_button, 50, 2000));
      test('should click on "Yes" modal button', async () => {
        await client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes'));
        await client.waitForVisible(AddProductPage.add_related_product_btn);
      });
    }, 'product/product');
  },
  addSpecificPrice(client, price = 0) {
    test('should go back to the Back Office', () => client.switchWindow(0));
    if (price !== 0) {
      test('should set the "Price (tax incl.)" input', () => client.waitAndSetValue(AddProductPage.product_pricing_ttc_input, price));
    }
    test('should click on "Delete" icon from the specific price table', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_delete_button, 50, 2000));
    test('should click on "Yes" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
    test('should click on "Add a specific price" button', () => client.scrollWaitForExistAndClick(AddProductPage.pricing_add_specific_price_button, 100, 2000));
  },

  CheckBasicSettingsPriceCategory(client) {
    test('should set the "Tax excluded" price input', () => client.setPrice(AddProductPage.priceTE_shortcut, '10'));
    test('should check that the "Tax included" price is equal to "12"', () => client.checkAttributeValue(AddProductPage.priceTTC_shortcut, 'value', '12'));
    test('should choose "5.5%" from the "Tax rule" list', async () => {
      await client.waitForExistAndClick(AddProductPage.tax_rule);
      await client.waitForExistAndClick(AddProductPage.tax_option.replace('%V', '5.5%'), 1000);
    });
    test('should check that the "Price (tax included)" is equal to "10.55"', () => client.checkAttributeValue(AddProductPage.priceTTC_shortcut, 'value', '10.55'));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
    test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
    test('should go to the Front Office', async () => {
      await client.switchWindow(1);
    });
    this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
    test('should check that the "Product price" is equal to "€10.55"', () => client.checkTextValue(productPage.product_price, '€10.55', 'equal', 4000));
    test('should go back to the Back Office', async () => {
      await client.closeOtherWindow(1);
      await client.switchWindow(0);
    });
    test('should choose "No tax" from the "Tax rule" list', async () => {
      await client.waitForExistAndClick(AddProductPage.tax_rule);
      await client.waitForExistAndClick(AddProductPage.tax_option.replace('%V', 'No tax'), 1000);
    });
    test('should check that the "Tax included" price is equal to "10"', () => client.checkAttributeValue(AddProductPage.priceTTC_shortcut, 'value', '10'));
    test('should choose "5.5%" from the "Tax rule" list', async () => {
      await client.waitForExistAndClick(AddProductPage.tax_rule);
      await client.waitForExistAndClick(AddProductPage.tax_option.replace('%V', '20%'), 1000);
    });
    test('should start to search a category', () => client.waitAndSetValue(AddProductPage.search_categories, 'Accessories'));
    test('should get the number of the displayed categories', () => client.getOptionNumber(AddProductPage.id_list_categories.split('#')[1], 'li', 'categoriesNumber', 2000));
    test('check if the displayed categories contains "Accessories"', async () => {
      for (let i = 1; i <= global.tab['categoriesNumber']; i++)
        await client.checkTextValue(AddProductPage.list_name_category.replace('%P', i), 'Accessories', 'contain', 2000);
    });
    test('should search "Clothes" category', () => client.waitAndSetValue(AddProductPage.search_categories, 'Clothes'));
    test('should associate "Clothes" category to the created product', () => client.waitForExistAndClick(AddProductPage.list_categories, 1000));
    test('should check that the tag of "Clothes" category is well displayed', () => client.checkTextValue(AddProductPage.tag_category.replace('%NAME', 'Clothes'), 'Clothes'));
    test('should check that "Clothes" category is checked', () => client.checkCheckboxStatus(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID']), true));
    test('should uncheck "Clothes" category', () => client.waitForExistAndClick(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID']), 1000));
    test('should check "Clothes" category', () => client.waitForExistAndClick(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID']), 1000));
    test('should check that the tag of "Clothes" category is well displayed', () => client.checkTextValue(AddProductPage.tag_category.replace('%NAME', 'Clothes'), 'Clothes'));
    test('should check that "Clothes" category is checked', () => client.checkCheckboxStatus(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID']), true));
    test('should delete the tag of "Clothes" category', () => client.waitForExistAndClick(AddProductPage.delete_tag_category.replace('%ID', global.tab['categeoryID'])));
    test('should check that "Clothes" category is unchecked', () => client.checkCheckboxStatus(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID']), false));
    test('should check "Clothes" category', () => client.waitForExistAndClick(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID']), 1000));
    test('should uncheck "Clothes" category', () => client.waitForExistAndClick(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID']), 1000));
    test('should check that the tag of "Clothes" category is not existing', () => client.isNotExisting(AddProductPage.delete_tag_category.replace("%ID", global.tab['categeoryID'])));
    test('should click on "Collapse" button', () => client.scrollWaitForExistAndClick(AddProductPage.category_collapse_button, 150, 1000));
    test('should check that the tree is reduced', () => client.checkIsNotVisible(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID'])));
    test('should click on "Expand" button', () => client.scrollWaitForExistAndClick(AddProductPage.category_expand_button, 150, 1000));
    test('should check that the tree is expanded', () => client.checkIsVisible(AddProductPage.category_checkbox_input.replace('%ID', global.tab['categeoryID'])));
    test('should click on "Clothes" radio button', () => client.waitForExistAndClick(AddProductPage.category_radio_button.replace('%VALUE', 3), 1000));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
    test('should check that "Clothes" is the default category', () => client.checkCheckboxStatus(AddProductPage.main_category_radio_button.replace('%VALUE', 3), true));
    test('should check that the tag display the full path of the category', () => client.checkAttributeValue(AddProductPage.tag_category.replace('%NAME', 'Clothes'), 'title', 'Home > Clothes'));
    test('should click on "Create a category" button', () => client.scrollWaitForExistAndClick(AddProductPage.product_create_category_btn));
    test('should check the existence of "New category name" input', () => client.isExisting(AddProductPage.product_category_name_input, 1000));
    test('should check the existence of Parent of the category" select', () => client.isExisting(AddProductPage.parent_category_select));
    test('should set the "New category name" input', () => client.waitAndSetValue(AddProductPage.product_category_name_input, data.virtual.new_category_name + date_time));
    test('should choose "Clothes" as Parent of the category from the dropdown list', async () => {
      await client.scrollWaitForExistAndClick(AddProductPage.parent_category_select);
      await client.waitForVisibleAndClick(AddProductPage.parent_category_option.replace('%N', 'Clothes'));
    });
    test('should click on "Create" button', () => client.scrollWaitForExistAndClick(AddProductPage.category_create_btn));
    test('should get the "ID" of the created category', async () => {
      await client.pause(2000);
      await client.getAttributeInVar(AddProductPage.category_radio.replace("%S", data.virtual.new_category_name + date_time), 'value', 'IDcreatedCategory');
    });
    test('should define the new category created "main category"', () => client.scrollWaitForExistAndClick(AddProductPage.category_radio_button.replace('%VALUE', global.tab['IDcreatedCategory'])));
  },

  CheckPricingTab(client, customerData, addressData, productData) {
    test('should click on "Pricing" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_pricing_tab, 50, 2000));
    test('should set the "Tax rule" to "5.5%"', async () => {
      await client.waitForExistAndClick(AddProductPage.pricing_tax_rule_select);
      await client.waitForExistAndClick(AddProductPage.pricing_tax_rule_option.replace('%T', '20%'));
    });
    test('should set the "Price (tax incl.)" input', () => client.waitAndSetValue(AddProductPage.product_pricing_ttc_input, '8.5'));
    test('should check that the "Price (tax excl.)" is equal to "7.083333"', () => client.checkAttributeValue(AddProductPage.product_pricing_ht_input, 'value', '7.083333'));
    test('should check that the "Price (tax incl.)" is equal to "€8.50" in the banner "Final retail price"', () => client.checkTextValue(AddProductPage.banner_tax_included_span, '€8.50', 'equal', 1000));
    test('should check that the "Price (tax excl.)" is equal to "€7.08" in the banner "Final retail price"', () => client.checkTextValue(AddProductPage.banner_tax_excluded_span, '€7.08', 'equal', 1000));
    test('should set the "Price (tax incl.)" input', () => client.waitAndSetValue(AddProductPage.product_pricing_ttc_input, '9,5'));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 3000));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should check that the "Price (tax incl.)" is equal to "9.5"', () => client.checkAttributeValue(AddProductPage.product_pricing_ttc_input, 'value', '9.5'));
    test('should check that the "Price (tax excl.)" is equal to "7.916667"', () => client.checkAttributeValue(AddProductPage.product_pricing_ht_input, 'value', '7.916667'));
    test('should click on "Basic settings" tab', async () => {
      await client.waitForExistAndClick(AddProductPage.basic_settings_tab);
      await client.refresh();
    });
    test('should check that the "Tax included" price is equal to "9.5"', () => client.checkAttributeValue(AddProductPage.priceTTC_shortcut, 'value', '9.5'));
    test('should check that the "Price (tax included)" is equal to "7.916667"', () => client.checkAttributeValue(AddProductPage.priceTE_shortcut, 'value', '7.916667'));
    test('should click on "Pricing" tab', () => client.waitForExistAndClick(AddProductPage.product_pricing_tab));
    test('should set the "Price per unit (tax excl.)" input', () => client.waitAndSetValue(AddProductPage.pack_unit_price, '2'));
    test('should set the "Unit" input', () => client.waitAndSetValue(AddProductPage.product_unit_input, 'kilo'));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 3000));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
    test('should go to the Front Office', async () => {
      await client.switchWindow(1);
      await client.pause(2000);
      await client.changeLanguage();
    });
    test('should check that the "Product price" is equal to "€9.50"', () => client.checkTextValue(productPage.product_price, '€9.50', 'equal', 3000));
    test('should check that the "Unit price" is equal to "(€2.40 kilo)"', () => client.checkTextValue(productPage.unit_price_text, '(€2.40 kilo)', 'equal', 3000));
    test('should go back to the Back Office', async () => {
      await client.closeOtherWindow(1);
      await client.switchWindow(0);
    });
    test('should set the "Tax rule" to "5.5%"', async () => {
      await client.waitForExistAndClick(AddProductPage.pricing_tax_rule_select);
      await client.waitForExistAndClick(AddProductPage.pricing_tax_rule_option.replace('%T', '5.5%'));
    });
    test('should check that the "Price (tax incl.)" is equal to "8.352084"', () => client.checkAttributeValue(AddProductPage.product_pricing_ttc_input, 'value', '8.352084'));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 3000));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
    test('should go to the Front Office', async () => {
      await client.switchWindow(1);
      await client.pause(2000);
      await client.changeLanguage();
    });
    test('should check that the "Product price" is equal to "€8.35"', () => client.checkTextValue(productPage.product_price, '€8.35', 'equal', 3000));
    test('should go back to the Back Office', async () => {
      await client.switchWindow(0);
    });
    test('should set the "Tax rule" to "20%"', async () => {
      await client.waitForExistAndClick(AddProductPage.pricing_tax_rule_select, 1000);
      await client.waitForExistAndClick(AddProductPage.pricing_tax_rule_option.replace('%T', '20%'), 1000);
    });
    test('should check that the "Price (tax incl.)" is equal to "9.5"', () => client.checkAttributeValue(AddProductPage.product_pricing_ttc_input, 'value', '9.5'));
    test('should click on "Display the "On sale!" flag on the product page, and on product listings." checkbox', () => client.waitForExistAndClick(AddProductPage.on_sale_checkbox));
    test('should set the "Cost price" input', () => client.waitAndSetValue(AddProductPage.pricing_wholesale, '6.123456'));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should check that the success alert message is well displayed', async () => {
      await client.waitForExistAndClick(AddProductPage.close_validation_button);
      await client.refresh();
    });
    test('should check that the "Cost price" is equal to "6.123456"', () => client.checkAttributeValue(AddProductPage.pricing_wholesale, 'value', '6.123456'));
    test('should go to the Front Office', async () => {
      await client.switchWindow(1);
      await client.refresh();
    });
    test('should check that the product on sale flag does exist', () => client.isExisting(productPage.product_on_sale_flag));
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should click on "Add a specific price" button', () => client.scrollWaitForExistAndClick(AddProductPage.pricing_add_specific_price_button, 50, 2000));
    test('should check that the "All currencies" select does exist', () => client.isExisting(AddProductPage.specific_price_for_currency_select, 2000));
    test('should check that the "All countries" select does exist', () => client.isExisting(AddProductPage.specific_price_for_country_select));
    test('should check that the "All groups" select does exist', () => client.isExisting(AddProductPage.specific_price_for_group_select));
    test('should check that the "Customer" input does exist', () => client.isExisting(AddProductPage.specific_price_customer_input));
    test('should check that the "Available from" calendar input does exist', () => client.isExisting(AddProductPage.specific_price_available_from_input));
    test('should check that the "Available to" calendar input does exist', () => client.isExisting(AddProductPage.specific_price_to_input));
    test('should check that the "Starting at" input does exist', () => client.isExisting(AddProductPage.specific_price_starting_at_input));
    test('should check that the "Product price (tax excl.)" input does exist', () => client.isExisting(AddProductPage.specific_product_price_input));
    test('should check that the "Leave initial price" checkbox does exist', () => client.isExisting(AddProductPage.leave_initial_price_checkbox));
    test('should check that the "Apply a discount of" input does exist', () => client.isExisting(AddProductPage.specific_price_discount_input));
    test('should check that the "Discount type" select does exist', () => client.isExisting(AddProductPage.specific_price_reduction_type_select));
    test('should check that the "Tax" select does exist', () => client.isExisting(AddProductPage.specific_price_reduction_tax_select));
    test('should click on "Cancel" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_cancel_button, 150, 3000));
    scenario('Specific price: Currency', client => {
      test('should click on "Add a specific price" button', () => client.waitForExistAndClick(AddProductPage.pricing_add_specific_price_button, 3000));
      test('should select "Euro" on "Currency" select', () => client.selectByVisibleText(AddProductPage.specific_price_for_currency_select,'Euro'));
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '10', 2000));
      test('should choose the "Percentage" from the specific price type', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, 'percentage'));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name, 2000));
      test('should check that the "Discount" is equal to "SAVE 10%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE 10%'));
      test('should click on "Currency" select and choose "DZD" from the dropdown list', async () => {
        await client.waitForExistAndClick(AccessPageFO.currency_list_select);
        await client.waitForVisibleAndClick(AccessPageFO.currency_list_element.replace('%NAME', 'DZD'));
      });
      test('should check that the product price is equal to "DZD9.5"', () => client.checkTextValue(productPage.product_price, 'DZD9.50'));
      test('should verify that the discount does not exist', () => client.isNotExisting(CheckoutOrderPage.product_discount_details));
    }, 'common_client');
    scenario('Specific price: Country', client => {
      this.addSpecificPrice(client);
      test('should click on "Country" select and choose "Algeria" from the dropdown list', () => client.selectByVisibleText(AddProductPage.specific_price_for_country_select, 'Algeria'));
      test('should set the "Apply a discount of" input', async () => {
        await client.scrollTo(AddProductPage.specific_price_discount_input,50);
        await client.waitAndSetValue(AddProductPage.specific_price_discount_input, '5')
      });
      test('should choose the "Percentage" from the specific price type', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, 'percentage'));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should click on "Currency" select and choose "EUR €" from the dropdown list', async () => {
        await client.waitForExistAndClick(AccessPageFO.currency_list_select);
        await client.waitForVisibleAndClick(AccessPageFO.currency_list_element.replace('%NAME', 'EUR'));
      });
      test('should click on the "Sign in" link', async () => {
        await client.waitForExistAndClick(AccessPageFO.sign_in_button, 1000);
        await client.waitForExistAndClick(AccessPageFO.sign_in_button);
      });
      test('should click on "No account? Create one here" link', () => client.waitForExistAndClick(accountPage.create_button, 1000));
      test('should choose a "Social title" option', () => client.waitForExistAndClick(accountPage.gender_radio_button));
      test('should set the "First name" input', () => client.waitAndSetValue(accountPage.firstname_input, customerData.firstname));
      test('should set the "Last name" input', () => client.waitAndSetValue(accountPage.lastname_input, customerData.lastname));
      test('should set the "Email" input', () => client.waitAndSetValue(accountPage.email_input, date_time + customerData.email));
      test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_input, customerData.password));
      test('should click on "Save" button', () => client.waitForExistAndClick(accountPage.save_account_button));
      test('should click on "User name" link', () => client.waitForExistAndClick(accountPage.account_link));
      test('should click on "ADD FIRST ADDRESS" button', () => client.waitForExistAndClick(accountPage.add_first_address));
      test('should set the "Address" input', () => client.waitAndSetValue(accountPage.adr_address, addressData.address));
      test('should set the "Zip/Postal Code" input', () => client.waitAndSetValue(accountPage.adr_postcode, addressData.postalCode));
      test('should set the "City" input', () => client.waitAndSetValue(accountPage.adr_city, addressData.city));
      test('should choose a "Country" from the dropdown list', () => client.waitAndSelectByVisibleText(accountPage.address_country_list, 'France'));
      test('should click on "SAVE" button', () => client.waitForExistAndClick(accountPage.adr_save));
      test('should check that the success alert message is well displayed', () => client.checkTextValue(accountPage.save_notification, 'Address successfully added!'));
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name, 2000));
      test('should check that the product price is equal to "€9.50"', () => client.checkTextValue(productPage.product_price, '€9.50', 'equal', 2000));
      test('should check that the "Discount" does not exist', () => client.isNotExisting(CheckoutOrderPage.product_discount_details));
    }, 'common_client');
    scenario('Specific price: Group', client => {
      this.addSpecificPrice(client);
      test('should select "Customer" from the dropdown list', () => client.selectByVisibleText(AddProductPage.specific_price_for_group_select,'Customer'));
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '25'));
      test('should choose the "Percentage" from the specific price type', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, 'percentage'));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the "Discount" is equal to "SAVE 25%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE 25%'));
      test('should click on "Sign out" button', () => client.waitForExistAndClick(AccessPageFO.sign_out_button));
      test('should check that the product price is equal to "€9.50"', () => client.checkTextValue(productPage.product_price, '€9.50'));
      test('should verify that the discount does not exist', () => client.isNotExisting(CheckoutOrderPage.product_discount_details));
    }, 'common_client');
    scenario('Specific price: Customer', client => {
      this.addSpecificPrice(client);
      test('should set the "Customer" input', async () => {
        await client.waitAndSetValue(AddProductPage.specific_price_customer_input, 'pub@prestashop.com', 2000);
        await client.waitForVisibleAndClick(AddProductPage.specific_price_customer_option);
      });
      test('should click on "Leave initial price" checkbox', () => client.scrollWaitForExistAndClick(AddProductPage.leave_initial_price_checkbox, 50));
      test('should set the "Product price (tax excl.)" input', () => client.waitAndSetValue(AddProductPage.specific_product_price_input, '6.5', 2000));
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '10'));
      test('should choose the "Percentage" from the specific price type', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, 'percentage'));
      test('should click on "Apply" button', () => client.waitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the product price is equal to "€9.50"', () => client.checkTextValue(productPage.product_price, '€9.50', 'equal', 2000));
      test('should verify that the discount does not exist', () => client.isNotExisting(CheckoutOrderPage.product_discount_details));
      test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check that the product price is equal to "€7.02"', () => client.checkTextValue(productPage.product_price, '€7.02'));
      test('should verify that the discount is equal to "SAVE 10%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE 10%'));
      test('should check that the connected "Customer" is equal to "John DOE"', () => client.checkTextValue(accountPage.account_link, 'John DOE'));
      test('should click on "Sign out" button', () => client.waitForExistAndClick(AccessPageFO.sign_out_button));
    }, 'common_client');
    scenario('Specific price: Starting at a specific quantity', client => {
      this.addSpecificPrice(client);
      test('should set the "Starting at" input', () => client.waitAndSetValue(AddProductPage.specific_price_starting_at_input, '3', 2000));
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '5'));
      test('should choose the "Percentage" from the specific price type', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, 'percentage'));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the "Quantity" is equal to "3"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 1), '3'));
      test('should check that the "Discount" is equal to "5%"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 2), '5%'));
      test('should check that the "You Save" is equal to "Up to €1.43"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 3), 'Up to €1.43'));
      test('should set the "Quantity" input', async () => {
        await client.waitAndSetValue(productPage.first_product_quantity, '3', 2000);
        await client.pause(1000);
      });
      test('should check that the product price is equal to "€9.03 "', () => client.checkTextValue(productPage.product_price, '€9.03', 'equal', 4000));
      test('should verify that the discount is equal to "SAVE 5%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE 5%'));
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should check that the product on sale flag does exist', () => client.isExisting(productPage.product_on_sale_flag));
      test('should click on "Quick view" button', async () => {
        await client.moveToObject(SearchProductPage.product_result_name);
        await client.waitForExistAndClick(SearchProductPage.quick_view_first_product, 2000);
        await client.pause(2000);
      });
      test('should set the "Quantity" input', async () => {
        await client.waitAndSetValue(productPage.first_product_quantity, '3', 2000);
        await client.pause(1000);
      });
      test('should check that the product price is equal to "€9.03"', () => client.checkTextValue(productPage.quick_view_product_price, '€9.03', 'equal', 4000));
      test('should verify that the discount is equal to "SAVE 5%"', () => client.checkTextValue(productPage.quick_view_product_discount, 'SAVE 5%'));
    }, 'common_client');
    scenario('Specific price: Tax excluded', client => {
      this.addSpecificPrice(client);
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '3', 2000));
      test('should choose the "Tax excluded" from the specific price tax', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_tax_select, '0'));
      test('should click on "Apply" button', () => client.waitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check that the product price is equal to "€5.90"', () => client.checkTextValue(productPage.product_price, '€5.90', 'equal', 3000));
      test('should verify that the discount is equal to "SAVE €3.60"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €3.60'));
    }, 'common_client');
    scenario('Specific price: Tax included', client => {
      this.addSpecificPrice(client);
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '3', 2000));
      test('should choose the "Tax included" from the specific price tax', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_tax_select, '1'));
      test('should click on "Apply" button', () => client.waitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check that the product price is equal to "€6.50"', () => client.checkTextValue(productPage.product_price, '€6.50', 'equal', 3000));
      test('should verify that the discount is equal to "SAVE €3.00"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €3.00'));
    }, 'common_client');
    scenario('Specific price: Date', client => {
      this.addSpecificPrice(client);
      test('should set the "Date available from" input', async () => await client.setInputValue(AddProductPage.specific_price_available_from_input, common.getCustomDate(-1),true,2000));
      test('should set the "Date to" input', async () => await client.setInputValue(AddProductPage.specific_price_to_input, common.getCustomDate(1),true));
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '10'));
      test('should choose the "Percentage" from the specific price type', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, 'percentage'));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the product price is equal to "€8.55"', () => client.checkTextValue(productPage.product_price, '€8.55', 'equal', 2000));
      test('should verify that the discount is equal to "SAVE 10%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE 10%'));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should click on "Delete" icon from the specific price table', () => client.waitForExistAndClick(AddProductPage.specific_price_delete_button, 2000));
      test('should click on "Yes" modal button', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')));
      test('should click on "Add a specific price" button', () => client.scrollWaitForExistAndClick(AddProductPage.pricing_add_specific_price_button, 50, 2000));
      test('should set the "Date available from" input', () => client.waitAndSetValue(AddProductPage.specific_price_available_from_input, common.getCustomDate(-2), 2000));
      test('should set the "Date to" input', () => client.waitAndSetValue(AddProductPage.specific_price_to_input, common.getCustomDate(-1)));
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '10'));
      test('should choose the "Percentage" from the specific price type', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_type_select, 'percentage'));
      test('should click on "Apply" button', () => client.waitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should verify that the discount does not exist', () => client.isNotExisting(CheckoutOrderPage.product_discount_details, 7000));
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
    scenario('Specific price: Starting at', client => {
      this.addSpecificPrice(client);
      test('should set the "Starting at" input', () => client.waitAndSetValue(AddProductPage.specific_price_starting_at_input, '3', 2000));
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '5'));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the "Quantity" is equal to "3"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 1), '3'));
      test('should check that the "Discount" is equal to "€5.00"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 2), '€5.00'));
      test('should check that the "You Save" is equal to "Up to €15.00"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 3), 'Up to €15.00'));
      test('should set the "Quantity" input', async () => {
        await client.waitAndSetValue(productPage.first_product_quantity, '3', 2000);
        await client.pause(1000);
      });
      test('should check that the product price is equal to "€4.50"', () => client.checkTextValue(productPage.product_price, '€4.50', 'equal', 4000));
      test('should verify that the discount is equal to "SAVE €5.00"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €5.00'));
    }, 'common_client');
    scenario('Specific price: Change price and choose the tax excluded', client => {
      this.addSpecificPrice(client, '10');
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '5', 1000));
      test('should choose the "Tax excluded" from the specific price tax', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_tax_select, '0'));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should check that the product price is equal to "€4.00"', () => client.checkTextValue(productPage.product_price, '€4.00', 'equal', 3000));
      test('should verify that the discount is equal to "SAVE €6.00"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €6.00'));
    }, 'common_client');
    scenario('Specific price: Change price and choose the tax included', client => {
      this.addSpecificPrice(client, '12');
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '5', 1000));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should check that the product price is equal to "€7.00"', () => client.checkTextValue(productPage.product_price, '€7.00', 'equal', 3000));
      test('should verify that the discount is equal to "SAVE €5.00"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €5.00'));
    }, 'common_client');
    scenario('Specific price: Change price and quantity and choose the tax included', client => {
      this.addSpecificPrice(client, '10');
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '3', 1000));
      test('should set the "Starting at" input', () => client.waitAndSetValue(AddProductPage.specific_price_starting_at_input, '2', 2000));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should check that the "Quantity" is equal to "2"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 1), '2'));
      test('should check that the "Discount" is equal to "€3.00"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 2), '€3.00'));
      test('should check that the "You Save" is equal to "Up to €6.00"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 3), 'Up to €6.00'));
      test('should set the "Quantity" input', async () => {
        await client.waitAndSetValue(productPage.first_product_quantity, '2', 2000);
        await client.pause(1000);
      });
      test('should check that the product price is equal to "€7.00"', () => client.checkTextValue(productPage.product_price, '€7.00', 'equal', 4000));
      test('should verify that the discount is equal to "SAVE €3.00"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €3.00'));
    }, 'common_client');
    scenario('Specific price: Change price and quantity and choose the tax excluded', client => {
      this.addSpecificPrice(client, '9');
      test('should set the "Apply a discount of" input', () => client.waitAndSetValue(AddProductPage.specific_price_discount_input, '3', 1000));
      test('should choose the "Tax excluded" from the specific price tax', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_tax_select, '0'));
      test('should set the "Starting at" input', () => client.waitAndSetValue(AddProductPage.specific_price_starting_at_input, '2', 2000));
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should check that the "Quantity" is equal to "2"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 1), '2'));
      test('should check that the "Discount" is equal to "€3.60"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 2), '€3.60'));
      test('should check that the "You Save" is equal to "Up to €7.20"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 3), 'Up to €7.20'));
      test('should set the "Quantity" input', async () => {
        await client.waitAndSetValue(productPage.first_product_quantity, '2', 2000);
        await client.pause(1000);
      });
      test('should check that the product price is equal to "€5.40"', () => client.checkTextValue(productPage.product_price, '€5.40', 'equal', 4000));
      test('should verify that the discount is equal to "SAVE €3.60"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €3.60'));
    }, 'common_client');
    scenario('Check the priority management', client => {
      this.addSpecificPrice(client, '9.5');
      test('should choose "Visitor" from the dropdown list "Group"', () => client.selectByVisibleText(AddProductPage.specific_price_for_group_select, 'Visitor'));
      test('should set the "Apply a discount of" input', async () => {
        await client.waitForVisible(AddProductPage.specific_price_discount_input);
        await client.waitAndSetValue(AddProductPage.specific_price_discount_input, '5');
      });
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button,100));
      test('should click on "Save" button', () => client.scrollWaitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should click on "Add a specific price" button', () => client.scrollWaitForExistAndClick(AddProductPage.pricing_add_specific_price_button, 50, 2000));
      test('should click on "Currency" select', () => client.selectByVisibleText(AddProductPage.specific_price_for_currency_select,'Euro'));
      test('should set the "Apply a discount of" input', async () => {
        await client.waitForVisible(AddProductPage.specific_price_discount_input,2000);
        await client.waitAndSetValue(AddProductPage.specific_price_discount_input, '3');
      });
      test('should click on "Apply" button', () => client.scrollWaitForExistAndClick(AddProductPage.specific_price_save_button,100));
      test('should click on "Save" button', () => client.scrollWaitForExistAndClick(AddProductPage.save_product_button,100));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should check that the product price is equal to "€6.50"', () => client.checkTextValue(productPage.product_price, '€6.50', 'equal', 5000));
      test('should verify that the discount is equal to "SAVE €3.00"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €3.00'));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should select the "Priority management"', () => client.selectPricingPriorities('id_shop', 'id_group', 'id_currency', 'id_country'));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should check that the product price is equal to "€4.50"', () => client.checkTextValue(productPage.product_price, '€4.50', 'equal', 4000));
      test('should verify that the discount is equal to "SAVE €5.00"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE €5.00'));
      test('should go back to the Back Office', async () => {
        await client.closeOtherWindow(1);
        await client.switchWindow(0);
      });
      test('should click on "Apply to all products" checkbox', () => client.waitForExistAndClick(AddProductPage.apply_all_product_checkbox));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for the "Customizable mug" product', () => client.searchProductByName('Customizable mug'));
      test('should click on the product name', () => client.waitForExistAndClick(AddProductPage.catalog_product_name));
      test('should verify that the first priority is for "Shop"', () => client.waitForExist(AddProductPage.priority_selected_option.replace('%NB', 0).replace('%P', 'id_shop')));
      test('should verify that the second priority is for "Group"', () => client.waitForExist(AddProductPage.priority_selected_option.replace('%NB', 1).replace('%P', 'id_group')));
      test('should verify that the third priority is for "Currency"', () => client.waitForExist(AddProductPage.priority_selected_option.replace('%NB', 2).replace('%P', 'id_currency')));
      test('should verify that the fourth priority is for "Country"', () => client.waitForExist(AddProductPage.priority_selected_option.replace('%NB', 3).replace('%P', 'id_country')));
      test('should click on "Pricing" tab', () => client.waitForExistAndClick(AddProductPage.product_pricing_tab, 2000));
      test('should select the default "Priority management"', () => client.selectPricingPriorities());
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/check_product');
  },

  CheckSeoTab(client, productData) {
    scenario('Check meta title, description, preview, friendly url and redirection in SEO tab', client => {
      test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.tab_description));
      test('should set the "Description" textarea', () => client.setEditorText(AddProductPage.description_textarea, 'This is the description'));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should switch to the Preview page in the Front Office', () => client.switchWindow(1));
      this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
      test('should check the meta title', () => client.checkTitleValue(productPage.page_meta_title, productData.name + date_time, 1000));
      test('should check the meta description', () => client.checkAttributeValue(productPage.page_meta_description, 'content', data.common.summary, 'equal', 2000));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should click on "SEO" tab', () => client.waitForExistAndClick(AddProductPage.product_SEO_tab));
      test('should check that the "Meta title" input is empty', () => client.checkAttributeValue(AddProductPage.SEO_meta_title, 'value', '', 'equal', 1000));
      test('should check that the "Meta description" input is empty', () => client.checkAttributeValue(AddProductPage.SEO_meta_description, 'value', '', 'equal', 1000));
      test('should check the preview contains the description of the product', () => client.checkTitleValue(AddProductPage.preview_bloc.replace('%C', 'serp-description'), 'This is the description', 1000));
      test('should check the preview contains the name of the product', () => client.checkTitleValue(AddProductPage.preview_bloc.replace('%C', 'serp-title'), productData.name + date_time, 1000));
      test('should set the "Meta title"', () => client.waitAndSetValue(AddProductPage.SEO_meta_title, 'metatitle'));
      test('should set the "Meta description"', () => client.waitAndSetValue(AddProductPage.SEO_meta_description, 'metadescription'));
      test('should check that the preview was updated', () => client.checkTitleValue(AddProductPage.preview_bloc.replace('%C', 'serp-description'), 'metadescription', 1000));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      test('should check the meta title', () => client.checkTitleValue(productPage.page_meta_title, 'metatitle', 1000));
      test('should check the meta description', () => client.checkAttributeValue(productPage.page_meta_description, 'content', 'metadescription', 'equal', 1000));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should check the preview contains the meta title of the product', () => client.checkTitleValue(AddProductPage.preview_bloc.replace('%C', 'serp-title'), 'metatitle', 1000));
      test('should verify that "Friendly URL" contains by default the product name', () => client.checkAttributeValue(AddProductPage.SEO_friendly_url, 'value', (productData.name).toLowerCase() + date_time));
      test('should set the "Friendly URL" input', () => client.waitAndSetValue(AddProductPage.SEO_friendly_url, 'friendlyUrl'));
      test('should click on "Reset URL" button', () => client.scrollWaitForExistAndClick(AddProductPage.reset_url_button, 50));
      test('should check the "Friendly URL" input', () => client.checkAttributeValue(AddProductPage.SEO_friendly_url, 'value', (productData.name).toLowerCase() + date_time));
      test('should set the "Friendly URL"', () => client.waitAndSetValue(AddProductPage.SEO_friendly_url, 'friendlyUrl'));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', async () => {
        await client.switchWindow(1);
        await client.refresh();
      });
      this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
      test('should check the friendly url', () => client.checkAttributeValue(productPage.page_link, 'href', 'friendlyUrl', 'contain', 1000));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, productData.name + 'edit' + date_time));
      test('should check that the preview was not changed', () => client.checkTitleValue(AddProductPage.preview_bloc.replace('%C', 'serp-title'), 'metatitle', 1000));
      test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, productData.name + date_time));
      test('should switch the product offline and verify the appearance of the green validation', async () => {
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
        await client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.', 3000);
      });
      test('should choose "No redirection (404)" from redirection list', () => client.waitAndSelectByValue(AddProductPage.redirection_select, "404"));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the product is not displayed', () => client.checkTextValue(productPage.alert_bloc, 'This product is no longer available.', 'contain', 1000));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should choose "Temporary redirection to a product (302)" from redirection list', () => client.waitAndSelectByValue(AddProductPage.redirection_select, "302-product"));
      test('should search and choose a target product', async () => {
        await client.waitAndSetValue(AddProductPage.target_product_input, 'demo_14');
        await client.waitForVisibleAndClick(AddProductPage.target_product.replace('%I', 1));
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should check that the product "CUSTOMIZABLE MUG "is well displayed', () => client.checkTextValue(productPage.product_name, 'CUSTOMIZABLE MUG', 'equal', 2000));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should switch the product online and verify the appearance of the green validation', async () => {
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
        await client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.', 3000);
      });
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should search for the product "' + productData.name + date_time + '"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should switch the product offline and verify the appearance of the green validation', async () => {
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
      });
      test('should choose "Temporary redirection to a category (302)" from redirection list', () => client.waitAndSelectByValue(AddProductPage.redirection_select, "302-category"));
      test('should search and choose a target category', async () => {
        await client.waitAndSetValue(AddProductPage.target_product_input, 'Accessories');
        await client.waitForVisibleAndClick(AddProductPage.target_product.replace('%I', 1));
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1, 2000));
      test('should check that the category "ACCESSORIES "is well displayed', () => client.checkTextValue(CategoryPageFO.category_title, 'ACCESSORIES', 'equal', 2000));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should switch the product online and verify the appearance of the green validation', async () => {
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
      });
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should search for the product "' + productData.name + date_time + '"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should switch the product offline and verify the appearance of the green validation', async () => {
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
      });
      test('should choose "Permanent redirection to a category (301)" from redirection list', () => client.waitAndSelectByValue(AddProductPage.redirection_select, "301-category"));
      test('should search and choose a target category', async () => {
        await client.waitAndSetValue(AddProductPage.target_product_input, 'Clothes');
        await client.waitForVisibleAndClick(AddProductPage.target_product.replace('%I', 1));
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1, 2000));
      test('should check that the category "CLOTHES "is well displayed', () => client.checkTextValue(CategoryPageFO.category_title, 'CLOTHES', 'equal', 2000));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should switch the product online and verify the appearance of the green validation', async () => {
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
        await client.pause(2000);
      });
    }, 'product/product', true);
    //Here we should close the navigator then open it again to not have problem of navigator cache
    scenario('Check redirection SEO tab', client => {
      test('should go to the Front Office', async () => {
        await client.open();
        await client.signInBO(AccessPageBO);
        await client.waitForExistAndClick(AccessPageBO.shopname);
        await client.switchWindow(1);
      });
      test('should search for the product "' + productData.name + date_time + '"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for the "' + productData.name + date_time + '" product', () => client.searchProductByName(productData.name + date_time));
      test('should click on the product name', () => client.waitForExistAndClick(AddProductPage.catalog_product_name));
      test('should click on "SEO" tab', () => client.waitForExistAndClick(AddProductPage.product_SEO_tab));

      test('should switch the product offline and verify the appearance of the green validation', async () => {
        await client.waitForSymfonyToolbar(AddProductPage, 3000);
        await client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000);
      });
      test('should choose "Permanent redirection to a product (301)', () => client.waitAndSelectByValue(AddProductPage.redirection_select, "301-product"));
      test('should search and choose a target product', async () => {
        await client.waitAndSetValue(AddProductPage.target_product_input, 'demo_12');
        await client.waitForVisibleAndClick(AddProductPage.target_product.replace('%I', 1));
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to the Front Office', () => client.switchWindow(1, 2000));
      test('should check that the product "MUG THE ADVENTURE BEGINS "is well displayed', () => client.checkTextValue(productPage.product_name, 'MUG THE ADVENTURE BEGINS', 'equal', 2000));
    }, 'product/check_product');

  },

  fillOptionsTab(supplierData) {
    scenario('Get the pagination Products per page value', client => {
      test('should go to "Shop Parameters - Product Settings" page', () => {
        return promise
          .then(() => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu))
          .then(() => client.pause(1000))
          .then(() => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.product_settings_submenu));
      });
      test('should get the pagination Products per page value', async () => {
        await client.scrollTo(ProductSettings.Pagination.products_per_page_input);
        await client.isVisible(ProductSettings.Pagination.products_per_page_input);
        if (global.isVisible) {
          await client.getAttributeInVar(ProductSettings.Pagination.products_per_page_input, 'value', 'pagination');
        }
      });
    }, 'product/product');

    commonManufacturers.createSupplier(supplierData);

    scenario('Fill the Options tab for the created product', client => {
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on the product name', () => client.waitForExistAndClick(AddProductPage.catalog_product_name));
      test('should click on "SAVE" button', () => {
        return promise
          .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
          .then(() => client.scrollWaitForExistAndClick(AddProductPage.product_online_toggle))
          .then(() => client.waitForSymfonyToolbar(AddProductPage, 1000))
          .then(() => client.waitForExistAndClick(AddProductPage.save_product_button));
      });
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      scenario('Fill "Options" form', client => {
        test('should click on "Options" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_options_tab));
        test('should verify that the default visibility is "Everywhere"', () => client.isSelected(AddProductPage.options_visibility_option.replace('%O', 'both')));
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should click on "Preview" button', () => {
          return promise
            .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
            .then(() => client.waitForExistAndClick(AddProductPage.preview_buttons));
        });
        test('should switch to the Front Office', () => client.switchWindow(1));
        this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
        test('should go to the "Home" page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
        test('should click on "SEE ALL PRODUCTS" link', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
        test('should get the number for pagination', async () => {
          await client.pause(2000);
          await client.getTextInVar(productPage.products_number, 'productNumber');
          global.pagination = await Number(Math.ceil(Number(global.tab['productNumber'].split(' ')[2]) / Number(global.tab['pagination'])));
        });
        test('should check that the product is well displayed', async () => {
          await client.clickPageNext(productPage.pagination_number_link.replace('%NUM', Math.ceil(Number(global.pagination))), 2000);
          await client.waitForExist(productPage.product_name_link.replace('%S', data.virtual.name + date_time), 3000);
        });
        test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, data.virtual.name + date_time));
        test('should check that the product is well displayed', () => client.scrollWaitForExistAndClick(SearchProductPage.product_result_name));
        this.chooseVisibility(client, SearchProductPage, productPage, 'Search', 'search');
        test('should go back to the Back Office', () => client.switchWindow(0));
        test('should set the "product name" to "PA' + date_time + '"', () => client.waitAndSetValue(AddProductPage.product_name_input, 'PA' + date_time));
        test('should click on "Basic settings" tab', () => client.scrollWaitForExistAndClick(AddProductPage.basic_settings_tab));
        test('should set the category to "Clothes"', async () => {
          await client.scrollWaitForExistAndClick(AddProductPage.category_checkbox.replace('%CATEGORY', 'Clothes'));
          await client.scrollWaitForExistAndClick(AddProductPage.category_radio_button.replace('%VALUE', 3));
        });
        test('should click on "Save" button', () => client.scrollWaitForExistAndClick(AddProductPage.save_product_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should go to the Front Office', () => client.switchWindow(1));
        this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
        test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, 'PA' + date_time));
        test('should check that the product is well displayed', async () => {
          await client.waitForExist(SearchProductPage.product_result_name);
          await client.waitForExistAndClick(productPage.first_product_all);
        });
        this.chooseVisibility(client, SearchProductPage, productPage, 'Catalog Only', 'catalog');
        this.chooseVisibility(client, SearchProductPage, productPage, 'nowhere', 'none');
        test('should go back to the Back Office', () => client.closeWindow(0));
        test('should check the product by accessing via its URL', async () => {
          await client.waitForExistAndClick(AddProductPage.preview_buttons);
          await client.switchWindow(1);
          await this.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
        });
        test('should go back to the Back Office', () => client.switchWindow(0));
        test('should verify that "Available for order" is checked', () => client.checkCheckboxStatus(AddProductPage.options_available_for_order_checkbox, true));
        test('should click on "Available for order" checkbox', () => client.waitForExistAndClick(AddProductPage.options_available_for_order_checkbox));
        test('should verify that "Web only (not sold in your retail store)" is unchecked', () => client.checkCheckboxStatus(AddProductPage.options_online_only, false));
        test('Should check that the "show price" does exist', () => client.isExisting(AddProductPage.options_show_price_checkbox));
        test('should click on "Show price" checkbox', () => client.waitForExistAndClick(AddProductPage.options_show_price_checkbox, 1000));
        test('should verify that "Display condition on product page" is unchecked', () => client.checkCheckboxStatus(AddProductPage.options_show_condition_checkbox, false));
        test('should click on "Display condition on product page" checkbox', () => client.waitForExistAndClick(AddProductPage.options_show_condition_checkbox));
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should go to the Front Office', () => client.switchWindow(1));
        test('should check that the "ADD TO CART" button is disabled (no error message is displayed)', () => client.checkAttributeValue(CheckoutOrderPage.add_to_cart_button, 'disabled', '', 'contain'));
        test('should check that the product online only flag does not exist', () => client.isNotExisting(productPage.product_online_only_flag, 1000));
        test('should click on "Product Details" tab', () => client.scrollWaitForExistAndClick(productPage.product_tab_list.replace('%I', 2), 150, 1000));
        test('should go back to the Back Office', () => client.switchWindow(0));
        test('should click on "Show price" checkbox', () => client.waitForExistAndClick(AddProductPage.options_show_price_checkbox, 1000));
        test('should click on "Available for order" checkbox', () => client.waitForExistAndClick(AddProductPage.options_available_for_order_checkbox));
        test('should click on "Web only (not sold in your retail store)" checkbox', () => client.waitForExistAndClick(AddProductPage.options_online_only));
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should go to the Front Office', () => client.switchWindow(1));
        this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
        test('should go back to the Back Office', () => client.switchWindow(0));
        test('should click on "Web only (not sold in your retail store)" checkbox', () => client.waitForExistAndClick(AddProductPage.options_online_only, 2000));
        test('should set "Tags" with ","', () => client.waitAndSetValue(AddProductPage.tag_input, 'First Tag, Second Tag, '));
        test('should choose the "Everywhere" from the visibility list', () => client.waitAndSelectByValue(AddProductPage.options_visibility, 'both'));
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should go to the Front Office', () => client.switchWindow(1));
        test('should search with "First Tag"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, 'First Tag'));
        test('should check that the product is well displayed', () => client.waitForExist(SearchProductPage.product_result_name));
        test('should go back to the Back Office', () => client.closeWindow(0));
        test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
        test('should search for the product', () => client.searchProductByName('PA' + global.date_time));
        test('should click on "Edit" button', () => client.waitForExistAndClick(ProductList.edit_button));
        test('should click on "Options" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_options_tab));
        test('should choose the "Used" from the condition list', () => client.waitAndSelectByValue(AddProductPage.options_condition_select, 'used', 1000));
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should go to the Front Office', () => {
          return promise
            .then(() => client.waitForExistAndClick(AddProductPage.preview_buttons))
            .then(() => client.switchWindow(1))
            .then(() => this.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name));
        });
        test('should click on "Product Details" tab', () => client.scrollWaitForExistAndClick(productPage.product_tab_list.replace('%I', 2), 150, 3000));
        test('should go back to the Back Office', () => client.switchWindow(0));
        test('should choose the "Refurbished" from the condition list', () => client.waitAndSelectByValue(AddProductPage.options_condition_select, 'refurbished', 1000));
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should go to the Front Office', () => client.switchWindow(1));
        this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
        test('should click on "Product Details" tab', () => client.scrollWaitForExistAndClick(productPage.product_tab_list.replace('%I', 2), 150, 1000));

        // Verify EAN ISBN et UPC only for products with combination
        this.fillCustomizationBlock(client);
        test('should go back to the Back Office', () => client.switchWindow(0));
        test('should check that there is no "attach a new file" is displayed', async () => {
          await client.isVisible(AddProductPage.attached_file_table);
          if (global.isVisible === false) {
            await client.isExisting(AddProductPage.no_attached_file_message, 1000);
          }
        });
        test('should check that the "attach a new file" is displayed', () => client.isExisting(AddProductPage.attached_file_table));
        test('should click on "ATTACH A NEW FILE"', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_new_file_button, 50, 2000));
        test('should add a file', () => client.addFile(AddProductPage.options_select_file, 'image_test.jpg'), 50);
        test('should set the file "Title"', () => client.waitAndSetValue(AddProductPage.options_file_name, 'title'));
        test('should set the file "Description" ', () => client.waitAndSetValue(AddProductPage.options_file_description, 'description'));
        test('should add the previous added file', () => client.scrollWaitForExistAndClick(AddProductPage.options_file_add_button, 50));
        test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
        test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        test('should go to the Front Office', () => client.switchWindow(1));
        test('should click on "Attachments" tab', () => client.scrollWaitForExistAndClick(productPage.product_tab_list.replace('%I', 3), 150, 1000));
        test('should check that the "Attachment title" is equal to "title"', async () => {
          await client.scrollTo(productPage.attachment_title);
          await client.checkTextValue(productPage.attachment_title, 'title', 'equal', 2000);
        });
        test('should check that the "Attachment description" is equal to "description"', async () => {
          await client.scrollTo(productPage.attachment_description);
          await client.checkTextValue(productPage.attachment_description, 'description', 'equal');
        });
        test('should go back to the Back Office', () => client.switchWindow(0));
        test('should check that the "Suppliers" table is displayed', () => client.isExisting(AddProductPage.suppliers_table));
        scenario('Add the supplier to the product', client => {
          test('should click the supplier "' + supplierData.name + global.date_time + '" checkbox', () => {
            return promise
              .then(() => client.scrollWaitForExistAndClick(AddProductPage.suppliers_checkbox.replace('%FileName', supplierData.name + global.date_time), 150, 1000))
              .then(() => client.getAttributeInVar(AddProductPage.suppliers_checkbox.replace('%FileName', supplierData.name + global.date_time), 'value', 'supplierValue'));
          });
          test('should check that The supplier is well associated in the product', () => client.checkTextValue(AddProductPage.suppliers_title, supplierData.name + global.date_time));
          test('should click the default supplier "' + supplierData.name + global.date_time + '" radio button', () => client.waitForExistAndClick(AddProductPage.suppliers_radio_button.replace('%FileName', supplierData.name + global.date_time)));
          test('should set the "Supplier reference" input', () => client.waitAndSetValue(AddProductPage.supplier_reference_input.replace('%V', global.tab['supplierValue']), 'Ref. Supplier'));
          test('should set the "Price(tax excl.)" input', () => client.waitAndSetValue(AddProductPage.supplier_price_input.replace('%V', global.tab['supplierValue']), '20'));
          test('should set the "Currency" select to "Euro"', () => client.waitAndSelectByValue(AddProductPage.supplier_currency_select.replace('%V', global.tab['supplierValue']), '1'));
          test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
          test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
        }, 'product/product');
      }, 'product/check_product');
    }, 'product/product');
  },
  chooseVisibility(client, SearchProductPage, productPage, visibilityOption, visibilityValue) {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should click on "Options" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_options_tab));
    test('should choose the ' + visibilityOption + ' from the visibility list', () => client.waitAndSelectByValue(AddProductPage.options_visibility, visibilityValue));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should go to the Front Office', () => client.switchWindow(1));
    test('should go to the "Home" page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
    test('should click on "SEE ALL PRODUCTS" link', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
    if (visibilityValue === 'none') {
      test('should check that the product does not exist', async () => {
        await client.clickPageNext(productPage.pagination_number_link.replace('%NUM', Math.ceil(Number(global.pagination))), 3000);
        await client.isNotExisting(productPage.product_name_link.replace('%S', 'pa' + date_time), 1000);
      });
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, 'PA' + date_time));
      test('should check that the product does not exist', () => client.isNotExisting(SearchProductPage.product_result_name));
    } else if (visibilityValue === 'search') {
      test('should check that the product does not exist', () => client.isNotExisting(productPage.product_name_link.replace('%S', data.virtual.name + date_time)));
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, data.virtual.name + date_time));
      test('should check that the product is well displayed', async () => {
        await client.waitForExist(SearchProductPage.product_result_name);
        await client.waitForExistAndClick(productPage.first_product_all);
      });
    } else if (visibilityValue === 'catalog') {
      test('should check that the product is well displayed', async () => {
        await client.clickPageNext(productPage.pagination_number_link.replace('%NUM', Math.ceil(Number(global.pagination))), 3000);
        await client.waitForExist(productPage.product_name_link.replace('%S', 'PA' + date_time), 3000)
      });
      test('should go to the "Home" page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, data.virtual.name + date_time));
      test('should check that the product does not exist', () => client.isNotExisting(SearchProductPage.product_result_name));
    }
  },
  fillCustomizationBlock(client) {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should click on "ADD A CUSTOMIZAITION FIELD" button', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
    test('should check that the "Delete" icon does exist', () => client.isExisting(AddProductPage.delete_customization_field_icon));
    test('should check that the "Required" checkbox does exist', () => client.isExisting(AddProductPage.options_first_custom_field_require));
    test('should set the customization field "Label" input', () => client.waitAndSetValue(AddProductPage.options_custom_field_label.replace('%R', 0), 'text not required'));
    test('should select the customization field "Type" Text', () => client.waitAndSelectByValue(AddProductPage.options_custom_field_type.replace('%R', 0), '1'));

    test('should click on "ADD A CUSTOMIZAITION" button', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
    test('should set the second customization field "Label" input', () => client.waitAndSetValue(AddProductPage.options_custom_field_label.replace('%R', 1), 'text required'));
    test('should select the customization field "Type" Text', () => client.waitAndSelectByValue(AddProductPage.options_custom_field_type.replace('%R', 1), '1'));
    test('should click on "Required" checkbox', () => client.waitForExistAndClick(AddProductPage.options_custom_field_require.replace('%R', 1)));

    test('should click on "ADD A CUSTOMIZAITION" button', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
    test('should set the third customization field "Label" input', () => client.waitAndSetValue(AddProductPage.options_custom_field_label.replace('%R', 2), 'File not required'));
    test('should select the customization field "Type" File', () => client.waitAndSelectByValue(AddProductPage.options_custom_field_type.replace('%R', 2), '0'));

    test('should click on "ADD A CUSTOMIZAITION" button', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
    test('should set the fourth customization field "Label" input', () => client.waitAndSetValue(AddProductPage.options_custom_field_label.replace('%R', 3), 'File required'));
    test('should select the customization field "Type" File', () => client.waitAndSelectByValue(AddProductPage.options_custom_field_type.replace('%R', 3), '0'));
    test('should click on "Required" checkbox', () => client.waitForExistAndClick(AddProductPage.options_custom_field_require.replace('%R', 3)));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should go to the Front Office', () => client.switchWindow(1));
    this.clickOnPreviewLink(client, AddProductPage.preview_link, AccessPageFO.logo_home_page);
    test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should click on "Proceed to checkout" modal button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
    test('should check that the "Product customization" image does exist', () => client.isVisible(CheckoutOrderPage.product_customization_modal_image));
    test('should click on "Product name" link', () => client.waitForExistAndClick(CheckoutOrderPage.product_name_link, 2000));
  }
};
