module.exports = class CommonPage {
  /**
   * Get page title
   * @param page
   * @returns {Promise<string>}
   */
  async getPageTitle(page) {
    return page.title();
  }

  /**
   * Go to URL
   * @param page
   * @param url
   * @returns {Promise<void>}
   */
  async goTo(page, url) {
    await page.goto(url);
  }

  /**
   * Get current url
   * @param page
   * @returns {Promise<string>}
   */
  async getCurrentURL(page) {
    return decodeURIComponent(page.url());
  }

  /**
   * Wait for selector to be visible
   * @param page
   * @param selector
   * @param timeout
   * @return {Promise<void>}
   */
  async waitForVisibleSelector(page, selector, timeout = 10000) {
    await page.waitForSelector(selector, {state: 'visible', timeout});
  }

  /**
   * Get Text from element
   * @param page
   * @param selector, from where to get text
   * @param waitForSelector
   * @return {Promise<string>}
   */
  async getTextContent(page, selector, waitForSelector = true) {
    if (waitForSelector) {
      await this.waitForVisibleSelector(page, selector);
    }
    const textContent = await page.$eval(selector, el => el.textContent);
    return textContent.replace(/\s+/g, ' ').trim();
  }

  /**
   * Get attribute from element
   * @param page
   * @param selector
   * @param attribute
   * @returns {Promise<string>}
   */
  async getAttributeContent(page, selector, attribute) {
    await page.waitForSelector(selector, {state: 'attached'});
    return page.$eval(selector, (el, attr) => el
      .getAttribute(attr), attribute);
  }

  /**
   * Is checkBox have checked status
   * @param page
   * @param selector, checkbox to check
   * @returns {Promise<boolean>}
   */
  async elementChecked(page, selector) {
    return page.$eval(selector, el => el.checked);
  }

  /**
   * Update checkbox value
   * @param page
   * @param selector
   * @param expectedValue
   * @return {Promise<void>}
   */
  async updateCheckboxValue(page, selector, expectedValue) {
    const actualValue = await this.elementChecked(page, selector);
    if (actualValue !== expectedValue) {
      await page.click(selector);
    }
  }

  /**
   * Is element visible
   * @param page
   * @param selector, element to check
   * @param timeout, how much should we wait
   * @returns {Promise<boolean>}, true if visible, false if not
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
   * @param page
   * @param selector, element to check
   * @param timeout, how much should we wait
   * @returns {Promise<boolean>}, true if visible, false if not
   */
  async elementNotVisible(page, selector, timeout = 10) {
    try {
      await page.waitForSelector(selector, {state: 'hidden', timeout});
      return true;
    } catch (error) {
      return false;
    }
  }

  /**
   * Open link in new Tab and get opened Page
   * @param page
   * @param selector, where to click
   * @param newPageSelector, selector to wait in new page (default to FO logo)
   * @return newPage, what was opened by the browser
   */
  async openLinkWithTargetBlank(page, selector, newPageSelector = 'body .logo') {
    const [newPage] = await Promise.all([
      page.waitForEvent('popup'),
      page.click(selector),
    ]);

    await newPage.waitForLoadState('networkidle');

    await newPage.waitForSelector(newPageSelector, {state: 'visible'});
    return newPage;
  }

  /**
   * Wait for selector and click
   * @param page
   * @param selector, element to check
   * @param timeout, wait timeout
   * @return {Promise<void>}
   */
  async waitForSelectorAndClick(page, selector, timeout = 5000) {
    await this.waitForVisibleSelector(page, selector, timeout);
    await page.click(selector);
  }

  /**
   * Reload actual browser page
   * @param page
   * @return {Promise<void>}
   */
  async reloadPage(page) {
    await page.reload();
  }

  /**
   * Delete the existing text from input then set a value
   * @param page
   * @param selector, input
   * @param value, value to set in the input
   * @return {Promise<void>}
   */
  async setValue(page, selector, value) {
    await this.waitForSelectorAndClick(page, selector);
    await page.click(selector, {clickCount: 3});
    // Delete text from input before typing
    await page.press(selector, 'Delete');
    if (value !== null) {
      await page.type(selector, value.toString());
    }
  }

  /**
   * To accept or dismiss a navigator dialog
   * @param page
   * @param accept
   * @return {Promise<void>}
   */
  async dialogListener(page, accept = true) {
    page.once('dialog', (dialog) => {
      if (accept) dialog.accept();
      else dialog.dismiss();
    });
  }

  /**
   * Close actual tab and goto another tab if wanted
   * @param browserContext
   * @param page
   * @param tabId
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
   * @param page
   * @param selector
   * @return {Promise<void>}
   */
  async scrollTo(page, selector) {
    await page.$eval(selector, el => el.scrollIntoView());
  }

  /**
   * Select option in select by visible text
   * @param page
   * @param selector
   * @param textValue
   * @returns {Promise<void>}
   */
  async selectByVisibleText(page, selector, textValue) {
    let found = false;
    let options = await page.$$eval(
      `${selector} option`,
      all => all.map(
        option => ({
          textContent: option.textContent,
          value: option.value,
        })),
    );

    options = await options.filter(option => textValue === option.textContent.trim());
    if (options.length !== 0) {
      const elementValue = await options[0].value;
      await page.selectOption(selector, elementValue);
      found = true;
    }
    if (!found) throw new Error(`${textValue} was not found as option of select`);
  }

  /**
   * To get a number from text
   * @param page
   * @param selector
   * @param timeout
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
   * @param page
   * @param selector
   * @param waitUntil, the event to wait after click (load/networkidle/domcontentloaded)
   * @return {Promise<void>}
   */
  async clickAndWaitForNavigation(page, selector, waitUntil = 'networkidle') {
    await Promise.all([
      page.waitForNavigation({waitUntil}),
      page.click(selector),
    ]);
  }

  /**
   * Navigate to the previous page in history
   * @param page
   * @param waitUntil
   * @return {Promise<void>}
   */
  async goToPreviousPage(page, waitUntil = 'load') {
    await page.goBack({waitUntil});
  }

  /**
   * Check if checkbox is selected
   * @param page
   * @param selector
   * @return {Promise<boolean>}
   */
  async isCheckboxSelected(page, selector) {
    return page.$eval(selector, el => el.checked);
  }

  /**
   * Select, unselect checkbox
   * @param page
   * @param checkboxSelector, selector of checkbox
   * @param valueWanted, true if we want to select checkBox, else otherwise
   * @return {Promise<void>}
   */
  async changeCheckboxValue(page, checkboxSelector, valueWanted = true) {
    if (valueWanted !== (await this.isCheckboxSelected(page, checkboxSelector))) {
      await page.click(checkboxSelector);
    }
  }

  /**
   * Sort array of strings or numbers
   * @param arrayToSort
   * @param isFloat
   * @return {Promise<*>}
   */
  async sortArray(arrayToSort, isFloat = false) {
    if (isFloat) {
      return arrayToSort.sort((a, b) => a - b);
    }
    return arrayToSort.sort((a, b) => a.localeCompare(b));
  }

  /**
   * Drag and drop element
   * @param page
   * @param selectorToDrag
   * @param selectorWhereToDrop
   * @return {Promise<void>}
   */
  async dragAndDrop(page, selectorToDrag, selectorWhereToDrop) {
    await page.hover(selectorToDrag);
    await page.mouse.down();
    await page.hover(selectorWhereToDrop);
    await page.mouse.up();
  }

  /**
   * Uppercase the first character of the word
   * @param word
   * @returns {string}
   */
  uppercaseFirstCharacter(word) {
    return `${word[0].toUpperCase()}${word.slice(1)}`;
  }
};
