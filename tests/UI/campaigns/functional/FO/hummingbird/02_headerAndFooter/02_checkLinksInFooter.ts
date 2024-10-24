// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import deleteCacheTest from '@commonTests/BO/advancedParameters/cache';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
// Import BO pages
// Import FO pages
import bestSalesPage from '@pages/FO/hummingbird/bestSales';
import deliveryPage from '@pages/FO/hummingbird/delivery';
import legalNoticePage from '@pages/FO/hummingbird/legalNotice';
import createAccountPage from '@pages/FO/hummingbird/myAccount/add';
import addAddressPage from '@pages/FO/hummingbird/myAccount/addAddress';
import addressesPage from '@pages/FO/hummingbird/myAccount/addresses';
import creditSlipsPage from '@pages/FO/hummingbird/myAccount/creditSlips';
import personalInfoPage from '@pages/FO/hummingbird/myAccount/identity';
import ordersPage from '@pages/FO/hummingbird/myAccount/orderHistory';
import guestOrderTrackingPage from '@pages/FO/hummingbird/orderTracking/guestOrderTracking';
import newProductsPage from '@pages/FO/hummingbird/newProducts';
import pricesDropPage from '@pages/FO/hummingbird/pricesDrop';
import securePaymentPage from '@pages/FO/hummingbird/securePayment';
import siteMapPage from '@pages/FO/hummingbird/siteMap';
import storesPage from '@pages/FO/hummingbird/stores';
import termsAndConditionsOfUsePage from '@pages/FO/hummingbird/termsAndConditionsOfUse';

import {
  type BrowserContext,
  dataCustomers,
  FakerCustomer,
  foHummingbirdAboutUsPage,
  foHummingbirdContactUsPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyWishlistsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_headerAndFooter_checkLinksInFooter';

/*
Pre-condition:
- Create new customer account
- Delete cache
Scenario:
- Go to FO
- Check footer Products links( Prices drop, New products and Best sales)
Check our company links( Delivery, Legal notices, Terms and conditions of use, About us, Secure payment, Contact us,
Sitemap, Stores)
- Check your account links( Personal info, Orders, Credit slips, Addresses)
- Check store information
- Check copyright
Post-condition:
- Delete created customer
 */
describe('FO - Header and Footer : Check links in footer page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let pageTitle: string;

  const today: Date = new Date();
  const currentYear: string = today.getFullYear().toString();
  const createCustomerData: FakerCustomer = new FakerCustomer();

  // Pre-condition: Create new account on FO
  createAccountTest(createCustomerData, `${baseContext}_preTest_1`);

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest_2`);

  // Pre-condition: Delete cache
  deleteCacheTest(`${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foHummingbirdHomePage.goToFo(page);

    const isHomePage = await foHummingbirdHomePage.isHomePage(page);
    expect(isHomePage).to.be.eq(true);
  });

  describe('Check \'Products\' footer links', async () => {
    [
      {linkSelector: 'Prices drop', pageTitle: pricesDropPage.pageTitle},
      {linkSelector: 'New products', pageTitle: newProductsPage.pageTitle},
      {linkSelector: 'Best sellers', pageTitle: bestSalesPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductsFooterLinks${index}`, baseContext);

        // Check prices drop link
        await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Our Company\' footer links', async () => {
    [
      {linkSelector: 'Delivery', pageTitle: deliveryPage.pageTitle},
      {linkSelector: 'Legal Notice', pageTitle: legalNoticePage.pageTitle},
      {linkSelector: 'Terms and conditions of use', pageTitle: termsAndConditionsOfUsePage.pageTitle},
      {linkSelector: 'About us', pageTitle: foHummingbirdAboutUsPage.pageTitle},
      {linkSelector: 'Secure payment', pageTitle: securePaymentPage.pageTitle},
      {linkSelector: 'Contact us', pageTitle: foHummingbirdContactUsPage.pageTitle},
      {linkSelector: 'Sitemap', pageTitle: siteMapPage.pageTitle},
      {linkSelector: 'Stores', pageTitle: storesPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOurCompanyFooterLinks${index}`, baseContext);

        // Check prices drop link
        await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links before login', async () => {
    [
      {linkSelector: 'Order tracking', pageTitle: guestOrderTrackingPage.pageTitle},
      {linkSelector: 'Sign in', pageTitle: foHummingbirdLoginPage.pageTitle},
      {linkSelector: 'Create account', pageTitle: createAccountPage.formTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks1${index}`, baseContext);

        // Check prices drop link
        await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Create account') {
          pageTitle = await createAccountPage.getHeaderTitle(page);
        } else {
          pageTitle = await foHummingbirdHomePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links after login with default customer', async () => {
    it('should login to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);
      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.equal(true);
    });

    [
      {linkSelector: 'Information', pageTitle: personalInfoPage.pageTitle},
      {linkSelector: 'Addresses', pageTitle: addressesPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: ordersPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: creditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: foHummingbirdMyWishlistsPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: foHummingbirdLoginPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks2${index}`, baseContext);

        // Check prices drop link
        await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
        } else {
          pageTitle = await foHummingbirdHomePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  // Pre-condition: Delete cache
  deleteCacheTest(`${baseContext}_preTest3`);

  describe('Check \'Your Account\' footer links after login with new customer without address', async () => {
    it('should login to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFONewCustomer', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);
      await foHummingbirdLoginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.equal(true);
    });

    [
      {linkSelector: 'Information', pageTitle: personalInfoPage.pageTitle},
      {linkSelector: 'Add first address', pageTitle: addAddressPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: ordersPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: creditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: foHummingbirdMyWishlistsPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: foHummingbirdLoginPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks3${index}`, baseContext);

        // Check prices drop link
        await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
        } else {
          pageTitle = await foHummingbirdHomePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Store Information\'', async () => {
    it('should check \'Store Information\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStoreInformation', baseContext);

      const storeInformation = await foHummingbirdHomePage.getStoreInformation(page);
      expect(storeInformation).to.contains(global.INSTALL.SHOP_NAME)
        .and.to.contain(global.INSTALL.COUNTRY)
        .and.to.contains(global.BO.EMAIL);
    });
  });

  describe('Check the copyright', async () => {
    it('should check the copyright', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCopyright', baseContext);

      const copyright = await foHummingbirdHomePage.getCopyright(page);
      expect(copyright).to.equal(`© ${currentYear} - Ecommerce software by PrestaShop™`);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest_1`);

  // Post-condition: Delete the created customer account
  deleteCustomerTest(createCustomerData, `${baseContext}_postTest_2`);
});
