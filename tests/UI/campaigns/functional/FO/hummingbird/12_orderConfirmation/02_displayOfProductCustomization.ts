// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

// context
const baseContext: string = 'functional_FO_hummingbird_orderConfirmation_displayOfProductCustomization';

/*
Pre-condition:
- Install the theme hummingbird
Scenario:
- Add product with customization to cart
- Proceed to checkout and confirm the order
- Check the payment confirmation details
- Check the customization modal
Post-condition:
- Uninstall the theme hummingbird
*/
describe('FO - Order confirmation : Display of product customization', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create new order in FO', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHummingbirdHomePage.goToHomePage(page);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should search for the product ${dataProducts.demo_14.name} and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foHummingbirdHomePage.setProductNameInSearchInput(page, dataProducts.demo_14.name);
      await foHummingbirdHomePage.clickAutocompleteSearchResult(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_14.name);
    });

    it('should add custom text and add the product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, 1, undefined, true, 'Hello world!');

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected!').to.equal(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should select the first carrier and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await foHummingbirdCheckoutPage.chooseShippingMethod(page, dataCarriers.myCarrier.id);

      const isPaymentStep = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const pageTitle = await foHummingbirdCheckoutOrderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCheckoutOrderConfirmationPage.pageTitle);

      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Check list of ordered products', async () => {
    it('should check the payment information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentInformation', baseContext);

      const totalToPay: string = (dataProducts.demo_14.finalPrice + dataCarriers.myCarrier.priceTTC).toFixed(2);

      const paymentInformation = await foHummingbirdCheckoutOrderConfirmationPage.getPaymentInformation(page);
      expect(paymentInformation).to.contains('You have chosen payment by '
        + `${dataPaymentMethods.wirePayment.displayName.toLowerCase()}`)
        .and.to.contains(`Amount €${totalToPay}`);
    });

    it('should check the order details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderDetails', baseContext);

      const orderDetails = await foHummingbirdCheckoutOrderConfirmationPage.getOrderDetails(page);
      expect(orderDetails).to.contains(`${dataPaymentMethods.wirePayment.displayName} Shipping method: `
        + `${dataCarriers.myCarrier.name} - ${dataCarriers.myCarrier.transitName}`);
    });

    it('should check the products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber', baseContext);

      const productsNumber = await foHummingbirdCheckoutOrderConfirmationPage.getNumberOfProducts(page);
      expect(productsNumber).to.equal(1);
    });

    it('should check the details of the first product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

      const result = await foHummingbirdCheckoutOrderConfirmationPage.getProductDetailsInRow(page, 1);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_14.coverImage),
        expect(result.details).to.contains(dataProducts.demo_14.name),
        expect(result.details).to.contains(`Reference: ${dataProducts.demo_14.reference}`),
        expect(result.details).to.contains('Product customization Type your text here Hello world!'),
        expect(result.prices).to.equal(`€${dataProducts.demo_14.finalPrice}`),
      ]);
    });

    it('should click on the button Customized and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnCustomizedProduct', baseContext);

      const isModalVisible = await foHummingbirdCheckoutOrderConfirmationPage.clickOnCustomizedButton(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should check the modal content', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModalContent', baseContext);

      const modalContent = await foHummingbirdCheckoutOrderConfirmationPage.getModalProductCustomizationContent(page);
      expect(modalContent).to.equal('Type your text here Hello world!');
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await foHummingbirdCheckoutOrderConfirmationPage.closeModalProductCustomization(page);
      expect(isModalNotVisible).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
