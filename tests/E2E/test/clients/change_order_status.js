var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');

global.orderQuantity = '';

class OrderStatus extends CommonClient {

  goToOrdersMenu() {
    return this.client
      .waitForExist(selector.OrderPage.orders_subtab, 90000)
      .click(selector.OrderPage.orders_subtab)
  }

  goToFirstOrder() {
    return this.client
      .waitForExist(selector.OrderPage.first_order, 90000)
      .click(selector.OrderPage.first_order)
  }

  changeOrderState(state) {
    return this.client
      .waitForExist(selector.OrderPage.order_state_select, 90000)
      .execute(function () {
        document.querySelector('#id_order_state').style = "";
      })
      .selectByVisibleText(selector.OrderPage.order_state_select, state)
      .waitForExist(selector.OrderPage.update_order_status_button, 90000)
      .click(selector.OrderPage.update_order_status_button)
  }

  getOrderQuantity() {
    return this.client
      .waitForExist(selector.OrderPage.order_quantity, 90000)
      .then(() => this.client.getText(selector.OrderPage.order_quantity))
      .then((text) => global.orderQuantity= text)
  }
}

module.exports = OrderStatus;
