// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {cartPage} from '@pages/FO/cart';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';
import {homePage} from '@pages/FO/home';
import checkoutPage from '@pages/FO/checkout';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import PaymentMethodData from '@data/faker/paymentMethod';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_checkout_payment_choosePaymentMethod';

describe('FO - Checkout - Payment : Choose a payment method', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  [
    PaymentMethods.wirePayment,
    PaymentMethods.checkPayment,
    PaymentMethods.cashOnDelivery,
  ].forEach((test: PaymentMethodData, index : number) => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFo${index}`, baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 1);
      await homePage.proceedToCheckout(page);

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

        const isCustomerConnected = await checkoutPage.customerLogin(page, Customers.johnDoe);
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
  });
});
