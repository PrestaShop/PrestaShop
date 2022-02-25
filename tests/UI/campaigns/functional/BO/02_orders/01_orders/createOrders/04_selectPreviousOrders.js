require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createCustomerTest, deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');
const {createAddressTest} = require('@commonTests/BO/customers/createDeleteAddress');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');
const {PaymentMethods} = require('@data/demo/paymentMethods');

const baseContext = 'functional_BO_orders_orders_createOrders_selectPreviousOrders';

let browserContext;
let page;

const newCustomer = new CustomerFaker();
const newAddress = new AddressFaker({
  email: `${newCustomer.firstName}.${newCustomer.lastName}@prestashop.com`,
  country: 'France',
});
const orderData = {
  customer: newCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};
/*

 */
describe('BO - Orders - Create order : Select previous orders', async () => {
  // Pre-condition: Create new customer
  createCustomerTest(newCustomer, baseContext);

  // Pre-condition: Create new address
  createAddressTest(newAddress, baseContext);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, baseContext);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to create order page', async () => {
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

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);
      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${newCustomer.firstName} ${newCustomer.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, newCustomer.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible).to.be.true;
    });
  });

  describe('Go to create order page and check details', async () => {
    it('should select \'Orders\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectOrdersTab', baseContext);

      const isOrdersTableVisible = await addOrderPage.clickOnOrdersTab(page);
      await expect(isOrdersTableVisible).to.be.true;
    });

    it('should check that there is only one order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersNumber', baseContext);

    });

    [
      {args: {columnName: 'ID', content: 5}},
      {args: {columnName: 'Date', content: 6}},
      {args: {columnName: 'Products', content: 6}},
      {args: {columnName: 'Total paid', content: 0}},
      {args: {columnName: 'Payment', content: 0}},
      {args: {columnName: 'Status', content: 0}},
    ].forEach((test) => {
      it(`should check the order ${test.args.columnName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check${test.args.blockName}OfOrder`, baseContext);

        const textResult = await addOrderPage.getTextFromOrdersTable(page, test.args.columnName);
        await expect(textResult).to.be.at.least(test.args.number);
      });
    });
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(newCustomer, baseContext);
});
