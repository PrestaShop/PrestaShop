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
};
