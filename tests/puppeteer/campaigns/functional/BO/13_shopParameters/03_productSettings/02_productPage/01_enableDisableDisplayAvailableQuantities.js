require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_displayAvailableQuantitiesOnProductPage';
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
const ProductPage = require('@pages/FO/product');

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
    productPage: new ProductPage(page),
  };
};

/*
Disable display available quantities on product page
Check that quantity is not displayed
Enable display available quantities on product page
Check that quantity is displayed
 */
describe('Enable/Disable display available quantities on the product page', async () => {
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

  it('should disable display available quantities on the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableDisplayAvailableQuantities', baseContext);
    const result = await this.pageObjects.productSettingsPage.setDisplayAvailableQuantities(false);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that the product quantity is not displayed in product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkQuantityIsNotVisible', baseContext);
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    await this.pageObjects.homePage.goToProductPage(1);
    const quantityIsVisible = await this.pageObjects.productPage.isQuantityDisplayed();
    await expect(quantityIsVisible).to.be.false;
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should enable display available quantities on the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDisplayAvailableQuantities', baseContext);
    const result = await this.pageObjects.productSettingsPage.setDisplayAvailableQuantities(true);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that the product quantity is displayed in product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkQuantityIsNotVisible', baseContext);
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    await this.pageObjects.homePage.goToProductPage(1);
    const quantityIsVisible = await this.pageObjects.productPage.isQuantityDisplayed();
    await expect(quantityIsVisible).to.be.true;
    page = await this.pageObjects.homePage.closePage(browser, 1);
    this.pageObjects = await init();
  });
});
