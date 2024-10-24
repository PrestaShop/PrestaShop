// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boEmailPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  //  Erase list of mails
  describe('Erase emails', async () => {
    it('should go to \'Advanced Parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPageToEraseEmails', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.emailLink,
      );
      await boDashboardPage.closeSfToolBar(page);

      const pageTitle = await boEmailPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmailPage.pageTitle);
    });

    it('should erase all emails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'eraseEmails', baseContext);

      const textResult = await boEmailPage.eraseAllEmails(page);
      expect(textResult).to.equal(boEmailPage.successfulDeleteMessage);

      const numberOfLines = await boEmailPage.getNumberOfElementInGrid(page);
      expect(numberOfLines).to.be.equal(0);
    });
  });

  // 1 - Create 6 orders to have 12 emails in the list
  describe('Create 6 orders to have emails in the table', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // Click on view my shop
      page = await boDashboardPage.viewMyShop(page);

      // Change language on FO
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    const tests: number[] = new Array(6).fill(0, 0, 6);

    tests.forEach((test: number, index: number) => {
      it(`should create the order n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrder${index}`, baseContext);

        // Go to home page
        await foClassicLoginPage.goToHomePage(page);

        // Go to the first product page
        await foClassicHomePage.goToProductPage(page, 1);

        // Add the created product to the cart
        await foClassicProductPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foClassicCheckoutOrderConfirmationPage.logout(page);
      const isCustomerConnected = await foClassicCheckoutOrderConfirmationPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go Back to BO
      page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await boEmailPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmailPage.pageTitle);
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should go to \'Advanced parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

      await boEmailPage.reloadPage(page);

      const pageTitle = await boEmailPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmailPage.pageTitle);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await boEmailPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boEmailPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boEmailPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boEmailPage.selectPaginationLimit(page, 20);
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

        const nonSortedTable = await boEmailPage.getAllRowsColumnContent(page, test.args.sortBy);
        await boEmailPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boEmailPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else if (test.args.isDate) {
          const expectedResult = await utilsCore.sortArrayDate(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 4 - Delete all emails
  describe('Delete emails by bulk action', async () => {
    it('should delete all emails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await boEmailPage.deleteEmailLogsBulkActions(page);
      expect(deleteTextResult).to.be.equal(boEmailPage.successfulMultiDeleteMessage);
    });
  });
});
