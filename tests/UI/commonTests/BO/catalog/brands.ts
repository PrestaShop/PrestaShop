import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boBrandsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to delete brand
 * @param brandName {string}
 * @param baseContext {string} String to identify the test
 */
function opsBulkDeleteBrands(brandName: string, baseContext: string = 'commonTests-opsDeleteBrand'): void {
  describe('Delete brand', async () => {
    let numberOfBrands: number = 0;
    let numberOfBrandsToDelete: number = 0;

    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.brandsAndSuppliersLink,
      );
      await boBrandsPage.closeSfToolBar(page);

      const pageTitle = await boBrandsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boBrandsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBefore', baseContext);

      numberOfBrands = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
      expect(numberOfBrands).to.gt(0);
    });

    it(`should filter by brand name '${brandName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await boBrandsPage.filterBrands(page, 'input', 'name', brandName);

      numberOfBrandsToDelete = await boBrandsPage.getNumberOfElementInGrid(page, 'manufacturer');
      expect(numberOfBrandsToDelete).to.be.above(0);
    });

    it('should delete brands by Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteFeatures', baseContext);

      const textResult = await boBrandsPage.deleteWithBulkActions(page, 'manufacturer');
      expect(textResult).to.be.contains(boBrandsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfter', baseContext);

      const numberOfBrandsAfterDelete = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
      expect(numberOfBrandsAfterDelete).to.equal(numberOfBrands - numberOfBrandsToDelete);
    });
  });
}

export default opsBulkDeleteBrands;
