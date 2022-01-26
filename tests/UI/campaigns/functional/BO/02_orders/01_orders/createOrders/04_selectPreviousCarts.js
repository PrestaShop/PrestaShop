require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages

const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_createOrders_selectPreviousCarts';

// Import expect rom chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfShoppingCarts;
let numberOfNonOrderedShoppingCarts;

/*
Go to create Order page
Search and choose a customer
Select Previous Cart
 */

describe('BO - Orders : Create Order - Select Previous Carts', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async function () {
    browserContext = await helper.closeBrowserContext(this.browser);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('PRE-TEST: Delete the Non ordered shopping carts', async () => {
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

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        // when we have a shopping cart non ordered (checkbox exists),
        // the column_id became column_id+1 = column of the lastname
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'c!lastname');
        await expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        await expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulMultiDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });

  describe('Check that no records found of carts on create order BO for the default customer', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testidentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);
      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it('should search for default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard', baseContext);

      await addOrderPage.searchCustomer(page, DefaultCustomer.email);

      const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
      await expect(customerName).to.contains(`${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`);
    });

    it('should click on the choose button of the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnChooseButton', baseContext);

      const isBlockHistoryVisible = await addOrderPage.chooseCustomer(page);
      await expect(isBlockHistoryVisible).to.be.true;
    });

    it('should check that no records found in the carts section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFoundForCarts', baseContext);

      const noRecordsFoundText = await addOrderPage.getTextColumnFromCartsTable(page);
      await expect(noRecordsFoundText).to.contains('No records found');
    });
  });
});
