// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';

import {
  // Import data
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.ordersLink,
    );

    const pageTitle = await ordersPage.getPageTitle(page);
    expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should go to create order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await ordersPage.goToCreateOrderPage(page);

    const pageTitle = await addOrderPage.getPageTitle(page);
    expect(pageTitle).to.contains(addOrderPage.pageTitle);
  });

  it('should create customer and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

    const customerName = await addOrderPage.addNewCustomer(page, customerData);
    expect(customerName).to.contains(`${customerData.firstName} ${customerData.lastName}`);
  });

  it('should search for the new customer and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchCustomer', baseContext);

    await addOrderPage.searchCustomer(page, customerData.email);

    const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
    expect(customerName).to.contains(`${customerData.firstName} ${customerData.lastName}`);
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(customerData, baseContext);
});
