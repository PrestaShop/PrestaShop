/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-32"][Name="Print the delivery slips of an order"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {CreateOrder} = require('../../../selectors/BO/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common_scenarios = require('../../common_scenarios/product');
const commonOrder = require('../../common_scenarios/order');
const welcomeScenarios = require('../../common_scenarios/welcome');
let promise = Promise.resolve();
global.orderInformation = [];
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
scenario('Print the delivery slips of an order', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');
  welcomeScenarios.findAndCloseWelcomeModal();
  common_scenarios.createProduct(AddProductPage, productData);
  commonOrder.createOrderBO(OrderPage, CreateOrder, productData);
  scenario('Change the status', client => {
    test('should check status to be equal to "Awaiting check payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting check payment'));
    test('should set order status to "Shipped"', () => client.updateStatus('Shipped'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    /**
     * should refresh the page, to pass the error
     */
    test('should refresh the page', () => client.refresh());
    test('should check that the status is "Shipped"', () => client.checkTextValue(OrderPage.order_status, 'Shipped'));
  }, 'order');
  commonOrder.getDeliveryInformation(0);
  scenario('Verify all the information on the deliveries slips', client => {
    test('should click on "View delivery slip" button', async () => {
      await client.waitForVisible(OrderPage.view_delivery_slip);
      // for headless, we need to remove attribute 'target' to avoid download in a new Tab
      if(global.headless)  await client.removeAttribute(OrderPage.view_delivery_slip,'target');
      await client.waitForExistAndClick(OrderPage.view_delivery_slip);
    });
    test('should click on "DOCUMENTS" subtab', () => client.waitForVisibleAndClick(OrderPage.document_submenu));
    test('should get the delivery slip information', () => {
      return promise
        .then(() => client.getTextInVar(OrderPage.date_delivery_slip, "date_delivery_slip"))
        .then(() => client.getNameInvoice(OrderPage.download_delivery_button))
    });
    test('should check the "delivery slip file name"', async () => {
      await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf', 4000);
      if (global.existingFile) {
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName)
      }
    });
    test('should check that the "delivery slip customer" is : John DOE', async () => {
      await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
      if (global.existingFile) {
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE');
      }
    });
    test('should check the "Billing & Delivery Address"', async () => {
      await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
      if (global.existingFile) {
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'My Company');
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, '16, Main street');
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, '75002 Paris');
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'France');
      }
    });
    test('should check the "delivery slips information"', async () => {
      await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
      if (global.existingFile) {
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab['date_delivery_slip']);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].OrderRef);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].Method);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductRef);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductName);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductCombination);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductQuantity);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductTotal);
        await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].PaymentMethod);
        await client.checkWordNumber(global.downloadsFolderPath, global.invoiceFileName, global.tab['date_delivery_slip'], 2);
      }
    });
    test('should delete the delivery slips file', async () => {
      await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
      if (global.existingFile) {
        await client.deleteFile(global.downloadsFolderPath, global.invoiceFileName, ".pdf", 2000);
      }
    });
  }, 'order');
}, 'order', true);
