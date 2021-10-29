require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');

// Import FO pages
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');

// Import data
const ProductData = require('@data/FO/product');

const baseContext = 'functional_BO_shopParameters_productSetting_productsGeneral_catalogMode';

let browserContext;
let page;

/*
Enable catalog mode / Disable show prices
Check catalog page
Enable show prices
Check catalog page
Disable catalog mode
 */
describe('BO - Shop Parameters - Product Settings : Enable/Disable catalog mode', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );

    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
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
      const result = await productSettingsPage.changeCatalogModeStatus(page, test.args.enable);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    if (test.args.enable) {
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

          const result = await productSettingsPage.setShowPricesStatus(page, showPrices.args.enable);
          await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
        });

        it('should check product prices in the home page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkPricesInHomePage${index}`, baseContext);

          page = await productSettingsPage.viewMyShop(page);

          await homePage.changeLanguage(page, 'en');

          const isPriceVisible = await homePage.isPriceVisible(page, 1);
          await expect(isPriceVisible).to.equal(showPrices.args.enable);
        });

        it('should go to the first product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

          await homePage.goToProductPage(page, 1);
          const pageTitle = await productPage.getPageTitle(page);
          await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
        });

        it('should check the existence of product price and add to cart button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkPrice&AddToCartButton${index}`, baseContext);

          let isVisible = await productPage.isPriceDisplayed(page);
          await expect(isVisible).to.equal(showPrices.args.isPriceExist);

          isVisible = await productPage.isAddToCartButtonDisplayed(page);
          await expect(isVisible).to.equal(showPrices.args.isAddToCartExist);
        });

        it('should close the page and go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

          page = await productPage.closePage(browserContext, page, 0);

          const pageTitle = await productSettingsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
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

        page = await productSettingsPage.viewMyShop(page);

        await homePage.changeLanguage(page, 'en');

        const isPriceVisible = await homePage.isPriceVisible(page, 1);
        await expect(isPriceVisible).to.be.true;
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage3', baseContext);

        await homePage.goToProductPage(page, 1);
        const pageTitle = await productPage.getPageTitle(page);
        await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
      });

      it('should check the existence of product price and add to cart button', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'checkPrice&AddToCartButtonVisible',
          baseContext,
        );

        let isVisible = await productPage.isPriceDisplayed(page);
        await expect(isVisible).to.be.true;

        isVisible = await productPage.isAddToCartButtonDisplayed(page);
        await expect(isVisible).to.be.true;
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

        page = await productPage.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    }
  });
});
