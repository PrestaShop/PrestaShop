var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

class OrderBO extends PrestashopClient {


  goToOrdersList() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.orders_subtab, 90000)
      .moveToObject(selector.BO.Orders.Order.orders_subtab)
      .waitForExist(selector.BO.Orders.Order.order_submenu, 90000)
      .click(selector.BO.Orders.Order.order_submenu)
  }

  searchOrderCreatedByReference() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.reference_search, 9000)
      .setValue(selector.BO.Orders.Order.reference_search, global.reference)
      .click(selector.BO.Orders.Order.search_order_button)
  }

  viewOrder() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.view_order_button, 9000)
      .click(selector.BO.Orders.Order.view_order_button)
  }

  checkOrderStatus(status) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.order_check_status, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.order_check_status))
      .then((status_txt) => {
        expect(status_txt).to.eql(status)
      })
  }

  checkShippingPrice() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_shipping_price, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_shipping_price))
      .then((shippingprice) => {
        expect(shippingprice).to.eql(global.shipping_price)
      })
  }

  checkProduct() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_product, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_product))
      .then((product) => {
        expect(product).to.eql(global.product)
      })
  }

  checkOrderMessage(message) {
    return this.client
      .scroll(0, 600)
      .waitForExist(selector.BO.Orders.Order.check_message_order, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_message_order))
      .then((value) => {
        expect(value).to.eql(message)
      });
  }

  checkTotalPrice() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_total_price, 9000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_total_price))
      .then((value) => {
        expect(value).to.eql(global.total_price)
      });
  }

  checkBasicPrice() {
    return this.client
      .scroll(0, 600)
      .waitForExist(selector.BO.Orders.Order.second_edit_product_button, 90000)
      .click(selector.BO.Orders.Order.second_edit_product_button)
      .waitForExist(selector.BO.Orders.Order.product_basic_price, 90000)
      .then(() => this.client.getValue(selector.BO.Orders.Order.product_basic_price))
      .then((basicPrice) => {
        expect('€' + basicPrice).to.eql(global.basic_price);
      })
  }

  checkQuantity(quantity) {
    return this.client
      .waitForExist(selector.BO.Orders.Order.quantity_value, 90000)
      .then(() => this.client.getValue(selector.BO.Orders.Order.quantity_value))
      .then((value) => {
        expect(value).to.eql(quantity)
      })
  }

  checkCustomerName() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.customer_name, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.customer_name))
      .then((name) => {
        expect(name).to.contain(global.customer);
      })
  }

  checkShippingMethod() {
    return this.client
      .waitForExist(selector.BO.Orders.Order.check_shipping_method, 90000)
      .then(() => this.client.getText(selector.BO.Orders.Order.check_shipping_method))
      .then((shippingMethod) => {
        expect(global.method).to.contain(shippingMethod);
      })
  }
}

module.exports = OrderBO;
