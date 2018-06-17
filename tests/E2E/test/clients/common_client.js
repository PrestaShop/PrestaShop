const {getClient} = require('../common.webdriverio.js');
const {selector} = require('../globals.webdriverio.js');
let path = require('path');
let fs = require('fs');
let pdfUtil = require('pdf-to-text');

global.tab = [];
global.isOpen = false;

class CommonClient {
  constructor() {
    this.client = getClient();
  }

  signInBO(selector, link, login, password) {
    return this.client.signInBO(selector, link, login, password);
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
      .waitForExist(selector, timeout);
  }

  goToSubtabMenuPage(menuSelector, selector) {
    return this.client
      .isOpen(menuSelector)
      .then(() => {
        if (global.isOpen) {
          this.client
            .execute(function (selector) {
              let element = document.querySelector(selector);
              element.scrollIntoView();
            }, selector)
            .waitForVisibleAndClick(selector, 2000);
        } else {
          this.client
            .waitForExistAndClick(menuSelector, 2000)
            .pause(2000)
            .execute(function (selector) {
              let element = document.querySelector(selector);
              element.scrollIntoView();
            }, selector)
            .waitForVisibleAndClick(selector);
        }})
      .then(()=> this.client.pause(4000));
  }

  closeBoarding(selector) {
    if (global.isVisible) {
      return this.client
        .click(selector)
        .pause(2000);
    } else {
      return this.client.pause(1000);
    }
  }

  isVisible(selector, pause = 0) {
    return this.client
      .pause(pause)
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

  waitAndSetValue(selector, value, pause = 0, timeout = 90000) {
    return this.client
      .pause(pause)
      .waitAndSetValue(selector, value, timeout);
  }

  scrollTo(selector, margin) {
    return this.client.scrollTo(selector, margin);
  }

  scrollWaitForExistAndClick(selector, margin, pause = 0, timeout = 90000) {
    return this.client
      .pause(pause)
      .scrollWaitForExistAndClick(selector, margin, timeout);
  }

  waitForVisibleAndClick(selector, timeout = 90000) {
    return this.client.waitForVisibleAndClick(selector, timeout);
  }

  moveToObject(selector, pause = 0) {
    return this.client
      .pause(pause)
      .moveToObject(selector);
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
      .chooseFile(selector, path.join(__dirname, '..', 'datas', picture));
  }

  getTextInVar(selector, globalVar, split = false, timeout = 90000) {
    if (split) {
      return this.client
        .waitForExist(selector, timeout)
        .then(() => this.client.getText(selector))
        .then((variable) => global.tab[globalVar] = variable.split(': ')[1]);
    } else {
      return this.client
        .waitForExist(selector, timeout)
        .then(() => this.client.getText(selector))
        .then((variable) => global.tab[globalVar] = variable);
    }
  }

  getAttributeInVar(selector, attribute, globalVar, timeout = 90000) {
    return this.client
      .waitForExist(selector, timeout)
      .then(() => this.client.getAttribute(selector, attribute))
      .then((variable) => global.tab[globalVar] = variable);
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
      case "deepequal":
        return this.client
          .pause(pause)
          .waitForExist(selector, 9000)
          .then(() => this.client.getText(selector))
          .then((text) => expect(text).to.deep.equal(textToCheckWith));
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
      .chooseFile(selector, path.join(__dirname, '..', 'datas', picture));
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
      .waitForExistAndClick(search_button);
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
      global.indexText = data.indexOf(text)
    });

    return this.client
      .pause(2000)
      .then(() => expect(global.indexText, text + "does not exist in the PDF document").to.not.equal(-1));
  }

  waitForVisible(selector, timeout = 90000) {
    return this.client
      .waitForVisible(selector, timeout);
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
      .isExisting(selector)
      .then((isExisting) => expect(isExisting).to.be.true);
  }

  isSelected(selector, pause = 0) {
    return this.client
      .pause(pause)
      .scrollTo(selector)
      .isSelected(selector)
      .then((isExisting) => expect(isExisting).to.be.true);
  }

  isNotExisting(selector, pause = 0) {
    return this.client
      .pause(pause)
      .isExisting(selector)
      .then((isExisting) => expect(isExisting).to.be.false);
  }

  pause(timeout) {
    return this.client.pause(timeout);
  }

  keys(button) {
    return this.client.keys(button);
  }

  alertAccept() {
    return this.client.alertAccept();
  }

  showElement(className, order) {
    return this.client
      .execute(function (className, order) {
        document.querySelectorAll(className)[order].style.display = 'block';
      }, className, order);
  }

  checkIsNotVisible(selector) {
    return this.client
      .pause(2000)
      .isVisible(selector)
      .then((isVisible) => expect(isVisible).to.be.false);
  }

  checkParamFromURL(param, value, pause = 0) {
    return this.client
      .pause(pause)
      .url()
      .then((res) => {
        let current_url = res.value;
        expect(current_url).to.contain(param);
        global.param = current_url.split(param + '=')[1].split("&")[0];
        expect(global.param).to.equal(value);
      });
  }

  /**
   * This function searches the data in the table in case a filter input exists
   * @param selector
   * @param data
   * @returns {*}
   */
  search(selector, data) {
    if (global.isVisible) {
      return this.client
        .waitAndSetValue(selector, data)
        .keys('Enter');
    }
  }

  /**
   * This function checks the search result
   * @param selector
   * @param data
   * @param pos
   * @returns {*}
   */
  checkExistence(selector, data, pos) {
    if (global.isVisible) {
      return this.client.getText(selector.replace('%ID', pos)).then(function (text) {
        expect(text).to.be.equal(data);
      });
    } else {
      return this.client.getText(selector.replace('%ID', pos - 1)).then(function (text) {
        expect(text).to.be.equal(data);
      });
    }
  }

  /**
   * This function checks the search result
   * @param selector editor body selector
   * @param content
   * @returns {*}
   */
  setEditorText(selector, content) {
    return this.client
      .pause(1000)
      .click(selector)
      .execute(function (content) {
        return (tinyMCE.activeEditor.setContent(content));
      }, content);
  }

  editObjectData(object, type = '') {
    for (let key in object) {
      if (object.hasOwnProperty(key) && key !== 'type') {
        if (typeof object[key] === 'string') {
          parseInt(object[key]) ? object[key] = (parseInt(object[key]) + 10).toString() : object[key] += 'update';
        } else if (typeof object[key] === 'number') {
          object[key] += 10;
        } else if (typeof object[key] === 'object') {
          this.editObjectData(object[key]);
        }
      }
      if (type !== '') {
        object['type'] = type;
      }
    }
  }

  deleteObjectElement(object, pos) {
    delete object[pos];
  }

}

module.exports = CommonClient;
