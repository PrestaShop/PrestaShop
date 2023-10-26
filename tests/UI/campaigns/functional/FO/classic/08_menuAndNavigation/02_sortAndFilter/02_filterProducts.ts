// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import categoryPageFO from '@pages/FO/category';
import {homePage} from '@pages/FO/home';
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_sortAndFilter_filterProducts';

/*
Pre-condition:
- Disable new product page
- Get the number of active products
- Change the number of products per page
Scenario:
- Filter products
Post-condition:
- Reset the number of products per page
- Enable new product page
 */
describe('FO - Menu and navigation : Filter products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;
  let productsNumber: number;

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

  /*  it('should go to \'Catalog > Products\' page', async function () {
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
    });*/
  });

 /* // Pre-condition : Change the product per page by the number of all products
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
  });*/

  // Filter products by Categories, size, color, composition, property, availability, brand, price, dimension & paper type
 /* describe('Filter products list by Category', async () => {
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

      const isCategoryPageVisible = await categoryPageFO.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should filter products by category \'Accessories - Art\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCategory', baseContext);

      await categoryPageFO.filterByCheckbox(page, 'category', 'Accessories', true);
      await categoryPageFO.filterByCheckbox(page, 'category', 'Art', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters', baseContext);

      const activeFilters = await categoryPageFO.getActiveFilters(page);
      expect(activeFilters).to.contains('Categories: Accessories')
        .and.to.contains('Categories: Art');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);

      productsNumber = await categoryPageFO.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPageFO.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['accessories', 'art', 'stationery']);
      }
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters', baseContext);

      const isActiveFilterNotVisible = await categoryPageFO.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of the displayed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProducts', baseContext);

      const numberOfProducts = await categoryPageFO.getNumberOfProducts(page);
      expect(numberOfProducts).to.equal(numberOfActiveProducts);
    });
  });

  describe('Filter products list by Size', async () => {
    it('should filter products by size \'S-L-XL\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBySize', baseContext);

      await categoryPageFO.filterByCheckbox(page, 'attribute_group', 'Size-S', true);
      await categoryPageFO.filterByCheckbox(page, 'attribute_group', 'Size-S-L', true);
      await categoryPageFO.filterByCheckbox(page, 'attribute_group', 'Size-S-L-XL', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters2', baseContext);

      const activeFilters = await categoryPageFO.getActiveFilters(page);
      expect(activeFilters).to.contains('Size: S')
        .and.to.contains('Size: L')
        .and.to.contains('Size: XL');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts2', baseContext);

      productsNumber = await categoryPageFO.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList2', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPageFO.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['size-s', 'size-l', 'size-xl']);
      }
    });
  });

  describe('Filter products list by Color', async () => {
    it('should filter products by Color \'Black\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByColor', baseContext);

      await categoryPageFO.filterByCheckbox(page, 'attribute_group', 'Color-Black', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters2', baseContext);

      const activeFilters = await categoryPageFO.getActiveFilters(page);
      expect(activeFilters).to.contains('Size: S')
        .and.to.contains('Size: L')
        .and.to.contains('Size: XL')
        .and.to.contains('Color: Black');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts3', baseContext);

      productsNumber = await categoryPageFO.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(0);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList3', baseContext);

      for (let i = 1; i <= productsNumber; i++) {
        const productURL = await categoryPageFO.getProductHref(page, i);
        expect(productURL).to.contain.oneOf(['size-s', 'size-l', 'size-xl', 'color-black']);
      }
    });

    it('should clear all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clearAllFilters2', baseContext);

      const isActiveFilterNotVisible = await categoryPageFO.clearAllFilters(page);
      expect(isActiveFilterNotVisible).to.eq(true);
    });

    it('should check the number of the displayed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayedProducts2', baseContext);

      const numberOfProducts = await categoryPageFO.getNumberOfProducts(page);
      expect(numberOfProducts).to.equal(numberOfActiveProducts);
    });
  });

  describe('Filter products list by Composition', async () => {
    it('should filter products by composition \'Ceramic - Cotton - Recycled cardboard\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByComposition', baseContext);

      await categoryPageFO.filterByCheckbox(page, 'feature', 'Composition-Ceramic', true);
      await categoryPageFO.filterByCheckbox(page, 'feature', 'Composition-Ceramic-Cotton', true);
      await categoryPageFO.filterByCheckbox(page, 'feature', '\'Composition-Ceramic-Cotton-Recycled+cardboard\'', true);
    });

    it('should check the active filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getActiveFilters3', baseContext);

      const activeFilters = await categoryPageFO.getActiveFilters(page);
      expect(activeFilters).to.contains('Composition: Ceramic')
        .and.to.contains('Composition: Cotton')
        .and.to.contains('Composition: Recycled cardboard');
    });

    it('should get the number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts4', baseContext);

      productsNumber = await categoryPageFO.getNumberOfProducts(page);
      expect(productsNumber).to.be.above(1);
    });
  });*/

  describe('Filter products list by Price', async () => {
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

      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPageFO.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should filter products by price \'€14.00 - €20.00\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByPrice', baseContext);

      await categoryPageFO.filterByPrice(page);
    });
  });

  /*// Post-condition : Reset product per page by the number of all products
  describe('POST-TEST : Reset the number of products per page', async () => {
    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFo', baseContext);

      page = await categoryPageFO.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the value of products per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ResetProductPerPage', baseContext);

      const result = await productSettingsPage.setProductsDisplayedPerPage(page, 12);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });*/
});
