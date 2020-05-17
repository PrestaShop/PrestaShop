require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_email_filterDeleteAndBulkActionsEmails';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmailPage = require('@pages/BO/advancedParameters/email');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');
const {Languages} = require('@data/demo/languages');


let browser;
let page;
let numberOfEmails = 0;
const today = new Date();
const day = (`0${today.getDate()}`).slice(-2); // Current day
const month = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const year = today.getFullYear(); // Current year
// Date today (yyy-mm-dd)
const dateToday = `${year}-${month}-${day}`;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    emailPage: new EmailPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

/*
Create an order to have 2 email logs in email table
Filter email logs list
Delete email log
Delete email logs by bulk action
 */
describe('Filter, delete and bulk actions email log', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Create order to have email logs', async () => {
    it('should go to FO and create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderInFO', baseContext);
      // Click on view my shop
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);
      // Add the created product to the cart
      await this.pageObjects.productPage.addProductToTheCart();
      // Proceed to checkout the shopping cart
      await this.pageObjects.cartPage.clickOnProceedToCheckout();
      // Checkout the order
      // Personal information step - Login
      await this.pageObjects.checkoutPage.clickOnSignIn();
      await this.pageObjects.checkoutPage.customerLogin(DefaultAccount);
      // Address step - Go to delivery step
      const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      // Payment step - Choose payment step
      await this.pageObjects.checkoutPage.choosePaymentAndOrder(PaymentMethods.wirePayment.moduleName);
      const cardTitle = await this.pageObjects.orderConfirmationPage
        .getTextContent(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitleH3);
      // Check the confirmation message
      await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
      // Logout from FO
      await this.pageObjects.foBasePage.logout();
      page = await this.pageObjects.orderConfirmationPage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });

  describe('Filter email logs table', async () => {
    it('should go to \'Advanced parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.advancedParametersLink,
        this.pageObjects.boBasePage.emailLink,
      );
      const pageTitle = await this.pageObjects.emailPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.emailPage.pageTitle);
    });

    it('should reset all filters and get number of email logs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);
      numberOfEmails = await this.pageObjects.emailPage.resetAndGetNumberOfLines();
      await expect(numberOfEmails).to.be.above(0);
    });
    const tests = [
      {
        args:
          {
            identifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_mail',
            filterValue: 1,
          },
      },
      {
        args:
          {
            identifier: 'filterByRecipient',
            filterType: 'input',
            filterBy: 'recipient',
            filterValue: DefaultAccount.email,
          },
      },
      {
        args:
          {
            identifier: 'filterByTemplate',
            filterType: 'input',
            filterBy: 'template',
            filterValue: 'order_conf',
          },
      },
      {
        args:
          {
            identifier: 'filterByLanguage',
            filterType: 'select',
            filterBy: 'id_lang',
            filterValue: Languages.english.name,
          },
      },
      {
        args:
          {
            identifier: 'filterBySubject',
            filterType: 'input',
            filterBy: 'subject',
            filterValue: PaymentMethods.wirePayment.name.toLowerCase(),
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter email logs by '${test.args.filterBy}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);
        await this.pageObjects.emailPage.filterEmailLogs(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfEmailsAfterFilter = await this.pageObjects.emailPage.getNumberOfElementInGrid();
        await expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfEmails);
        for (let row = 1; row <= numberOfEmailsAfterFilter; row++) {
          const textColumn = await this.pageObjects.emailPage.getTextColumn(test.args.filterBy, row);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.identifier}Reset`, baseContext);
        const numberOfEmailsAfterReset = await this.pageObjects.emailPage.resetAndGetNumberOfLines();
        await expect(numberOfEmailsAfterReset).to.be.equal(numberOfEmails);
      });
    });

    it('should filter email logs by date sent \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDateSent', baseContext);
      await this.pageObjects.emailPage.filterEmailLogsByDate(dateToday, dateToday);
      const numberOfEmailsAfterFilter = await this.pageObjects.emailPage.getNumberOfElementInGrid();
      await expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfEmails);
      for (let row = 1; row <= numberOfEmailsAfterFilter; row++) {
        const textColumn = await this.pageObjects.emailPage.getTextColumn('date_add', row);
        await expect(textColumn).to.contains(dateToday);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'dateSentReset', baseContext);
      const numberOfEmailsAfterReset = await this.pageObjects.emailPage.resetAndGetNumberOfLines();
      await expect(numberOfEmailsAfterReset).to.be.equal(numberOfEmails);
    });
  });

  describe('Delete email log', async () => {
    it('should filter email logs by \'subject\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBySubjectToDelete', baseContext);
      await this.pageObjects.emailPage.filterEmailLogs('input', 'subject', PaymentMethods.wirePayment.name);
      const numberOfEmailsAfterFilter = await this.pageObjects.emailPage.getNumberOfElementInGrid();
      await expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfEmails);
    });

    it('should delete email log', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmail', baseContext);
      const textResult = await this.pageObjects.emailPage.deleteEmailLog(1);
      await expect(textResult).to.equal(this.pageObjects.emailPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);
      const numberOfEmailsAfterReset = await this.pageObjects.emailPage.resetAndGetNumberOfLines();
      await expect(numberOfEmailsAfterReset).to.be.equal(numberOfEmails - 1);
    });
  });

  describe('Delete email logs by bulk action', async () => {
    it('should delete all email logs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);
      const deleteTextResult = await this.pageObjects.emailPage.deleteEmailLogsBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.emailPage.successfulMultiDeleteMessage);
    });
  });
});
