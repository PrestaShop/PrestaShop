const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {productPage}= require('../../../selectors/FO/product_page');
const {buyOrderPage}= require('../../../selectors/FO/buy_order_page');
const {layerCart}= require('../../../selectors/FO/layer_cart_page');

scenario('Create order in FO', client => {
  scenario('Open the browser and connect to the FO', client => {
    test('should open the browser', () => client.open());
    test('should sign in FO', () => client.signInFO(AccessPageFO));
  }, 'order/order');
  scenario('Create order in FO', client => {
    test('should change the FO language to english', () => client.changeLanguage());
    test('should choose product ', () => client.waitForExistAndClick(productPage.first_product));
    test('should select product "size M" ', () => client.waitAndSelectByValue(productPage.first_product_size, '2'));
    test('should select product "color blue"', () => client.waitForExistAndClick(productPage.first_product_color));
    test('should set the product "quantity"', () => client.waitAndSetValue(productPage.first_product_quantity,"4"));
    test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(buyOrderPage.add_to_cart_button));
    test('should verify the appearance of the green confirmation', () => client.checkGreenConfirmation());
    test('should click on proceed to checkout button 1', () => client.waitForExistAndClick(layerCart.command_button));
    test('should click on proceed to checkout button 2', () => client.waitForExistAndClick(layerCart.proceed_to_checkout_button));
    test('should click on confirm adress button', () => client.waitForExistAndClick(buyOrderPage.checkout_step2_continue_button));
    test('should choose shipping method my carrier', () => client.waitForExistAndClick(buyOrderPage.shipping_method_option));
    test('should create message', () => client.waitAndSetValue(buyOrderPage.message_textarea, 'Order message test'));
    test('should click on "confirm delivery" button', () => client.waitForExistAndClick(buyOrderPage.checkout_step3_continue_button));
    test('should set the payment type "Payment by bank wire"', () => client.waitForExistAndClick(buyOrderPage.checkout_step4_payment_radio));
    test('should set "the condition to approve"', () => client.waitForExistAndClick(buyOrderPage.condition_check_box));
    test('should click on order with an obligation to pay button', () => client.waitForExistAndClick(buyOrderPage.confirmation_order_button));
    test('should check the order confirmation', () => client.checkTextValue(buyOrderPage.confirmation_order_message,'YOUR ORDER IS CONFIRMED',"contain"));
  }, 'order/order');
}, 'order/order');

scenario('Get the order informations', client => {
  test('should get the product', () => client.getTextInVar(buyOrderPage.order_product, "product"));
  test('should get the product basic price ', () => client.getTextInVar(buyOrderPage.order_basic_price, "basic_price"));
  test('should get the product total price ', () => client.getTextInVar(buyOrderPage.order_total_price, "total_price"));
  test('should get the order reference', () => client.getTextInVar(buyOrderPage.order_reference, "reference", true));
  test('should get the shipping method', () => client.getTextInVar(buyOrderPage.shipping_method, "method", true));
  test('should get the shipping price', () => client.getTextInVar(buyOrderPage.order_shipping_prince_value, "shipping_price"));
  test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
}, 'order/order', true);
