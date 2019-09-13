// Using chai
const {expect} = require('chai');

module.exports = class CommonPage {
  constructor(page) {
    this.page = page;
  }

  async getPageTitle() {
    return this.page.title();
  }

  async goTo(URL) {
    await this.page.goto(URL);
  }

  /**
   * Get Text from element
   * @param selector, from where to get text
   * @return textContent
   */
  async getTextContent(selector) {
    return this.page.$eval(selector, el => el.textContent);
  }

  /**
   * Is checkBox have checked status
   * @param selector, checkbox to check
   * @return boolean, true if checked, false if not
   */
  async elementChecked(selector) {
    return this.page.$eval(selector, el => el.checked);
  }

  /**
   * Is element visible
   * @param selector, element to check
   * @return boolean, true if visible, false if not
   */
  async elementVisible(selector, timeout = 10) {
    try {
      await this.page.waitForSelector(selector, {visible: true, timeout});
      return true;
    } catch (error) {
      return false;
    }
  }

  /**
   * Open link in new Tab and get opened Page
   * @param currentPage, current page where to click on selector
   * @param selector, where to click
   * @return newPage, what was opened by the browser
   */
  async openLinkWithTargetBlank(currentPage, selector, waitForNavigation = true) {
    const [newPage] = await Promise.all([
      new Promise(resolve => this.page.once('popup', resolve)),
      currentPage.click(selector),
    ]);
    if(waitForNavigation)
      await newPage.waitForNavigation({waitUntil: 'networkidle0'});
    await newPage.waitForSelector('body', {visible: true});
    return newPage;
  }

  /**
   * Wait for selector and click
   * @param selector, element to check
   * @param timeout, wait timeout
   * @return {Promise<void>}
   */
  async waitForSelectorAndClick(selector, timeout = 5000) {
    await this.page.waitForSelector(selector, {visible: true, timeout});
    await this.page.click(selector);
  }

  /**
   * Check text value
   * @param selector, element to check
   * @param textToCheckWith, text to check with
   * @param parameter, parameter to use
   * @return promise, throw an error if element does not exist or text is not correct
   */
  async checkTextValue(selector, textToCheckWith, parameter = 'equal') {
    await this.page.waitForSelector(selector);
    switch (parameter) {
      case 'equal':
        await this.page.$eval(selector, el => el.innerText)
          .then(text => expect(text.replace(/\s+/g, ' ').trim()).to.equal(textToCheckWith));
        break;
      case 'contain':
        await this.page.$eval(selector, el => el.innerText)
          .then(text => expect(text).to.contain(textToCheckWith));
        break;
      default:
      // do nothing
    }
  }

  /**
   * Check attribute value
   * @param selector, element to check
   * @param attribute, attribute to test
   * @param textToCheckWith, text to check with
   * @return promise, throw an error if element does not exist or attribute value is not correct
   */
  async checkAttributeValue(selector, attribute, textToCheckWith) {
    await this.page.waitForSelector(selector);
    const value = await this.page.$eval(selector, (el, attr) => el
      .getAttribute(attr), attribute);
    expect(value).to.be.equal(textToCheckWith);
  }

  /**
   * Reload actual browser page
   * @return {Promise<void>}
   */
  async reloadPage() {
    await this.page.reload({waitUntil: 'networkidle0'});
  }

  /**
   * Delete the existing text from input then set a value
   * @param selector, input
   * @param value, value to set in the input
   * @return {Promise<void>}
   */
  async setValue(selector, value) {
    await this.waitForSelectorAndClick(selector);
    await this.page.click(selector, {clickCount: 3});
    await this.page.type(selector, value);
  }

  /**
   * To accept or dismiss a navigator dialog
   * @param accept
   * @return {Promise<void>}
   */
  async dialogListener(accept = true) {
    this.page.once('dialog', (dialog) => {
      if (accept) dialog.accept();
      else dialog.dismiss();
    });
  }

  /**
   * Close actual tab and goto another tab if wanted
   * @param tabId
   * @return {Promise<void>}
   */
  async closePage(tabId = -1) {
    await this.page.close();
    if (tabId !== -1) {
      this.page = (await global.browser.pages())[tabId];
      await this.page.bringToFront();
      await this.page.waitFor(10000);
    }
    return this.page;
  }

  /**
   * Scroll to element
   * @param selector
   * @return {Promise<void>}
   */
  async scrollTo(selector) {
    await this.page.$eval(selector, el => el.scrollIntoView());
  }


  /**
   * Select option in select by visible text
   * @param selector, id of select
   * @param textValue, text in option to select
   */
  async selectByVisibleText(selector, textValue) {
    let found = false;
    const options = await this.page.$$(`${selector} option`);
    for (let i = 0; i < options.length; i++) {
      /*eslint-disable*/
      const elementText = await (await options[i].getProperty('textContent')).jsonValue();
      if (elementText === textValue) {
        const elementValue = await (await options[i].getProperty('value')).jsonValue();
        await this.page.select(selector, elementValue);
        found = true;
        break;
      }
      /* eslint-enable */
    }
    await expect(found, `${textValue} was not found as option of select`).to.be.true;
  }

  /**
   * To get a number from text
   * @param selector
   * @param timeout
   * @return integer
   */
  async getNumberFromText(selector, timeout = 0) {
    await this.page.waitFor(timeout);
    const text = await this.getTextContent(selector);
    const number = /\d+/g.exec(text).toString();
    return parseInt(number, 10);
  }
};
