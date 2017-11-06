const {getClient} = require('../common.webdriverio.js');
const {selector} = require('../globals.webdriverio.js');

class PrestashopClient {
  constructor() {
    this.client = getClient();
  }

  signinBO() {
    return this.client.signinBO();
  }

  signoutBO() {
    return this.client.signoutBO();
  }

  signinFO() {
    return this.client.signinFO();
  }

  signoutFO() {
    return this.client.signoutFO();
  }

  takeScreenshot() {
    return this.client.saveScreenshot(`test/screenshots/${this.client.desiredCapabilities.browserName}_exception_${global.date_time}.png`);
  }

  successPanel(index) {
    return this.client
      .waitForExist(selector.BO.CatalogPage.success_panel)
      .then(() => this.client.getText(selector.BO.CatalogPage.success_panel))
      .then((text) => expect(text.substr(2)).to.be.equal(index));
  }


  open() {
    return this.client.init().windowHandleSize({width: 1280, height: 1024});
  }

  close() {
    return this.client.end();
  }
}

module.exports = PrestashopClient;
