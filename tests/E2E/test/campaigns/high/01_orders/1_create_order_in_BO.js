const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {CreateOrder} = require('../../../selectors/BO/order');
const orderScenarios = require('../../common_scenarios/order');
const common_scenarios = require('../../common_scenarios/product');
const welcomeScenarios = require('../../common_scenarios/welcome');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();

let productData = {
  name: 'P1',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'test_1',
  type: 'combination',
  attribute: {
    1: {
      name: 'color',
      variation_quantity: '10'
    }
  }
};

scenario('Create order in the Back Office', () => {
  scenario('Create "Product"', () => {
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'order');
    welcomeScenarios.findAndCloseWelcomeModal();
    common_scenarios.createProduct(AddProductPage, productData);
    scenario('Logout from the Back Office', client => {
      test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'order');
  }, 'order', true);

  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'order');

  orderScenarios.createOrderBO(OrderPage, CreateOrder, productData);

  scenario('Check the created order in the Back Office', client => {
    test('should check status to be equal to "Awaiting check payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting check payment'));
    test('should set order status to "Refunded"', () => client.updateStatus('Payment error'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check that the status is "Refunded"', () => client.checkTextValue(OrderPage.order_status, 'Payment error'));
    test('should set order status to "Awaiting bank wire payment"', () => client.updateStatus('Awaiting bank wire payment'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check that the status is "Awaiting bank wire payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting bank wire payment'));
    test('should set order status to "Payment accepted"', () => client.updateStatus('Payment accepted'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check that the status is "Payment accepted"', () => client.checkTextValue(OrderPage.order_status, 'Payment accepted'));
    test('should check that the "shipping cost" is equal to €8.40', () => client.checkTextValue(OrderPage.shipping_cost, '€8.40'));
    test('should check that the "order message" is equal to "Order message test"', () => client.checkTextValue(OrderPage.message_order, 'Order message test', 'contain', 4000));
    test('should check "the product information"', () => client.checkTextValue(OrderPage.product_Url, ('Beige', productData.name, productData.reference, 'M'), 'contain'));
    test('should check that the "quantity" is  equal to "4"', () => client.checkTextValue(OrderPage.order_quantity.replace("%NUMBER", 1), '4'));
    test('should check "basic price" ', () => {
      return promise
        .then(() => client.scrollWaitForExistAndClick(OrderPage.edit_product_button, 50))
        .then(() => client.checkTextValue(OrderPage.product_basic_price.replace("%NUMBER", 1), global.basic_price));
    });
    test('should check that the "customer" is equal to "John DOE"', () => client.checkTextValue(OrderPage.customer_name, 'John DOE', "contain", 4000));
    test('should set order status to Payment accepted ', () => client.updateStatus('Delivered'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check status to be equal to "Payment Delivered"', () => client.checkTextValue(OrderPage.order_status, 'Delivered'));

    scenario('Print invoice', client => {
      test('should click on "DOCUMENTS" subtab', () => client.waitForVisibleAndClick(OrderPage.document_submenu));
      test('should download the invoice document', () => client.downloadDocument(OrderPage.download_invoice_button));
      test('should check the "invoice file name" ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName));
      test('should check that the "invoice customer" is "John Doe"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
      test('should check  the "invoice basic price"  ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.basic_price));
      test('should check that the "invoice product information" is : "P1' + global.date_time + ' - Size : M- Color : Beige"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "P1" + global.date_time + " - Size : M- Color : Beige"));
    }, 'order');

    scenario('Print delivery invoice', client => {
      test('should download the delivery invoice document', () => client.downloadDocument(OrderPage.download_delivery_button));
      test('should check the "delivery invoice file name"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName));
      test('should check that the "delivery invoice customer" is : Johan DOE', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
      test('should check that the "delivery invoice product information" is : "P1' + global.date_time + ' - Size : M- Color : Beige"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "P1" + global.date_time + " - Size : M- Color : Beige"));
      test('should check that the "delivery invoice product carrier" is : My carrier"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, "My carrier"));
    }, 'order');

  }, 'order');
}, 'order', true);

