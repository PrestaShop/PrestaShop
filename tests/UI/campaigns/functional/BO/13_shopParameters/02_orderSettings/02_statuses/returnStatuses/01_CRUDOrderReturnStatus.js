require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const statusesPage = require('@pages/BO/shopParameters/orderSettings/statuses');
const addOrderReturnStatusPage = require('@pages/BO/shopParameters/orderSettings/statuses/returnStatus/add');

// Import data
const OrderReturnStatusFaker = require('@data/faker/orderReturnStatus');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_returnStatuses_CRUDOrderReturnStatus';

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
Update order return status
Delete order return status
 */
describe('Create, update and delete order return status in BO', async () => {
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

  it('should reset all filters and get number of order return statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderReturnStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfOrderReturnStatuses).to.be.above(0);
  });

  // 1 - Create order return status
  describe('Create order return status', async () => {
    it('should go to add new order return status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddOrderReturnStatusPage', baseContext);

      await statusesPage.goToNewOrderReturnStatusPage(page);

      const pageTitle = await addOrderReturnStatusPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderReturnStatusPage.pageTitleCreate);
    });

    it('should create order return status and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderStatus', baseContext);

      await addOrderReturnStatusPage.setOrderReturnStatus(page, createOrderReturnStatusData);

      /* Successful message is not visible, skipping it */
      /* https://github.com/PrestaShop/PrestaShop/issues/21270 */
      // const textResult = await addOrderReturnStatusPage.setOrderStatus(page, createOrderReturnStatusData);

      // await expect(textResult).to.contains(statusesPage.successfulCreationMessage);

      const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
      await expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderReturnStatuses + 1);
    });
  });

  // 2 - Update order return status
  describe('Update order return status created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await statusesPage.resetFilter(page, tableName);

      await statusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        createOrderReturnStatusData.name,
      );

      const textEmail = await statusesPage.getTextColumn(page, tableName, 1, 'name', 3);
      await expect(textEmail).to.contains(createOrderReturnStatusData.name);
    });

    it('should go to edit order return status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditOrderReturnStatusPage', baseContext);

      await statusesPage.gotoEditPage(page, tableName, 1);

      const pageTitle = await addOrderReturnStatusPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderReturnStatusPage.pageTitleEdit);
    });

    it('should update order return status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderReturnStatus', baseContext);

      const textResult = await addOrderReturnStatusPage.setOrderReturnStatus(page, editOrderStatusData);
      await expect(textResult).to.contains(statusesPage.successfulUpdateMessage);

      const numberOfOrderReturnStatusesAfterUpdate = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      await expect(numberOfOrderReturnStatusesAfterUpdate).to.be.equal(numberOfOrderReturnStatuses + 1);
    });
  });

  // 3 - Delete order return status
  describe('Delete order return status', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await statusesPage.resetFilter(page, tableName);

      await statusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        editOrderStatusData.name,
      );

      const textEmail = await statusesPage.getTextColumn(page, tableName, 1, 'name', 3);
      await expect(textEmail).to.contains(editOrderStatusData.name);
    });

    it('should delete order return status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderStatus', baseContext);

      const textResult = await statusesPage.deleteOrderStatus(page, tableName, 1);
      await expect(textResult).to.contains(statusesPage.successfulDeleteMessage);

      const numberOfOrderReturnStatusesAfterDelete = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      await expect(numberOfOrderReturnStatusesAfterDelete).to.be.equal(numberOfOrderReturnStatuses);
    });
  });
});
