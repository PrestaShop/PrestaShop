// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import deleteCacheTest from '@commonTests/BO/advancedParameters/cache';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
// Import BO pages
// Import FO pages
import aboutUsPage from '@pages/FO/hummingbird/aboutUs';
import bestSalesPage from '@pages/FO/hummingbird/bestSales';
import contactUsPage from '@pages/FO/hummingbird/contactUs';
import deliveryPage from '@pages/FO/hummingbird/delivery';
import homePage from '@pages/FO/hummingbird/home';
import legalNoticePage from '@pages/FO/hummingbird/legalNotice';
import loginPage from '@pages/FO/hummingbird/login';
import createAccountPage from '@pages/FO/hummingbird/myAccount/add';
import addAddressPage from '@pages/FO/hummingbird/myAccount/addAddress';
import addressesPage from '@pages/FO/hummingbird/myAccount/addresses';
import creditSlipsPage from '@pages/FO/hummingbird/myAccount/creditSlips';
import personalInfoPage from '@pages/FO/hummingbird/myAccount/identity';
import myWishlistPage from '@pages/FO/hummingbird/myAccount/myWishlists';
import ordersPage from '@pages/FO/hummingbird/myAccount/orderHistory';
import guestOrderTrackingPage from '@pages/FO/hummingbird/orderTracking/guestOrderTracking';
import newProductsPage from '@pages/FO/hummingbird/newProducts';
import pricesDropPage from '@pages/FO/hummingbird/pricesDrop';
import securePaymentPage from '@pages/FO/hummingbird/securePayment';
import siteMapPage from '@pages/FO/hummingbird/siteMap';
import storesPage from '@pages/FO/hummingbird/stores';
import termsAndConditionsOfUsePage from '@pages/FO/hummingbird/termsAndConditionsOfUse';

// Import data
import CustomerData from '@data/faker/customer';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
  const createCustomerData: CustomerData = new CustomerData();

  // Pre-condition: Create new account on FO
  createAccountTest(createCustomerData, `${baseContext}_preTest_1`);

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_2`);

  // Pre-condition: Delete cache
  deleteCacheTest(`${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
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
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await homePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Our Company\' footer links', async () => {
    [
      {linkSelector: 'Delivery', pageTitle: deliveryPage.pageTitle},
      {linkSelector: 'Legal Notice', pageTitle: legalNoticePage.pageTitle},
      {linkSelector: 'Terms and conditions of use', pageTitle: termsAndConditionsOfUsePage.pageTitle},
      {linkSelector: 'About us', pageTitle: aboutUsPage.pageTitle},
      {linkSelector: 'Secure payment', pageTitle: securePaymentPage.pageTitle},
      {linkSelector: 'Contact us', pageTitle: contactUsPage.pageTitle},
      {linkSelector: 'Sitemap', pageTitle: siteMapPage.pageTitle},
      {linkSelector: 'Stores', pageTitle: storesPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOurCompanyFooterLinks${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await homePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links before login', async () => {
    [
      {linkSelector: 'Order tracking', pageTitle: guestOrderTrackingPage.pageTitle},
      {linkSelector: 'Sign in', pageTitle: loginPage.pageTitle},
      {linkSelector: 'Create account', pageTitle: createAccountPage.formTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks1${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Create account') {
          pageTitle = await createAccountPage.getHeaderTitle(page);
        } else {
          pageTitle = await homePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links after login with default customer', async () => {
    it('should login to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await homePage.goToLoginPage(page);
      await loginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.equal(true);
    });

    [
      {linkSelector: 'Information', pageTitle: personalInfoPage.pageTitle},
      {linkSelector: 'Addresses', pageTitle: addressesPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: ordersPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: creditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: myWishlistPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: loginPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks2${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await myWishlistPage.getPageTitle(page);
        } else {
          pageTitle = await homePage.getPageTitle(page);
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

      await homePage.goToLoginPage(page);
      await loginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.equal(true);
    });

    [
      {linkSelector: 'Information', pageTitle: personalInfoPage.pageTitle},
      {linkSelector: 'Add first address', pageTitle: addAddressPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: ordersPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: creditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: myWishlistPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: loginPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks3${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await myWishlistPage.getPageTitle(page);
        } else {
          pageTitle = await homePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Store Information\'', async () => {
    it('should check \'Store Information\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStoreInformation', baseContext);

      const storeInformation = await homePage.getStoreInformation(page);
      expect(storeInformation).to.contains(global.INSTALL.SHOP_NAME)
        .and.to.contain(global.INSTALL.COUNTRY)
        .and.to.contains(global.BO.EMAIL);
    });
  });

  describe('Check the copyright', async () => {
    it('should check the copyright', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCopyright', baseContext);

      const copyright = await homePage.getCopyright(page);
      expect(copyright).to.equal(`© ${currentYear} - Ecommerce software by PrestaShop™`);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);

  // Post-condition: Delete the created customer account
  deleteCustomerTest(createCustomerData, `${baseContext}_postTest_2`);
});
