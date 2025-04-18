import {expect} from 'chai';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  type BrowserContext,
  FakerCustomer,
  type Page,
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

  const customerData: FakerCustomer = new FakerCustomer();

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

  it('should create customer and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

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
  deleteCustomerTest(customerData, baseContext);
});
