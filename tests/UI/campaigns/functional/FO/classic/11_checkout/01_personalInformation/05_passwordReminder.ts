// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createAccountTest} from '@commonTests/FO/classic/account';

// Import pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {passwordReminderPage} from '@pages/FO/classic/passwordReminder';

import {
  FakerCustomer,
  foClassicHomePage,
  foClassicMyAccountPage,
  foClassicProductPage,
  type MailDev,
  type MailDevEmail,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_checkout_personalInformation_passwordReminder';

/*
Pre-condition:
- Setup SMTP config
- Create account on FO
Scenario:
- Open FO page
- Add first product to the cart
- Proceed to checkout and validate the cart
- Send an email to reset password
- Reset password
- Try to sign in with old password and check error message
- Try to sign in with new password
Post-condition:
- Delete created customer account
- Go back to default smtp config
 */
describe('FO - Checkout - Personal information : Password reminder', async () => {
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

  // Pre-condition : Create new customer on FO
  createAccountTest(customerData, `${baseContext}_preTest_2`);

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

  describe('Update customer password', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);
      await foClassicProductPage.addProductToTheCart(page, 1);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should proceed to checkout and validate the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should click on sign in then on \'Forgot your password?\' link ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForgetPasswordLink', baseContext);

      await checkoutPage.clickOnSignIn(page);
      await checkoutPage.goToPasswordReminderPage(page);

      const pageTitle = await passwordReminderPage.getPageTitle(page);
      expect(pageTitle).to.equal(passwordReminderPage.pageTitle);
    });

    it('should set the email address and send reset link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordLink', baseContext);

      await passwordReminderPage.sendResetPasswordLink(page, customerData.email);

      const successAlertContent = await passwordReminderPage.checkResetLinkSuccess(page);
      expect(successAlertContent).to.contains(customerData.email);
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
      expect(emailAddress).to.contains(customerData.email);
    });

    it('should change the password and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changePassword', baseContext);

      await passwordReminderPage.setNewPassword(page, newPassword);

      const successMessage = await foClassicMyAccountPage.getSuccessMessageAlert(page);
      expect(successMessage).to.equal(`${foClassicMyAccountPage.resetPasswordSuccessMessage} ${customerData.email}`);
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foClassicMyAccountPage.logout(page);
      const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  describe('Check new customer password', async () => {
    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foClassicMyAccountPage.goToHomePage(page);
      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);
      await foClassicProductPage.addProductToTheCart(page, 1);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should proceed to checkout and validate the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateCart2', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should enter an invalid credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, customerData);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);

      const loginError = await checkoutPage.getLoginError(page);
      expect(loginError).to.contains(checkoutPage.authenticationErrorMessage);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signIn', baseContext);

      const isCustomerConnected = await checkoutPage.customerLogin(page, customerNewPassword);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });
  });

  // Post-condition : Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
