import testContext from '@utils/testContext';
import {expect} from 'chai';

import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {
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

const baseContext: string = 'functional_FO_classic_login_createAccount';

describe('FO - Login : Create account', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const customerData: FakerCustomer = new FakerCustomer();

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    utilsMail.stopListener(mailListener);
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest`);

  describe('FO - Login : Create account', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foClassicLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foClassicCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foClassicCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Created customer is not connected!').to.eq(true);
    });

    it('should check if the page is redirected to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isHomePage', baseContext);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to redirect to FO home page!').to.eq(true);
    });

    it('should check if welcome mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWelcomeMail', baseContext);

      expect(newMail.subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Welcome!`);
    });

    it('should check the content of the email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailText', baseContext);

      expect(newMail.text).to.contains(`Hi ${customerData.firstName} ${customerData.lastName}`)
        .and.to.contains(`Thank you for creating a customer account at ${global.INSTALL.SHOP_NAME}.`)
        .and.to.contains(`Email address: ${customerData.email}`);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foClassicHomePage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });
  });

  // Post-condition: Delete created customer account from BO
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
