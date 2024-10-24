// Import utils
import testContext from '@utils/testContext';

// Import pages
import statusesPage from '@pages/BO/shopParameters/orderSettings/statuses';
import addOrderStatusPage from '@pages/BO/shopParameters/orderSettings/statuses/add';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrderSettingsPage,
  type BrowserContext,
  FakerOrderStatus,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_statuses_statuses_CRUDOrderStatus';

/*
Create new order status
View new status in order page
Update order status
Delete order status
 */
describe('BO - Shop Parameters - Order Settings - Statuses : CRUD order status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrderStatuses: number = 0;

  const tableName: string = 'order';
  const createOrderStatusData: FakerOrderStatus = new FakerOrderStatus();
  const editOrderStatusData: FakerOrderStatus = new FakerOrderStatus({name: `edit_${createOrderStatusData.name}`});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(`${createOrderStatusData.name}.jpg`),
      utilsFile.generateImage(`${editOrderStatusData.name}.jpg`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      utilsFile.deleteFile(`${createOrderStatusData.name}.jpg`),
      utilsFile.deleteFile(`${editOrderStatusData.name}.jpg`),
    ]);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.orderSettingsLink,
    );

    const pageTitle = await boOrderSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
  });

  it('should go to \'Statuses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPage', baseContext);

    await boOrderSettingsPage.goToStatusesPage(page);

    const pageTitle = await statusesPage.getPageTitle(page);
    expect(pageTitle).to.contains(statusesPage.pageTitle);
  });

  it('should reset all filters and get number of order statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfOrderStatuses).to.be.above(0);
  });

  // 1 - Create order status
  describe('Create order status', async () => {
    it('should go to add new order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToaddOrderStatusPage', baseContext);

      await statusesPage.goToNewOrderStatusPage(page);

      const pageTitle = await addOrderStatusPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderStatusPage.pageTitleCreate);
    });

    it('should create order status and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderStatus', baseContext);

      const textResult = await addOrderStatusPage.setOrderStatus(page, createOrderStatusData);
      expect(textResult).to.contains(statusesPage.successfulCreationMessage);

      const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
      expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderStatuses + 1);
    });
  });

  // 2 - Check the new status in order page
  describe('Check the existence of the new status in the order page', async () => {
    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await statusesPage.goToSubMenu(
        page,
        statusesPage.ordersParentLink,
        statusesPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
    });

    it(`should check if the order status '${createOrderStatusData.name}' is visible`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDoesStatusVisible', baseContext);

      const isStatusExist = await viewOrderBasePage.doesStatusExist(page, createOrderStatusData.name);
      expect(isStatusExist, 'Status does not exist').to.eq(true);
    });
  });

  // 3 - Update order status
  describe('Update order status created', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPageToUpdate', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.orderSettingsLink,
      );

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });

    it('should go to \'Statuses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPageToUpdate', baseContext);

      await boOrderSettingsPage.goToStatusesPage(page);

      const pageTitle = await statusesPage.getPageTitle(page);
      expect(pageTitle).to.contains(statusesPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await statusesPage.resetFilter(page, tableName);
      await statusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        createOrderStatusData.name,
      );

      const textEmail = await statusesPage.getTextColumn(page, tableName, 1, 'name');
      expect(textEmail).to.contains(createOrderStatusData.name);
    });

    it('should go to edit order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditOrderStatusPage', baseContext);

      await statusesPage.goToEditPage(page, tableName, 1);

      const pageTitle = await addOrderStatusPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderStatusPage.pageTitleEdit(createOrderStatusData.name));
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await addOrderStatusPage.setOrderStatus(page, editOrderStatusData);
      expect(textResult).to.contains(statusesPage.successfulUpdateMessage);

      const numberOfOrderStatusesAfterUpdate = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfOrderStatusesAfterUpdate).to.be.equal(numberOfOrderStatuses + 1);
    });
  });

  // 4 - Delete order status
  describe('Delete order status', async () => {
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

      const textEmail = await statusesPage.getTextColumn(page, tableName, 1, 'name');
      expect(textEmail).to.contains(editOrderStatusData.name);
    });

    it('should delete order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderStatus', baseContext);

      const textResult = await statusesPage.deleteOrderStatus(page, tableName, 1);
      expect(textResult).to.contains(statusesPage.successfulDeleteMessage);

      const numberOfOrderStatusesAfterDelete = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfOrderStatusesAfterDelete).to.be.equal(numberOfOrderStatuses);
    });
  });
});
