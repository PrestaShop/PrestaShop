// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import carriersPage from '@pages/BO/shipping/carriers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shipping_carriers_changePosition';

/*
Go to carriers page
Change first carrier position to 3
Reset carrier position
 */
describe('BO - Shipping - Carriers : Change carrier position', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.carriersLink,
    );

    const pageTitle = await carriersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(carriersPage.pageTitle);
  });

  describe('Change carrier position', async () => {
    // Should reset filters and sort by position before changing position
    it('should reset all filters and get number of carriers in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCarriersFilters', baseContext);

      const numberOfCarriers = await carriersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCarriers).to.be.above(2);
    });

    it('should sort by \'position\' \'asc\' And check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sortByPosition', baseContext);

      let nonSortedTable = await carriersPage.getAllRowsColumnContent(page, 'a!position');

      await carriersPage.sortTable(page, 'a!position', 'up');

      let sortedTable = await carriersPage.getAllRowsColumnContent(page, 'a!position');

      nonSortedTable = await nonSortedTable.map((text: string): number => parseFloat(text));
      sortedTable = await sortedTable.map((text: string): number => parseFloat(text));

      const expectedResult = await basicHelper.sortArrayNumber(nonSortedTable);

      await expect(sortedTable).to.deep.equal(expectedResult);
    });

    it('should change first carrier position to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCarrierPosition', baseContext);

      // Get first row carrier name
      const firstRowCarrierName = await carriersPage.getTextColumn(page, 1, 'name');

      // Change position and check successful message
      const textResult = await carriersPage.changePosition(page, 1, 3);
      await expect(textResult, 'Unable to change position').to.contains(carriersPage.successfulUpdateMessage);

      // Get third row carrier name and check if is equal the first row carrier name before changing position
      const thirdRowCarrierName = await carriersPage.getTextColumn(page, 3, 'name');
      await expect(thirdRowCarrierName, 'Changing position was done wrongly').to.equal(firstRowCarrierName);
    });

    it('should reset third carrier position to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCarrierPosition', baseContext);

      // Get third row carrier name
      const thirdRowCarrierName = await carriersPage.getTextColumn(page, 3, 'name');

      // Change position and check successful message
      const textResult = await carriersPage.changePosition(page, 3, 1);
      await expect(textResult, 'Unable to change position').to.contains(carriersPage.successfulUpdateMessage);

      // Get first row carrier name and check if is equal the first row carrier name before changing position
      const firstRowCarrierName = await carriersPage.getTextColumn(page, 1, 'name');
      await expect(firstRowCarrierName, 'Changing position was done wrongly').to.equal(thirdRowCarrierName);
    });
  });
});
