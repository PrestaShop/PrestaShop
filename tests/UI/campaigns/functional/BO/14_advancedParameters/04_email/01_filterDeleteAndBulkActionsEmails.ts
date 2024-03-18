// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import emailPage from '@pages/BO/advancedParameters/email';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';

// Import data
import Languages from '@data/demo/languages';
import PaymentMethods from '@data/demo/paymentMethods';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_email_filterDeleteAndBulkActionsEmails';

/*
Create an order to have 2 email logs in email table
Filter email logs list
Delete email log
Delete email logs by bulk action
 */
describe('BO - Advanced Parameters - Email : Filter, delete and bulk delete emails', async () => {
  const today:string = date.getDateFormat('yyyy-mm-dd');

  let browserContext: BrowserContext;
  let page: Page;
  let numberOfEmails: number = 0;

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

  describe('Create order to have emails in the table', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // Click on view my shop
      page = await dashboardPage.viewMyShop(page);

      // Change language in FO
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.contains(cartPage.pageTitle);
    });

    it('should proceed to checkout and sign in', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Personal information step - Login
      await checkoutPage.clickOnSignIn(page);
      await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should pay the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'payTheOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutFO', baseContext);

      // Logout from FO
      await orderConfirmationPage.logout(page);

      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go Back to BO
      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await dashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });
  });

  describe('Filter E-mail table', async () => {
    it('should go to \'Advanced Parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );

      const pageTitle = await emailPage.getPageTitle(page);
      expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should reset all filters and get number of email logs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      numberOfEmails = await emailPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmails).to.be.above(0);
    });
    const tests = [
      {
        args:
          {
            identifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_mail',
            filterValue: '1',
          },
      },
      {
        args:
          {
            identifier: 'filterByRecipient',
            filterType: 'input',
            filterBy: 'recipient',
            filterValue: dataCustomers.johnDoe.email,
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
      it(`should filter E-mail table by '${test.args.filterBy}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

        await emailPage.filterEmailLogs(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfEmailsAfterFilter = await emailPage.getNumberOfElementInGrid(page);
        expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfEmails);

        for (let row = 1; row <= numberOfEmailsAfterFilter; row++) {
          const textColumn = await emailPage.getTextColumn(page, test.args.filterBy, row);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.identifier}Reset`, baseContext);

        const numberOfEmailsAfterReset = await emailPage.resetAndGetNumberOfLines(page);
        expect(numberOfEmailsAfterReset).to.be.equal(numberOfEmails);
      });
    });

    it('should filter E-mail table by date sent \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDateSent', baseContext);

      await emailPage.filterEmailLogsByDate(page, today, today);

      const numberOfEmailsAfterFilter = await emailPage.getNumberOfElementInGrid(page);
      expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfEmails);

      for (let row = 1; row <= numberOfEmailsAfterFilter; row++) {
        const textColumn = await emailPage.getTextColumn(page, 'date_add', row);
        expect(textColumn).to.contains(today);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'dateSentReset', baseContext);

      const numberOfEmailsAfterReset = await emailPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmailsAfterReset).to.be.equal(numberOfEmails);
    });
  });

  describe('Delete E-mail', async () => {
    it('should filter email list by \'subject\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBySubjectToDelete', baseContext);

      await emailPage.filterEmailLogs(page, 'input', 'subject', PaymentMethods.wirePayment.name);

      const numberOfEmailsAfterFilter = await emailPage.getNumberOfElementInGrid(page);
      expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfEmails);
    });

    it('should delete email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmail', baseContext);

      const textResult = await emailPage.deleteEmailLog(page, 1);
      expect(textResult).to.equal(emailPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmailsAfterReset = await emailPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmailsAfterReset).to.be.equal(numberOfEmails - 1);
    });
  });

  describe('Delete E-mail by bulk action', async () => {
    it('should delete all emails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await emailPage.deleteEmailLogsBulkActions(page);
      expect(deleteTextResult).to.be.equal(emailPage.successfulMultiDeleteMessage);
    });
  });
});
