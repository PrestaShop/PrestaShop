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
const {OrderPage} = require('../selectors/BO/order');
const {CreateOrder} = require('../selectors/BO/order');

global.tab = [];
global.orders = [];
global.lineFile = [];
let common = require('../common.webdriverio');
let fs = require('fs');
const exec = require('child_process').exec;

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

  downloadCart(selector) {
    return this.client
      .waitForExistAndClick(selector)
      .pause(2000)
      .then(() => {
        let exportDate = common.getCustomDate(0);
        let files = fs.readdirSync(downloadsFolderPath);
        for (let i = 0; i < files.length; i++) {
          if (files[i].includes('cart_' + exportDate)) {
            global.exportCartFileName = files[i];
          }
        }
      });
  }

  getShoppingCartNumber(selector) {
    return this.client
      .execute(function (selector) {
        let count = document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
        return count;
      }, selector)
      .then((count) => {
        global.shoppingCartsNumber = count.value;
      });
  }

  readFile(folderPath, fileName, pause = 0) {
    fs.readFile(folderPath + fileName, {encoding: 'utf-8'}, function (err, data) {
      global.lineFile = data.split("\n");
    });
    return this.client
      .pause(pause)
      .then(() => expect(global.lineFile, "No data").to.be.not.empty)
  }

  checkFile(folderPath, fileName, pause = 0) {
    fs.stat(folderPath + fileName, function (err, stats) {
      err === null && stats.isFile() ? global.existingFile = true : global.existingFile = false;
    });
    return this.client
      .pause(pause)
      .then(() => expect(global.existingFile).to.be.true)
  }

  checkExportedFileInfo(pause = 0) {
    return this.client
      .pause(pause)
      .then(() => {
        for (let i = 1; i < (global.lineFile.length - 1); i++) {
          expect(global.lineFile[i]).to.be.equal(global.orders[i - 1])
        }
      })
  }

  checkEnable(selector) {
    return this.client
      .waitForExist(selector, 90000)
      .isEnabled(selector)
      .then((text) => expect(text).to.be.false);
  }

  getDocumentName(selector) {
    return this.client
      .then(() => this.client.getText(selector))
      .then((name) =>{
        global.creditSlip = name.replace('#', '')
      })
  }

  async deleteDownloadedDocument(fileName, extension = "pdf") {
    await exec('rm ' + global.downloadsFolderPath + fileName + "." + extension,
      (error, stdout, stderr) => {
        if (error !== null) {
          console.log(`[exec] Error while removing the downloaded file: ${error}`);
        }
      });
    return this.client
      .pause(4000);
  }
}

module.exports = Order;
