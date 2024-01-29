// Import utils
import helper from '@utils/helpers';
import mailHelper from '@utils/mailHelper';
import testContext from '@utils/testContext';

// Import commonTests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage} from '@pages/FO/classic/login';
import {createAccountPage} from '@pages/FO/classic/myAccount/add';

// Import data
import CustomerData from '@data/faker/customer';
import type MailDevEmail from '@data/types/maildevEmail';

import {expect} from 'chai';
import type MailDev from 'maildev';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_login_createAccount';

describe('FO - Login : Create account', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  const customerData: CustomerData = new CustomerData();

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    mailHelper.stopListener(mailListener);
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest`);

  describe('FO - Login : Create account', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await loginPage.getPageTitle(page);
      expect(pageTitle).to.equal(loginPage.pageTitle);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await loginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await createAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(createAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await createAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Created customer is not connected!').to.eq(true);
    });

    it('should check if the page is redirected to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isHomePage', baseContext);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to redirect to FO home page!').to.eq(true);
    });

    it('should check if welcome mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWelcomeMail', baseContext);

      numberOfEmails = allEmails.length;
      expect(numberOfEmails).to.equal(1);
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Welcome!`);
    });

    it('should check the content of the email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailText', baseContext);

      expect(allEmails[numberOfEmails - 1].text).to.contains(`Hi ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(`Thank you for creating a customer account at ${global.INSTALL.SHOP_NAME}.`)
        .and.to.contains(`Email address: ${customerData.email}`);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });
  });

  // Post-condition: Delete created customer account from BO
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
