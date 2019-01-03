/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
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
      .then((isVisible) => {
        if (isVisible) {
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
