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
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParams_productSetting_catalogMode';

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.productSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {actionCatalogMode: 'enable', enable: true}},
    {args: {actionCatalogMode: 'disable', enable: false}},
  ];
  tests.forEach((test) => {
    it(`should ${test.args.actionCatalogMode} catalog mode`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.actionCatalogMode}CatalogMode`,
        baseContext,
      );
      const result = await this.pageObjects.productSettingsPage.changeCatalogModeStatus(test.args.enable);
      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    if (test.args.actionCatalogMode === 'enable') {
      const testShowPrices = [
        {
          args:
            {
              action: 'disable', enable: false, isPriceExist: false, isAddToCartExist: false,
            },
        },
        {
          args:
            {
              action: 'enable', enable: true, isPriceExist: true, isAddToCartExist: false,
            },
        },
      ];
      testShowPrices.forEach((showPrices, index) => {
        it(`should ${showPrices.args.action} show prices`, async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${showPrices.args.action}ShowPrices`,
            baseContext,
          );
          const result = await this.pageObjects.productSettingsPage.setShowPricesStatus(showPrices.args.enable);
          await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
        });

        it('should check product prices in the home page', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkPricesInHomePage${this.pageObjects.boBasePage.uppercaseFirstCharacter(showPrices.args.action)}`,
            baseContext,
          );
          page = await this.pageObjects.boBasePage.viewMyShop();
          this.pageObjects = await init();
          await this.pageObjects.foBasePage.changeLanguage('en');
          const isPriceVisible = await this.pageObjects.homePage.isPriceVisible(1);
          await expect(isPriceVisible).to.equal(showPrices.args.enable);
        });

        it('should go to the first product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);
          await this.pageObjects.homePage.goToProductPage(1);
          const pageTitle = await this.pageObjects.productPage.getPageTitle();
          await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
        });

        it('should check the existence of product price and add to cart button', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkPrice&AddToCartButton${this.pageObjects.boBasePage.uppercaseFirstCharacter(showPrices.args.action)}`,
            baseContext,
          );
          let isVisible = await this.pageObjects.productPage.isPriceDisplayed();
          await expect(isVisible).to.equal(showPrices.args.isPriceExist);
          isVisible = await this.pageObjects.productPage.isAddToCartButtonDisplayed();
          await expect(isVisible).to.equal(showPrices.args.isAddToCartExist);
          page = await this.pageObjects.productPage.closePage(browser, 1);
          this.pageObjects = await init();
        });
      });
    } else {
      it('should check product prices in the home page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'checkPricesInHomePageVisible',
          baseContext,
        );
        page = await this.pageObjects.boBasePage.viewMyShop();
        this.pageObjects = await init();
        await this.pageObjects.foBasePage.changeLanguage('en');
        const isPriceVisible = await this.pageObjects.homePage.isPriceVisible(1);
        await expect(isPriceVisible).to.be.true;
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage3', baseContext);
        await this.pageObjects.homePage.goToProductPage(1);
        const pageTitle = await this.pageObjects.productPage.getPageTitle();
        await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
      });

      it('should check the existence of product price and add to cart button', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'checkPrice&AddToCartButtonVisible',
          baseContext,
        );
        let isVisible = await this.pageObjects.productPage.isPriceDisplayed();
        await expect(isVisible).to.be.true;
        isVisible = await this.pageObjects.productPage.isAddToCartButtonDisplayed();
        await expect(isVisible).to.be.true;
        page = await this.pageObjects.productPage.closePage(browser, 1);
        this.pageObjects = await init();
      });
    }
  });
});
