require('dotenv').config();
const {DefaultEmployee} = require('@data/demo/employees');

global.FO = {
  URL: process.env.URL_FO || 'http://localhost/prestashop/',
};

global.BO = {
  URL: process.env.URL_BO || `${global.FO.URL}admin-dev/`,
  EMAIL: process.env.LOGIN || DefaultEmployee.email,
  PASSWD: process.env.PASSWD || DefaultEmployee.password,
  FIRSTNAME: process.env.FIRSTNAME || DefaultEmployee.firstName,
  LASTNAME: process.env.LASTNAME || DefaultEmployee.lastName,
};

global.INSTALL = {
  URL: process.env.URL_INSTALL || `${global.FO.URL}install-dev/`,
  LANGUAGE: process.env.INSTALL_LANGUAGE || 'en',
  COUNTRY: process.env.INSTALL_COUNTRY || 'fr',
  DB_SERVER: process.env.DB_SERVER || '127.0.0.1',
  DB_NAME: process.env.DB_NAME || 'prestashopdb',
  DB_USER: process.env.DB_USER || 'root',
  DB_PASSWD: process.env.DB_PASSWD || '',
  DB_PREFIX: process.env.DB_PREFIX || 'tst_',
  SHOP_NAME: process.env.SHOP_NAME || 'PrestaShop',
  PS_VERSION: process.env.PS_VERSION || '1.7.6.0',
};

global.BROWSER = {
  name: process.env.BROWSER || 'chromium',
  lang: 'en-GB',
  width: 1680,
  height: 900,
  sandboxArgs: ['--no-sandbox', '--disable-setuid-sandbox'],
  acceptDownloads: true,
  config: {
    headless: JSON.parse(process.env.HEADLESS || true),
    timeout: 0,
    slowMo: parseInt(process.env.SLOW_MO, 10) || 5,
  },
  interceptErrors: JSON.parse(process.env.INTERCEPT_ERRORS || false),
};

global.GENERATE_FAILED_STEPS = JSON.parse(process.env.GENERATE_FAILED_STEPS || false);

global.TAKE_SCREENSHOT_AFTER_FAIL = JSON.parse(process.env.TAKE_SCREENSHOT_AFTER_FAIL || false);

global.maildevConfig = {
  smtpPort: process.env.SMTP_PORT || '1025',
  smtpServer: process.env.SMTP_SERVER || '172.20.0.4',
};
