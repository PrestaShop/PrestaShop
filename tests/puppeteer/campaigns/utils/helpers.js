require('./globals');

const playwright = require('playwright');

module.exports = {
  /**
   * Create puppeteer browser
   * @param attempt, number of attempts to restart browser creation if function throw error
   * @return {Promise<browser>}
   */
  async createBrowser(attempt = 1) {
    try {
      const browser = await playwright[global.BROWSER].launch(global.BROWSER_CONFIG);
      return await browser.newContext({
        acceptDownloads:true,
        viewport: {
          width: 1680,
          height: 900,
        }
      });
    } catch (e) {
      if (attempt <= 3) {
        await (new Promise(resolve => setTimeout(resolve, 5000)));
        return this.createBrowser(attempt + 1);
      }
      throw new Error(e);
    }
  },
  async newTab(context) {
    return context.newPage();
  },
  async closeBrowser(browser) {
    return browser.close();
  },
  async setDownloadBehavior(page) {
    /*await page._client.send('Page.setDownloadBehavior', {
      behavior: 'allow',
      downloadPath: global.BO.DOWNLOAD_PATH,
    });*/
  },
};
