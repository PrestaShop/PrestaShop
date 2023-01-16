// Import data
import type {
  PageWaitForSelectorOptionsState,
  WaitForNavigationWaitUntil,
} from '@data/types/playwright';

import {
  BrowserContext, ElementHandle, FileChooser, Page,
} from 'playwright';
import {Unboxed} from 'playwright-core/types/structs';

/**
 * Parent page, contains functions that can be used in every page (BO, FO ...)
 * @class
 */
export default class CommonPage {
  /**
   * Get page title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page: Page): Promise<string> {
    return page.title();
  }

  /**
   * Go to URL
   * @param page {Page} Browser tab
   * @param url {string} Url to go to
   * @returns {Promise<void>}
   */
  async goTo(page: Page, url: string): Promise<void> {
    await page.goto(url);
  }

  /**
   * Go to FO page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToFo(page: Page): Promise<void> {
    await this.goTo(page, global.FO.URL);
  }

  /**
   * Go to BO page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToBO(page: Page): Promise<void> {
    await this.goTo(page, global.BO.URL);
  }

  /**
   * Get current url
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCurrentURL(page: Page): Promise<string> {
    return decodeURIComponent(page.url());
  }

  /**
   * Wait for selector to have a state
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param state {PageWaitForSelectorOptionsState} Selector state between 'visible'|'hidden'|'attached'|'detached'
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<void>}
   */
  async waitForSelector(
    page: Page,
    selector: string,
    state: PageWaitForSelectorOptionsState,
    timeout: number = 10000,
  ): Promise<void> {
    await page.waitForSelector(selector, {state, timeout});
  }

  /**
   * Wait for selector to be visible
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForVisibleSelector(page: Page, selector: string, timeout: number = 10000): Promise<void> {
    await this.waitForSelector(page, selector, 'visible', timeout);
  }

  /**
   * Wait for selector to be visible
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForHiddenSelector(page: Page, selector: string, timeout: number = 10000): Promise<void> {
    await this.waitForSelector(page, selector, 'hidden', timeout);
  }

  /**
   * Wait for selector to be attached
   * @param page {Page} Browser tab
   * @param selector {string} selector to wait
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @return {Promise<void>}
   */
  async waitForAttachedSelector(page: Page, selector: string, timeout: number = 10000): Promise<void> {
    await this.waitForSelector(page, selector, 'attached', timeout);
  }

  /**
   * Get Text from element
   * @param page {Page} Browser tab
   * @param selector{string} From where to get text
   * @param waitForSelector {boolean} True to wait for selector to be visible before getting text
   * @return {Promise<string>}
   */
  async getTextContent(page: Page, selector: string, waitForSelector: boolean = true): Promise<string> {
    if (waitForSelector) {
      await this.waitForVisibleSelector(page, selector);
    }
    const textContent = await page.textContent(selector);

    return (textContent ?? '').replace(/\s+/g, ' ').trim();
  }

  /**
   * Get attribute from element
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param attribute {string} Name of the attribute to get
   * @returns {Promise<string|null>}
   */
  async getAttributeContent(page: Page, selector: string, attribute: string): Promise<string|null> {
    return page.getAttribute(selector, attribute);
  }

  /**
   * Is element visible
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<boolean>} True if visible, false if not
   */
  async elementVisible(page: Page, selector: string, timeout: number = 10): Promise<boolean> {
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
  async elementNotVisible(page: Page, selector: string, timeout: number = 10): Promise<boolean> {
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
  async openLinkWithTargetBlank(page: Page, selector: string, newPageSelector: string = 'body .logo'): Promise<Page> {
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
  async waitForSelectorAndClick(page: Page, selector: string, timeout: number = 5000): Promise<void> {
    await this.waitForVisibleSelector(page, selector, timeout);
    await page.click(selector);
  }

  /**
   * Reload actual browser page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async reloadPage(page: Page): Promise<void> {
    await page.reload();
  }

  /**
   * Delete the existing text from input then set a value
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the input to set its value
   * @param value {?string|number} Value to set on the input
   * @return {Promise<void>}
   */
  async setValue(page: Page, selector: string, value: string|number): Promise<void> {
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
  async clearInput(page: Page, selector:string): Promise<void> {
    await this.waitForVisibleSelector(page, selector);
    // eslint-disable-next-line no-return-assign,no-param-reassign
    await page.$eval(selector, (el: HTMLInputElement) => el.value = '');
  }

  /**
   * To accept or dismiss a javascript dialog
   * @param page {Page} Browser tab
   * @param accept {boolean} True to accept the dialog, false to dismiss
   * @param text {string} Text to set on dialog input
   * @return {Promise<void>}
   */
  async dialogListener(page: Page, accept: boolean = true, text: string = ''): Promise<void> {
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
   * @return {Promise<Page|undefined>}
   */
  async closePage(browserContext: BrowserContext, page: Page, tabId: number = -1): Promise<Page|undefined> {
    await page.close();
    let focusedPage: Page|undefined;

    if (tabId !== -1) {
      focusedPage = (browserContext.pages())[tabId];
    }
    return focusedPage;
  }

  /**
   * Scroll to element
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element to scroll to
   * @return {Promise<void>}
   */
  async scrollTo(page: Page, selector: string): Promise<void> {
    await page.$eval(selector, (el) => el.scrollIntoView());
  }

  /**
   * Select option in select by visible text
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the select
   * @param textValue {string/number} Value to select
   * @param force {boolean} Forcing the value of the select
   * @returns {Promise<void>}
   */
  async selectByVisibleText(page: Page, selector: string, textValue: string|number, force: boolean = false): Promise<void> {
    await page.selectOption(selector, {label: textValue.toString()}, {force});
  }

  /**
   * Select option by value
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the select
   * @param valueToSelect {number} Value to select
   * @param force {boolean} Forcing the value of the select
   * @returns {Promise<void>}
   */
  async selectByValue(page: Page, selector: string, valueToSelect: number, force: boolean = false): Promise<void> {
    await page.selectOption(selector, {value: valueToSelect.toString()}, {force});
  }

  /**
   * To get a number from text
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param timeout {number} Time to wait on milliseconds before throwing an error
   * @returns {Promise<number>}
   */
  async getNumberFromText(page: Page, selector: string, timeout: number = 0): Promise<number> {
    await page.waitForTimeout(timeout);
    const text = await this.getTextContent(page, selector);
    const number = (/\d+/g.exec(text) ?? '').toString();

    return parseInt(number, 10);
  }

  /**
   * Go to Page and wait for navigation
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the element
   * @param waitUntil {WaitForNavigationWaitUntil} The event to wait after click
   * @param timeout {number} Time to wait for navigation
   * @return {Promise<void>}
   */
  async clickAndWaitForNavigation(
    page: Page,
    selector: string,
    waitUntil: WaitForNavigationWaitUntil = 'networkidle',
    timeout: number = 30000,
  ): Promise<void> {
    await Promise.all([
      page.waitForNavigation({waitUntil, timeout}),
      page.click(selector),
    ]);
  }

  /**
   * Navigate to the previous page in history
   * @param page {Page} Browser tab
   * @param waitUntil {WaitForNavigationWaitUntil} The event to wait after click (load/networkidle/domcontentloaded)
   * @return {Promise<void>}
   */
  async goToPreviousPage(page: Page, waitUntil: WaitForNavigationWaitUntil = 'load'): Promise<void> {
    await page.goBack({waitUntil});
  }

  /**
   * Check if checkbox is selected
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the checkbox
   * @return {Promise<boolean>}
   */
  isChecked(page: Page, selector: string): Promise<boolean> {
    return page.isChecked(selector);
  }

  /**
   * Select, unselect checkbox
   * @param page {Page} Browser tab
   * @param checkboxSelector {string} String to locate the checkbox
   * @param valueWanted {boolean} Value wanted on the selector
   * @return {Promise<void>}
   */
  async setChecked(page: Page, checkboxSelector: string, valueWanted: boolean = true): Promise<void> {
    await page.setChecked(checkboxSelector, valueWanted);
  }

  /**
   * Set checkbox value when its hidden
   * @param page {Page} Browser tab
   * @param checkboxSelector {string} Selector of the checkbox resolve hidden
   * @param valueWanted {boolean} Wanted value for the checkbox
   * @return {Promise<void>}
   */
  async setHiddenCheckboxValue(page: Page, checkboxSelector: string, valueWanted: boolean = true): Promise<void> {
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
  async setCheckedWithIcon(page: Page, checkboxSelector: string, valueWanted: boolean = true): Promise<void> {
    if (valueWanted !== (await this.isChecked(page, checkboxSelector))) {
      // The selector is not visible, that why '+ i' is required here
      await page.$eval(`${checkboxSelector} + i`, (el: HTMLInputElement) => el.click());
    }
  }

  /**
   * Drag and drop element
   * @param page {Page} Browser tab
   * @param source {string} String to locate the element to drag
   * @param target {string} String to locate the element where to drop
   * @return {Promise<void>}
   */
  async dragAndDrop(page: Page, source: string, target: string): Promise<void> {
    await page.dragAndDrop(source, target);
  }

  /**
   * Upload file in input type=file selector
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the file input
   * @param filePath {string} Path of the file to add
   * @return {Promise<void>}
   */
  async uploadFile(page: Page, selector: string, filePath: string): Promise<void> {
    const input = await page.$(selector);

    if (input) {
      await input.setInputFiles(filePath);
    }
  }

  /**
   * Upload file using file chooser
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the file chooser
   * @param filePath {Array<string>} Path of the file to add
   * @returns {Promise<void>}
   */
  async uploadOnFileChooser(page: Page, selector: string, filePath: string): Promise<void> {
    // Set value when fileChooser is open
    page.once('filechooser', async (fileChooser: FileChooser) => {
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
  async getPriceFromText(page: Page, selector: string, timeout: number = 0): Promise<number> {
    await page.waitForTimeout(timeout);
    const text = await this.getTextContent(page, selector);

    return Number(text.replace(/[^0-9.-]+/g, ''));
  }

  /**
   * Get parent element from selector
   * @param page {Page} Browser tab
   * @param selector {string} String to locate the child element
   * @return {Promise<ElementHandle>}
   */
  getParentElement(page: Page, selector: string): Promise<ElementHandle> {
    /* eslint-env browser */
    return page.evaluateHandle((sl: Unboxed<string>) => document.querySelector(sl).parentElement, selector);
  }

  /**
   * Click on selector and wait for download event
   * @param page {Page} Browser tab
   * @param selector {string} Selector to click on
   * @param targetBlank {boolean} Link has attribute target=blank
   * @returns {Promise<string|null>}
   */
  async clickAndWaitForDownload(page: Page, selector: string, targetBlank: boolean = false): Promise<string|null> {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    if (targetBlank) {
      await page.$eval(selector, (el: HTMLLinkElement) => el.target = '');
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
  async waitForPageTitleToLoad(page: Page): Promise<void> {
    let isTitleEmpty = true;

    for (let i = 0; i < 20 && isTitleEmpty; i++) {
      isTitleEmpty = (await this.getPageTitle(page) === '');
      await page.waitForTimeout(100);
    }
  }

  /**
   * Resize the page to defined viewport
   * @param page {Page} Browser tab
   * @param mobileSize {boolean} Define if the viewport is for mobile or not
   * @returns {Promise<void>}
   */
  async resize(page: Page, mobileSize: boolean): Promise<void> {
    if (mobileSize) {
      await page.setViewportSize({width: 600, height: 600});
    } else {
      await page.setViewportSize({width: global.BROWSER.width, height: global.BROWSER.height});
    }
  }
}

module.exports = CommonPage;
