// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
// Import FO pages
import {homePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_displayAvailableQuantities';

/*
Disable display available quantities on product page
Check that quantity is not displayed
Enable display available quantities on product page
Check that quantity is displayed
 */

describe('BO - Shop Parameters - Product Settings : Display available quantities on the product page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );
    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', enable: false}},
    {args: {action: 'enable', enable: true}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} Display available quantities on the product page`, async function () {
      await testContext.addContextItem(this,
        'testIdentifier',
        `${test.args.action}DisplayAvailableQuantities`,
        baseContext,
      );

      const result = await productSettingsPage.setDisplayAvailableQuantitiesStatus(page, test.args.enable);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page was not opened').to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
    });

    it('should check the product quantity on the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkQuantity${index}`, baseContext);

      const quantityIsVisible = await productPage.isQuantityDisplayed(page);
      expect(quantityIsVisible).to.be.equal(test.args.enable);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });
});
