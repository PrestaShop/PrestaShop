require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const {setupSmtpConfigTest, resetSmtpConfigTest} = require('@commonTests/configSMTP');
const mailHelper = require('@utils/mailHelper');

// Importing pages
// FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const passwordReminderPage = require('@pages/FO/passwordReminder');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');


// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_login_passwordReminder';

let browserContext;
let page;
let newMail;
const resetPasswordMailSubject = 'Password query confirmation';

// mailListener
let mailListener;


/*
Go to the smtp parameters page
Setup the smtp parameters in BO
Send a test mail
Check the test mail
Go to login page in FO
Use password reminder
 */

describe('FO - Login : Password reminder', async () => {
  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);
    // Handle every new email
    mailListener.on('new', (email) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    mailHelper.stopListener(mailListener);
  });

  describe('Go to FO to use the password reminder', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);
      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await loginPage.getPageTitle(page);
      await expect(pageTitle).to.equal(loginPage.pageTitle);
    });

    it('should go to password reminder page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPasswordReminderPage', baseContext);

      await loginPage.goToPasswordReminderPage(page);
      const pageTitle = await passwordReminderPage.getPageTitle(page);
      await expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should send reset password link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordLink', baseContext);

      await passwordReminderPage.sendResetPasswordLink(page, DefaultCustomer.email);

      const successAlertContent = await passwordReminderPage.checkResetLinkSuccess(page);
      await expect(successAlertContent).to.contains(DefaultCustomer.email);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResetPasswordMail', baseContext);

      await expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(baseContext);
});
