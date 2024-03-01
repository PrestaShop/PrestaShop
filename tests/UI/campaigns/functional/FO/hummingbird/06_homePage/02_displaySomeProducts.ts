// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
import categoryPageFO from '@pages/FO/hummingbird/category';
import homePage from '@pages/FO/hummingbird/home';
import newProductsPage from '@pages/FO/hummingbird/newProducts';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_homePage_displaySomeProducts';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO
- Check the block of popular products
- Check the banner and the custom text block
- Check the block of new products
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Home Page : Display some products', async () => {
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

  describe('Check popular products block', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should check popular product title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProducts', baseContext);

      await homePage.changeLanguage(page, 'en');

      const popularProductTitle = await homePage.getBlockTitle(page, 'featured-products');
      expect(popularProductTitle).to.equal('Popular Products');
    });

    it('should check the number of popular products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProductsNumber', baseContext);

      const productsNumber = await homePage.getProductsBlockNumber(page, 'featured-products');
      expect(productsNumber).to.equal(8);
    });

    it('should check All products link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllPopularProductsLink', baseContext);

      await homePage.clickOnAllProductsButton(page, 'featured-products');

      const isCategoryPageVisible = await categoryPageFO.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage1', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });
  });

  describe('Check the banner and the custom text block', async () => {
    it('should check that the banner is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBanner', baseContext);

      const isVisible = await homePage.isBannerVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check that the custom text block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomTextBlock', baseContext);

      const isVisible = await homePage.isCustomTextBlockVisible(page);
      expect(isVisible).to.eq(true);
    });
  });

  describe('Check new products block', async () => {
    it('should check new products title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsBlock', baseContext);

      const popularProductTitle = await homePage.getBlockTitle(page, 'new-products');
      expect(popularProductTitle).to.equal('New products');
    });

    it('should check the number of new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsNumber', baseContext);

      const productsNumber = await homePage.getProductsBlockNumber(page, 'new-products');
      expect(productsNumber).to.equal(8);
    });

    it('should check All new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllNewProductsLink', baseContext);

      await homePage.clickOnAllProductsButton(page, 'new-products');

      const pageTitle = await newProductsPage.getPageTitle(page);
      expect(pageTitle).to.equal(newProductsPage.pageTitle);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
