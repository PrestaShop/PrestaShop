var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');

global.orderQuantity = '';

class OrderStatus extends CommonClient {

  goToOrdersMenu() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.orders_subtab, 90000)
      .click(selector.BO.Orders.Order.orders_subtab)
  }

  goToFirstOrder() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.first_order, 90000)
      .click(selector.BO.Orders.Order.first_order)
  }

  changeOrderState(state) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.order_state_select, 90000)
      .execute(function () {
        document.querySelector('#id_order_state').style = "";
      })
      .selectByVisibleText(selector.BO.Orders.Order.order_state_select, state)
      .waitForExist(selector.BO.Orders.Order.update_order_status_button, 90000)
      .click(selector.BO.Orders.Order.update_order_status_button)
  }

  getOrderQuantity() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.order_quantity, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.order_quantity))
      .then((text) => global.orderQuantity= text)
  }
}

module.exports = OrderStatus;
