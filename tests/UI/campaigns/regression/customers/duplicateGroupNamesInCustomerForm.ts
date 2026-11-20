import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boCustomersPage,
  boCustomersCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerGroup,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

// https://github.com/PrestaShop/PrestaShop/issues/28977
const baseContext: string = 'regression_customers_duplicateGroupNamesInCustomerForm';

/*
Regression test for issue #28977:
The customer "Default customer group" / "Group access" selectors used to be keyed by group
name, so two groups sharing the same name collapsed into a single entry.
Scenario:
- Create two customer groups with the exact same name
- Go to the add customer page
- Check that both groups are listed in the "Default customer group" dropdown
- Delete the two created groups
 */
describe('Regression - Customers : Groups sharing the same name are all displayed in the customer form', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number = 0;

  const groupName: string = 'DuplicateAccessGroup';
  const firstGroupData: FakerGroup = new FakerGroup({name: groupName});
  const secondGroupData: FakerGroup = new FakerGroup({name: groupName});

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

  describe('Create two groups sharing the same name', async () => {
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

    it('should reset all filters and get number of groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroups).to.be.above(0);
    });

    it('should create the first group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFirstGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, firstGroupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });

    it('should create the second group with the same name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSecondGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, secondGroupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 2);
    });
  });

  describe('Check both groups are displayed in the add customer form', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await boCustomersPage.goToAddNewCustomerPage(page);

      const pageTitle = await boCustomersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleCreate);
    });

    it('should check that both groups are listed in the default customer group dropdown', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultGroupOptions', baseContext);

      const groupOptions = await boCustomersCreatePage.getDefaultCustomerGroupOptions(page);
      const matchingOptions = groupOptions.filter((option: string) => option.includes(groupName));
      expect(matchingOptions.length).to.be.equal(2);
    });
  });

  describe('Delete the two created groups', async () => {
    it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPageToDelete', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPageToDelete', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it('should filter the list by the duplicated name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', groupName);

      const numberOfGroupsAfterFilter = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterFilter).to.be.equal(2);
    });

    it('should bulk delete the two groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteGroups', baseContext);

      const textResult = await boCustomerGroupsPage.bulkDeleteGroups(page);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulMultiDeleteMessage);
    });

    it('should reset filter and check the number of groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterDelete = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups);
    });
  });
});
