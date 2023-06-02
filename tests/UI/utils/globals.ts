import Employees from '@data/demo/employees';
import {config} from 'dotenv';
import path from 'path';

config();

global.FO = {
  URL: process.env.URL_FO || 'http://localhost/prestashop/',
};

/*
Linked to the issue #22581
 */
global.URLHasPort = (global.FO.URL).match(/:\d+.+/) !== null;

global.BO = {
  URL: process.env.URL_BO || `${global.FO.URL}admin-dev/`,
  EMAIL: process.env.LOGIN || Employees.DefaultEmployee.email,
  PASSWD: process.env.PASSWD || Employees.DefaultEmployee.password,
  FIRSTNAME: process.env.FIRSTNAME || Employees.DefaultEmployee.firstName,
  LASTNAME: process.env.LASTNAME || Employees.DefaultEmployee.lastName,
};

global.PSConfig = {
  parametersFile: process.env.PS_PARAMETERS_FILE || path.resolve(__dirname, '../../../', 'app/config/parameters.php'),
};

global.INSTALL = {
  URL: process.env.URL_INSTALL || `${global.FO.URL}install-dev/`,
  LANGUAGE: process.env.INSTALL_LANGUAGE || 'en',
  COUNTRY: process.env.INSTALL_COUNTRY || 'France',
  ENABLE_SSL: process.env.ENABLE_SSL === 'true',
  DB_SERVER: process.env.DB_SERVER || '127.0.0.1',
  DB_NAME: process.env.DB_NAME || 'prestashopdb',
  DB_USER: process.env.DB_USER || 'root',
  DB_PASSWD: process.env.DB_PASSWD || '',
  DB_PREFIX: process.env.DB_PREFIX || 'tst_',
  SHOP_NAME: process.env.SHOP_NAME || 'PrestaShop',
};

global.BROWSER = {
  name: process.env.BROWSER || 'chromium',
  lang: process.env.BROWSER_LANG || 'en-GB',
  width: process.env.BROWSER_WIDTH ? parseInt(process.env.BROWSER_WIDTH, 10) : 1680,
  height: process.env.BROWSER_HEIGHT ? parseInt(process.env.BROWSER_HEIGHT, 10) : 900,
  sandboxArgs: ['--no-sandbox', '--disable-setuid-sandbox'],
  acceptDownloads: true,
  config: {
    headless: process.env.HEADLESS ? JSON.parse(process.env.HEADLESS) : true,
    timeout: 0,
    slowMo: process.env.SLOW_MO ? parseInt(process.env.SLOW_MO, 10) : 5,
  },
  interceptErrors: process.env.INTERCEPT_ERRORS ? JSON.parse(process.env.INTERCEPT_ERRORS) : false,
};

global.GENERATE_FAILED_STEPS = process.env.GENERATE_FAILED_STEPS ? JSON.parse(process.env.GENERATE_FAILED_STEPS) : false;

global.SCREENSHOT = {
  FOLDER: process.env.SCREENSHOT_FOLDER || './screenshots',
  AFTER_FAIL: process.env.TAKE_SCREENSHOT_AFTER_FAIL ? JSON.parse(process.env.TAKE_SCREENSHOT_AFTER_FAIL) : false,
};

global.maildevConfig = {
  smtpPort: parseInt(process.env.SMTP_PORT ?? '1025', 10),
  smtpServer: process.env.SMTP_SERVER || 'localhost',
  silent: true,
};

global.keycloakConfig = {
  keycloakExternalUrl: process.env.KEYCLOAK_URL_EXTERNAL || 'http://127.0.0.1:8003',
  keycloakInternalUrl: process.env.KEYCLOAK_URL_INTERNAL || 'http://keycloak:8080',
  keycloakAdminUser: process.env.KEYCLOAK_ADMIN_USER || 'admin',
  keycloakAdminPass: process.env.KEYCLOAK_ADMIN_PASS || 'admin',
  keycloakClientId: process.env.KEYCLOAK_CLIENT_ID || 'KEYCLOAK_CLIENT_ID',
};
