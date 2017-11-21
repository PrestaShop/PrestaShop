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

  onBoarding() {
    return this.client
      .waitForVisible(selector.BO.Onboarding.popup_close_button)
      .click(selector.BO.Onboarding.popup_close_button)
      .pause(2000)
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

  languageChange(language) {
    if (language === "francais") {
      return this.client
        .waitForExist(selector.FO.common.language_selector, 90000)
        .click(selector.FO.common.language_selector)
        .waitForVisible(selector.FO.common.language_FR, 90000)
        .click(selector.FO.common.language_FR)
    } else {
      return this.client
        .waitForExist(selector.FO.common.language_selector, 90000)
        .click(selector.FO.common.language_selector)
        .waitForVisible(selector.FO.common.language_EN, 90000)
        .click(selector.FO.common.language_EN)
    }
  }

  open() {
    return this.client.init().windowHandleSize({width: 1280, height: 1024});
  }

  close() {
    return this.client.end();
  }
}

module.exports = PrestashopClient;
