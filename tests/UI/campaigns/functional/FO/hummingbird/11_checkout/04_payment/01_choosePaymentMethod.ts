// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import orderConfirmationPage from '@pages/FO/hummingbird/checkout/orderConfirmation';
import homePage from '@pages/FO/hummingbird/home';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import PaymentMethodData from '@data/faker/paymentMethod';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import MailDevEmail from '@data/types/maildevEmail';
import MailDev from 'maildev';

const baseContext: string = 'functional_FO_hummingbird_checkout_payment_choosePaymentMethod';

describe('FO - Checkout - Payment : Choose a payment method', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_0`);

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_1`);

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

  describe('Choose a payment method', async () => {
    [
      PaymentMethods.wirePayment,
      PaymentMethods.checkPayment,
      PaymentMethods.cashOnDelivery,
    ].forEach((test: PaymentMethodData, index: number) => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFo${index}`, baseContext);

        await homePage.goToFo(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should quick view the first product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `quickViewFirstProduct${index}`, baseContext);

        await homePage.quickViewProduct(page, 1);

        const isQuickViewModal = await quickViewModal.isQuickViewProductModalVisible(page);
        expect(isQuickViewModal).to.equal(true);
      });

      it('should add the first product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await quickViewModal.addToCartByQuickView(page);
        await blockCartModal.proceedToCheckout(page);

        const pageTitle = await cartPage.getPageTitle(page);
        expect(pageTitle).to.eq(cartPage.pageTitle);
      });

      it('should proceed to checkout and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `proceedToCheckout${index}`, baseContext);

        await cartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      if (index === 0) {
        it('should signin', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `signin${index}`, baseContext);

          await checkoutPage.clickOnSignIn(page);

          const isCustomerConnected = await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
          expect(isCustomerConnected, 'Customer is connected').to.eq(true);
        });
      }

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, test.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it(`should check the payment method is ${test.displayName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMethod${index}`, baseContext);

        const paymentMethod = await orderConfirmationPage.getPaymentMethod(page);
        expect(paymentMethod).to.be.equal(test.displayName);
      });

      it('should check if order and payment confirmation mails are in mailbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMail${index}`, baseContext);

        numberOfEmails = allEmails.length;
        expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Order confirmation`);

        if (index === 0) {
          expect(allEmails[numberOfEmails - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Awaiting bank wire payment`);
        } else if (index === 1) {
          expect(allEmails[numberOfEmails - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Awaiting check payment`);
        }
      });
    });
  });

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_0`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);
});
