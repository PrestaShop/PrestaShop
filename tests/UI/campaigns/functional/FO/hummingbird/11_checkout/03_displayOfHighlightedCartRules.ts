// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';
import {createCartRuleTest, deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import homePage from '@pages/FO/hummingbird/home';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';
import foLoginPage from '@pages/FO/hummingbird/login';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
  FakerCartRule,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_checkout_displayOfHighlightedCartRules';

/*
Pre-condition:
- Install the theme hummingbird
- Create new cart rule
Scenario:
- Add product to cart
- Click on promo code and click on apply
- Delete promo code
Post-condition:
- Uninstall the theme hummingbird
- Delete created cart rule
 */

describe('FO - Checkout : Display of highlighted cart rule', async () => {
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

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, `${baseContext}_preTest_1`);

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_2`);

  describe('Display of highlighted promo code', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it(`should search for the product ${dataProducts.demo_6.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, dataProducts.demo_6.name);

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
      await testContext.addContextItem(this, 'testIdentifier', 'checkPromoCodeBlock', baseContext);

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

      const totalAfterPromoCode: number = dataProducts.demo_6.combinations[0].price - cartRuleWithCodeData.discountAmount!.value;

      const priceATI = await cartPage.getATIPrice(page);
      expect(priceATI).to.equal(parseFloat(totalAfterPromoCode.toFixed(2)));

      const discountValue = await cartPage.getDiscountValue(page, 1);
      expect(discountValue).to.equal(-cartRuleWithCodeData.discountAmount!.value);
    });

    it('should remove the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeTheDiscount', baseContext);

      const isDeleteIconNotVisible = await cartPage.removeVoucher(page);
      expect(isDeleteIconNotVisible, 'The discount is not removed').to.equal(true);
    });

    it('should check the displayed promo code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPromoCodeBlock2', baseContext);

      const promoCode = await cartPage.getHighlightPromoCode(page);
      expect(promoCode).to.equal(`${cartRuleWithCodeData.code} - ${cartRuleWithCodeData.name}`);
    });

    it('should verify the total without discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalWithoutDiscount', baseContext);

      const priceATI = await cartPage.getATIPrice(page);
      expect(priceATI).to.equal(dataProducts.demo_6.combinations[0].price);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);

  // Post-condition: Delete created cart rule
  deleteCartRuleTest(cartRuleWithCodeData.name, `${baseContext}_postTest_2`);
});
