// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';

// Import FO pages
import categoryPage from '@pages/FO/hummingbird/category';
import homePage from '@pages/FO/hummingbird/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_sortAndFilter_clearFilters';

/*
Pre-condition:
- Get the number of active products
Scenario:
- Filter products by composition and availability
- Clear filters
 */
describe('FO - Menu and navigation : Clear filters', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let productsNumber: number;

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

  // Pre-condition: Get the number of products
  describe('PRE-TEST : Get the number of active products', async () => {
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should filter by Active Status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfActiveProducts', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await productsPage.filterProducts(page, 'active', 'Yes', 'select');

      numberOfActiveProducts = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfActiveProducts).to.within(0, numberOfProducts);
    });
  });

  // Filter products by composition and  availability
  describe('Filter products list by Composition', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      // Click on view my shop
      page = await productSettingsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const result = await homePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await homePage.changeLanguage(page, 'en');
      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should filter products by composition \'Ceramic - Cotton\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByComposition', baseContext);

      await categoryPage.filterByCheckbox(page, 'Composition', 'Composition-Ceramic');
      await categoryPage.filterByCheckbox(page, 'Composition', 'Composition-Ceramic-Cotton');
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters1', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.to.contains('Composition: Cotton');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts4', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should close the second filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeActiveFilter', baseContext);

      await categoryPage.closeFilter(page, 2);

      const isNotVisible = await categoryPage.isActiveFilterNotVisible(page);
      expect(isNotVisible).to.equal(false);
    });

    it('should check the filter \'Composition: Ceramic\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActiveFilters1', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.not.to.contains('Composition: Cotton');
    });
  });

  describe('Filter products list by Availability', async () => {
    it('should filter products by availability \'In Stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByAvailability', baseContext);

      await categoryPage.filterByCheckbox(page, 'Availability', 'Availability-In+stock');
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters2', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Availability: In stock')
        .and.to.contains('Composition: Ceramic');
    });

    it('should close the filter \'Availability: In stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeActiveFilter2', baseContext);

      await page.waitForTimeout(5000);
      await categoryPage.closeFilter(page, 1);

      const isNotVisible = await categoryPage.isActiveFilterNotVisible(page);
      expect(isNotVisible).to.equal(false);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters3', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.to.not.contains('Availability: In stock');
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters5', baseContext);

      const isActiveFilterNotVisible = await categoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts2', baseContext);

      const productsNumberAfterClearFilter = await categoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.equal(numberOfActiveProducts);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
