const {getClient} = require('../common.webdriverio.js');
const {selector} = require('../globals.webdriverio.js');
var path = require('path');
var http = require('http');
var fs = require('fs');

var PDF = require('pdfkit');            //including the pdfkit module
var request = require('request');

var pdfUtil = require('pdf-to-text');

class CommonClient {
  constructor() {
    this.client = getClient();
  }

  signInBO(selector) {
    return this.client.signInBO(selector);
  }

  signOutBO() {
    return this.client.signOutBO();
  }

  signInFO(selector) {
    return this.client.signInFO(selector);
  }

  signOutFO(selector) {
    return this.client.signOutFO(selector);
  }

  goToSubtabMenuPage(menuSelector, selector) {
    return this.client
      .waitForExist(menuSelector, 90000)
      .moveToObject(menuSelector)
      .waitForExistAndClick(selector)
  }

  checkOnBoardingModal() {
    return this.client
      .isVisible('.onboarding-welcome')
      .then((text) => global.onboarding = text)
  }

  OnBoarding(selector) {
    if (global.onboarding == true) {
      return this.client
        .click(selector.popup_close_button)
        .pause(2000)
    } else {
      return this.client
        .pause(1000)
    }
  }

  takeScreenshot() {
    return this.client.saveScreenshot(`test/screenshots/${this.client.desiredCapabilities.browserName}_exception_${global.date_time}.png`);
  }

  successPanel(index) {
    return this.client
      .waitForExist(selector.CatalogPage.success_panel)
      .then(() => this.client.getText(selector.CatalogPage.success_panel))
      .then((text) => expect(text.substr(2)).to.be.equal(index));
  }

  languageChange(language) {
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

  open() {
    return this.client.init().windowHandleSize({width: 1280, height: 1024});
  }

  close() {
    return this.client.end();
  }

  waitForExistAndClick(selector, timeout = 90000) {
    return this.client.waitForExistAndClick(selector, timeout);
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

  waitAndSelectByValue(selector, value, timeout = 90000) {
    return this.client.waitAndSelectByValue(selector, value, timeout);
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

  checkTextValue(selector, textToCheckWith, parameter = 'equal') {
    switch (parameter) {
      case "contain":
        return this.client
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text).to.contain(textToCheckWith));
        break;
      case "equal":
        return this.client
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text).to.equal(textToCheckWith));
        break;
      case "notequal":
        return this.client
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text).to.not.equal(textToCheckWith));
        break;
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
    function verify_text_existence(positionToVerify) {
      if (positionToVerify == -1) {
        throw(err)
      }
    }
    pdfUtil.pdfToText(folderPath + fileName + '.pdf', function (err, data) {
      if (err) throw(err);
      verify_text_existence(data.indexOf(text))
    });
    return this.client
      .pause(5000)
  }

}

module.exports = CommonClient;
