var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

class Order extends PrestashopClient {

  goToOrdersList() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.orders_subtab, 90000)
      .moveToObject(selector.BO.Orders.Order.orders_subtab)
      .waitForExist(selector.BO.Orders.Order.order_submenu, 90000)
      .click(selector.BO.Orders.Order.order_submenu)
  }

  clickOnAddNewOrderButton() {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.new_order_button, 90000)
      .click(selector.BO.Orders.CreateOrder.new_order_button)
  }

  searchCustomer(customerName) {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.customer_search_input, 90000)
      .setValue(selector.BO.Orders.CreateOrder.customer_search_input, customerName)
      .waitForExist(selector.BO.Orders.CreateOrder.choose_customer_button, 90000)
      .click(selector.BO.Orders.CreateOrder.choose_customer_button)
  }

  searchProduct(productName, qty) {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.product_search_input, 90000)
      .setValue(selector.BO.Orders.CreateOrder.product_search_input, productName)
  }

  selectProductType(type) {
    return this.client
      .waitForVisible(selector.BO.Orders.CreateOrder.product_select, 9000)
      .selectByVisibleText(selector.BO.Orders.CreateOrder.product_select, type)
  }

  selectProductCombination(combination) {
    return this.client
      .pause(2000)
      .waitForExist(selector.BO.Orders.CreateOrder.product_combination, 9000)
      .selectByVisibleText(selector.BO.Orders.CreateOrder.product_combination, combination)
  }

  addProductQuantity(qty) {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.quantity_input, 90000)
      .setValue(selector.BO.Orders.CreateOrder.quantity_input, qty)
  }

  clickOnAddToCartButton() {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.add_to_cart_button, 90000)
      .click(selector.BO.Orders.CreateOrder.add_to_cart_button)
  }

  getBasicPriceValue() {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.basic_price_value, 90000)
      .then(() => this.client.getValue(selector.BO.Orders.CreateOrder.basic_price_value))
      .then((price) => global.basic_price = price)
  }

  selectDelivery() {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.delivery_option, 90000)
      .selectByIndex(selector.BO.Orders.CreateOrder.delivery_option, 1)
      .pause(3000)
  }

  addOrderMessage() {
    return this.client
      .waitForVisible(selector.BO.Orders.CreateOrder.order_message_textarea, 90000)
      .setValue(selector.BO.Orders.CreateOrder.order_message_textarea,'Order message test')

  }

  selectPayment() {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.payment, 90000)
      .selectByValue(selector.BO.Orders.CreateOrder.payment, "ps_checkpayment")
  }

  selectOrderStatus() {
    return this.client
      .waitForExist(selector.BO.Orders.order_state_select, 90000)
      .selectByValue(selector.BO.Orders.order_state_select, '1')
  }

  clickOnCreateOrder() {
    return this.client
      .waitForExist(selector.BO.Orders.CreateOrder.create_order_button, 90000)
      .click(selector.BO.Orders.CreateOrder.create_order_button)
  }

  checkOrderStatus(status) {
    return this.client
      .pause(3000)
      .waitForExist(selector.BO.Orders.Order.order_check_status, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.order_check_status))
      .then((status_txt) => expect(status_txt).to.eql(status));
  }

  checkShippingCost(cost) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_shipping_Cost, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_shipping_Cost))
      .then((value) => {
        expect(value).to.eql(cost)
      })
  }

  checkOrderMessage(message) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_message_order, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_message_order))
      .then((value) => expect(value).to.eql(message))
  }

  checkPayment(payment) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_payment_type, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_payment_type))
      .then((value) => {
        expect(value).to.eql(payment)
      })

  }

  checkQuantity(qty) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_quantity, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_quantity))
      .then((value) => {
        expect(value).to.eql(qty)
      })
  }

  checkProductInformation(color, productName, reference, size) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.product_Url, 9000)
      .then(() => this.client.getText(selector.BO.Orders.Order.product_Url))
      .then((value) => {
        expect(value).to.contains(color, productName, reference, size);
      })
  }

  checkCustomer(customerName) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.customer_name, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.customer_name))
      .then((name) => {
        expect(name).to.contain(customerName);
      })
  }

  checkBasicPrice() {
    return this.client
      .scroll(0, 600)
      .waitForExist(selector.BO.Orders.Order.edit_product_button, 90000)
      .click(selector.BO.Orders.Order.edit_product_button)
      .waitForExist(selector.BO.Orders.Order.product_basic_price, 90000)
      .then(() => this.client.getValue(selector.BO.Orders.Order.product_basic_price))
      .then((basicPrice) => {
        expect(basicPrice).to.eql(global.basic_price);
      })
  }

  updateStatus(value) {
    return this.client
      .execute(function () {
        document.querySelector('#id_order_state').style = "";
      })
      .selectByVisibleText(selector.BO.Orders.order_state_select, value)
      .then(() => this.client.getValue(selector.BO.Orders.order_state_select))
      .then((order) => global.order_status = order)
  }

  clickOnUpdateStatusButton() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.update_order_status_button, 90000)
      .click(selector.BO.Orders.Order.update_order_status_button)
  }

}

module.exports = Order;
