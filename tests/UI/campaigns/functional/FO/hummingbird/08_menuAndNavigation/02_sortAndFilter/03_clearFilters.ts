// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductSettingsPage,
  type BrowserContext,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_sortAndFilter_clearFilters';

/*
Pre-condition:
- Get the number of active products
Scenario:
- Filter products by composition and availability
- Clear filters
 */
describe('FO - Menu and Navigation - Sort and filter : Clear filters', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let productsNumber: number;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

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
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await foHummingbirdHomePage.changeLanguage(page, 'en');
      await foHummingbirdHomePage.goToAllProductsPage(page, 'ps-featuredproducts');

      const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should filter products by composition \'Ceramic - Cotton\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByComposition', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Composition', 'Composition-Ceramic', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Composition', 'Composition-Ceramic-Cotton', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters1', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.to.contains('Composition: Cotton');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts4', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should close the second filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeActiveFilter', baseContext);

      await foHummingbirdCategoryPage.closeFilter(page, 2);

      const isNotVisible = await foHummingbirdCategoryPage.isActiveFilterNotVisible(page);
      expect(isNotVisible).to.equal(false);
    });

    it('should check the filter \'Composition: Ceramic\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActiveFilters1', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.not.to.contains('Composition: Cotton');
    });
  });

  describe('Filter products list by Availability', async () => {
    it('should filter products by availability \'In Stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByAvailability', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Availability', 'Availability-In+stock', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters2', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Availability: In stock')
        .and.to.contains('Composition: Ceramic');
    });

    it('should close the filter \'Availability: In stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeActiveFilter2', baseContext);

      await page.waitForTimeout(5000);
      await foHummingbirdCategoryPage.closeFilter(page, 1);

      const isNotVisible = await foHummingbirdCategoryPage.isActiveFilterNotVisible(page);
      expect(isNotVisible).to.equal(false);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters3', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.to.not.contains('Availability: In stock');
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters5', baseContext);

      const isActiveFilterNotVisible = await foHummingbirdCategoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts2', baseContext);

      const productsNumberAfterClearFilter = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.equal(numberOfActiveProducts);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
