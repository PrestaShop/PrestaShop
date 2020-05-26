const {DefaultAccount} = require('@data/demo/employees');

global.FO = {
  URL: process.env.URL_FO || 'http://localhost/prestashop/',
};
global.BO = {
  URL: process.env.URL_BO || `${global.FO.URL}admin-dev/`,
  EMAIL: process.env.LOGIN || DefaultAccount.email,
  PASSWD: process.env.PASSWD || DefaultAccount.password,
  FIRSTNAME: process.env.FIRSTNAME || DefaultAccount.firstName,
  LASTNAME: process.env.LASTNAME || DefaultAccount.lastName,
  DOWNLOAD_PATH: process.env.DOWNLOAD_PATH || '/downloads',
};
global.INSTALL = {
  URL: process.env.URL_INSTALL || `${global.FO.URL}install-dev/`,
  LANGUAGE: process.env.INSTALL_LANGUAGE || 'en',
  COUNTRY: process.env.INSTALL_COUNTRY || 'fr',
  DB_NAME: process.env.DB_NAME || 'ps_develop',
  DB_USER: process.env.DB_USER || 'demops',
  DB_PASSWD: process.env.DB_PASSWD || 'bribri',
  SHOPNAME: process.env.SHOPNAME || 'Prestashop',
  PS_VERSION: process.env.PS_VERSION || '1.7.6.0',
};
global.BROWSER_CONFIG = {
  headless: JSON.parse(process.env.HEADLESS || true),
  timeout: 0,
  slowMo: parseInt(process.env.SLOWMO, 10) || 5,
  args: ['--window-size=1680,900', '--start-maximized', '--no-sandbox', '--lang=en-GB'],
};
global.BROWSER = process.env.BROWSER || 'chromium';
