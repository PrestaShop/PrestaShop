require('module-alias/register');
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');

// Import data
const CustomerFaker = require('@data/faker/customer');

const customerData = new CustomerFaker();

const baseContext = 'functional_BO_orders_orders_createOrders_createCustomer';

let browserContext;
let page;

/*
Scenario:
- Go to create order page
- Create customer
Post-condition:
- Delete the created customer
 */
describe('BO - Orders - Create order : Create customer from new order page', async () => {
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
    await expect(pageTitle).to.contains(ordersPage.pageTitle);
  });

  it('should go to create order page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await ordersPage.goToCreateOrderPage(page);
    const pageTitle = await addOrderPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addOrderPage.pageTitle);
  });

  it('should create customer and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

    const customerName = await addOrderPage.addNewCustomer(page, customerData);
    await expect(customerName).to.contains(`${customerData.firstName} ${customerData.lastName}`);
  });

  it('should search for the new customer and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchCustomer', baseContext);

    await addOrderPage.searchCustomer(page, customerData.email);

    const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
    await expect(customerName).to.contains(`${customerData.firstName} ${customerData.lastName}`);
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(customerData, baseContext);
});
