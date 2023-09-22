// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';
import mailHelper from '@utils/mailHelper';

// Import common tests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createAccountTest} from '@commonTests/FO/account';

// Import pages
import {homePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import {passwordReminderPage} from '@pages/FO/passwordReminder';
import {myAccountPage} from '@pages/FO/myAccount';

// Import data
import CustomerData from '@data/faker/customer';
import type MailDevEmail from '@data/types/maildevEmail';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import type MailDev from 'maildev';

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
  const customerData: CustomerData = new CustomerData();
  const newPassword: string = 'new test password';
  const customerNewPassword: CustomerData = new CustomerData();
  customerNewPassword.email = customerData.email;
  customerNewPassword.password = newPassword;

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
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    mailHelper.stopListener(mailListener);
  });

  describe('Update customer password', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await homePage.goToProductPage(page, 1);
      await productPage.addProductToTheCart(page, 1);

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

      const successMessage = await myAccountPage.getSuccessMessageAlert(page);
      expect(successMessage).to.equal(`${myAccountPage.resetPasswordSuccessMessage} ${customerData.email}`);
    });

    it('should logout from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await myAccountPage.logout(page);
      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  describe('Check new customer password', async () => {
    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await myAccountPage.goToHomePage(page);
      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await homePage.goToProductPage(page, 1);
      await productPage.addProductToTheCart(page, 1);

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
