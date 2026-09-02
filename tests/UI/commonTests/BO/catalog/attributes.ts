import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

/**
 * Function to delete attribute
 * @param attributeName {string}
 * @param baseContext {string} String to identify the test
 */
function opsBulkDeleteAttributes(attributeName: string, baseContext: string = 'commonTests-opsDeleteAttribute'): void {
  describe('Delete attribute', async () => {
    let browserContext: BrowserContext;
    let page: Page;
    let numberOfAttributes: number = 0;
    let numberOfAttributesToDelete: number = 0;

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

    it('should go to \'Catalog > Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.attributesAndFeaturesLink,
      );
      await boAttributesPage.closeSfToolBar(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBefore', baseContext);

      numberOfAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.gt(0);
    });

    it(`should filter by attribute name '${attributeName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await boAttributesPage.filterTable(page, 'name', attributeName);

      numberOfAttributesToDelete = await boAttributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesToDelete).to.be.above(0);
    });

    it('should delete attributes by Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteFeatures', baseContext);

      const textResult = await boAttributesPage.bulkDeleteAttributes(page);
      expect(textResult).to.be.contains(boAttributesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfter', baseContext);

      const numberOfAttributesAfterDelete = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes - numberOfAttributesToDelete);
    });
  });
}

export default opsBulkDeleteAttributes;
