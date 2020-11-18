/**
 * This script is based on the scenario described in this test link
 * [id="PS-31"][Name="Print Invoice"]
 **/

const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CustomerSettings} = require('../../../selectors/BO/shopParameters/customer_settings');
const {OrderPage} = require('../../../selectors/BO/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Taxes} = require('../../../selectors/BO/international/taxes');
const commonProduct = require('../../common_scenarios/product');
const {HomePage} = require('../../../selectors/FO/home_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const welcomeScenarios = require('../../common_scenarios/welcome');
let promise = Promise.resolve();
let dateFormat = require('dateformat');
let dateSystem = dateFormat(new Date(), 'mm/dd/yyyy');
global.orderInfo = [];
let productData = [
  {
    name: 'P1_New',
    quantity: '10',
    price: '7.00',
    tax_rule: '20%',
    description: '',
    image_name: 'image_test.jpg',
    reference: 'test_1',
    type: 'combination',
    attribute: {
      1: {
        name: 'color',
        variation_quantity: '10'
      }
    }
  }, {
    name: 'P2_New',
    quantity: '10',
    price: '9.00',
    tax_rule: '10%',
    description: '',
    image_name: 'image_test.jpg',
    reference: 'test_2',
    type: 'combination',
    attribute: {
      1: {
        name: 'color',
        variation_quantity: '10'
      }
    }
  },
  {
    name: 'P3_New',
    quantity: '10',
    price: '13.00',
    tax_rule: '5.5%',
    description: '',
    image_name: 'image_test.jpg',
    reference: 'test_3',
    type: 'combination',
    attribute: {
      1: {
        name: 'color',
        variation_quantity: '10'
      }
    }
  }
];

scenario('Print the invoice of an order', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Change the Customer Group tax parameter and the tax option', client => {
    test('should go to "Shop Parameters > Customer settings" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu));
    test('should click on "Group" tab', () => client.waitForExistAndClick(CustomerSettings.groups.group_button));
    test('should click on customer "Edit" button', () => client.waitForExistAndClick(CustomerSettings.groups.customer_edit_button));
    test('should select "Tax excluded" option for "Price display method"', () => client.waitAndSelectByValue(CustomerSettings.groups.price_display_method, "1"));
    test('should click on "Save" button', () => client.waitForExistAndClick(CustomerSettings.groups.save_button));
    test('should go to "International > Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
    test('should display tax in the shopping cart', () => client.waitForExistAndClickJs(Taxes.taxes.display_tax.replace('%D', '1')));
    test('should click on "Save" button', () => client.waitForExistAndClickJs(Taxes.taxes.save_button,false));
  }, 'order');

  for (let i = 0; i <= 2; i++) {
    commonProduct.createProduct(AddProductPage, productData[i]);
  }

  scenario('Create order in the Front Office', () => {
    scenario('Connect to the Front Office', client => {
      test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    }, 'common_client');

    scenario('Create order in the Front Office', client => {
      test('should set the language of shop to "English"', () => client.changeLanguage());
      for (let i = 0; i < productData.length; i++) {
        test('should search for a product ' + productData[i].name + global.date_time + '', () => {
          return promise
            .then(() => client.waitAndSetValue(HomePage.search_input, productData[i].name + date_time))
            .then(() => client.waitForExistAndClick(HomePage.search_icon, 2000))
            .then(() => client.waitForExistAndClick(productPage.productLink.replace('%PRODUCTNAME', productData[i].name + date_time)));
        });
        test('should select product "size M"', () => client.waitAndSelectByValue(productPage.first_product_size, '2'));
        test('should set the product "quantity"', () => {
          return promise
            .then(() => client.setInputValue(productPage.first_product_quantity, "4"))
            .then(() => client.getTextInVar(CheckoutOrderPage.product_current_price, "basic_price_" + i));
        });
        test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
        if (productData.length - 1 !== i) {
          test('should click on "CONTINUE SHOPPING" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.continue_shopping_button));
        }
        else {
          test('should click on "PROCEED TO CHECKOUT" button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
        }
      }
      test('should set the quantity to "4" using the keyboard', () => client.setInputValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), '4'));
      test('should click on "PROCEED TO CHECKOUT" button 2', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
      test('should click on confirm address button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step2_continue_button));
      test('should choose shipping method my carrier', () => client.waitForExistAndClick(CheckoutOrderPage.shipping_method_option, 2000));
      test('should create message', () => client.waitAndSetValue(CheckoutOrderPage.message_textarea, 'Order message test', 1000));
      test('should click on "confirm delivery" button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step3_continue_button));
      test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step4_payment_radio));
      test('should set "the condition to approve"', () => client.waitForExistAndClick(CheckoutOrderPage.condition_check_box));
      test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(CheckoutOrderPage.confirmation_order_button));
      test('should check the order confirmation', () => {
        return promise
          .then(() => client.checkTextValue(CheckoutOrderPage.confirmation_order_message, 'YOUR ORDER IS CONFIRMED', "contain"))
          .then(() => client.getTextInVar(CheckoutOrderPage.order_total_price, "total_price"))
          .then(() => client.getTextInVar(CheckoutOrderPage.order_shipping_prince_value, "shipping_price"))
          .then(() => client.getTextInVar(CheckoutOrderPage.order_total_tax, "total_tax"))
          .then(() => client.getTextInVar(CheckoutOrderPage.order_total_tax_excl_value, "total_tax_excl"))
          .then(() => client.getTextInVar(CheckoutOrderPage.order_amount, "total_amount"))
          .then(() => client.getTextInVar(CheckoutOrderPage.order_reference, "reference", true))
          .then(() => client.getTextInVar(CheckoutOrderPage.payment_method, "payment_method", true))
          .then(() => client.getTextInVar(CheckoutOrderPage.shipping_method, "method", true));
      });
      for (let i = 0; i < productData.length; i++) {
        test('should get the product ' + productData[i].name + '  Information', () => {
          return promise
            .then(() => client.getTextInVar(CheckoutOrderPage.product_combination.replace('%I', i + 1), "product_combination_" + i))
            .then(() => client.getTextInVar(CheckoutOrderPage.quantity_product.replace('%I', i + 1), "quantity_product_" + i))
            .then(() => client.getTextInVar(CheckoutOrderPage.total_product.replace('%I', i + 1), "total_product_" + i))
        });
      }
      for (let i = 0; i < productData.length; i++) {
        test('should check the basic price for product ' + productData[i].name + '', () => client.checkTextValue(CheckoutOrderPage.basic_price_product.replace('%I', i + 1), global.tab["basic_price_" + i], 'contain'));
      }
    }, 'common_client');

    scenario('Logout from the Front Office', client => {
      test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
    }, 'order');

  }, 'order', true);

  scenario('Check the created order in the Back Office', () => {
    scenario('Open the browser and connect to the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'order');

    scenario('Change the status to "Payment Accepted"', client => {
      test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should search for the created order by reference', () => client.waitAndSetValue(OrderPage.search_by_reference_input, global.tab['reference']));
      test('should click on "Search" button', () => client.waitForExistAndClick(OrderPage.search_order_button));
      test('should go to the order', () => client.scrollWaitForExistAndClick(OrderPage.view_order_button.replace('%NUMBER', 1), 150, 2000));
      test('should check that the status is "Awaiting bank wire payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting bank wire payment'));
      test('should set order status to "Payment accepted"', () => client.updateStatus('Payment accepted'));
      test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
      /**
       * should refresh the page, to pass the error
       */
      test('should refresh the page', () => client.refresh());
      test('should check that the status is "Payment accepted"', () => client.checkTextValue(OrderPage.order_status, 'Payment accepted'));
    }, 'order');

    scenario('Print invoice then check information', client => {
      test('should click on "View Invoice" button', async () => {
        await client.waitForVisible(OrderPage.view_invoice_button);
        // for headless, we need to remove attribute 'target' to avoid download in a new Tab
        if(global.headless)  await client.removeAttribute(OrderPage.view_invoice_button,'target');
        await client.waitForExistAndClick(OrderPage.view_invoice_button)
      });
      test('should click on "DOCUMENTS" tab', () => client.waitForVisibleAndClick(OrderPage.document_submenu));
      test('should get the invoice information', () => {
        return promise
          .then(() => client.getTextInVar(OrderPage.date_invoice, "date_invoice"))
          .then(() => client.getNameInvoice(OrderPage.download_invoice_button))
      });
      test('should check the "invoice file name"', async () => {
        await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf', 1000);
        if (global.existingFile) {
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName)
        }
      });
      test('should check that the "invoice customer" is : John DOE', async () => {
        await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
        if (global.existingFile) {
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'John DOE');
        }
      });
      test('should check the "Delivery & Billing Address" information', async () => {
        await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
        if (global.existingFile) {
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'My Company');
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, '16, Main street');
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, '75002 Paris');
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'France');
        }
      });
      test('should check the "invoice information"', async () => {
        await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
        if (global.existingFile) {
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.invoiceFileName);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, dateSystem);
          await client.checkWordNumber(global.downloadsFolderPath, global.invoiceFileName, dateSystem, 3);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab['reference']);
          for (let i = 0; i <= 2; i++) {
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, productData[i].reference);
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, productData[i].name);
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, productData[i].tax_rule.split('%')[0]);
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["basic_price_" + i]);
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["quantity_product_" + i]);
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["total_product_" + i]);
            await client.checkWordNumber(global.downloadsFolderPath, global.invoiceFileName, global.tab["total_product_" + i], 2);
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, parseFloat(productData[i].tax_rule.split('%')[0]).toFixed(3));
            await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, (Number(global.tab["total_product_" + i].split('€')[1]) * Number(productData[i].tax_rule.split('%')[0])) / 100);
          }
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["total_price"]);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["shipping_price"]);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["total_tax"]);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["total_tax_excl"]);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["total_amount"]);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab["payment_method"]);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, global.tab['method'].split('\n')[0]);
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'Size : M-');
          await client.checkDocument(global.downloadsFolderPath, global.invoiceFileName, 'Beige');
          await client.checkWordNumber(global.downloadsFolderPath, global.invoiceFileName, 'Size : M-', 3);
          await client.checkWordNumber(global.downloadsFolderPath, global.invoiceFileName, 'Beige', 3);
        }
      });
      test('should delete the invoice file', async () => {
        await client.checkFile(global.downloadsFolderPath, global.invoiceFileName + '.pdf');
        if (existingFile) {
          await client.deleteFile(global.downloadsFolderPath, global.invoiceFileName, ".pdf", 2000);
        }
      });
    }, 'order');
  }, 'order');

  scenario('Change the Customer Group tax parameter', client => {

    test('should go to "Shop Parameters > Customer settings" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu));
    test('should click on "Group" tab', () => client.waitForExistAndClick(CustomerSettings.groups.group_button));
    test('should click on customer "Edit" button', () => client.waitForExistAndClick(CustomerSettings.groups.customer_edit_button));
    test('should select "Tax included" option for "Price display method"', () => client.waitAndSelectByValue(CustomerSettings.groups.price_display_method, "0"));
    test('should click on "Save" button', () => client.waitForExistAndClick(CustomerSettings.groups.save_button));
    test('should go to "International > Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
    test('should in display tax in the shopping cart', () => client.waitForExistAndClickJs(Taxes.taxes.display_tax.replace('%D', '0')));
    test('should click on "Save" button', () => client.waitForExistAndClickJs(Taxes.taxes.save_button,false));
  }, 'order');
}, 'order', true);
