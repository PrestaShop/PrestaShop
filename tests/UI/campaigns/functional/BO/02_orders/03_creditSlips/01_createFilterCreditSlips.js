require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');
const creditSlipsPage = require('@pages/BO/orders/creditSlips/index');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_creditSlips_createFilterCreditSlips';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

// Today date
const today = new Date();

// Current day
const day = (`0${today.getDate()}`).slice(-2);

// Current month
const month = (`0${today.getMonth() + 1}`).slice(-2);

// Current year
const year = today.getFullYear();

// Date today format (yyy-mm-dd)
const dateToday = `${year}-${month}-${day}`;

// Date today format (mm/dd/yyyy)
const dateTodayToCheck = `${month}/${day}/${year}`;

let numberOfCreditSlips = 0;

/*
Create order
Create 2 credit slips for the same order
Filter Credit slips table( by ID, Order ID, Date issued From and To)
Download the 2 credit slip files and check them
 */
describe('BO - Orders - Credit slips : Create, filter and check credit slips file', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page, 5);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(5);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);

      await orderConfirmationPage.logout(page);

      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  describe('Create 2 credit slips for the same order', async () => {
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

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    const tests = [
      {args: {productID: 1, quantity: 1, documentRow: 4}},
      {args: {productID: 1, quantity: 2, documentRow: 5}},
    ];

    tests.forEach((test, index) => {
      it(`should create the partial refund n°${index}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addPartialRefund${index + 1}`, baseContext);

        await viewOrderPage.clickOnPartialRefund(page);

        const textMessage = await viewOrderPage.addPartialRefundProduct(page, test.args.productID, test.args.quantity);
        await expect(textMessage).to.contains(viewOrderPage.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCreditSlipDocument${index + 1}`, baseContext);

        // Get document name
        const documentType = await viewOrderPage.getDocumentType(page, test.args.documentRow);
        await expect(documentType).to.be.equal('Credit slip');
      });
    });
  });

  describe('Filter Credit slips', async () => {
    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.creditSlipsLink,
      );

      await creditSlipsPage.closeSfToolBar(page);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should reset all filters and get number of credit slips', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCreditSlips = await creditSlipsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCreditSlips).to.be.above(0);
    });

    const tests = [
      {
        args:
          {
            testIdentifier: 'filterIdCreditSlip',
            filterBy: 'id_credit_slip',
            filterValue: 1,
            columnName: 'id_order_slip',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterIdOrder',
            filterBy: 'id_order',
            filterValue: 4,
            columnName: 'id_order',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await creditSlipsPage.filterCreditSlips(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        // Get number of credit slips
        const numberOfCreditSlipsAfterFilter = await creditSlipsPage.getNumberOfElementInGrid(page);
        await expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

        for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
          const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(
            page,
            i,
            test.args.columnName,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCreditSlipsAfterReset = await creditSlipsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
      });
    });

    it('should filter by Date issued \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssued', baseContext);

      // Filter credit slips
      await creditSlipsPage.filterCreditSlipsByDate(page, dateToday, dateToday);

      // Check number of element
      const numberOfCreditSlipsAfterFilter = await creditSlipsPage.getNumberOfElementInGrid(page);
      await expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

      for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
        const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(page, i, 'date_add');
        await expect(textColumn).to.contains(dateTodayToCheck);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssuedReset', baseContext);

      const numberOfCreditSlipsAfterReset = await creditSlipsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
    });
  });

  const creditSlips = [
    {args: {number: 'first', id: 1}},
    {args: {number: 'second', id: 2}},
  ];

  creditSlips.forEach((creditSlip) => {
    describe(`Download the ${creditSlip.args.number} Credit slips and check it`, async () => {
      it(`should filter credit slip by id '${creditSlip.args.id}'`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `filterToDownload${creditSlip.args.number}`,
          baseContext,
        );

        // Filter credit slips
        await creditSlipsPage.filterCreditSlips(
          page,
          'id_credit_slip',
          creditSlip.args.id,
        );

        // Check text column
        const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(
          page,
          1,
          'id_order_slip',
        );

        await expect(textColumn).to.contains(creditSlip.args.id);
      });

      it(`should download the ${creditSlip.args.number} credit slip and check the file existence`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `download${creditSlip.args.number}`, baseContext);

        const filePath = await creditSlipsPage.downloadCreditSlip(page);

        const exist = await files.doesFileExist(filePath);
        await expect(exist).to.be.true;
      });
    });
  });
});
