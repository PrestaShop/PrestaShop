const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common_scenarios = require('../../common_scenarios/product');
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
}];

scenario('Modify quantity and check the movement of a group of product', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');

  common_scenarios.createProduct(AddProductPage, productData[0]);

  common_scenarios.createProduct(AddProductPage, productData[1]);

  scenario('Modify quantity and check the movement of a group of product', client => {
    test('should go to "Stocks" page', () => {
      return promise
        .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu))
        .then(() => client.waitForExistAndClick(Stock.sort_product_icon, 2000))
        .then(() => client.pause(5000));
    });
    test('should set the "Quantity" of the first product to 15', () => client.modifyProductQuantity(Stock, 2, 15));
    test('should set the "Quantity" of the second product to 50', () => client.modifyProductQuantity(Stock, 1, 50));
    test('should click on "Apply new quantity" button', () => client.waitForExistAndClick(Stock.group_apply_button));
    test('should click on "Movements" tab', () => client.goToStockMovements(Menu, Movement));
    test('should verify the new "Quantity" and "Type" of the two changed products', () => {
      return promise
        .then(() => client.pause(2000))
        .then(() => client.getTextInVar(Movement.time_movement.replace('%P', 1), 'firstMovementDate'))
        .then(() => client.getTextInVar(Movement.time_movement.replace('%P', 2), 'secondMovementDate'))
        .then(() => client.checkOrderMovement(Movement, client));
    });
  }, 'stocks');
}, 'stocks', true);
