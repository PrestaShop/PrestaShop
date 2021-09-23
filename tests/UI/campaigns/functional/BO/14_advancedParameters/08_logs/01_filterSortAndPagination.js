require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const logsPage = require('@pages/BO/advancedParameters/logs');
const foLoginPage = require('@pages/FO/login');
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_logs_filterSortAndPagination';

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {DefaultEmployee} = require('@data/demo/employees');

let browserContext;
let page;

let numberOfLogs = 0;

const today = new Date();

// Current day
const dd = (`0${today.getDate()}`).slice(-2);
// Current month
const mm = (`0${today.getMonth() + 1}`).slice(-2);
// Current year
const yyyy = today.getFullYear();

// Date today (yyy-mm-dd)
const dateToday = `${mm}/${dd}/${yyyy}`;

/*
Erase all logs
Login and logout 6 times
Create 6 orders
Pagination next and previous
Filter logs table by : Id, Employee, Severity, Message, Object type, Object ID, Error code, Date
Sort logs table by : Id, Employee, Severity, Message, Object type, Object ID, Error code, Date
 */

describe('Filter, sort and pagination logs', async () => {
  // before and after functions
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

  it('should go to "Advanced parameters > Logs" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToEraseLogs', baseContext);

    await dashboardPage.goToSubMenu(page, dashboardPage.advancedParametersLink, dashboardPage.logsLink);

    await logsPage.closeSfToolBar(page);

    const pageTitle = await logsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(logsPage.pageTitle);
  });

  it('should erase all logs', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'eraseLogs', baseContext);

    const textResult = await logsPage.eraseAllLogs(page);
    await expect(textResult).to.equal(logsPage.successfulUpdateMessage);

    numberOfLogs = await logsPage.getNumberOfElementInGrid(page);
    await expect(numberOfLogs).to.be.equal(0);
  });

  // Login and logout 5 times to have 5 logs
  describe('Logout then login 5 times to have 5 logs', async () => {
    const tests = new Array(5).fill(0, 0, 5);

    tests.forEach((test, index) => {
      it(`should logout from BO n°${index + 1}`, async function () {
        await loginCommon.logoutBO(this, page);
      });

      it(`should login in BO n°${index + 1}`, async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to "Advanced parameters > Logs" page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToLogsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(page, dashboardPage.advancedParametersLink, dashboardPage.logsLink);

        const pageTitle = await logsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(logsPage.pageTitle);

        const numberOfElements = await logsPage.getNumberOfElementInGrid(page);
        await expect(numberOfElements).to.be.equal(numberOfLogs + index + 1);
      });
    });
  });

  // Create 6 orders to have 6 logs
  describe('Create 6 orders to have 6 logs', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // Click on view my shop
      page = await dashboardPage.viewMyShop(page);

      // Change language on FO
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    const tests = new Array(6).fill(0, 0, 6);

    tests.forEach((test, index) => {
      it(`should create the order n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrder${index}`, baseContext);

        // Go to home page
        await foLoginPage.goToHomePage(page);

        // Go to the first product page
        await homePage.goToProductPage(page, 1);

        // Add the created product to the cart
        await productPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await orderConfirmationPage.logout(page);
      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  // 1 - Pagination
  describe('Pagination next and previous', async () => {
    it('should go to "Advanced parameters > Logs" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToFilter', baseContext);

      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      await dashboardPage.goToSubMenu(page, dashboardPage.advancedParametersLink, dashboardPage.logsLink);

      const pageTitle = await logsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(logsPage.pageTitle);

      const numberOfElements = await logsPage.getNumberOfElementInGrid(page);
      await expect(numberOfElements).to.be.equal(numberOfLogs + 11);
    });

    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await logsPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await logsPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await logsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await logsPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 2 - Filter logs
  describe('Filter Logs', async () => {
    [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_log',
            filterValue: 50,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByEmployee',
            filterType: 'input',
            filterBy: 'employee',
            filterValue: DefaultEmployee.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySeverity',
            filterType: 'input',
            filterBy: 'severity',
            filterValue: 'Error',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByMessage',
            filterType: 'input',
            filterBy: 'message',
            filterValue: 'Back office',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectType',
            filterType: 'input',
            filterBy: 'object_type',
            filterValue: 'Cart',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectID',
            filterType: 'input',
            filterBy: 'object_id',
            filterValue: 2,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByErrorCode',
            filterType: 'input',
            filterBy: 'error_code',
            filterValue: 1,
          },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await logsPage.filterLogs(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfLogsAfterFilter = await logsPage.getNumberOfElementInGrid(page);

        await expect(numberOfLogsAfterFilter).to.be.at.most(numberOfLogs + 11);

        for (let i = 1; i <= numberOfLogsAfterFilter; i++) {
          const textColumn = await logsPage.getTextColumn(page, i, test.args.filterBy);

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLogsAfterReset = await logsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfLogsAfterReset).to.equal(numberOfLogs + 11);
      });
    });

    it('should filter logs by date sent \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDateSent', baseContext);

      await logsPage.filterLogsByDate(page, dateToday, dateToday);

      const numberOfEmailsAfterFilter = await logsPage.getNumberOfElementInGrid(page);
      await expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfLogs + 11);

      for (let row = 1; row <= numberOfEmailsAfterFilter; row++) {
        const textColumn = await logsPage.getTextColumn(page, row, 'date_add');
        await expect(textColumn).to.contains(dateToday);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterFilterByDate', baseContext);

      const numberOfLogsAfterReset = await logsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLogsAfterReset).to.equal(numberOfLogs + 11);
    });
  });

  // 3 : Sort logs
  describe('Sort logs table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_log', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByEmployeeAsc', sortBy: 'employee', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByEmployeeDesc', sortBy: 'employee', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySeverityDesc', sortBy: 'severity', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySeverityAsc', sortBy: 'severity', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByMessageDesc', sortBy: 'message', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByMessageAsc', sortBy: 'message', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectTypeDesc', sortBy: 'object_type', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectTypeAsc', sortBy: 'object_type', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectIDDesc', sortBy: 'object_id', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectIDAsc', sortBy: 'object_id', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByErrorCodeDesc', sortBy: 'error_code', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByErrorCodeDAsc', sortBy: 'error_code', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByDateAddDesc', sortBy: 'date_add', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByDateAddAsc', sortBy: 'date_add', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_log', sortDirection: 'asc', isFloat: true,
          },
      },
    ].forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await logsPage.getAllRowsColumnContent(page, test.args.sortBy);
        await logsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await logsPage.getAllRowsColumnContent(page, test.args.sortBy);
        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await logsPage.sortArray(nonSortedTable, test.args.isFloat);
        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });
});
