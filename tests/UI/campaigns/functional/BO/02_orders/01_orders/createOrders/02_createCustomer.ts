import {expect} from 'chai';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  boCustomersCreatePage,
  type BrowserContext,
  FakerCustomer,
  type Page,
  type Frame,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_createOrders_createCustomer';

/*
Scenario:
- Go to create order page
- Create customer
Post-condition:
- Delete the created customer
 */
describe('BO - Orders - Create order : Create customer from new order page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let customerFrame: Frame;

  const customerData1: FakerCustomer = new FakerCustomer({
    firstName: 'Tom',
    lastName: null!,
    email: '',
    password: '',
  });
  const customerData2: FakerCustomer = new FakerCustomer({
    firstName: 'Tom',
    lastName: 'Thierry',
    email: ' ',
    password: '',
  });
  const customerData3: FakerCustomer = new FakerCustomer({
    firstName: 'Tom',
    lastName: 'Thierry',
    password: '',
  });
  const customerData: FakerCustomer = new FakerCustomer({
    password: 'abcdefghijkl',
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

  it('should click on add new customer button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

    const isIframeVisible = await boOrdersCreatePage.clickOnAddNewCustomerButton(page);
    expect(isIframeVisible).to.equal(true);
  });

  it('should enable guest account and check the disabled input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableGuestAccount', baseContext);

    customerFrame = boOrdersCreatePage.getNewCustomerIframe(page)!;

    await boCustomersCreatePage.enableGuestAccount(customerFrame!, true);

    let isDisable = await boCustomersCreatePage.isPasswordDisabled(customerFrame!);
    expect(isDisable).to.equal(true);

    isDisable = await boCustomersCreatePage.isCustomerDisabled(customerFrame!);
    expect(isDisable).to.equal(true);

    isDisable = await boCustomersCreatePage.isGroupAccessDisabled(customerFrame!);
    expect(isDisable).to.equal(true);

    isDisable = await boCustomersCreatePage.isDefaultCustomerGroupDisabled(customerFrame!);
    expect(isDisable).to.equal(true);
  });

  it('should disable guest account and check the enabled input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableGuestAccount', baseContext);

    await boCustomersCreatePage.enableGuestAccount(customerFrame!, false);

    let isEnable = await boCustomersCreatePage.isPasswordEnabled(customerFrame!);
    expect(isEnable).to.equal(true);

    isEnable = await boCustomersCreatePage.isCustomerEnabled(customerFrame!);
    expect(isEnable).to.equal(true);

    isEnable = await boCustomersCreatePage.isGroupAccessEnabled(customerFrame!);
    expect(isEnable).to.equal(true);

    isEnable = await boCustomersCreatePage.isDefaultCustomerGroupEnabled(customerFrame!);
    expect(isEnable).to.equal(true);
  });

  it('should set the first name and check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setFirstName', baseContext);

    await boCustomersCreatePage.createEditCustomer(customerFrame, customerData1, false);

    const errorMessage = await boCustomersCreatePage.getRequiredInputErrorMessage(customerFrame, 'lastName');
    expect(errorMessage).to.equal(boCustomersCreatePage.requiredFieldErrorMessage);
  });

  it('should create customer without email check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setFirstLastName', baseContext);

    await boCustomersCreatePage.createEditCustomer(customerFrame, customerData2, false);

    const errorMessage = await boCustomersCreatePage.getRequiredInputErrorMessage(customerFrame, 'email');
    expect(errorMessage).to.equal(boCustomersCreatePage.requiredFieldErrorMessage);
  });

  it('should create customer without password check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setFirstLastNameEmail', baseContext);

    await boCustomersCreatePage.createEditCustomer(customerFrame, customerData3, false);

    const errorMessage = await boCustomersCreatePage.getRequiredInputErrorMessage(customerFrame, 'password');
    expect(errorMessage).to.equal(boCustomersCreatePage.requiredFieldErrorMessage);
  });

  it('should create customer and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createNewCustomer', baseContext);

    const customerName = await boOrdersCreatePage.addNewCustomer(page, customerData);
    expect(customerName).to.contains(`${customerData.firstName} ${customerData.lastName}`);
  });

  it('should search for the new customer and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchCustomer', baseContext);

    await boOrdersCreatePage.searchCustomer(page, customerData.email);

    const customerName = await boOrdersCreatePage.getCustomerNameFromResult(page, 1);
    expect(customerName).to.contains(`${customerData.firstName} ${customerData.lastName}`);
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(customerData, `${baseContext}Post_test`);
});
