require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingPage = require('@pages/BO/shopParameters/customerSettings');
const groupsPage = require('@pages/BO/shopParameters/customerSettings/groups');
const addGroupPage = require('@pages/BO/shopParameters/customerSettings/groups/add');

// Import data
const GroupFaker = require('@data/faker/group');

// Import test context
const baseContext = 'functional_BO_shopParameters_customerSettings_groups_bulkDeleteGroups';

// Browser and tab
let browserContext;
let page;

let numberOfGroups = 0;

const groupsToCreate = [
  new GroupFaker({name: 'todelete1'}),
  new GroupFaker({name: 'todelete2'}),
];

describe('BO - Shop Parameters - Customer Settings : Bulk delete groups', async () => {
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

  describe('Create 2 groups in BO', async () => {
    groupsToCreate.forEach((groupToCreate, index) => {
      it('should go to add new group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewGroupPage${index + 1}`, baseContext);

        await groupsPage.goToNewGroupPage(page);
        const pageTitle = await addGroupPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addGroupPage.pageTitleCreate);
      });

      it('should create group and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createGroup${index + 1}`, baseContext);

        const textResult = await addGroupPage.createEditGroup(page, groupToCreate);
        await expect(textResult).to.contains(groupsPage.successfulCreationMessage);

        const numberOfGroupsAfterCreation = await groupsPage.getNumberOfElementInGrid(page);
        await expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + index + 1);
      });
    });
  });

  describe('Delete groups with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await groupsPage.filterTable(page, 'input', 'b!name', 'todelete');

      const numberOfGroupsAfterFilter = await groupsPage.getNumberOfElementInGrid(page);
      await expect(numberOfGroupsAfterFilter).to.be.at.most(numberOfGroups);

      for (let i = 1; i <= numberOfGroupsAfterFilter; i++) {
        const textColumn = await groupsPage.getTextColumn(page, i, 'b!name');
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete groups with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteGroups', baseContext);

      const deleteTextResult = await groupsPage.bulkDeleteGroups(page);
      await expect(deleteTextResult).to.be.contains(groupsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterReset = await groupsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfGroupsAfterReset).to.be.equal(numberOfGroups);
    });
  });
});
