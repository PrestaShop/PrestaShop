require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Importing pages
// BO pages
const dashboardPage = require('@pages/BO/dashboard');
const emailPage = require('@pages/BO/advancedParameters/email');
// FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const passwordReminderPage = require('@pages/FO/passwordReminder');

// Import datas
const {DefaultAccount} = require('@data/demo/customer');



// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_userAccount_editInformation';

let browserContext;
let page;
let newMail;
const smtpServer = '172.17.0.1';
const smtpPort = '1025';
const testMailSubject = "Test message -- Prestashop"
const resetPasswordMailSubject = 'Password query confirmation'

// Enable maildev
const MailDev = require('maildev');

const maildev = new MailDev({
  smtp: smtpPort,
});
// Handle every new email
maildev.on('new', (email) => {
  newMail = email;
});

/*
Go to the smtp parameters page
Setup the smtp parameters in BO
Send a test mail
Check the test mail
Go to login page in FO
Use password reminder
 */

describe('Password reminder', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    maildev.listen((err) => {
      if(err) return console.log(err);
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    maildev.close((err) => {
      console.log('MailDev SMTP Server Shutdown');
      if(err) return console.log(err);
    });
  });

  describe('Go to BO to setup the smtp parameters', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToBo', baseContext);
      await loginCommon.loginBO(this, page);
    });

    it('should go to email parameters page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );

      await emailPage.closeSfToolBar(page);

      const pageTitle = await emailPage.getPageTitle(page);
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should fill the smtp parameters form fields', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillSmtpParametersFormField', baseContext);

      const alertSuccessMessage = await emailPage.setupSmtpParameters(page, smtpServer, DefaultAccount.email,
        DefaultAccount.password, smtpPort);
      await expect(alertSuccessMessage).to.contains(emailPage.successfulUpdateMessage);
    });

    it('should check successful message after sending test email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail', baseContext);

      const textResult = await emailPage.sendTestEmail(page, global.BO.EMAIL);
      await expect(textResult).to.contains(emailPage.sendTestEmailSuccessfulMessage);
    });

    it('should check if test mail is received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailBox', baseContext);

      await expect(newMail.subject).to.contains(testMailSubject);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logOutFromBO', baseContext);
      await loginCommon.logoutBO(this, page);
    });
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

      await passwordReminderPage.sendResetPasswordLink(page, DefaultAccount.email);

      const successAlertContent = await passwordReminderPage.checkResetLinkSuccess(page);
      await expect(successAlertContent).to.contains(DefaultAccount.email);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordLink', baseContext);

      await expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });
  });

  describe('Go to BO and reset to default mail parameters', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToBo', baseContext);
      await loginCommon.loginBO(this, page);
    });

    it('should go to email setup page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );

      await emailPage.closeSfToolBar(page);

      const pageTitle = await emailPage.getPageTitle(page);
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should reset parameters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetMailParameters', baseContext);

      const successParametersReset = await emailPage.resetDefaultParameters(page);
      await expect(successParametersReset).to.contains(emailPage.successfulUpdateMessage);;
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logOutFromBO', baseContext);
      await loginCommon.logoutBO(this, page);
    });
  });
});
