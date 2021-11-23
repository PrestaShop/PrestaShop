require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const customersPage = require('@pages/BO/customers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const CustomerFaker = require('@data/faker/customer');

const customerData = new CustomerFaker();

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_createOrders_createCustomer';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/*
Go to create order page
Create customer
Delete the created customer
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

  describe('Create customer', () => {
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
  });

  describe('Delete Customer', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await addOrderPage.goToSubMenu(
        page,
        addOrderPage.customersParentLink,
        addOrderPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        customerData.email,
      );

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(customerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await customersPage.deleteCustomer(page, 1);
      await expect(textResult).to.equal(customersPage.successfulDeleteMessage);
    });
  });
});
