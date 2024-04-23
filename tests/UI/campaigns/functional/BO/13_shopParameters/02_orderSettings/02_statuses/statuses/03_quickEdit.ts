// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import orderSettingsPage from '@pages/BO/shopParameters/orderSettings';
import statusesPage from '@pages/BO/shopParameters/orderSettings/statuses';

import {
  // Import data
  dataOrderStatuses,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
  });

  it('should go to \'Statuses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPage', baseContext);

    await orderSettingsPage.goToStatusesPage(page);

    const pageTitle = await statusesPage.getPageTitle(page);
    expect(pageTitle).to.contains(statusesPage.pageTitle);
  });

  it('should filter by status name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByName', baseContext);

    numberOfOrderStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfOrderStatuses).to.be.above(0);

    await statusesPage.filterTable(page, tableName, 'input', 'name', dataOrderStatuses.shipped.name);

    const numberOfLinesAfterFilter = await statusesPage.getNumberOfElementInGrid(page, tableName);
    expect(numberOfLinesAfterFilter).to.be.above(0);

    for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
      const textColumn = await statusesPage.getTextColumn(page, tableName, row, 'name');
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

      const isActionPerformed = await statusesPage.setStatus(
        page,
        tableName,
        1,
        orderStatus.args.columnName,
        orderStatus.args.enable,
      );

      if (isActionPerformed) {
        const resultMessage = await statusesPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(statusesPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await statusesPage.getStatus(page, tableName, 1, orderStatus.args.columnName);
      expect(currentStatus).to.be.equal(orderStatus.args.enable);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfLinesAfterReset).to.equal(numberOfOrderStatuses);
  });
});
