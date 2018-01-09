var CommonClient = require('./../common_client');
const {OrderPage} = require('../../selectors/BO/order_page');
const {CreateOrder} = require('../../selectors/BO/create_order');
const {buyOrderPage}= require('../../selectors/FO/buy_order_page');

global.tab = [];

class Order extends CommonClient {

  checkBasicToTalPrice() {
    return this.client
      .scroll(0, 600)
      .waitForExistAndClick(OrderPage.edit_product_button)
      .waitForExist(OrderPage.product_basic_price, 90000)
      .then(() => this.client.getValue(OrderPage.product_basic_price))
      .then((basicPrice) => expect('€' + basicPrice).to.eql(global.tab["basic_price"]))
  }

  checkShippingMethod() {
    return this.client
      .waitForExist(OrderPage.shipping_method, 90000)
      .then(() => this.client.getText(OrderPage.shipping_method))
      .then((shippingMethod) => {
        expect(global.tab["method"]).to.contain(shippingMethod);
      })
  }

  checkGreenConfirmation() {
    return this.client
      .waitForVisible(buyOrderPage.green_confirmation, 90000)
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
      .pause(2000)
  }

  addOrderMessage(mesageOrder) {
    return this.client
      .scroll(0.900)
      .waitForExist(CreateOrder.order_message_textarea, 90000)
      .pause(2000)
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

  checkBasicPrice() {
    return this.client
      .scroll(0, 1000)
      .waitForExistAndClick(OrderPage.edit_product_button)
      .waitForExist(OrderPage.product_basic_price, 90000)
      .then(() => this.client.getValue(OrderPage.product_basic_price))
      .then((basicPrice) => expect(basicPrice).to.eql(global.basic_price));
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
      .waitForExistAndClick(OrderPage.download_invoice_button)
      .then(() => this.client.getText(OrderPage.download_invoice_button))
      .then((name) => global.invoiceFileName = name.replace('#', ''))
      .then(() => this.client.pause(2000));
  }

  downloadDeliveryDocument() {
    return this.client
      .waitForExistAndClick(OrderPage.download_delivery_button)
      .then(() => this.client.getText(OrderPage.download_delivery_button))
      .then((name) => global.invoiceFileName = name.replace('#', ''))
      .then(() => this.client.pause(2000));
  }

  checkEnable(selector) {
    return this.client
      .waitForExist(selector, 90000)
      .isEnabled(selector)
      .then((text) => expect(text).to.be.false);
  }

}

module.exports = Order;
