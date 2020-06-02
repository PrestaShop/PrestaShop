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
      return (await playwright[global.BROWSER].launch(global.BROWSER_CONFIG));
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
        acceptDownloads: true,
        locale: 'en-GB',
        viewport:
          {
            width: 1680,
            height: 900,
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
   * @param browser
   * @return {Promise<*>}
   */
  async closeBrowser(browser) {
    return browser.close();
  },
};
