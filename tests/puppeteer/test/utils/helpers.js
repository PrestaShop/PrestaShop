require('./globals');
const chai = require('chai');
chai.use(require('chai-string'));

global.expect = chai.expect;

const puppeteer = require('puppeteer');

global.test = (name, instructions) => it(name, () => instructions().catch());

global.scenario = (name, tests, init, close = false) => describe(name, async () => {
  before(async () => {
    global.browser = await puppeteer.launch({
      headless: JSON.parse(global.HEADLESS),
      timeout: 0,
      args: ['--start-maximized', '--no-sandbox'],
      defaultViewport: {
        width: 1270,
        height: 899
      },
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
