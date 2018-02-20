var CommonClient = require('./common_client');
const {OrderPage} = require('../selectors/BO/order');
const {CreateOrder} = require('../selectors/BO/order');

global.tab = [];

class Order extends CommonClient {

  addOrderMessage(orderMessage) {
    return this.client
      .scroll(0.900)
      .waitForExist(CreateOrder.order_message_textarea, 90000)
      .pause(2000)
      .setValue(CreateOrder.order_message_textarea, orderMessage)
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

  downloadDocument(selector) {
    return this.client
      .waitForExistAndClick(selector)
      .then(() => this.client.getText(selector))
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
