'use strict';
var common = require('./common.webdriverio.js');
var path = require('path');
var should = require('should');
var argv = require('minimist')(process.argv.slice(2));
var pdfUtil = require('pdf-to-text');

global.date_time = new Date().getTime();
global.URL = argv.URL;

global.db_server = argv.DB_SERVER || "mysql";
global.db_name = argv.DB_NAME || "prestashop";
global.db_user = argv.DB_USER || "root";
global.db_passwd = argv.DB_PASSWD || "doge";
global.db_empty_password = !!argv.DB_EMPTY_PASSWD; // Cast as boolean
global.module_tech_name = argv.MODULE;
global.saucelabs = argv.SAUCELABS;
global.selenium_url = argv.SELENIUM;
global._projectdir = path.join(__dirname, '..', '..');
global.new_customer_email = 'pub' + date_time + '@prestashop.com';
global.categoryImage = path.join(__dirname, '', 'datas', 'category_image.png');
global.categoryThumb = path.join(__dirname, '', 'datas', 'category_miniature.png');
global.brandsImage = path.join(__dirname, '', 'datas', 'prestashop.png');

/** @todo I have to delete this initial code before **/
global.downloadsFolderPath = argv.DIR  || "/home/fourat.achour/Téléchargements/";
global.rcLink = argv.RCLINK || "build.prestashop.com/news/prestashop-1-7-3-0-beta-1/";
global.rcTarget = argv.RCTARGET || "/projet/prestashop_1.7.2.4/admin-dev/autoupgrade/download";
global.filename = argv.FILENAME || "prestashop_1.7.3.0_beta1.zip";

global.onboarding = false;
global.invoiceFileName = "";
global.basic_price = "";

module.exports = {
  selector: require('./selectors'),
  shouldExist: function (err, existing) {
    should(err).be.not.defined;
    should(existing).be.true;
  }
};
