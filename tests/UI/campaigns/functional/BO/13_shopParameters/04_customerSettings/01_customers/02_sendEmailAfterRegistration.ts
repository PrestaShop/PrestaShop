import testContext from '@utils/testContext';
import {expect} from 'chai';

import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boCustomersPage,
  boCustomerSettingsPage,
  boDashboardPage,
  boEmailPage,
  boLoginPage,
  type BrowserContext,
  FakerCustomer,
  foClassicCreateAccountPage,
  foClassicHomePage,
  foClassicLoginPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const firstCustomerToCreate: FakerCustomer = new FakerCustomer();
  const secondCustomerToCreate: FakerCustomer = new FakerCustomer();

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(baseContext);

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe('Enable/Disable send an email after registration', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.customerSettingsLink,
        );
        await boCustomerSettingsPage.closeSfToolBar(page);

        const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} send an email after registration`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}SendEmail`, baseContext);

        const result = await boCustomerSettingsPage.setOptionStatus(
          page,
          boCustomerSettingsPage.OPTION_EMAIL_REGISTRATION,
          test.args.enable,
        );
        expect(result).to.contains(boCustomerSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Go to FO
        page = await boCustomerSettingsPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should create a customer account from FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomerAccount${index}`, baseContext);

        // Create account
        await foClassicHomePage.goToLoginPage(page);
        await foClassicLoginPage.goToCreateAccountPage(page);
        await foClassicCreateAccountPage.createAccount(page, test.args.customer);

        const connected = await foClassicCreateAccountPage.isCustomerConnected(page);
        expect(connected, 'Customer is not created in FO').to.eq(true);
      });

      it('should logout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `logoutFO_${index}`, baseContext);

        // Logout from FO
        await foClassicCreateAccountPage.logout(page);

        const connected = await foClassicHomePage.isCustomerConnected(page);
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

        page = await foClassicCreateAccountPage.closePage(browserContext, page, 0);

        const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
      });

      it('should go to \'Advanced parameters > E-mail\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEmailPage${index}`, baseContext);

        await boCustomerSettingsPage.goToSubMenu(
          page,
          boCustomerSettingsPage.advancedParametersLink,
          boCustomerSettingsPage.emailLink,
        );

        const pageTitle = await boEmailPage.getPageTitle(page);
        expect(pageTitle).to.contains(boEmailPage.pageTitle);
      });

      it('should check if there is a welcome email for the new customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchNewCustomerEmail${index}`, baseContext);

        await boEmailPage.filterEmailLogs(page, 'input', 'recipient', test.args.customer.email);

        const numberOfEmailAfterFilter = await boEmailPage.getNumberOfElementInGrid(page);
        expect(numberOfEmailAfterFilter).to.be.equal(test.args.nbrAfterFilter);
      });
    });
  });

  describe('POST-TEST : Delete the two created customers', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await boEmailPage.goToSubMenu(
        page,
        boEmailPage.customersParentLink,
        boEmailPage.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });

    [
      {args: {customerToDelete: firstCustomerToCreate}},
      {args: {customerToDelete: secondCustomerToCreate}},
    ].forEach((test, index: number) => {
      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index + 1}`, baseContext);

        await boCustomersPage.resetFilter(page);

        await boCustomersPage.filterCustomers(
          page,
          'input',
          'email',
          test.args.customerToDelete.email,
        );

        const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
        expect(textEmail).to.contains(test.args.customerToDelete.email);
      });

      it('should delete customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCustomer${index + 1}`, baseContext);

        const textResult = await boCustomersPage.deleteCustomer(page, 1);
        expect(textResult).to.equal(boCustomersPage.successfulDeleteMessage);

        const numberOfCustomersAfterDelete = await boCustomersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers - (index + 1));
      });
    });
  });

  // Post-Condition: Reset config SMTP
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
