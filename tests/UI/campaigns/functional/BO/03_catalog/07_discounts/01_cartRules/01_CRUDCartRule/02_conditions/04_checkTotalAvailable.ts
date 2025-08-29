import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

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
  foClassicProductPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_checkTotalAvailable';

/*
Scenario:
- Create cart rule with total available = 1
- Go to FO, Add product to cart
- Add promo code and check total after discount
- Complete the order
- Try to use a second time the same promo code anc check the error message
Post-condition:
- Delete the created cart rule
*/
describe('BO - Catalog - Cart rules : Check Total available', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const cartRuleCode: FakerCartRule = new FakerCartRule({
    name: 'New cart rule',
    code: '4QABV6L3',
    quantity: 1,
    discountType: 'Percent',
    discountPercent: 20,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO : Create cart rule', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it('should create cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleCode);
      expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      // View my shop and init pages
      page = await boCartRulesCreatePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });
  });

  [
    {args: {testIdentifier: 'cartRuleAccepted', testTitle: 'for the first time'}},
    {args: {testIdentifier: 'cartRuleNotAccepted', testTitle: 'for the second time'}},
  ].forEach((test, index) => {
    describe(`FO : Check the created cart rule '${test.args.testTitle}'`, async () => {
      it('should go to the first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

        await foClassicHomePage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
      });

      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await foClassicProductPage.addProductToTheCart(page);

        const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should set the promo code', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addPromoCode${index}`, baseContext,
        );

        await foClassicCartPage.addPromoCode(page, cartRuleCode.code);
      });

      if (test.args.testIdentifier === 'cartRuleAccepted') {
        it('should verify the total after discount', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'verifyTotalAfterDiscount', baseContext);

          const discountedPrice = dataProducts.demo_1.finalPrice
            - utilsCore.percentage(dataProducts.demo_1.finalPrice, cartRuleCode.getDiscountPercent());

          const totalAfterDiscount = await foClassicCartPage.getATIPrice(page);
          expect(totalAfterDiscount).to.equal(parseFloat(discountedPrice.toFixed(2)));
        });

        it('should proceed to checkout', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

          // Proceed to checkout the shopping cart
          await foClassicCartPage.clickOnProceedToCheckout(page);

          const isCheckout = await foClassicCheckoutPage.isCheckoutPage(page);
          expect(isCheckout).to.eq(true);
        });

        it('should sign in by default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

          await foClassicCheckoutPage.clickOnSignIn(page);

          const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
          expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
        });

        it('should go to delivery address step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

          const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
          expect(isDeliveryStep, 'Delivery Step boc is not displayed').to.eq(true);
        });

        it('should choose the shipping method', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'shippingMethodStep', baseContext);

          const isPaymentStep = await foClassicCheckoutPage.goToPaymentStep(page);
          expect(isPaymentStep, 'Payment Step bloc is not displayed').to.eq(true);
        });

        it('should choose the payment type and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

          await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

          const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
          // Check the confirmation message
          expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should click on the logo of the shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

          await foClassicHomePage.clickOnHeaderLink(page, 'Logo');

          const pageTitle = await foClassicHomePage.getPageTitle(page);
          expect(pageTitle).to.equal(foClassicHomePage.pageTitle);
        });
      }
      if (test.args.testIdentifier === 'cartRuleNotAccepted') {
        it('should search for the same created voucher and check the error message', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'searchExistingVoucher', baseContext);

          const voucherErrorText = await foClassicCartPage.getCartRuleErrorMessage(page);
          expect(voucherErrorText).to.equal(foClassicCartPage.cartRuleAlreadyUsedErrorText);
        });
      }
    });
  });

  // Post-condition : Delete the created cart rule
  deleteCartRuleTest(cartRuleCode.name, `${baseContext}_postTest`);
});
