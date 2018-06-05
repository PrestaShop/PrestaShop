const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();

let firstProductData = {
  name: 'TEST PRODUCT',
  quantity: '10',
  price: '12',
  picture_name: '1.png'
};

let secondProductData = {
  name: 'PRD',
  quantity: '10',
  price: '12',
  picture_name: '1.png'
};

scenario('Check product page buttons', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');

  scenario('Testing "Preview" button', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "NEW PRODUCT" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
    test('should set the "product name"', () => client.waitAndSetValue(AddProductPage.product_name_input, firstProductData.name + date_time));
    test('should set the "Quantity" of product', () => client.waitAndSetValue(AddProductPage.quantity_shortcut_input, firstProductData.quantity));
    test('should set the "Price" input', () => client.waitAndSetValue(AddProductPage.priceTE_shortcut, firstProductData.price));
    test('should upload the first product picture', () => client.uploadPicture(firstProductData.picture_name, AddProductPage.picture));
    test('should set the product "online"', () =>  {
      return promise
        .then(() => client.isVisible(AddProductPage.symfony_toolbar))
        .then(() => {
          if (global.isVisible) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar)
          }
        })
        .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle));
    });
    test('should click on "SAVE" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Preview" button', () =>  {
      return promise
        .then(() => client.isVisible(AddProductPage.symfony_toolbar))
        .then(() => {
          if (global.isVisible) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar)
          }
        })
        .then(() => client.waitForExistAndClick(AddProductPage.preview_buttons));
    });
    test('should switch to the Front Office', () => client.switchWindow(1));
    test('should check that the product name is equal to "TEST PRODUCT' + date_time + '"', () => client.checkTextValue(productPage.product_name, firstProductData.name, "contain"));
  }, 'product/product');

  scenario('Testing "Duplicate" button', client => {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should click on "Duplicate" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.dropdown_button))
        .then(() => client.waitForExistAndClick(AddProductPage.duplicate_button));
    });
    test('should check the duplication success message', () => client.checkTextValue(AddProductPage.success_panel, "Product successfully duplicated."));
    test('should switch the product "online"', () => client.waitForExistAndClick(AddProductPage.product_online_toggle));
    test('should click on "SAVE" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    scenario('Check that the product is well duplicated', client => {
      test('should go to "Catalog" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.products_submenu));
      test('should search for product by name', () => client.searchProductByName("copy of " + firstProductData.name + date_time));
      test('should check the existence of product name', () => client.checkTextValue(AddProductPage.catalog_product_name, "copy of " + firstProductData.name + date_time));
      test('should check the existence of product category', () => client.checkTextValue(AddProductPage.catalog_product_category, "Home"));
      test('should check the existence of product price TE', () => client.checkProductPriceTE(firstProductData.price));
      test('should check the existence of product quantity', () => client.checkTextValue(AddProductPage.catalog_product_quantity, '0'));
      test('should check the existence of product status', () => client.checkTextValue(AddProductPage.catalog_product_online, 'check'));
      test('should click on the product name', () => client.waitForExistAndClick(AddProductPage.catalog_product_name));
    }, 'product/check_product');
  }, 'product/product');

  scenario('Testing "Online" button', client => {
    test('should switch the product "Offline"', () => client.waitForExistAndClick(AddProductPage.product_online_toggle));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
    test('should switch to the Front Office', () => client.switchWindow(2));
    test('should check the offline warning message', () => client.checkTextValue(productPage.offline_warning_message, "This product is not visible to your customers.", "contain"));
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should switch the product "Online"', () => client.waitForExistAndClick(AddProductPage.product_online_toggle));
  }, 'product/product');

  scenario('Check "Go to catalog" button', client => {
    test('should click on "Go to catalog" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.dropdown_button))
        .then(() => client.waitForExistAndClick(AddProductPage.go_to_catalog_button));
    });
    test('should search for product by name', () => client.searchProductByName("copy of " + firstProductData.name + date_time));
    test('should click on the product name', () => client.waitForExistAndClick(AddProductPage.catalog_product_name));
  }, 'product/check_product');

  scenario('Testing "Add new product" button', client => {
    test('should click on "Add new product" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.dropdown_button))
        .then(() => client.waitForExistAndClick(AddProductPage.new_product_dropdown_button))
        .then(() => client.pause(5000));
    });
    test('should set "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, secondProductData.name + date_time));
    test('should set "Quantity" input', () => client.waitAndSetValue(AddProductPage.quantity_shortcut_input, secondProductData.quantity));
    test('should set the "Tax exclude" price', () => client.waitAndSetValue(AddProductPage.priceTE_shortcut, secondProductData.price));
    test('should switch the product "Online"', () => client.waitForExistAndClick(AddProductPage.product_online_toggle));
    test('should click on "SAVE" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Go to catalog" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.dropdown_button))
        .then(() => client.waitForExistAndClick(AddProductPage.go_to_catalog_button));
    });
    test('should search for product by name', () => client.searchProductByName(secondProductData.name + date_time));
    test('should click on the product name', () => client.waitForExistAndClick(AddProductPage.catalog_product_name));
  }, 'product/check_product');

  scenario('Testing "Delete" button', client => {
    test('should click on "Delete" icon', () => client.waitForExistAndClick(AddProductPage.delete_button));
    /**
     * This scenario is based on the bug described in this ticket
     * http://forge.prestashop.com/browse/BOOM-4950
     **/
    test('should click on "Yes" of the confirmation modal', () => client.waitForVisibleAndClick(AddProductPage.delete_confirmation_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.success_panel, "Product successfully deleted."));
    test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
  }, 'product/product');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'product/product');

}, 'product/product', true);