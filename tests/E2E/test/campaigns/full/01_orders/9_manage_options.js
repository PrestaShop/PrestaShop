const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {Invoices} = require('../../../selectors/BO/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');

const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');

const commonOrder = require('../../common_scenarios/order');
const commonProduct = require('../../common_scenarios/product');

let promise = Promise.resolve();

global.orderInformation = [];

let productData = [{
  name: 'MN1',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'att',
  taxRule: {
    value: 'FR Taux réduit (5.5%)'
  }
}, {
  name: 'MN2',
  quantity: "10",
  price: '10',
  image_name: 'image_test.jpg',
  reference: 'att',
  taxRule: {
    value: 'FR Taux réduit (10%)'
  }
}, {
  name: 'MN3',
  quantity: "10",
  price: '10',
  image_name: 'image_test.jpg',
  reference: 'att',
  taxRule: {
    value: 'FR Taux super réduit (2.1%)'
  }
}, {
  name: 'MN4',
  quantity: "10",
  price: '10',
  image_name: 'image_test.jpg',
  reference: 'att',
  taxRule: {
    value: 'FR Taux standard (20%)'
  }
}];

scenario('Manage options', () => {
  scenario('Open the browser', client => {
    test('should open the browser', () => client.open());
  }, 'order');

  scenario('Test Case 1: Check invoice generation when disabling the invoice option', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    commonOrder.createOrderFO();
    scenario('Disable invoice and check the invoice', client => {
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
      test('should go to "Order - Invoice" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.invoices_submenu));
      test('should close the symfony toolbar if exists', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.symfony_toolbar))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar);
            }
          });
      });
      test('should click on "NO" option for "Enable invoices"', () => client.waitForExistAndClick(Invoices.disable_enable_invoice.replace('%STATUS', 0)));
      test('should click on "Save" button', () => client.waitForExistAndClick(Invoices.save_button));
      test('should check the success message', () => client.checkTextValue(Invoices.success_msg, 'Update successful'));
      test('should go orders list', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should go the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
      test('should change order state to "Payment accepted"', () => client.changeOrderState(OrderPage, 'Payment accepted'));
      test('should click on "DOCUMENTS" tab', () => client.waitForExistAndClick(OrderPage.document_submenu));
      test('should check the visibility of the "There is no available document" message ', () => client.waitForVisible(OrderPage.empty_page_logo));
      test('should click on "Order" menu', () => client.waitForExistAndClick(Menu.Sell.Orders.orders_submenu));
      test('should Verify if there isn\'t the invoice logo in the "PDF" column', () => client.isNotExisting(OrderPage.pdf_icon.replace('%ORDER', 1)));
    }, 'order');
  }, 'order');

  scenario('Test Case 2: Check invoice generation when enabling the invoice option', client => {
    test('should login successfully in the Front Office', () => client.linkAccess(URL));
    commonOrder.createOrderFO();
    scenario('Enable invoice and check the PDF document', client => {
      test('should login successfully in the Back Office', () => client.linkAccess(URL + '/admin-dev'));
      test('should go to "Order - Invoice" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.invoices_submenu));
      test('should close the symfony toolbar if exists', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.symfony_toolbar))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar);
            }
          });
      });
      test('should click on "YES" option for "Enable invoices"', () => client.waitForExistAndClick(Invoices.disable_enable_invoice.replace('%STATUS', 1)));
      test('should click on "Save" button', () => client.waitForExistAndClick(Invoices.save_button));
      test('should check the success message', () => client.checkTextValue(Invoices.success_msg, 'Update successful'));
      test('should click on "Order" menu', () => client.waitForExistAndClick(Menu.Sell.Orders.orders_submenu));
      test('should go the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
      test('should change order state to "Payment accepted"', () => client.changeOrderState(OrderPage, 'Payment accepted'));
      test('should click on "DOCUMENTS" tab', () => client.waitForExistAndClick(OrderPage.document_submenu));
      test('should check the visibility of the "invoice column"', () => client.isNotExisting(OrderPage.empty_page_logo));
      test('should click on the invoice', () => client.waitForExistAndClick(OrderPage.invoice_document));
      test('should click on "Order" menu', () => client.waitForExistAndClick(Menu.Sell.Orders.orders_submenu));
      test('should Verify the visibility of the invoice logo in the "PDF" column', () => client.isExisting(OrderPage.pdf_icon.replace('%ORDER', 1)));
      test('should click on the "invoice" logo', () => client.waitForExistAndClick(OrderPage.pdf_icon.replace('%ORDER', 1)));
    }, 'order');
  }, 'order');

  scenario('Test Case 3: Check invoice generation when enabling the invoice option', client => {
    test('should go to "Invoice" page', () => client.waitForExistAndClick(Menu.Sell.Orders.invoices_submenu));
    test('should click on "YES" option for "Enable invoices"', () => client.waitForExistAndClick(Invoices.disable_enable_invoice.replace('%STATUS', 1)));
    test('should click on "YES" option for "tax breakdown"', () => client.waitForExistAndClick(Invoices.disable_enable_tax_breakdown.replace('%STATUS', 1)));
    test('should click on "Save" button', () => client.waitForExistAndClick(Invoices.save_button));
    test('should check the success message', () => client.checkTextValue(Invoices.success_msg, 'Update successful'));

    commonProduct.createProduct(AddProductPage, productData[0]);
    commonProduct.createProduct(AddProductPage, productData[1]);
    commonProduct.createProduct(AddProductPage, productData[2]);
    commonProduct.createProduct(AddProductPage, productData[3]);

    scenario('Order 8 products (2 qty of each) with 4 different taxes rules & unit price tax excluded with decimals ', client => {
      test('should login successfully in the Front Office', () => client.linkAccess(URL));
      test('should set the language of shop to "English"', () => client.changeLanguage());
      test('should search for the created product ' + productData[0]['name'] + date_time, () => client.waitAndSetValue(SearchProductPage.search_input, productData[0]['name'] + date_time));
      test('should click on "Loop" icon', () => client.waitForExistAndClick(SearchProductPage.search_button));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should set the "quantity" input', () => client.waitAndSetValue(productPage.first_product_quantity, 2));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should search for the created product ' + productData[1]['name'] + date_time, () => client.waitAndSetValue(SearchProductPage.search_input, productData[1]['name'] + date_time));
      test('should click on "Loop" icon', () => client.waitForExistAndClick(SearchProductPage.search_button));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should set the "quantity" input', () => client.waitAndSetValue(productPage.first_product_quantity, 2));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should search for the created product ' + productData[2]['name'] + date_time, () => client.waitAndSetValue(SearchProductPage.search_input, productData[2]['name'] + date_time));
      test('should click on "Loop" icon', () => client.waitForExistAndClick(SearchProductPage.search_button));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should set the "quantity" input', () => client.waitAndSetValue(productPage.first_product_quantity, 2));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should search for the created product ' + productData[3]['name'] + date_time, () => client.waitAndSetValue(SearchProductPage.search_input, productData[3]['name'] + date_time));
      test('should click on "Loop" icon', () => client.waitForExistAndClick(SearchProductPage.search_button));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should set the "quantity" input', () => client.waitAndSetValue(productPage.first_product_quantity, 2));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should click on "COMMANDER" button', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
      test('should click on confirm address button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step2_continue_button));
      test('should choose shipping method my carrier', () => client.waitForExistAndClick(CheckoutOrderPage.shipping_method_option));
      test('should create message', () => client.waitAndSetValue(CheckoutOrderPage.message_textarea, 'Order message test'));
      test('should click on "confirm delivery" button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step3_continue_button));
      test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step4_payment_radio));
      test('should set "the condition to approve"', () => client.waitForExistAndClick(CheckoutOrderPage.condition_check_box));
      test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(CheckoutOrderPage.confirmation_order_button));
      test('should check the order confirmation', () => client.checkTextValue(CheckoutOrderPage.confirmation_order_message, 'YOUR ORDER IS CONFIRMED', "contain"));
    }, 'order');

    scenario('Get order information', client => {
      test('should login successfully in the Back Office', () => client.linkAccess(URL + '/admin-dev'));
      test('should click on "Order" menu', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should go the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
      test('should change order state to "Payment accepted"', () => client.changeOrderState(OrderPage, 'Payment accepted'));
      test('should get all order information', () => {
        return promise
          .then(() => client.getTextInVar(OrderPage.order_date, "invoiceDate"))
          .then(() => client.getTextInVar(OrderPage.order_ref, "OrderRef"))
          .then(() => {
            client.getTextInVar(OrderPage.product_information, "ProductRef").then(() => {
              global.tab['ProductRef'] = global.tab['ProductRef'].split('\n')[1];
              global.tab['ProductRef'] = global.tab['ProductRef'].substring(18);
            })
          })
          .then(() => client.pause(2000))
          .then(() => {
            client.getTextInVar(OrderPage.product_information, "ProductCombination").then(() => {
              global.tab['ProductCombination'] = global.tab['ProductCombination'].split('\n')[0];
              global.tab['ProductCombination'] = global.tab['ProductCombination'].split(':')[1];
            })
          })
          .then(() => client.pause(2000))
          .then(() => client.getTextInVar(OrderPage.product_quantity, "ProductQuantity"))
          .then(() => client.getTextInVar(OrderPage.product_name_tab, "ProductName"))
          .then(() => client.getTextInVar(OrderPage.total_order_price, "TotalPrice"))
          .then(() => client.displayHiddenBlock('product_price_edit'))
          .then(() => client.getAttributeInVar(OrderPage.product_unit_price, "value", "ProductUnitPrice"))
          .then(() => client.getAttributeInVar(OrderPage.product_price, "value", "ProductPrice"))
          .then(() => {
            global.tab["ProductTaxRate"] = ['5.5', '10', '2.1', '20'];
          })
          .then(() => client.getTextInVar(OrderPage.total_product, "TotalProduct"))
          .then(() => client.getTextInVar(OrderPage.shipping_cost_price, "ShippingCost"))
          .then(() => client.getTextInVar(OrderPage.total, "Total"))
          .then(() => client.getTextInVar(OrderPage.total_tax, "TotalTax"))
          .then(() => client.getTextInVar(OrderPage.carrier, "Carrier"))
          .then(() => client.getTextInVar(OrderPage.payment_method, "PaymentMethod"))
          .then(() => {
            global.orderInformation[0] = {
              "invoiceDate": global.tab['invoiceDate'],
              "OrderRef": global.tab['OrderRef'],
              "ProductRef": global.tab['ProductRef'],
              "ProductName": global.tab['ProductName'],
              "ProductCombination": global.tab['ProductCombination'],
              "ProductQuantity": global.tab['ProductQuantity'],
              "TotalPrice": global.tab['TotalPrice'],
              "ProductUnitPrice": global.tab['ProductUnitPrice'],
              "ProductPrice": global.tab['ProductPrice'],
              "ProductTaxRate": global.tab['ProductTaxRate'],
              "TotalProduct": global.tab['TotalProduct'],
              "ShippingCost": global.tab['ShippingCost'],
              "Total": global.tab['Total'],
              "TotalTax": global.tab['TotalTax'],
              "Carrier": global.tab['Carrier'],
              "PaymentMethod": global.tab['PaymentMethod']
            }
          });
      });
    }, 'order');

    scenario('Check downloaded invoice', client => {
      test('should click on "DOCUMENTS" subtab', () => client.waitForVisibleAndClick(OrderPage.document_submenu));
      test('should download the invoice document', () => client.downloadDocument(OrderPage.download_invoice_button));
      test('should check the "invoice file name" ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName));
      test('should check the Customer name', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE'));
      test('should check the "Delivery Address " ', () => {
        return promise
          .then(() => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'My Company'))
          .then(() => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, '16, Main street'))
          .then(() => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, '75002 Paris'))
          .then(() => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'France'));
      });
      test('should check the "Order Reference" ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].OrderRef));
      test('should check the "invoice Date" ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].invoiceDate));
      test('should check the "Product Reference"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductRef));
      for (let i = 0; i <= 3; i++) {
        test('should check the ' + i + ' Product Name ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductName[i]));
        test('should check the "Unit Price" of the ' + i + ' product ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductUnitPrice[i]));
        test('should check the "Tax Rate" of the ' + i + ' product ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductTaxRate[i]));
        test('should check the "Product Quantity" of the ' + i + ' product ', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ProductQuantity[i]));
      }
      test('should check the "Total"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].Total));
      test('should check the "Total Tax"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].TotalTax));
      test('should check the "Total Product"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].TotalProduct));
      test('should check the "Shipping Cost"', () => client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.orderInformation[0].ShippingCost));
    }, 'order');

  }, 'order');


  scenario('Test Case 4: Check downloaded invoice', client => {
    test('should go to "Invoice" page', () => client.waitForExistAndClick(Menu.Sell.Orders.invoices_submenu));
    test('should click on "Yes" option for "Enable product image "', () => client.waitForExistAndClick(Invoices.disable_enable_image.replace('%STATUS', 1)));
    test('should click on "Save" button', () => client.waitForExistAndClick(Invoices.save_button));
    test('should check the success message', () => client.checkTextValue(Invoices.success_msg, 'Update successful'));

  }, 'order');

}, 'order');
