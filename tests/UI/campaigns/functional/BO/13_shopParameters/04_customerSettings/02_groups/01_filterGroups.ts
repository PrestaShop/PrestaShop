// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingPage from '@pages/BO/shopParameters/customerSettings';
import groupsPage from '@pages/BO/shopParameters/customerSettings/groups';

// Import data
import {groupAccess} from '@data/demo/groupAccess';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_groups_filterGroups';

describe('BO - Shop Parameters - Customer Settings : Filter groups by id, name and discount,  '
  + 'members and show prices', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number = 0;

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

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.customerSettingsLink,
    );

    await customerSettingPage.closeSfToolBar(page);

    const pageTitle = await customerSettingPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customerSettingPage.pageTitle);
  });

  it('should go to \'Groups\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

    await customerSettingPage.goToGroupsPage(page);

    const pageTitle = await groupsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(groupsPage.pageTitle);
  });

  it('should reset all filters and get number of groups in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfGroups = await groupsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfGroups).to.be.above(0);
  });

  describe('Filter groups', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_group',
            filterValue: groupAccess.visitor.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'b!name',
            filterValue: groupAccess.visitor.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterDiscount',
            filterType: 'input',
            filterBy: 'reduction',
            filterValue: groupAccess.visitor.discount.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterMembers',
            filterType: 'input',
            filterBy: 'nb',
            filterValue: '1',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterShopPrices',
            filterType: 'select',
            filterBy: 'show_prices',
            filterValue: groupAccess.visitor.showPrices ? '1' : '0',
          },
        expected: 'Yes',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await groupsPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfGroupsAfterFilter = await groupsPage.getNumberOfElementInGrid(page);
        await expect(numberOfGroupsAfterFilter).to.be.at.most(numberOfGroups);

        for (let row = 1; row <= numberOfGroupsAfterFilter; row++) {
          const textColumn = await groupsPage.getTextColumn(page, row, test.args.filterBy);

          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfGroupsAfterReset = await groupsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfGroupsAfterReset).to.equal(numberOfGroups);
      });
    });
  });
});
