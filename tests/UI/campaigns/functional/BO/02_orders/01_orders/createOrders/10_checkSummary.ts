import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  boOrdersViewBlockMessagesPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerCartRule,
  foClassicCheckoutPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsCore,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const cartRuleWithCodeData: FakerCartRule = new FakerCartRule({
    name: 'WithCode',
    code: 'Discount',
    discountType: 'Amount',
    discountAmount: {
      value: 8,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
  });
  const paymentMethodModuleName: string = dataPaymentMethods.checkPayment.moduleName;
  const orderMessage: string = 'Test order message';

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, `${baseContext}_preTest_1`);

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

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

  // 1 - Go to create order page and add product to cart
  describe('Go to create order page and add a product to the cart', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it(`should choose customer ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

      const isCartsTableVisible = await boOrdersCreatePage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });

    it('should check that summary block is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryNotVisible', baseContext);

      const isSummaryBlockVisible = await boOrdersCreatePage.isSummaryBlockVisible(page);
      expect(isSummaryBlockVisible, 'Summary block is visible!').to.eq(false);
    });

    it(`should add to cart '${dataProducts.demo_12.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addStandardSimpleProduct', baseContext);

      const productToSelect = `${dataProducts.demo_12.name} - €${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`;
      await boOrdersCreatePage.addProductToCart(page, dataProducts.demo_12, productToSelect);

      const result = await boOrdersCreatePage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_12.thumbImage),
        expect(result.description).to.equal(dataProducts.demo_12.name),
        expect(result.reference).to.equal(dataProducts.demo_12.reference),
        expect(result.price).to.equal(dataProducts.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check that summary block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryVisible', baseContext);

      const isSummaryBlockVisible = await boOrdersCreatePage.isSummaryBlockVisible(page);
      expect(isSummaryBlockVisible, 'Summary block is not visible!').to.eq(true);
    });
  });

  // 2 - Check summary block
  describe('Check summary block', async () => {
    describe('Check summary information', async () => {
      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock1', baseContext);

        const totalTaxes = utilsCore.percentage(dataProducts.demo_12.priceTaxExcluded, dataProducts.demo_12.tax);

        const result = await boOrdersCreatePage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('€0.00'),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${dataProducts.demo_12.price.toFixed(2)}`),
        ]);
      });

      it('should add for the created voucher with code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher1', baseContext);

        const voucherToSelect = await boOrdersCreatePage.searchVoucher(page, cartRuleWithCodeData.name);
        expect(voucherToSelect).to.equal(`${cartRuleWithCodeData.name} - ${cartRuleWithCodeData.code}`);

        const result = await boOrdersCreatePage.getVoucherDetailsFromTable(page);
        await Promise.all([
          expect(result.name).to.contains(cartRuleWithCodeData.name),
          expect(result.value).to.equal(cartRuleWithCodeData.discountAmount!.value),
        ]);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock2', baseContext);

        const discount: number = parseFloat(cartRuleWithCodeData.discountAmount!.value.toString());
        const totalTaxes = utilsCore.percentage(
          dataProducts.demo_12.priceTaxExcluded - discount,
          20,
        );
        const totalTaxExcluded = dataProducts.demo_12.priceTaxExcluded - discount;
        const totalTaxIncluded = totalTaxes + totalTaxExcluded;

        const result = await boOrdersCreatePage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalVouchers).to.equal(`-€${discount.toFixed(2)}`),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${totalTaxExcluded.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxIncluded.toFixed(2)}`),
        ]);
      });

      it('should delete the voucher', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteVoucher', baseContext);

        await boOrdersCreatePage.removeVoucher(page, 1);

        const isVoucherTableNotVisible = await boOrdersCreatePage.isVouchersTableNotVisible(page);
        expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.eq(true);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock3', baseContext);

        const totalTaxes = utilsCore.percentage(dataProducts.demo_12.priceTaxExcluded, dataProducts.demo_12.tax);

        const result = await boOrdersCreatePage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('€0.00'),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${dataProducts.demo_12.price.toFixed(2)}`),
        ]);
      });

      it(`should choose the carrier '${dataCarriers.myCarrier.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseCarrier', baseContext);

        const shippingPriceTTC = await boOrdersCreatePage.setDeliveryOption(
          page, `${dataCarriers.myCarrier.name} - Delivery next day!`,
        );
        expect(shippingPriceTTC).to.equal(`€${dataCarriers.myCarrier.priceTTC.toFixed(2)}`);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock4', baseContext);

        const totalTaxExc = (dataProducts.demo_12.priceTaxExcluded + dataCarriers.myCarrier.price).toFixed(2);
        const totalTaxInc = (dataProducts.demo_12.price + dataCarriers.myCarrier.priceTTC).toFixed(2);

        const result = await boOrdersCreatePage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalShipping).to.equal(`€${dataCarriers.myCarrier.price.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${totalTaxExc}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxInc}`),
        ]);
      });
    });

    describe('Test \'More actions\' button', async () => {
      it('should choose \'Send pre-filled order to the customer by email\' from more actions', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setMoreActions', baseContext);

        const textMessage = await boOrdersCreatePage.setMoreActionsPreFilledOrder(page);
        expect(textMessage, 'Invalid success message!').to.be.equal(boOrdersCreatePage.emailSendSuccessMessage);
      });

      it('should check if the mail is in mailbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkIfMailIsInMailbox', baseContext);

        expect(newMail.subject).to.eq(`[${global.INSTALL.SHOP_NAME}] Process the payment of your order`);
        expect(newMail.text).to.contains('A new order has been generated on your behalf.');
      });

      it('should choose \'Proceed to checkout in the front office\' from more actions', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

        page = await boOrdersCreatePage.setMoreActionsProceedToCheckout(page);

        const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
        expect(isCheckoutPage, 'Not redirected to checkout page!').to.eq(true);
      });

      it('should close the checkout page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

        page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

        const pageTitle = await boOrdersCreatePage.getPageTitle(page);
        expect(pageTitle, 'Fo page not closed!').to.contains(boOrdersCreatePage.pageTitle);
      });
    });

    describe('Test \'Create order\' button', async () => {
      it('should set order message, click on create order and check that the order is not created', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCreateOrder', baseContext);

        await boOrdersCreatePage.setOrderMessage(page, orderMessage);

        const isOrderCreated = await boOrdersCreatePage.clickOnCreateOrderButton(page, false);
        expect(isOrderCreated, 'The order is created!').to.eq(false);
      });

      it('should choose payment method, click on create button then check that the order is not created',
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnCreateOrder2', baseContext);

          await boOrdersCreatePage.setPaymentMethod(page, paymentMethodModuleName);

          const isOrderCreated = await boOrdersCreatePage.clickOnCreateOrderButton(page, false);
          expect(isOrderCreated, 'The order is created!').to.eq(false);
        });

      it('should choose payment method, order status then click on create order and check that the order is create',
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnCreateOrder3', baseContext);

          await boOrdersCreatePage.setPaymentMethod(page, paymentMethodModuleName);
          await boOrdersCreatePage.setOrderStatus(page, dataOrderStatuses.paymentAccepted);

          const isOrderCreated = await boOrdersCreatePage.clickOnCreateOrderButton(page, true);
          expect(isOrderCreated, 'The order is created!').to.eq(true);
        });

      it('should check that the page displayed is view order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrder message', baseContext);

        const pageTitle = await boOrdersViewBlockMessagesPage.getPageTitle(page);
        expect(pageTitle, 'View order page is not displayed!').to.contain(
          boOrdersViewBlockMessagesPage.pageTitle,
        );
      });

      it('should check that the order message is displayed on view order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderMessage', baseContext);

        const textMessage = await boOrdersViewBlockMessagesPage.getTextMessage(page, 1, 'customer');
        expect(textMessage, 'Message is not correct!').to.contains(orderMessage);
      });
    });
  });

  // Post-condition: Delete created cart rule
  deleteCartRuleTest(cartRuleWithCodeData.name, `${baseContext}_postTest_1`);

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
