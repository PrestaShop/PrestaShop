require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createCustomerTest, deleteCustomerTest} = require('@commonTests/BO/createDeleteCustomer');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');

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
  // Pre-condition : Create disabled customer
  createCustomerTest(disabledCustomer, baseContext);

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

  // Post-condition : Delete disabled customer
  deleteCustomerTest(disabledCustomer, baseContext);
});
