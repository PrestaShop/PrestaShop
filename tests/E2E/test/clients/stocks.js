var CommonClient = require('./common_client');
let promise = Promise.resolve();
global.tab = [];
let dateFormat = require('dateformat');

class ModifyQuantity extends CommonClient {

  goToStockMovements(Menu, Movement) {
    return this.client
      .waitForExistAndClick(Menu.Sell.Catalog.movement_tab)
      .waitForExist(Movement.variation, 90000)
      .pause(1000)
      .isVisible(Movement.sort_data_time_icon, 2000)
      .then((isVisible) => {
        if (isVisible) {
          this.client.waitForVisibleAndClick(Movement.sort_data_time_icon);
        }
        this.client.pause(1000);
      });
  }

  modifyProductQuantity(Stock, order, quantity, comma = 'false') {
    return this.client
      .pause(1000)
      .waitForExist(Stock.product_quantity.replace('%O', order), 90000)
      .then(() => this.client.getText(Stock.product_quantity.replace('%O', order)))
      .then((text) => global.tab["productQuantity"] = text)
      .waitAndSetValue(Stock.product_quantity_input.replace('%O', order), quantity)
      .then(() => this.client.getText(Stock.product_quantity_modified.replace('%O', order)))
      .then((text) => {
        if (comma === true)
          expect(text.substring(14)).to.be.equal((Number(global.tab["productQuantity"]) + parseFloat(quantity.replace(',', '.'))).toString());
        else
          expect(text.substring(14)).to.be.equal((Number(global.tab["productQuantity"]) + quantity).toString());
      });
  }

  checkMovement(selector, order, quantity, variation, type, reference = "", dateAndTime = "", employee = "", productName = "") {
    return this.client
      .waitForVisible(selector.variation_value.replace('%P', order), 90000)
      .then(() => this.client.getText(selector.variation_value.replace('%P', order)))
      .then((text) => expect(text).to.be.equal(variation))
      .then(() => this.client.getText(selector.quantity_value.replace('%P', order)))
      .then((text) => expect(text.substring(2)).to.be.equal(quantity))
      .then(() => this.client.getText(selector.type_value.replace('%P', order)))
      .then((text) => expect(text.indexOf(type)).to.not.equal(-1))
      .then(() => this.client.getText(selector.reference_value.replace('%P', order)))
      .then((text) => expect(text).to.be.equal(reference))
      .then(() => this.client.getText(selector.time_movement.replace('%P', order)))
      .then((text) => {
        if (dateAndTime !== "") {
          expect(text).to.be.contain(dateAndTime)
        }
      })
      .then(() => this.client.getText(selector.employee_value.replace('%P', order)))
      .then((text) => {
        if (employee !== '') {
          expect(text).to.be.equal(employee)
        }
      })
      .then(() => this.client.getText(selector.product_value.replace('%P', order)))
      .then((text) => {
        if (productName !== '') {
          expect(text).to.be.equal(productName)
        }
      });
  }

  checkOrderMovement(Movement, client) {
    return promise
      .then(() => client.pause(2000))
      .then(() => client.getTextInVar(Movement.reference_value.replace('%P', 1), 'firstReference'))
      .then(() => {
        if (global.tab['firstReference'] === 'firstProduct') {
          return promise
            .then(() => client.checkMovement(Movement, 2, '50', '+', 'Employee Edition', 'secondProduct'))
            .then(() => client.checkMovement(Movement, 1, '15', '+', 'Employee Edition', 'firstProduct'));
        } else {
          return promise
            .then(() => client.checkMovement(Movement, 1, '50', '+', 'Employee Edition', 'secondProduct'))
            .then(() => client.checkMovement(Movement, 2, '15', '+', 'Employee Edition', 'firstProduct'));
        }
      });
  }
}

module.exports = ModifyQuantity;
