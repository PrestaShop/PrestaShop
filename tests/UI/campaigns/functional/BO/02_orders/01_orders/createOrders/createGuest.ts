import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  boCustomersPage,
  boCustomersCreatePage,
  type BrowserContext,
  dataGroups,
  FakerCustomer,
  type Frame,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_createOrders_createGuest';

/*
Scenario:
- Go to create order page
- Click on Add new customer button
- Enable guest account and check disabled fields
- Fill the form and create the guest customer
- Check the guest customer is displayed in the search results
- Go to Customers page and search by email
- Check the guest customer is displayed
- Delete the guest customer
- Reset filter
 */
describe('BO - Orders - Create order : Create guest from new order page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let customerFrame: Frame;

  const guestData: FakerCustomer = new FakerCustomer({
    firstName: 'Test',
    lastName: 'GUEST',
    email: 'test@guest.com',
    guestAccount: true,
  });

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

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.ordersLink,
    );

    const pageTitle = await boOrdersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersPage.pageTitle);
  });

  it('should go to create order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await boOrdersPage.goToCreateOrderPage(page);

    const pageTitle = await boOrdersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
  });

  it('should click on \'Add new customer\' button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickAddNewCustomer', baseContext);

    const isIframeVisible = await boOrdersCreatePage.clickOnAddNewCustomerButton(page);
    expect(isIframeVisible).to.equal(true);
  });

  it('should enable guest account and check disabled fields', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableGuestAccount', baseContext);

    customerFrame = boOrdersCreatePage.getNewCustomerIframe(page)!;

    await boCustomersCreatePage.enableGuestAccount(customerFrame, true);

    const isPasswordDisabled = await boCustomersCreatePage.isPasswordDisabled(customerFrame);
    expect(isPasswordDisabled).to.equal(true);

    const isCustomerDisabled = await boCustomersCreatePage.isCustomerDisabled(customerFrame);
    expect(isCustomerDisabled).to.equal(true);

    const isGroupAccessDisabled = await boCustomersCreatePage.isGroupAccessDisabled(customerFrame);
    expect(isGroupAccessDisabled).to.equal(true);

    const isDefaultCustomerGroupDisabled = await boCustomersCreatePage.isDefaultCustomerGroupDisabled(customerFrame);
    expect(isDefaultCustomerGroupDisabled).to.equal(true);
  });

  it('should fill the form and create the guest customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createGuestCustomer', baseContext);

    const customerName = await boOrdersCreatePage.addNewCustomer(page, guestData);
    expect(customerName).to.contains(`${guestData.firstName} ${guestData.lastName}`);
  });

  it('should go to \'Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.customersLink,
    );

    const pageTitle = await boCustomersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersPage.pageTitle);
  });

  it('should search by email and check the guest customer is displayed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchCustomer', baseContext);

    await boCustomersPage.filterCustomers(page, 'input', 'email', guestData.email);

    const numberOfCustomersAfterFilter = await boCustomersPage.getNumberOfElementInGrid(page);
    expect(numberOfCustomersAfterFilter).to.equal(1);

    const email = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
    expect(email).to.equal(guestData.email);

    const defaultGroup = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'default_group');
    expect(defaultGroup).to.equal(dataGroups.guest.name);
  });

  it('should delete the guest customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

    const textResult = await boCustomersPage.deleteCustomer(page, 1);
    expect(textResult).to.equal(boCustomersPage.successfulDeleteMessage);
  });

  it('should reset the filter', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    await boCustomersPage.resetFilter(page);

    const numberOfCustomers = await boCustomersPage.getNumberOfElementInGrid(page);
    expect(numberOfCustomers).to.be.above(0);
  });
});
