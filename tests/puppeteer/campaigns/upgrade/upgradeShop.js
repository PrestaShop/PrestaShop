require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const ModuleCatalogPage = require('@pages/BO/moduleCatalog');
const ModuleManagerPage = require('@pages/BO/moduleManager');
const ShopParamsGeneralPage = require('@pages/BO/shopParamsGeneral');
const ShopParamsMaintenancePage = require('@pages/BO/shopParamsMaintenance');
const AutoUpgradePage = require('@pages/BO/modulesPages/autoUpgrade');
const HomePage = require('@pages/FO/home');
const loginCommon = require('@commonTests/loginBO');

let browser;
let page;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    moduleCatalogPage: new ModuleCatalogPage(page),
    moduleManagerPage: new ModuleManagerPage(page),
    shopParamsGeneralPage: new ShopParamsGeneralPage(page),
    shopParamsMaintenancePage: new ShopParamsMaintenancePage(page),
    autoUpgradePage: new AutoUpgradePage(page),
    homePage: new HomePage(page),
  };
};

// Upgrade shop from a version to the last stable one
describe('Upgrade Prestashop to last Stable', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'en-GB',
    });
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  loginCommon.loginBO();
  it('should go the Module Catalog page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.modulesParentLink,
      this.pageObjects.boBasePage.moduleCatalogueLink);
    const pageTitle = await this.pageObjects.moduleCatalogPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.moduleCatalogPage.pageTitle);
  });

  it('should Install module \'1-Click Upgrade\'', async function () {
    await this.pageObjects.moduleCatalogPage.searchModule('autoupgrade', '1-Click Upgrade');
    const installResultMessage = await this.pageObjects.moduleCatalogPage.installModule('1-Click Upgrade');
    await expect(installResultMessage)
      .to.equal(this.pageObjects.moduleCatalogPage.installMessageSuccessful.replace('%MODULETAG', 'autoupgrade'));
  });

  it('should disable Shop', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.shopParametersGeneralLink);
    let pageTitle = await this.pageObjects.shopParamsGeneralPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.shopParamsGeneralPage.pageTitle);
    await this.pageObjects.shopParamsGeneralPage.goToSubTabMaintenance();
    pageTitle = await this.pageObjects.shopParamsMaintenancePage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.shopParamsMaintenancePage.pageTitle);
    await this.pageObjects.shopParamsMaintenancePage.changeShopStatus(false);
  });

  it('should Go to configuration module Page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.modulesParentLink,
      this.pageObjects.boBasePage.moduleManagerLink);
    let pageTitle = await this.pageObjects.moduleManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.moduleManagerPage.pageTitle);
    await this.pageObjects.moduleManagerPage.searchModule('autoupgrade', '1-Click Upgrade');
    await this.pageObjects.moduleManagerPage.goToConfigurationPage('1-Click Upgrade');
    pageTitle = await this.pageObjects.autoUpgradePage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.autoUpgradePage.pageTitle);
  });

  it('should upgrade Prestashop', async function () {
    await this.pageObjects.autoUpgradePage.upgradePrestashop('major');
    await expect(this.actualStepsDoneForUpgradeTable).to.include
      .members(this.pageObjects.autoUpgradePage.expectedStepsDoneForUpgradeTable);
  });

  it('should reload and check that user was automatically logged out', async function () {
    await this.pageObjects.boBasePage.reloadPage();
    const pageTitle = await this.pageObjects.loginPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.loginPage.pageTitle);
  });

  it('should login and verify version in BO', async function () {
    await this.pageObjects.loginPage.goTo(global.BO.URL);
    await this.pageObjects.loginPage.login(global.BO.EMAIL, global.BO.PASSWD);
    const pageTitle = await this.pageObjects.dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.dashboardPage.pageTitle);
    const version = await this.pageObjects.boBasePage.getTextContent(this.pageObjects.boBasePage.shopVersionBloc);
    expect(version).to.be.equal(global.INSTALL.PS_VERSION);
  });

  it('should enable Shop', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.shopParametersGeneralLink);
    let pageTitle = await this.pageObjects.shopParamsGeneralPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.shopParamsGeneralPage.pageTitle);
    await this.pageObjects.shopParamsGeneralPage.goToSubTabMaintenance();
    pageTitle = await this.pageObjects.shopParamsMaintenancePage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.shopParamsMaintenancePage.pageTitle);
    await this.pageObjects.shopParamsMaintenancePage.changeShopStatus(true);
  });

  it('should verify FO', async function () {
    page = await this.pageObjects.boBasePage.viewMyShop();
    await page.waitForSelector(this.pageObjects.homePage.userInfoLink, {visible: true});
    await page.waitForSelector(this.pageObjects.homePage.contactLink, {visible: true});
  });
});
