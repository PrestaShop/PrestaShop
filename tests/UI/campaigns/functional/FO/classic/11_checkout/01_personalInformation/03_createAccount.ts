// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';
import mailHelper from '@utils/mailHelper';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

import MailDevEmail from '@data/types/maildevEmail';
import MailDev from 'maildev';

const baseContext: string = 'functional_FO_classic_checkout_personalInformation_createAccount';

/*
Pre-condition:
- Setup SMTP parameters
Scenario:
- Open FO page
- Add first product to the cart
- Proceed to checkout and validate the cart
- Fill personal information
- Complete the order
- Check welcome email
Post-condition:
- Reset SMTP parameters
- Delete customer
 */

describe('FO - Checkout - Personal information : Create account', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const guestData: CustomerData = new CustomerData();
  const addressData: AddressData = new AddressData({country: 'France'});
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

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

  describe('Create account', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
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

    it('should proceed to checkout validate the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should fill guest personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(page, guestData);
      expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.equal(true);
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should check if welcome mail and order confirmation email are in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWelcomeMail', baseContext);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Order confirmation`);
      expect(allEmails[numberOfEmails - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Awaiting bank wire payment`);
      expect(allEmails[numberOfEmails - 3].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Welcome!`);
    });

    it('should check the content of the welcome email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailText', baseContext);

      expect(allEmails[numberOfEmails - 3].text).to.contains(`Hi ${guestData.firstName} ${guestData.lastName}`)
        .and.to.contains(`Thank you for creating a customer account at ${global.INSTALL.SHOP_NAME}.`)
        .and.to.contains(`Email address: ${guestData.email}`);
    });
  });

  // Post-condition: Delete created customer account from BO
  deleteCustomerTest(guestData, `${baseContext}_postTest_1`);

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
