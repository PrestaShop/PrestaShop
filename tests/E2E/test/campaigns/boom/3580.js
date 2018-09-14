const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');
const {productPage} = require('../../selectors/FO/product_page');
const {Menu} = require('../../selectors/BO/menu.js');
const {CustomerSettings} = require('../../selectors/BO/shopParameters/customer_settings.js');
const commonScenarios = require('../common_scenarios/taxes');
let promise = Promise.resolve();

let productData = {
  name: 'SPD',
  reference: 'Product with specific price',
  quantity: "50",
  price: '10',
  image_name: 'image_test.jpg',
  pricing: [
    {
      starting_at: '10',
      product_price: '8',
      discount_type: '0'
    },
    {
      starting_at: '20',
      product_price: '7',
      discount_type: '0'
    }
  ]
};

let taxData = {
  name: 'VAT',
  tax_value: '19',
  tax: '23'
};

scenario('BOMM-3580: Create "Tax rules" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  commonScenarios.createTaxRule(taxData.name, taxData.tax_value);
  commonScenarios.checkTaxRule(taxData.name);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

scenario('Create "Product"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');
  scenario('Create a new product in the Back Office', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "New Product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
    test('should set the "Name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, productData.name + date_time));
    test('should set the "Reference"', () => client.waitAndSetValue(AddProductPage.product_reference, productData.reference));
    test('should set the "Quantity" input', () => client.waitAndSetValue(AddProductPage.quantity_shortcut_input, productData.quantity));
    test('should set the "Price" input', () => client.setPrice(AddProductPage.priceTE_shortcut, productData.price));
    test('should upload the first product picture', () => client.uploadPicture(productData.image_name, AddProductPage.picture));
    scenario('Edit the product pricing', client => {
      test('should click on "Pricing"', () => client.scrollWaitForExistAndClick(AddProductPage.product_pricing_tab, 50));
      test('should set the "Tax rule" to "23%"', () => {
        return promise
          .then(() => client.waitForExistAndClick(AddProductPage.pricing_tax_rule_select))
          .then(() => client.waitAndSetValue(AddProductPage.pricing_tax_rule_input, taxData.name + date_time))
          .then(() => client.waitForExistAndClick(AddProductPage.pricing_tax_rule_option));
      });
      test('should click on "Add specific price" button', () => client.scrollWaitForExistAndClick(AddProductPage.pricing_add_specific_price_button));
      test('should set the "Starting at" input', () => client.waitAndSetValue(AddProductPage.specific_price_starting_at_input, productData.pricing[0].starting_at, 3000));
      test('should set the "Leave initial price" input', () => client.scrollWaitForExistAndClick(AddProductPage.leave_initial_price_checkbox, 150, 2000));
      test('should set the "Product price" input', () => client.waitAndSetValue(AddProductPage.specific_product_price_input, productData.pricing[0].product_price));
      test('should select "Tax exclude"', () => client.waitAndSelectByValue(AddProductPage.specific_price_reduction_tax_select, productData.pricing[0].discount_type));
      test('should click on "Apply" button', () => client.waitForExistAndClick(AddProductPage.specific_price_save_button));
      test('should click on "Save" button', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.symfony_toolbar, 3000))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar)
            }
          })
          .then(() => client.waitForExistAndClick(AddProductPage.save_product_button, 2000));
      });
      test('should click on "Add specific price" button', () => client.scrollWaitForExistAndClick(AddProductPage.pricing_add_specific_price_button));
      test('should set the "Starting at" input', () => client.waitAndSetValue(AddProductPage.specific_price_starting_at_input, productData.pricing[1].starting_at, 3000));
      test('should set the "Leave initial price" input', () => client.scrollWaitForExistAndClick(AddProductPage.leave_initial_price_checkbox, 150, 2000));
      test('should set the "Product price" input', () => client.waitAndSetValue(AddProductPage.specific_product_price_input, productData.pricing[1].product_price));
      test('should click on "Apply" button', () => {
        return promise
          .then(() => client.scrollTo(AddProductPage.specific_price_save_button))
          .then(() => client.waitForExistAndClick(AddProductPage.specific_price_save_button));
      });
    }, 'product/product');
    scenario('Save the created product', client => {
      test('should switch the product online', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.symfony_toolbar, 3000))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar)
            }
          })
          .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 2000));
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    }, 'product/product');
  }, 'product/product');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'product/product');
}, 'product/product', true);

scenario('Check the product discount in the Front Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Edit price display method', client => {
    test('should go to the "Customer settings" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu));
    test('should click on "Groups" tab', () => client.waitForExistAndClick(Menu.Configure.ShopParameters.groups_tab));
    test('should click on "Edit" button', () => {
      return promise
        .then(() => client.searchByValue(CustomerSettings.groups.filter_name_input, CustomerSettings.groups.filter_search_button, 'Visitor'))
        .then(() => client.waitForExistAndClick(CustomerSettings.groups.edit_button));
    });
    test('should change the "Price display method" to "Tax included"', () => client.waitAndSelectByValue(CustomerSettings.groups.price_display_method_select, 0));
    test('should click on "Save" button', () => {
      return promise
        .then(() => client.scrollTo(CustomerSettings.groups.save_button))
        .then(() => client.waitForExistAndClick(CustomerSettings.groups.save_button));
    });
  }, 'common_client');
  scenario('Check the discount calculation in the Front Office', client => {
    test('should go to the Front Office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1));
    });
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should search for the product "' + productData.name + date_time + '"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the first quantity is equal to "10"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 1), productData.pricing[0].starting_at));
    test('should check that the second quantity is equal to "20"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 2).replace('%D', 1), productData.pricing[1].starting_at));
    /*
     * if the price display method is tax included
     * discount = ((productData.price + (productData.price * 0.23)) - (productData.pricing[0].product_price + (productData.pricing[0].product_price * 0.23)))
     */
    test('should check that the first discount is equal to "€2.46"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 2), '€2.46'));
    test('should check that the second discount is equal to "€3.69"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 2).replace('%D', 2), '€3.69'));
    test('should check that the first save is equal to "Up to €24.60"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 3), 'Up to €24.60'));
    test('should check that the second save is equal to "Up to €73.80"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 2).replace('%D', 3), 'Up to €73.80'));
  }, 'common_client');

  scenario('Edit price display method', client => {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should go to the "Customer settings" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu));
    test('should click on "Groups" tab', () => client.waitForExistAndClick(Menu.Configure.ShopParameters.groups_tab));
    test('should click on "Edit" button', () => {
      return promise
        .then(() => client.searchByValue(CustomerSettings.groups.filter_name_input, CustomerSettings.groups.filter_search_button, 'Visitor'))
        .then(() => client.waitForExistAndClick(CustomerSettings.groups.edit_button));
    });
    test('should change the "Price display method" to "Tax excluded"', () => client.waitAndSelectByValue(CustomerSettings.groups.price_display_method_select, 1));
    test('should click on "Save" button', () => {
      return promise
        .then(() => client.scrollTo(CustomerSettings.groups.save_button))
        .then(() => client.waitForExistAndClick(CustomerSettings.groups.save_button));
    });
  }, 'common_client');

  scenario('Check the discount calculation in the Front Office', client => {
    test('should go to the Front Office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1));
    });
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should search for the product "' + productData.name + date_time + '"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the first quantity is equal to "10"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 1), productData.pricing[0].starting_at));
    test('should check that the second quantity is equal to "20"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 2).replace('%D', 1), productData.pricing[1].starting_at));
    /*
     * if the price display method is tax excluded
     * discount = (productData.price - productData.pricing[0].product_price)
     */
    test('should check that the first discount is equal to "€2"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 2), '€2.00'));
    test('should check that the second discount is equal to "€3"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 2).replace('%D', 2), '€3.00'));
    test('should check that the first save is equal to "Up to €20"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 1).replace('%D', 3), 'Up to €20.00'));
    test('should check that the second save is equal to "Up to €60"', () => client.checkTextValue(productPage.product_discounts_table.replace('%R', 2).replace('%D', 3), 'Up to €60.00'));
  }, 'common_client');

  scenario('Delete "Tax rules" in the Back Office', () => {
    scenario('Go back to Back Office', client => {
      test('should go back successfully to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
    commonScenarios.deleteTaxRule(taxData.name);
    scenario('Logout from the Back Office', client => {
      test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'common_client');
  }, 'common_client');
}, 'common_client', true);
