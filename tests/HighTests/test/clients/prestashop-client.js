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
    return this.client.saveScreenshot(`screenshots/${this.client.desiredCapabilities.browserName}_exception_${global.date_time}.png`);
  }

  addCategorySuccessPanel(index, erreurmsg) {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.success_panel)
      .getText(selector.BO.CatalogPage.CategorySubmenu.success_panel).then(function (text) {
        text = text.indexOf(index);
        if (text === -1) {
          done(new Error(erreurmsg));
        }
      })
  }

  open() {
    return this.client.init().windowHandleSize({width: 1280, height: 1024});
  }

  close() {
    return this.client.end();
  }
}

module.exports = PrestashopClient;
