require('module-alias/register');
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
const FOBasePage = require('@pages/FO/FObasePage');
const ProductPage = require('@pages/FO/product');
// Importing data
const ProductData = require('@data/FO/product');

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
    foBasePage: new FOBasePage(page),
    productPage: new ProductPage(page),
  };
};

/*
Enable catalog mode / Disable show prices
Check catalog page
Enable show prices
Check catalog page
Disable catalog mode
 */
describe('Enable/Disable catalog mode', async () => {
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
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.productSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  it('should enable catalog mode', async function () {
    const result = await this.pageObjects.productSettingsPage.changeCatalogModeStatus(true);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should disable show prices', async function () {
    const result = await this.pageObjects.productSettingsPage.changeShowPricesStatus(false);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that there is no prices in the home page', async function () {
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    await this.pageObjects.foBasePage.changeLanguage('en');
    const isPriceVisible = await this.pageObjects.homePage.isPriceVisible(1);
    await expect(isPriceVisible).to.be.false;
  });

  it('should go to the first product page', async function () {
    await this.pageObjects.homePage.goToProductPage(1);
    const pageTitle = await this.pageObjects.productPage.getPageTitle();
    await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
  });

  it('should check that there is no price and no add to cart button', async function () {
    const isPriceVisible = await this.pageObjects.productPage.elementVisible(this.pageObjects.productPage.productPrice);
    await expect(isPriceVisible).to.be.false;
    const isVisible = await this.pageObjects.productPage.elementVisible(this.pageObjects.productPage.addToCartButton);
    await expect(isVisible).to.be.false;
    page = await this.pageObjects.productPage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should enable show prices', async function () {
    const result = await this.pageObjects.productSettingsPage.changeShowPricesStatus(true);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that the product price is displayed in the home page', async function () {
    page = await this.pageObjects.boBasePage.viewMyShop();
    this.pageObjects = await init();
    await this.pageObjects.foBasePage.changeLanguage('en');
    const isPriceExist = await this.pageObjects.homePage.isPriceVisible(1);
    await expect(isPriceExist).to.be.true;
  });

  it('should go to the first product page', async function () {
    await this.pageObjects.homePage.goToProductPage(1);
    const pageTitle = await this.pageObjects.productPage.getPageTitle();
    await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
  });

  it('should check that the price is displayed and add to cart button is not displayed', async function () {
    let isVisible = await this.pageObjects.productPage.elementVisible(
      this.pageObjects.productPage.productPrice,
      1000,
    );
    await expect(isVisible).to.be.true;
    isVisible = await this.pageObjects.productPage.elementVisible(this.pageObjects.productPage.addToCartButton);
    await expect(isVisible).to.be.false;
    page = await this.pageObjects.productPage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should enable catalog mode', async function () {
    const result = await this.pageObjects.productSettingsPage.changeCatalogModeStatus(false);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });
});
