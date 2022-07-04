/**
 * Parent page, contains functions that can be used in every page (BO, FO ...)
 * @class
 */
class CommonPage {
  /**
   * Get page title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page) {
    return page.title();
  }

  /**
   * Go to URL
   * @param page {Page} Browser tab
   * @param url {string} Url to go to
   * @returns {Promise<void>}
   */
  async goTo(page, url) {
    await page.goto(url);
  }

  /**
   * Go to FO page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToFo(page) {
    await this.goTo(page, global.FO.URL);
  }

  /**
   * Go to BO page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToBO(page) {
    await this.goTo(page, global.BO.URL);
  }

  /**
   * Get current url
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCurrentURL(page) {
    return decodeURIComponent(page.url());
  }

  /**
   * Wait for selector to have a state
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param state {string} Selector state between 'visible'|'hidden'|'attached'|'detached'
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<void>}
   */
  async waitForSelector(page, selector, state, timeout = 10000) {
    await page.waitForSelector(selector, {state, timeout});
  }

  /**
   * Wait for selector to be visible
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForVisibleSelector(page, selector, timeout = 10000) {
    await this.waitForSelector(page, selector, 'visible', timeout);
  }

  /**
   * Wait for selector to be visible
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForHiddenSelector(page, selector, timeout = 10000) {
    await this.waitForSelector(page, selector, 'hidden', timeout);
  }

  /**
   * Wait for selector to be attached
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForAttachedSelector(page, selector, timeout = 10000) {
    await this.waitForSelector(page, selector, 'attached', timeout);
  }

  /**
   * Wait for selector to be detached
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForDetachedSelector(page, selector, timeout = 10000) {
    await this.waitForSelector(page, selector, 'detached', timeout);
  }

  /**
   * Get Text from element
   * @param page {Page} Browser tab
   * @param selector{string} From where to get text
   * @param waitForSelector {boolean} True to wait for selector to be visible before getting text
   * @return {Promise<string>}
   */
  async getTextContent(page, selector, waitForSelector = true) {
    if (waitForSelector) {
      await this.waitForVisibleSelector(page, selector);
    }
    const textContent = await page.textContent(selector);

    return textContent.replace(/\s+/g, ' ').trim();
  }

  /**
   * Get attribute from element
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param attribute {string} Name of the attribute to get
   * @returns {Promise<string>}
   */
  async getAttributeContent(page, selector, attribute) {
    return page.getAttribute(selector, attribute);
  }

  /**
   * Is element visible
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<boolean>} True if visible, false if not
   */
  async elementVisible(page, selector, timeout = 10) {
    try {
      await this.waitForVisibleSelector(page, selector, timeout);
      return true;
    } catch (error) {
      return false;
    }
  }

  /**
   * Is element not visible
   * @param page {Page} Browser tab
   * @param selector, element to check
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<boolean>} True if not visible, false if visible
   */
  async elementNotVisible(page, selector, timeout = 10) {
    try {
      await this.waitForHiddenSelector(page, selector, timeout);
      return true;
    } catch (error) {
      return false;
    }
  }

  /**
   * Open link in new Tab and get opened Page
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element for the click
   * @param newPageSelector {string} String to locate the element on the opened page (default to FO logo)
   * @return {Promise<Page>} Opened tab after the click
   */
  async openLinkWithTargetBlank(page, selector, newPageSelector = 'body .logo') {
    const [newPage] = await Promise.all([
      page.waitForEvent('popup'),
      page.click(selector),
    ]);

    await newPage.waitForLoadState('networkidle');

    await this.waitForVisibleSelector(newPage, newPageSelector);
    return newPage;
  }

  /**
   * Wait for selector and click
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element for the check
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForSelectorAndClick(page, selector, timeout = 5000) {
    await this.waitForVisibleSelector(page, selector, timeout);
    await page.click(selector);
  }

  /**
   * Reload actual browser page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async reloadPage(page) {
    await page.reload();
  }

  /**
   * Delete the existing text from input then set a value
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the input to set its value
   * @param value {?string|number} Value to set on the input
   * @return {Promise<void>}
   */
  async setValue(page, selector, value) {
    await this.clearInput(page, selector);

    if (value !== null) {
      await page.type(selector, value.toString());
    }
  }

  /**
   * Delete text from input
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element for the deletion
   * @returns {Promise<void>}
   */
  async clearInput(page, selector) {
    await this.waitForVisibleSelector(page, selector);
    // eslint-disable-next-line no-return-assign,no-param-reassign
    await page.$eval(selector, el => el.value = '');
  }

  /**
   * To accept or dismiss a javascript dialog
   * @param page {Page} Browser tab
   * @param accept {boolean} True to accept the dialog, false to dismiss
   * @param text {string} Text to set on dialog input
   * @return {Promise<void>}
   */
  async dialogListener(page, accept = true, text = '') {
    page.once('dialog', (dialog) => {
      if (accept && text === '') {
        dialog.accept();
      } else if (text !== '') {
        dialog.accept(text);
      } else {
        dialog.dismiss();
      }
    });
  }

  /**
   * Close actual tab and goto another tab if wanted
   * @param browserContext {BrowserContext} Context of the page
   * @param page {Page} Browser tab
   * @param tabId {number} Tab to get focus on after closing the other tab
   * @return {Promise<void>}
   */
  async closePage(browserContext, page, tabId = -1) {
    await page.close();
    let focusedPage;

    if (tabId !== -1) {
      focusedPage = (await browserContext.pages())[tabId];
    }
    return focusedPage;
  }

  /**
   * Scroll to element
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element to scroll to
   * @return {Promise<void>}
   */
  async scrollTo(page, selector) {
    await page.$eval(selector, el => el.scrollIntoView());
  }

  /**
   * Select option in select by visible text
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the select
   * @param textValue {string/number} Value to select
   * @param force {boolean} Forcing the value of the select
   * @returns {Promise<void>}
   */
  async selectByVisibleText(page, selector, textValue, force = false) {
    await page.selectOption(selector, {label: textValue.toString()}, {force});
  }

  /**
   * To get a number from text
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<number>}
   */
  async getNumberFromText(page, selector, timeout = 0) {
    await page.waitForTimeout(timeout);
    const text = await this.getTextContent(page, selector);
    const number = /\d+/g.exec(text).toString();

    return parseInt(number, 10);
  }

  /**
   * Go to Page and wait for navigation
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param waitUntil {'load'|'domcontentloaded'|'networkidle'|'commit'} The event to wait after click
   * @param timeout {number} Time to wait for navigation
   * @return {Promise<void>}
   */
  async clickAndWaitForNavigation(page, selector, waitUntil = 'networkidle', timeout = 30000) {
    await Promise.all([
      page.waitForNavigation({waitUntil, timeout}),
      page.click(selector),
    ]);
  }

  /**
   * Navigate to the previous page in history
   * @param page {Page} Browser tab
   * @param waitUntil {string} The event to wait after click (load/networkidle/domcontentloaded)
   * @return {Promise<void>}
   */
  async goToPreviousPage(page, waitUntil = 'load') {
    await page.goBack({waitUntil});
  }

  /**
   * Check if checkbox is selected
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the checkbox
   * @return {Promise<boolean>}
   */
  isChecked(page, selector) {
    return page.isChecked(selector);
  }

  /**
   * Select, unselect checkbox
   * @param page {Page} Browser tab
   * @param checkboxSelector {string} String to locate the checkbox
   * @param valueWanted {boolean} Value wanted on the selector
   * @return {Promise<void>}
   */
  async setChecked(page, checkboxSelector, valueWanted = true) {
    await page.setChecked(checkboxSelector, valueWanted);
  }

  /**
   * Set checkbox value when its hidden
   * @param page {Page} Browser tab
   * @param checkboxSelector {string} Selector of the checkbox resolve hidden
   * @param valueWanted {boolean} Wanted value for the checkbox
   * @return {Promise<void>}
   */
  async setHiddenCheckboxValue(page, checkboxSelector, valueWanted = true) {
    if (valueWanted !== (await this.isChecked(page, checkboxSelector))) {
      const parentElement = await this.getParentElement(page, checkboxSelector);
      await parentElement.click();
    }
  }

  /**
   * Select, unselect checkbox with icon click
   * @param page {Page} Browser tab
   * @param checkboxSelector {string} Selector of checkbox
   * @param valueWanted {boolean} True if we want to select checkBox, else otherwise
   * @return {Promise<void>}
   */
  async setCheckedWithIcon(page, checkboxSelector, valueWanted = true) {
    if (valueWanted !== (await this.isChecked(page, checkboxSelector))) {
      // The selector is not visible, that why '+ i' is required here
      await page.$eval(`${checkboxSelector} + i`, el => el.click());
    }
  }

  /**
   * Drag and drop element
   * @param page {Page} Browser tab
   * @param source {string} String to locate the element to drag
   * @param target {string} String to locate the element where to drop
   * @return {Promise<void>}
   */
  async dragAndDrop(page, source, target) {
    await page.dragAndDrop(source, target);
  }

  /**
   * Upload file in input type=file selector
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the file input
   * @param filePath {string} Path of the file to add
   * @return {Promise<void>}
   */
  async uploadFile(page, selector, filePath) {
    const input = await page.$(selector);
    await input.setInputFiles(filePath);
  }

  /**
   * Upload file using file chooser
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the file chooser
   * @param filePath {Array<string>} Path of the file to add
   * @returns {Promise<void>}
   */
  async uploadOnFileChooser(page, selector, filePath) {
    // Set value when fileChooser is open
    page.once('filechooser', async (fileChooser) => {
      await fileChooser.setFiles(filePath);
    });
    await page.click(selector);
  }

  /**
   * Get a float price from text
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<number>}
   */
  async getPriceFromText(page, selector, timeout = 0) {
    await page.waitForTimeout(timeout);
    const text = await this.getTextContent(page, selector);

    const number = Number(text.replace(/[^0-9.-]+/g, ''));

    return parseFloat(number);
  }

  /**
   * Get parent element from selector
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the child element
   * @return {Promise<ElementHandle>}
   */
  getParentElement(page, selector) {
    /* eslint-env browser */
    return page.evaluateHandle(sl => document.querySelector(sl).parentElement, selector);
  }

  /**
   * Click on selector and wait for download event
   * @param page {Page} Browser tab
   * @param selector {string} Selector to click on
   * @param targetBlank {boolean} Link has attribute target=blank
   * @returns {Promise<string>}
   */
  async clickAndWaitForDownload(page, selector, targetBlank = false) {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    if (targetBlank) {
      await page.$eval(selector, el => el.target = '');
    }
    /* eslint-enable no-return-assign, no-param-reassign */

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(selector),
    ]);

    return download.path();
  }

  /**
   * Wait for title to be filled
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async waitForPageTitleToLoad(page) {
    let isTitleEmpty = true;
    for (let i = 0; i < 20 && isTitleEmpty; i++) {
      isTitleEmpty = (await this.getPageTitle(page) === '');
      await page.waitForTimeout(100);
    }
  }
}

module.exports = CommonPage;
