require('./globals');

const puppeteer = require('puppeteer');

module.exports = {
  async createBrowser() {
    return puppeteer.launch(global.browserConfig);
  },
  async newTab(browser) {
    return browser.newPage();
  },
  async closeBrowser(browser) {
    return browser.close();
  },
};
