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

const baseContext = 'functional_BO_shopParameters_customerSettings_groups_CRUDGroups';

// Browser and tab
let browserContext;
let page;

let numberOfGroups = 0;

const createGroupData = new GroupFaker();
const editGroupData = new GroupFaker();

describe('BO - Shop Parameters - Customer Settings : Create, update and delete group in BO', async () => {
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

  describe('Create group in BO', async () => {
    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await groupsPage.goToNewGroupPage(page);
      const pageTitle = await addGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addGroupPage.pageTitleCreate);
    });

    it('should create group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroup', baseContext);

      const textResult = await addGroupPage.createEditGroup(page, createGroupData);
      await expect(textResult).to.contains(groupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await groupsPage.getNumberOfElementInGrid(page);
      await expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });
  });

  describe('Update group created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await groupsPage.resetFilter(page);

      await groupsPage.filterTable(page, 'input', 'b!name', createGroupData.name);

      const textEmail = await groupsPage.getTextColumn(page, 1, 'b!name');
      await expect(textEmail).to.contains(createGroupData.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);
      const pageTitle = await addGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should update group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup', baseContext);

      const textResult = await addGroupPage.createEditGroup(page, editGroupData);
      await expect(textResult).to.contains(groupsPage.successfulUpdateMessage);

      const numberOfGroupsAfterUpdate = await groupsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfGroupsAfterUpdate).to.be.equal(numberOfGroups + 1);
    });
  });

  describe('Delete group', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await groupsPage.resetFilter(page);

      await groupsPage.filterTable(page, 'input', 'b!name', editGroupData.name);

      const textEmail = await groupsPage.getTextColumn(page, 1, 'b!name');
      await expect(textEmail).to.contains(editGroupData.name);
    });

    it('should delete group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteGroup', baseContext);

      const textResult = await groupsPage.deleteGroup(page, 1);
      await expect(textResult).to.contains(groupsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterDelete = await groupsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups);
    });
  });
});
