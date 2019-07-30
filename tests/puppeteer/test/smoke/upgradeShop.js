// importing pages
const BO_LOGIN_PAGE = require('../../pages/BO/BO_login');
const BO_DASHBOARD_PAGE = require('../../pages/BO/BO_dashboard');
const BO_COMMON_PAGE = require('../../pages/BO/BO_commonPage');
const BO_MODULE_CATALOG_PAGE = require('../../pages/BO/BO_moduleCatalog');
const BO_MODULE_MANAGER_PAGE = require('../../pages/BO/BO_moduleManager');
const BO_SHOPPARAMETERS_GENERAL_PAGE = require('../../pages/BO/BO_shopParamsGeneral');
const BO_SHOPPARAMETERS_MAINTENANCE_PAGE = require('../../pages/BO/BO_shopParamsMaintenance');
const BO_AUTOUPGRADE_PAGE = require('../../pages/BO/BO_modules/BO_autoUpgrade');

let page;
let BO_LOGIN;
let BO_DASHBOARD;
let BO_COMMON;
let BO_MODULE_CATALOG;
let BO_MODULE_MANAGER;
let BO_SHOPPARAMETERS_GENERAL;
let BO_SHOPPARAMETERS_MAINTENANCE;
let BO_AUTOUPGRADE;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  BO_LOGIN = await (new BO_LOGIN_PAGE(page));
  BO_DASHBOARD = await (new BO_DASHBOARD_PAGE(page));
  BO_COMMON = await (new BO_COMMON_PAGE(page));
  BO_MODULE_CATALOG = await (new BO_MODULE_CATALOG_PAGE(page));
  BO_MODULE_MANAGER = await (new BO_MODULE_MANAGER_PAGE(page));
  BO_SHOPPARAMETERS_GENERAL = await (new BO_SHOPPARAMETERS_GENERAL_PAGE(page));
  BO_SHOPPARAMETERS_MAINTENANCE = await (new BO_SHOPPARAMETERS_MAINTENANCE_PAGE(page));
  BO_AUTOUPGRADE = await (new BO_AUTOUPGRADE_PAGE(page));
};

// test scenario
global.scenario('Upgrade Prestashop to last Stable', async () => {
  test('should login into BO', async () => {
    await BO_LOGIN.goTo(global.URL_BO);
    await BO_LOGIN.login(global.EMAIL, global.PASSWD);
    const pageTitle = await BO_DASHBOARD.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_DASHBOARD.pageTitle);
    await BO_COMMON.closeOnboardingModal();
  });

  test('should go the Module Catalog page', async () => {
    await BO_COMMON.goToSubMenu(BO_COMMON.modulesParentLink, BO_COMMON.moduleCatalogueLink);
    const pageTitle = await BO_MODULE_CATALOG.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_MODULE_CATALOG.pageTitle);
  });

  test('should Install module \'1-Click Upgrade\'', async () => {
    await BO_MODULE_CATALOG.searchModule('autoupgrade', '1-Click Upgrade');
    const installResultMessage = await BO_MODULE_CATALOG.installModule('1-Click Upgrade');
    await global.expect(installResultMessage)
      .to.equal(BO_MODULE_CATALOG.installMessageSuccessful.replace('%MODULETAG', 'autoupgrade'));
  });

  test('should disable Shop', async () => {
    await BO_COMMON.goToSubMenu(BO_COMMON.shopParametersParentLink, BO_COMMON.shopParametersGeneralLink);
    let pageTitle = await BO_SHOPPARAMETERS_GENERAL.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_SHOPPARAMETERS_GENERAL.pageTitle);
    await BO_SHOPPARAMETERS_GENERAL.goToSubTabMaintenance();
    pageTitle = await BO_SHOPPARAMETERS_MAINTENANCE.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_SHOPPARAMETERS_MAINTENANCE.pageTitle);
    await BO_SHOPPARAMETERS_MAINTENANCE.changeShopStatus(false);
  });

  test('should Go to configuration module Page', async () => {
    await BO_COMMON.goToSubMenu(BO_COMMON.modulesParentLink, BO_COMMON.moduleManagerLink);
    let pageTitle = await BO_MODULE_MANAGER.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_MODULE_MANAGER.pageTitle);
    await BO_MODULE_MANAGER.searchModule('autoupgrade', '1-Click Upgrade');
    await BO_MODULE_MANAGER.goToConfigurationPage('1-Click Upgrade');
    pageTitle = await BO_AUTOUPGRADE.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_AUTOUPGRADE.pageTitle);
  });

  test('should upgrade Prestashop', async () => {
    await BO_AUTOUPGRADE.upgradePrestashop('major');
  });

  test('should reload and check that user was automatically logged out', async () => {
    await BO_COMMON.reloadPage();
    const pageTitle = await BO_LOGIN.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_LOGIN.pageTitle);
  });

  test('should login and verify version in BO', async () => {
    await BO_LOGIN.goTo(global.URL_BO);
    await BO_LOGIN.login(global.EMAIL, global.PASSWD);
    const pageTitle = await BO_DASHBOARD.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_DASHBOARD.pageTitle);
    const version = await BO_COMMON.getTextContent(BO_COMMON.shopVersionBloc);
    global.expect(version).to.be.equal('1.7.6.0');
  });

  test('should enable Shop', async () => {
    await BO_COMMON.goToSubMenu(BO_COMMON.shopParametersParentLink, BO_COMMON.shopParametersGeneralLink);
    let pageTitle = await BO_SHOPPARAMETERS_GENERAL.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_SHOPPARAMETERS_GENERAL.pageTitle);
    await BO_SHOPPARAMETERS_GENERAL.goToSubTabMaintenance();
    pageTitle = await BO_SHOPPARAMETERS_MAINTENANCE.getPageTitle();
    await global.expect(pageTitle).to.contains(BO_SHOPPARAMETERS_MAINTENANCE.pageTitle);
    await BO_SHOPPARAMETERS_MAINTENANCE.changeShopStatus(true);
  });

  test('should verify FO', async () => {
    page = await BO_COMMON.viewMyShop();
    await page.waitForSelector('#_desktop_user_info', {visible: true});
  });
}, init, true);
