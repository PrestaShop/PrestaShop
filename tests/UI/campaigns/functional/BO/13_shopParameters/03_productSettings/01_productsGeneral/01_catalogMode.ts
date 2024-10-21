// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  dataProducts,
  type Page,
  foClassicHomePage,
  foClassicProductPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_catalogMode';

/*
Enable catalog mode / Disable show prices
Check catalog page
Enable show prices
Check catalog page
Disable catalog mode
 */
describe('BO - Shop Parameters - Product Settings : Enable/Disable catalog mode', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.productSettingsLink,
    );
    await boProductSettingsPage.closeSfToolBar(page);

    const pageTitle = await boProductSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
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

      const result = await boProductSettingsPage.changeCatalogModeStatus(page, test.args.enable);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
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

      testShowPrices.forEach((showPrices, index: number) => {
        it(`should ${showPrices.args.action} show prices`, async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${showPrices.args.action}ShowPrices`,
            baseContext,
          );

          const result = await boProductSettingsPage.setShowPricesStatus(page, showPrices.args.enable);
          expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await boProductSettingsPage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should check the product price of the first product in the home page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkPricesInHomePage${index}`, baseContext);

          const isPriceVisible = await foClassicHomePage.isPriceVisible(page, 1);
          expect(isPriceVisible).to.equal(showPrices.args.enable);
        });

        it('should go to the first product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

          await foClassicHomePage.goToProductPage(page, 1);

          const pageTitle = await foClassicProductPage.getPageTitle(page);
          expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
        });

        it('should check the existence of product price and add to cart button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkPrice&AddToCartButton${index}`, baseContext);

          let isVisible = await foClassicProductPage.isPriceDisplayed(page);
          expect(isVisible).to.equal(showPrices.args.isPriceExist);

          isVisible = await foClassicProductPage.isAddToCartButtonDisplayed(page);
          expect(isVisible).to.equal(showPrices.args.isAddToCartExist);
        });

        it('should close the page and go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

          page = await foClassicProductPage.closePage(browserContext, page, 0);

          const pageTitle = await boProductSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
        });
      });
    } else {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

        page = await boProductSettingsPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should check that the product price is visible in the home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPricesInHomePageVisible', baseContext);

        const isPriceVisible = await foClassicHomePage.isPriceVisible(page, 1);
        expect(isPriceVisible).to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage3', baseContext);

        await foClassicHomePage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
      });

      it('should check the existence of product price and add to cart button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPrice&AddToCartButtonVisible', baseContext);

        let isVisible = await foClassicProductPage.isPriceDisplayed(page);
        expect(isVisible).to.eq(true);

        isVisible = await foClassicProductPage.isAddToCartButtonDisplayed(page);
        expect(isVisible).to.eq(true);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    }
  });
});
