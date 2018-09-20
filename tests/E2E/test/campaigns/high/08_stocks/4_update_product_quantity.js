const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const stock_common_scenarios = require('../../common_scenarios/stock');
const commonProduct = require('../../common_scenarios/product');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();

let productData = [{
  name: 'FirstProduct',
  reference: 'firstProduct',
  quantity: "100",
  price: '5',
  image_name: 'image_test.jpg'
}, {
  name: 'SecondProduct',
  reference: 'secondProduct',
  quantity: "100",
  price: '5',
  image_name: 'image_test.jpg'
}, {
  name: 'ThirdProduct',
  reference: 'thirdProduct',
  quantity: "100",
  price: '5',
  image_name: 'image_test.jpg'
}];

scenario('Increase & decrease the quantity for one product using the arrow up/down button and save by the "Check" sign', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    commonProduct.createProduct(AddProductPage, productData[0]);
    commonProduct.createProduct(AddProductPage, productData[1]);
    commonProduct.createProduct(AddProductPage, productData[2]);
  }, 'stocks');

  scenario('Increase quantity of products using the arrow up button', client => {
    test('should go to "Stocks" page', () => {
      return promise
        .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu))
        .then(() => client.isVisible(Stock.sort_product_icon, 2000))
        .then(() => {
          if (global.isVisible) {
            client.waitForVisibleAndClick(Stock.sort_product_icon);
          }
        })
        .then(() => client.pause(5000))
    });

    for (let i = 1; i <= 3; i++) {
      stock_common_scenarios.changeStockProductQuantity(client, Stock, i, 5);
    }
    test('should click on "Apply new quantity" button', () => client.waitForExistAndClick(Stock.group_apply_button));
    test('should check the success panel', () => {
      return promise
        .then(() => client.waitForVisible(Stock.success_hidden_panel))
        .then(() => client.checkTextValue(Stock.success_hidden_panel, 'Stock successfully updated', 'contain'));
    });
  }, 'stocks');


  scenario('should check the Increase quantity of products movement', client => {
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "+", "Employee Edition", 'firstProduct');
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 2, "5", "+", "Employee Edition", 'secondProduct');
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 3, "5", "+", "Employee Edition", 'thirdProduct');
  }, 'stocks');

  scenario('Decrease quantity of products using the arrow down button', client => {
    test('should go to "Stocks" page', () => {
      return promise
        .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu))
        .then(() => client.isVisible(Stock.sort_product_icon, 2000))
        .then(() => {
          if (global.isVisible) {
            client.waitForVisibleAndClick(Stock.sort_product_icon);
          }
        })
        .then(() => client.pause(5000))
    });

    for (let i = 1; i <= 3; i++) {
      stock_common_scenarios.changeStockProductQuantity(client, Stock, i, 5, 'checkBtn', "remove");
    }
    test('should check the success panel', () => {
      return promise
        .then(() => client.waitForVisible(Stock.success_hidden_panel))
        .then(() => client.checkTextValue(Stock.success_hidden_panel, 'Stock successfully updated', 'contain'));
    });
  }, 'stocks');

  scenario('should check the Increase quantity of products movement', client => {
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "-", "Employee Edition", 'firstProduct');
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 2, "5", "-", "Employee Edition", 'secondProduct');
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 3, "5", "-", "Employee Edition", 'thirdProduct');
  }, 'stocks');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'stocks');
}, 'stocks', true);

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-5789
 **/

scenario('Enter a negative quantity with keyboard for one product in the field ', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');
  scenario('Change the quantity for one product entering the value in the field, and click on the "check" sign', client => {
    test('should go to "Stocks" page', () => {
      return promise
        .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu))
        .then(() => client.waitForExistAndClick(Stock.sort_product_icon, 2000))
        .then(() => client.pause(5000));
    });
    test('should set the "Quantity" of the first product to 15', () => client.modifyProductQuantity(Stock, 1, -15));
    test('should click on "Apply new quantity" button', () => client.waitForExistAndClick(Stock.group_apply_button));
    scenario('should check the Increase quantity of products movement', client => {
      stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 1, "15", "-", "Employee Edition");
    }, 'stocks');

  }, 'stocks');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'stocks', true);
}, 'stocks', true);
