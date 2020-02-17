require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_newDaysNumber';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const HomePage = require('@pages/FO/home');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    homePage: new HomePage(page),
  };
};

/*
Update new days number to 0
Check that there is no new products in FO
Go back to the default value
Check that all products are new in FO
 */
describe('Test new days number', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to product settings page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.productSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  it('should update Number of days to 0', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateNumberOfDays', baseContext);
    const result = await this.pageObjects.productSettingsPage.updateNumberOfDays(0);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that there is no new flag in the product miniature in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstCheckOfNewFlagInFO', baseContext);
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    const isNewFlagVisible = await this.pageObjects.homePage.isNewFlagVisible(1);
    await expect(isNewFlagVisible).to.be.false;
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should go back to the default Number of days value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToDefaultNumberOfDays', baseContext);
    const result = await this.pageObjects.productSettingsPage.updateNumberOfDays(20);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that there is a new flag in the product miniature in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'secondCheckOfNewFlagInFO', baseContext);
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    const isNewFlagVisible = await this.pageObjects.homePage.isNewFlagVisible(1);
    await expect(isNewFlagVisible).to.be.true;
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });
});
