require('./globals');
const chai = require('chai');
chai.use(require('chai-string'));
global.expect = chai.expect;

const puppeteer = require('puppeteer');

global.test = (name, instructions) => it(name, () => {
  return instructions().catch();
});

global.scenario = (name, tests, init, close = false) =>
  describe(name, async () => {
    before(async () => {
      global.browser = await puppeteer.launch({
        headless: global.HEADLESS,
        timeout: 0,
        args: ['--start-maximized', '--window-size=1920,1040', '--no-sandbox']
      });
      await init();
    });

    await tests();

    if (close) {
      await after(async () => {
        await global.browser.close();
      });
    }
  });
