// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import productPage from '@pages/FO/hummingbird/product';

// Import demo data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_homePage_selectColor';

describe('FO - Home Page : Select color', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Select color', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should select the color White for the first product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor1', baseContext);

      await homePage.selectProductColor(page, 1, 'White');

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check that the displayed product is white', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProduct', baseContext);

      const pageURL = await productPage.getCurrentURL(page);
      expect(pageURL).to.contains('color-white');
      // @todo https://github.com/PrestaShop/PrestaShop/issues/35481
      // .and.to.contains('size-m');
    });

    it('should go to Home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await productPage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should select the color Black for the first product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor2', baseContext);

      await homePage.selectProductColor(page, 1, 'Black');

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should check that the displayed product is white', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProduct2', baseContext);

      const pageURL = await productPage.getCurrentURL(page);
      expect(pageURL).to.contains('color-black');
      // @todo https://github.com/PrestaShop/PrestaShop/issues/35481
      // .and.to.contains('size-m');
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
