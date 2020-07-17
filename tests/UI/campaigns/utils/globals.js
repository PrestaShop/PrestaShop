/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
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
};

global.INSTALL = {
  URL: process.env.URL_INSTALL || `${global.FO.URL}install-dev/`,
  LANGUAGE: process.env.INSTALL_LANGUAGE || 'en',
  COUNTRY: process.env.INSTALL_COUNTRY || 'fr',
  DB_NAME: process.env.DB_NAME || 'prestashopdb',
  DB_USER: process.env.DB_USER || 'root',
  DB_PASSWD: process.env.DB_PASSWD || '',
  SHOPNAME: process.env.SHOPNAME || 'Prestashop',
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
    slowMo: parseInt(process.env.SLOWMO, 10) || 5,
  },
};
