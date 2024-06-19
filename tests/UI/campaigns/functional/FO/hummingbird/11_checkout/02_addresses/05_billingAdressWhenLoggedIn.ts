// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
// Import FO pages
import foHomePage from '@pages/FO/hummingbird/home';
import cartPage from '@pages/FO/hummingbird/cart';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {
  dataCustomers,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_checkout_addresses_billingAdressWhenLoggedIn';

describe('FO - Guest checkout: Billing address when logged in', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_0`);

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
      await foHomePage.goToFo(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCartAndCheckout', baseContext);

      await foHomePage.quickViewProduct(page, 3);
      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.eq(cartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected).to.eq(true);

      const isAddressesStep = await checkoutPage.isAddressesStep(page);
      expect(isAddressesStep).to.eq(true);

      const isDeliveryAddressSelected = await checkoutPage.isDeliveryAddressSelected(page, 1);
      expect(isDeliveryAddressSelected).to.equal(true);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber).to.equal(2);
    });

    it('should click on \'Billing address differs from shipping address\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBillingAddressDifferent', baseContext);

      await checkoutPage.clickOnDifferentInvoiceAddressLink(page);

      const isInvoiceAddressBlockVisible = await checkoutPage.isInvoiceAddressBlockVisible(page);
      expect(isInvoiceAddressBlockVisible).to.eq(true);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber).to.equal(2);

      const invoiceAddressesNumber = await checkoutPage.getNumberOfInvoiceAddresses(page);
      expect(invoiceAddressesNumber).to.equal(2);

      const isInvoiceAddress1Selected = await checkoutPage.isInvoiceAddressSelected(page, 1);
      expect(isInvoiceAddress1Selected).to.equal(true);

      const isInvoiceAddress2Selected = await checkoutPage.isInvoiceAddressSelected(page, 2);
      expect(isInvoiceAddress2Selected).to.equal(false);
    });

    it('should choose the invoice address different than shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'invoiceAddressDiffShippingAddress', baseContext);

      await checkoutPage.selectInvoiceAddress(page, 2);

      const isInvoiceAddress1Selected = await checkoutPage.isInvoiceAddressSelected(page, 1);
      expect(isInvoiceAddress1Selected).to.equal(false);

      const isInvoiceAddress2Selected = await checkoutPage.isInvoiceAddressSelected(page, 2);
      expect(isInvoiceAddress2Selected).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);
});
