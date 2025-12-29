import testContext from '@utils/testContext';
import {expect} from 'chai';

import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  type FakerPaymentMethod,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  enableTheme('hummingbird', `${baseContext}_preTest_1`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    utilsMail.stopListener(mailListener);
  });

  describe('Choose a payment method', async () => {
    [
      dataPaymentMethods.wirePayment,
      dataPaymentMethods.checkPayment,
      dataPaymentMethods.cashOnDelivery,
    ].forEach((test: FakerPaymentMethod, index: number) => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFo${index}`, baseContext);

        await foHummingbirdHomePage.goToFo(page);
        await foHummingbirdHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should quick view the first product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `quickViewFirstProduct${index}`, baseContext);

        await foHummingbirdHomePage.quickViewProduct(page, 1);

        const isQuickViewModal = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
        expect(isQuickViewModal).to.equal(true);
      });

      it('should add the first product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
        await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

        const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
        expect(pageTitle).to.eq(foHummingbirdCartPage.pageTitle);
      });

      it('should proceed to checkout and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `proceedToCheckout${index}`, baseContext);

        await foHummingbirdCartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      if (index === 0) {
        it('should signin', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `signin${index}`, baseContext);

          await foHummingbirdCheckoutPage.clickOnSignIn(page);

          const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
          expect(isCustomerConnected, 'Customer is connected').to.eq(true);
        });
      }

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

        // Payment step - Choose payment step
        await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, test.moduleName);

        // Check the confirmation message
        const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it(`should check the payment method is ${test.displayName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMethod${index}`, baseContext);

        const paymentMethod = await foHummingbirdCheckoutOrderConfirmationPage.getPaymentMethod(page);
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
  disableTheme('hummingbird', `${baseContext}_postTest_1`);
});
