import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  foClassicCategoryPage,
  foClassicHomePage,
  foClassicNewProductsPage,
  foClassicPricesDropPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_homePage_displaySomeProducts';

/*
- Go to FO
- Check the block of popular products
- Check the banner and the custom text block
- Check the block of products on sale
- Check the block of new products
 */
describe('FO - Home Page : Display some products', async () => {
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

  describe('Check popular products block', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should check popular product title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProducts', baseContext);

      await foClassicHomePage.changeLanguage(page, 'en');

      const popularProductTitle = await foClassicHomePage.getBlockTitle(page, 'popularproducts');
      expect(popularProductTitle).to.equal('Popular Products');
    });

    it('should check the number of popular products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProductsNumber', baseContext);

      const productsNumber = await foClassicHomePage.getProductsBlockNumber(page, 'popularproducts');
      expect(productsNumber).to.equal(8);
    });

    it('should check All products link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllPopularProductsLink', baseContext);

      await foClassicHomePage.goToAllProductsBlockPage(page, 1);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage1', baseContext);

      await foClassicHomePage.goToHomePage(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });
  });

  describe('Check the banner and the custom text block', async () => {
    it('should check that the banner is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBanner', baseContext);

      const isVisible = await foClassicHomePage.isBannerVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check that the custom text block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomTextBlock', baseContext);

      const isVisible = await foClassicHomePage.isCustomTextBlockVisible(page);
      expect(isVisible).to.eq(true);
    });
  });

  describe('Check products on sale block', async () => {
    it('should check products on sale block title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsOnSaleBlockTitle', baseContext);

      const popularProductTitle = await foClassicHomePage.getBlockTitle(page, 'onsale');
      expect(popularProductTitle).to.equal('On sale');
    });

    it('should check the number of products in sale', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProductsInSale', baseContext);

      const productsNumber = await foClassicHomePage.getProductsBlockNumber(page, 'onsale');
      expect(productsNumber).to.equal(2);
    });

    it('should check All products for sale link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllProductsInSaleLink', baseContext);

      await foClassicHomePage.goToAllProductsBlockPage(page, 2);

      const pageTitle = await foClassicPricesDropPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicPricesDropPage.pageTitle);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage2', baseContext);

      await foClassicHomePage.goToHomePage(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });
  });

  describe('Check new products block', async () => {
    it('should check new products title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsBlock', baseContext);

      const popularProductTitle = await foClassicHomePage.getBlockTitle(page, 'newproducts');
      expect(popularProductTitle).to.equal('New products');
    });

    it('should check the number of new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsNumber', baseContext);

      const productsNumber = await foClassicHomePage.getProductsBlockNumber(page, 'newproducts');
      expect(productsNumber).to.equal(8);
    });

    it('should check All new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllNewProductsLink', baseContext);

      await foClassicHomePage.goToAllProductsBlockPage(page, 3);

      const pageTitle = await foClassicNewProductsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicNewProductsPage.pageTitle);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage3', baseContext);

      await foClassicHomePage.goToHomePage(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });
  });
});
