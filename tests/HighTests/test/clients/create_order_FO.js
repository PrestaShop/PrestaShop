var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');


class OrderFo extends PrestashopClient {
  changeFOLanguage() {
    return this.client
      .waitForExist(selector.FO.common.language_selector, 9000)
      .click(selector.FO.common.language_selector)
      .waitForExist(selector.FO.common.language_EN, 90000)
      .click(selector.FO.common.language_EN)
  }

  chooseProduct() {
    return this.client
      .waitForExist(selector.FO.ProductPage.first_product, 90000)
      .click(selector.FO.ProductPage.first_product)
  }


  selectProductSize(size) {
    return this.client
      .waitForExist(selector.FO.ProductPage.first_product_size, 9000)
      .selectByValue(selector.FO.ProductPage.first_product_size, size)
  }

  chooseColor() {
    return this.client
      .pause(1000)
      .waitForExist(selector.FO.ProductPage.first_product_color, 9000)
      .click(selector.FO.ProductPage.first_product_color)
  }

  addQuantity(qty) {
    return this.client
      .waitForExist(selector.FO.ProductPage.first_product_quantity, 9000)
      .setValue(selector.FO.ProductPage.first_product_quantity, qty)
  }

  clickOnAddToCartButton() {
    return this.client
      .pause(2000)
      .waitForExist(selector.FO.BuyOrderPage.add_to_cart_button, 90000)
      .click(selector.FO.BuyOrderPage.add_to_cart_button)
  }

  checkGreenConfirmation() {
    return this.client
      .waitForVisible(selector.FO.BuyOrderPage.green_confirmation, 90000)

  }

  clickOnCommandButton() {
    return this.client
      .waitForExist(selector.FO.LayerCartPage.command_button, 90000)
      .click(selector.FO.LayerCartPage.command_button)
      .waitForExist(selector.FO.BuyOrderPage.proceed_to_checkout_button, 90000)
      .click(selector.FO.BuyOrderPage.proceed_to_checkout_button)
  }

  clickOnConfirmAddressesButton() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.checkout_step2_continue_button, 90000)
      .click(selector.FO.BuyOrderPage.checkout_step2_continue_button)
  }

  chooseShippingMethod() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.shipping_method_option, 9000)
      .click(selector.FO.BuyOrderPage.shipping_method_option)
  }

  clickOnConfirmDeliveryButton() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.checkout_step3_continue_button, 90000)
      .click(selector.FO.BuyOrderPage.checkout_step3_continue_button)
  }

  createMessage(message) {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.message_textarea, 9000)
      .setValue(selector.FO.BuyOrderPage.message_textarea, message)
  }

  choosePayment() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.checkout_step4_payment_radio, 90000)
      .click(selector.FO.BuyOrderPage.checkout_step4_payment_radio)
  }

  checkConditionToApprove() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.condition_check_box, 9000)
      .click(selector.FO.BuyOrderPage.condition_check_box)
  }

  clickOnOrderConfirmationButton() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.confirmation_order_button, 9000)
      .click(selector.FO.BuyOrderPage.confirmation_order_button)
  }

  checkOrderConfirmationMessage(confirmationMessage) {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.confirmation_order_message, 9000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.confirmation_order_message))
      .then((message) => {
        expect(message).to.contain(confirmationMessage)
      })
  }

  getOrderReference() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.order_reference, 9000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.order_reference))
      .then((ref) => {
        global.reference = ref.split(': ')[1];
      })
  }

  getProductBasicPrice() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.order_basic_price, 9000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.order_basic_price))
      .then((price) => global.basic_price = price)

  }


  getProductTotalPrice() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.order_total_price, 9000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.order_total_price))
      .then((totalprice) => global.total_price = totalprice)
      .then((totalprice) => {
      })
  }

  getShippingMethod() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.shipping_method, 90000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.shipping_method))
      .then((method) => {
        global.method = method.split(': ')[1];
      })
  }

  getShippingPrice() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.order_shipping_prince_value, 90000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.order_shipping_prince_value))
      .then((shippingprice) => global.shipping_price = shippingprice)
  }

  getProduct() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.order_product, 9000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.order_product))
      .then((product) => global.product = product)
  }

  getCustomerName() {
    return this.client
      .waitForExist(selector.FO.BuyOrderPage.customer_name, 9000)
      .then(() => this.client.getText(selector.FO.BuyOrderPage.customer_name))
      .then((customer) => global.customer = customer)
      .then((customer) => {
      })
  }
}

module.exports = OrderFo;
