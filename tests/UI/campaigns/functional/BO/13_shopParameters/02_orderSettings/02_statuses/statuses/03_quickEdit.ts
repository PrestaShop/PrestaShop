import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrderSettingsPage,
  boOrderStatusesPage,
  type BrowserContext,
  dataOrderStatuses,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_statuses_statuses_quickEdit';

/*
Quick edit send email to customer
Quick edit delivery
Quick edit invoice
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Quick edit order status in BO', async () => {
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

  it('should filter by status name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByName', baseContext);

    numberOfOrderStatuses = await boOrderStatusesPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfOrderStatuses).to.be.above(0);

    await boOrderStatusesPage.filterTable(page, tableName, 'input', 'name', dataOrderStatuses.shipped.name);

    const numberOfLinesAfterFilter = await boOrderStatusesPage.getNumberOfElementInGrid(page, tableName);
    expect(numberOfLinesAfterFilter).to.be.above(0);

    for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
      const textColumn = await boOrderStatusesPage.getTextColumn(page, tableName, row, 'name');
      expect(textColumn).to.contains(dataOrderStatuses.shipped.name);
    }
  });

  const statuses = [
    {
      args: {
        status: 'disable', enable: false, columnName: 'send_email',
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'send_email',
      },
    },
    {
      args: {
        status: 'disable', enable: false, columnName: 'delivery',
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'delivery',
      },
    },
    {
      args: {
        status: 'disable', enable: false, columnName: 'invoice',
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'invoice',
      },
    },
  ];

  statuses.forEach((orderStatus, index: number) => {
    it(`should ${orderStatus.args.status} ${orderStatus.args.columnName} by quick edit`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${orderStatus.args.status}${index}`, baseContext);

      const isActionPerformed = await boOrderStatusesPage.setStatus(
        page,
        tableName,
        1,
        orderStatus.args.columnName,
        orderStatus.args.enable,
      );

      if (isActionPerformed) {
        const resultMessage = await boOrderStatusesPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boOrderStatusesPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await boOrderStatusesPage.getStatus(page, tableName, 1, orderStatus.args.columnName);
      expect(currentStatus).to.be.equal(orderStatus.args.enable);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfLinesAfterReset = await boOrderStatusesPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfLinesAfterReset).to.equal(numberOfOrderStatuses);
  });
});
