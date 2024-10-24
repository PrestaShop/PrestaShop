// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
// Import FO pages
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {
  type BrowserContext,
  dataCustomers,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_checkout_addresses_billingAdressWhenLoggedIn';

describe('FO - Guest checkout: Billing address when logged in', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest_0`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Make an order with 2 different addresses for delivery and invoice', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // Go to FO
      await foHummingbirdHomePage.goToFo(page);

      // Change FO language
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartAndCheckout', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 3);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.eq(foHummingbirdCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected).to.eq(true);

      const isAddressesStep = await foHummingbirdCheckoutPage.isAddressesStep(page);
      expect(isAddressesStep).to.eq(true);

      const isDeliveryAddressSelected = await foHummingbirdCheckoutPage.isDeliveryAddressSelected(page, 1);
      expect(isDeliveryAddressSelected).to.equal(true);

      const addressesNumber = await foHummingbirdCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber).to.equal(2);
    });

    it('should click on \'Billing address differs from shipping address\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBillingAddressDifferent', baseContext);

      await foHummingbirdCheckoutPage.clickOnDifferentInvoiceAddressLink(page);

      const isInvoiceAddressBlockVisible = await foHummingbirdCheckoutPage.isInvoiceAddressBlockVisible(page);
      expect(isInvoiceAddressBlockVisible).to.eq(true);

      const addressesNumber = await foHummingbirdCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber).to.equal(2);

      const invoiceAddressesNumber = await foHummingbirdCheckoutPage.getNumberOfInvoiceAddresses(page);
      expect(invoiceAddressesNumber).to.equal(2);

      const isInvoiceAddress1Selected = await foHummingbirdCheckoutPage.isInvoiceAddressSelected(page, 1);
      expect(isInvoiceAddress1Selected).to.equal(true);

      const isInvoiceAddress2Selected = await foHummingbirdCheckoutPage.isInvoiceAddressSelected(page, 2);
      expect(isInvoiceAddress2Selected).to.equal(false);
    });

    it('should choose the invoice address different than shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'invoiceAddressDiffShippingAddress', baseContext);

      await foHummingbirdCheckoutPage.selectInvoiceAddress(page, 2);

      const isInvoiceAddress1Selected = await foHummingbirdCheckoutPage.isInvoiceAddressSelected(page, 1);
      expect(isInvoiceAddress1Selected).to.equal(false);

      const isInvoiceAddress2Selected = await foHummingbirdCheckoutPage.isInvoiceAddressSelected(page, 2);
      expect(isInvoiceAddress2Selected).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest_1`);
});
