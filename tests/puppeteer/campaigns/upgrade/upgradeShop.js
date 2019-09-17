const {expect} = require('chai');
const helper = require('../../utils/helpers');
// Using chai
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

let browser;
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
const init = async function () {
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
describe('Upgrade Prestashop to last Stable', async () => {
  before(async () => {
    browser = await helper.createBrowser();
    page = await browser.newPage();
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'en-GB',
    });
    await init();
  });
  after(async () => {
    await browser.close();
  });
  it('should login into BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await bOBasePage.closeOnboardingModal();
  });

  it('should go the Module Catalog page', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.modulesParentLink, bOBasePage.moduleCatalogueLink);
    const pageTitle = await moduleCatalogPage.getPageTitle();
    await expect(pageTitle).to.contains(moduleCatalogPage.pageTitle);
  });

  it('should Install module \'1-Click Upgrade\'', async () => {
    await moduleCatalogPage.searchModule('autoupgrade', '1-Click Upgrade');
    const installResultMessage = await moduleCatalogPage.installModule('1-Click Upgrade');
    await expect(installResultMessage)
      .to.equal(moduleCatalogPage.installMessageSuccessful.replace('%MODULETAG', 'autoupgrade'));
  });

  it('should disable Shop', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.shopParametersParentLink, bOBasePage.shopParametersGeneralLink);
    let pageTitle = await shopParamsGeneralPage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsGeneralPage.pageTitle);
    await shopParamsGeneralPage.goToSubTabMaintenance();
    pageTitle = await shopParamsMaintenancePage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsMaintenancePage.pageTitle);
    await shopParamsMaintenancePage.changeShopStatus(false);
  });

  it('should Go to configuration module Page', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.modulesParentLink, bOBasePage.moduleManagerLink);
    let pageTitle = await moduleManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    await moduleManagerPage.searchModule('autoupgrade', '1-Click Upgrade');
    await moduleManagerPage.goToConfigurationPage('1-Click Upgrade');
    pageTitle = await autoUpgradePage.getPageTitle();
    await expect(pageTitle).to.contains(autoUpgradePage.pageTitle);
  });

  it('should upgrade Prestashop', async () => {
    await autoUpgradePage.upgradePrestashop('major');
  });

  it('should reload and check that user was automatically logged out', async () => {
    await bOBasePage.reloadPage();
    const pageTitle = await loginPage.getPageTitle();
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  });

  it('should login and verify version in BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    const version = await bOBasePage.getTextContent(bOBasePage.shopVersionBloc);
    expect(version).to.be.equal(global.PS_VERSION);
  });

  it('should enable Shop', async () => {
    await bOBasePage.goToSubMenu(bOBasePage.shopParametersParentLink, bOBasePage.shopParametersGeneralLink);
    let pageTitle = await shopParamsGeneralPage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsGeneralPage.pageTitle);
    await shopParamsGeneralPage.goToSubTabMaintenance();
    pageTitle = await shopParamsMaintenancePage.getPageTitle();
    await expect(pageTitle).to.contains(shopParamsMaintenancePage.pageTitle);
    await shopParamsMaintenancePage.changeShopStatus(true);
  });

  it('should verify FO', async () => {
    page = await bOBasePage.viewMyShop();
    await page.waitForSelector(homePage.userInfoLink, {visible: true});
    await page.waitForSelector(homePage.contactLink, {visible: true});
  });
});
