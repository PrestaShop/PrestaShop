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
describe('FO - Menu and navigation : Filter products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let productsNumber: number;

  // Pre-condition : Install Hummingbird
  //installHummingbird(`${baseContext}_preTest`);

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

  // Pre-condition : Change the product per page by the number of all products
  describe('PRE-TEST : Change the number of products per page', async () => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the value of products per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeProductPerPage', baseContext);

      const result = await productSettingsPage.setProductsDisplayedPerPage(page, numberOfActiveProducts);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  // Filter products by Categories, size, color, composition, property, availability, brand, price, dimension & paper type
  describe('Filter products list by Category', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      // Click on view my shop
      page = await productSettingsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await homePage.changeLanguage(page, 'en');
      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should filter products by category \'Accessories - Art\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCategory', baseContext);

      await categoryPage.filterByCheckbox(page, 'category', 'Accessories', true);
      await categoryPage.filterByCheckbox(page, 'category', 'Art', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Categories: Accessories')
        .and.to.contains('Categories: Art');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['accessories', 'art', 'stationery']);
      }
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters', baseContext);

      const isActiveFilterNotVisible = await categoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of the displayed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProducts', baseContext);

      const numberOfProducts = await categoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.equal(numberOfActiveProducts);
    });
  });

  describe('Filter products list by Size', async () => {
    it('should filter products by size \'S-L-XL\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBySize', baseContext);

      await categoryPage.filterByCheckbox(page, 'attribute_group', 'Size-S', true);
      await categoryPage.filterByCheckbox(page, 'attribute_group', 'Size-S-L', true);
      await categoryPage.filterByCheckbox(page, 'attribute_group', 'Size-S-L-XL', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters2', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Size: S')
        .and.to.contains('Size: L')
        .and.to.contains('Size: XL');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts2', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList2', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['size-s', 'size-l', 'size-xl']);
      }
    });
  });

  describe('Filter products list by Color', async () => {
    it('should filter products by Color \'Black\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByColor', baseContext);

      await categoryPage.filterByCheckbox(page, 'attribute_group', 'Color-Black', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters3', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Size: S')
        .and.to.contains('Size: L')
        .and.to.contains('Size: XL')
        .and.to.contains('Color: Black');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts3', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(0);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList3', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['size-s', 'size-l', 'size-xl', 'color-black']);
      }
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters2', baseContext);

      const isActiveFilterNotVisible = await categoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of the displayed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProducts2', baseContext);

      const numberOfProducts = await categoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.equal(numberOfActiveProducts);
    });
  });

  describe('Filter products list by Composition', async () => {
    it('should filter products by composition \'Ceramic - Cotton - Recycled cardboard\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByComposition', baseContext);

      await categoryPage.filterByCheckbox(page, 'feature', 'Composition-Ceramic', true);
      await categoryPage.filterByCheckbox(page, 'feature', 'Composition-Ceramic-Cotton', true);
      await categoryPage.filterByCheckbox(page, 'feature', '\'Composition-Ceramic-Cotton-Recycled+cardboard\'', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters4', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.to.contains('Composition: Cotton')
        .and.to.contains('Composition: Recycled cardboard');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts4', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });
  });

  describe('Filter products list by Price', async () => {
    it('should filter products by price \'€14.00 - €20.00\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByPrice', baseContext);

      const maxPrice = await categoryPage.getMaximumPrice(page);
      const minPrice = await categoryPage.getMinimumPrice(page);

      await categoryPage.filterByPrice(page, minPrice, maxPrice, 14, 39);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters5', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Price: €14.00 - €39.00')
        .and.to.contains('Composition: Ceramic')
        .and.to.contains('Composition: Cotton')
        .and.to.contains('Composition: Recycled cardboard');
    });

    it('should check filter products by price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrices', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);

      for (let i = 1; i <= productsNumber; i++) {
        const price = await categoryPage.getProductPrice(page, i);
        expect(price).to.within(14, 39);
      }
    });
  });

  describe('Filter products list by Brand', async () => {
    it('should filter products by brand \'Graphic Corner\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByBrand', baseContext);

      await categoryPage.filterByCheckbox(page, 'manufacturer', '\'Graphic+Corner\'', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters6', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Recycled cardboard')
        .and.to.contains('Price: €14.00 - €39.00')
        .and.to.contains('Brand: Graphic Corner');
    });

    it('should check filter products by brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBrands', baseContext);

      const numberOfProductsAfterFilter = await categoryPage.getNumberOfProducts(page);
      expect(productsNumber).to.be.greaterThan(numberOfProductsAfterFilter);

      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const price = await categoryPage.getProductPrice(page, i);
        expect(price).to.within(14, 39);
      }
    });
  });

  describe('Filter products list by Dimension', async () => {
    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters3', baseContext);

      const isActiveFilterNotVisible = await categoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts5', baseContext);

      const productsNumberAfterClearFilter = await categoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });

    it('should filter products by Dimension \'40x60cm -  60x90cm\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDimension', baseContext);

      await categoryPage.filterByCheckbox(page, 'attribute_group', 'Dimension-40x60cm', true);
      await categoryPage.filterByCheckbox(page, 'attribute_group', 'Dimension-40x60cm-60x90cm', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters7', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Dimension: 40x60cm')
        .and.to.contains('Dimension: 60x90cm');
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList4', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['dimension-40x60cm', 'dimension-60x90cm']);
      }
    });
  });

  describe('Filter products list by Availability', async () => {
    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters6', baseContext);

      const isActiveFilterNotVisible = await categoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts6', baseContext);

      const productsNumberAfterClearFilter = await categoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });

    it('should filter products by availability \'In Stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByAvailability', baseContext);

      await categoryPage.filterByCheckbox(page, 'availability', '\'Availability-In+stock\'', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters8', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Availability: In stock');
    });
  });

  describe('Filter products list by Paper Type', async () => {
    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters4', baseContext);

      const isActiveFilterNotVisible = await categoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts7', baseContext);

      const productsNumberAfterClearFilter = await categoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });

    it('should filter products by paper type \'Ruled - Plain - Squared\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByPaperType', baseContext);

      await categoryPage.filterByCheckbox(page, 'attribute_group', '\'Paper+Type-Ruled\'', true);
      await categoryPage.filterByCheckbox(page, 'attribute_group', '\'Paper+Type-Ruled-Plain\'', true);
      await categoryPage.filterByCheckbox(page, 'attribute_group', '\'Paper+Type-Ruled-Plain-Squared\'', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters9', baseContext);

      const activeFilters = await categoryPage.getActiveFilters(page);
      expect(activeFilters).to.contains('Paper Type: Ruled')
        .and.to.contains('Paper Type: Plain')
        .and.to.contains('Paper Type: Squared');
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList5', baseContext);

      productsNumber = await categoryPage.getNumberOfProducts(page);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPage.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['paper_type-ruled', 'paper_type-plain', 'paper_type-squared']);
      }
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters5', baseContext);

      const isActiveFilterNotVisible = await categoryPage.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts8', baseContext);

      const productsNumberAfterClearFilter = await categoryPage.getNumberOfProducts(page);
      expect(productsNumberAfterClearFilter).to.be.equal(numberOfActiveProducts);
    });
  });

  // Post-condition : Reset number of products per page
  describe('POST-TEST : Reset the number of products per page', async () => {
    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFo', baseContext);

      page = await categoryPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the value of products per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ResetProductPerPage', baseContext);

      const result = await productSettingsPage.setProductsDisplayedPerPage(page, 12);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  //uninstallHummingbird(`${baseContext}_postTest`);
});
