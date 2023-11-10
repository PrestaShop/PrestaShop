// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import groupsPage from '@pages/BO/shopParameters/customerSettings/groups';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_groups_defaultGroupsOptions';

describe('BO - Shop Parameters - Customer Settings : Default groups options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should check that the default value selected for Visitors group is Visitor', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultGroupForVisitors', baseContext);

    const selectedGroup = await groupsPage.getGroupSelectedValue(page, 'visitors');
    expect(selectedGroup).to.equal('Visitor');
  });

  it('should get the dropdown list of visitors group', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getOptionsOfVisitorsGroup', baseContext);

    const options = await groupsPage.getGroupDropDownList(page, 'visitors');
    expect(options).to.equal('Visitor Guest Customer');
  });

  it('should check that the default value selected for Guests group is Visitor', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultGroupForGuests', baseContext);

    const selectedGroup = await groupsPage.getGroupSelectedValue(page, 'guests');
    expect(selectedGroup).to.equal('Guest');
  });

  it('should get the dropdown list of Guests group', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getOptionsOfGuestsGroup', baseContext);

    const options = await groupsPage.getGroupDropDownList(page, 'guests');
    expect(options).to.equal('Visitor Guest Customer');
  });

  it('should check that the default value selected for Customers group is Visitor', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultGroupForCustomers', baseContext);

    const selectedGroup = await groupsPage.getGroupSelectedValue(page, 'customers');
    expect(selectedGroup).to.equal('Customer');
  });

  it('should get the dropdown list of Customers group', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getOptionsOfCustomersGroup', baseContext);

    const options = await groupsPage.getGroupDropDownList(page, 'customers');
    expect(options).to.equal('Visitor Guest Customer');
  });
});
