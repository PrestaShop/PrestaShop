require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const CustomerFaker = require('@data/faker/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_createOrders_searchCustomers';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

const nonExistentCustomer = new CustomerFaker();
const disabledCustomer = new CustomerFaker({enabled: false});

/*
Create disabled customer
Search for non existent customer and check error message
Search for disabled customer and check error message
Search for existent customer and check displayed customer card
Delete created customer
 */
describe('BO - Orders - Create order : Search for customers from new order page', async () => {
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

  describe('Create disabled customer', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, disabledCustomer);
      await expect(textResult).to.equal(customersPage.successfulCreationMessage);
    });
  });

  describe('Search for customers', () => {
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

    const steps = [
      {
        testIdentifier: 'checkNonExistentCustomerError',
        customerType: 'non existent',
        customer: nonExistentCustomer,
      },
      {
        testIdentifier: 'checkDisabledCustomerError',
        customerType: 'disabled',
        customer: disabledCustomer,
      },
    ];

    steps.forEach((step) => {
      it(`should search for ${step.customerType} customer and check error message`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', step.testIdentifier, baseContext);

        await addOrderPage.searchCustomer(page, step.customer.email);

        const errorDisplayed = await addOrderPage.getNoCustomerFoundError(page);
        await expect(errorDisplayed, 'Error is not correct').to.equal(addOrderPage.noCustomerFoundText);
      });
    });

    it('should search for existent customer and check customer card', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistentCustomerCard', baseContext);

      await addOrderPage.searchCustomer(page, DefaultCustomer.email);

      const customerName = await addOrderPage.getCustomerNameFromResult(page, 1);
      await expect(customerName).to.contains(`${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`);
    });
  });

  describe('Delete created Customer', async () => {
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

      await customersPage.filterCustomers(page, 'input', 'email', disabledCustomer.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(disabledCustomer.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await customersPage.deleteCustomer(page, 1);
      await expect(textResult).to.equal(customersPage.successfulDeleteMessage);
    });
  });
});
