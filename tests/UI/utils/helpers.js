require('./globals');

const playwright = require('playwright');

/**
 * @module BrowserHelper
 * @description Helper used to wrap low level functions from playwright using global parameters.
 */
module.exports = {
  /**
   * Create playwright browser with options on global
   * @param attempt {number} Number of attempts to restart browser creation if function throw error
   * @return {Promise<Browser>}
   */
  async createBrowser(attempt = 1) {
    try {
      const browserConfig = global.BROWSER.config;

      // Add argument for chromium (window size for headful debug and sandbox)
      if (global.BROWSER.name === 'chromium') {
        browserConfig.args = [
          `--window-size=${global.BROWSER.width}, ${global.BROWSER.height}`,
          `--lang=${global.BROWSER.lang}`,
        ];

        browserConfig.args = await (browserConfig.args).concat(global.BROWSER.sandboxArgs);
      }

      return (await playwright[global.BROWSER.name].launch(browserConfig));
    } catch (e) {
      if (attempt <= 3) {
        await (new Promise(resolve => setTimeout(resolve, 5000)));
        return this.createBrowser(attempt + 1);
      }
      throw new Error(e);
    }
  },

  /**
   * Create a browser context
   * @param browser {Browser} Created browser context with options on global
   * @return {Promise<BrowserContext>}
   */
  createBrowserContext(browser) {
    return browser.newContext(
      {
        acceptDownloads: global.BROWSER.acceptDownloads,
        locale: global.BROWSER.lang,
        viewport:
          {
            width: global.BROWSER.width,
            height: global.BROWSER.height,
          },
      },
    );
  },

  /**
   * Create new tab in browser
   * @param context {BrowserContext} Context created
   * @return {Promise<Page>}
   */
  async newTab(context) {
    const page = await context.newPage();

    if (global.BROWSER.interceptErrors) {
      await this.interceptAllErrors(page);
    }
    return page;
  },

  /**
   * Destroy browser instance, that delete as well all files downloaded
   * @param browserContext {BrowserContext} Instance of the browser context to destroy
   * @return {Promise<void>}
   */
  async closeBrowserContext(browserContext) {
    await browserContext.close();
  },

  /**
   * Destroy browser instance, that delete as well all files downloaded
   * @param browser {Browser} Instance of the browser to close
   * @return {Promise<void>}
   */
  async closeBrowser(browser) {
    await browser.close();
  },

  /**
   * Intercept response errors
   * @param page {Page} Browser tab given
   */
  interceptResponseErrors(page) {
    page.on('response', (response) => {
      const status = response.status().toString();
      const url = page.url();
      const requestUrl = response.request().url();

      if (status.startsWith('4') || status.startsWith('5')) {
        global.browserErrors.responses.push({url, requestUrl, status});
      }
    });
  },

  /**
   * Intercept js errors
   * @param page {Page} Browser tab given
   */
  interceptJsErrors(page) {
    page.on('pageerror', (e) => {
      global.browserErrors.js.push(
        {
          url: page.url(),
          error: e.toString(),
        },
      );
    });
  },

  /**
   * Intercept console errors
   * @param page {Page} Browser tab given
   */
  interceptConsoleErrors(page) {
    page.on('console', (msg) => {
      if (msg.type() === 'error') {
        global.browserErrors.console.push({
          url: page.url(),
          error: msg.text(),
        });
      }
    });
  },

  /**
   * Intercept all errors (response, js, console)
   * @param page {Page} Browser tab given
   */
  interceptAllErrors(page) {
    this.interceptResponseErrors(page);
    this.interceptJsErrors(page);
    this.interceptConsoleErrors(page);
  },

  /**
   * Get last opened tab (The current active tab)
   * @param browser {Browser} Browser given
   * @returns {Promise<Page>}
   */
  async getLastOpenedTab(browser) {
    // Get contexts
    const contexts = await browser.contexts();

    // Get pages from last created context
    const tabs = await contexts[contexts.length - 1].pages();

    return tabs[tabs.length - 1];
  },
};
