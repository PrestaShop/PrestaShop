// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import groupsPage from '@pages/BO/shopParameters/customerSettings/groups';
import addGroupPage from '@pages/BO/shopParameters/customerSettings/groups/add';

// Import data
import GroupData from '@data/faker/group';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import test context
const baseContext: string = 'functional_BO_shopParameters_customerSettings_groups_bulkDeleteGroups';

describe('BO - Shop Parameters - Customer Settings : Bulk delete groups', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number = 0;

  const groupsToCreate: GroupData[] = [
    new GroupData({name: 'todelete1'}),
    new GroupData({name: 'todelete2'}),
  ];

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

    await customerSettingsPage.closeSfToolBar(page);

    const pageTitle = await customerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
  });

  it('should go to \'Groups\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

    await customerSettingsPage.goToGroupsPage(page);

    const pageTitle = await groupsPage.getPageTitle(page);
    expect(pageTitle).to.contains(groupsPage.pageTitle);
  });

  it('should reset all filters and get number of groups in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfGroups = await groupsPage.resetAndGetNumberOfLines(page);
    expect(numberOfGroups).to.be.above(0);
  });

  describe('Create 2 groups in BO', async () => {
    groupsToCreate.forEach((groupToCreate: GroupData, index: number) => {
      it('should go to add new group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewGroupPage${index + 1}`, baseContext);

        await groupsPage.goToNewGroupPage(page);

        const pageTitle = await addGroupPage.getPageTitle(page);
        expect(pageTitle).to.contains(addGroupPage.pageTitleCreate);
      });

      it('should create group and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createGroup${index + 1}`, baseContext);

        const textResult = await addGroupPage.createEditGroup(page, groupToCreate);
        expect(textResult).to.contains(groupsPage.successfulCreationMessage);

        const numberOfGroupsAfterCreation = await groupsPage.getNumberOfElementInGrid(page);
        expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + index + 1);
      });
    });
  });

  describe('Delete groups with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await groupsPage.filterTable(page, 'input', 'b!name', 'todelete');

      const numberOfGroupsAfterFilter = await groupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterFilter).to.be.at.most(numberOfGroups);

      for (let i = 1; i <= numberOfGroupsAfterFilter; i++) {
        const textColumn = await groupsPage.getTextColumn(page, i, 'b!name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete groups with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteGroups', baseContext);

      const deleteTextResult = await groupsPage.bulkDeleteGroups(page);
      expect(deleteTextResult).to.be.contains(groupsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterReset = await groupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterReset).to.be.equal(numberOfGroups);
    });
  });
});
