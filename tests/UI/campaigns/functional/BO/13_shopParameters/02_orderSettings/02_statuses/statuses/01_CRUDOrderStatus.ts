import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBasePage,
  boOrderSettingsPage,
  boOrderStatusesPage,
  boOrderStatusesCreatePage,
  type BrowserContext,
  FakerOrderStatus,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

    const pageTitle = await boOrderStatusesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderStatusesPage.pageTitle);
  });

  it('should reset all filters and get number of order statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderStatuses = await boOrderStatusesPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfOrderStatuses).to.be.above(0);
  });

  // 1 - Create order status
  describe('Create order status', async () => {
    it('should go to add new order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToboOrderStatusesCreatePage', baseContext);

      await boOrderStatusesPage.goToNewOrderStatusPage(page);

      const pageTitle = await boOrderStatusesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderStatusesCreatePage.pageTitleCreate);
    });

    it('should create order status and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderStatus', baseContext);

      const textResult = await boOrderStatusesCreatePage.setOrderStatus(page, createOrderStatusData);
      expect(textResult).to.contains(boOrderStatusesPage.successfulCreationMessage);

      const numberOfLinesAfterCreation = await boOrderStatusesPage.getNumberOfElementInGrid(page, tableName);
      expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderStatuses + 1);
    });
  });

  // 2 - Check the new status in order page
  describe('Check the existence of the new status in the order page', async () => {
    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boOrderStatusesPage.goToSubMenu(
        page,
        boOrderStatusesPage.ordersParentLink,
        boOrderStatusesPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });

    it(`should check if the order status '${createOrderStatusData.name}' is visible`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDoesStatusVisible', baseContext);

      const isStatusExist = await boOrdersViewBasePage.doesStatusExist(page, createOrderStatusData.name);
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

      const pageTitle = await boOrderStatusesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderStatusesPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boOrderStatusesPage.resetFilter(page, tableName);
      await boOrderStatusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        createOrderStatusData.name,
      );

      const textEmail = await boOrderStatusesPage.getTextColumn(page, tableName, 1, 'name');
      expect(textEmail).to.contains(createOrderStatusData.name);
    });

    it('should go to edit order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditOrderStatusPage', baseContext);

      await boOrderStatusesPage.goToEditPage(page, tableName, 1);

      const pageTitle = await boOrderStatusesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderStatusesCreatePage.pageTitleEdit(createOrderStatusData.name));
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await boOrderStatusesCreatePage.setOrderStatus(page, editOrderStatusData);
      expect(textResult).to.contains(boOrderStatusesPage.successfulUpdateMessage);

      const numberOfOrderStatusesAfterUpdate = await boOrderStatusesPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfOrderStatusesAfterUpdate).to.be.equal(numberOfOrderStatuses + 1);
    });
  });

  // 4 - Delete order status
  describe('Delete order status', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boOrderStatusesPage.resetFilter(page, tableName);
      await boOrderStatusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        editOrderStatusData.name,
      );

      const textEmail = await boOrderStatusesPage.getTextColumn(page, tableName, 1, 'name');
      expect(textEmail).to.contains(editOrderStatusData.name);
    });

    it('should delete order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderStatus', baseContext);

      const textResult = await boOrderStatusesPage.deleteOrderStatus(page, tableName, 1);
      expect(textResult).to.contains(boOrderStatusesPage.successfulDeleteMessage);

      const numberOfOrderStatusesAfterDelete = await boOrderStatusesPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfOrderStatusesAfterDelete).to.be.equal(numberOfOrderStatuses);
    });
  });
});
