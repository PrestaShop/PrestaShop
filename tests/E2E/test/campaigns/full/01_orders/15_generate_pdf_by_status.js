/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CustomerSettings} = require('../../../selectors/BO/shopParameters/customer_settings');
const {OrderPage} = require('../../../selectors/BO/order');
const {Invoices} = require('../../../selectors/BO/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonOrder = require('../../common_scenarios/order');
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
    scenario('Change the Customer Group tax parameter', client => {
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
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
          .then(() => client.pause(2000))
          .then(() => client.isVisible(AddProductPage.symfony_toolbar))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar)
            }
          })
      });
      test('should click on "Generate PDF by status"', () => client.waitForExistAndClick(Invoices.generate_pdf_by_status_button));
      test('should wait for the "invoice" to download', () => client.pause(7000));
      for (let i = 1; i <= 2; i++) {
        commonOrder.checkOrderInvoice(client, i)
      }
      test('should delete the invoice pdf file', () => client.deleteFile(global.downloadsFolderPath, 'invoices.pdf'));
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
      test('should login successfully in the Back Office', () => client.linkAccess(URL+'/admin-dev'));
      test('should go to "Product settings" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      for (let i = 1; i <= 2; i++) {
        test('should go the order n°' + i, () => client.waitForExistAndClick(OrderPage.order_view_button.replace("%ORDERNumber", i)));
        if (i === 1) {
          test('should change order state to "Awaiting check payment"', () => client.changeOrderState(OrderPage, 'Awaiting check payment'));
        } else {
          test('should change order state to "Canceled"', () => client.changeOrderState(OrderPage, 'Canceled'));
        }
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
      test('should delete the invoice pdf file', () => client.deleteFile(global.downloadsFolderPath, 'invoices.pdf'));
    }, 'order');
  }, 'order');

}, 'order', true);