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

const baseContext: string = 'functional_BO_shopParameters_customerSettings_groups_invalidDiscount';

/*
Regression test for https://github.com/PrestaShop/PrestaShop/issues/28415
Submitting a group with an invalid discount (banned symbols) used to redirect to the groups list.
It should stay on the form and display the error instead.

Scenario:
- Add path: fill the 'Add new group' form with an invalid discount, save,
  check that the error is displayed AND that we stay on the creation form.
- Edit path: create a valid group, open its edit form, submit an invalid
  discount, check that the error is displayed AND that we stay on the edit form.
 */
describe('BO - Shop Parameters - Customer Settings : Invalid discount keeps the group form open', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number = 0;

  const groupData: FakerGroup = new FakerGroup();
  const editGroupData: FakerGroup = new FakerGroup();
  const invalidDiscount: string = 'abc%$';

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

  describe('Add path : invalid discount keeps the creation form open', async () => {
    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);
    });

    it('should fill the form with an invalid discount and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'submitInvalidDiscountCreate', baseContext);

      const errorMessage = await boCustomerGroupsCreatePage.setInvalidDiscount(page, groupData, invalidDiscount);
      expect(errorMessage).to.contains('The discount value is incorrect (must be a percentage).');
    });

    it('should check that we stayed on the group creation form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStayedOnCreateForm', baseContext);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);
    });

    it('should go back to \'Groups\' page and check that no group was created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoGroupCreated', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.goToGroupsPage(page);

      const numberOfGroupsAfter = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfter).to.be.equal(numberOfGroups);
    });
  });

  describe('Edit path : invalid discount keeps the edit form open', async () => {
    it('should create a valid group to edit', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroupToEdit', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, editGroupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });

    it('should filter the list by the new group name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForEdit', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', editGroupData.name);

      const textColumn = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(editGroupData.name);
    });

    it('should go to the edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage', baseContext);

      await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
    });

    it('should submit an invalid discount and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'submitInvalidDiscountEdit', baseContext);

      const errorMessage = await boCustomerGroupsCreatePage.setInvalidDiscount(page, editGroupData, invalidDiscount);
      expect(errorMessage).to.contains('The discount value is incorrect (must be a percentage).');
    });

    it('should check that we stayed on the group edit form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStayedOnEditForm', baseContext);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
    });

    it('should go back to \'Groups\' page and check the group count is unchanged', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGroupCountUnchanged', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.goToGroupsPage(page);

      const numberOfGroupsAfterEdit = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterEdit).to.be.equal(numberOfGroups + 1);
    });

    it('should delete the created group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCreatedGroup', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', editGroupData.name);

      const textResult = await boCustomerGroupsPage.deleteGroup(page, 1);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulDeleteMessage);

      await boCustomerGroupsPage.resetFilter(page);
      const numberOfGroupsAfterDelete = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups);
    });
  });
});
