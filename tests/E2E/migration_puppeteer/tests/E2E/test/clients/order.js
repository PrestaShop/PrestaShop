var CommonClient = require('./common_client');
const {OrderPage} = require('../selectors/BO/order');
const {CreateOrder} = require('../selectors/BO/order');
let pdfUtil = require('pdf-to-text');
global.tab = [];
global.orders = [];
global.lineFile = [];
global.order_status = '';
let fs = require('fs');
const exec = require('child_process').exec;

class Order extends CommonClient {

  async addOrderMessage(orderMessage) {
    await page.waitFor(2000);
    await this.waitForExist(CreateOrder.order_message_textarea, 90000);
    await this.pause(2000);
    await this.waitAndSetValue(CreateOrder.order_message_textarea, orderMessage, 3000);
    await this.waitForExistAndClick(CreateOrder.order_message_div, 2000);
  }

  async updateStatus(value) {
    await page.waitFor(3000);
    await page.evaluate(() => {
      let element = document.querySelector('#id_order_state');
      element.style = "";
    });
    await this.waitAndSelectByVisibleText(OrderPage.order_state_select, value);
    global.order_status = await global.selectValue;
  }

  downloadDocument(selector) {
    return this.client
      .waitForExistAndClick(selector)
      .then(() => this.client.getText(selector))
      .then((name) => global.invoiceFileName = name.replace('#', ''))
      .then(() => this.client.pause(2000));
  }

  async downloadCart(selector) {
    await this.waitForExistAndClick(selector);
    await page.waitFor(2000);
    let exportDate = await this.getCustomDate(0);
    let files = await fs.readdirSync(global.downloadsFolderPath);
    for (let i = 0; i < files.length; i++) {
      if (files[i].includes('cart_' + exportDate)) {
        global.exportCartFileName = await files[i];
      }
    }
  }

  async getShoppingCartNumber(selector, wait = 0) {
    await page.waitFor(wait);
    let result = await page.evaluate((selector) => {
      return document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
    }, selector);
    global.shoppingCartsNumber = await result;
  }

  async readFile(folderPath, fileName, wait = 0) {
    await fs.readFile(folderPath + fileName, {encoding: 'utf-8'}, function (err, data) {
      global.lineFile = data.split("\n");
    });
    await page.waitFor(wait);
    await expect(global.lineFile, "No data").to.be.not.empty
  }

  async checkExportedFileInfo(wait = 0) {
    await page.waitFor(wait);
    for (let i = 1; i < (global.lineFile.length - 1); i++) {
      await expect(global.lineFile[i]).to.be.equal(global.orders[i - 1]);
    }
  }

  async checkEnable(selector) {
    await page.waitForSelector(selector, {visible: true});
    const is_disabled = await page.$(selector + '[disabled]') !== null;
    expect(is_disabled).to.be.true;
  }

  async getCreditSlipDocumentName(selector) {
    await page.$eval(selector, el => el.innerText).then((text) => {
      global.creditSlip = text.trim();
    });
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
