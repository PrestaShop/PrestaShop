import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/hummingbird/order';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyOrderDetailsPage,
  foHummingbirdMyOrderHistoryPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_userAccount_orderHistory_orderDetails_reorderFromOrderDetails';

/*
Pre-condition:
- Install hummingbird
- Create order by default customer
Scenario:
- Go to userAccount > order history > order detail
- Click on the reorder link
- Proceed checkout
- Go back to the order list
- Check if the reorder is displayed
- Go to the order detail
- Check if the reorder contain the same product as the "original" order
Post-condition:
- Uninstall hummingbird
 */
describe('FO - User Account - Order History - Order details : Reorder from order detail', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const orderData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_1`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Go to order detail and proceed reorder', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage: boolean = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foHummingbirdMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foHummingbirdMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foHummingbirdMyOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await foHummingbirdMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyOrderDetailsPage.pageTitle);
    });

    it('should click on reorder link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnReorderLink', baseContext);

      await foHummingbirdMyOrderDetailsPage.clickOnReorderLink(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStepForReorder', baseContext);

      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should validate Step Delivery and go to Payment Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStepForReorder', baseContext);

      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmReorder', baseContext);

      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const pageTitle = await foHummingbirdCheckoutOrderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCheckoutOrderConfirmationPage.pageTitle);

      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Go to new order detail and check content', async () => {
    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToAccountPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go back to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrderHistoryPage', baseContext);

      await foHummingbirdMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foHummingbirdMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFoToOrderDetails', baseContext);

      await foHummingbirdMyOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await foHummingbirdMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyOrderDetailsPage.pageTitle);
    });

    it('should check the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTheOrderedProduct', baseContext);

      const orderedProduct = await foHummingbirdMyOrderDetailsPage.getProductName(page, 1, 2);
      expect(orderedProduct).to.contain(dataProducts.demo_1.name);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foHummingbirdCheckoutOrderConfirmationPage.logout(page);

      const isCustomerConnected = await foHummingbirdCheckoutOrderConfirmationPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
