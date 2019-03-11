const {Menu} = require('../../selectors/BO/menu.js');
let promise = Promise.resolve();
const {ProductList, AddProductPage} = require('../../selectors/BO/add_product_page');
const {CategorySubMenu} = require('../../selectors/BO/catalogpage/category_submenu');
const {TrafficAndSeo} = require('../../selectors/BO/shopParameters/shop_parameters');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
let data = require('../../datas/product-data');
global.productVariations = [];
global.productCategories = {HOME: {}};
global.categories = {HOME: {}};

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
                      promise
                        .then(() => client.scrollWaitForVisibleAndClick(AddProductPage.attribute_value_checkbox.replace('%ID', global.tab[productData.attribute[key].name + '_id']).replace('%S', index)));
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
        .then(() => client.waitForVisibleAndClick(AddProductPage.feature_select_option_text.replace('%ID', id).replace('%V', feature)));
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
    for (let i = 0; i <= pagination; i++) {
      for (let j = 0; j < global.productInfo.length; j++) {
        await client.pause(4000);
        await client.isVisible(AccessPageFO.product_name.replace('%PAGENAME', global.productInfo[j].name.substring(0, 23)));
        if (global.isVisible) {
          global.productInfo[j].status = await true;
        }
      }
      if (i < pagination) {
        await client.isVisible(productPage.pagination_next);
        await client.clickPageNext(productPage.pagination_next, 2000);
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
      test('should check that the product summary is well filled', () => client.checkTextEditor(AddProductPage.summary_textarea, productData.summary));
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
    test('should click on the "Preview" link', () => {
      return promise
        .then(() => client.isVisible(productSelector))
        .then(() => {
          if (global.ps_mode_dev && !global.isVisible) {
            client.waitForExistAndClick(selector)
          } else {
            client.pause(0);
          }
        })
        .then(() => client.pause(5000));
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
    await client.waitForExistAndClick(ProductList.filter_by_category_button);
    await client.waitForExistAndClick(ProductList.unselect_filter_link);
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
    test('should click on "Save" button', () => client.scrollWaitForExistAndClick(TrafficAndSeo.SeoAndUrls.save_button, 1000));
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
  }

};
