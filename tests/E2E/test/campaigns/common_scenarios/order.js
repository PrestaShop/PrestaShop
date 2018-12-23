const {productPage} = require('../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../selectors/FO/order_page');
const {accountPage} = require('../../selectors/FO/add_account_page');
const {OrderPage} = require('../../selectors/BO/order');
const {Menu} = require('../../selectors/BO/menu.js');
const {ShoppingCart} = require('../../selectors/BO/order');

const {CreditSlip} = require('../../selectors/BO/order');
const {ProductList} = require('../../selectors/BO/add_product_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {MerchandiseReturns} = require('../../selectors/BO/Merchandise_returns');

let dateFormat = require('dateformat');
let data = require('../../datas/customer_and_address_data');
let promise = Promise.resolve();

global.orderInformation = [];

module.exports = {
  createOrderFO: function (authentication = "connected", login = 'pub@prestashop.com', password = '123456789') {
    scenario('Create order in the Front Office', client => {
      test('should set the language of shop to "English"', () => client.changeLanguage());
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product, 2000));
      test('should select product "size M" ', () => client.waitAndSelectByValue(productPage.first_product_size, '2'));
      test('should select product "color Black"', () => client.waitForExistAndClick(productPage.first_product_color));
      test('should set the product "quantity"', () => {
        return promise
          .then(() => client.waitAndSetValue(productPage.first_product_quantity, "4"))
          .then(() => client.getTextInVar(CheckoutOrderPage.product_current_price, "first_basic_price"));
      });
      test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should set the quantity to "4" using the keyboard', () => client.waitAndSetValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), '4'));
      test('should click on proceed to checkout button 2', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));

      if (authentication === "create_account" || authentication === "guest") {
        scenario('Create new account', client => {
          test('should choose a "Social title"', () => client.waitForExistAndClick(accountPage.gender_radio_button));
          test('should set the "First name" input', () => client.waitAndSetValue(accountPage.firstname_input, data.customer.firstname));
          test('should set the "Last name" input', () => client.waitAndSetValue(accountPage.lastname_input, data.customer.lastname));
          if (authentication === "create_account") {
            test('should set the "Email" input', () => client.waitAndSetValue(accountPage.new_email_input, data.customer.email.replace("%ID", date_time)));
            test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_account_input, data.customer.password));
          } else {
            test('should set the "Email" input', () => client.waitAndSetValue(accountPage.new_email_input, data.customer.email.replace("%ID", '_guest' + date_time)));
          }
          test('should click on "CONTINUE" button', () => client.waitForExistAndClick(accountPage.new_customer_btn));
        }, 'common_client');

        scenario('Create new address', client => {
          test('should set the "Address" input', () => client.waitAndSetValue(accountPage.adr_address, data.address.address));
          test('should set the "Zip/Postal Code" input', () => client.waitAndSetValue(accountPage.adr_postcode, data.address.postalCode));
          test('should set the "City" input', () => client.waitAndSetValue(accountPage.adr_city, data.address.city));
          test('should click on "CONTINUE" button', () => client.scrollWaitForExistAndClick(accountPage.new_address_btn));
        }, 'common_client');
      }

      if (authentication === "connect") {
        scenario('Login with existing customer', client => {
          test('should click on "Sign in"', () => client.waitForExistAndClick(accountPage.sign_tab));
          test('should set the "Email" input', () => client.waitAndSetValue(accountPage.signin_email_input, login));
          test('should set the "Password" input', () => client.waitAndSetValue(accountPage.signin_password_input, password));
          test('should click on "CONTINUE" button', () => client.waitForExistAndClick(accountPage.continue_button));
        }, 'common_client');
      }

      if (login !== 'pub@prestashop.com') {
        scenario('Add new address', client => {
          test('should set the "company" input', () => client.waitAndSetValue(CheckoutOrderPage.company_input, 'prestashop'));
          test('should set "VAT number" input', () => client.waitAndSetValue(CheckoutOrderPage.vat_number_input, '0123456789'));
          test('should set "Address" input', () => client.waitAndSetValue(CheckoutOrderPage.address_input, '12 rue d\'amsterdam'));
          test('should set "Second address" input', () => client.waitAndSetValue(CheckoutOrderPage.address_second_input, 'RDC'));
          test('should set "Postal code" input', () => client.waitAndSetValue(CheckoutOrderPage.zip_code_input, '75009'));
          test('should set "City" input', () => client.waitAndSetValue(CheckoutOrderPage.city_input, 'Paris'));
          test('should set "Pays" input', () => client.waitAndSelectByVisibleText(CheckoutOrderPage.country_input, 'France'));
          test('should set "Home phone" input', () => client.waitAndSetValue(CheckoutOrderPage.phone_input, '0123456789'));
          test('should click on "Use this address for invoice too', () => client.waitForExistAndClick(CheckoutOrderPage.use_address_for_facturation_input));
          test('should click on "CONTINUE', () => client.waitForExistAndClick(accountPage.new_address_btn));

          scenario('Add Invoice Address', client => {
            test('should set the "company" input', () => client.waitAndSetValue(CheckoutOrderPage.invoice_company_input, 'prestashop'));
            test('should set "VAT number" input', () => client.waitAndSetValue(CheckoutOrderPage.invoice_vat_number_input, '0123456789'));
            test('should set "Address" input', () => client.waitAndSetValue(CheckoutOrderPage.invoice_address_input, '12 rue d\'amsterdam'));
            test('should set "Second address" input', () => client.waitAndSetValue(CheckoutOrderPage.invoice_address_second_input, 'RDC'));
            test('should set "Postal code" input', () => client.waitAndSetValue(CheckoutOrderPage.invoice_zip_code_input, '75009'));
            test('should set "City" input', () => client.waitAndSetValue(CheckoutOrderPage.invoice_city_input, 'Paris'));
            test('should set "Pays" input', () => client.waitAndSelectByVisibleText(CheckoutOrderPage.invoice_country_input, 'France'));
            test('should set "Home phone" input', () => client.waitAndSetValue(CheckoutOrderPage.invoice_phone_input, '0123456789'));
            test('should click on "CONTINUE" button', () => client.waitForExistAndClick(accountPage.new_address_btn));
          }, 'order');

        }, 'common_client');
      }

      if (authentication === "connected" || authentication === "connect") {
        if (login === 'pub@prestashop.com') {
          scenario('Choose the personal and delivery address ', client => {
            test('should click on confirm address button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step2_continue_button));
          }, 'common_client');
        }
      }

      scenario('Choose "SHIPPING METHOD"', client => {
        test('should choose shipping method my carrier', () => client.waitForExistAndClick(CheckoutOrderPage.shipping_method_option));
        test('should create message', () => client.waitAndSetValue(CheckoutOrderPage.message_textarea, 'Order message test'));
        test('should click on "confirm delivery" button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step3_continue_button));
      }, 'common_client');

      scenario('Choose "PAYMENT" method', client => {
        test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step4_payment_radio));
        test('should set "the condition to approve"', () => client.waitForExistAndClick(CheckoutOrderPage.condition_check_box));
        test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(CheckoutOrderPage.confirmation_order_button));
        test('should check the order confirmation', () => {
          return promise
            .then(() => client.checkTextValue(CheckoutOrderPage.confirmation_order_message, 'YOUR ORDER IS CONFIRMED', "contain"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_product, "product"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_basic_price, "basic_price"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_total_price, "total_price"))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_reference, "reference", true))
            .then(() => client.getTextInVar(CheckoutOrderPage.shipping_method, "method", true))
            .then(() => client.getTextInVar(CheckoutOrderPage.order_shipping_prince_value, "shipping_price"))
        });
        /**
         * This scenario is based on the bug described in this ticket
         * http://forge.prestashop.com/browse/BOOM-3886
         */
        test('should check that the basic price is equal to "22,94 €" (BOOM-3886)', () => client.checkTextValue(CheckoutOrderPage.order_basic_price, global.tab["first_basic_price"]));
        /**** END ****/
      }, 'common_client');
    }, 'common_client');
  },
  createOrderBO: function (OrderPage, CreateOrder, productData) {
    scenario('Create order in the Back Office', client => {
      test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should click on "Add new order" button', () => client.waitForExistAndClick(CreateOrder.new_order_button, 1000));
      test('should search for a customer', () => client.waitAndSetValue(CreateOrder.customer_search_input, 'john doe'));
      test('should choose the customer', () => client.waitForExistAndClick(CreateOrder.choose_customer_button));
      test('should search for a product by name', () => client.waitAndSetValue(CreateOrder.product_search_input, productData.name + global.date_time));
      test('should set the product combination', () => client.waitAndSelectByValue(CreateOrder.product_combination, global.combinationId));
      test('should set the product quantity', () => client.waitAndSetValue(CreateOrder.quantity_input.replace('%NUMBER', 1), '4'));
      test('should click on "Add to cart" button', () => client.scrollWaitForExistAndClick(CreateOrder.add_to_cart_button));
      test('should get the basic product price', () => client.getTextInVar(CreateOrder.basic_price_value, global.basic_price));
      test('should set the delivery option ', () => client.waitAndSelectByValue(CreateOrder.delivery_option, '2,'));
      test('should add an order message ', () => client.addOrderMessage('Order message test'));
      test('should set the payment type ', () => client.waitAndSelectByValue(CreateOrder.payment, 'ps_checkpayment'));
      test('should set the order status ', () => client.waitAndSelectByValue(OrderPage.order_state_select, '1'));
      test('should click on "Create the order"', () => client.waitForExistAndClick(CreateOrder.create_order_button));
    }, 'order');
  },
  checkOrderInBO: function (clientType = "client") {
    scenario('Check the created order information in the Back Office', client => {
      test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should search for the created order by reference', () => client.waitAndSetValue(OrderPage.search_by_reference_input, global.tab['reference']));
      test('should go to search order', () => client.waitForExistAndClick(OrderPage.search_order_button));
      test('should go to the order', () => client.scrollWaitForExistAndClick(OrderPage.view_order_button.replace('%NUMBER', 1), 150, 2000));
      test('should check that the customer name is "John DOE"', () => client.checkTextValue(OrderPage.customer_name, 'John DOE', 'contain'));
      if (clientType === "guest") {
        test('should check that the order has been placed by a guest', () => client.isExisting(OrderPage.transform_guest_customer_button));
      }
      test('should status be equal to "Awaiting bank wire payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting bank wire payment'));
      test('should check the shipping price', () => client.checkTextValue(OrderPage.shipping_cost, global.tab['shipping_price']));
      test('should check the product name', () => client.checkTextValue(OrderPage.product_name.replace("%NUMBER", 1), global.tab['product']));
      test('should check the order message', () => client.checkTextValue(OrderPage.message_order, 'Order message test'));
      test('should check the total price', () => client.checkTextValue(OrderPage.total_price, global.tab["total_price"]));
      test('should check basic product price', () => {
        return promise
          .then(() => client.scrollWaitForExistAndClick(OrderPage.edit_product_button))
          .then(() => client.checkAttributeValue(OrderPage.product_basic_price.replace("%NUMBER", 1), 'value', global.tab["basic_price"].replace('€', '')))
      });
      test('should check shipping method', () => client.checkTextValue(OrderPage.shipping_method, global.tab["method"].split('\n')[0], 'contain'));
    }, "order");
  },

  getShoppingCartsInfo: async function (client) {
    for (let i = 1; i <= global.shoppingCartsNumber; i++) {
      await client.getTextInVar(ShoppingCart.id.replace('%NUMBER', i), "id");
      await client.getTextInVar(ShoppingCart.order_id.replace('%NUMBER', i), "order_id");
      await client.getTextInVar(ShoppingCart.customer.replace('%NUMBER', i), "customer");
      await client.getTextInVar(ShoppingCart.total.replace('%NUMBER', i), "total");
      await client.getTextInVar(ShoppingCart.carrier.replace('%NUMBER', i), "carrier");
      await client.getTextInVar(ShoppingCart.date.replace('%NUMBER', i), "date");
      await client.getTextInVar(ShoppingCart.customer_online.replace('%NUMBER', i), "customer_online");
      await  parseInt(global.tab["order_id"]) ? global.tab["order_id"] = parseInt(global.tab["order_id"]) : global.tab["order_id"] = '"' + global.tab["order_id"] + '"';
      await  global.tab["carrier"] === '--' ? global.tab["carrier"] = '' : global.tab["carrier"] = '"' + global.tab["carrier"] + '"';
      await  global.tab["customer_online"] === 'Yes' ? global.tab["customer_online"] = 1 : global.tab["customer_online"] = 0;
      global.tab["date"] = await dateFormat(global.tab["date"], "yyyy-mm-dd HH:MM:ss");
      await global.orders.push(parseInt(global.tab["id"]) + ';' + global.tab["order_id"] + ';' + '"' + global.tab["customer"] + '"' + ';' + global.tab["total"] + ';' + global.tab["carrier"] + ';' + '"' + global.tab["date"] + '"' + ';' + global.tab["customer_online"]);
    }
  },
  checkExportedFile: async function (client) {
    await client.downloadCart(ShoppingCart.export_carts_button);
    await client.checkFile(global.downloadsFolderPath, global.exportCartFileName);
    await client.readFile(global.downloadsFolderPath, global.exportCartFileName, 1000);
    await client.checkExportedFileInfo(1000);
    await client.waitForExistAndClick(ShoppingCart.reset_button);
  },
  initCheckout: function (client) {
    test('should add some product to cart"', () => {
      return promise
        .then(() => client.waitForExistAndClick(productPage.cloths_category))
        .then(() => client.waitForExistAndClick(productPage.second_product_clothes_category))
        .then(() => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button))
        .then(() => client.waitForVisible(CheckoutOrderPage.blockcart_modal))
        .then(() => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button))
        .then(() => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
    });
  },
  creditSlip: function (refundedValue, i) {
    scenario('Generate a credit slip', client => {
      test('should go to the orders list', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should go to the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
      test('should change order state to "Payment accepted"', () => client.changeOrderState(OrderPage, 'Payment accepted'));
      test('should click on "Partial refund" button', () => client.waitForExistAndClick(OrderPage.partial_refund));
      test('should set the "quantity refund" to "2"', () => client.waitAndSetValue(OrderPage.quantity_refund, refundedValue));
      test('should click on "Re-stock products" CheckBox', () => client.waitForExistAndClick(OrderPage.re_stock_product));
      test('should click on "Partial refund" button', () => client.waitForExistAndClick(OrderPage.refund_products_button));
      test('should check the success message', () => client.checkTextValue(OrderPage.success_msg, 'partial refund was successfully created.', 'contain'));
      test('should get all order information', () => {
        return promise
          .then(() => client.getTextInVar(OrderPage.order_id, "orderID"))
          .then(() => client.getTextInVar(OrderPage.order_date, "invoiceDate"))
          .then(() => client.getTextInVar(OrderPage.order_ref, "orderRef"))
          .then(() => {
            client.getTextInVar(OrderPage.product_information, "productRef").then(() => {
              global.tab['productRef'] = global.tab['productRef'].split('\n')[1];
              global.tab['productRef'] = global.tab['productRef'].substring(18);
            })
          })
          .then(() => client.pause(2000))
          .then(() => {
            client.getTextInVar(OrderPage.product_information, "productCombination").then(() => {
              global.tab['productCombination'] = global.tab['productCombination'].split('\n')[0];
              global.tab['productCombination'] = global.tab['productCombination'].split(':')[1];
            })
          })
          .then(() => client.pause(2000))
          .then(() => client.getTextInVar(OrderPage.product_quantity, "productQuantity"))
          .then(() => {
            client.getTextInVar(OrderPage.product_name_tab, "productName").then(() => {
              global.tab['productName'] = global.tab['productName'].substring(0, 25);
            })
          })
          .then(() => client.getTextInVar(OrderPage.product_unit_price_tax_included, "unitPrice"))
          .then(() => global.tab['unitPrice'] = global.tab['unitPrice'].substr(1, global.tab['unitPrice'].length))
          .then(() => client.getTextInVar(OrderPage.product_total, "productTotal"))
          .then(() => client.waitForExistAndClick(OrderPage.edit_product_button))
          .then(() => client.getAttributeInVar(OrderPage.product_unit_price, "value", "taxExcl"))
          .then(() => client.pause(2000))
          .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu))
          .then(() => client.searchByValue(AddProductPage.catalogue_filter_by_name_input, AddProductPage.catalogue_submit_filter_button, global.tab['productName']))
          .then(() => client.waitForExistAndClick(ProductList.edit_button))
          .then(() => client.getAttributeInVar(AddProductPage.tax_rule, "title", 'taxRate'))
          .then(() => global.tab['taxRate'] = global.tab['taxRate'].substr(18, 2))
          .then(() => {
            global.orderInformation[i] = {
              "orderID": global.tab['orderID'].replace("#", ''),
              "invoiceDate": global.tab['invoiceDate'],
              "productRef": global.tab['productRef'],
              "productCombination": global.tab['productCombination'],
              "productQuantity": global.tab['productQuantity'],
              "productName": global.tab['productName'],
              "unitPrice": global.tab['unitPrice'],
              "orderRef": global.tab['orderRef'],
              "productTotal": global.tab['productTotal'],
              "taxExcl": global.tab['taxExcl'],
              "taxRate": global.tab['taxRate']
            }
          });
      });
      test('should go to the orders list', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
      test('should go to the created order', () => client.waitForExistAndClick(OrderPage.order_view_button.replace('%ORDERNumber', 1)));
      test('should click on "DOCUMENTS" subtab', () => client.scrollWaitForExistAndClick(OrderPage.document_submenu));
      test('should get the credit slip name', () => client.getDocumentName(OrderPage.credit_slip_document_name));
      test('should go to "Credit slip" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu));
      test('should click on "Download credit slip" button', () => {
        return promise
          .then(() => client.waitForVisibleAndClick(CreditSlip.download_btn.replace('%ID', global.tab['orderID'].replace("#", ''))))
          .then(() => client.pause(8000));
      });
    }, 'order');
  },
  checkCreditSlip: function (refundedValue, i) {
    scenario('Check the credit slip information', client => {
      test('should check the "Billing Address" ', () => {
        return promise
          .then(() => client.checkDocument(global.downloadsFolderPath, global.creditSlip, 'My Company'))
          .then(() => client.checkDocument(global.downloadsFolderPath, global.creditSlip, '16, Main street'))
          .then(() => client.checkDocument(global.downloadsFolderPath, global.creditSlip, '75002 Paris'))
          .then(() => client.checkDocument(global.downloadsFolderPath, global.creditSlip, 'France'));
      });
      test('should check the "Name & Last name" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.tab['accountName']));
      test('should check the "Invoice date Reference"', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.orderInformation[i].invoiceDate));
      test('should check the "Invoice order Reference"', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.orderInformation[i].orderRef, 5000));
      test('should check the "Product combination" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.orderInformation[i].productCombination));
      test('should check the "Product quantity" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.orderInformation[i].productQuantity));
      test('should check the "Product name" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.orderInformation[i].productName));
      test('should check the "Unit Price" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.orderInformation[i].unitPrice));
      test('should check the "Price" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, parseInt(global.orderInformation[i].unitPrice * refundedValue)));
      test('should check the "Product total" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, global.orderInformation[i].productTotal));
      test('should check the "Total Tax" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, Math.round(global.orderInformation[i].taxExcl * refundedValue * global.orderInformation[i].taxRate) / 100));
      test('should check the "Tax Rate" ', () => client.checkDocument(global.downloadsFolderPath, global.creditSlip, parseInt((global.orderInformation[i].taxRate)).toFixed(3)));
      test('should check the "Tax detail" ', () => {
        return promise
          .then(() => client.checkDocument(global.downloadsFolderPath, global.creditSlip, 'Products'))
          .then(() => client.deleteDownloadedDocument(global.creditSlip));
      });
    }, 'order');
  },
  enableMerchandise: function () {
    scenario('Enable Merchandise Returns', client => {
      test('should go to "Merchandise Returns" page', () => client.goToSubtabMenuPage(Menu.Sell.CustomerService.customer_service_menu, Menu.Sell.CustomerService.merchandise_returns_submenu));
      test('should enable "Merchandise Returns"', () => client.waitForExistAndClick(MerchandiseReturns.enableReturns));
      test('should click on "Save" button', () => client.waitForExistAndClick(MerchandiseReturns.save_button));
      test('should check the success message', () => client.checkTextValue(MerchandiseReturns.success_msg, 'The settings have been successfully updated.', 'contain'));
    }, 'order');
  },
  checkOrderInvoice: function (client, i) {
    test('should check the Customer name of the ' + i + ' product', () => client.checkDocument(global.downloadsFolderPath, 'invoices', 'John DOE'));
    test('should check the "Delivery Address " of the product n°' + i, () => {
      return promise
        .then(() => client.checkDocument(global.downloadsFolderPath, 'invoices', 'My Company'))
        .then(() => client.checkDocument(global.downloadsFolderPath, 'invoices', '16, Main street'))
        .then(() => client.checkDocument(global.downloadsFolderPath, 'invoices', '75002 Paris'))
        .then(() => client.checkDocument(global.downloadsFolderPath, 'invoices', 'France'));
    });
    test('should check the "invoice Date" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].invoiceDate));
    test('should check the "Order Reference" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].OrderRef));
    test('should check the "Product Reference"of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].ProductRef));
    test('should check the "Product Combination" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].ProductCombination));
    test('should check the "Product Quantity" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].ProductQuantity));
    test('should check the "Total Price" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].TotalPrice));
    test('should check the "Unit Price" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].ProductUnitPrice));
    test('should check the "Tax Rate" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].ProductTaxRate));
    test('should check the "Total Product" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].TotalProduct));
    test('should check the "Shipping Cost" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].ShippingCost));
    test('should check the "Total" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].Total));
    test('should check the "Total Tax" of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].TotalTax));
    test('should check the "Carrier" name of the product n°' + i, () => client.checkDocument(global.downloadsFolderPath, 'invoices', global.orderInfo[i - 1].Carrier));
  }
};
