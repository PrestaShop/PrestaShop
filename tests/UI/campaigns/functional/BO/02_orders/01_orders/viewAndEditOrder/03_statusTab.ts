// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import mailHelper from '@utils/mailHelper';
import testContext from '@utils/testContext';

// Import commonTests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest, createOrderByGuestTest} from '@commonTests/FO/order';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import loginPage from '@pages/BO/login';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import Customers from '@data/demo/customers';
import Employees from '@data/demo/employees';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import EmployeeData from '@data/faker/employee';
import OrderData from '@data/faker/order';
import type MailDevEmail from '@data/types/maildevEmail';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import type MailDev from 'maildev';

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_statusTab';

/*
Pre-condition :
- Setup smtp parameters
- Create new employee
- Create order by guest
- Create order by default customer
Scenario :
- Login by default account and go to view order page by guest customer
- Check status number, order note, resend email
- Change order status and check status number, status name, employee name and date
- Login by new employee account and go to view order page by by guest customer
- Change order status and check status number, status name, employee name and date
- Go to view order page by default customer
- Check status number, order note
- Add order note and check that doesn't exist on other order
- Login by default account and go to view order page by default customer
- Check order note then delete it
Post-condition :
- Delete employee
- Delete guest account
- Reset default email parameters
 */

describe('BO - Orders - View and edit order : Check order status tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const today: string = date.getDateFormat('mm/dd/yyyy');
  const orderNote: string = 'Test order note';
  const addressData: AddressData = new AddressData({country: 'France'});
  const customerData: CustomerData = new CustomerData({password: ''});
  // New employee data
  const createEmployeeData: EmployeeData = new EmployeeData({
    defaultPage: 'Dashboard',
    language: 'English (English)',
    permissionProfile: 'SuperAdmin',
  });
  // New order by customer data
  const orderByCustomerData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  // New order by guest data
  const orderByGuestData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_5,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // Pre-condition: Create new employee
  createEmployeeTest(createEmployeeData, `${baseContext}_preTest_2`);

  // Pre-condition: Create order by guest
  createOrderByGuestTest(orderByGuestData, `${baseContext}_preTest_3`);

  // Pre-condition: Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_4`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should login in BO by default employee', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });
  });

  // 2 - Check history table and order note after some edit by default employee
  describe('Check history table after some edits by default employee', async () => {
    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber', baseContext);

      const statusNumber = await orderPageTabListBlock.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(1);
    });

    it('should click on \'Resend email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resendEmail', baseContext);

      const textResult = await orderPageTabListBlock.resendEmail(page);
      await expect(textResult).to.contains(orderPageTabListBlock.validationSendMessage);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfResetPasswordMailIsInMailbox', baseContext);

      await expect(newMail.subject).to.contains('[PrestaShop] Awaiting bank wire payment');
    });

    it('should click on update status and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUpdateStatusAndSeeTheErrorMessage', baseContext);

      const textResult = await orderPageTabListBlock.clickOnUpdateStatus(page);
      await expect(textResult).to.contains(orderPageTabListBlock.errorAssignSameStatus);
    });

    it(`should change the order status to '${OrderStatuses.canceled.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelOrderByStatus', baseContext);

      const textResult = await orderPageTabListBlock.updateOrderStatus(page, OrderStatuses.canceled.name);
      await expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber2', baseContext);

      const statusNumber = await orderPageTabListBlock.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(2);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName1', baseContext);

      const statusName = await orderPageTabListBlock.getTextColumnFromHistoryTable(page, 'status', 1);
      await expect(statusName).to.be.equal(OrderStatuses.canceled.name);
    });

    it('should check the employee name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeName1', baseContext);

      const employeeName = await orderPageTabListBlock.getTextColumnFromHistoryTable(page, 'employee', 1);
      await expect(employeeName).to.be.equal(`${Employees.DefaultEmployee.firstName} ${Employees.DefaultEmployee.lastName}`);
    });

    it('should check the date from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDate1', baseContext);

      const date = await orderPageTabListBlock.getTextColumnFromHistoryTable(page, 'date', 1);
      await expect(date).to.contain(today);
    });

    it('should check that the order note is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed1', baseContext);

      const isOpened = await orderPageTabListBlock.isOrderNoteOpened(page);
      await expect(isOpened).to.be.false;
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  // 3 - Check history table and order note after some modifications by new employee
  describe('Check history table and order note after some modifications by new employee', async () => {
    it('should login by new employee account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginWithNewEmployee', baseContext);

      await loginPage.goTo(page, global.BO.URL);
      await loginPage.successLogin(page, createEmployeeData.email, createEmployeeData.password);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusToAccepted', baseContext);

      const textResult = await orderPageTabListBlock.updateOrderStatus(page, OrderStatuses.paymentAccepted.name);
      await expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber3', baseContext);

      const statusNumber = await orderPageTabListBlock.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(3);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName2', baseContext);

      const statusName = await orderPageTabListBlock.getTextColumnFromHistoryTable(page, 'status', 1);
      await expect(statusName).to.be.equal(OrderStatuses.paymentAccepted.name);
    });

    it('should check the employee name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeName2', baseContext);

      const employeeName = await orderPageTabListBlock.getTextColumnFromHistoryTable(page, 'employee', 1);
      await expect(employeeName).to.be.equal(`${createEmployeeData.firstName} ${createEmployeeData.lastName}`);
    });

    it('should check the date from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDate2', baseContext);

      const date = await orderPageTabListBlock.getTextColumnFromHistoryTable(page, 'date', 1);
      await expect(date).to.contain(today);
    });

    it(`should change the order status to '${OrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusToShipped', baseContext);

      const textResult = await orderPageTabListBlock.updateOrderStatus(page, OrderStatuses.shipped.name);
      await expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 4', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber4', baseContext);

      const statusNumber = await orderPageTabListBlock.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(4);
    });

    it('should check that the order note still closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed2', baseContext);

      const isOpened = await orderPageTabListBlock.isOrderNoteOpened(page);
      await expect(isOpened).to.be.false;
    });

    it('should open the order note textarea', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openOrderNote', baseContext);

      const isOpened = await orderPageTabListBlock.openOrderNoteTextarea(page);
      await expect(isOpened).to.be.true;
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage3', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters3', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${Customers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer3', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(Customers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock3', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber5', baseContext);

      const statusNumber = await orderPageTabListBlock.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(1);
    });

    it('should set an order note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setOrderNote', baseContext);

      const textResult = await orderPageTabListBlock.setOrderNote(page, orderNote);
      await expect(textResult).to.equal(orderPageTabListBlock.updateSuccessfullMessage);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage4', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters4', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer4', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock4', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should check that the order note is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed3', baseContext);

      const isOpened = await orderPageTabListBlock.isOrderNoteOpened(page);
      await expect(isOpened).to.be.false;
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  // 4 - delete order note
  describe('Delete order note by default employee', async () => {
    it('should login with default account', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage5', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters5', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${Customers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(Customers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should check that the order note is not empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteEmpty', baseContext);

      const orderNote = await orderPageTabListBlock.getOrderNoteContent(page);
      await expect(orderNote).to.be.equal(orderNote);
    });

    it('should delete the order note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderNote', baseContext);

      const textResult = await orderPageTabListBlock.setOrderNote(page, '');
      await expect(textResult).to.equal(orderPageTabListBlock.updateSuccessfullMessage);
    });
  });

  // Post-condition: Delete employee
  deleteEmployeeTest(createEmployeeData, `${baseContext}_postTest_1`);

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, `${baseContext}_postTest_2`);

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_3`);
});
