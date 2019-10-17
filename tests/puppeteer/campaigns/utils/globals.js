global.FO = {
  URL: process.env.URL_FO || 'http://localhost/prestashop/',
};
global.BO = {
  URL: process.env.URL_BO || `${global.FO.URL}admin-dev/`,
  EMAIL: process.env.LOGIN || 'demo@prestashop.com',
  PASSWD: process.env.PASSWD || 'prestashop_demo',
};
global.INSTALL = {
  URL: process.env.URL_INSTALL || `${global.FO.URL}install-dev/`,
  LANGUAGE: process.env.INSTALL_LANGUAGE || 'en',
  COUNTRY: process.env.INSTALL_COUNTRY || 'fr',
  DB_USER: process.env.DB_USER || 'root',
  DB_PASSWD: process.env.DB_PASSWD || '',
  SHOPNAME: process.env.SHOPNAME || 'Prestashop',
  PS_VERSION: process.env.PS_VERSION || '1.7.6.0',
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
