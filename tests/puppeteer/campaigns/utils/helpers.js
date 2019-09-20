require('./globals');

const puppeteer = require('puppeteer');

module.exports = {
  async createBrowser() {
    return puppeteer.launch({
      headless: JSON.parse(global.HEADLESS),
      timeout: 0,
      slowMo: 25,
      args: ['--start-maximized', '--no-sandbox', '--lang=fr-FR'],
      defaultViewport: {
        width: 1680,
        height: 900,
      },
    });
  },
};
