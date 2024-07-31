// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';

// Import FO pages
import {passwordReminderPage} from '@pages/FO/classic/passwordReminder';

import {
  boDashboardPage,
  dataCustomers,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  type MailDev,
  type MailDevEmail,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_customers_passwordResetDelay';

describe('BO - Shop Parameters - Customer Settings : Password reset delay', async () => {
  const passwordResetDelayMinutes: number = 360;
  const resetPasswordMailSubject: string = 'Password query confirmation';
  const newPassword: string = 'prestashop';

  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

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

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(baseContext);

  describe('Password reset delay', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await customerSettingsPage.closeSfToolBar(page);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerSettingsPage.pageTitle);

      const passwordResetDelay = await customerSettingsPage.getPasswordResetDelayValue(page);
      expect(passwordResetDelay).to.equal(passwordResetDelayMinutes);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await customerSettingsPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO2', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);
    });

    it('should click on \'Forgot your password?\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPasswordReminderPage', baseContext);

      await foClassicLoginPage.goToPasswordReminderPage(page);

      const pageTitle = await passwordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should set the email address and send reset link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordLink', baseContext);

      await passwordReminderPage.sendResetPasswordLink(page, dataCustomers.johnDoe.email);

      const successAlertContent = await passwordReminderPage.checkResetLinkSuccess(page);
      expect(successAlertContent).to.contains(dataCustomers.johnDoe.email);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResetPasswordMail', baseContext);

      expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });

    it('should open reset password link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openResetPasswordLink', baseContext);

      await passwordReminderPage.openForgotPasswordPage(page, newMail.text);

      const pageTitle = await passwordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should check the email address to reset password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailAddress', baseContext);

      const emailAddress = await passwordReminderPage.getEmailAddressToReset(page);
      expect(emailAddress).to.contains(dataCustomers.johnDoe.email);
    });

    it('should change the password and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePasswordButWithoutConfirmation', baseContext);

      await passwordReminderPage.setNewPassword(page, newPassword, '');

      const errorMessage = await passwordReminderPage.getErrorMessage(page);
      expect(errorMessage).to.equal(passwordReminderPage.errorFillConfirmationMessage);
    });

    it('should change the password and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePassword', baseContext);

      await passwordReminderPage.setNewPassword(page, newPassword);

      const successMessage = await foClassicMyAccountPage.getSuccessMessageAlert(page);
      expect(successMessage).to.equal(`${foClassicMyAccountPage.resetPasswordSuccessMessage} ${dataCustomers.johnDoe.email}`);
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foClassicMyAccountPage.logout(page);

      const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);
    });

    it('should click on \'Forgot your password?\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPasswordReminderPageAndRetry', baseContext);

      await foClassicLoginPage.goToPasswordReminderPage(page);

      const pageTitle = await passwordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should set the email address and send reset link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'retrySendResetPasswordLink', baseContext);

      await passwordReminderPage.sendResetPasswordLink(page, dataCustomers.johnDoe.email);

      const regeneratePasswordAlert = await passwordReminderPage.getErrorMessage(page);
      expect(regeneratePasswordAlert).to.contains(passwordReminderPage.errorRegenerationMessage);
    });
  });

  // Post-Condition: Reset config SMTP
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
