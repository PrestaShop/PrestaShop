var CommonClient = require('./common_client');
const {OrderPage} = require('../selectors/BO/order');
const {CreateOrder} = require('../selectors/BO/order');
let pdfUtil = require('pdf-to-text');
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

  async getCreditSlipDocumentName(selector) {
    let name = await this.client.getText(selector);
    global.creditSlip = await name.replace('#', '');
  }

  getNameInvoice(selector, pause = 0) {
    return this.client
      .pause(pause)
      .then(() => this.client.getText(selector))
      .then((name) => global.invoiceFileName = name.replace('#', ''))
      .then(() => this.client.pause(2000));
  }

  async checkWordNumber(folderPath, fileName, text, occurrence) {
    await pdfUtil.pdfToText(folderPath + fileName + '.pdf', function (err, data) {
      global.numberOccurence = (data.split(text).length) - 1;
    });

    return this.client
      .pause(2000)
      .then(() => expect(global.numberOccurence, text + "does not exist " + occurrence + " in the PDF document").to.equal(occurrence));
  }
}

module.exports = Order;
