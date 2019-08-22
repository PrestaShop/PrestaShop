require('./globals');

const puppeteer = require('puppeteer');

global.test = (name, instructions) => it(name, () => instructions().catch());

global.scenario = (name, tests, init, close = false) => describe(name, async () => {
  before(async () => {
    global.browser = await puppeteer.launch({
      headless: JSON.parse(global.HEADLESS),
      timeout: 0,
      slowMo: 25,
      args: ['--start-maximized', '--no-sandbox', '--lang=fr-FR'],
      defaultViewport: {
        width: 1270,
        height: 899,
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
