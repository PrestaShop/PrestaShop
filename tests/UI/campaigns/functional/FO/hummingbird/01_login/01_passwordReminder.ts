// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import createAccountTest from '@commonTests/FO/hummingbird/account';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  FakerCustomer,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdPasswordReminderPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_login_passwordReminder';

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
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const resetPasswordMailSubject: string = 'Password query confirmation';
  const customerData: FakerCustomer = new FakerCustomer();
  const newPassword: string = 'new test password';
  const customerNewPassword: FakerCustomer = new FakerCustomer();
  customerNewPassword.email = customerData.email;
  customerNewPassword.password = newPassword;

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_2`);

  // Pre-condition : Create new customer on FO
  createAccountTest(customerData, `${baseContext}_preTest_3`);

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

  describe('Go to FO and check the password reminder', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should click on \'Forgot your password?\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPasswordReminderPage', baseContext);

      await foHummingbirdLoginPage.goToPasswordReminderPage(page);

      const pageTitle = await foHummingbirdPasswordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdPasswordReminderPage.pageTitle);
    });

    it('should set the email address and send reset link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordLink', baseContext);

      await foHummingbirdPasswordReminderPage.sendResetPasswordLink(page, customerData.email);

      const successAlertContent = await foHummingbirdPasswordReminderPage.checkResetLinkSuccess(page);
      expect(successAlertContent).to.contains(customerData.email);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResetPasswordMail', baseContext);

      expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });

    it('should open reset password link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openResetPasswordLink', baseContext);

      await foHummingbirdPasswordReminderPage.openForgotPasswordPage(page, newMail.text);

      const pageTitle = await foHummingbirdPasswordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdPasswordReminderPage.pageTitle);
    });

    it('should check the email address to reset password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailAddress', baseContext);

      const emailAddress = await foHummingbirdPasswordReminderPage.getEmailAddressToReset(page);
      expect(emailAddress).to.contains(customerData.email);
    });

    it('should change the password and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePassword', baseContext);

      await foHummingbirdPasswordReminderPage.setNewPassword(page, newPassword);

      const successMessage = await foHummingbirdMyAccountPage.getSuccessMessageAlert(page);
      expect(successMessage).to.equal(`${foHummingbirdMyAccountPage.resetPasswordSuccessMessage} ${customerData.email}`);
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foHummingbirdMyAccountPage.logout(page);
      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should try to login with old password and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFOWithOldPassword', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, customerData, false);

      const loginError = await foHummingbirdLoginPage.getLoginError(page);
      expect(loginError).to.contains(foHummingbirdLoginPage.loginErrorText);
    });

    it('should sign in with new password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, customerNewPassword);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO2', baseContext);

      await foHummingbirdMyAccountPage.logout(page);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should click on \'Forgot your password?\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForgetPassword2', baseContext);

      await foHummingbirdLoginPage.goToPasswordReminderPage(page);

      const pageTitle = await foHummingbirdPasswordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdPasswordReminderPage.pageTitle);
    });

    it('should set the customer email and check the error alert', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await foHummingbirdPasswordReminderPage.sendResetPasswordLink(page, customerData.email);

      const regeneratePasswordAlert = await foHummingbirdPasswordReminderPage.getErrorMessage(page);
      expect(regeneratePasswordAlert).to.contains(foHummingbirdPasswordReminderPage.errorRegenerationMessage);
    });
  });

  // Post-condition : Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_3`);
});
