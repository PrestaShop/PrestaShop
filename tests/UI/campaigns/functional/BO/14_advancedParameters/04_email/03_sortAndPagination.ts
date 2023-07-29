// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import emailPage from '@pages/BO/advancedParameters/email';

// Import FO pages
import {loginPage as foLoginPage} from '@pages/FO/login';
import {homePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_email_sortAndPagination';

/*
Create 6 orders to have 12 emails
Pagination
Sort by Id, Recipient, Template, Language, Subject, Send
Delete by bulk actions
 */
describe('BO - Advanced Parameters - E-mail : Sort and pagination emails', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    it('should go to \'Advanced Parameters > E-mail\' page', async function () {
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
  describe('Create 6 orders to have emails in the table', async () => {
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

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    const tests: number[] = new Array(6).fill(0, 0, 6);

    tests.forEach((test: number, index: number) => {
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

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go Back to BO
      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await emailPage.getPageTitle(page);
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should go to \'Advanced parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

      await emailPage.reloadPage(page);

      const pageTitle = await emailPage.getPageTitle(page);
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await emailPage.selectPaginationLimit(page, 10);
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

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await emailPage.selectPaginationLimit(page, 20);
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
          testIdentifier: 'sortByDateAddDesc', sortBy: 'date_add', sortDirection: 'desc', isDate: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByDateAddAsc', sortBy: 'date_add', sortDirection: 'asc', isDate: true,
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

  describe('Sort E-mail table', async () => {
    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await emailPage.getAllRowsColumnContent(page, test.args.sortBy);
        await emailPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await emailPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else if (test.args.isDate) {
          const expectedResult = await basicHelper.sortArrayDate(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
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
