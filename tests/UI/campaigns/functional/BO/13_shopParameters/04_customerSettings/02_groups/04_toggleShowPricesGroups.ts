// Import utils
import testContext from '@utils/testContext';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

import {expect} from 'chai';
import {
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  boFeatureFlagPage,
  boLoginPage,
  type BrowserContext,
  FakerGroup,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_groups_toggleShowPricesGroups';

setFeatureFlag(boFeatureFlagPage.featureFlagCustomerGroup, true, `${baseContext}_preTest`);

describe('BO - Shop Parameters - Customer Settings : Toggle show prices for customer groups', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number = 0;

  const groupData: FakerGroup = new FakerGroup({shownPrices: true});

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

  it('should reset all filters and get number of groups', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
    expect(numberOfGroups).to.be.above(0);
  });

  // 1 - Create a test group with show_prices enabled
  describe('Create a group with show prices enabled', async () => {
    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);
    });

    it('should create group with show prices enabled and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroup', baseContext);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, groupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });
  });

  // 2 - Toggle show prices OFF (via AJAX toggle column — no page reload)
  describe('Toggle show prices OFF via quick-edit', async () => {
    it('should filter list by name to find the created group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForToggle', baseContext);

      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', groupData.name);

      const numberOfGroupsAfterFilter = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterFilter).to.be.equal(1);

      const textColumn = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(groupData.name);
    });

    it('should verify show prices is currently ON', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShowPricesIsOn', baseContext);

      const status = await boCustomerGroupsPage.getTextColumn(page, 1, 'show_prices');
      expect(status).to.contains('Yes');
    });

    it('should toggle show prices to OFF via the toggle column', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'toggleShowPricesOff', baseContext);

      const toggleResult = await boCustomerGroupsPage.setStatus(page, 1, false);
      expect(toggleResult).to.be.equal(true);
    });

    it('should verify show prices changed to OFF without page reload', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShowPricesIsOffAfterToggle', baseContext);

      const status = await boCustomerGroupsPage.getTextColumn(page, 1, 'show_prices');
      expect(status).to.contains('No');
    });

    it('should reload the page and verify the toggle persisted', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShowPricesPersistedAfterReload', baseContext);

      await boCustomerGroupsPage.reloadPage(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', groupData.name);

      const status = await boCustomerGroupsPage.getTextColumn(page, 1, 'show_prices');
      expect(status).to.contains('No');
    });
  });

  // 3 - Toggle show prices back ON
  describe('Toggle show prices back ON via quick-edit', async () => {
    it('should toggle show prices back to ON', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'toggleShowPricesOn', baseContext);

      const toggleResult = await boCustomerGroupsPage.setStatus(page, 1, true);
      expect(toggleResult).to.be.equal(true);
    });

    it('should verify show prices is ON again', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShowPricesIsOnAgain', baseContext);

      const status = await boCustomerGroupsPage.getTextColumn(page, 1, 'show_prices');
      expect(status).to.contains('Yes');
    });
  });

  // 4 - Delete the test group
  describe('Delete the created group', async () => {
    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBeforeDelete', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', groupData.name);

      const numberOfGroupsAfterFilter = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterFilter).to.be.equal(1);
    });

    it('should delete the group and verify deletion', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteGroup', baseContext);

      const textResult = await boCustomerGroupsPage.deleteGroup(page, 1);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulDeleteMessage);
    });

    it('should reset filter and verify group count is back to initial', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfGroupsAfterDelete = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups);
    });
  });
});

setFeatureFlag(boFeatureFlagPage.featureFlagCustomerGroup, false, `${baseContext}_postTest`);
