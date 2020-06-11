require('./globals');

const playwright = require('playwright');

module.exports = {
  /**
   * Create playwright browser
   *
   * @param attempt, number of attempts to restart browser creation if function throw error
   * @return {Promise<browser>}
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
   *
   * @param browser
   * @return {Promise<*>}
   */
  async createBrowserContext(browser) {
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
   *
   * @param context
   * @return {Promise<*>}
   */
  async newTab(context) {
    return context.newPage();
  },

  /**
   * Destroy browser instance, that delete as well all files downloaded
   *
   * @param browserContext
   * @return {Promise<*>}
   */
  async closeBrowserContext(browserContext) {
    return browserContext.close();
  },

  /**
   * Destroy browser instance, that delete as well all files downloaded
   *
   * @param browser
   * @return {Promise<*>}
   */
  async closeBrowser(browser) {
    return browser.close();
  },
};
