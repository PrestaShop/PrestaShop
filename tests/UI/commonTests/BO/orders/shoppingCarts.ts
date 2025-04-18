// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boShoppingCartsPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.shoppingCartsLink,
      );

      const pageTitle = await boShoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst1', baseContext);

      numberOfShoppingCarts = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search for the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts1', baseContext);

      await boShoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await boShoppingCartsPage.getTextColumn(page, row, 'status');
        expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists1', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await boShoppingCartsPage.bulkDeleteShoppingCarts(page);
        expect(deleteTextResult).to.be.contains(boShoppingCartsPage.successfulDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts1', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });
}

export default deleteNonOrderedShoppingCarts;
