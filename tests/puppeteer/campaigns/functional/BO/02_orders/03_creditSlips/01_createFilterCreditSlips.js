require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const FOLoginPage = require('@pages/FO/login');
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
const CreditSlipsPage = require('@pages/BO/orders/creditSlips/index');
// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_creditSlips_createFilterCreditSlips';

let browser;
let page;
const today = new Date();
const day = (`0${today.getDate()}`).slice(-2); // Current day
const month = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const year = today.getFullYear(); // Current year
// Date today (yyy-mm-dd)
const dateToday = `${year}-${month}-${day}`;
// Date today to check(mm/dd/yyyy)
const dateTodayToCheck = `${month}/${day}/${year}`;
let numberOfCreditSlips = 0;
const firstCreditSlipFileName = '000001.pdf';
const secondCreditSlipFileName = '000002.pdf';

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    foLoginPage: new FOLoginPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
    creditSlipsPage: new CreditSlipsPage(page),
  };
};

/*
Create 2 credit slips for the same order
Filter Credit slips table( by ID, Order ID, Date issued From and To)
Download the 2 credit slip files and check them
 */
describe('Create, filter and check credit slips file', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    /* Delete the generated credit slips */
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${firstCreditSlipFileName}`);
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${secondCreditSlipFileName}`);
    await helper.closeBrowser(browser);
  });

  describe('Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);
      await this.pageObjects.homePage.goToFo();
      await this.pageObjects.homePage.changeLanguage('en');
      const isHomePage = await this.pageObjects.homePage.isHomePage();
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);
      await this.pageObjects.homePage.goToLoginPage();
      const pageTitle = await this.pageObjects.foLoginPage.getPageTitle();
      await expect(pageTitle, 'Fail to open FO login page').to.contains(this.pageObjects.foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);
      await this.pageObjects.foLoginPage.customerLogin(DefaultAccount);
      const isCustomerConnected = await this.pageObjects.foLoginPage.isCustomerConnected();
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);
      await this.pageObjects.foLoginPage.goToHomePage();
      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);
      // Add the created product to the cart
      // Add the created product to the cart
      await this.pageObjects.productPage.addProductToTheCart();
      // Edit the product quantity
      await this.pageObjects.cartPage.editProductQuantity(1, 5);
      // Proceed to checkout the shopping cart
      await this.pageObjects.cartPage.clickOnProceedToCheckout();
      // Address step - Go to delivery step
      const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      // Payment step - Choose payment step
      await this.pageObjects.checkoutPage.choosePaymentAndOrder(PaymentMethods.wirePayment.moduleName);
      const cardTitle = await this.pageObjects.orderConfirmationPage.getOrderConfirmationCardTitle();
      // Check the confirmation message
      await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);
      await this.pageObjects.orderConfirmationPage.logout();
      const isCustomerConnected = await this.pageObjects.orderConfirmationPage.isCustomerConnected();
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  describe('Create 2 credit slips for the same order', async () => {
    // Login into BO
    loginCommon.loginBO();

    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it('should go to the created order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);
      await this.pageObjects.ordersPage.goToOrder(1);
      const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);
      const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    const tests = [
      {args: {productID: 1, quantity: 1, documentRow: 4}},
      {args: {productID: 1, quantity: 2, documentRow: 5}},
    ];
    tests.forEach((test, index) => {
      it('should add a partial refund', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addPartialRefund${index + 1}`, baseContext);
        await this.pageObjects.viewOrderPage.clickOnPartialRefund();
        const textMessage = await this.pageObjects.viewOrderPage.addPartialRefundProduct(
          test.args.productID,
          test.args.quantity,
        );
        await expect(textMessage).to.contains(this.pageObjects.viewOrderPage.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCreditSlipDocument${index + 1}`, baseContext);
        const documentName = await this.pageObjects.viewOrderPage.getDocumentName(test.args.documentRow);
        await expect(documentName).to.be.equal('Credit slip');
      });
    });
  });

  describe('Filter Credit slips', async () => {
    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.creditSlipsLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.creditSlipsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.creditSlipsPage.pageTitle);
    });

    it('should reset all filters and get number of credit slips', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);
      numberOfCreditSlips = await this.pageObjects.creditSlipsPage.resetAndGetNumberOfLines();
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
        await this.pageObjects.creditSlipsPage.filterCreditSlips(
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfCreditSlipsAfterFilter = await this.pageObjects.creditSlipsPage.getNumberOfElementInGrid();
        await expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);
        for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
          const textColumn = await this.pageObjects.creditSlipsPage.getTextColumnFromTableCreditSlips(
            i,
            test.args.columnName,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);
        const numberOfCreditSlipsAfterReset = await this.pageObjects.creditSlipsPage.resetAndGetNumberOfLines();
        await expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
      });
    });

    it('should filter by Date issued \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssued', baseContext);
      await this.pageObjects.creditSlipsPage.filterCreditSlipsByDate(dateToday, dateToday);
      const numberOfCreditSlipsAfterFilter = await this.pageObjects.creditSlipsPage.getNumberOfElementInGrid();
      await expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);
      for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
        const textColumn = await this.pageObjects.creditSlipsPage.getTextColumnFromTableCreditSlips(
          i,
          'date_add',
        );
        await expect(textColumn).to.contains(dateTodayToCheck);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssuedReset', baseContext);
      const numberOfCreditSlipsAfterReset = await this.pageObjects.creditSlipsPage.resetAndGetNumberOfLines();
      await expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
    });
  });

  const creditSlips = [
    {args: {number: 'first', id: 1, fileName: firstCreditSlipFileName}},
    {args: {number: 'second', id: 2, fileName: secondCreditSlipFileName}},
  ];
  creditSlips.forEach((creditSlip) => {
    describe(`Download the ${creditSlip.args.number} Credit slips and check it`, async () => {
      it(`should filter by the credit slip id '${creditSlip.args.id}'`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `filterToDownload${creditSlip.args.number}`,
          baseContext,
        );
        await this.pageObjects.creditSlipsPage.filterCreditSlips(
          'id_credit_slip',
          creditSlip.args.id,
        );
        const textColumn = await this.pageObjects.creditSlipsPage.getTextColumnFromTableCreditSlips(
          1,
          'id_order_slip',
        );
        await expect(textColumn).to.contains(creditSlip.args.id);
      });

      it(`should download the ${creditSlip.args.number} credit slip and check the file existence`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `download${creditSlip.args.number}`, baseContext);
        await this.pageObjects.creditSlipsPage.downloadCreditSlip();
        const exist = await files.doesFileExist(creditSlip.args.fileName);
        await expect(exist).to.be.true;
      });
    });
  });
});
