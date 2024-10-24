// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicPasswordReminderPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);

      const passwordResetDelay = await boCustomerSettingsPage.getPasswordResetDelayValue(page);
      expect(passwordResetDelay).to.equal(passwordResetDelayMinutes);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boCustomerSettingsPage.viewMyShop(page);
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

      const pageTitle = await foClassicPasswordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicPasswordReminderPage.pageTitle);
    });

    it('should set the email address and send reset link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordLink', baseContext);

      await foClassicPasswordReminderPage.sendResetPasswordLink(page, dataCustomers.johnDoe.email);

      const successAlertContent = await foClassicPasswordReminderPage.checkResetLinkSuccess(page);
      expect(successAlertContent).to.contains(dataCustomers.johnDoe.email);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResetPasswordMail', baseContext);

      expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });

    it('should open reset password link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openResetPasswordLink', baseContext);

      await foClassicPasswordReminderPage.openForgotPasswordPage(page, newMail.text);

      const pageTitle = await foClassicPasswordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicPasswordReminderPage.pageTitle);
    });

    it('should check the email address to reset password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailAddress', baseContext);

      const emailAddress = await foClassicPasswordReminderPage.getEmailAddressToReset(page);
      expect(emailAddress).to.contains(dataCustomers.johnDoe.email);
    });

    it('should change the password and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePasswordButWithoutConfirmation', baseContext);

      await foClassicPasswordReminderPage.setNewPassword(page, newPassword, '');

      const errorMessage = await foClassicPasswordReminderPage.getErrorMessage(page);
      expect(errorMessage).to.equal(foClassicPasswordReminderPage.errorFillConfirmationMessage);
    });

    it('should change the password and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePassword', baseContext);

      await foClassicPasswordReminderPage.setNewPassword(page, newPassword);

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

      const pageTitle = await foClassicPasswordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicPasswordReminderPage.pageTitle);
    });

    it('should set the email address and send reset link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'retrySendResetPasswordLink', baseContext);

      await foClassicPasswordReminderPage.sendResetPasswordLink(page, dataCustomers.johnDoe.email);

      const regeneratePasswordAlert = await foClassicPasswordReminderPage.getErrorMessage(page);
      expect(regeneratePasswordAlert).to.contains(foClassicPasswordReminderPage.errorRegenerationMessage);
    });
  });

  // Post-Condition: Reset config SMTP
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
