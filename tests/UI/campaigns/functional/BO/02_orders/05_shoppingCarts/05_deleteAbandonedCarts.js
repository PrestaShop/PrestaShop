// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createShoppingCart} = require('@commonTests/FO/createShoppingCart');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');

const baseContext = 'functional_BO_orders_shoppingCarts_deleteAbandonedCarts';

let browserContext;
let page;
let numberOfShoppingCarts;
let numberOfShoppingCartsAfterFilter;

const orderByCustomerData = {
  customer: DefaultCustomer,
  product: Products.demo_1,
  productQuantity: 1,
};

/*
Pre-condition:
- creat shopping cart from FO
Scenario:
- Filter shopping carts by non ordered
- Delete them
 */

describe('BO - Orders : Create shopping cart and delete abandoned one', async () => {
  // Pre-condition: Create 1 order in FO
  createShoppingCart(orderByCustomerData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Delete abandoned carts', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'status', 'Non ordered');

      numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfShoppingCartsAfterFilter).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfShoppingCartsAfterFilter;

      for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'status');
        await expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCarts', baseContext);

      const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
      await expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });
});
