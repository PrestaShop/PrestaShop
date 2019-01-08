'use strict';
let argv = require('minimist')(process.argv.slice(2));

global.dateTime = new Date().getTime();
global.firstName = 'Demo';
global.lastName = 'Prestashop';
global.email = argv.LOGIN || 'demo@prestashop.com';
global.password = argv.PASSWD || 'prestashop_demo';

global.rcTarget = argv.RC_TARGET || '';
global.rcLink = argv.UrlStableVersion || 'https://download.prestashop.com/download/releases/prestashop_1.7.4.2.zip';
global.prestashopFolderName = 'prestashop';
global.URL = argv.URL || 'http://127.0.0.1:8081/prestashop';
global.language = argv.LANG || 'en';
global.country = argv.COUNTRY || 'france';
global.dbServer = argv.DB_SERVER || '10.1.37.2';
global.dbUser = argv.DB_USER || 'root';
global.dbPassword = argv.DB_PASSWD || 'sifast2016';

global.downloadFileFolder = './test/campaigns/files/generated_files/downloads/';
global.customerEmail = 'pub@prestashop.com';
global.customerPassword = '123456789';
global.dataFileFolder = './test/mocha/datas/';

global.installFolderName = argv.INSTALL_FOLDER_NAME || '/install-dev';
global.adminFolderName = argv.ADMIN_FOLDER_NAME || '/admin-dev';

global.test_addons = !argv.TEST_ADDONS; //Cast as boolean
global.module_tech_name = argv.MODULE || 'ps_legalcompliance';
