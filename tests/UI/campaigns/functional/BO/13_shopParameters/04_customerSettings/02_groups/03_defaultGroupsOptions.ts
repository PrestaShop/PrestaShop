// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boCustomerGroupsPage,
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_groups_defaultGroupsOptions';

describe('BO - Shop Parameters - Customer Settings : Default groups options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should check that the default value selected for Visitors group is Visitor', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultGroupForVisitors', baseContext);

    const selectedGroup = await boCustomerGroupsPage.getGroupSelectedValue(page, 'visitors');
    expect(selectedGroup).to.equal('Visitor');
  });

  it('should get the dropdown list of visitors group', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getOptionsOfVisitorsGroup', baseContext);

    const options = await boCustomerGroupsPage.getGroupDropDownList(page, 'visitors');
    expect(options).to.equal('Visitor Guest Customer');
  });

  it('should check that the default value selected for Guests group is Visitor', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultGroupForGuests', baseContext);

    const selectedGroup = await boCustomerGroupsPage.getGroupSelectedValue(page, 'guests');
    expect(selectedGroup).to.equal('Guest');
  });

  it('should get the dropdown list of Guests group', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getOptionsOfGuestsGroup', baseContext);

    const options = await boCustomerGroupsPage.getGroupDropDownList(page, 'guests');
    expect(options).to.equal('Visitor Guest Customer');
  });

  it('should check that the default value selected for Customers group is Visitor', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultGroupForCustomers', baseContext);

    const selectedGroup = await boCustomerGroupsPage.getGroupSelectedValue(page, 'customers');
    expect(selectedGroup).to.equal('Customer');
  });

  it('should get the dropdown list of Customers group', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getOptionsOfCustomersGroup', baseContext);

    const options = await boCustomerGroupsPage.getGroupDropDownList(page, 'customers');
    expect(options).to.equal('Visitor Guest Customer');
  });
});
