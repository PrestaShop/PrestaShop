var CommonClient = require('./common_client');
let promise = Promise.resolve();
global.tab = [];

class ModifyQuantity extends CommonClient {

  goToStockMovements(Menu, Movement) {
    return this.client
      .waitForExistAndClick(Menu.Sell.Catalog.movement_tab)
      .waitForExist(Movement.variation, 90000)
      .pause(1000)
      .isVisible(Movement.sort_data_time_icon, 2000)
      .then(() => {
        if (global.isVisible) {
          this.client.waitForVisibleAndClick(Movement.sort_data_time_icon);
        }
        this.client.pause(1000);
      });
  }

  modifyProductQuantity(Stock, order, quantity) {
    return this.client
      .pause(1000)
      .waitForExist(Stock.product_quantity.replace('%O', order), 90000)
      .then(() => this.client.getText(Stock.product_quantity.replace('%O', order)))
      .then((text) => global.tab["productQuantity"] = text)
      .waitAndSetValue(Stock.product_quantity_input.replace('%O', order), quantity)
      .then(() => this.client.getText(Stock.product_quantity_modified.replace('%O', order)))
      .then((text) => expect(text.substring(14)).to.be.equal((Number(global.tab["productQuantity"]) + quantity).toString()));
  }

  checkMovement(selector, order, quantity, variation, type, reference = "") {
    return this.client
      .waitForVisible(selector.variation_value.replace('%P', order), 90000)
      .then(() => this.client.getText(selector.variation_value.replace('%P', order)))
      .then((text) => expect(text).to.be.equal(variation))
      .then(() => this.client.getText(selector.quantity_value.replace('%P', order)))
      .then((text) => expect(text.substring(2)).to.be.equal(quantity))
      .then(() => this.client.getText(selector.type_value.replace('%P', order)))
      .then((text) => expect(text.indexOf(type)).to.not.equal(-1))
      .then(() => this.client.getText(selector.reference_value.replace('%P', order)))
      .then((text) => expect(text).to.be.equal(reference));
  }

  changeOrderState(selector, state) {
    return this.client
      .waitForExist(selector.order_state_select, 90000)
      .execute(function () {
        document.querySelector('#id_order_state').style = "";
      })
      .selectByVisibleText(selector.order_state_select, state)
      .waitForExistAndClick(selector.update_status_button)
  }

  checkOrderMovement(Movement, client) {
    if (global.tab['firstMovementDate'] === global.tab['secondMovementDate']) {
      promise = client.checkMovement(Movement, 1, "15", "+", "Employee Edition", "firstProduct");
      return promise.then(() => client.checkMovement(Movement, 2, "50", "+", "Employee Edition", 'secondProduct'));
    } else {
      promise = client.checkMovement(Movement, 1, "50", "+", "Employee Edition", 'secondProduct');
      return promise.then(() => client.checkMovement(Movement, 2, "15", "+", "Employee Edition", "firstProduct"));
    }
  }
}

module.exports = ModifyQuantity;
