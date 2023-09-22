// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import mailHelper from '@utils/mailHelper';
import testContext from '@utils/testContext';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';
import orderPageMessagesBlock from '@pages/BO/orders/view/messagesBlock';
// Import FO pages
import checkoutPage from '@pages/FO/checkout';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';
import type MailDevEmail from '@data/types/maildevEmail';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import type MailDev from 'maildev';

const baseContext: string = 'functional_BO_orders_orders_createOrders_checkSummary';

/*
Pre-condition:
- Create cart rule
Scenario:
- Go to create order page, choose customer and add product to cart
- Check summary information
- Add voucher/ Delete voucher then check summary information
- Check 'Create order button'
- Check 'More actions button'
Post-condition:
- Delete created cart rule
 */
describe('BO - Orders - Create order : Check summary', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  // Data to create cart rule with code
  const cartRuleWithCodeData: CartRuleData = new CartRuleData({
    name: 'WithCode',
    code: 'Discount',
    discountType: 'Amount',
    discountAmount: {
      value: 8,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
  });
  const paymentMethodModuleName: string = PaymentMethods.checkPayment.moduleName;
  const orderMessage: string = 'Test order message';

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, `${baseContext}_preTest_1`);

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  // 1 - Go to create order page and add product to cart
  describe('Go to create order page and add a product to the cart', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });

    it('should check that summary block is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryNotVisible', baseContext);

      const isSummaryBlockVisible = await addOrderPage.isSummaryBlockVisible(page);
      expect(isSummaryBlockVisible, 'Summary block is visible!').to.eq(false);
    });

    it(`should add to cart '${Products.demo_12.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addStandardSimpleProduct', baseContext);

      const productToSelect = `${Products.demo_12.name} - €${Products.demo_12.priceTaxExcluded.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_12, productToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_12.thumbImage),
        expect(result.description).to.equal(Products.demo_12.name),
        expect(result.reference).to.equal(Products.demo_12.reference),
        expect(result.price).to.equal(Products.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check that summary block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryVisible', baseContext);

      const isSummaryBlockVisible = await addOrderPage.isSummaryBlockVisible(page);
      expect(isSummaryBlockVisible, 'Summary block is not visible!').to.eq(true);
    });
  });

  // 2 - Check summary block
  describe('Check summary block', async () => {
    describe('Check summary information', async () => {
      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock1', baseContext);

        const totalTaxes = await basicHelper.percentage(Products.demo_12.priceTaxExcluded, Products.demo_12.tax);

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('€0.00'),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price.toFixed(2)}`),
        ]);
      });

      it('should add for the created voucher with code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher1', baseContext);

        const voucherToSelect = await addOrderPage.searchVoucher(page, cartRuleWithCodeData.name);
        expect(voucherToSelect).to.equal(`${cartRuleWithCodeData.name} - ${cartRuleWithCodeData.code}`);

        const result = await addOrderPage.getVoucherDetailsFromTable(page);
        await Promise.all([
          expect(result.name).to.contains(cartRuleWithCodeData.name),
          expect(result.value).to.equal(cartRuleWithCodeData.discountAmount!.value),
        ]);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock2', baseContext);

        const totalTaxes = await basicHelper.percentage(
          Products.demo_12.priceTaxExcluded - cartRuleWithCodeData.discountAmount!.value,
          20,
        );
        const totalTaxExcluded = Products.demo_12.priceTaxExcluded - cartRuleWithCodeData.discountAmount!.value;
        const totalTaxIncluded = totalTaxes + totalTaxExcluded;

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalVouchers).to.equal(`-€${cartRuleWithCodeData.discountAmount!.value.toFixed(2)}`),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${totalTaxExcluded.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxIncluded.toFixed(2)}`),
        ]);
      });

      it('should delete the voucher', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteVoucher', baseContext);

        await addOrderPage.removeVoucher(page, 1);

        const isVoucherTableNotVisible = await addOrderPage.isVouchersTableNotVisible(page);
        expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.eq(true);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock3', baseContext);

        const totalTaxes = await basicHelper.percentage(Products.demo_12.priceTaxExcluded, Products.demo_12.tax);

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('€0.00'),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price.toFixed(2)}`),
        ]);
      });

      it(`should choose the carrier '${Carriers.myCarrier.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseCarrier', baseContext);

        const shippingPriceTTC = await addOrderPage.setDeliveryOption(
          page, `${Carriers.myCarrier.name} - Delivery next day!`,
        );
        expect(shippingPriceTTC).to.equal(`€${Carriers.myCarrier.priceTTC.toFixed(2)}`);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock4', baseContext);

        const totalTaxExc = (Products.demo_12.priceTaxExcluded + Carriers.myCarrier.price).toFixed(2);
        const totalTaxInc = (Products.demo_12.price + Carriers.myCarrier.priceTTC).toFixed(2);

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalShipping).to.equal(`€${Carriers.myCarrier.price.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${totalTaxExc}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxInc}`),
        ]);
      });
    });

    describe('Test \'More actions\' button', async () => {
      it('should choose \'Send pre-filled order to the customer by email\' from more actions', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setMoreActions', baseContext);

        const textMessage = await addOrderPage.setMoreActionsPreFilledOrder(page);
        expect(textMessage, 'Invalid success message!').to.be.equal(addOrderPage.emailSendSuccessMessage);
      });

      it('should check if the mail is in mailbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkIfMailIsInMailbox', baseContext);

        expect(newMail.subject).to.eq(`[${global.INSTALL.SHOP_NAME}] Process the payment of your order`);
        expect(newMail.text).to.contains('A new order has been generated on your behalf.');
      });

      it('should choose \'Proceed to checkout in the front office\' from more actions', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

        page = await addOrderPage.setMoreActionsProceedToCheckout(page);

        const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
        expect(isCheckoutPage, 'Not redirected to checkout page!').to.eq(true);
      });

      it('should close the checkout page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

        page = await checkoutPage.closePage(browserContext, page, 0);

        const pageTitle = await addOrderPage.getPageTitle(page);
        expect(pageTitle, 'Fo page not closed!').to.contains(addOrderPage.pageTitle);
      });
    });

    describe('Test \'Create order\' button', async () => {
      it('should set order message, click on create order and check that the order is not created', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCreateOrder', baseContext);

        await addOrderPage.setOrderMessage(page, orderMessage);

        const isOrderCreated = await addOrderPage.clickOnCreateOrderButton(page, false);
        expect(isOrderCreated, 'The order is created!').to.eq(false);
      });

      it('should choose payment method, click on create button then check that the order is not created',
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnCreateOrder2', baseContext);

          await addOrderPage.setPaymentMethod(page, paymentMethodModuleName);

          const isOrderCreated = await addOrderPage.clickOnCreateOrderButton(page, false);
          expect(isOrderCreated, 'The order is created!').to.eq(false);
        });

      it('should choose payment method, order status then click on create order and check that the order is create',
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnCreateOrder3', baseContext);

          await addOrderPage.setPaymentMethod(page, paymentMethodModuleName);
          await addOrderPage.setOrderStatus(page, OrderStatuses.paymentAccepted);

          const isOrderCreated = await addOrderPage.clickOnCreateOrderButton(page, true);
          expect(isOrderCreated, 'The order is created!').to.eq(true);
        });

      it('should check that the page displayed is view order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrder message', baseContext);

        const pageTitle = await orderPageMessagesBlock.getPageTitle(page);
        expect(pageTitle, 'View order page is not displayed!').to.contain(
          orderPageMessagesBlock.pageTitle,
        );
      });

      it('should check that the order message is displayed on view order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderMessage', baseContext);

        const textMessage = await orderPageMessagesBlock.getTextMessage(page, 1, 'customer');
        expect(textMessage, 'Message is not correct!').to.contains(orderMessage);
      });
    });
  });

  // Post-condition: Delete created cart rule
  deleteCartRuleTest(cartRuleWithCodeData.name, `${baseContext}_postTest_1`);

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
