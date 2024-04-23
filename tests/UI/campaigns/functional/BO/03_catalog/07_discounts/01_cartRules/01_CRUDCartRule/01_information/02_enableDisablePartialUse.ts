// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';
import {cartPage} from '@pages/FO/classic/cart';
import {homePage} from '@pages/FO/classic/home';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {productPage as foProductPage} from '@pages/FO/classic/product';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {vouchersPage as foVouchersPage} from '@pages/FO/classic/myAccount/vouchers';
import {checkoutPage} from '@pages/FO/classic/checkout';

// Import data
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {
  // Import data
  dataCustomers,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_information_enableDisablePartialUse';

describe('BO - Catalog - Cart rules : CRUD cart rule with enabled/disabled partial use', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  const cartRuleEnabledPartialUse: CartRuleData = new CartRuleData({
    name: 'partialUseEnabled',
    partialUse: true,
    dateFrom: pastDate,
    discountType: 'Amount',
    discountAmount: {
      value: 100,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });
  const cartRuleDisabledPartialUse: CartRuleData = new CartRuleData({
    name: 'partialUseEnabled',
    partialUse: false,
    dateFrom: pastDate,
    discountType: 'Amount',
    discountAmount: {
      value: 100,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  const amountValue: number = cartRuleEnabledPartialUse.discountAmount!.value - Products.demo_1.finalPrice;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('case 1 : Create cart rule with enabled partial use then check it on FO', async () => {
    describe('Create cart rule in BO', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Catalog > Discounts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.discountsLink,
        );

        const pageTitle = await cartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage1', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule2', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleEnabledPartialUse);
        expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });

    describe('Verify discount in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

        // View my shop and init pages
        page = await addCartRulePage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

        await homePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount1', baseContext);

        const priceATI = await cartPage.getATIPrice(page);
        expect(priceATI).to.equal(0);

        const cartRuleName = await cartPage.getCartRuleName(page);
        expect(cartRuleName).to.equal(cartRuleEnabledPartialUse.name);

        const discountValue = await cartPage.getDiscountValue(page);
        expect(discountValue.toString()).to.equal(`-${Products.demo_1.finalPrice}`);
      });

      it('should validate shopping cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

        await cartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      it('should sign in by created customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

        await checkoutPage.clickOnSignIn(page);

        const isCustomerConnected = await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
        expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to vouchers page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage', baseContext);

        await homePage.goToMyAccountPage(page);
        await myAccountPage.goToVouchersPage(page);

        const pageHeaderTitle = await foVouchersPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(foVouchersPage.pageTitle);
      });

      it('should get the number of vouchers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfVouchers', baseContext);

        const numberOfVouchers = await foVouchersPage.getNumberOfVouchers(page);
        expect(numberOfVouchers).to.equal(1);
      });

      [
        {args: {column: 'description', row: 1, value: cartRuleEnabledPartialUse.name}},
        {args: {column: 'quantity', row: 1, value: '1'}},
        {args: {column: 'value', row: 1, value: `€${amountValue} Tax included`}},
        {args: {column: 'minimum', row: 1, value: 'None'}},
        {args: {column: 'cumulative', row: 1, value: 'Yes'}},
      ].forEach((cartRule, index: number) => {
        it(`should check the voucher ${cartRule.args.column} n°${cartRule.args.row}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkVoucher${index}`, baseContext);

          const cartRuleTextColumn = await foVouchersPage.getTextColumnFromTableVouchers(
            page,
            cartRule.args.row,
            cartRule.args.column,
          );
          expect(cartRuleTextColumn).to.equal(cartRule.args.value);
        });
      });
    });

    describe('Verify discount in BO', async () => {
      it('should go back to BO and reload the page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

        // Close tab and init other page objects with new current tab
        page = await homePage.closePage(browserContext, page, 0);

        await cartRulesPage.reloadPage(page);

        const pageTitle = await cartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should check the number of cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCartRules', baseContext);

        const numberOfCartRules = await cartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRules).to.equal(2);
      });

      it('should go to edit the first cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage', baseContext);

        await cartRulesPage.goToEditCartRulePage(page, 1);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        expect(pageTitle).to.contains(addCartRulePage.editPageTitle);
      });

      it('should check the cart rule limit customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleCustomer', baseContext);

        const customer = await addCartRulePage.getLimitSingleCustomer(page);
        expect(customer).to.equal(
          `${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName} (${dataCustomers.johnDoe.email})`);
      });

      it('should check the cart rule amount value', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleAmountValue', baseContext);

        const amount = await addCartRulePage.getAmountValue(page);
        expect(amount).to.equal(amountValue.toString());
      });
    });

    describe('bulk delete cart rules', async () => {
      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton', baseContext);

        await addCartRulePage.clickOnCancelButton(page);

        const pageTitle = await cartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should bulk delete cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

        const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
        expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
      });
    });
  });

  describe('case 2 : Create cart rule with disabled partial use then check it on FO', async () => {
    describe('Create cart rule in BO', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule1', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleDisabledPartialUse);
        expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });

    describe('Verify discount in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

        // View my shop and init pages
        page = await addCartRulePage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

        await homePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount2', baseContext);

        const priceATI = await cartPage.getATIPrice(page);
        expect(priceATI).to.equal(0);

        const cartRuleName = await cartPage.getCartRuleName(page);
        expect(cartRuleName).to.equal(cartRuleEnabledPartialUse.name);

        const discountValue = await cartPage.getDiscountValue(page);
        expect(discountValue.toString()).to.equal(`-${Products.demo_1.finalPrice}`);
      });

      it('should validate shopping cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage2', baseContext);

        await cartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep2', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep2', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder2', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to vouchers page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage2', baseContext);

        await homePage.goToMyAccountPage(page);
        await myAccountPage.goToVouchersPage(page);

        const pageHeaderTitle = await foVouchersPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(foVouchersPage.pageTitle);
      });

      it('should get the number of vouchers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfVouchers2', baseContext);

        const numberOfVouchers = await foVouchersPage.getNumberOfVouchers(page);
        expect(numberOfVouchers).to.equal(0);
      });
    });

    describe('Verify discount in BO', async () => {
      it('should go back to BO and reload the page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await homePage.closePage(browserContext, page, 0);

        await cartRulesPage.reloadPage(page);

        const pageTitle = await cartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should check the number of cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCartRules2', baseContext);

        const numberOfCartRules = await cartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRules).to.equal(1);
      });
    });

    describe('Delete the created cart rule', async () => {
      it('should delete cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

        const deleteTextResult = await cartRulesPage.deleteCartRule(page);
        expect(deleteTextResult).to.be.contains(cartRulesPage.successfulDeleteMessage);
      });
    });
  });
});
