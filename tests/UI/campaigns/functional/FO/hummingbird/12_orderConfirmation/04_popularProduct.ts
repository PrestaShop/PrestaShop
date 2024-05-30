// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import cartPage from '@pages/FO/hummingbird/cart';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import orderConfirmationPage from '@pages/FO/hummingbird/checkout/orderConfirmation';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';
import categoryPage from '@pages/FO/hummingbird/category';

import {
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_hummingbird_orderConfirmation_popularProduct';

describe('FO - Order confirmation : Popular product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check Popular product block', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const result = await homePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await homePage.goToHomePage(page);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should add the product ${dataProducts.demo_6.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo3ByQuickView', baseContext);

      await homePage.searchProduct(page, dataProducts.demo_6.name);
      await searchResultsPage.quickViewProduct(page, 1);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
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

    it('should select the first carrier and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await checkoutPage.chooseShippingMethod(page, dataCarriers.clickAndCollect.id);

      const isPaymentStep = await checkoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by check and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.checkPayment.moduleName);

      const pageTitle = await orderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should check popular product title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProducts', baseContext);

      const popularProductTitle = await orderConfirmationPage.getBlockTitle(page);
      expect(popularProductTitle).to.equal('Popular Products');
    });

    it('should check the number of popular products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProductsNumber', baseContext);

      const productsNumber = await orderConfirmationPage.getProductsBlockNumber(page);
      expect(productsNumber).to.equal(8);
    });

    it('should quick view the first product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewFirstProduct', baseContext);

      await orderConfirmationPage.quickViewProduct(page, 1);

      const isQuickViewModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isQuickViewModalVisible).to.equal(true);
    });

    it('should add the product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.eq(cartPage.pageTitle);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should go to delivery address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

      const isDeliveryStep = await checkoutPage.goToDeliveryStep(page);
      expect(isDeliveryStep, 'Delivery Step boc is not displayed').to.eq(true);
    });

    it('should choose the shipping method', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'shippingMethodStep', baseContext);

      const isPaymentStep = await checkoutPage.goToPaymentStep(page);
      expect(isPaymentStep, 'Payment Step bloc is not displayed').to.eq(true);
    });

    it('should choose the payment type and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      // Check the confirmation message
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should click on all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnAllProductsPage', baseContext);

      await orderConfirmationPage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
