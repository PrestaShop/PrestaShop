const {getClient} = require('../common.webdriverio.js');
const {languageFO} = require('../selectors/FO/index');
let path = require('path');
let fs = require('fs');
let pdfUtil = require('pdf-to-text');
const exec = require('child_process').exec;

global.tab = [];
global.isOpen = false;
global.param = [];

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
        }
      })
      .then(() => this.client.pause(4000));
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

  isVisibleWithinViewport(selector) {
    return this.client
      .isVisibleWithinViewport(selector);
  }

  takeScreenshot() {
    return this.client.saveScreenshot(`test/screenshots/${this.client.desiredCapabilities.browserName}_exception_${new Date().getTime()}.png`);
  }

  changeLanguage(language = 'en') {
    return this.client
      .waitForExistAndClick(languageFO.language_selector, 2000)
      .pause(2000)
      .isVisible(languageFO.language_option.replace('%LANG', language))
      .then((isVisible) => {
        expect(isVisible, "This language is not existing").to.be.true;
        if (isVisible) {
          this.client.waitForVisibleAndClick(languageFO.language_option.replace('%LANG', language));
        }
      })
      .then(() => this.client.pause(3000));
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

  closeWindow(id) {
    return this.client.closeWindow(id);
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

  waitForVisibleAndClick(selector, pause = 0, timeout = 90000) {
    return this.client
      .pause(pause)
      .waitForVisibleAndClick(selector, timeout);
  }

  scrollWaitForVisibleAndClick(selector, pause = 0, timeout = 90000) {
    return this.client
      .pause(pause)
      .scrollTo(selector)
      .waitForVisibleAndClick(selector, timeout);
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
      case "notequal":
        return this.client
          .pause(pause)
          .waitForExist(selector, 90000)
          .then(() => this.client.getAttribute(selector, attribute))
          .then((text) => expect(text).to.not.equal(value));
    }
  }

  checkCssPropertyValue(selector, property, value, parameter = 'equal', pause = 0) {
    switch (parameter) {
      case "contain":
        return this.client
          .pause(pause)
          .waitForExist(selector, 90000)
          .then(() => this.client.getCssProperty(selector, property))
          .then((property) => expect(property.value).to.be.contain(value));
      case "equal":
        return this.client
          .pause(pause)
          .waitForExist(selector, 90000)
          .then(() => this.client.getCssProperty(selector, property))
          .then((property) => expect(property.value).to.be.equal(value));
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
      .pause(2000)
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
  async checkDocument(folderPath, fileName, text) {
   await pdfUtil.pdfToText(folderPath + fileName + '.pdf', function (err, data) {
      global.data = global.data + data;
      global.indexText = global.data.indexOf(text);
    });

    return this.client
      .pause(2000)
      .then(() => expect(global.indexText, text + "does not exist in the PDF document").to.not.equal(-1));
  }

  /**
   * This function allows to check the existence of file after downloading
   * @param folderPath
   * @param fileName
   * @returns {*}
   */
  checkFile(folderPath, fileName, pause = 0) {
    fs.stat(folderPath + fileName, function (err, stats) {
      err === null && stats.isFile() ? global.existingFile = true : global.existingFile = false;
    });

    return this.client
      .pause(pause)
      .then(() => expect(global.existingFile).to.be.true)
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

  switchWindow(id) {
    return this.client.switchWindow(id);
  }

  switchTab(id) {
    return this.client
      .then(() => this.client.getTabIds())
      .then((ids) => this.client.switchTab(ids[id]));
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

  isNotSelected(selector, pause = 0) {
    return this.client
      .pause(pause)
      .scrollTo(selector)
      .isSelected(selector)
      .then((isExisting) => expect(isExisting).to.be.false);
  }

  isNotExisting(selector, pause = 0) {
    return this.client
      .pause(pause)
      .isExisting(selector)
      .then((isExisting) => expect(isExisting).to.be.false);
  }

  clickOnResumeButton(selector) {
    if (!global.isVisible) {
      return this.client
        .click(selector);
    } else {
      return this.client.pause(1000);
    }
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

  alertDismiss() {
    return this.client.alertDismiss();
  }

  getText(selector) {
    return this.client.getText(selector);
  }

  alertText() {
    return this.client.alertText();
  }

  showElement(className, order) {
    return this.client
      .execute(function (className, order) {
        document.querySelectorAll(className)[order].style.display = 'inherit';
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

  checkTextEditor(selector, content, pause = 0) {
    return this.client
      .pause(pause)
      .scrollTo(selector)
      .waitForExistAndClick(selector)
      .execute(function () {
        return (tinyMCE.activeEditor.getContent());
      })
      .then((values) => expect(values.value.indexOf(content) >= 0).to.equal(true));
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

  setAttributeById(selector) {
    return this.client
      .execute(function (selector) {
        document.getElementById(selector).style.display = 'none';
      }, selector);
  }

  stringifyNumber(number) {
    let special = ['zeroth', 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'twelfth', 'thirteenth', 'fourteenth', 'fifteenth', 'sixteenth', 'seventeenth', 'eighteenth', 'nineteenth'];
    let deca = ['twent', 'thirt', 'fort', 'fift', 'sixt', 'sevent', 'eight', 'ninet'];
    if (number < 20) return special[number];
    if (number % 10 === 0) return deca[Math.floor(number / 10) - 2] + 'ieth';
    return deca[Math.floor(number / 10) - 2] + 'y-' + special[number % 10];
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

  refresh() {
    return this.client
      .refresh();
  }

  deleteCookie() {
    return this.client
      .deleteCookie()
      .refresh();
  }

  middleClick(selector, globalVisibility = true, pause = 2000) {
    if (globalVisibility) {
      return this.client
        .moveToObject(selector)
        .pause(pause)
        .middleClick(selector);
    } else {
      return this.client.pause(1000);
    }
  }

  getParamFromURL(param, pause = 0) {
    return this.client
      .pause(pause)
      .url()
      .then((res) => {
        let current_url = res.value;
        expect(current_url).to.contain(param);
        global.param[param] = current_url.split(param + '=')[1].split("&")[0];
      });
  }

  dragAndDrop(sourceElement, destinationElement) {
    return this.client
      .pause(2000)
      .moveToObject(sourceElement)
      .buttonDown()
      .moveToObject(destinationElement)
      .buttonUp()
      .pause(2000);
  }

  selectByVisibleText(selector, text, timeout = 90000) {
    return this.client
      .waitForExist(selector, timeout)
      .selectByVisibleText(selector, text)
  }

  middleClickWhenVisible(selector) {
    if (global.isVisible) {
      return this.client
        .middleClick(selector)
    }
  }

  checkList(selector) {
    this.client
      .element(selector)
      .then(function (elements) {
        expect(elements).to.have.lengthOf.above(0);
      })
  }

  /**
   * These functions are used to sort table then check the sorted table
   * elementsTable, elementsSortedTable are two global variables that must be initialized in the sort table function
   * "normalize('NFKD').replace(/[\u0300-\u036F]/g, '')" is used to replace special characters example ô to o
   * * "normalize('NFKD').replace(/[\u0300-\u036F]/g, '')" is used to replace special characters example € to o
   */
  getTableField(element_list, i, sorted = false, priceWithCurrency = false) {
    return this.client
      .getText(element_list.replace("%ID", i + 1)).then(function (name) {
        if (sorted) {
          if (priceWithCurrency === true) {
            elementsSortedTable[i] = name.normalize('NFKD').replace(/[^\x00-\x7F]/g, '').toLowerCase();
          } else {
            elementsSortedTable[i] = name.normalize('NFKD').replace(/[\u0300-\u036F]/g, '').toLowerCase();
          }
        }
        else {
          if (priceWithCurrency === true) {
            elementsTable[i] = name.normalize('NFKD').replace(/[^\x00-\x7F]/g, '').toLowerCase();
          } else {
            elementsTable[i] = name.normalize('NFKD').replace(/[\u0300-\u036F]/g, '').toLowerCase();
          }
        }
      });
  }

  /**
   * This function checks the sort of a table
   * @param isNumber= true if we sort by a number, isNumber= false if we sort by a string
   * @param sortWay equal to 'ASC' or 'DESC'
   */
  async checkSortTable(isNumber = false, sortWay = 'ASC') {
    return await this.client
      .pause(2000)
      .then(async () => {
        if (isNumber) {
          if (sortWay === 'ASC') {
            await expect(elementsTable.sort(function (a, b) {
              return a - b;
            })).to.deep.equal(elementsSortedTable);
          } else {
            await expect(elementsTable.sort(function (a, b) {
              return a - b
            }).reverse()).to.deep.equal(elementsSortedTable);
          }
        } else {
          if (sortWay === 'ASC') {
            await expect(elementsTable.sort()).to.deep.equal(elementsSortedTable);
          } else {
            await expect(elementsTable.sort().reverse()).to.deep.equal(elementsSortedTable);
          }
        }
      });
  }

  displayHiddenBlock(selector) {
    return this.client
      .execute(function (selector) {
        document.getElementsByClassName(selector).style = '';
      })
  }

  changeOrderState(selector, state) {
    return this.client
      .waitForExist(selector.order_state_select, 90000)
      .execute(function () {
        document.querySelector('#id_order_state').style = "";
      })
      .selectByVisibleText(selector.order_state_select, state)
      .waitForExistAndClick(selector.update_status_button)
  }

  getDocumentName(selector) {
    return this.client
      .then(() => this.client.getText(selector))
      .then((name) => {
        global.invoiceFileName = name.replace('#', '')
      });
  }

  deleteFile(folderPath, fileName, extension = "", pause = 0) {
    fs.unlinkSync(folderPath + fileName + extension);
    return this.client
      .pause(pause)
  }
}

module.exports = CommonClient;
