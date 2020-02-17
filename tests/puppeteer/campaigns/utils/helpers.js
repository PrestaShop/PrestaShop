require('./globals');

const puppeteer = require('puppeteer');

module.exports = {
  async createBrowser() {
    return puppeteer.launch(global.BROWSER_CONFIG);
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
