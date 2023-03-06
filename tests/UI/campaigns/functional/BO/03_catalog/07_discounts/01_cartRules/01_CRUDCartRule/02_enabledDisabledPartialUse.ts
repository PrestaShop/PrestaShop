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
import cartPage from '@pages/FO/cart';
import foHomePage from '@pages/FO/home';
import foProductPage from '@pages/FO/product';

// Import data
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import foCartPage from "@pages/FO/cart";
import checkoutPage from "@pages/FO/checkout";
import Customers from "@data/demo/customers";
import PaymentMethods from "@data/demo/paymentMethods";
import orderConfirmationPage from "@pages/FO/checkout/orderConfirmation";
import homePage from "@pages/FO/home";
import foMyAccountPage from "@pages/FO/myAccount";
import foVouchersPage from "@pages/FO/myAccount/vouchers";

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_enabledDisabledPartialUse';

describe('BO - Catalog - Cart rules : CRUD cart rule with enabled/disabled partial use', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  const cartRuleEnabledPartialUse: CartRuleData = new CartRuleData({
    dateFrom: pastDate,
    name: 'partialUseEnabled',
    partialUse: true,
    discountType: 'Amount',
    discountAmount: {
      value: 100,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });
  const amountValue = cartRuleEnabledPartialUse.discountAmount.value - Products.demo_1.finalPrice;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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
    await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  describe('case 1 : Create cart rule with enabled partial use then check it on FO', async () => {
    describe('Create cart rule in BO', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleEnabledPartialUse);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });

    describe('Verify discount in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

        // View my shop and init pages
        page = await addCartRulePage.viewMyShop(page);
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        await expect(isHomePage, 'Fail to open FO home page').to.be.true;
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

        await foHomePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        await expect(pageTitle.toUpperCase()).to.contains(Products.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        await foProductPage.addProductToTheCart(page);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount1', baseContext);

        const priceATI = await cartPage.getATIPrice(page);
        await expect(priceATI).to.equal(0);

        const cartRuleName = await cartPage.getCartRuleName(page);
        await expect(cartRuleName).to.equal(cartRuleEnabledPartialUse.name);

        const discountValue = await cartPage.getDiscountValue(page);
        await expect(discountValue.toString()).to.equal(`-${Products.demo_1.finalPrice}`);
      });

      it('should validate shopping cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

        await foCartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
        await expect(isCheckoutPage).to.be.true;
      });

      it('should sign in by created customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

        await checkoutPage.clickOnSignIn(page);

        const isCustomerConnected = await checkoutPage.customerLogin(page, Customers.johnDoe);
        await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to vouchers page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage', baseContext);

        await homePage.goToMyAccountPage(page);
        await foMyAccountPage.goToVouchersPage(page);

        const pageHeaderTitle = await foVouchersPage.getPageTitle(page);
        await expect(pageHeaderTitle).to.equal(foVouchersPage.pageTitle);
      });

      [
        {args: {column: 'code', row: 1, value: ''}},
        {args: {column: 'description', row: 1, value: cartRuleEnabledPartialUse.name}},
        {args: {column: 'quantity', row: 1, value: '0'}},
        {args: {column: 'value', row: 1, value: '€100.00 Tax included'}},
        {args: {column: 'minimum', row: 1, value: 'None'}},
        {args: {column: 'cumulative', row: 1, value: 'Yes'}},
        {args: {column: 'code', row: 1, value: cartRuleEnabledPartialUse.code}},
        {args: {column: 'quantity', row: 2, value: '1'}},
        {args: {column: 'value', row: 2, value: `€${amountValue} Tax included`}},
        {args: {column: 'minimum', row: 2, value: 'None'}},
        {args: {column: 'cumulative', row: 2, value: 'Yes'}},
      ].forEach((cartRule, index: number) => {
        it(`should check the voucher ${cartRule.args.column} n°${cartRule.args.row}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkVoucher${index}`, baseContext);

          const cartRuleTextColumn = await foVouchersPage.getTextColumnFromTableVouchers(
            page,
            cartRule.args.row,
            cartRule.args.column,
          );
          await expect(cartRuleTextColumn).to.equal(cartRule.args.value);
        });
      });
    });

    describe('Verify discount in BO', async () => {
      it('should go back to BO and reload the page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foHomePage.closePage(browserContext, page, 0);

        await cartRulesPage.reloadPage(page);

        const pageTitle = await cartRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should check the number of cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCartRules', baseContext);

        const numberOfCartRules = await cartRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCartRules).to.equal(2);
      });

      it('should go to edit the first cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage', baseContext);

        await cartRulesPage.goToEditCartRulePage(page, 1);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.editPageTitle);
      });

      it('should check the cart rule limit customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleCustomer', baseContext);

        const customer = await addCartRulePage.getLimitSingleCustomer(page);
        await expect(customer).to.equal(
          `${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName} (${Customers.johnDoe.email})`);
      });

      it('should check the cart rule amount value', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleAmountValue', baseContext);

        const amount = await addCartRulePage.getAmountValue(page);
        await expect(amount).to.equal(amountValue.toString());
      });
    });

    describe('bulk delete cart rules', async () => {
      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton', baseContext);

        await addCartRulePage.clickOnCancelButton(page);

        const pageTitle = await cartRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should bulk delete cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

        const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
        await expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
      });
    });
  });
});
