// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create new order with different invoice address', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should go to fourth product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 4);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_5.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should fill customer information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

      const isStepCompleted = await foHummingbirdCheckoutPage.setGuestPersonalInformation(page, guestData);
      expect(isStepCompleted).to.equal(true);
    });

    // @todo : https://github.com/PrestaShop/hummingbird/issues/614
    it('should fill different delivery and invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerAddresses', baseContext);

      this.skip();

      const isStepCompleted = await foHummingbirdCheckoutPage.setAddress(page, deliveryAddress, invoiceAddress);
      expect(isStepCompleted).to.equal(true);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeTheOrder', baseContext);

      this.skip();

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

      // Payment step - Choose payment step
      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
