// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

/**
 * Function to delete non-ordered shopping carts
 * @param baseContext {string} String to identify the test
 */
function deleteNonOrderedShoppingCarts(baseContext: string = 'commonTests-deleteNonOrderedShoppingCartsTest'): void {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfShoppingCarts: number = 0;
  let numberOfNonOrderedShoppingCarts: number = 0;

  describe('PRE-TEST: Delete the non-ordered shopping carts', async () => {
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

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst1', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search for the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts1', baseContext);

      await shoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'status');
        expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists1', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts1', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });
}

export default deleteNonOrderedShoppingCarts;
