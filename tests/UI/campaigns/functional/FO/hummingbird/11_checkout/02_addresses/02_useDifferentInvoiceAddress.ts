// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import productPage from '@pages/FO/hummingbird/product';
import cartPage from '@pages/FO/hummingbird/cart';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import orderConfirmationPage from '@pages/FO/hummingbird/checkout/orderConfirmation';

import {
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_checkout_addresses_useDifferentInvoiceAddress';

/*
Pre-condition:
- Install the theme hummingbird
Scenario:
- Go to FO
- Add product to cart
- Go to checkout page
- Choose to order as guest
- Add guest information
- Add delivery address
- Click on Use another address for invoice
- Fill a second form address
- Finish the order
Post-condition:
- Uninstall the theme hummingbird
*/
describe('FO - Checkout - Addresses: Use different invoice address', async () => {
  // Create faker data
  const guestData: FakerCustomer = new FakerCustomer({password: ''});
  const deliveryAddress: FakerAddress = new FakerAddress({country: 'France'});
  const invoiceAddress: FakerAddress = new FakerAddress({country: 'France'});

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

  describe('Create new order with different invoice address', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should go to fourth product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.goToProductPage(page, 4);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_5.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await productPage.addProductToTheCart(page, 1);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should fill customer information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

      const isStepCompleted = await checkoutPage.setGuestPersonalInformation(page, guestData);
      expect(isStepCompleted).to.equal(true);
    });

    // @todo : https://github.com/PrestaShop/hummingbird/issues/614
    it('should fill different delivery and invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerAddresses', baseContext);

      this.skip();

      const isStepCompleted = await checkoutPage.setAddress(page, deliveryAddress, invoiceAddress);
      expect(isStepCompleted).to.equal(true);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeTheOrder', baseContext);

      this.skip();

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
