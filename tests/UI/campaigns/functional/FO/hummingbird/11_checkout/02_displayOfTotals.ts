import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataProducts,
  FakerCartRule,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_checkout_displayOfTotals';

/*
Pre-condition:
- Install the theme hummingbird
- Create new cart rule
Scenario:
- Add product to cart
- Click on created promo code
- Check cart details
- Proceed to checkout
- Choose carrier and check details
Post-condition:
- Uninstall the theme hummingbird
- Delete created cart rule
 */

describe('FO - Checkout : Display of total (price, vouchers, shipping)', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');

  // Data to create cart rule with code
  const cartRuleWithCodeData: FakerCartRule = new FakerCartRule({
    name: 'kdo',
    code: '1234',
    highlight: true,
    dateFrom: pastDate,
    quantityPerUser: 100,
    quantity: 100,
    discountType: 'Amount',
    discountAmount: {
      value: 5,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });
  const cartRuleDiscount: number = parseFloat(cartRuleWithCodeData.discountAmount!.value.toString());

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, `${baseContext}_preTest_1`);

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_2`);

  describe('Display total', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in with created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it(`should search for the product ${dataProducts.demo_12.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_12.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should add the product to cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check the displayed promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPromoCodeBlock2', baseContext);

      const promoCode = await foHummingbirdCartPage.getHighlightPromoCode(page);
      expect(promoCode).to.equal(`${cartRuleWithCodeData.code} - ${cartRuleWithCodeData.name}`);
    });

    it('should click on the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode2', baseContext);

      await foHummingbirdCartPage.clickOnPromoCode(page);

      const cartRuleName = await foHummingbirdCartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(cartRuleWithCodeData.name);
    });

    it('should verify the total after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount3', baseContext);

      const totalAfterPromoCode: number = dataProducts.demo_12.finalPrice - cartRuleDiscount;

      const priceATI = await foHummingbirdCartPage.getATIPrice(page);
      expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

      const discountValue = await foHummingbirdCartPage.getCartRuleValue(page, 1);
      expect(discountValue).to.contains(`-€${cartRuleDiscount.toFixed(2)}`);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should select the first carrier and check the shipping price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await foHummingbirdCheckoutPage.chooseShippingMethod(page, dataCarriers.myCarrier.id);

      const shippingCost = await foHummingbirdCheckoutPage.getShippingCost(page);
      expect(shippingCost).to.equal(`€${dataCarriers.myCarrier.priceTTC.toFixed(2)}`);
    });

    it('should check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleName', baseContext);

      const cartRuleName = await foHummingbirdCheckoutPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.contains(cartRuleWithCodeData.name);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const totalAfterDiscount = await foHummingbirdCheckoutPage.getATIPrice(page);
      expect(totalAfterDiscount.toFixed(2))
        .to.equal(
          (dataProducts.demo_12.price - cartRuleDiscount + dataCarriers.myCarrier.priceTTC).toFixed(2),
        );
    });

    it('should remove the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount', baseContext);

      const isDeleteIconNotVisible = await foHummingbirdCheckoutPage.removePromoCode(page);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_1`);

  // Post-condition: Delete created cart rule
  deleteCartRuleTest(cartRuleWithCodeData.name, `${baseContext}_postTest_2`);
});
