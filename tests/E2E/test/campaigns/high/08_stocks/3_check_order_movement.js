const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const {OrderPage} = require('../../../selectors/BO/order');
const {CreateOrder} = require('../../../selectors/BO/order');
const orderScenarios = require('../../common_scenarios/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');

const common_scenarios = require('../../common_scenarios/product');

let productData = {
  name: 'Mvt',
  quantity: "4",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'mvt',
  type: 'combination',
  attribute: {
    name: 'color',
    variation_quantity: '4'
  }
};

scenario('Check order movement', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');

  scenario('Create "Product"', () => {
    common_scenarios.createProduct(AddProductPage, productData);
  }, 'order');

  orderScenarios.createOrderBO(OrderPage, CreateOrder, productData);

  scenario('Change order state to "Delivered"', client => {
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
    test('should go to the first order', () => client.waitForExistAndClick(OrderPage.first_order));
    test('should change order state to "Delivered"', () => client.changeOrderState(OrderPage, 'Delivered'));
    test('should get the order quantity', () => client.getTextInVar(OrderPage.order_quantity, "orderQuantity"));
  }, 'stocks');

  scenario('Check order movement', client => {
    test('should go to "Stocks" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu));
    test('should go to "Movements" tabs', () => client.goToStockMovements(Movement));
    test('should check the movements of the delivered product', () => client.checkMovement(Movement, 1, '4', "-", "Customer Order"));
  }, 'stocks');

}, 'stocks', true);
