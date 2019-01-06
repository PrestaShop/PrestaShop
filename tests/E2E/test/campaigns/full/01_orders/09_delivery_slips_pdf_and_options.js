const {Menu} = require('../../../selectors/BO/menu.js');
const commonOrder = require('../../common_scenarios/order');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {DeliverySlip} = require('../../../selectors/BO/order');
const {OrderPage} = require('../../../selectors/BO/order');
let promise = Promise.resolve();
global.orderInformation = [];

scenario('Test1: Delivery slips PDF', () => {
  scenario('Open the browser and login successfully in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'common_client');
  scenario('Create orders and change the status to "Shipped"', () => {
    for (let i = 1; i <= 3; i++) {
      commonOrder.createOrderFO();
      scenario('Logout from the Front Office', client => {
        test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO))
      }, 'common_client');
      scenario('Login in the Back Office ', client => {
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
        commonOrder.updateStatus("Shipped");
        commonOrder.getDeliveryInformation(i - 1);
      }, 'common_client');
      scenario('Logout from the back office and login in the Front Office', client => {
        test('should logout successfully in the Back Office', () => client.signOutBO(AccessPageBO));
        test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
      }, 'common_client');
    }
  }, 'order');
  scenario('Verify all the information on the deliveries slips', client => {
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    test('should go to the Delivery Slips page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.delivery_slips_submenu));
    test('should generate the "deliveries" pdf file', () => {
      return promise
        .then(() => client.waitForExistAndClick(DeliverySlip.generate_btn))
        .then(() => client.pause(5000));
    });
    for (let i = 1; i <= 3; i++) {
      test('should check the Customer name of the ' + i + ' product', () => client.checkDocument(global.downloadsFolderPath, 'deliveries', 'John DOE'));
      test('should check the "Delivery Address" of the product n°' + i, () => {
        return promise
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', 'My Company'))
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', '16, Main street'))
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', '75002 Paris'))
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', 'France'))
      });
      test('should check the "invoice Date" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'deliveries', global.orderInformation[i - 1].invoiceDate));
      test('should check the "Order Reference" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'deliveries', global.orderInformation[i - 1].OrderRef));
      test('should check the "Product Reference"of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'deliveries', global.orderInformation[i - 1].ProductRef));
      test('should check the "Product Combination" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'deliveries', global.orderInformation[i - 1].ProductCombination));
      test('should check the "Product Quantity" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'deliveries', global.orderInformation[i - 1].ProductQuantity));
      test('should check the "Product Name" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'deliveries', global.orderInformation[i - 1].ProductName));
      test('should check the "total Price" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'deliveries', global.orderInformation[i - 1].ProductTotal));
    }
  }, 'order');
}, 'order');
scenario('Test2: Delivery slips options', () => {
  scenario('Change "Delivery slips" options', client => {
    test('should change the "Delivery prefix"', () => {
      return promise
        .then(() => client.getAttributeInVar(DeliverySlip.delivery_prefix, 'value', 'deliveryPrefix'))
        .then(() => client.waitAndSetValue(DeliverySlip.delivery_prefix, 'PR'))
    });
    test('should change the "Delivery Number"', () => {
      return promise
        .then(() => client.getAttributeInVar(DeliverySlip.delivery_number, 'value', 'delivNumber'))
        .then(() => client.waitAndSetValue(DeliverySlip.delivery_number, Number(global.tab['delivNumber']) + 1));
    });
    test('should Enable the product image', () => client.waitForExistAndClick(DeliverySlip.enable_product_image));
    test('should click on "Save" button', () => client.waitForExistAndClick(DeliverySlip.save_button));
    test('should verify the success message', () => client.checkTextValue(DeliverySlip.success_message, 'Update successful'));
  }, 'common_client');
  scenario('Create Order', client => {
    test('should login successfully in the Front Office', () => client.linkAccess(URL));
    commonOrder.createOrderFO();
  }, 'common_client');
  scenario('Check the delivery slip modified option', client => {
    test('should login successfully in the Back Office', () => client.linkAccess(URL + '/admin-dev'));
    commonOrder.updateStatus("Payment accepted");
    commonOrder.updateStatus("Shipped");
    commonOrder.getDeliveryInformation(0);
    scenario('Check that the option name of the delivery slip is updated successfully', client => {
      test('should click on "DOCUMENTS" tab', () => client.waitForExistAndClick(OrderPage.document_submenu));
      test('should get the "Delivery slip" Document name', () => client.checkTextValue(OrderPage.delivery_slip_document, 'PR', 'contain'));
      test('should get the "Delivery slip" Document Number', () => client.checkTextValue(OrderPage.delivery_slip_document, Number(global.tab['delivNumber']) + 1, 'contain'));
      test('should get the invoice name', () => client.getDocumentName(OrderPage.delivery_slip_document));
      test('should generate the "deliveries" pdf file', () => {
        return promise
          .then(() => client.waitForExistAndClick(OrderPage.delivery_slip_document))
          .then(() => client.pause(5000));
      });
      test('should check the Customer name of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
      test('should check the "Delivery Address" of the order ', () => {
        return promise
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', 'My Company'))
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', '16, Main street'))
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', '75002 Paris'))
          .then(() => client.checkDocument(global.downloadsFolderPath, 'deliveries', 'France'));
      });
      test('should check the "invoice Date" of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].invoiceDate));
      test('should check the "Order Reference" of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].OrderRef));
      test('should check the "Product Reference"of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductRef));
      test('should check the "Product Combination" of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductCombination));
      test('should check the "Product Quantity" of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductQuantity));
      test('should check the "Product Name" of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductName));
      test('should check the "total Price" of the order', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductTotal));
    }, 'order');
  }, 'order');

  scenario('Back to default behaviour', client => {
    test('should go to the Delivery Slips page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.delivery_slips_submenu));
    test('should change the "Delivery prefix"', () => client.waitAndSetValue(DeliverySlip.delivery_prefix, global.tab["deliveryPrefix"]));
    test('should change the "Delivery Number"', () => client.waitAndSetValue(DeliverySlip.delivery_number, global.tab['delivNumber']));
    test('should Disable the product image', () => client.waitForExistAndClick(DeliverySlip.disable_product_image));
    test('should click on "Save" button', () => client.waitForExistAndClick(DeliverySlip.save_button));
    test('should delete the "delivery" file', () => client.deleteFile(downloadsFolderPath, "deliveries", ".pdf", 2000));
  }, 'order');

}, 'order', true);

