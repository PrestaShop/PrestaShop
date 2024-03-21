// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import homePage from '@pages/FO/hummingbird/home';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

// Import data
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';
import {dataCustomers} from '@prestashop-core/ui-testing';
import Carriers from '@data/demo/carriers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

describe('FO - Checkout : Display of totals', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create cart rule with code
  const cartRuleWithCodeData: CartRuleData = new CartRuleData({
    name: 'kdo',
    code: '1234',
    highlight: true,
    quantityPerUser: 100,
    quantity: 100,
    discountType: 'Amount',
    discountAmount: {
      value: 5,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_1`);

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, `${baseContext}_preTest_2`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Display total', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it(`should search for the product ${Products.demo_12.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, Products.demo_12.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should add the product to cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should check the displayed promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPromoCodeBlock2', baseContext);

      const promoCode = await cartPage.getHighlightPromoCode(page);
      expect(promoCode).to.equal(`${cartRuleWithCodeData.code} - ${cartRuleWithCodeData.name}`);
    });

    it('should click on the promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPromoCode2', baseContext);

      await cartPage.clickOnPromoCode(page);

      const cartRuleName = await cartPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.equal(cartRuleWithCodeData.name);
    });

    it('should verify the total after the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount3', baseContext);

      const totalAfterPromoCode: number = Products.demo_12.finalPrice - cartRuleWithCodeData.discountAmount!.value;

      const priceATI = await cartPage.getATIPrice(page);
      expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

      const discountValue = await cartPage.getDiscountValue(page, 1);
      expect(discountValue).to.equal(-cartRuleWithCodeData.discountAmount!.value);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected!').to.equal(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should select the first carrier and check the shipping price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await checkoutPage.chooseShippingMethod(page, Carriers.myCarrier.id);

      const shippingCost = await checkoutPage.getShippingCost(page);
      expect(shippingCost).to.equal(`€${Carriers.myCarrier.priceTTC.toFixed(2)}`);
    });

    it('should check the cart rule name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartRuleName', baseContext);

      const cartRuleName = await checkoutPage.getCartRuleName(page, 1);
      expect(cartRuleName).to.equal(cartRuleWithCodeData.name);
    });

    it('should check the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const totalAfterDiscount = await checkoutPage.getATIPrice(page);
      expect(totalAfterDiscount.toFixed(2))
        .to.equal((Products.demo_12.price - cartRuleWithCodeData.discountAmount!.value + Carriers.myCarrier.priceTTC).toFixed(2));
    });

    it('should remove the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount', baseContext);

      const isDeleteIconNotVisible = await checkoutPage.removePromoCode(page);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);

  // Post-condition: Delete created cart rule
  deleteCartRuleTest(cartRuleWithCodeData.name, `${baseContext}_postTest_2`);
});
