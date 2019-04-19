const {languageFO} = require('../selectors/FO/index');
const exec = require('child_process').exec;
const puppeteer = require('puppeteer');
let path = require('path');
let fs = require('fs');
let pdfUtil = require('pdf-to-text');
const {AccessPageBO} = require('../selectors/BO/access_page');
require('../globals');

let options = {
  timeout: 30000,
  headless: global.headless,
  defaultViewport: {
    width: 0,
    height: 0
  },
  args: [`--window-size=${1280},${1024}`]
};

global.tab = [];
global.isOpen = false;
global.param = [];
global.selectValue = '';

class CommonClient {

  async open() {
    global.browser = await puppeteer.launch(options);
    global.page = await this.getPage(0);
    global.alertAccept = false ;
    page._client.send('Page.setDownloadBehavior', {
      behavior: 'allow',
      downloadPath : global.downloadsFolderPath
    });
    //Set the user agent and the accept language for headless mode => Chrome Headless will closely emulate Chrome
    if (global.headless) {
      const headlessUserAgent = await page.evaluate(() => navigator.userAgent);
      const chromeUserAgent = headlessUserAgent.replace('HeadlessChrome', 'Chrome');
      await page.setUserAgent(chromeUserAgent);
      await page.setExtraHTTPHeaders({
        'accept-language': 'en-US,en;q=0.8'
      });
    } else {
      await page.setViewport({ width: 1280, height: 1024 });
    }
  }

  async getPage(id) {
    const pages = await browser.pages();
    return await pages[id];
  }

  async stopTracing() {
    await page.tracing.stop();
  }

  async close() {
    await browser.close();
  }

  async startTracing(testName = 'test') {
    await page.tracing.start({
      path: 'test/tracing/' + testName + '.json',
      categories: ['devtools.timeline']
    });
  }

  async signInBO(selector, link = global.URL, login = global.adminEmail, password = global.adminPassword) {
    await page.goto(link + '/admin-dev');
    await this.waitAndSetValue(selector.login_input, login);
    await this.waitAndSetValue(selector.password_inputBO, password);
    await this.waitForExistAndClick(selector.login_buttonBO);
    await page.waitFor(selector.menuBO, {timeout: 120000});
  }

  async waitAndSetValue(selector, value, wait = 0, options = {}, isFrame = false) {
    await page.waitFor(wait);
    if (isFrame) {
      await frame.waitFor(selector, options);
      await frame.click(selector);
    } else {
      await page.waitFor(selector, options);
      await page.click(selector);
    }
    await page.keyboard.down('Control');
    await page.keyboard.down('A');
    await page.keyboard.up('A');
    await page.keyboard.up('Control');
    await page.keyboard.press('Backspace');
    if (isFrame) {
      await frame.type(selector, value);
    } else {
      await page.type(selector, value);
    }
  }

  async pause(timeoutOrSelectorOrFunction, options = {}) {
    await page.waitFor(timeoutOrSelectorOrFunction, options);
  }

  async waitForExistAndClick(selector, wait = 0, options = {}, isFrame = false) {
    await page.waitFor(wait);
    if (isFrame) {
      await frame.waitFor(selector);
      await frame.click(selector, options);
    } else {
      await page.waitFor(selector);
      await page.click(selector, options);
    }
  }

  async isVisible(selector, wait = 0) {
    await page.waitFor(wait);
    const exists = await page.$(selector) !== null;
    if (exists) {
      global.isVisible = await page.evaluate((selector) => {
        const e = document.querySelector(selector);
        const style = window.getComputedStyle(e);
        return style && style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
      }, selector);
    } else {
      global.isVisible = exists;
    }
    return global.isVisible;
  }

  async closeBoarding(selector) {
    if (global.isVisible) {
      await page.click(selector);
      await page.waitFor(2000);
    } else {
      await page.waitFor(1000);
    }
  }

  async screenshot(fileName = 'screenshot') {
    await page.screenshot({path: 'test/screenshots/' + fileName + global.dateTime + '.png'});
  }

  async goToSubtabMenuPage(menuSelector, selector) {
    await page.waitFor(selector);
    const selector_link = await page.$eval(selector, ({href}) => href);
    await page.goto(selector_link, {waitUntil: 'networkidle0'});
    /*
    let isOpen = false;
    let result = await page.evaluate((menuSelector) => {
      isOpen = document.querySelector(menuSelector).matches('open');
      return isOpen;
    }, menuSelector);
    if (result === false) {
      await this.waitForExistAndClick(menuSelector);
    }
    await this.waitForExistAndClick(selector, 2000);
    */
  }

  async scrollWaitForVisibleAndClick(selector, wait = 0, timeout = 40000) {
    await page.waitFor(selector);
    await page.evaluate((selector) => {
      document.querySelector(selector).scrollIntoView();
    }, selector);
    await this.waitForVisibleAndClick(selector, wait, timeout)
  }

  async isExisting(selector, wait = 0) {
    await page.waitFor(wait);
    const exists = await page.$(selector) !== null;
    expect(exists).to.be.true;
    if (exists) {
      //If exist, element should be visible too
      await this.isVisible(selector);
      expect(global.isVisible).to.be.true;
      return global.isVisible;
    }
  }

  async waitForVisibleAndClick(selector, wait = 0, timeout = 30000) {
    await page.waitFor(wait);
    await page.waitFor(selector, {visible: true, timeout: timeout});
    await page.click(selector);
  }

  async checkTextValue(selector, textToCheckWith, parameter = 'equal', wait = 0, isFrame = false) {
    let content = {};
    if (isFrame) {
      content = global.frame;
    } else {
      content = global.page;
    }
    switch (parameter) {
      case "equal":
        await content.waitFor(wait);
        await content.waitFor(selector);
        await content.$eval(selector, el => el.innerText).then((text) => {
          if (text.indexOf('\t') != -1) {
            text = text.replace("\t", "");
          }
          expect(text.trim()).to.equal(textToCheckWith)
        });
        break;
      case "contain":
        await content.waitFor(wait);
        await content.waitFor(selector);
        await content.$eval(selector, el => el.innerText).then((text) => expect(text).to.contains(textToCheckWith));
        break;
      case "deepequal":
        await content.waitFor(wait);
        await content.waitFor(selector);
        await content.$eval(selector, el => el.innerText).then((text) => expect(text).to.deep.equal(textToCheckWith));
        break;
      case "notequal":
        await content.waitFor(wait);
        await content.waitFor(selector);
        await content.$eval(selector, el => el.innerText).then((text) => expect(text).to.not.equal(textToCheckWith));
        break;
      case "greaterThan":
        await content.waitFor(wait);
        await content.waitFor(selector);
        await content.$eval(selector, el => el.innerText).then((text) => expect(parseInt(text)).to.be.gt(textToCheckWith));
        break;
    }
  }

  async waitForSymfonyToolbar(AddProductPage, wait = 4000) {
    await page.waitFor(wait);
    await this.isVisible(AddProductPage.symfony_toolbar_block);
    if (global.isVisible) {
      await this.waitForExistAndClick(AddProductPage.symfony_toolbar);
    }
  }

  alertAccept(action = 'accept') {
    if(!global.alertAccept){
      switch (action) {
        case "accept":
          page.on("dialog", (dialog) => {
            dialog.accept();
          });
          break;
        default :
          page.on("dialog", (dialog) => {
            dialog.dismiss();
          });
      }
    }
    global.alertAccept = true ;
  }

  /**
   * close dialog listner
   */
  alertListenerClose(){
    page.removeListener("dialog",(dialog) => {dialog.accept();});
  }

  async scrollTo(selector) {
    await page.waitFor(selector);
    await page.evaluate((selector) => {
      document.querySelector(selector).scrollIntoView();
    }, selector);
  }

  async uploadPicture(fileName, selector) {
    const inputFile = await page.$(selector);
    await inputFile.uploadFile(path.join(__dirname, '..', 'datas', fileName));
  }

  async setEditorText(selector, textDescription) {
    await page.click(selector,{clickCount:3});
    await page.keyboard.type(textDescription);
    //  await page.type(selector, textDescription);
  }

  async checkIsNotVisible(selector) {
    await page.waitFor(2000);
    await this.isVisible(selector);
    await expect(isVisible).to.be.false;
  }

  async isNotExisting(selector, wait = 0) {
    await page.waitFor(wait);
    const exists = await page.$(selector) === null;
    expect(exists).to.be.true;
    return exists;
  }

  async waitForExist(selector, option = {}) {
    await page.waitFor(selector, option);
  }

  async deleteCookie() {
    let cookiesTable = await page.cookies();
    await page.deleteCookie({name: cookiesTable[1].name});
  }

  async refresh() {
    await page.reload();
  }

  async localhost(link, installDirectory) {
    await page.goto(link + '/' + installDirectory);
  }

  async signInFO(selector, link = global.URL) {
    await page.goto(link);
    await page._client.send('Emulation.clearDeviceMetricsOverride');
    await this.waitForExistAndClick(selector.sign_in_button, 3000);
    await this.waitAndSetValue(selector.login_input, 'pub@prestashop.com');
    await this.waitAndSetValue(selector.password_inputFO, '123456789');
    await this.waitForExistAndClick(selector.login_button);
    await this.waitForExistAndClick(selector.logo_home_page);
  }

  async waitForVisible(selector, options = {visible: true}) {
    await page.waitFor(selector, options);
  }

  async waitAndSelectByValue(selector, value, wait = 0, isFrame = false) {
    await page.waitFor(wait);
    if (isFrame) {
      await frame.waitFor(selector);
      await frame.select(selector, value);
    } else {
      await page.waitFor(selector);
      await page.select(selector, value);
    }
  }

  async signOutBO() {
    await this.pause(2000);
    await this.deleteCookie();
  }

  async deleteCookie() {
    let cookiesTable = await page.cookies();
    await page.deleteCookie({name: cookiesTable[1].name});
    await this.refresh();
  }

  async refresh() {
    await page.reload();
  }

  async changeLanguage(language = 'en') {
    await this.waitForExistAndClick(languageFO.language_selector);
    await this.pause(1000);
    await this.waitForVisibleAndClick(languageFO.language_option.replace('%LANG', language));
  }

  async searchByValue(nameSelector, buttonSelector, value) {
    await page.waitForSelector(nameSelector);
    await this.waitAndSetValue(nameSelector, value, 2000);
    await this.waitForExistAndClick(buttonSelector);
  }

  async checkAttributeValue(selector, attribute, textToCheckWith, parameter = 'equal', wait = 0) {
    await page.waitFor(wait);
    await page.waitFor(selector);

    let value = await page.evaluate((selector, attribute) => {
      let elem = document.querySelector(selector);
      return elem.getAttribute(attribute);
    }, selector, attribute);
    switch (parameter) {
      case 'contain': {
        expect(value).to.be.contain(textToCheckWith);
        break;
      }
      case 'equal': {
        expect(value).to.be.equal(textToCheckWith);
        break;
      }
      case 'notequal': {
        expect(value).to.not.equal(textToCheckWith);
        break;
      }
    }
  }

  async signOutFO(selector) {
    await this.waitForExistAndClick(selector.sign_out_button);
    await this.waitForExist(selector.sign_in_button, 90000);
    await this.deleteCookie();
  }

  async accessToFO(selector) {
    await page.goto(global.URL);
    await this.waitForExistAndClick(selector.logo_home_page);
  }

  async scrollWaitForExistAndClick(selector, wait = 0, timeout = 90000) {
    await this.scrollTo(selector);
    await this.waitForExistAndClick(selector, wait, {timeout: timeout})
  }

  async getTextInVar(selector, globalVar, split = false, timeout = 90000) {
    await this.waitForExist(selector, timeout);
    if (split) {
      await page.$eval(selector, el => el.innerText).then((text) => {
        global.tab[globalVar] = text.split(': ')[1];
      });
    } else {
      await page.$eval(selector, el => el.innerText).then((text) => {
        global.tab[globalVar] = text;
      });
    }
  }

  async moveToObject(selector, wait = 0) {
    await page.waitFor(wait);
    await page.hover(selector);
  }

  async getAttributeInVar(selector, attribute, globalVar, timeout = 90000) {
    await this.waitForExist(selector, timeout);
    let variable = await page.$eval(selector, (el, attribute) => {
      if (el.getAttribute(attribute) !== '') {
        return el.getAttribute(attribute);
      } else {
        return el[attribute];
      }
    }, attribute);
    global.tab[globalVar] = await variable;
  }

  /**
   * This function searches the data in the table in case a filter input exists
   * @param selector
   * @param data
   * @returns {*}
   */
  async search(selector, data) {
    await this.waitAndSetValue(selector, data);
    await page.keyboard.press('Enter');
  }

  async closePsAddonsAlert() {
    await this.isVisible(AccessPageBO.psAddons_alert_close_button);
    if (global.isVisible) {
      await this.waitForExistAndClick(AccessPageBO.psAddons_alert_close_button);
    }
  }

  async switchWindow(id, wait = 0) {
    await page.waitFor(5000, {waituntil: 'networkidle2'});
    await page.waitFor(wait);
    global.page = await this.getPage(id);
    await page.bringToFront();
    await page._client.send('Emulation.clearDeviceMetricsOverride');
  }

  async goToFrame(frameName) {
    page.waitFor(4000);
    let frame = await page.frames().find(frame => frame.name().includes(frameName));
    global.frame = frame;
  }

  /**
   * Press on Keyboard , don't work with multiple keyss
   */
  async keys(button) {
    await page.keyboard.press(button);
  }
    /**
     * Press on Keyboard , multiple Keys
     * @param buttons, array of buttons to click on
     */
    async multipleKeys(buttons) {
      for(let i = 0 ; i< buttons.length ; i++)  await page.keyboard.down(buttons[i]);
      for(let j = 0 ; j< buttons.length ; j++)  await page.keyboard.up(buttons[j]);
    }


  async waitAndSelectByVisibleText(selector, value, wait = 0, isFrame = false) {
    let content = {};
    let textValue = '';
    if (isFrame) {
      content = global.frame;
    } else {
      content = global.page;
    }
    await content.waitFor(wait);
    await content.waitFor(selector);
    let result = await page.evaluate((selector, value) => {
      let options = document.querySelector(selector).options;
      for (let i = 0; i < options.length; i++) {
        if (options[i].innerText.includes(value)) {
          textValue = options[i].value;
          return textValue;
        }
      }
    }, selector, value);
    global.selectValue = await result;
    await content.select(selector, result);
  }

  async checkTextElementValue(selector, textToCheckWith, parameter = 'equal', wait = 0) {
    switch (parameter) {
      case "equal":
        await page.waitFor(wait);
        await page.waitFor(selector);
        await page.$eval(selector, el => el.value).then((text) => expect(text).to.equal(textToCheckWith));
        break;
      case "contain":
        await page.waitFor(wait);
        await page.waitFor(selector);
        await page.$eval(selector, el => el.value).then((text) => expect(text).to.contain(textToCheckWith));
        break;
    }
  }

  checkList(selector) {
    page.$$(selector)
      .then(function (elements) {
        expect(elements).to.have.lengthOf.above(0);
      }, selector)
  }

  async checkExistence(selector, data, pos) {
    if (global.isVisible) {
      await page.waitFor(selector.replace('%ID', pos));
      await page.$eval(selector.replace('%ID', pos), el => el.innerText).then((text) => expect(text.trim).to.equal(data.trim));
    }
    else {
      await page.waitFor(selector.replace('%ID', pos - 1));
      await page.$eval(selector.replace('%ID', pos - 1), el => el.innerText).then((text) => expect(text.trim).to.equal(data.trim));
    }
  }

  async closeWindow() {
    await page.close();
  }

  async changeOrderState(selector, state) {
    await page.waitFor(selector.order_state_select);
    await page.evaluate(() => {
      let element = document.querySelector('#id_order_state');
      element.style = "";
    });
    await this.waitAndSelectByVisibleText(selector.order_state_select, state);
    await this.waitForExistAndClick(selector.update_status_button);
  }

  async checkFile(folderPath, fileName, wait = 0) {
    await fs.stat(folderPath + fileName, function (err, stats) {
      err === null && stats.isFile() ? global.existingFile = true : global.existingFile = false;
    });
    await page.waitFor(wait);
    await expect(global.existingFile).to.be.true;
  }

  async getCustomDate(numberOfDay) {
    let today = await new Date();
    await today.setDate(today.getDate() + numberOfDay);
    let dd = await today.getDate();
    let mm = await today.getMonth() + 1; //January is 0!
    let yyyy = await today.getFullYear();
    if (dd < 10) {
      dd = await '0' + dd;
    }
    if (mm < 10) {
      mm = await '0' + mm;
    }
    return await yyyy + '-' + mm + '-' + dd;
  }

  async setDownloadBehavior(removeBlankTarget = false, selector = '') {
    if (removeBlankTarget) {
      await page.waitFor(2000);
      await page.evaluate((selector) => {
        let element = document.querySelector(selector);
        element.setAttribute('target', '');
      }, selector);
    }
    await page.waitFor(4000);
    await page._client.send('Page.setDownloadBehavior', {
      behavior: 'allow',
      downloadPath: global.downloadsFolderPath
    });
  }

  async deleteFile(folderPath, fileName, extension = "", wait = 0) {
    fs.unlinkSync(folderPath + fileName + extension);
    await page.waitFor(wait);
  }

  async checkDocument(folderPath, fileName, text) {
    await pdfUtil.pdfToText(folderPath + fileName + '.pdf', function (err, data) {
      global.indexText = data.indexOf(text);
      global.data = global.data + data;
    });
    await page.waitFor(2000);
    expect(global.indexText, text + "does not exist in the PDF document").to.not.equal(-1);
  }

  async getDocumentName(selector) {
    await page.$eval(selector, el => el.innerText).then((text) => {
      global.invoiceFileName = text.replace('#', '');
    });
  }

  linkAccess(link) {
    return page.goto(link);
  }

  /**
   * Go to link, for only '<a/>'
   * @param selector, link to go to
   */
  async goToLink(selector) {
    const selector_link = await page.$eval(selector, ({href}) => href);
    await page.goto(selector_link, {waitUntil: 'networkidle0'});

  }

  /**
   * To type on texte area
   * @param selector, textarea to fill
   * @param text_value, value to write
   */
  async fillTextArea(selector, text_value) {
    await page.click(selector);
    await page.keyboard.type(text_value);
  }

  /**
   * To type on input type text
   * @param selector, input to set
   * @param text_value, value to write
   */
  async fillInputText(selector, text_value) {
    await page.focus(selector);
    await page.$eval(selector, el => el.setSelectionRange(0, el.value.length));
    await page.keyboard.type(text_value);
  }

  /**
   * To type on input type number
   * @param selector, input to set
   * @param number_value, value to write
   */
  async fillInputNumber(selector, number_value) {
    await page.focus(selector);
    //await page.$eval(selector, el => el.click({clickCount: 3}));
    await page.evaluate(() => document.execCommand('selectall', false, null));
    await page.keyboard.type(number_value);
  }

  /**
   * Check input.Value content
   * @param selector, input to check
   * @param textToCheckWith, String of the expected text
   * @param parameter, String of equal / contains / notequal
   * @return true if expected is true, else false
   */
  async checkInputValue(selector, textToCheckWith, parameter = 'equal') {
    await page.waitFor(selector);

    let value = await page.evaluate((selector) => {
      let elem = document.querySelector(selector);
      return elem.value;
    }, selector);
    switch (parameter) {
      case 'contain': {
        expect(value).to.be.contain(textToCheckWith);
        break;
      }
      case 'equal': {
        expect(value).to.be.equal(textToCheckWith);
        break;
      }
      case 'notequal': {
        expect(value).to.not.equal(textToCheckWith);
        break;
      }
    }
  }

  /**
   * Get Element By text and click
   * @param selector, selector to click on
   * @text text to check for element
   */
  async waitElementByTextAndClick(selector, text) {
    const elements = await page.evaluate((selector) => { return document.querySelectorAll(selector);} , selector);
    var found = false;
    for (var element in elements) {
      const label = await this.page.evaluate(el => el.textContent, element);
      if (label.includes(text)) {
        element.click();
        found = true;
        break;
      }
    }
    return expect(found).to.be.true;
  }

  async displayHiddenBlock(selector) {
    await page.evaluate(function (selector) {
      let element = document.getElementsByClassName(selector);
      element.style = ''
    }, selector);
  }


  /**
   * These functions are used to sort table then check the sorted table
   * elementsTable, elementsSortedTable are two global variables that must be initialized in the sort table function
   * "normalize('NFKD').replace(/[\u0300-\u036F]/g, '')" is used to replace special characters example ô to o
   * * "normalize('NFKD').replace(/[\u0300-\u036F]/g, '')" is used to replace special characters example € to o
   */
  async getTableField(element_list, i, sorted = false, priceWithCurrency = false) {
    const name = await page.evaluate((selector) => { return document.querySelector(selector).textContent; }, element_list.replace("%ID", i + 1));
    if(sorted){
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
  }
  /**
   * This function checks the sort of a table
   * @param isNumber= true if we sort by a number, isNumber= false if we sort by a string
   * @param sortWay equal to 'ASC' or 'DESC'
   */
  async checkSortTable(isNumber = false, sortWay = 'ASC') {
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
  }
   /**
    * Verify selected Value
    * @param selector : select to check
    * @param value : value of the option
    */
  async isSelected(selector, value = 0) {
    const selectedIndex = await page.evaluate(async (selector) => await document.querySelector(selector).selectedIndex, selector);
    expect(selectedIndex).to.be.equal(value);
  }
  /**
   * Verify not selected Value
   * @param selector : select to check
   * @param value : value of the option
   */
  async isNotSelected(selector, value = 0) {
    const selectedIndex = await page.evaluate(async (selector) => await document.querySelector(selector).selectedIndex, selector);
    expect(selectedIndex).to.be.not.equal(value);
  }

  /**
   * check text from element
   * @param selector : element to get text with
   * @param textToCheckWith : text to check
   * @param parameter : equal / notequal / contain
   */
  async checkTextContent(selector, textToCheckWith, parameter = 'equal') {
    await page.waitFor(selector);

    let value = await page.evaluate((selector) => {
       let elem = document.querySelector(selector);
     return elem.textContent;
    }, selector);
    switch (parameter) {
      case 'contain': {
        expect(value).to.be.contains(textToCheckWith);
        break;
      }
      case 'equal': {
        expect(value).to.be.equal(textToCheckWith);
        break;
      }
      case 'notequal': {
        expect(value).to.not.equal(textToCheckWith);
        break;
      }
    }
    }

  /**
   * get param from URL
   * @param param, param to get
   * @return {boolean}
   */
  async getParamFromURL(param) {
    let current_url = page.url();
    expect(current_url).to.contain(param);
    global.param[param] = current_url.split(param + '=')[1].split("&")[0];
  }

  /**
   * Access to BO function
   */
  async accessToBO() {
    await page.goto(global.URL + '/admin-dev');
  }

  /**
   * Edit Object Data
   * @param object, object to edit
   * @param type, type of object
   */
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

  /**
   * delete object element
   * @param object, object to delete from
   * @param pos, position to delete
   */
  deleteObjectElement(object, pos) {
    delete object[pos];
  }

  /**
   * find and click on element by text
   * @param selector, selector of the list to search in
   * @param textToFind, text to click on
   * @param param, equal / contain
   */
  async findAndClickByText(selector, textToFind, param = 'equal'){
    let number_elements = await page.evaluate((selector) => {return document.querySelectorAll(selector).length;},selector);
    let found = false;
    for(let i =0 ; i<number_elements ; i++){
      let text_content = await page.evaluate((selector,i) => {return document.querySelectorAll(selector)[i].textContent;},selector,i);
      if(param === 'equal' && text_content === textToFind){
        await page.evaluate((selector,i) => {return document.querySelectorAll(selector)[i].click();},selector,i);
        found = true;
        break ;
      }
      else if(param === 'contain' && text_content.includes(textToFind)){
        await page.evaluate((selector,i) => {return document.querySelectorAll(selector)[i].click();},selector,i);
        found = true;
        break ;
      }
    }
    return expect(found).to.be.true;
  }

  /**
   * select by visible text
   * @param selector, selector to find
   * @param text, text of the element to select
   */
  async selectByVisibleText(selector,text){
    let number_options = await page.evaluate((selector) => { return document.querySelectorAll(selector).length; }, selector + ' option');
    let found = false;
    for(let i=0; i< number_options ; i++){
      let text_content = await page.evaluate((selector,i) => { return document.querySelectorAll(selector)[i].textContent; }, selector + ' option',i);
      if(text_content===text){
        let el_value = await page.evaluate((selector,i) => { return document.querySelectorAll(selector)[i].value; }, selector + ' option',i);
        this.waitAndSelectByValue(selector,el_value);
        found = true;
        break;
      }
    }
    expect(found).to.be.true;
  }

  /**
   * get text from an element
   * @param selector, selector to get text from
   */
  async getText(selector){
    let text = await page.evaluate((selector) => {return document.querySelector(selector).innerText;},selector);
    return text;
  }

  /**
   * check validation message puppeteer
   * @param selector
   * @param validationText
   * @param parameter
   */
  async checkElementValidation(selector, validationText, parameter = 'equal') {
    let message = await page.evaluate((selector) => {return document.querySelector(selector).validationMessage;},selector);
    switch (parameter) {
      case "equal":
        expect(message).to.be.equal(validationText);
        break;
      case "contain":
        expect(message).to.contains(validationText);
        break;
      default:
        break;
    }
  }
}

module.exports = CommonClient;
