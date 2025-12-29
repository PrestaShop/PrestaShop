// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  type BrowserContext,
  foHummingbirdHomePage,
  foHummingbirdCategoryPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_homePage_allProducts';

describe('FO - Home Page : Display all products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let numberOfProducts: number;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO : Get the number of products', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should filter by Active Status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByStatus', baseContext);

      await boProductsPage.filterProducts(page, 'active', 'Yes', 'select');

      numberOfActiveProducts = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfActiveProducts).to.within(0, numberOfProducts);

      for (let i = 1; i <= numberOfActiveProducts; i++) {
        const productStatus = await boProductsPage.getProductStatusFromList(page, i);
        expect(productStatus).to.eq(true);
      }
    });
  });

  describe('FO : Display all Products', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await foHummingbirdHomePage.changeLanguage(page, 'en');
      await foHummingbirdHomePage.goToAllProductsPage(page, 'ps-featuredproducts');

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should check the number of products on the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'numberOfProducts', baseContext);

      const numberOfProducts = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.eql(numberOfActiveProducts);
    });

    it('should check that the header name is equal to HOME', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'nameOfHeader', baseContext);

      const headerProductsName = await foHummingbirdCategoryPage.getHeaderPageName(page);
      expect(headerProductsName).to.equal('Home');
    });

    it('should check that the sorting link is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'homeSortAndPaginationLink', baseContext);

      const isSortingLinkVisible = await foHummingbirdCategoryPage.isSortButtonVisible(page);
      expect(isSortingLinkVisible).to.eq(true);
    });

    it('should check the showing items text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'showingItemTextDisplayed', baseContext);

      const numberOfItems = await foHummingbirdCategoryPage.getShowingItems(page);
      expect(numberOfItems).equal(`Showing 1-12 of ${numberOfActiveProducts} item(s)`);
    });

    it('should check the list of product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayedListOfProduct', baseContext);

      const listOfProductDisplayed = await foHummingbirdCategoryPage.getNumberOfProductsDisplayed(page);
      expect(listOfProductDisplayed).to.be.above(0);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
