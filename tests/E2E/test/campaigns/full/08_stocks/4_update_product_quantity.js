/**
 * This script is based on the scenario described in this test link
 * [id="PS-293"][Name="Update quantity of a product "]
 **/
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const stockCommonScenarios = require('../../common_scenarios/stock');
const commonProduct = require('../../common_scenarios/product');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');
let promise = Promise.resolve();
let dateFormat = require('dateformat');
let dateSystem = dateFormat(new Date(), 'yyyy-mm-dd');
let productData = [{
  name: 'FirstProductQuantity',
  reference: 'firstProduct',
  quantity: "5",
  price: '5',
  image_name: 'image_test.jpg'
}, {
  name: 'SecondProductQuantity',
  reference: 'secondProduct',
  quantity: "5",
  price: '5',
  image_name: 'image_test.jpg'
}, {
  name: 'ThirdProductQuantity',
  reference: 'thirdProduct',
  quantity: "5",
  price: '5',
  image_name: 'image_test.jpg'
}];
scenario('Update quantity of a product', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');
  welcomeScenarios.findAndCloseWelcomeModal();
  for (let i = 0; i < 3; i++) {
    commonProduct.createProduct(AddProductPage, productData[i]);
  }

  scenario('Increase the quantity for one product using the arrow down button and save by the "Check" sign', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock);
    stockCommonScenarios.changeStockProductQuantity(client, Stock, 1, 5, 'checkBtn');
    stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, +5, 'equal', 'changed', Stock, 1);
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "+", "Employee Edition", productData[2].reference, dateSystem, productData[2].name + date_time);
  }, 'stocks');

  scenario('Decrease the quantity for one product using the arrow down and save by the "Apply new quantity" button', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock);
    stockCommonScenarios.changeStockProductQuantity(client, Stock, 1, 5, '', '');
    test('should click on "Apply new quantity" button', () => client.waitForExistAndClick(Stock.group_apply_button));
    test('should check the success panel', () => {
      return promise
        .then(() => client.waitForVisible(Stock.success_hidden_panel))
        .then(() => client.checkTextValue(Stock.success_hidden_panel,'Stock successfully updated', 'contain'));
    });
    stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, -5, 'equal', 'changed', Stock, 1);
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "-", "Employee Edition", productData[2].reference, dateSystem, productData[2].name + date_time);
  }, 'stocks');

  scenario('Change the quantity for one product entering the value in the field and save by the "Check" sign', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock);
    test('should set the "Quantity" of the first product to 15', () => client.modifyProductQuantity(Stock, 1, 15));
    test('should click on "Check" button', () => client.waitForExistAndClick(Stock.save_product_quantity_button.replace('%I', 1)));
    test('should check the success panel', () => {
      return promise
        .then(() => client.waitForVisible(Stock.success_hidden_panel))
        .then(() => client.checkTextValue(Stock.success_hidden_panel,'Stock successfully updated', 'contain'));
    });
    stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, +15, 'equal', 'changed', Stock, 1);
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "15", "+", "Employee Edition", productData[2].reference, dateSystem, productData[2].name + date_time);
  }, 'stocks');

  scenario('Enter a negative quantity with keyboard for one product in the field and save by the "Check" sign', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock);
    stockCommonScenarios.changeStockQuantityWithKeyboard(client, Stock, 1, 5, 'checkBtn');
    stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, -5, 'equal', 'changed', Stock, 1);
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "-", "Employee Edition", productData[2].reference, dateSystem, productData[2].name + date_time);
  }, 'stocks');

  scenario('Enter a negative quantity with the arrow down for one product ', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock);
    stockCommonScenarios.changeStockProductQuantity(client, Stock, 1, 5, 'checkBtn', "remove");
    stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, -5, 'equal', 'changed', Stock, 1);
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "-", "Employee Edition", productData[2].reference, dateSystem, productData[2].name + date_time);
  }, 'stocks');

  scenario('Enter a decimal quantity with "." for one product in the field and save by the "Check" sign (issue #9616)', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock);
    test('should set the "Quantity" of the first product to 10.5', () => client.modifyProductQuantity(Stock, 1, 10.5));
    test('should click on "Check" button', () => client.waitForExistAndClick(Stock.save_product_quantity_button.replace('%I', 1)));
    stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, 0, 'equal', 'unchanged', Stock, 1);
  }, 'stocks');

  scenario('Change the quantity for several lines  and save by the "Apply new quantity" button', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock, true);
    for (let i = 1; i <= 3; i++) {
      stockCommonScenarios.changeStockProductQuantity(client, Stock, i, 5, '');
    }
    test('should click on "Apply new quantity" button', () => client.waitForExistAndClick(Stock.group_apply_button));
    test('should check the success panel', () => {
      return promise
        .then(() => client.waitForVisible(Stock.success_hidden_panel))
        .then(() => client.checkTextValue(Stock.success_hidden_panel,'Stock successfully updated', 'contain'));
    });
    for (let i = 1; i <= 3; i++) {
      stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, +5, 'equal', 'changed', Stock, i);
    }
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "+", "Employee Edition", productData[2].reference, dateSystem, productData[2].name + global.date_time, true);
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "+", "Employee Edition", productData[1].reference, dateSystem, productData[1].name + global.date_time, true);
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "+", "Employee Edition", productData[0].reference, dateSystem, productData[0].name + global.date_time, true);
  }, 'stocks');

  scenario('Change the quantity for several lines with keyboard and click on the "check" sign', client => {
    stockCommonScenarios.goToStockPageAndSortByProduct(client, Menu, Stock);
    for (let i = 1; i <= 3; i++) {
      stockCommonScenarios.changeStockQuantityWithKeyboard(client, Stock, i, 5);
    }
    test('should click on "Check" button', () => client.waitForExistAndClick(Stock.save_product_quantity_button.replace('%I', 2)));
    test('should check the success panel', () => {
      return promise
        .then(() => client.waitForVisible(Stock.success_hidden_panel))
        .then(() => client.checkTextValue(Stock.success_hidden_panel, "Stock successfully updated", 'contain'));
    });
    for (let i = 1; i <= 3; i++) {
      if (i === 2) {
        stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, -5, 'equal', 'changed', Stock, i);
      } else {
        stockCommonScenarios.checkAvailableAndPhysicalQuantity(client, -5, 'notequal', 'unchanged', Stock, i);
      }
    }
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "5", "-", "Employee Edition", productData[1].reference, dateSystem, productData[1].name + date_time, true);
  }, 'stocks');
}, 'stocks', true);
