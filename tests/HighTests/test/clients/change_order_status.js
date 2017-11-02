var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

global.orderQuantity = '';

class OrderStatus extends PrestashopClient {

  goToOrdersMenu() {
    return this.client
      .waitForExist(selector.BO.OrderPage.orders_subtab, 90000)
      .click(selector.BO.OrderPage.orders_subtab)
  }

  goToFirstOrder() {
    return this.client
      .waitForExist(selector.BO.OrderPage.first_order, 90000)
      .click(selector.BO.OrderPage.first_order)
  }

  changeOrderState(state) {
    return this.client
      .waitForExist(selector.BO.OrderPage.order_state_select, 90000)
      .execute(function () {
        document.querySelector('#id_order_state').style = "";
      })
      .selectByVisibleText(selector.BO.OrderPage.order_state_select, state)
      .waitForExist(selector.BO.OrderPage.update_status_button, 90000)
      .click(selector.BO.OrderPage.update_status_button)
  }

  getOrderQuantity() {
    return this.client
      .waitForExist(selector.BO.OrderPage.order_quantity, 90000)
      .then(() => this.client.getText(selector.BO.OrderPage.order_quantity))
      .then((text) => global.orderQuantity= text)
  }
}

module.exports = OrderStatus;
