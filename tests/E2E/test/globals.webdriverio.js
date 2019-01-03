/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
'use strict';
var common = require('./common.webdriverio.js');
var path = require('path');
var should = require('should');
var argv = require('minimist')(process.argv.slice(2));
var pdfUtil = require('pdf-to-text');

global.date_time = new Date().getTime();

let siteUrl = argv.URL || 'http://localhost'
if (!/^https?:\/\//i.test(siteUrl)) {
  siteUrl = 'http://' + siteUrl;
}
global.URL = siteUrl;

global.install_language = argv.LANGUAGE || 'en';
global.install_country = argv.COUNTRY || 'france';
global.db_server = argv.DB_SERVER || 'lolcahost';
global.db_user = argv.DB_USER || 'root';
global.db_passwd = argv.DB_PASSWD || 'doge';
global.db_empty_password = !!argv.DB_EMPTY_PASSWD; //Cast as boolean
global.selenium_protocol = argv.SELENIUM_PROTOCOL;
global.selenium_host = argv.SELENIUM_HOST;
global.selenium_port = argv.SELENIUM_PORT;
global.module_tech_name = argv.MODULE || 'ps_legalcompliance';
global.test_addons = !argv.TEST_ADDONS; //Cast as boolean
global.install_shop = !!argv.INSTALL;
global.downloadsFolderPath = argv.DIR;                   // Download directory
global.UrlLastStableVersion = argv.URLLASTSTABLEVERSION; // URL of last stable version of prestashop

global.rcLink = argv.RCLINK || ''; // Link for download The RC
global.rcTarget = argv.RCTARGET; // Last stable version location directory
global.filename = argv.FILENAME || ''; // RC file name

global.headless = argv.HEADLESS || false;

global.adminEmail = argv.ADMIN_EMAIL || 'demo@prestashop.com';
global.adminPassword = argv.ADMIN_PASSWORD || 'prestashop_demo';

global._projectdir = path.join(__dirname, '..', '..');
global.debugFile = argv.DEBUG_FILE || _projectdir + '/../config/defines.inc.php';
global.new_customer_email = 'pub' + date_time + '@prestashop.com';
global.categoryImage = path.join(__dirname, '', 'datas', 'category_image.png');
global.categoryThumb = path.join(__dirname, '', 'datas', 'category_miniature.png');
global.brandsImage = path.join(__dirname, '', 'datas', 'prestashop.png');

global.onboarding = false;
global.invoiceFileName = '';
global.basic_price = '';
global.indexText = 0;
global.categoryID = '';
module.exports = {
  selector: require('./selectors'),
  shouldExist: function (err, existing) {
    should(err).be.not.defined;
    should(existing).be.true;
  }
};
