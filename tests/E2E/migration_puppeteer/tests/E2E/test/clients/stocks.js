var CommonClient = require('./common_client');
let promise = Promise.resolve();
global.tab = [];
let dateFormat = require('dateformat');

class ModifyQuantity extends CommonClient {

  async goToStockMovements(Menu, Movement) {
    await this.waitForExistAndClick(Menu.Sell.Catalog.movement_tab);
    await page.waitFor(Movement.variation, {timeout: 90000});
    await this.pause(1000);
    global.isVisible = await this.isVisible(Movement.sort_data_time_icon, 2000);
    if (isVisible) {
      await this.waitForVisibleAndClick(Movement.sort_data_time_icon);
    }
    await this.pause(1000);
  }

  async modifyProductQuantity(Stock, order, quantity, comma = 'false') {
    await page.waitForSelector(Stock.product_quantity.replace('%O', order));
    global.tab["productQuantity"] = await this.getText(Stock.product_quantity.replace('%O', order));
    await this.fillInputNumber(Stock.product_quantity_input.replace('%O', order), String(quantity));
    let text_modified = await this.getText(Stock.product_quantity_modified.replace('%O', order));
    if (comma === true)
      expect(text_modified.substring(14)).to.be.equal((Number(global.tab["productQuantity"]) + parseFloat(quantity.replace(',', '.'))).toString());
    else
      expect(text_modified.substring(14)).to.be.equal((Number(global.tab["productQuantity"]) + quantity).toString());
  }

  async checkMovement(selector, order, quantity, variation, type, reference = "", dateAndTime = "", employee = "", productName = "") {
    await this.waitForVisible(selector.variation_value.replace('%P', order), 90000);
    await page.$eval(selector.variation_value.replace('%P', order), el => el.innerText).then((text) => {
      expect(text).to.be.equal(variation);
    });
    await page.$eval(selector.quantity_value.replace('%P', order), el => el.innerText).then((text) => {
      expect(text.substring(2)).to.be.equal(quantity);
    });
    await page.$eval(selector.type_value.replace('%P', order), el => el.innerText).then((text) => {
      expect(text.indexOf(type)).to.not.equal(-1);
    });
    await page.$eval(selector.reference_value.replace('%P', order), el => el.innerText).then((text) => {
      expect(text).to.be.equal(reference);
    });
    await page.$eval(selector.time_movement.replace('%P', order), el => el.innerText).then((text) => {
      if (dateAndTime !== "") {
        expect(text).to.be.contains(dateAndTime);
      }
    });
    await page.$eval(selector.employee_value.replace('%P', order), el => el.innerText).then((text) => {
      if (employee !== '') {
        expect(text).to.be.equal(employee);
      }
    });
    await page.$eval(selector.product_value.replace('%P', order), el => el.innerText).then((text) => {
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