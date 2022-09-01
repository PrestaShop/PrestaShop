require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const {setupSmtpConfigTest, resetSmtpConfigTest} = require('@commonTests/BO/advancedParameters/configSMTP');
const mailHelper = require('@utils/mailHelper');

// Importing pages
// FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const passwordReminderPage = require('@pages/FO/passwordReminder');
const myAccountPage = require('@pages/FO/myAccount');

// Import common tests
const {createAccountTest} = require('@commonTests/FO/createAccount');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import faker data
const CustomerFaker = require('@data/faker/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_login_passwordReminder';

let browserContext;
let page;
let newMail;
const resetPasswordMailSubject = 'Password query confirmation';

// mailListener
let mailListener;

const customerData = new CustomerFaker();
const newPassword = 'new test password';
const customerNewPassword = {email: customerData.email, password: newPassword};

/*
Pre-condition:
- Config smtp
- Create new customer on FO
Scenario:
- Send an email to reset password
- Reset password
- Try to sign in with old password and check error message
- Try to sign in with new password
Post-condition:
- Delete created customer
- Go back to default smtp config
 */
describe('FO - Login : Password reminder', async () => {
  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // Pre-condition : Create new customer on FO
  createAccountTest(customerData, `${baseContext}_preTest_2`);

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

  describe('Go to FO and check the password reminder', async () => {
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

    it('should click on \'Forgot your password?\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPasswordReminderPage', baseContext);

      await loginPage.goToPasswordReminderPage(page);
      const pageTitle = await passwordReminderPage.getPageTitle(page);
      await expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should set the email address and send reset link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordLink', baseContext);

      await passwordReminderPage.sendResetPasswordLink(page, customerData.email);

      const successAlertContent = await passwordReminderPage.checkResetLinkSuccess(page);
      await expect(successAlertContent).to.contains(customerData.email);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResetPasswordMail', baseContext);

      await expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });

    it('should open reset password link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openResetPasswordLink', baseContext);

      await passwordReminderPage.openForgotPasswordPage(page, newMail.text);

      const pageTitle = await passwordReminderPage.getPageTitle(page);
      await expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should check the email address to reset password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailAddress', baseContext);

      const emailAddress = await passwordReminderPage.getEmailAddressToReset(page);
      await expect(emailAddress).to.contains(customerData.email);
    });

    it('should change the password and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePassword', baseContext);

      await passwordReminderPage.setNewPassword(page, newPassword);

      const successMessage = await myAccountPage.getSuccessMessageAlert(page);
      await expect(successMessage).to.equal(`${myAccountPage.resetPasswordSuccessMessage} ${customerData.email}`);
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await myAccountPage.logout(page);
      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });

    it('should try to login with old password and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFOWithOldPassword', baseContext);

      await loginPage.customerLogin(page, customerData);

      const loginError = await loginPage.getLoginError(page);
      await expect(loginError).to.contains(loginPage.loginErrorText);
    });

    it('should sign in with new password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await loginPage.customerLogin(page, customerNewPassword);
      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO2', baseContext);

      await myAccountPage.logout(page);
      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });

    it('should click on \'Forgot your password?\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForgetPassword2', baseContext);

      await loginPage.goToPasswordReminderPage(page);

      const pageTitle = await passwordReminderPage.getPageTitle(page);
      await expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should set the customer email and check the error alert', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await passwordReminderPage.sendResetPasswordLink(page, customerData.email);

      const regeneratePasswordAlert = await passwordReminderPage.getErrorMessage(page);
      await expect(regeneratePasswordAlert).to.contains(passwordReminderPage.errorMessage);
    });
  });

  // Post-condition : Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
