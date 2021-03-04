require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const emailPage = require('@pages/BO/advancedParameters/email');
const foLoginPage = require('@pages/FO/login');
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_email_sortAndPagination';

let browserContext;
let page;

/*
Create 6 orders to have 12 emails
Pagination
Sort by Id, Recipient, Template, Language, Subject, Send
Delete by bulk actions
 */
describe('Sort and pagination emails', async () => {
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

  //  Erase list of mails
  describe('Erase emails', async () => {
    it('should go to \'Advanced parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPageToEraseEmails', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );

      await dashboardPage.closeSfToolBar(page);

      const pageTitle = await emailPage.getPageTitle(page);
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should erase all emails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'eraseEmails', baseContext);

      const textResult = await emailPage.eraseAllEmails(page);
      await expect(textResult).to.equal(emailPage.successfulDeleteMessage);

      const numberOfLines = await emailPage.getNumberOfElementInGrid(page);
      await expect(numberOfLines).to.be.equal(0);
    });
  });

  // 1 - Create 6 orders to have 12 emails in the list
  describe('Create 6 orders to have email logs', async () => {
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

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should go to \'Advanced parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );

      await dashboardPage.closeSfToolBar(page);

      const pageTitle = await emailPage.getPageTitle(page);
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await emailPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await emailPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await emailPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await emailPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort emails
  const tests = [
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_mail', sortDirection: 'desc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByRecipientAsc', sortBy: 'recipient', sortDirection: 'asc',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByRecipientDesc', sortBy: 'recipient', sortDirection: 'desc',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByTemplateDesc', sortBy: 'template', sortDirection: 'desc',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByTemplateAsc', sortBy: 'template', sortDirection: 'asc',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByLanguageDesc', sortBy: 'language', sortDirection: 'desc',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByLanguageAsc', sortBy: 'language', sortDirection: 'asc',
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
          testIdentifier: 'sortBySubjectDesc', sortBy: 'subject', sortDirection: 'desc',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortBySubjectAsc', sortBy: 'subject', sortDirection: 'asc',
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_mail', sortDirection: 'asc', isFloat: true,
        },
    },
  ];

  describe('Sort emails table', async () => {
    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await emailPage.getAllRowsColumnContent(page, test.args.sortBy);
        await emailPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await emailPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await emailPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 - Delete all emails
  describe('Delete emails by bulk action', async () => {
    it('should delete all emails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await emailPage.deleteEmailLogsBulkActions(page);
      await expect(deleteTextResult).to.be.equal(emailPage.successfulMultiDeleteMessage);
    });
  });
});
