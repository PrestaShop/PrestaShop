/**
 * This script is based on the scenario described in this test link
 * [id="PS-21"][Name="Create an order in the BO"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage, CustomerAccount} = require('../../../selectors/FO/order_page');
const {HomePage} = require('../../../selectors/FO/home_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding');
const {Localization} = require('../../../selectors/BO/international/localization');
const {OrderPage, CreateOrder} = require('../../../selectors/BO/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {DiscountSubMenu} = require('../../../selectors/BO/catalogpage/discount_submenu');
const {Addresses} = require('../../../selectors/BO/customers/addresses');
const {ShoppingCart} = require('../../../selectors/BO/order');
const orderScenarios = require('../../common_scenarios/order');
const commonScenariosProduct = require('../../common_scenarios/product');
const welcomeScenarios = require('../../common_scenarios/welcome');
const commonScenariosAddress = require('../../common_scenarios/address');
const commonScenariosCustomer = require('../../common_scenarios/customer');
const commonScenariosDiscount = require('../../common_scenarios/discount');
const commonCurrency = require('../../common_scenarios/currency');

let dateFormat = require('dateformat');
let promise = Promise.resolve();
let dateSystem = dateFormat(new Date(), 'mm/dd/yyyy');
let productData = [{
  name: 'firstProduct',
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
}, {
  name: 'secondProduct',
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
}];
let customerData = {
  first_name: 'Test',
  last_name: 'Test',
  email_address: 'test@prestashop.com',
  password: '123456789',
  birthday: {
    day: '18',
    month: '12',
    year: '1991'
  }
};
let addressData = {
  email_address: 'test@prestashop.com',
  id_number: '123456789',
  address_alias: 'Ma super address',
  first_name: 'Test',
  last_name: 'Test',
  company: 'prestashop',
  vat_number: '0123456789',
  address: '12 rue d\'amsterdam',
  second_address: 'RDC',
  ZIP: '75009',
  city: 'Paris',
  country: 'France',
  home_phone: '0123456789',
  other: 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'
};
let cartRuleData = [
  {
    name: 'Cart_rule',
    customer_email: date_time + customerData.email_address,
    minimum_amount: 0,
    type: 'percent',
    reduction: 50
  }, {
    name: 'Cart_rule_order',
    customer_email: date_time + customerData.email_address,
    minimum_amount: 0,
    type: 'percent',
    reduction: 20,
    highlight: 'on',
    partial_use: 'off',
    free_shipping: 'off'
  }];
let firstCurrencyData = {
  name: 'GBP',
  exchangeRate: '0.86'
};

let secondCustomerData = {
  first_name: 'new',
  last_name: 'new',
  email_address: 'new' + global.adminEmail,
  password: '123456789',
  birthday: {
    day: '18',
    month: '12',
    year: '1991'
  }
};

scenario('Create order in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');

  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Create customer, addresses, carts and orders', () => {
    commonScenariosCustomer.createCustomer(customerData);
    commonScenariosAddress.createCustomerAddress(addressData);
    commonScenariosProduct.createProduct(AddProductPage, productData[0]);
    orderScenarios.createOrderBO(OrderPage, CreateOrder, productData[0], date_time + customerData.email_address);
    scenario('Add product to cart  in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should click on "sign in" button', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
      test('should set the "Email" input', () => client.waitAndSetValue(AccessPageFO.login_input, date_time + customerData.email_address));
      test('should set the "Password" input', () => client.waitAndSetValue(AccessPageFO.password_inputFO, customerData.password));
      test('should click on "SIGN IN" button', () => client.waitForExistAndClick(AccessPageFO.login_button));
      test('should click on shop logo', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
      test('should search for a product by name', () => {
        return promise
          .then(() => client.waitAndSetValue(HomePage.search_input, productData[0].name + date_time))
          .then(() => client.waitForExistAndClick(HomePage.search_icon))
          .then(() => client.waitForExistAndClick(productPage.productLink.replace('%PRODUCTNAME', productData[0].name + date_time)));
      });
      test('should choose "M" from size list', () => client.waitAndSelectByValue(productPage.first_product_size, '2'));
      test('should set the product "Quantity" input', () => client.waitAndSetValue(productPage.first_product_quantity, '4'));
      test('should click on "ADD TO CART" button  ', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on "PROCEED TO CHECKOUT" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should click on "PROCEED TO CHECKOUT" button 2', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'customer');
  }, 'order');
  commonScenariosProduct.createProduct(AddProductPage, productData[1]);
  commonScenariosDiscount.createCartRule(cartRuleData[0], 'firstCartRuleCode');
  scenario('Click on "Stop the OnBoarding" button', client => {
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button))
        .then(() => client.pause(1000));
    });
  }, 'onboarding');
  commonCurrency.accessToCurrencies();
  commonCurrency.createCurrency('Successful creation.', firstCurrencyData, false, true, true);
  commonCurrency.checkCurrencyByIsoCode(firstCurrencyData);
  scenario('Enable currency', client => {
    test('should click on "Enable icon"', () => client.waitForExistAndClick(Localization.Currencies.check_icon.replace('%ID', 1).replace('%ICON', "not-valid")));
  }, 'common_client');
  orderScenarios.createCustomerFromOrder(secondCustomerData);
  scenario('Display then check details of the created customer', client => {
    test('should display details of the customer', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.detail_customer_button, 1000))
        .then(() => client.goToFrame(1))
        .then(() => client.pause(2000));
    });
    test('should check the "First name" and the "Last name" of the created customer', () => client.checkTextValue(CreateOrder.customer_details_header_bloc, secondCustomerData.first_name + ' ' + secondCustomerData.last_name, 'contain'));
    test('should check the "Email" of the created customer', () => client.checkTextValue(CreateOrder.customer_details_email_link, secondCustomerData.email_address, 'contain'));
    test('should close details of the customer', () => {
      return promise
        .then(() => client.closeFrame())
        .then(() => client.waitForExistAndClick(CreateOrder.close_detail_link, 1000));
    });
  }, 'order');
  scenario('Display then check details of the existing cart', client => {
    test('should search for a customer', () => {
      return promise
        .then(() => client.waitAndSetValue(CreateOrder.customer_search_input, date_time + customerData.email_address))
        .then(() => client.pause(1000));
    });
    test('should choose the created customer', () => client.waitForExistAndClick(CreateOrder.choose_customer_button));
    test('should display details of the cart', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.detail_cart_button, 1000))
        .then(() => client.goToFrame(1))
        .then(() => client.pause(3000));
    });
    test('should check the "Unit Price" of the product', () => client.checkTextValue(ShoppingCart.product_unit_price.replace('%NUMBER', 1), '€6.00'));
    test('should check the "Quantity" of the product', () => client.checkTextValue(ShoppingCart.quantity_product.replace('%NUMBER', 1), '4', 'equal'));
    test('should check the "Stock" of the product', () => client.checkTextValue(ShoppingCart.stock_product.replace('%NUMBER', 1), '6'));
    test('should check the "Total" of the product', () => client.checkTextValue(ShoppingCart.total_product.replace('%NUMBER', 1), '€24.00'));
    test('should check the "Total" of the cart', () => client.checkTextValue(ShoppingCart.total_cart_summary.replace('%NUMBER', 1), '€24.00'));
    test('should close details of the cart', () => {
      return promise
        .then(() => client.closeFrame())
        .then(() => client.waitForExistAndClick(CreateOrder.close_detail_link, 1000));
    });
  }, 'order');
  scenario('Display then check details of the existing order', client => {
    test('should click on "Use" button', () => client.waitForExistAndClick(CreateOrder.use_cart_button, 1000));
    test('should click on "Orders"', () => client.waitForExistAndClick(CreateOrder.orders_tab));
    test('should display details of the orders', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.detail_orders_button, 1000))
        .then(() => client.goToFrame(1))
        .then(() => client.pause(3000));
    });
    test('should check that the customer name is "' + customerData.first_name + ' ' + customerData.last_name + '"', () => client.checkTextValue(OrderPage.customer_name, customerData.first_name + ' ' + customerData.last_name, 'contain'));
    test('should check that status is equal to "Awaiting bank wire payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting check payment'));
    test('should check the shipping price', () => client.checkTextValue(OrderPage.shipping_cost, global.tab['price'], 'contain'));
    test('should check the product name', () => client.checkTextValue(OrderPage.product_name.replace('%NUMBER', 1), productData[0]['name'], 'contain'));
    test('should check the order message', () => client.checkTextValue(OrderPage.message_order, 'Order message test'));
    test('should check the total price', () => client.checkTextValue(OrderPage.total_order_price, global.tab['total_tax'], 'contain'));
    test('should close details of the order', () => {
      return promise
        .then(() => client.closeFrame())
        .then(() => client.waitForExistAndClick(CreateOrder.close_detail_link, 1000));
    });
  }, 'order');
  scenario('Use the existing order then create order in the Back Office', client => {
    test('should click on "Use" button', () => client.waitForExistAndClick(CreateOrder.use_orders_button, 1000));
    test('should click on "remove" icon of the product ', () => client.waitForExistAndClick(CreateOrder.delete_product_button, 1000));
    test('should search for the created product by name', () => client.waitAndSetValue(CreateOrder.product_search_input, productData[1].name + global.date_time));
    test('should set the product combination', () => client.waitAndSelectByValue(CreateOrder.product_combination, global.combinationId));
    test('should set the product "Quantity" input', () => client.waitAndSetValue(CreateOrder.quantity_input.replace('%NUMBER', 1), '4'));
    test('should click on "Add to cart" button', () => client.scrollWaitForExistAndClick(CreateOrder.add_to_cart_button));
    test('should click on arrow up button to increase quantity', async () => await client.waitForExistAndClick(CreateOrder.quantity_arrow_up_button));
    test('should click on arrow down button to decrease quantity', async () => await client.waitForExistAndClick(CreateOrder.quantity_arrow_down_button));
    test('should get the price for product', async () => {
        await client.pause(500);
        await client.getTextInVar(CreateOrder.price_product_column, 'price_product');
    });
    test('should choose "British Pound Sterling" from currency list', () => {
      return promise
        .then(() => client.selectByVisibleText(CreateOrder.currency_select, 'British Pound'))
        .then(() => client.pause(2000));
    });
    test('should verify that the price is changed', () => client.checkTextValue(CreateOrder.price_product_column, global.tab['price_product'], 'notequal'));
    test('should choose "Euro" from currency list', () => {
      return promise
        .then(() => client.selectByVisibleText(CreateOrder.currency_select, 'Euro'))
        .then(() => client.pause(2000));
    });
    test('should verify that the price is changed', () => client.checkTextValue(CreateOrder.price_product_column, global.tab['price_product'], 'equal'));
    test('should set the language "French"', () => client.waitAndSelectByValue(CreateOrder.language_select, '2'));
    test('should search for a voucher by name', () => {
      return promise
        .then(() => client.waitAndSetValue(CreateOrder.voucher_input, global.tab["firstCartRuleCode"]))
        .then(() => client.pause(2000))
        .then(() => client.keys('ArrowDown'))
        .then(() => client.waitForExistAndClick(DiscountSubMenu.cartRules.first_result_option));
    });
    test('should click on "Delete" button of the voucher', () => client.waitForExistAndClick(CreateOrder.delete_voucher_button));
  }, 'order');
  scenario('Add voucher from order in the Back Office', client => {
    test('should click on "Add new voucher" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.new_voucher_button, 1000))
        .then(() => client.goToFrame(1))
        .then(() => client.pause(3000));
    });
    test('should set the "Name" input', () => client.waitAndSetValue(DiscountSubMenu.cartRules.name_input, cartRuleData[1].name));
    test('should click on "Generate" button', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.generate_button));
    test('should switch the "Highlight" to "Yes"', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.highlight_button.replace('%S', cartRuleData[1].highlight)));
    test('should switch the "Partial use" to "No"', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.partial_use_button.replace('%S', cartRuleData[1].partial_use)));
    test('should click on "CONDITIONS" tab', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.conditions_tab));
    test('should set the "Minimum amount" input', () => client.waitAndSetValue(DiscountSubMenu.cartRules.minimum_amount_input, cartRuleData[1].minimum_amount));
    test('should click on "ACTIONS" tab', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.actions_tab));
    test('should switch the "Free shipping" to "No"', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.free_shipping_button.replace('%S', cartRuleData[1].free_shipping)));
    test('should click on "' + cartRuleData[1].type + '" radio', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.apply_discount_radio.replace("%T", cartRuleData[1].type), 2000));
    test('should set the "Reduction value" ' + cartRuleData[1].type + 'input', () => client.waitAndSetValue(DiscountSubMenu.cartRules.reduction_input.replace("%T", cartRuleData[1].type), cartRuleData[1].reduction, 2000));
    test('should click on "Save" button', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.save_button));
  }, 'order');
  scenario('Edit delivery address and invoice address then create order in the Back Office', client => {
    test('should click on "Edit" of delivery address', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.edit_delivery_address_button, 2000))
        .then(() => client.goToFrame(1))
        .then(() => client.pause(1000));
    });
    test('should set the "First Name" ', () => client.waitAndSetValue(Addresses.first_name_input, 'TestModification', 2000));
    test('should set the "Last Name" ', () => client.waitAndSetValue(Addresses.last_name_input, 'TestModification', 2000));
    test('should set the "Company" ', () => client.waitAndSetValue(Addresses.company, 'PrestashopModification', 2000));
    test('should click on "Save" button', () => client.waitForExistAndClick(Addresses.save_button));
    test('should check the "Name " for Delivery addresses', () => client.checkTextValue(CreateOrder.detail_addresses_bloc, 'TestModification TestModification', 'contain', 2000));
    test('should click on "Add a new address" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.new_address_button, 1000))
        .then(() => client.goToFrame(1))
        .then(() => client.pause(3000));
    });
    test('should set "Address alias" input', () => client.waitAndSetValue(Addresses.address_alias_input, 'Address xxx ' + global.date_time));
    test('should set the "First Name" ', () => client.waitAndSetValue(Addresses.first_name_input, 'NewAddress', 2000));
    test('should set the "Last Name" ', () => client.waitAndSetValue(Addresses.last_name_input, 'NewAddress', 2000));
    test('should set "Address" input', () => client.waitAndSetValue(Addresses.address_input, "12 rue test " + date_time));
    test('should set "Postal code" input', () => client.waitAndSetValue(Addresses.zip_code_input, '75009'));
    test('should set "City" input', () => client.waitAndSetValue(Addresses.city_input, 'Paris'));
    test('should set "Pays" input', () => client.waitAndSelectByVisibleText(Addresses.country_input, 'France'));
    test('should click on "Save" button', () => client.scrollWaitForExistAndClick(Addresses.save_button));
    test('should set the delivery address ', () => client.waitAndSelectByVisibleText(CreateOrder.delivery_address_select, 'Address xxx ' + global.date_time));
    test('should set the invoice address ', () => client.waitAndSelectByVisibleText(CreateOrder.invoice_address_select, 'Address xxx ' + global.date_time));
    test('should set the delivery option', () => {
      return promise
        .then(() => client.waitAndSelectByValue(CreateOrder.delivery_option, '2,'))
        .then(() => client.pause(1000));
    });
    test('should check the shipping price', () => client.checkTextValue(CreateOrder.shipping_price, '0', 'notequal'));
    test('should check "Total shipping"', () => client.checkTextValue(CreateOrder.total_shipping, '0', 'notequal'));
    test('should switch the "Free shipping" to "Yes"', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.free_shipping_button.replace('%S', '')))
        .then(() => client.pause(1000));
    });
    test('should check the shipping is free', () => client.checkTextValue(CreateOrder.total_shipping, '0', 'equal'));
    test('should switch the "Free shipping" to "No"', () => {
      return promise
        .then(() => client.waitForExistAndClick(CreateOrder.free_shipping_button.replace('%S', '_off')))
        .then(() => client.pause(1000));
    });
    test('should check that the shipping have a price ', () => client.checkTextValue(CreateOrder.total_shipping, '0', 'notequal'));
    test('should add an order message ', () => client.addOrderMessage('Order message test'));
    test('should check "Total products" ', () => client.checkTextValue(CreateOrder.total_products_span, global.tab["price_product"]));
    test('should check "Total vouchers (Tax excl.)" ', () => client.checkTextValue(CreateOrder.total_vouchers_span, '0', 'notequal'));
    test('should check "Total shipping (Tax excl.)" ', () => client.checkTextValue(CreateOrder.total_shipping, '0', 'notequal'));
    test('should check "Total taxes" ', () => client.checkTextValue(CreateOrder.total_taxes_span, '0', 'notequal'));
    test('should check "Total (Tax excluded)" ', () => client.checkTextValue(CreateOrder.total_tax_excluded_span, '0', 'notequal'));
    test('should check "Total (Tax included)" ', () => client.checkTextValue(CreateOrder.total_tax_included_span, '0', 'notequal'));
    test('should set the payment type ', () => client.waitAndSelectByValue(CreateOrder.payment, 'ps_checkpayment'));
    test('should set the order status ', () => client.waitAndSelectByValue(OrderPage.order_state_select, '1'));
    test('should click on "Create the order" button', () => client.waitForExistAndClick(CreateOrder.create_order_button));
  }, 'order');
  scenario('Check the created order in the Back Office', client => {
    test('should check status to be equal to "Awaiting check payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting check payment'));
    test('should check that the "order message" is equal to "Order message test"', () => client.checkTextValue(OrderPage.message_order, 'Order message test', 'contain', 4000));
    test('should check "Customer information"', () => {
      return promise
        .then(() => client.checkTextValue(OrderPage.customer_name, customerData.first_name + ' ' + customerData.last_name, 'contain', 4000))
        .then(() => client.checkTextValue(OrderPage.customer_email_link, date_time + customerData.email_address))
        .then(() => client.checkTextValue(OrderPage.customer_account_registred_text, dateSystem, 'contain'))
        .then(() => client.checkTextValue(OrderPage.valid_order_placed_number_span, '0', 'contain'))
        .then(() => client.checkTextValue(OrderPage.total_registration_span, '0.00', 'contain'));
    });
    test('should check "Shipping Address"', () => client.checkTextValue(OrderPage.shipping_address_bloc, 'NewAddress NewAddress', "contain"));
    test('should click on "Invoice Address" subtab', () => client.waitForVisibleAndClick(OrderPage.invoice_address_tab, 1000));
    test('should check "Shipping" information', () => {
      return promise
        .then(() => client.checkTextValue(OrderPage.date_shipping, dateSystem, 'contain'))
        .then(() => client.checkTextValue(OrderPage.carrier, 'carrier', 'contain'))
        .then(() => client.checkTextValue(OrderPage.weight_shipping, '0.00', 'contain'))
        .then(() => client.checkTextValue(OrderPage.shipping_cost, '€8.40'))
    });
    test('should check "Payment" information', () => client.checkTextValue(OrderPage.payment_method, '', 'contain'));
    test('should check that the "quantity" is  equal to "4"', () => client.checkTextValue(OrderPage.order_quantity.replace("%NUMBER", 1), '4'));
    test('should check "Products" information', () => {
      return promise
        .then(() => client.checkTextValue(OrderPage.product_Url, productData[1].name + date_time, 'contain'))
        .then(() => client.checkTextValue(OrderPage.order_quantity.replace("%NUMBER", 1), '4'))
        .then(() => client.checkTextValue(OrderPage.stock_product.replace("%NUMBER", 1), '6'))
        .then(() => client.getTextInVar(OrderPage.total_order, "total_orders"));
    });
  }, 'order');
  scenario('Check the created order in the Front Office', () => {
    scenario('Login in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(2));
      });
    }, 'common_client');
    scenario('Display the list of orders', client => {
      test('should go to the customer account', () => client.waitForExistAndClick(CheckoutOrderPage.customer_name));
      test('should display a list of orders', () => {
        return promise
          .then(() => client.waitForExistAndClick(CustomerAccount.order_history_button))
          .then(() => client.checkList(CustomerAccount.details_buttons));
      });
      test('should click on the "Details" button', () => client.waitForExistAndClick(CustomerAccount.details_button.replace("%NUMBER", 1)));
    }, 'common_client');
    scenario('Order detail page', client => {
      test('should check that is the order details page', () => client.checkTextValue(CustomerAccount.order_details_words, "Order details"));
      test('should display order information', () => client.waitForVisible(CustomerAccount.order_infos_block));
      test('should display order statuses', () => client.waitForVisible(CustomerAccount.order_status_block));
      test('should display invoice address', () => client.waitForVisible(CustomerAccount.invoice_address_block));
      test('should display order products', () => client.waitForVisible(CustomerAccount.order_products_block));
      test('should display the return button', () => client.waitForVisible(CustomerAccount.order_products_block));
      test('should display a form to add a message', () => client.waitForVisible(CustomerAccount.add_message_block));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
  }, 'common_client');
  commonCurrency.accessToCurrencies();
  commonCurrency.checkCurrencyByIsoCode(firstCurrencyData);
  commonCurrency.deleteCurrency(true, 'Successful deletion.');
  scenario('Click on "Reset" button', client => {
    test('should click on reset button', () => client.waitForExistAndClick(Localization.Currencies.reset_button));
  }, 'common_client');
}, 'order', true);
