// Import utils
import testContext from '@utils/testContext';

import {
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerGroup,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_groups_CRUDGroups';

describe('BO - Shop Parameters - Customer Settings : Create, update and delete group in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number = 0;

  const createGroupData: FakerGroup = new FakerGroup();
  const editGroupData: FakerGroup = new FakerGroup();

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

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );
    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
  });

  it('should go to \'Groups\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

    await boCustomerSettingsPage.goToGroupsPage(page);

    const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
  });

  it('should reset all filters and get number of groups in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
    expect(numberOfGroups).to.be.above(0);
  });

  describe('Create group in BO', async () => {
    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);
    });

    it('should create group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroup', baseContext);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, createGroupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });
  });

  describe('Update group created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', createGroupData.name);

      const textEmail = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textEmail).to.contains(createGroupData.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage', baseContext);

      await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
    });

    it('should update group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup', baseContext);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, editGroupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulUpdateMessage);

      const numberOfGroupsAfterUpdate = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterUpdate).to.be.equal(numberOfGroups + 1);
    });
  });

  describe('Delete group', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', editGroupData.name);

      const textEmail = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textEmail).to.contains(editGroupData.name);
    });

    it('should delete group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteGroup', baseContext);

      const textResult = await boCustomerGroupsPage.deleteGroup(page, 1);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterDelete = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups);
    });
  });
});
