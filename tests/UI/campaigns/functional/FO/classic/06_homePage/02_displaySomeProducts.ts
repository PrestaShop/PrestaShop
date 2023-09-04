// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import categoryPageFO from '@pages/FO/category';
import {homePage} from '@pages/FO/home';
import {pricesDropPage} from '@pages/FO/pricesDrop';
import {newProductsPage} from '@pages/FO/newProducts';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
      await expect(result).to.be.true;
    });

    it('should check popular product title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProducts', baseContext);

      await homePage.changeLanguage(page, 'en');

      const popularProductTitle = await homePage.getBlockTitle(page, 1);
      await expect(popularProductTitle).to.equal('Popular Products');
    });

    it('should check the number of popular products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProductsNumber', baseContext);

      const productsNumber = await homePage.getProductsBlockNumber(page, 1);
      await expect(productsNumber).to.equal(8);
    });

    it('should check All products link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllPopularProductsLink', baseContext);

      await homePage.goToAllProductsBlockPage(page, 1);

      const isCategoryPageVisible = await categoryPageFO.isCategoryPage(page);
      await expect(isCategoryPageVisible, 'Home category page was not opened').to.be.true;
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage1', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });
  });

  describe('Check the banner and the custom text block', async () => {
    it('should check that the banner is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBanner', baseContext);

      const isVisible = await homePage.isBannerVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should check that the custom text block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomTextBlock', baseContext);

      const isVisible = await homePage.isCustomTextBlockVisible(page);
      await expect(isVisible).to.be.true;
    });
  });

  describe('Check products on sale block', async () => {
    it('should check products on sale block title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsOnSaleBlockTitle', baseContext);

      const popularProductTitle = await homePage.getBlockTitle(page, 2);
      await expect(popularProductTitle).to.equal('On sale');
    });

    it('should check the number of products in sale', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProductsInSale', baseContext);

      const productsNumber = await homePage.getProductsBlockNumber(page, 2);
      await expect(productsNumber).to.equal(2);
    });

    it('should check All products for sale link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllProductsInSaleLink', baseContext);

      await homePage.goToAllProductsBlockPage(page, 2);

      const pageTitle = await pricesDropPage.getPageTitle(page);
      await expect(pageTitle).to.equal(pricesDropPage.pageTitle);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage2', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });
  });

  describe('Check new products block', async () => {
    it('should check new products title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsBlock', baseContext);

      const popularProductTitle = await homePage.getBlockTitle(page, 3);
      await expect(popularProductTitle).to.equal('New products');
    });

    it('should check the number of new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewProductsNumber', baseContext);

      const productsNumber = await homePage.getProductsBlockNumber(page, 3);
      await expect(productsNumber).to.equal(8);
    });

    it('should check All new products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllNewProductsLink', baseContext);

      await homePage.goToAllProductsBlockPage(page, 3);

      const pageTitle = await newProductsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(newProductsPage.pageTitle);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage3', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });
  });
});
