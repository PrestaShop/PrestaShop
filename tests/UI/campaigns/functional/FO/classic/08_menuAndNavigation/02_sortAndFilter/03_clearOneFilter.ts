// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductSettingsPage,
  type BrowserContext,
  foClassicHomePage,
  foClassicCategoryPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_sortAndFilter_clearOneFilter';

/*
Pre-condition:
- Get the number of active products
Scenario:
- Filter products by composition and availability
- Clear one filter
 */
describe('FO - Menu and navigation : Clear one filter', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let productsNumber: number;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Get the number of products
  describe('PRE-TEST : Get the number of active products', async () => {
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

    it('should filter by Active Status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfActiveProducts', baseContext);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      await boProductsPage.filterProducts(page, 'active', 'Yes', 'select');

      numberOfActiveProducts = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfActiveProducts).to.within(0, numberOfProducts);
    });
  });

  // Filter products by composition and  availability
  describe('Filter products list by Composition', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      // Click on view my shop
      page = await boProductSettingsPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await foClassicHomePage.changeLanguage(page, 'en');
      await foClassicHomePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should filter products by composition \'Ceramic - Cotton - Recycled cardboard\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByComposition', baseContext);

      await foClassicCategoryPage.filterByCheckbox(page, 'feature', 'Composition-Ceramic', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters1', baseContext);

      const activeFilters = await foClassicCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts4', baseContext);

      productsNumber = await foClassicCategoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should close the active filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeActiveFilter', baseContext);

      await foClassicCategoryPage.closeFilter(page, 1);

      const isNotVisible = await foClassicCategoryPage.isActiveFilterNotVisible(page);
      expect(isNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts1', baseContext);

      const productsNumberAfterClearFilter = await foClassicCategoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });
  });

  describe('Filter products list by Availability', async () => {
    it('should filter products by availability \'In Stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByAvailability', baseContext);

      await foClassicCategoryPage.filterByCheckbox(page, 'availability', '\'Availability-In+stock\'', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters2', baseContext);

      const activeFilters = await foClassicCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Availability: In stock');
    });

    it('should close the active filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeActiveFilter2', baseContext);

      await foClassicCategoryPage.closeFilter(page, 1);

      const isNotVisible = await foClassicCategoryPage.isActiveFilterNotVisible(page);
      expect(isNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts2', baseContext);

      const productsNumberAfterClearFilter = await foClassicCategoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });
  });
});
