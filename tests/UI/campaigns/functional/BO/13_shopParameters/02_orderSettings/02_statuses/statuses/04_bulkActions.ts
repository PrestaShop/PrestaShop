// Import utils
import testContext from '@utils/testContext';

// Import pages
import statusesPage from '@pages/BO/shopParameters/orderSettings/statuses';
import addOrderStatusPage from '@pages/BO/shopParameters/orderSettings/statuses/add';

import {
  boDashboardPage,
  boLoginPage,
  boOrderSettingsPage,
  type BrowserContext,
  FakerOrderStatus,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_statuses_statuses_bulkActions';

/*
Create 2 order statuses
Delete by bulk actions
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Bulk actions in order statuses table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrderStatuses: number = 0;

  const tableName: string = 'order';

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    /* Delete the generated images */
    for (let i = 0; i <= 2; i++) {
      await utilsFile.deleteFile(`todelete${i}.jpg`);
    }
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

  it('should change the items number to 20 per page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

    const paginationNumber = await statusesPage.selectPaginationLimit(page, tableName, 20);
    expect(paginationNumber).to.contains('(page 1 / 1)');
  });

  // 1 - Create 2 order statuses
  [1, 2].forEach((test: number, index: number) => {
    describe(`Create order status n°${index + 1} in BO`, async () => {
      before(() => utilsFile.generateImage(`todelete${index}.jpg`));

      const orderStatusData: FakerOrderStatus = new FakerOrderStatus({name: `todelete${index}`});

      it('should go to add new order status group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddOrderStatusPage${index}`, baseContext);

        await statusesPage.goToNewOrderStatusPage(page);

        const pageTitle = await addOrderStatusPage.getPageTitle(page);
        expect(pageTitle).to.contains(addOrderStatusPage.pageTitleCreate);
      });

      it('should create order status and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderStatus${index}`, baseContext);

        const textResult = await addOrderStatusPage.setOrderStatus(page, orderStatusData);
        expect(textResult).to.contains(statusesPage.successfulCreationMessage);

        const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
        expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderStatuses + index + 1);
      });

      after(() => utilsFile.deleteFile(`todelete${index}.jpg`));
    });
  });

  // 2 : Delete order statuses created with bulk actions
  describe('Delete order statuses with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await statusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        'todelete',
      );

      const numberOfLinesAfterFilter = await statusesPage.getNumberOfElementInGrid(page, tableName);

      for (let i = 1; i <= numberOfLinesAfterFilter; i++) {
        const textColumn = await statusesPage.getTextColumn(page, tableName, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete order statuses with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStatus', baseContext);

      const deleteTextResult = await statusesPage.bulkDeleteOrderStatuses(page, tableName);
      expect(deleteTextResult).to.be.contains(statusesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfLinesAfterReset).to.be.equal(numberOfOrderStatuses);
    });
  });
});
