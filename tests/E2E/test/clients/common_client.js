const {getClient} = require('../common.webdriverio.js');
const {selector} = require('../globals.webdriverio.js');
var path = require('path');
var fs = require('fs');
var pdfUtil = require('pdf-to-text');

global.tab = [];

class CommonClient {
  constructor() {
    this.client = getClient();
  }

  signInBO(selector, link) {
    return this.client.signInBO(selector, link);
  }

  signOutBO() {
    return this.client.signOutBO();
  }

  signInFO(selector, link) {
    return this.client.signInFO(selector, link);
  }

  signOutFO(selector) {
    return this.client.signOutFO(selector);
  }

  localhost(link) {
    return this.client.localhost(link);
  }

  linkAccess(link) {
    return this.client.linkAccess(link);
  }

  waitForVisibleElement(selector, timeout) {
    return this.client.waitForVisibleElement(selector, timeout);
  }

  waitForExist(selector, timeout = 90000) {
    return this.client
      .waitForExist(selector, timeout)
  }


  goToSubtabMenuPage(menuSelector, selector) {
    return this.client
      .waitForExist(menuSelector, 90000)
      .moveToObject(menuSelector)
      .waitForVisibleAndClick(selector)
  }

  closeBoarding(selector) {
    if (global.isVisible) {
      return this.client
        .click(selector)
        .pause(2000)
    } else {
      return this.client.pause(1000)
    }
  }

  isVisible(selector) {
    return this.client
      .isVisible(selector)
      .then((isVisible) => {
        global.isVisible = isVisible;
      });
  }

  takeScreenshot() {
    return this.client.saveScreenshot(`test/screenshots/${this.client.desiredCapabilities.browserName}_exception_${new Date().getTime()}.png`);
  }

  changeLanguage(language) {
    if (language === "francais") {
      return this.client
        .waitForExistAndClick(selector.languageFO.language_selector)
        .waitForVisibleAndClick(selector.languageFO.language_FR)
    } else {
      return this.client
        .waitForExistAndClick(selector.languageFO.language_selector)
        .waitForVisibleAndClick(selector.languageFO.language_EN)
    }
  }

  selectLanguage(selector, option, language, id) {
    return this.client
      .waitForExistAndClick(selector)
      .waitForExistAndClick(option.replace('%LANG', language).replace('%ID', id))
  }

  open() {
    if (headless !== 'undefined' && headless) {
      return this.client.init().windowHandleSize({width: 1280, height: 899});
    } else {
      return this.client.init().windowHandleSize({width: 1280, height: 1024});
    }
  }

  close() {
    return this.client.end();
  }

  waitForExistAndClick(selector, pause = 0, timeout = 90000) {
    return this.client
      .pause(pause)
      .waitForExistAndClick(selector, timeout);
  }

  waitAndSetValue(selector, value, timeout = 90000) {
    return this.client.waitAndSetValue(selector, value, timeout);
  }

  scrollTo(selector, margin) {
    return this.client.scrollTo(selector, margin);
  }

  scrollWaitForExistAndClick(selector, margin, timeout = 90000) {
    return this.client.scrollWaitForExistAndClick(selector, margin, timeout)
  }

  waitForVisibleAndClick(selector, timeout = 90000) {
    return this.client.waitForVisibleAndClick(selector, timeout);
  }

  moveToObject(selector) {
    return this.client.moveToObject(selector);
  }

  waitAndSelectByValue(selector, value, timeout = 90000) {
    return this.client.waitAndSelectByValue(selector, value, timeout);
  }

  waitAndSelectByVisibleText(selector, value, timeout = 90000) {
    return this.client.waitAndSelectByVisibleText(selector, value, timeout);
  }

  addFile(selector, picture, value = 150) {
    return this.client
      .scrollTo(selector, value)
      .waitForExist(selector, 90000)
      .chooseFile(selector, path.join(__dirname, '..', 'datas', picture))
  }

  getTextInVar(selector, globalVar, split = false) {
    if (split) {
      return this.client
        .waitForExist(selector, 9000)
        .then(() => this.client.getText(selector))
        .then((variable) => global.tab[globalVar] = variable.split(': ')[1])
    } else {
      return this.client
        .waitForExist(selector, 9000)
        .then(() => this.client.getText(selector))
        .then((variable) => global.tab[globalVar] = variable)
    }
  }

  checkTextValue(selector, textToCheckWith, parameter = 'equal', pause = 0) {
    switch (parameter) {
      case "contain":
        return this.client
          .pause(pause)
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text).to.contain(textToCheckWith));
        break;
      case "equal":
        return this.client
          .pause(pause)
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text).to.equal(textToCheckWith));
        break;
      case "notequal":
        return this.client
          .pause(pause)
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text).to.not.equal(textToCheckWith));
        break;
    }
  }

  checkAttributeValue(selector, attribute, value, parameter = 'equal', pause = 0) {
    switch (parameter) {
      case "contain":
        return this.client
          .pause(pause)
          .waitForExist(selector, 90000)
          .then(() => this.client.getAttribute(selector, attribute))
          .then((text) => expect(text).to.be.contain(value));
      case "equal":
        return this.client
          .pause(pause)
          .waitForExist(selector, 90000)
          .then(() => this.client.getAttribute(selector, attribute))
          .then((text) => expect(text).to.be.equal(value));
    }
  }

  uploadPicture(picture, selector, className = "dz-hidden-input") {
    return this.client
      .execute(function (className) {
        document.getElementsByClassName(className).style = '';
      })
      .chooseFile(selector, path.join(__dirname, '..', 'datas', picture))
  }

  /**
   * This function allows to search a data by value
   * @param search_input
   * @param search_button
   * @param value
   * @returns {*}
   */
  searchByValue(search_input, search_button, value) {
    return this.client
      .waitAndSetValue(search_input, value)
      .waitForExistAndClick(search_button)
  }

  /**
   * This function allows to download a pdf document and check the existence of string in it
   * @param folderPath
   * @param fileName
   * @param text
   * @returns {*}
   */
  checkDocument(folderPath, fileName, text) {
    pdfUtil.pdfToText(folderPath + fileName + '.pdf', function (err, data) {
      global.indexText=data.indexOf(text)
    });

    return this.client
      .pause(2000)
      .then(()=> expect(global.indexText,text + "does not exist in the PDF document").to.not.equal(-1))
  }

  waitForVisible(selector, timeout = 90000) {
    return this.client
      .waitForVisible(selector, timeout)
  }

  accessToBO(selector) {
    return this.client.accessToBO(selector);
  }

  accessToFO(selector) {
    return this.client.accessToFO(selector);
  }

  waitAndSelectByAttribute(selector, attribute, value, pause = 0, timeout = 90000) {
    return this.client.waitAndSelectByAttribute(selector, attribute, value, pause, timeout);
  }

  refresh(selector) {
    return this.client
      .refresh();
  }

  switchWindow(id) {
    return this.client.switchWindow(id);
  }

  isExisting(selector, pause = 0) {
    return this.client
      .pause(pause)
      .scrollTo(selector)
      .isExisting(selector)
      .then((isExisting) => expect(isExisting).to.be.true)
  }

  clickOnResumeButton(selector) {
    if (!global.isVisible) {
      return this.client
        .click(selector)
    } else {
      return this.client.pause(1000)
    }
  }

  pause(timeout) {
    return this.client.pause(timeout);
  }

  keys(button) {
    return this.client.keys(button)
  }

}

module.exports = CommonClient;
