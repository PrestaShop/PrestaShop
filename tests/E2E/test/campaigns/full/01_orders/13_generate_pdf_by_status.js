/**
 * This script is based on the scenario described in this test link
 * [id="PS-90"][Name="Generate a PDF by order status"]
 **/

const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CustomerSettings} = require('../../../selectors/BO/shopParameters/customer_settings');
const {OrderPage} = require('../../../selectors/BO/order');
const {Invoices} = require('../../../selectors/BO/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonOrder = require('../../common_scenarios/order');
const {OnBoarding} = require('../../../selectors/BO/onboarding');
const welcomeScenarios = require('../../common_scenarios/welcome');

let promise = Promise.resolve();

global.orderInfo = [];

scenario('Generate a PDF by status', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'order');
  scenario('Generate a PDF by "Awaiting bank wire payment" status', client => {
    scenario('Create order in front office', client => {
      commonOrder.createOrderFO();
      scenario('Go to the home page', client => {
        test('should go to the Home Page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
      }, 'order');
      commonOrder.createOrderFO();
    }, 'order');
    scenario('Open the browser and login successfully in the Back Office ', client => {
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'common_client');
    welcomeScenarios.findAndCloseWelcomeModal();
    scenario('Change the Customer Group tax parameter', client => {
      test('should go to "Product settings" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu));
      test('should click on "Group" tab', () => client.waitForExistAndClick(CustomerSettings.groups.group_button));
      test('should click on customer "Edit" button', () => client.waitForExistAndClick(CustomerSettings.groups.customer_edit_button));
      test('should select "Tax excluded" option for "Price display method"', () => client.waitAndSelectByValue(CustomerSettings.groups.price_display_method, "1"));
      test('should click on "Save" button', () => client.waitForExistAndClick(CustomerSettings.groups.save_button));
    }, 'order');
    scenario('Get the created Orders information', client => {
      test('should go to "Product settings" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      for (let i = 1; i <= 2; i++) {
        test('should go the order n°' + i, () => client.waitForExistAndClick(OrderPage.order_view_button.replace("%ORDERNumber", i)));
        /**
         * should refresh the page, to pass the error
         */
        test('should refresh the page', () => client.refresh());
        test('should go to "Documents" tab', () => client.waitForExistAndClick(OrderPage.documents_tab));
        test('should click on "Generate invoice" button', () => client.waitForExistAndClick(OrderPage.generate_invoice_button));
        test('should verify the success message', () => client.waitForVisible(OrderPage.success_msg));
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
            .then(() => client.getTextInVar(OrderPage.total_order_price, "TotalPrice"))
            .then(() => client.displayHiddenBlock('product_price_edit'))
            .then(() => client.getAttributeInVar(OrderPage.product_unit_price, "value", "ProductUnitPrice"))
            .then(() => client.getAttributeInVar(OrderPage.product_price, "value", "ProductPrice"))
            .then(() => {
              global.tab["ProductTaxRate"] = Math.round(((global.tab["ProductPrice"] - global.tab["ProductUnitPrice"]) / global.tab["ProductUnitPrice"]) * 100);
            })
            .then(() => client.getTextInVar(OrderPage.total_product, "TotalProduct"))
            .then(() => client.getTextInVar(OrderPage.shipping_cost_price, "ShippingCost"))
            .then(() => client.getTextInVar(OrderPage.total, "Total"))
            .then(() => client.getTextInVar(OrderPage.total_tax, "TotalTax"))
            .then(() => client.getTextInVar(OrderPage.carrier, "Carrier"))
            .then(() => {
              global.orderInfo[i - 1] = {
                "invoiceDate": global.tab['invoiceDate'],
                "OrderRef": global.tab['OrderRef'],
                "ProductRef": global.tab['ProductRef'],
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
                "Carrier": global.tab['Carrier']
              }
            });
        });
        test('should go to "Product settings" page', () => client.waitForExistAndClick(Menu.Sell.Orders.orders_submenu));
      }
    }, 'order');
    scenario('Generate a PDF by status "Awaiting bank wire payment" and check information', client => {
      test('should go to "Orders - Invoices" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.invoices_submenu));
      test('should click on "Awaiting bank wire payment" option', () => client.waitForExistAndClick(OrderPage.awaiting_bank_wire_payment_option));
      test('should close the symfony toolbar ', () => {
        return promise
          .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
      });
      test('should click on "Generate PDF by status"', () => client.waitForExistAndClick(Invoices.generate_pdf_by_status_button));
      test('should wait for the "invoice" to download', () => client.pause(7000));
      for (let i = 1; i <= 2; i++) {
        commonOrder.checkOrderInvoice(client, i)
      }
      test('should delete the invoice pdf file', async () =>{
        await client.checkFile(global.downloadsFolderPath, 'invoices.pdf');
        if (global.existingFile) {
          await  client.deleteFile(global.downloadsFolderPath, 'invoices', '.pdf');
        }
      });
    }, 'order');
  }, 'order');

  scenario('Generate a PDF by "Awaiting check payment" and "Cancelled" status', client => {
    scenario('Login in the Front Office', client => {
      test('should login successfully in the Front Office', () => client.linkAccess(URL));
    }, 'order');
    scenario('Create order in Front Office', client => {
      commonOrder.createOrderFO();
      scenario('Go to the home page', client => {
        test('should go to the Home Page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
      }, 'order');
      commonOrder.createOrderFO();
    }, 'order');
    scenario('Go to the created order, change status and Get all information', client => {
      test('should login successfully in the Back Office', () => client.linkAccess(URL + '/admin-dev'));
      test('should go to "Product settings" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      for (let i = 1; i <= 2; i++) {
        test('should go the order n°' + i, () => client.waitForExistAndClick(OrderPage.order_view_button.replace("%ORDERNumber", i)));
        if (i === 1) {
          test('should change order state to "Awaiting check payment"', () => client.changeOrderState(OrderPage, 'Awaiting check payment'));
        } else {
          test('should change order state to "Canceled"', () => client.changeOrderState(OrderPage, 'Canceled'));
        }
        /**
         * should refresh the page, to pass the error
         */
        test('should refresh the page', () => client.refresh());
        test('should go to "Documents" tab', () => client.waitForExistAndClick(OrderPage.documents_tab));
        test('should click on "Generate invoice" button', () => client.waitForExistAndClick(OrderPage.generate_invoice_button));
        test('should verify the success message', () => client.waitForVisible(OrderPage.success_msg));
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
            .then(() => client.getTextInVar(OrderPage.total_order_price, "TotalPrice"))
            .then(() => client.displayHiddenBlock('product_price_edit'))
            .then(() => client.getAttributeInVar(OrderPage.product_unit_price, "value", "ProductUnitPrice"))
            .then(() => client.getAttributeInVar(OrderPage.product_price, "value", "ProductPrice"))
            .then(() => {
              global.tab["ProductTaxRate"] = Math.round(((global.tab["ProductPrice"] - global.tab["ProductUnitPrice"]) / global.tab["ProductUnitPrice"]) * 100);
            })
            .then(() => client.getTextInVar(OrderPage.total_product, "TotalProduct"))
            .then(() => client.getTextInVar(OrderPage.shipping_cost_price, "ShippingCost"))
            .then(() => client.getTextInVar(OrderPage.total, "Total"))
            .then(() => client.getTextInVar(OrderPage.total_tax, "TotalTax"))
            .then(() => client.getTextInVar(OrderPage.carrier, "Carrier"))
            .then(() => {
              global.orderInfo[i - 1] = {
                "invoiceDate": global.tab['invoiceDate'],
                "OrderRef": global.tab['OrderRef'],
                "ProductRef": global.tab['ProductRef'],
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
                "Carrier": global.tab['Carrier']
              }
            });
        });
        test('should go to "Product settings" page', () => client.waitForExistAndClick(Menu.Sell.Orders.orders_submenu));
      }
    }, 'order');
    scenario('Generate a PDF by status and check it', client => {
      test('should go to "Orders - Invoices" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.invoices_submenu));
      test('should click on "Awaiting bank wire payment" option', () => client.waitForExistAndClick(OrderPage.awaiting_check_payment));
      test('should click on "Cancelled" option', () => client.waitForExistAndClick(OrderPage.cancelled_option));
      test('should click on "Generate PDF by status"', () => client.waitForExistAndClick(Invoices.generate_pdf_by_status_button));
      test('should wait for the "invoice" to download', () => client.pause(7000));
      for (let i = 1; i <= 2; i++) {
        commonOrder.checkOrderInvoice(client, i)
      }
      test('should delete the invoice pdf file', async () => {
        await client.checkFile(global.downloadsFolderPath, 'invoices.pdf');
        if (global.existingFile) {
         await client.deleteFile(global.downloadsFolderPath, 'invoices', '.pdf');
        }
      });
    }, 'order');
  }, 'order');
  scenario('Close symfony toolbar then click on "Stop the OnBoarding" button', client => {
    test('should close symfony toolbar', () => {
      return promise
        .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
    });
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button))
        .then(() => client.pause(2000));
    });
  }, 'onboarding');
  scenario('Back Customer Group tax parameter to default behaviour', client => {
    test('should go to "Customer settings" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu));
    test('should click on "Group" tab', () => client.waitForExistAndClick(CustomerSettings.groups.group_button));
    test('should click on customer "Edit" button', () => client.waitForExistAndClick(CustomerSettings.groups.customer_edit_button));
    test('should select "Tax included" option for "Price display method"', () => client.waitAndSelectByValue(CustomerSettings.groups.price_display_method, "0"));
    test('should click on "Save" button', () => client.waitForExistAndClick(CustomerSettings.groups.save_button));
  }, 'order');
}, 'order', true);

