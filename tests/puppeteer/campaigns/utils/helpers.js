require('./globals');

const puppeteer = require('puppeteer');

module.exports = {
  /**
   * Create puppeteer browser
   * @param attempt, number of attempts to restart browser creation if function throw error
   * @return {Promise<browser>}
   */
  async createBrowser(attempt = 1) {
    try {
      return await puppeteer.launch(global.BROWSER_CONFIG);
    } catch (e) {
      if (attempt <= 3) {
        await (new Promise(resolve => setTimeout(resolve, 5000)));
        return this.createBrowser(attempt + 1);
      }
      throw new Error(e);
    }
  },
  async newTab(browser) {
    return browser.newPage();
  },
  async closeBrowser(browser) {
    return browser.close();
  },
  async setDownloadBehavior(page) {
    await page._client.send('Page.setDownloadBehavior', {
      behavior: 'allow',
      downloadPath: global.BO.DOWNLOAD_PATH,
    });
  },
};
