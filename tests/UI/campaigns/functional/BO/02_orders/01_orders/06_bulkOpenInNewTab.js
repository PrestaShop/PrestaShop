require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');
const testContext = require('@utils/testContext');

// Import utils
const helper = require('@utils/helpers');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const orderPageCustomerBlock = require('@pages/BO/orders/view/customerBlock');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByGuestTest} = require('@commonTests/FO/createOrder');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import demo data
const {PaymentMethods} = require('@data/demo/paymentMethods');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

const baseContext = 'functional_BO_orders_orders_bulkOpenInNewTab';

let browserContext;
let page;
const firstCustomerData = new CustomerFaker();
const secondCustomerData = new CustomerFaker();
const addressData = new AddressFaker({country: 'France'});

const firstOrderByGuestData = {
  customer: firstCustomerData,
  product: 1,
  productQuantity: 1,
  address: addressData,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

const secondOrderByGuestData = {
  customer: secondCustomerData,
  product: 1,
  productQuantity: 1,
  address: addressData,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition:
- Create 2 orders in FO
Scenario:
- Go to BO > Orders page
- Bulk open in new tabs the 2 last orders
- Check the 2 orders (Check customer block)
Post-condition:
- Delete the 2 created guest customers
 */
describe('BO - Orders : Bulk open on new tab', async () => {
  // Pre-condition: Create first order in FO
  createOrderByGuestTest(firstOrderByGuestData, `${baseContext}_preTest_1`);

  // Pre-condition: Create second order in FO
  createOrderByGuestTest(secondOrderByGuestData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Open on new tab by bulk actions', async () => {
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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it('should click on \'Open in new tabs\' with bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkOpenInNewTabs', baseContext);

      page = await ordersPage.bulkOpenInNewTabs(page, false, [1, 2]);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check the first opened order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderPage', baseContext);

      // Check second customer information
      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(page);
      await expect(customerInfo).to.contains(secondCustomerData.socialTitle);
      await expect(customerInfo).to.contains(secondCustomerData.firstName);
      await expect(customerInfo).to.contains(secondCustomerData.lastName);
    });

    it('should close the tab and check that the second order page is opened', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFirstOrderPage', baseContext);

      page = await orderPageCustomerBlock.closePage(browserContext, page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check the second order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondOrderPage', baseContext);

      // Check second customer information
      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(page);
      await expect(customerInfo).to.contains(firstCustomerData.socialTitle);
      await expect(customerInfo).to.contains(firstCustomerData.firstName);
      await expect(customerInfo).to.contains(firstCustomerData.lastName);
    });
  });

  // Post-condition: Delete first guest customers
  deleteCustomerTest(firstCustomerData, `${baseContext}_postTest_1`);

  // Post-condition: Delete second guest customers
  deleteCustomerTest(secondCustomerData, `${baseContext}_postTest_2`);
});
