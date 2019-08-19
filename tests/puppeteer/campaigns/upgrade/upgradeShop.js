// Using chai
const {expect} = require('chai');
// importing pages
const LoginPage = require('../../pages/BO/login');
const DashboardPage = require('../../pages/BO/dashboard');
const BOBasePage = require('../../pages/BO/BObasePage');
const ModuleCatalogPage = require('../../pages/BO/moduleCatalog');
const ModuleManagerPage = require('../../pages/BO/moduleManager');
const ShopParamsGeneralPage = require('../../pages/BO/shopParamsGeneral');
const ShopParamsMaintenancePage = require('../../pages/BO/shopParamsMaintenance');
const AutoUpgradePage = require('../../pages/BO/modulesPages/autoUpgrade');
const HomePage = require('../../pages/FO/home');

let page;
let loginPage;
let dashboardPage;
let bOBasePage;
let moduleCatalogPage;
let moduleManagerPage;
let shopParamsGeneralPage;
let shopParamsMaintenancePage;
let autoUpgradePage;
let homePage;


// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  bOBasePage = await (new BOBasePage(page));
  moduleCatalogPage = await (new ModuleCatalogPage(page));
  moduleManagerPage = await (new ModuleManagerPage(page));
  shopParamsGeneralPage = await (new ShopParamsGeneralPage(page));
  shopParamsMaintenancePage = await (new ShopParamsMaintenancePage(page));
  autoUpgradePage = await (new AutoUpgradePage(page));
  homePage = await (new HomePage(page));
};

// Upgrade shop from a version to the last stable one
global.scenario('Upgrade Prestashop to last Stable', async () => {
  test('should login into BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await bOBasePage.closeOnboardingModal();
  });

  test('should go the Module Catalog page', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.modulesParentLink, bOBasePage.moduleCatalogueLink);
    const pageTitle = await moduleCatalogPage.getPageTitle();
    await expect(pageTitle).to.contains(moduleCatalogPage.pageTitle);
  });

  test('should Install module \'1-Click Upgrade\'', async () => {
    await moduleCatalogPage.searchModule('autoupgrade', '1-Click Upgrade');
    const installResultMessage = await moduleCatalogPage.installModule('1-Click Upgrade');
    await expect(installResultMessage)
      .to.equal(moduleCatalogPage.installMessageSuccessful.replace('%MODULETAG', 'autoupgrade'));
  });

  test('should disable Shop', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.shopParametersParentLink, bOBasePage.shopParametersGeneralLink);
    let pageTitle = await shopParamsGeneralPage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsGeneralPage.pageTitle);
    await shopParamsGeneralPage.goToSubTabMaintenance();
    pageTitle = await shopParamsMaintenancePage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsMaintenancePage.pageTitle);
    await shopParamsMaintenancePage.changeShopStatus(false);
  });

  test('should Go to configuration module Page', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.modulesParentLink, bOBasePage.moduleManagerLink);
    let pageTitle = await moduleManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    await moduleManagerPage.searchModule('autoupgrade', '1-Click Upgrade');
    await moduleManagerPage.goToConfigurationPage('1-Click Upgrade');
    pageTitle = await autoUpgradePage.getPageTitle();
    await expect(pageTitle).to.contains(autoUpgradePage.pageTitle);
  });

  test('should upgrade Prestashop', async () => {
    await autoUpgradePage.upgradePrestashop('major');
  });

  test('should reload and check that user was automatically logged out', async () => {
    await bOBasePage.reloadPage();
    const pageTitle = await loginPage.getPageTitle();
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  });

  test('should login and verify version in BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    const version = await bOBasePage.getTextContent(bOBasePage.shopVersionBloc);
    expect(version).to.be.equal('1.7.6.0');
  });

  test('should enable Shop', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.shopParametersParentLink, bOBasePage.shopParametersGeneralLink);
    let pageTitle = await shopParamsGeneralPage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsGeneralPage.pageTitle);
    await shopParamsGeneralPage.goToSubTabMaintenance();
    pageTitle = await shopParamsMaintenancePage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsMaintenancePage.pageTitle);
    await shopParamsMaintenancePage.changeShopStatus(true);
  });

  test('should verify FO', async () => {
    page = await bOBasePage.viewMyShop();
    await page.waitForSelector(homePage.userInfoLink, {visible: true});
    await page.waitForSelector(homePage.contactLink, {visible: true});
  });
}, init, true);
