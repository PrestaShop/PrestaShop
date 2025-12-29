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

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_sortAndFilter_filterProducts';

/*
Pre-condition:
- Get the number of active products
- Change the number of products per page
- Install the theme hummingbird
Scenario:
- Filter products by category, size, color, composition, price, brand, dimension, availability, paper type
Post-condition:
- Reset the number of products per page
- Uninstall the theme hummingbird
 */
describe('FO - Menu and Navigation - Sort and filter : Filter products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let productsNumber: number;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

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

  // Pre-condition : Change the product per page by the number of all products
  describe('PRE-TEST : Change the number of products per page', async () => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.productSettingsLink,
      );

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should change the value of products per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeProductPerPage', baseContext);

      const result = await boProductSettingsPage.setProductsDisplayedPerPage(page, numberOfActiveProducts);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });
  });

  // Filter products by Categories, size, color, composition, property, availability, brand, price, dimension & paper type
  describe('Filter products list by Category', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      // Click on view my shop
      page = await boProductSettingsPage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

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

    it('should filter products by category \'Accessories - Art\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCategory', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Categories', 'Accessories', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Categories', 'Art', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Categories: Accessories')
        .and.to.contains('Categories: Art');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters', baseContext);

      const isActiveFilterNotVisible = await foHummingbirdCategoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of the displayed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProducts', baseContext);

      const numberOfProducts = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.equal(numberOfActiveProducts);
    });
  });

  describe('Filter products list by Size', async () => {
    it('should filter products by size \'S-L-XL\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBySize', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Size', 'Size-S', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Size', 'Size-S-L', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Size', 'Size-S-L-XL', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters2', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Size: S')
        .and.to.contains('Size: L')
        .and.to.contains('Size: XL');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts2', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList2', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await foHummingbirdCategoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['size-s', 'size-l', 'size-xl']);
      }
    });
  });

  describe('Filter products list by Color', async () => {
    it('should filter products by Color \'Black\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByColor', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Color', '#434A54', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters3', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Size: S')
        .and.to.contains('Size: L')
        .and.to.contains('Size: XL')
        .and.to.contains('Color: Black');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts3', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(0);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList3', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await foHummingbirdCategoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['size-s', 'size-l', 'size-xl', 'color-black']);
      }
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters2', baseContext);

      const isActiveFilterNotVisible = await foHummingbirdCategoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of the displayed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProducts2', baseContext);

      const numberOfProducts = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.equal(numberOfActiveProducts);
    });
  });

  describe('Filter products list by Composition', async () => {
    it('should filter products by composition \'Ceramic - Cotton - Recycled cardboard\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByComposition', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Composition', 'Composition-Ceramic', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Composition', 'Composition-Ceramic-Cotton', true);
      await foHummingbirdCategoryPage.filterByCheckbox(
        page,
        'Composition',
        'Composition-Ceramic-Cotton-Recycled+cardboard',
        true,
      );
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters4', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.to.contains('Composition: Cotton')
        .and.to.contains('Composition: Recycled cardboard');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts4', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });
  });

  describe('Filter products list by Price', async () => {
    it('should filter products by price \'€14.00 - €30.00\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByPrice', baseContext);

      const maxPrice = await foHummingbirdCategoryPage.getMaximumPrice(page);
      const minPrice = await foHummingbirdCategoryPage.getMinimumPrice(page);

      await foHummingbirdCategoryPage.filterByPrice(page, minPrice, maxPrice, 14, 30);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters5', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Price: €14.00')
        .and.to.contains('Composition: Ceramic')
        .and.to.contains('Composition: Cotton')
        .and.to.contains('Composition: Recycled cardboard');
    });

    it('should check filter products by price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrices', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);

      for (let i = 1; i <= productsNumber; i++) {
        const price = await foHummingbirdCategoryPage.getProductPrice(page, i);
        expect(price).to.within(14, 30);
      }
    });
  });

  describe('Filter products list by Brand', async () => {
    it('should filter products by brand \'Graphic Corner\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByBrand', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Brand', 'Graphic+Corner', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters6', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Recycled cardboard')
        .and.to.contains('Price: €14.00 -')
        .and.to.contains('Brand: Graphic Corner');
    });

    it('should check filter products by brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBrands', baseContext);

      const numberOfProductsAfterFilter = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.greaterThan(numberOfProductsAfterFilter);

      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const price = await foHummingbirdCategoryPage.getProductPrice(page, i);
        expect(price).to.within(14, 39);
      }
    });
  });

  describe('Filter products list by Dimension', async () => {
    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters3', baseContext);

      const isActiveFilterNotVisible = await foHummingbirdCategoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts5', baseContext);

      const productsNumberAfterClearFilter = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });

    it('should filter products by Dimension \'40x60cm -  60x90cm\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDimension', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Dimension', 'Dimension-40x60cm', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Dimension', 'Dimension-40x60cm-60x90cm', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters7', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Dimension: 40x60cm')
        .and.to.contains('Dimension: 60x90cm');
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList4', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await foHummingbirdCategoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['dimension-40x60cm', 'dimension-60x90cm']);
      }
    });
  });

  describe('Filter products list by Availability', async () => {
    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters6', baseContext);

      const isActiveFilterNotVisible = await foHummingbirdCategoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts6', baseContext);

      const productsNumberAfterClearFilter = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });

    it('should filter products by availability \'In Stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByAvailability', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Availability', 'Availability-In+stock', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters8', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Availability: In stock');
    });
  });

  describe('Filter products list by Paper Type', async () => {
    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters4', baseContext);

      const isActiveFilterNotVisible = await foHummingbirdCategoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts7', baseContext);

      const productsNumberAfterClearFilter = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });

    it('should filter products by paper type \'Ruled - Plain - Squared\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByPaperType', baseContext);

      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Paper Type', 'Paper+Type-Ruled', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Paper Type', 'Paper+Type-Ruled-Plain', true);
      await foHummingbirdCategoryPage.filterByCheckbox(page, 'Paper Type', 'Paper+Type-Ruled-Plain-Squared', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters9', baseContext);

      const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Paper Type: Ruled')
        .and.to.contains('Paper Type: Plain')
        .and.to.contains('Paper Type: Squared');
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList5', baseContext);

      productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await foHummingbirdCategoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['paper_type-ruled', 'paper_type-plain', 'paper_type-squared']);
      }
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters5', baseContext);

      const isActiveFilterNotVisible = await foHummingbirdCategoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts8', baseContext);

      const productsNumberAfterClearFilter = await foHummingbirdCategoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });
  });

  // Post-condition : Reset number of products per page
  describe('POST-TEST : Reset the number of products per page', async () => {
    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFo', baseContext);

      page = await foHummingbirdCategoryPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should change the value of products per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ResetProductPerPage', baseContext);

      const result = await boProductSettingsPage.setProductsDisplayedPerPage(page, 12);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
