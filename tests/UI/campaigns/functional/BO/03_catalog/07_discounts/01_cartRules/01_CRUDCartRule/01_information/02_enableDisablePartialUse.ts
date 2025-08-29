import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerCartRule,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicMyAccountPage,
  foClassicMyVouchersPage,
  foClassicProductPage,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_information_enableDisablePartialUse';

describe('BO - Catalog - Cart rules : CRUD cart rule with enabled/disabled partial use', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');
  const cartRuleEnabledPartialUse: FakerCartRule = new FakerCartRule({
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
  const cartRuleDisabledPartialUse: FakerCartRule = new FakerCartRule({
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

  const amountValue: number = parseFloat(cartRuleEnabledPartialUse.discountAmount!.value.toString())
    - dataProducts.demo_1.finalPrice;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('case 1 : Create cart rule with enabled partial use then check it on FO', async () => {
    describe('Create cart rule in BO', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Catalog > Discounts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.discountsLink,
        );

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage1', baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule2', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleEnabledPartialUse);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
      });
    });

    describe('Verify discount in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

        // View my shop and init pages
        page = await boCartRulesCreatePage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage1', baseContext);

        await foClassicHomePage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        await foClassicProductPage.addProductToTheCart(page);

        const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount1', baseContext);

        const priceATI = await foClassicCartPage.getATIPrice(page);
        expect(priceATI).to.equal(0);

        const cartRuleName = await foClassicCartPage.getCartRuleName(page);
        expect(cartRuleName).to.equal(cartRuleEnabledPartialUse.name);

        const discountValue = await foClassicCartPage.getCartRuleValue(page);
        expect(discountValue).to.equal(`-€${dataProducts.demo_1.finalPrice.toFixed(2)}`);
      });

      it('should validate shopping cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

        await foClassicCartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      it('should sign in by created customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

        await foClassicCheckoutPage.clickOnSignIn(page);

        const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
        expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to vouchers page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);
        await foClassicMyAccountPage.goToVouchersPage(page);

        const pageHeaderTitle = await foClassicMyVouchersPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(foClassicMyVouchersPage.pageTitle);
      });

      it('should get the number of vouchers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfVouchers', baseContext);

        const numberOfVouchers = await foClassicMyVouchersPage.getNumberOfVouchers(page);
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

          const cartRuleTextColumn = await foClassicMyVouchersPage.getTextColumnFromTableVouchers(
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
        page = await foClassicHomePage.closePage(browserContext, page, 0);

        await boCartRulesPage.reloadPage(page);

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should check the number of cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCartRules', baseContext);

        const numberOfCartRules = await boCartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRules).to.equal(2);
      });

      it('should go to edit the first cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCartRulePage', baseContext);

        await boCartRulesPage.goToEditCartRulePage(page, 1);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
      });

      it('should check the cart rule limit customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleCustomer', baseContext);

        const customer = await boCartRulesCreatePage.getLimitSingleCustomer(page);
        expect(customer).to.equal(
          `${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName} (${dataCustomers.johnDoe.email})`);
      });

      it('should check the cart rule amount value', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleAmountValue', baseContext);

        const amount = await boCartRulesCreatePage.getAmountValue(page);
        expect(amount).to.equal(amountValue.toString());
      });
    });

    describe('bulk delete cart rules', async () => {
      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton', baseContext);

        await boCartRulesCreatePage.clickOnCancelButton(page);

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should bulk delete cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

        const deleteTextResult = await boCartRulesPage.bulkDeleteCartRules(page);
        expect(deleteTextResult).to.be.contains(boCartRulesPage.successfulMultiDeleteMessage);
      });
    });
  });

  describe('case 2 : Create cart rule with disabled partial use then check it on FO', async () => {
    describe('Create cart rule in BO', async () => {
      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it('should create cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule1', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleDisabledPartialUse);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
      });
    });

    describe('Verify discount in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

        // View my shop and init pages
        page = await boCartRulesCreatePage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage2', baseContext);

        await foClassicHomePage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await foClassicProductPage.addProductToTheCart(page);

        const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should verify the total after discount', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount2', baseContext);

        const priceATI = await foClassicCartPage.getATIPrice(page);
        expect(priceATI).to.equal(0);

        const cartRuleName = await foClassicCartPage.getCartRuleName(page);
        expect(cartRuleName).to.equal(cartRuleEnabledPartialUse.name);

        const discountValue = await foClassicCartPage.getCartRuleValue(page);
        expect(discountValue).to.equal(`-€${dataProducts.demo_1.finalPrice.toFixed(2)}`);
      });

      it('should validate shopping cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage2', baseContext);

        await foClassicCartPage.clickOnProceedToCheckout(page);

        const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
        expect(isCheckoutPage).to.eq(true);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep2', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep2', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder2', baseContext);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to vouchers page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFOVouchersPage2', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);
        await foClassicMyAccountPage.goToVouchersPage(page);

        const pageHeaderTitle = await foClassicMyVouchersPage.getPageTitle(page);
        expect(pageHeaderTitle).to.equal(foClassicMyVouchersPage.pageTitle);
      });

      it('should get the number of vouchers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfVouchers2', baseContext);

        const numberOfVouchers = await foClassicMyVouchersPage.getNumberOfVouchers(page);
        expect(numberOfVouchers).to.equal(0);
      });
    });

    describe('Verify discount in BO', async () => {
      it('should go back to BO and reload the page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foClassicHomePage.closePage(browserContext, page, 0);

        await boCartRulesPage.reloadPage(page);

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should check the number of cart rules', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCartRules2', baseContext);

        const numberOfCartRules = await boCartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRules).to.equal(1);
      });
    });

    describe('Delete the created cart rule', async () => {
      it('should delete cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

        const deleteTextResult = await boCartRulesPage.deleteCartRule(page);
        expect(deleteTextResult).to.be.contains(boCartRulesPage.successfulDeleteMessage);
      });
    });
  });
});
