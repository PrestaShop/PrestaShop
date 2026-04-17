// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataCategories,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'sanity_catalogFO_filterProducts';

/*
  Open the FO home page
  Get the product number
  Filter products by a category
  Filter products by a subcategory
 */
describe('FO - Catalog : Filter Products by categories in Home page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allProductsNumber: number = 0;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Catalog FO: Filter products from catalog', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should check and get the products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await foHummingbirdHomePage.goToAllProductsPage(page);

      allProductsNumber = await foHummingbirdCategoryPage.getProductsNumber(page);
      expect(allProductsNumber).to.be.above(0);
    });

    it('should filter products by the category \'Accessories\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'FilterProductByCategory', baseContext);

      await foHummingbirdCategoryPage.goToCategory(page, dataCategories.accessories.id);

      const pageTitle = await foHummingbirdCategoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.accessories.name);

      const numberOfProducts = await foHummingbirdCategoryPage.getProductsNumber(page);
      expect(numberOfProducts).to.be.below(allProductsNumber);
    });

    it('should filter products by the subcategory \'Stationery\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'FilterProductBySubCategory', baseContext);

      await foHummingbirdCategoryPage.reloadPage(page);
      await foHummingbirdCategoryPage.goToSubCategory(page, dataCategories.accessories.id, dataCategories.stationery.id);

      const numberOfProducts = await foHummingbirdCategoryPage.getProductsNumber(page);
      expect(numberOfProducts).to.be.below(allProductsNumber);
    });
  });
});
