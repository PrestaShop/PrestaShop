// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import createShoppingCart from '@commonTests/FO/classic/shoppingCart';

import {
  boDashboardPage,
  boLoginPage,
  boShoppingCartsPage,
  type BrowserContext,
  dataCustomers,
  dataProducts,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_orders_shoppingCarts_deleteAbandonedCarts';

/*
Pre-condition:
- creat shopping cart from FO
Scenario:
- Filter shopping carts by non ordered
- Delete them
 */

describe('BO - Orders : Create shopping cart and delete abandoned one', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfShoppingCarts: number;
  let numberOfShoppingCartsAfterFilter: number;

  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
  });

  // Pre-condition: Create 1 order in FO
  createShoppingCart(orderByCustomerData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Delete abandoned carts', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.shoppingCartsLink,
      );

      const pageTitle = await boShoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfShoppingCarts = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts', baseContext);

      await boShoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

      numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfShoppingCartsAfterFilter;

      for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
        const textColumn = await boShoppingCartsPage.getTextColumn(page, row, 'status');
        expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCarts', baseContext);

      const deleteTextResult = await boShoppingCartsPage.bulkDeleteShoppingCarts(page);
      expect(deleteTextResult).to.be.contains(boShoppingCartsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts', baseContext);

      const numberOfShoppingCartsAfterReset = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
      expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });
});
