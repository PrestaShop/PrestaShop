// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import addOrderPage from '@pages/BO/orders/add';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';

import {
  boCustomersPage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  type FakerOrderStatus,
  type Frame,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_orders_orders_createOrders_selectPreviousOrders';

/*
Pre-condition:
- Create customer
- Create address
- Create order by new customer
Scenario:
- Go to create order page
- Check orders table
- Check order details
- Complete the order
Post-condition:
- Delete created customer

 */
describe('BO - Orders - Create order : Select previous orders', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderID: number = 0;
  let customerID: number = 0;
  let orderIframe: Frame|null;

  const today: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const newCustomer: FakerCustomer = new FakerCustomer();
  const newAddress: FakerAddress = new FakerAddress({
    email: newCustomer.email,
    country: 'France',
  });
  const orderData: FakerOrder = new FakerOrder({
    customer: newCustomer,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const paymentMethodModuleName: string = dataPaymentMethods.checkPayment.moduleName;
  const orderStatus: FakerOrderStatus = dataOrderStatuses.paymentAccepted;

  // Pre-condition: Create new customer
  createCustomerTest(newCustomer, `${baseContext}_preTest_1`);

  // Pre-condition: Create new address
  createAddressTest(newAddress, `${baseContext}_preTest_2`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_3`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Get customer ID
  describe('PRE-TEST: Get customer ID', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.customersLink,
      );
      await boCustomersPage.closeSfToolBar(page);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCustomersPage.filterCustomers(page, 'input', 'email', newCustomer.email);

      const textResult = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textResult).to.contains(newCustomer.email);
    });

    it('should get the customer ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCustomerID', baseContext);

      customerID = parseInt(await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'id_customer'), 10);
      expect(customerID).to.be.above(0);
    });
  });

  // Pre-condition: Get created order ID
  describe('PRE-TEST: Get the created order ID', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${newCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', newCustomer.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(newCustomer.lastName);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await boOrdersPage.getOrderIDNumber(page);
      expect(orderID).to.be.at.least(5);
    });
  });

  // 1 - Go to create order page
  describe('Go to create order page', async () => {
    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${newCustomer.firstName} ${newCustomer.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, newCustomer.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });
  });

  // 2 - Check orders table
  describe('Check orders table', async () => {
    it('should go to \'Orders\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectOrdersTab', baseContext);

      const isOrdersTableVisible = await addOrderPage.clickOnOrdersTab(page);
      expect(isOrdersTableVisible, 'Orders table is not visible!').to.eq(true);
    });

    it('should check that there is only one order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersNumber', baseContext);

      const ordersNumber = await addOrderPage.getOrdersNumber(page);
      expect(ordersNumber, 'Orders number is not correct!').to.be.equal(1);
    });

    it('should check the order \'id\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIdOrder', baseContext);

      const textResult = await addOrderPage.getTextFromOrdersTable(page, 'id');
      expect(textResult, 'The \'id\' value is not correct!').to.contains(orderID);
    });

    [
      {args: {columnName: 'date', content: today}},
      {args: {columnName: 'products', content: 1}},
      {args: {columnName: 'total-paid', content: '€0.00'}},
      {args: {columnName: 'payment-method', content: 'Bank transfer'}},
      {args: {columnName: 'status', content: 'Awaiting bank wire payment'}},
    ].forEach((test) => {
      it(`should check the order '${test.args.columnName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check${test.args.columnName}Order`, baseContext);

        const textResult = await addOrderPage.getTextFromOrdersTable(page, test.args.columnName);
        expect(textResult, `The '${test.args.columnName}' value is not correct!`).to.contains(test.args.content);
      });
    });
  });

  // 3 - Check order details
  describe('Check order details', async () => {
    it('should click on \'Details\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDetailsButton', baseContext);

      const isIframeVisible = await addOrderPage.clickOnOrderDetailsButton(page);
      expect(isIframeVisible, 'View order Iframe is not visible!').to.eq(true);
    });

    it('should check customer title, name, lastname, reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInfo', baseContext);

      orderIframe = addOrderPage.getOrderIframe(page, orderID);
      expect(orderIframe).to.not.eq(null);

      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(orderIframe!);
      expect(customerInfo).to.contains(newCustomer.socialTitle);
      expect(customerInfo).to.contains(newCustomer.firstName);
      expect(customerInfo).to.contains(newCustomer.lastName);
      expect(customerInfo).to.contains(customerID.toString());
    });

    it('should check number of ordered products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts0', baseContext);

      const productCount = await boOrdersViewBlockProductsPage.getProductsNumber(orderIframe!);
      expect(productCount).to.equal(1);
    });

    it('should check the ordered product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductDetails', baseContext);

      const result = await boOrdersViewBlockProductsPage.getProductDetails(orderIframe!, 1);
      await Promise.all([
        expect(result.name).to.equal(`${dataProducts.demo_1.name} (Size: S - Color: White)`),
        expect(result.reference).to.equal(`Reference number: ${dataProducts.demo_1.reference}`),
        expect(result.basePrice).to.equal(dataProducts.demo_1.finalPrice),
        expect(result.quantity).to.equal(1),
        expect(result.total).to.equal(dataProducts.demo_1.finalPrice),
      ]);
    });

    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber', baseContext);

      const statusNumber = await boOrdersViewBlockTabListPage.getStatusNumber(orderIframe!);
      expect(statusNumber).to.be.equal(1);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName', baseContext);

      const statusName = await boOrdersViewBlockTabListPage.getTextColumnFromHistoryTable(orderIframe!, 'status', 1);
      expect(statusName).to.be.equal('Awaiting bank wire payment');
    });
  });

  // 4 - Complete the order
  describe('Complete the order', async () => {
    it('should close the order Iframe', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeOrderIframe', baseContext);

      const isIframeNotVisible = await addOrderPage.closeIframe(page);
      expect(isIframeNotVisible, 'Order iframe still visible!').to.eq(true);
    });

    it('should click on \'Use\' button and check that product table is visible on \'Cart\' block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUseButton', baseContext);

      const isProductTableVisible = await addOrderPage.clickOnOrderUseButton(page);
      expect(isProductTableVisible, 'Product table is not visible!').to.eq(true);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      await addOrderPage.setSummaryAndCreateOrder(page, paymentMethodModuleName, orderStatus);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contain(boOrdersViewBlockProductsPage.pageTitle);
    });
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(newCustomer, `${baseContext}_postTest_1`);
});
