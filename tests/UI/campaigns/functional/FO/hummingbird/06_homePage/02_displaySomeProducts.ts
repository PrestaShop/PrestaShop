// Import utils
import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  foHummingbirdNewProductsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check popular products block', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should check popular product title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProducts', baseContext);

      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const popularProductTitle = await foHummingbirdHomePage.getBlockTitle(page, 'ps-featuredproducts');
      expect(popularProductTitle).to.equal('Featured products');
    });

    it('should check the number of popular products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProductsNumber', baseContext);

      const productsNumber = await foHummingbirdHomePage.getProductsBlockNumber(page, 'ps-featuredproducts');
      expect(productsNumber).to.equal(4);
    });

    it('should check All products link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllPopularProductsLink', baseContext);

      await foHummingbirdHomePage.goToAllProductsPage(page, 'ps-featuredproducts');

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage1', baseContext);

      await foHummingbirdHomePage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });
  });

  describe('Check the banner and the custom text block', async () => {
    it('should check that the banner is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBanner', baseContext);

      const isVisible = await foHummingbirdHomePage.isBannerVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check that the custom text block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomTextBlock', baseContext);

      const isVisible = await foHummingbirdHomePage.isCustomTextBlockVisible(page);
      expect(isVisible).to.eq(true);
    });
  });

  describe('Check new products block', async () => {
    it('should check new products title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsBlock', baseContext);

      const popularProductTitle = await foHummingbirdHomePage.getBlockTitle(page, 'ps-newproducts');
      expect(popularProductTitle).to.equal('Latest arrivals');
    });

    it('should check the number of new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsNumber', baseContext);

      const productsNumber = await foHummingbirdHomePage.getProductsBlockNumber(page, 'ps-newproducts');
      expect(productsNumber).to.equal(4);
    });

    it('should check All new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllNewProductsLink', baseContext);

      await foHummingbirdHomePage.goToAllProductsPage(page, 'ps-newproducts');

      const pageTitle = await foHummingbirdNewProductsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdNewProductsPage.pageTitle);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
