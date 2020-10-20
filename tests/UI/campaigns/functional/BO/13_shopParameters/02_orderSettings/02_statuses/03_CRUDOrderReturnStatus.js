require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const statusesPage = require('@pages/BO/shopParameters/orderSettings/statuses');
const addOrderReturnStatusPage = require('@pages/BO/shopParameters/orderSettings/statuses/returnStatus/add');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import data
const OrderReturnStatusFaker = require('@data/faker/orderStatus');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_CRUDOrderReturnStatus';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfOrderReturnStatuses = 0;
const tableName = 'order_return';

const createOrderReturnStatusData = new OrderReturnStatusFaker();
const editOrderStatusData = new OrderReturnStatusFaker({name: `edit_${createOrderReturnStatusData.name}`});

/*
Create new order return status
View new return status in order page
Update order return status
Delete order return status
 */
describe('Create, read, update and delete order return status in BO', async () => {
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

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.orderSettingsLink,
    );

    const pageTitle = await orderSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
  });

  it('should go to \'Statuses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPage', baseContext);

    await orderSettingsPage.goToStatusesPage(page);

    const pageTitle = await statusesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(statusesPage.pageTitle);
  });

  it('should reset all filters and get number of order statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderReturnStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfOrderReturnStatuses).to.be.above(0);
  });

  // 1 - Create order return status
  describe('Create order status', async () => {
    it('should go to add new order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddOrderReturnStatusPage', baseContext);

      await statusesPage.goToNewOrderReturnStatusPage(page);

      const pageTitle = await addOrderReturnStatusPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderReturnStatusPage.pageTitleCreate);
    });

    it('should create order status and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderStatus', baseContext);

      const textResult = await addOrderReturnStatusPage.setOrderStatus(page, createOrderReturnStatusData);
      await expect(textResult).to.contains(statusesPage.successfulCreationMessage);

      const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
      await expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderReturnStatuses + 1);
    });
  });

  /*// 2 - Check the new status in order page
  describe('Check the existence of the new status in the order page', async () => {
    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await statusesPage.goToSubMenu(
        page,
        statusesPage.ordersParentLink,
        statusesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should check if the order status '${createOrderReturnStatusData.name}' is visible`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDoesStatusVisible', baseContext);

      const isStatusExist = await viewOrderPage.doesStatusExist(page, createOrderReturnStatusData.name);
      await expect(isStatusExist, 'Status does not exist').to.be.true;
    });
  });

  // 3 - Update order status
  describe('Update order status created', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPageToUpdate', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.orderSettingsLink,
      );

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should go to \'Statuses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPageToUpdate', baseContext);

      await orderSettingsPage.goToStatusesPage(page);

      const pageTitle = await statusesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(statusesPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await statusesPage.resetFilter(page);

      await statusesPage.filterTable(
        page,
        'input',
        'name',
        createOrderReturnStatusData.name,
      );

      const textEmail = await statusesPage.getTextColumn(page, 1, 'name', 3);
      await expect(textEmail).to.contains(createOrderReturnStatusData.name);
    });

    it('should go to edit order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditOrderStatusPage', baseContext);

      await statusesPage.gotoEditOrderStatusPage(page, 1);

      const pageTitle = await addOrderReturnStatusPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderReturnStatusPage.pageTitleEdit);
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await addOrderReturnStatusPage.setOrderStatus(page, editOrderStatusData);
      await expect(textResult).to.contains(statusesPage.successfulUpdateMessage);

      const numberOfOrderReturnStatusesAfterUpdate = await statusesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrderReturnStatusesAfterUpdate).to.be.equal(numberOfOrderReturnStatuses + 1);
    });
  });

  // 4 - Delete order status
  describe('Delete order status', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await statusesPage.resetFilter(page);

      await statusesPage.filterTable(
        page,
        'input',
        'name',
        editOrderStatusData.name,
      );

      const textEmail = await statusesPage.getTextColumn(page, 1, 'name', 3);
      await expect(textEmail).to.contains(editOrderStatusData.name);
    });

    it('should delete order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderStatus', baseContext);

      const textResult = await statusesPage.deleteOrderStatus(page, 1);
      await expect(textResult).to.contains(statusesPage.successfulDeleteMessage);

      const numberOfOrderReturnStatusesAfterDelete = await statusesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrderReturnStatusesAfterDelete).to.be.equal(numberOfOrderReturnStatuses);
    });
  });*/
});
