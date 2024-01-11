// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import MailDevEmail from '@data/types/maildevEmail';
import mailHelper from '@utils/mailHelper';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import CustomerSettingsOptions from '@pages/BO/shopParameters/customerSettings/options';
import emailPage from '@pages/BO/advancedParameters/email';
import customersPage from '@pages/BO/customers';

// Import FO pages
import {homePage} from '@pages/FO/home';
import {loginPage as loginFOPage} from '@pages/FO/login';
import {createAccountPage} from '@pages/FO/myAccount/add';

// Import data
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import MailDev from 'maildev';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_customers_sendEmailAfterRegistration';

/*
Disable send an email after registration
Create customer account
Check that there is no email sent to the new customer in 'Advanced Parameters > Email'
Enable send an email after registration
Create customer account
Check that there is an email sent to the new customer in 'Advanced Parameters > Email'
 */
describe('BO - Shop Parameters - Customer Settings : Enable/Disable send an email after registration', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;
  let numberOfCustomers: number = 0;

  const firstCustomerToCreate: CustomerData = new CustomerData();
  const secondCustomerToCreate: CustomerData = new CustomerData();

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(baseContext);

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe('Enable/Disable send an email after registration', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    const tests = [
      {
        args: {
          action: 'disable', enable: false, customer: firstCustomerToCreate, nbrAfterFilter: 0,
        },
      },
      {
        args: {
          action: 'enable', enable: true, customer: secondCustomerToCreate, nbrAfterFilter: 1,
        },
      },
    ];

    tests.forEach((test, index) => {
      it('should go to \'Shop parameters > Customer Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCustomerSettingsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.customerSettingsLink,
        );
        await customerSettingsPage.closeSfToolBar(page);

        const pageTitle = await customerSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} send an email after registration`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}SendEmail`, baseContext);

        const result = await customerSettingsPage.setOptionStatus(
          page,
          CustomerSettingsOptions.OPTION_EMAIL_REGISTRATION,
          test.args.enable,
        );
        expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Go to FO
        page = await customerSettingsPage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should create a customer account from FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomerAccount${index}`, baseContext);

        // Create account
        await homePage.goToLoginPage(page);
        await loginFOPage.goToCreateAccountPage(page);
        await createAccountPage.createAccount(page, test.args.customer);

        const connected = await createAccountPage.isCustomerConnected(page);
        expect(connected, 'Customer is not created in FO').to.eq(true);
      });

      it('should logout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `logoutFO_${index}`, baseContext);

        // Logout from FO
        await createAccountPage.logout(page);

        const connected = await homePage.isCustomerConnected(page);
        expect(connected, 'Customer is connected in FO').to.eq(false);
      });

      if (index === 1) {
        it('should check if the mail is in mailbox and check the subject', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkMailIsInMailbox${index}`, baseContext);

          expect(newMail.subject).to.contains('Welcome!');
        });
      }

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackTOBO${index}`, baseContext);

        page = await createAccountPage.closePage(browserContext, page, 0);

        const pageTitle = await customerSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
      });

      it('should go to \'Advanced parameters > E-mail\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEmailPage${index}`, baseContext);

        await customerSettingsPage.goToSubMenu(
          page,
          customerSettingsPage.advancedParametersLink,
          customerSettingsPage.emailLink,
        );

        const pageTitle = await emailPage.getPageTitle(page);
        expect(pageTitle).to.contains(emailPage.pageTitle);
      });

      it('should check if there is a welcome email for the new customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchNewCustomerEmail${index}`, baseContext);

        await emailPage.filterEmailLogs(page, 'input', 'recipient', test.args.customer.email);

        const numberOfEmailAfterFilter = await emailPage.getNumberOfElementInGrid(page);
        expect(numberOfEmailAfterFilter).to.be.equal(test.args.nbrAfterFilter);
      });
    });
  });

  describe('POST-TEST : Delete the two created customers', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await emailPage.goToSubMenu(
        page,
        emailPage.customersParentLink,
        emailPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });

    [
      {args: {customerToDelete: firstCustomerToCreate}},
      {args: {customerToDelete: secondCustomerToCreate}},
    ].forEach((test, index: number) => {
      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index + 1}`, baseContext);

        await customersPage.resetFilter(page);

        await customersPage.filterCustomers(
          page,
          'input',
          'email',
          test.args.customerToDelete.email,
        );

        const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
        expect(textEmail).to.contains(test.args.customerToDelete.email);
      });

      it('should delete customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCustomer${index + 1}`, baseContext);

        const textResult = await customersPage.deleteCustomer(page, 1);
        expect(textResult).to.equal(customersPage.successfulDeleteMessage);

        const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers - (index + 1));
      });
    });
  });

  // Post-Condition: Reset config SMTP
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
