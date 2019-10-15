global.FO = {
  URL: process.env.URL_FO || 'http://localhost/prestashop/',
  EMAIL: process.env.CLIENT_LOGIN || 'pub@prestashop.com',
  PASSWD: process.env.CLIENT_PASSWD || '123456789',
};
global.BO = {
  URL: process.env.URL_BO || `${global.FO.URL}admin-dev/`,
  EMAIL: process.env.LOGIN || 'demo@prestashop.com',
  PASSWD: process.env.PASSWD || 'prestashop_demo',
};
global.BROWSER_CONFIG = {
  headless: JSON.parse(process.env.HEADLESS || true),
  timeout: 0,
  slowMo: 25,
  args: ['--start-maximized', '--no-sandbox', '--lang=en-GB'],
  defaultViewport: {
    width: 1680,
    height: 900,
  },
};
