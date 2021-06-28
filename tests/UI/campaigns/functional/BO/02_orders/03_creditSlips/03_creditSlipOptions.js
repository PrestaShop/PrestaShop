require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const creditSlipsPage = require('@pages/BO/orders/creditSlips/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_creditSlips_creditSlipOptions';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

let fileName;
const prefixToEdit = 'CreSlip';

/*
Edit credit slip prefix
Change the Order status to shipped
Check the credit slip file name
Delete the slip prefix value
Check the credit slip file name
 */
describe('BO - Orders - Credit slips : Credit slip options', async () => {
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

  describe(`Change the credit slip prefix to '${prefixToEdit}'`, async () => {
    it('should go to \'Orders > Credit slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.creditSlipsLink,
      );

      await creditSlipsPage.closeSfToolBar(page);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it(`should change the credit slip prefix to ${prefixToEdit}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePrefix', baseContext);

      await creditSlipsPage.changePrefix(page, prefixToEdit);
      const textMessage = await creditSlipsPage.saveCreditSlipOptions(page);
      await expect(textMessage).to.contains(creditSlipsPage.successfulUpdateMessage);
    });
  });

  describe('Check the new credit slip prefix', async () => {
    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await creditSlipsPage.goToSubMenu(
        page,
        creditSlipsPage.ordersParentLink,
        creditSlipsPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the last order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it(`should check that the credit slip file name contain the prefix '${prefixToEdit}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedPrefixOnFileName', baseContext);

      // Get file name
      fileName = await viewOrderPage.getFileName(page, 4);
      expect(fileName).to.contains(prefixToEdit);
    });
  });

  describe(`Back to the default credit slip prefix value '${prefixToEdit}'`, async () => {
    it('should go to \'Orders > Credit slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPageToResetPrefix', baseContext);

      await viewOrderPage.goToSubMenu(
        page,
        viewOrderPage.ordersParentLink,
        viewOrderPage.creditSlipsLink,
      );

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should delete the credit slip prefix', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePrefix', baseContext);

      await creditSlipsPage.changePrefix(page, ' ');

      const textMessage = await creditSlipsPage.saveCreditSlipOptions(page);
      await expect(textMessage).to.contains(creditSlipsPage.successfulUpdateMessage);
    });
  });

  describe('Check that the new prefix does not exist in the credit slip file name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageToCheckDeletedPrefix', baseContext);

      await creditSlipsPage.goToSubMenu(
        page,
        creditSlipsPage.ordersParentLink,
        creditSlipsPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderToCheckDeletedPrefix', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should check the credit slip file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedPrefix', baseContext);

      fileName = await viewOrderPage.getFileName(page, 4);
      expect(fileName).to.not.contains(prefixToEdit);
    });
  });
});
