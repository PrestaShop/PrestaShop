import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  type FakerPaymentMethod,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_checkout_payment_choosePaymentMethod';

describe('FO - Checkout - Payment : Choose a payment method', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest`);

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

        await foClassicHomePage.goToFo(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should quickView the first product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `quickViewFirstProduct${index}`, baseContext);

        await foClassicHomePage.quickViewProduct(page, 1);

        const isQuickViewModal = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
        expect(isQuickViewModal).to.equal(true);
      });

      it('should add the first product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await foClassicModalQuickViewPage.addToCartByQuickView(page);
        await foClassicModalBlockCartPage.proceedToCheckout(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
      });

      it('should proceed to checkout and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `proceedToCheckout${index}`, baseContext);

        await foClassicCartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      if (index === 0) {
        it('should signin', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `signin${index}`, baseContext);

          await foClassicCheckoutPage.clickOnSignIn(page);

          const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
          expect(isCustomerConnected, 'Customer is connected').to.eq(true);
        });
      }

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, test.moduleName);

        // Check the confirmation message
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it(`should check the payment method is ${test.displayName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMethod${index}`, baseContext);

        const paymentMethod = await foClassicCheckoutOrderConfirmationPage.getPaymentMethod(page);
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
  resetSmtpConfigTest(`${baseContext}_postTest`);
});
