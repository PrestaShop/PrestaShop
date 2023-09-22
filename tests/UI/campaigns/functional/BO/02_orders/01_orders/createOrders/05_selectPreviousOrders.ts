// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import BO pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';
import OrderStatusData from '@data/faker/orderStatus';

import {expect} from 'chai';
import type {BrowserContext, Frame, Page} from 'playwright';

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

  const today: string = date.getDateFormat('yyyy-mm-dd');
  const newCustomer: CustomerData = new CustomerData();
  const newAddress: AddressData = new AddressData({
    email: newCustomer.email,
    country: 'France',
  });
  const orderData: OrderData = new OrderData({
    customer: newCustomer,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  const paymentMethodModuleName: string = PaymentMethods.checkPayment.moduleName;
  const orderStatus: OrderStatusData = OrderStatuses.paymentAccepted;

  // Pre-condition: Create new customer
  createCustomerTest(newCustomer, `${baseContext}_preTest_1`);

  // Pre-condition: Create new address
  createAddressTest(newAddress, `${baseContext}_preTest_2`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_3`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Get customer ID
  describe('PRE-TEST: Get customer ID', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );
      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'email', newCustomer.email);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textResult).to.contains(newCustomer.email);
    });

    it('should get the customer ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCustomerID', baseContext);

      customerID = parseInt(await customersPage.getTextColumnFromTableCustomers(page, 1, 'id_customer'), 10);
      expect(customerID).to.be.above(0);
    });
  });

  // Pre-condition: Get created order ID
  describe('PRE-TEST: Get the created order ID', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${newCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', newCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(newCustomer.lastName);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await ordersPage.getOrderIDNumber(page);
      expect(orderID).to.be.at.least(5);
    });
  });

  // 1 - Go to create order page
  describe('Go to create order page', async () => {
    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);

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

      orderIframe = await addOrderPage.getOrderIframe(page, orderID);
      expect(orderIframe).to.not.eq(null);

      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(orderIframe!);
      expect(customerInfo).to.contains(newCustomer.socialTitle);
      expect(customerInfo).to.contains(newCustomer.firstName);
      expect(customerInfo).to.contains(newCustomer.lastName);
      expect(customerInfo).to.contains(customerID.toString());
    });

    it('should check number of ordered products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts0', baseContext);

      const productCount = await orderPageProductsBlock.getProductsNumber(orderIframe!);
      expect(productCount).to.equal(1);
    });

    it('should check the ordered product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductDetails', baseContext);

      const result = await orderPageProductsBlock.getProductDetails(orderIframe!, 1);
      await Promise.all([
        expect(result.name).to.equal(`${Products.demo_1.name} (Size: S - Color: White)`),
        expect(result.reference).to.equal(`Reference number: ${Products.demo_1.reference}`),
        expect(result.basePrice).to.equal(Products.demo_1.finalPrice),
        expect(result.quantity).to.equal(1),
        expect(result.total).to.equal(Products.demo_1.finalPrice),
      ]);
    });

    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber', baseContext);

      const statusNumber = await orderPageTabListBlock.getStatusNumber(orderIframe!);
      expect(statusNumber).to.be.equal(1);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName', baseContext);

      const statusName = await orderPageTabListBlock.getTextColumnFromHistoryTable(orderIframe!, 'status', 1);
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

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle).to.contain(orderPageProductsBlock.pageTitle);
    });
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(newCustomer, `${baseContext}_postTest_1`);
});
