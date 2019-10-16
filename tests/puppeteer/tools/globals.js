global.INFORMATIONS = {
  URL_FO: process.env.URL_FO || 'http://localhost/prestashop/',
  EMAIL_CLIENT: process.env.CLIENT_LOGIN || 'pub@prestashop.com',
  PASSWD_CLIENT: process.env.CLIENT_PASSWD || '123456789',
  URL_BO: process.env.URL_BO || `http://localhost/prestashop/admin-dev/`,
  EMAIL: process.env.LOGIN || 'demo@prestashop.com',
  PASSWD: process.env.PASSWD || 'prestashop_demo',
};
global.BROWSER_CONFIG = {
  headless: JSON.parse(process.env.HEADLESS || false),
  timeout: 0,
  slowMo: 25,
  args: ['--start-maximized', '--no-sandbox', '--lang=en-GB'],
  defaultViewport: {
    width: 1680,
    height: 900,
  },
};
