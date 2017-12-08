var CommonClient = require('./../common_client');
const {OrderPage} = require('../../selectors/BO/order_page');
const {CreateOrder} = require('../../selectors/BO/create_order');
const {buyOrderPage}= require('../../selectors/FO/buy_order_page');
const {productPage}= require('../../selectors/FO/product_page');

global.tab=[];

class Order extends CommonClient {

  checkBasicToTalPrice() {
    return this.client
      .scroll(0, 600)
      .waitForExist(OrderPage.second_edit_product_button, 90000)
      .click(OrderPage.second_edit_product_button)
      .waitForExist(OrderPage.product_basic_price, 90000)
      .then(() => this.client.getValue(OrderPage.product_basic_price))
      .then((basicPrice) => expect('€' + basicPrice).to.eql(global.tab["basic_price"]))
  }

  checkShippingMethod() {
    return this.client
      .waitForExist(OrderPage.check_shipping_method, 90000)
      .then(() => this.client.getText(OrderPage.check_shipping_method))
      .then((shippingMethod) => {
        expect(global.tab["method"]).to.contain(shippingMethod);
      })
  }

  checkGreenConfirmation() {
    return this.client
      .waitForVisible(buyOrderPage.green_confirmation, 90000)
  }

  addQuantity(qty) {
    return this.client
      .pause(1000)
      .waitForExist(productPage.first_product_quantity, 9000)
      .setValue(productPage.first_product_quantity, qty)
  }

  checkOrderConfirmationMessage(confirmationMessage) {
    return this.client
      .waitForExist(buyOrderPage.confirmation_order_message, 9000)
      .then(() => this.client.getText(buyOrderPage.confirmation_order_message))
      .then((message) => {
        expect(message).to.contain(confirmationMessage)
      })
  }

  getBasicPriceValue() {
    return this.client
      .waitForExist(CreateOrder.basic_price_value, 90000)
      .then(() => this.client.getValue(CreateOrder.basic_price_value))
      .then((price) => global.basic_price = price);
  }

  selectDelivery() {
    return this.client
      .waitForExist(CreateOrder.delivery_option, 90000)
      .selectByIndex(CreateOrder.delivery_option, 1)
      .pause(3000)
  }

  addOrderMessage(mesageOrder) {
    return this.client
      .scroll(0.900)
      .waitForExist(CreateOrder.order_message_textarea, 90000)
      .pause(3000)
      .setValue(CreateOrder.order_message_textarea, mesageOrder)
  }

  checkProductInformation(color, productName, reference, size) {
    return this.client
      .waitForExist(OrderPage.product_Url, 9000)
      .then(() => this.client.getText(OrderPage.product_Url))
      .then((value) => {
        expect(value).to.contains(color, productName, reference, size);
      })
  }

  checkCustomer(customerName) {
    return this.client
      .waitForExist(OrderPage.customer_name, 90000)
      .then(() => this.client.getText(OrderPage.customer_name))
      .then((name) => {
        expect(name).to.contain(customerName);
      })
  }

  checkBasicPrice() {
    return this.client
      .scroll(0, 1000)
      .waitForExist(OrderPage.edit_product_button, 90000)
      .click(OrderPage.edit_product_button)
      .waitForExist(OrderPage.product_basic_price, 90000)
      .then(() => this.client.getValue(OrderPage.product_basic_price))
      .then((basicPrice) => {
        expect(basicPrice).to.eql(global.basic_price);
      })
  }

  updateStatus(value) {
    return this.client
      .execute(function () {
        document.querySelector('#id_order_state').style = "";
      })
      .selectByVisibleText(OrderPage.order_state_select, value)
      .then(() => this.client.getValue(OrderPage.order_state_select))
      .then((order) => global.order_status = order)
  }

  downloadDocument() {
    return this.client
      .waitForExist(OrderPage.download_button, 90000)
      .click(OrderPage.download_button)
      .then(() => this.client.getText(OrderPage.download_button))
      .then((name) => global.invoiceFileName = name.replace('#', ''))
      .then(() => this.client.pause(2000));
  }

  downloadDeliveryDocument() {
    return this.client
      .waitForExist(OrderPage.download_delivery_button, 90000)
      .click(OrderPage.download_delivery_button)
      .then(() => this.client.getText(OrderPage.download_delivery_button))
      .then((name) => global.invoiceFileName = name.replace('#', ''))
      .then(() => this.client.pause(2000));
  }
}

module.exports = Order;
