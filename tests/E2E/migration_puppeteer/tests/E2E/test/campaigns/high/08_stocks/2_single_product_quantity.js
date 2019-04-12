const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const {Menu} = require('../../../selectors/BO/menu.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common_scenarios = require('../../common_scenarios/product');
const stock_common_scenarios = require('../../common_scenarios/stock');
let promise = Promise.resolve();
let productData = [{
  name: 'SingleProduct',
  reference: 'SingleProduct',
  quantity: "100",
  price: '5',
  image_name: 'image_test.jpg'
}];

scenario('Modify quantity and check movement for single product', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');

  common_scenarios.createProduct(AddProductPage, productData[0]);

  scenario('Modify quantity and check movement for single product', client => {
    test('should go to "Stocks" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu));
    test('should set the "Search products" input', () => client.waitAndSetValue(Stock.search_input, "SingleProduct" + date_time));
    test('should click on "Search" button', () => {
      return promise
          .then(() => client.waitForExistAndClick(Stock.search_button))
          .then(() => client.checkTextContent(Stock.product_selector,"SingleProduct" + date_time,'contain'));
    });
    stock_common_scenarios.changeStockProductQuantity(client, Stock, 1, 4, 'checkBtn');
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 1, "4", "+", "Employee Edition","SingleProduct");
    test('should go to "Stock" tab', () => client.waitForExistAndClick(Menu.Sell.Catalog.stock_tab));
    test('should set the "Search products" input', () => client.waitAndSetValue(Stock.search_input, "SingleProduct" + date_time));
    test('should click on "Search" button', () => {
      return promise
          .then(() => client.waitForExistAndClick(Stock.search_button))
          .then(() => client.checkTextContent(Stock.product_selector,"SingleProduct" + date_time,'contain'));
    });
    stock_common_scenarios.changeStockProductQuantity(client, Stock, 1, 4, 'checkBtn', "remove");
    stock_common_scenarios.checkMovementHistory(client, Menu, Movement, 1, "4", "-", "Employee Edition", "SingleProduct");
    common_scenarios.deleteProduct(AddProductPage, productData[0]);
    scenario('Logout from the Back Office', client => {
      test('should logout successfully from Back Office', () => client.signOutBO());
    }, 'common_client');
  }, 'stocks');
}, 'stocks', true);