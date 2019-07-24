/**
 * This script is based on the scenario described in this test link
 * [id="PS-27"][Name="Create an order as a guest"]
 **/

const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {accountPage} = require('../../../selectors/FO/add_account_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {Customer} = require('../../../selectors/BO/customers/customer');
const {Addresses} = require('../../../selectors/BO/customers/addresses');
const {OrderPage} = require('../../../selectors/BO/order');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const commonProductScenarios = require('../../common_scenarios/product');
const stockCommonScenarios = require('../../common_scenarios/stock');
let dateFormat = require('dateformat');
let dateSystem = dateFormat(new Date(), 'yyyy-mm-dd');
let data = require('../../../datas/customer_and_address_data');
let promise = Promise.resolve();
const {ProductSettings} = require('../../../selectors/BO/shopParameters/product_settings');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Employee} = require('../../../selectors/BO/employee_page');
const welcomeScenarios = require('../../common_scenarios/welcome');

let productData = [{
  name: 'ProductA',
  quantity: '350',
  price: '11.90',
  priceTTC: '€14.28',
  image_name: 'image_test.jpg',
  reference: 'refA',
  quantities: {
    stock: 'deny'
  }
}, {
  name: 'ProductB',
  quantity: '350',
  price: '17.50',
  priceTTC: '€21.00',
  image_name: 'image_test.jpg',
  reference: 'refB',
  quantities: {
    stock: 'allow'
  }
}];

scenario('Create order by a guest from the Front Office', client => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  for (let m = 0; m < productData.length; m++) {
    commonProductScenarios.createProduct(AddProductPage, productData[m]);
  }
  scenario('Check the "' + productData[0].name + date_time + '" in the Front Office', client => {
    test('should go to the Front Office', async () => {
      await client.waitForVisibleAndClick(AccessPageBO.shopname, 1000);
      await client.switchWindow(1);
    });
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should search for the product "' + productData[0].name + date_time + '"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData[0].name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.search_product_name.replace("%PRODUCT", productData[0].name + date_time)));
    test('should check that the price is equal to ' + productData[0].priceTTC, () => client.checkTextValue(productPage.product_price, productData[0].priceTTC, 'equal', 1000));
    test('should change quantity to "300" using the keyboard and push "Enter"', () => client.waitAndSetValue(productPage.first_product_quantity, '300'));
    test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should check that the message is equal to "Product successfully added to your shopping cart"', () => client.checkTextValue(CheckoutOrderPage.success_product_add_to_cart_modal, 'Product successfully added to your shopping cart', 'contain', 3000));
    test('should check the existence of the product picture', () => client.isExisting(CheckoutOrderPage.modal_product_picture));
    test('should check that the product name is equal to ' + productData[0].name + date_time, () => client.isExisting(CheckoutOrderPage.modal_product_name, productData[0].name + date_time));
    test('should check that the product price is equal to ' + productData[0].priceTTC, () => client.checkTextValue(CheckoutOrderPage.modal_product_unit_price, productData[0].priceTTC));
    test('should check that the product quantity is equal to "300"', () => client.checkTextValue(CheckoutOrderPage.modal_product_quantity, '300', 'contain'));
    test('should check that the message is equal to "There are 300 items in your cart."', () => client.checkTextValue(CheckoutOrderPage.modal_cart_product_count, 'There are 300 items in your cart.'));
    test('should check that the total product is equal to "4 284,00 €"', () => client.checkTextValue(CheckoutOrderPage.modal_total_products, '€4,284.00', 'contain'));
    test('should check that the total shipping is equal to "Free"', () => client.checkTextValue(CheckoutOrderPage.modal_total_shipping, 'Free', 'contain'));
    test('should check that the total is equal to "4,284.00 € (tax incl.)"', () => client.checkTextValue(CheckoutOrderPage.modal_total, '€4,284.00', 'contain'));
    test('should click on "Continue shopping" button', () => client.waitForExistAndClick(CheckoutOrderPage.continue_shopping_button, 1000));
    test('should stay on the same product page', () => client.checkTextValue(productPage.product_name, (productData[0].name + date_time).toUpperCase(), 'equal', 1000));
    test('should go to home Page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page, 1000));
  }, 'common_client');
  scenario('Check the "' + productData[1].name + date_time + '" in the Front Office', client => {
    test('should go back to the "Back Office"', async () => client.switchWindow(0));
    test('should go to "Products" page', async () => {
      await client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu);
      await client.isVisible(CatalogPage.reset_button, 2000);
      if (global.isVisible) {
        await client.waitForVisibleAndClick(CatalogPage.reset_button, 2000);
      }
    });
    test('should get the products number', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000));
    });
    test('should close "catalog" menu', () => client.waitForVisibleAndClick(Menu.Sell.Catalog.catalog_menu));
    test('should go to "Shop Parameters - Product Settings" page', () => {
      return promise
        .then(() => client.pause(3000))
        .then(() => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.product_settings_submenu));
    });
    test('should check the created product ' + productData[1].name + date_time + ' in the Front Office', async () => {
      await client.getAttributeInVar(ProductSettings.Pagination.products_per_page_input, "value", "pagination");
      global.pagination = await Number(Math.trunc(Number(global.productsNumber) / Number(global.tab['pagination'])));
      await client.switchWindow(1);
      await client.waitForExistAndClick(AccessPageFO.logo_home_page, 1000);
      await client.changeLanguage();
      await client.scrollWaitForExistAndClick(productPage.see_all_products);
      if (global.pagination !== 0) {
        for (let i = 0; i <= global.pagination; i++) {
          if (i < global.pagination) {
            await client.isVisible(productPage.pagination_next);
            if (global.isVisible) {
              await client.clickPageNext(productPage.pagination_next, 3000);
            }
          }
        }
      }
      await client.isVisible(productPage.productLink.replace('%PRODUCTNAME', productData[1].name + date_time), 2000);
      if (global.isVisible) {
        await client.scrollWaitForExistAndClick(productPage.productLink.replace('%PRODUCTNAME', productData[1].name + date_time), 50, 2000);
      }
    });
    test('should check that the price is equal to ' + productData[1].priceTTC, () => client.checkTextValue(productPage.product_price, productData[1].priceTTC, 'equal', 1000));
    test('should change quantity to "300" using the keyboard and push "Enter"', () => client.waitAndSetValue(productPage.first_product_quantity, '300'));
    test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should check that the message is equal to "Product successfully added to your shopping cart"', () => client.checkTextValue(CheckoutOrderPage.success_product_add_to_cart_modal, 'Product successfully added to your shopping cart', 'contain', 1000));
    test('should check the existence of the product picture', () => client.isExisting(CheckoutOrderPage.modal_product_picture));
    test('should check that the product name is equal to ' + productData[1].name + date_time, () => client.isExisting(CheckoutOrderPage.modal_product_name, productData[1].name + date_time));
    test('should check that the product price is equal to ' + productData[1].priceTTC, () => client.checkTextValue(CheckoutOrderPage.modal_product_unit_price, productData[1].priceTTC));
    test('should check that the product quantity is equal to "300"', () => client.checkTextValue(CheckoutOrderPage.modal_product_quantity, '300', 'contain'));
    test('should check that the message is equal to "There are 600 items in your cart."', () => client.checkTextValue(CheckoutOrderPage.modal_cart_product_count, 'There are 600 items in your cart.'));
    test('should check that the total product is equal to "10,584.00€"', () => client.checkTextValue(CheckoutOrderPage.modal_total_products, '€10,584.00', 'contain'));
    test('should check that the total shipping is equal to "Free"', () => client.checkTextValue(CheckoutOrderPage.modal_total_shipping, 'Free', 'contain'));
    test('should check that the total is equal to "10,584.00€ (tax incl.)"', () => client.checkTextValue(CheckoutOrderPage.modal_total, '€10,584.00', 'contain'));
    test('should click on "PROCEED TO CHECKOUT" modal button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
  }, 'product/product');
  scenario('Check all the information in the cart', client => {
    test('should check "image" of the products', async () => {
      await client.isExisting(CheckoutOrderPage.product_picture.replace('%PRODUCT', productData[0].name + date_time), 1000);
      await client.isExisting(CheckoutOrderPage.product_picture.replace('%PRODUCT', productData[1].name + date_time));
    });
    test('should check "name" of the products', async () => {
      await client.checkTextValue(CheckoutOrderPage.product_name.replace('%NUMBER', 1), productData[0].name + date_time);
      await client.checkTextValue(CheckoutOrderPage.product_name.replace('%NUMBER', 2), productData[1].name + date_time);
    });
    test('should check "unit price" of the products', async () => {
      await client.checkTextValue(CheckoutOrderPage.product_unit_price.replace('%NUMBER', 1), productData[0].priceTTC);
      await client.checkTextValue(CheckoutOrderPage.product_unit_price.replace('%NUMBER', 2), productData[1].priceTTC);
    });
    test('should check "quantity"', async () => {
      await client.checkAttributeValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), 'value', '300');
      await client.checkAttributeValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 2), 'value', '300');
    });
    test('should check the price of 300 products', async () => {
      await client.checkTextValue(CheckoutOrderPage.product_total_price.replace('%NUMBER', 1), '€4,284.00');
      await client.checkTextValue(CheckoutOrderPage.product_total_price.replace('%NUMBER', 2), '€6,300.00');
    });
    test('should check the shipping is "free"', () => client.checkTextValue(CheckoutOrderPage.shipping_value, 'Free'));
    test('should check that the total is equal to "10,584.00€ (tax incl.)"', () => client.checkTextValue(CheckoutOrderPage.cart_total, '€10,584.00'));
    test('should click on "PROCEED TO CHECKOUT"', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
    test('should set the "First name" input', () => client.waitAndSetValue(accountPage.firstname_input, data.customer.firstname, 1000));
    test('should set the "Last name" input', () => client.waitAndSetValue(accountPage.lastname_input, data.customer.lastname));
    test('should set the "Email" input', () => client.waitAndSetValue(accountPage.new_email_input, data.customer.email.replace("%ID", 'account' + date_time)));
    test('should click on "CONTINUE" button', () => client.waitForExistAndClick(accountPage.new_customer_btn));
  }, 'common_client');
  scenario('Go back to the Back Office and check the created customer', () => {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
    test('should check the email existence in the "Customers list"', async () => {
      await client.isVisible(Customer.customer_filter_by_email_input);
      await client.search(Customer.customer_filter_by_email_input, data.customer.email.replace('%ID', 'account' + date_time));
      await client.checkExistence(Customer.email_address_value, data.customer.email.replace('%ID', 'account' + date_time), 6);
    });
    test('should check the "First name" of the created customer', () => client.checkTextValue(Customer.first_name_value.replace('%ID', 1), data.customer.firstname));
    test('should check the "Last name" of the created customer', () => client.checkTextValue(Customer.last_name_value.replace('%ID', 1), data.customer.lastname));
  }, 'common_client');

  scenario('Go back to the Front Office  and create the address', () => {
    test('should go back to the Front Office', () => client.switchWindow(1));
    test('should set the "Address" input', () => client.waitAndSetValue(accountPage.adr_address, data.address.address + " " + date_time));
    test('should set the "Zip/Postal Code" input', () => client.waitAndSetValue(accountPage.adr_postcode, data.address.postalCode));
    test('should set the "City" input', () => client.waitAndSetValue(accountPage.adr_city, data.address.city));
    test('should click on "CONTINUE" button', () => client.scrollWaitForExistAndClick(accountPage.new_address_btn));
  }, 'common_client');
  scenario('Go back to the Back Office and check the created address', () => {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should go to the "Addresses" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
    test('should check the address existence in the "addresses list"', async () => {
      await client.isVisible(Addresses.filter_by_address_input);
      await client.search(Addresses.filter_by_address_input, data.address.address + " " + date_time);
      await client.checkExistence(Addresses.address_value, data.address.address + " " + date_time, 5);
    });
    test('should check the "First name"', () => client.checkTextValue(Addresses.first_name_value.replace('%ID', 1), data.customer.firstname));
    test('should check the "Last name"', () => client.checkTextValue(Addresses.last_name_value.replace('%ID', 1), data.customer.lastname));
    test('should check the "Zip/Postal Code"', () => client.checkTextValue(Addresses.zip_code_value.replace('%ID', 1), data.address.postalCode));
    test('should check the "City"', () => client.checkTextValue(Addresses.city_value.replace('%ID', 1), data.address.city));
    test('should check the "Country"', () => client.checkTextValue(Addresses.country_value.replace('%ID', 1), data.address.country));
  }, 'common_client');
  scenario('Go back to the Front Office  and complete the missing steps for creating the order', () => {
    test('should go back to the Front Office', () => client.switchWindow(1));
    test('should choose shipping method my carrier', () => client.waitForExistAndClick(CheckoutOrderPage.shipping_method_option, 2000));
    test('should check the amount of shipping', () => client.checkTextValue(CheckoutOrderPage.shipping_value, '€8.40', 'equal', 1000));
    test('should check the total (tax incl.)', () => client.checkTextValue(CheckoutOrderPage.checkout_total_price, '€10,592.40'));
    test('should click on "confirm delivery" button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step3_continue_button));
    test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step4_payment_radio));
    test('should set "the condition to approve"', () => client.waitForExistAndClick(CheckoutOrderPage.condition_check_box));
    test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(CheckoutOrderPage.confirmation_order_button));
    test('should check the order confirmation', () => client.checkTextValue(CheckoutOrderPage.confirmation_order_message, 'YOUR ORDER IS CONFIRMED', "contain"));
    test('should check "image" of the products', async () => {
      await client.isExisting(CheckoutOrderPage.confirmation_product_picture.replace('%D', '1'), 2000);
      await client.isExisting(CheckoutOrderPage.confirmation_product_picture.replace('%D', '2'));
    });
    test('should check "name" of the products', async () => {
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_name.replace('%ID', 1), productData[0].name + date_time);
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_name.replace('%ID', 2), productData[1].name + date_time);
    });
    test('should check "unit price" of the products', async () => {
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_unit_price.replace('%ID', 1), productData[0].priceTTC);
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_unit_price.replace('%ID', 2), productData[1].priceTTC);
    });
    test('should check "quantity"', async () => {
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_quantity.replace('%ID', 1), '300');
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_quantity.replace('%ID', 2), '300');
    });
    test('should check the price of 300 products', async () => {
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_total_price.replace('%ID', 1), '€4,284.00');
      await client.checkTextValue(CheckoutOrderPage.confirmation_product_total_price.replace('%ID', 2), '€6,300.00');
    });
    test('should check the shipping price', () => client.checkTextValue(CheckoutOrderPage.confirmation_shipping_price, '€8.40'));
    test('should check that the sub total is equal to "10,584.00€ (tax incl.)"', () => client.checkTextValue(CheckoutOrderPage.confirmation_sub_total_price, "€10,584.00"));
    test('should check that the total is equal to "10,592.40€ (tax incl.)"', () => client.checkTextValue(CheckoutOrderPage.confirmation_total_price, "€10,592.40"));
    test('should get the order reference', () => client.getTextInVar(CheckoutOrderPage.order_reference, 'orderReference'));
    test('should get the first product name', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_name.replace('%ID', 1), 'firstProductName'));
    test('should get the second product name', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_name.replace('%ID', 2), 'secondProductName'));
    test('should get the unit price of the first product', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_unit_price.replace('%ID', 1), 'firstProductPrice'));
    test('should get the unit price of the second product', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_unit_price.replace('%ID', 2), 'secondProductPrice'));
    test('should get the quantity of the first product', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_quantity.replace('%ID', 1), 'firstProductQuantity'));
    test('should get the quantity of the second product', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_quantity.replace('%ID', 2), 'secondProductQuantity'));
    test('should get the total price of the first product', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_total_price.replace('%ID', 1), 'firstProductTotalPrice'));
    test('should get the total price of the second product', () => client.getTextInVar(CheckoutOrderPage.confirmation_product_total_price.replace('%ID', 2), 'secondProductTotalPrice'));
    test('should get the sub total price', () => client.getTextInVar(CheckoutOrderPage.confirmation_sub_total_price, 'subTotal'));
    test('should get the shipping price', () => client.getTextInVar(CheckoutOrderPage.confirmation_shipping_price, 'shippingPrice'));
    test('should get the total price (tax incl)', () => client.getTextInVar(CheckoutOrderPage.confirmation_total_price, 'totalPrice'));
    test('should get the order reference', () => client.getTextInVar(CheckoutOrderPage.order_reference, 'orderReference'));
    test('should check if popular products are displayed', () => client.isExisting(AccessPageFO.popular_products_block, 1000));
  }, 'common_client');
  scenario('Go back to the Back Office and check the created order details', () => {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
    test('should search for the created order by reference', () => client.waitAndSetValue(OrderPage.search_by_reference_input, (global.tab['orderReference']).split(" ")[2]));
    test('should go to search order', () => client.waitForExistAndClick(OrderPage.search_order_button));
    test('should go to the order', () => client.scrollWaitForExistAndClick(OrderPage.view_order_button.replace('%NUMBER', 1), 150, 2000));
    test('should check "name" of the products', async () => {
      await client.checkTextValue(OrderPage.product_name.replace('%NUMBER', 1), global.tab['firstProductName']);
      await client.checkTextValue(OrderPage.product_name.replace('%NUMBER', 2), global.tab['secondProductName']);
    });
    test('should check "unit price" of the products', async () => {
      await client.checkTextValue(OrderPage.product_basic_price_TTC.replace('%NUMBER', 1), global.tab['firstProductPrice']);
      await client.checkTextValue(OrderPage.product_basic_price_TTC.replace('%NUMBER', 2), global.tab['secondProductPrice']);
    });
    test('should check "quantity"', async () => {
      await client.checkTextValue(OrderPage.order_quantity.replace('%NUMBER', 1), global.tab['firstProductQuantity']);
      await client.checkTextValue(OrderPage.order_quantity.replace('%NUMBER', 2), global.tab['secondProductQuantity']);
    });
    test('should check the price of 300 products', async () => {
      await client.checkTextValue(OrderPage.total_price_tax_included.replace('%NUMBER', 1), global.tab['firstProductTotalPrice']);
      await client.checkTextValue(OrderPage.total_price_tax_included.replace('%NUMBER', 2), global.tab['secondProductTotalPrice']);
    });
    test('should check the shipping price', () => client.checkTextValue(OrderPage.shipping_cost_price, global.tab['shippingPrice']));
    test('should check that the sub total is equal to "10,584.00€ (tax incl.)"', () => client.checkTextValue(OrderPage.total_price, global.tab['subTotal']));
    test('should check that the total is equal to "10,592.40€ (tax incl.)"', () => client.checkTextValue(OrderPage.total_order, global.tab['totalPrice']));
    test('should Check if the status is "Awaiting bank wire payment" payment method name', () => client.checkTextValue(OrderPage.status.replace("%STATUS", "bank wire"), "Awaiting bank wire payment"));
  }, 'common_client');
  stockCommonScenarios.checkStockProduct(client, productData[0].name + date_time, Menu, Stock, '50', '300', '350');
  stockCommonScenarios.checkStockProduct(client, productData[1].name + date_time, Menu, Stock, '50', '300', '350');
  scenario('Change the status of the order', client => {
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
    test('should search for the created order by reference', () => client.waitAndSetValue(OrderPage.search_by_reference_input, (global.tab['orderReference']).split(" ")[2]));
    test('should go to search order', () => client.waitForExistAndClick(OrderPage.search_order_button));
    test('should go to the order', () => client.scrollWaitForExistAndClick(OrderPage.view_order_button.replace('%NUMBER', 1), 150, 2000));
    test('should set order status to Payment accepted', async () => {
      await client.pause(1000);
      await client.updateStatus('Payment accepted');
    });
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    /**
     * should refresh the page, to pass the error
     */
    test('should refresh the page', () => client.refresh());
    test('should set order status to Delivered ', () => client.updateStatus('Delivered'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    /**
     * should refresh the page, to pass the error
     */
    test('should refresh the page', () => client.refresh());
  }, 'order');
  commonProductScenarios.checkProductQuantity(Menu, AddProductPage, productData[0].name + date_time, '50');
  commonProductScenarios.checkProductQuantity(Menu, AddProductPage, productData[1].name + date_time, '50');
  stockCommonScenarios.checkStockProduct(client, productData[0].name + date_time, Menu, Stock, '50', '0', '50');
  stockCommonScenarios.checkStockProduct(client, productData[1].name + date_time, Menu, Stock, '50', '0', '50');
  scenario('Check the movement of the "' + productData[0].name + date_time + '"', client => {
    test('should go to "Employee" page', async () => {
      await client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu);
      await client.waitForVisible(Menu.Configure.AdvancedParameters.advanced_parameters_menu);
      await client.goToSubtabMenuPage(Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.team_submenu);
    });
    test('should go to "Stocks" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu));
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, '300', '-', 'Customer Order', productData[0].reference, dateSystem, productData[0].name + date_time, true);
  }, 'stocks');
  scenario('Check the movement of the "' + productData[1].name + date_time + '"', client => {
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, '300', '-', 'Customer Order', productData[1].reference, dateSystem,  productData[1].name + date_time, true);
  }, 'stocks');
  scenario('Check that the created order is opened in a new window', client => {
    test('should click on "Customer Order" link  ', () => client.waitForExistAndClick(Movement.type_value.replace('%P', 1)));
    test('should check that the created order is opened in a new window', async () => {
      await client.switchWindow(2);
      await client.checkTextValue(OrderPage.page_title, global.tab['orderReference'].split(" ")[2], 'contain');
    });
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
