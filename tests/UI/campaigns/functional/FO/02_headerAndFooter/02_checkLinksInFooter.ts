// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCacheTest} from '@commonTests/BO/advancedParameters/deleteCache';
import {deleteCustomerTest} from '@commonTests/BO/customers/createDeleteCustomer';
import {createAccountTest} from '@commonTests/FO/createAccount';

// Import pages
// Import BO pages
// Import FO pages
import aboutUsPage from '@pages/FO/aboutUs';
import bestSalesPage from '@pages/FO/bestSales';
import contactUsPage from '@pages/FO/contactUs';
import deliveryPage from '@pages/FO/delivery';
import homePage from '@pages/FO/home';
import legalNoticePage from '@pages/FO/legalNotice';
import loginPage from '@pages/FO/login';
import createAccountPage from '@pages/FO/myAccount/add';
import addAddressPage from '@pages/FO/myAccount/addAddress';
import addressesPage from '@pages/FO/myAccount/addresses';
import creditSlipsPage from '@pages/FO/myAccount/creditSlips';
import personalInfoPage from '@pages/FO/myAccount/identity';
import myWishlistPage from '@pages/FO/myAccount/myWishlists';
import ordersPage from '@pages/FO/myAccount/orderHistory';
import guestOrderTrackingPage from '@pages/FO/orderTracking/guestOrderTracking';
import newProductsPage from '@pages/FO/newProducts';
import pricesDropPage from '@pages/FO/pricesDrop';
import securePaymentPage from '@pages/FO/securePayment';
import siteMapPage from '@pages/FO/siteMap';
import storesPage from '@pages/FO/stores';
import termsAndConditionsOfUsePage from '@pages/FO/termsAndConditionsOfUse';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import CustomerFaker from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext = 'functional_FO_headerAndFooter_checkLinksInFooter';

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
  const createCustomerData: CustomerFaker = new CustomerFaker();

  // Pre-condition: Create new account on FO
  createAccountTest(createCustomerData, `${baseContext}_preTest1`);

  // Pre-condition: Delete cache
  deleteCacheTest(`${baseContext}_preTest2`);

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
    await expect(isHomePage).to.be.true;
  });

  describe('Check \'Products\' footer links', async () => {
    [
      {linkSelector: 'Prices drop', pageTitle: pricesDropPage.pageTitle},
      {linkSelector: 'New products', pageTitle: newProductsPage.pageTitle},
      {linkSelector: 'Best sellers', pageTitle: bestSalesPage.pageTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductsFooterLinks${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await homePage.getPageTitle(page);
        await expect(pageTitle).to.equal(args.pageTitle);
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
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOurCompanyFooterLinks${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await homePage.getPageTitle(page);
        await expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links before login', async () => {
    [
      {linkSelector: 'Order tracking', pageTitle: guestOrderTrackingPage.pageTitle},
      {linkSelector: 'Sign in', pageTitle: loginPage.pageTitle},
      {linkSelector: 'Create account', pageTitle: createAccountPage.formTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks1${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Create account') {
          pageTitle = await createAccountPage.getHeaderTitle(page);
        } else {
          pageTitle = await homePage.getPageTitle(page);
        }
        await expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links after login with default customer', async () => {
    it('should login to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await homePage.goToLoginPage(page);
      await loginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    [
      {linkSelector: 'Information', pageTitle: personalInfoPage.pageTitle},
      {linkSelector: 'Addresses', pageTitle: addressesPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: ordersPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: creditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: myWishlistPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: loginPage.pageTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks2${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await myWishlistPage.getPageTitle(page);
        } else {
          pageTitle = await homePage.getPageTitle(page);
        }
        await expect(pageTitle).to.equal(args.pageTitle);
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
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    [
      {linkSelector: 'Information', pageTitle: personalInfoPage.pageTitle},
      {linkSelector: 'Add first address', pageTitle: addAddressPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: ordersPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: creditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: myWishlistPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: loginPage.pageTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks3${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await myWishlistPage.getPageTitle(page);
        } else {
          pageTitle = await homePage.getPageTitle(page);
        }
        await expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Store Information\'', async () => {
    it('should check \'Store Information\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStoreInformation', baseContext);

      const storeInformation = await homePage.getStoreInformation(page);
      await expect(storeInformation).to.contains(global.INSTALL.SHOP_NAME)
        .and.to.contain(global.INSTALL.COUNTRY)
        .and.to.contains(global.BO.EMAIL);
    });
  });

  describe('Check the copyright', async () => {
    it('should check the copyright', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCopyright', baseContext);

      const copyright = await homePage.getCopyright(page);
      await expect(copyright).to.equal(`© ${currentYear} - Ecommerce software by PrestaShop™`);
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(createCustomerData, `${baseContext}_postTest`);
});
