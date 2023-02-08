// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
// Import FO pages
import categoryPageFO from '@pages/FO/category';
import {homePage} from '@pages/FO/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_homePage_allProducts';

describe('FO - Home Page : Display all products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let numberOfProducts: number;

  // Pre-condition: Disable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, false, `${baseContext}_disableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO : Get the number of products', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should filter by Active Status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByStatus', baseContext);

      await productsPage.filterProducts(page, 'active', 'Active', 'select');

      numberOfActiveProducts = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfActiveProducts).to.within(0, numberOfProducts);

      for (let i = 1; i <= numberOfActiveProducts; i++) {
        const productStatus = await productsPage.getProductStatusFromList(page, i);
        await expect(productStatus).to.be.true;
      }
    });
  });

  describe('FO : Display all Products', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await homePage.changeLanguage(page, 'en');
      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPageFO.isCategoryPage(page);
      await expect(isCategoryPageVisible, 'Home category page was not opened').to.be.true;
    });

    it('should check the number of products on the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'numberOfProducts', baseContext);

      const numberOfProducts = await categoryPageFO.getNumberOfProducts(page);
      expect(numberOfProducts).to.eql(numberOfActiveProducts);
    });

    it('should check that the header name is equal to HOME', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'nameOfHeader', baseContext);

      const headerProductsName = await categoryPageFO.getHeaderPageName(page);
      expect(headerProductsName).to.equal('HOME');
    });

    it('should check that the sorting link is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'homeSortAndPaginationLink', baseContext);

      const isSortingLinkVisible = await categoryPageFO.isSortButtonVisible(page);
      await expect(isSortingLinkVisible, 'Sorting Link is not visible').to.be.true;
    });

    it('should check the showing items text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'showingItemTextDisplayed', baseContext);

      const numberOfItems = await categoryPageFO.getShowingItems(page);
      await expect(numberOfItems).equal(`Showing 1-12 of ${numberOfActiveProducts} item(s)`);
    });

    it('should check the list of product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayedListOfProduct', baseContext);

      const listOfProductDisplayed = await categoryPageFO.getNumberOfProductsDisplayed(page);
      await expect(listOfProductDisplayed).to.be.above(0);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
