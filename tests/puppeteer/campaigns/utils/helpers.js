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
        width: 1680,
        height: 900,
      },
    });
    await init();
  });
  after(async () => {
    if(close)
      await global.browser.close();
  });

  await tests();
});
