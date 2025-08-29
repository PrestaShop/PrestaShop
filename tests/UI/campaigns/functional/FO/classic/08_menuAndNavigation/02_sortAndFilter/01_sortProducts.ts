// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductSettingsPage,
  type BrowserContext,
  foClassicCategoryPage,
  foClassicHomePage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_sortAndFilter_sortProducts';

/*
Pre-condition:
- Disable new product page
- Get the number of active products
- Change the number of products per page
Scenario:
- Sort products list by all options
Post-condition:
- Reset the number of products per page
- Enable new product page
 */
describe('FO - Menu and navigation : Sort products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfActiveProducts: number;

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

  // Sort products by name, price
  describe('Sort products list', async () => {
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

    it('should check that the products as sorted by relevance', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultSort', baseContext);

      const isSortingLinkVisible = await foClassicCategoryPage.getSortByValue(page);
      expect(isSortingLinkVisible).to.contain('Relevance');
    });

    const tests = [
      {
        args: {
          testIdentifier: 'sortByNameAsc',
          sortName: 'Name, A to Z',
          attribute: 'title',
          sortBy: 'product.name.asc',
          sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc',
          sortName: 'Name, Z to A',
          attribute: 'title',
          sortBy: 'product.name.desc',
          sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPriceAsc',
          sortName: 'Price, low to high',
          attribute: 'price-and-shipping',
          sortBy: 'product.price.asc',
          sortDirection: 'asc',
        },
      },
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/19810
      /* {
         args: {
           testIdentifier: 'sortByPriceDesc',
           sortName: 'Price, high to low',
           attribute: 'price-and-shipping',
           sortBy: 'product.price.desc',
           sortDirection: 'desc',
         },
       },*/
    ];
    tests.forEach((test) => {
      it(`should sort by '${test.args.sortName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await foClassicCategoryPage.getAllProductsAttribute(page, test.args.attribute);
        await foClassicCategoryPage.sortProductsList(page, test.args.sortBy);
        const sortedTable = await foClassicCategoryPage.getAllProductsAttribute(page, test.args.attribute);

        const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // Post-condition : Reset product per page by the number of all products
  describe('POST-TEST : Reset the number of products per page', async () => {
    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFo', baseContext);

      page = await foClassicCategoryPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should change the value of products per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ResetProductPerPage', baseContext);

      const result = await boProductSettingsPage.setProductsDisplayedPerPage(page, 12);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });
  });
});
