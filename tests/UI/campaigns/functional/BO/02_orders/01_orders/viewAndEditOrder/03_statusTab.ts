// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createOrderByCustomerTest, createOrderByGuestTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataEmployees,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerEmployee,
  FakerOrder,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsDate,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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

  const today: string = utilsDate.getDateFormat('mm/dd/yyyy');
  const orderNote: string = 'Test order note';
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  // New employee data
  const createEmployeeData: FakerEmployee = new FakerEmployee({
    defaultPage: 'Dashboard',
    language: 'English (English)',
    permissionProfile: 'SuperAdmin',
  });
  // New order by customer data
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  // New order by guest data
  const orderByGuestData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_5,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should login in BO by default employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock1', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });
  });

  // 2 - Check history table and order note after some edit by default employee
  describe('Check history table after some edits by default employee', async () => {
    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber', baseContext);

      const statusNumber = await boOrdersViewBlockTabListPage.getStatusNumber(page);
      expect(statusNumber).to.be.equal(1);
    });

    it('should click on \'Resend email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resendEmail', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.resendEmail(page);
      expect(textResult).to.contains(boOrdersViewBlockTabListPage.validationSendMessage);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfResetPasswordMailIsInMailbox', baseContext);

      expect(newMail.subject).to.contains('[PrestaShop] Awaiting bank wire payment');
    });

    it('should click on update status and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUpdateStatusAndSeeTheErrorMessage', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.clickOnUpdateStatus(page);
      expect(textResult).to.contains(boOrdersViewBlockTabListPage.errorAssignSameStatus);
    });

    it(`should change the order status to '${dataOrderStatuses.canceled.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelOrderByStatus', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.updateOrderStatus(page, dataOrderStatuses.canceled.name);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber2', baseContext);

      const statusNumber = await boOrdersViewBlockTabListPage.getStatusNumber(page);
      expect(statusNumber).to.be.equal(2);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName1', baseContext);

      const statusName = await boOrdersViewBlockTabListPage.getTextColumnFromHistoryTable(page, 'status', 1);
      expect(statusName).to.be.equal(dataOrderStatuses.canceled.name);
    });

    it('should check the employee name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeName1', baseContext);

      const employeeName = await boOrdersViewBlockTabListPage.getTextColumnFromHistoryTable(page, 'employee', 1);
      expect(employeeName).to.be.equal(`${dataEmployees.defaultEmployee.firstName} ${dataEmployees.defaultEmployee.lastName}`);
    });

    it('should check the date from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDate1', baseContext);

      const date = await boOrdersViewBlockTabListPage.getTextColumnFromHistoryTable(page, 'date', 1);
      expect(date).to.contain(today);
    });

    it('should check that the order note is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed1', baseContext);

      const isOpened = await boOrdersViewBlockTabListPage.isOrderNoteOpened(page);
      expect(isOpened).to.eq(false);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  // 3 - Check history table and order note after some modifications by new employee
  describe('Check history table and order note after some modifications by new employee', async () => {
    it('should login by new employee account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginWithNewEmployee', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, createEmployeeData.email, createEmployeeData.password);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock2', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusToAccepted', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.updateOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber3', baseContext);

      const statusNumber = await boOrdersViewBlockTabListPage.getStatusNumber(page);
      expect(statusNumber).to.be.equal(3);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName2', baseContext);

      const statusName = await boOrdersViewBlockTabListPage.getTextColumnFromHistoryTable(page, 'status', 1);
      expect(statusName).to.be.equal(dataOrderStatuses.paymentAccepted.name);
    });

    it('should check the employee name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeName2', baseContext);

      const employeeName = await boOrdersViewBlockTabListPage.getTextColumnFromHistoryTable(page, 'employee', 1);
      expect(employeeName).to.be.equal(`${createEmployeeData.firstName} ${createEmployeeData.lastName}`);
    });

    it('should check the date from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDate2', baseContext);

      const date = await boOrdersViewBlockTabListPage.getTextColumnFromHistoryTable(page, 'date', 1);
      expect(date).to.contain(today);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusToShipped', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.updateOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 4', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber4', baseContext);

      const statusNumber = await boOrdersViewBlockTabListPage.getStatusNumber(page);
      expect(statusNumber).to.be.equal(4);
    });

    it('should check that the order note still closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed2', baseContext);

      const isOpened = await boOrdersViewBlockTabListPage.isOrderNoteOpened(page);
      expect(isOpened).to.eq(false);
    });

    it('should open the order note textarea', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openOrderNote', baseContext);

      const isOpened = await boOrdersViewBlockTabListPage.openOrderNoteTextarea(page);
      expect(isOpened).to.eq(true);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage3', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters3', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer3', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock3', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber5', baseContext);

      const statusNumber = await boOrdersViewBlockTabListPage.getStatusNumber(page);
      expect(statusNumber).to.be.equal(1);
    });

    it('should set an order note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setOrderNote', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.setOrderNote(page, orderNote);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.updateSuccessfullMessage);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage4', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters4', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer4', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock4', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should check that the order note is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed3', baseContext);

      const isOpened = await boOrdersViewBlockTabListPage.isOrderNoteOpened(page);
      expect(isOpened).to.eq(false);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  // 4 - delete order note
  describe('Delete order note by default employee', async () => {
    it('should login with default account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage5', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters5', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should check that the order note is not empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteEmpty', baseContext);

      const orderNote = await boOrdersViewBlockTabListPage.getOrderNoteContent(page);
      expect(orderNote).to.be.equal(orderNote);
    });

    it('should delete the order note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderNote', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.setOrderNote(page, '');
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.updateSuccessfullMessage);
    });
  });

  // Post-condition: Delete employee
  deleteEmployeeTest(createEmployeeData, `${baseContext}_postTest_1`);

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, `${baseContext}_postTest_2`);

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_3`);
});
