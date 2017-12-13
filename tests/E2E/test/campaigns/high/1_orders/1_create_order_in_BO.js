const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OrderPage} = require('../../../selectors/BO/order_page');
const {CreateOrder} = require('../../../selectors/BO/create_order');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
let promise = Promise.resolve();
scenario('Create order in BO', client => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'order/order');

  scenario('Create order in BO', client => {

    test('should close the onboarding modal if exist', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.welcome_modal))
        .then(() => client.closeBoarding(OnBoarding.popup_close_button))
    });
    test('should go to orders list', () => client.goToSubtabMenuPage(OrderPage.orders_subtab, OrderPage.order_submenu));
    test('should click on "Add new order" button', () => client.waitForExistAndClick(CreateOrder.new_order_button));
    test('should search for a customer', () => client.waitAndSetValue(CreateOrder.customer_search_input, 'john doe'));
    test('should choose the customer', () => client.waitForExistAndClick(CreateOrder.choose_customer_button));
    test('should search for a product by name', () => client.waitAndSetValue(CreateOrder.product_search_input, 'Blouse'));
    test('should set the product type', () => client.waitAndSelectByValue(CreateOrder.product_select, '2'));
    test('should set the product combination', () => client.waitAndSelectByValue(CreateOrder.product_combination, '8'));
    test('should set the product quantity', () => client.waitAndSetValue(CreateOrder.quantity_input, '4'));
    test('should click on "Add to cart" button', () => client.scrollWaitForExistAndClick(CreateOrder.add_to_cart_button));
    test('should get the basic product price', () => client.getBasicPriceValue());
    test('should set the delivery option ', () => client.selectDelivery());
    test('should add an order message ', () => client.addOrderMessage('Order message test'));
    test('should set the payment type ', () => client.waitAndSelectByValue(CreateOrder.payment, 'ps_checkpayment'));
    test('should set the order status ', () => client.waitAndSelectByValue(OrderPage.order_state_select, '1'));
    test('should click on "Create the order"', () => client.waitForExistAndClick(CreateOrder.create_order_button));
  }, 'order/order');
}, 'order/order');

