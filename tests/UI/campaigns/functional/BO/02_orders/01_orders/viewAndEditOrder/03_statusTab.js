require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const mailHelper = require('@utils/mailHelper');
const date = require('@utils/date');

// Import common tests
const {setupSmtpConfigTest, resetSmtpConfigTest} = require('@commonTests/configSMTP');
const {createOrderByCustomerTest, createOrderByGuestTest} = require('@commonTests/FO/createOrder');
const {createEmployeeTest, deleteEmployeeTest} = require('@commonTests/BO/createDeleteEmployee');
const {deleteCustomerTest} = require('@commonTests/BO/createDeleteCustomer');
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import demo data
const {DefaultEmployee} = require('@data/demo/employees');
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');

// Import faker data
const EmployeeFaker = require('@data/faker/employee');
const AddressFaker = require('@data/faker/address');
const CustomerFaker = require('@data/faker/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_statusTab';

let browserContext;
let page;
let today;

const orderNote = 'Test order note';

const addressData = new AddressFaker({country: 'France'});
const customerData = new CustomerFaker({password: ''});

// New employee data
const createEmployeeData = new EmployeeFaker({
  defaultPage: 'Dashboard',
  language: 'English (English)',
  permissionProfile: 'SuperAdmin',
});

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

// New order by guest data
const orderByGuestData = {
  customer: customerData,
  product: 4,
  productQuantity: 1,
  address: addressData,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

// maildev config
let newMail;

// mailListener
let mailListener;

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
  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(baseContext);

  // Pre-condition - Create new employee
  createEmployeeTest(createEmployeeData, baseContext);

  // Pre-condition - Create order by guest
  createOrderByGuestTest(orderByGuestData, baseContext);

  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    today = await date.getDate('mm/dd/yyyy');

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email) => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Check history table and order note after some edit by default employee
  describe('Check history table after some edits by default employee', async () => {
    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber', baseContext);

      const statusNumber = await viewOrderPage.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(1);
    });

    it('should click on \'Resend email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resendEmail', baseContext);

      const textResult = await viewOrderPage.resendEmail(page);
      await expect(textResult).to.contains(viewOrderPage.validationSendMessage);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfResetPasswordMailIsInMailbox', baseContext);

      await expect(newMail.subject).to.contains('[PrestaShop] Awaiting bank wire payment');
    });

    it('should click on update status and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUpdateStatusAndSeeTheErrorMessage', baseContext);

      const textResult = await viewOrderPage.clickOnUpdateStatus(page);
      await expect(textResult).to.contains(viewOrderPage.errorAssignSameStatus);
    });

    it(`should change the order status to '${Statuses.canceled.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await viewOrderPage.updateOrderStatus(page, Statuses.canceled.status);
      await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber2', baseContext);

      const statusNumber = await viewOrderPage.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(2);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName', baseContext);

      const statusName = await viewOrderPage.getTextColumnFromHistoryTable(page, 'status', 1);
      await expect(statusName).to.be.equal(Statuses.canceled.status);
    });

    it('should check the employee name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeName', baseContext);

      const employeeName = await viewOrderPage.getTextColumnFromHistoryTable(page, 'employee', 1);
      await expect(employeeName).to.be.equal(`${DefaultEmployee.firstName} ${DefaultEmployee.lastName}`);
    });

    it('should check the date from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDate', baseContext);

      const date = await viewOrderPage.getTextColumnFromHistoryTable(page, 'date', 1);
      await expect(date).to.contain(today);
    });

    it('should check that the order note is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed1', baseContext);

      const isOpened = await viewOrderPage.isOrderNoteOpened(page);
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
      await loginPage.login(page, createEmployeeData.email, createEmployeeData.password);

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
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await viewOrderPage.updateOrderStatus(page, Statuses.paymentAccepted.status);
      await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber3', baseContext);

      const statusNumber = await viewOrderPage.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(3);
    });

    it('should check the status name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusName', baseContext);

      const statusName = await viewOrderPage.getTextColumnFromHistoryTable(page, 'status', 1);
      await expect(statusName).to.be.equal(Statuses.paymentAccepted.status);
    });

    it('should check the employee name from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmployeeName', baseContext);

      const employeeName = await viewOrderPage.getTextColumnFromHistoryTable(page, 'employee', 1);
      await expect(employeeName).to.be.equal(`${createEmployeeData.firstName} ${createEmployeeData.lastName}`);
    });

    it('should check the date from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDate', baseContext);

      const date = await viewOrderPage.getTextColumnFromHistoryTable(page, 'date', 1);
      await expect(date).to.contain(today);
    });

    it(`should change the order status to '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await viewOrderPage.updateOrderStatus(page, Statuses.shipped.status);
      await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check that the status number is equal to 4', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber3', baseContext);

      const statusNumber = await viewOrderPage.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(4);
    });

    it('should check that the order note still closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed2', baseContext);

      const isOpened = await viewOrderPage.isOrderNoteOpened(page);
      await expect(isOpened).to.be.false;
    });

    it('should open the order note textarea', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openOrderNote', baseContext);

      const isOpened = await viewOrderPage.openOrderNoteTextarea(page);
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

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer3', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage3', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should check that the status number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber3', baseContext);

      const statusNumber = await viewOrderPage.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(1);
    });

    it('should set an order note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setOrderNote', baseContext);

      const textResult = await viewOrderPage.setOrderNote(page, orderNote);
      await expect(textResult).to.equal(viewOrderPage.updateSuccessfullMessage);
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
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage4', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should check that the order note is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteClosed3', baseContext);

      const isOpened = await viewOrderPage.isOrderNoteOpened(page);
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

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should check that the order note is not empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNoteEmpty', baseContext);

      const orderNote = await viewOrderPage.getOrderNoteContent(page);
      await expect(orderNote).to.be.equal(orderNote);
    });

    it('should delete the order note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderNote', baseContext);

      const textResult = await viewOrderPage.setOrderNote(page, '');
      await expect(textResult).to.equal(viewOrderPage.updateSuccessfullMessage);
    });
  });

  // Post-condition - Delete employee
  deleteEmployeeTest(createEmployeeData, baseContext);

  // Post-condition - Delete guest account
  deleteCustomerTest(customerData, baseContext);

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(baseContext);
});
