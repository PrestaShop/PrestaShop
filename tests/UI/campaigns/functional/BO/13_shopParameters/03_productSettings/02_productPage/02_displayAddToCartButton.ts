// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
// Import FO pages
import homePage from '@pages/FO/hummingbird/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_displayAddToCartButton';

describe('BO - Shop Parameters - Product Settings : Display add to cart button when a product has attributes', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}-preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Display add to cart button when a product has attributes', async () => {
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
      it(`should ${test.args.action} Add to cart button when product has attributes`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayAddToCartButton`, baseContext);

        const result = await productSettingsPage.setDisplayAddToCartButton(page, test.args.enable);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        page = await productSettingsPage.viewMyShop(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should check the add to cart button in the second popular product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAddToCartButton${index}`, baseContext);

        const isAddToCartButtonVisible = await homePage.isAddToCartButtonVisible(page, 2);
        expect(isAddToCartButtonVisible).to.eq(test.args.enable);
      });

      it('should check that the add to cart button in the sixth popular product is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAddToCartButton2${index}`, baseContext);

        const isAddToCartButtonVisible = await homePage.isAddToCartButtonVisible(page, 6);
        expect(isAddToCartButtonVisible).to.eq(true);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

        page = await homePage.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}-postTest`);
});
