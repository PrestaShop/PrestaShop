// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_displayAddToCartButton';

describe('BO - Shop Parameters - Product Settings : Display add to cart button when a product has attributes', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}-preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Display add to cart button when a product has attributes', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

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
      {args: {action: 'disable', enable: false}},
      {args: {action: 'enable', enable: true}},
    ];

    tests.forEach((test, index: number) => {
      it(`should ${test.args.action} Add to cart button when product has attributes`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayAddToCartButton`, baseContext);

        const result = await boProductSettingsPage.setDisplayAddToCartButton(page, test.args.enable);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        page = await boProductSettingsPage.viewMyShop(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should check the add to cart button in the second popular product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAddToCartButton${index}`, baseContext);

        const isAddToCartButtonVisible = await foHummingbirdHomePage.isAddToCartButtonVisible(page, 2);
        expect(isAddToCartButtonVisible).to.eq(test.args.enable);
      });

      it('should check that the add to cart button in the sixth popular product is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAddToCartButton2${index}`, baseContext);

        const isAddToCartButtonVisible = await foHummingbirdHomePage.isAddToCartButtonVisible(page, 6);
        expect(isAddToCartButtonVisible).to.eq(true);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

        page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}-postTest`);
});
