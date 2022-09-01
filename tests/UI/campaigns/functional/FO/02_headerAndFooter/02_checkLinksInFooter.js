require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import common tests
const {deleteCacheTest} = require('@commonTests/BO/advancedParameters/deleteCache');
const {createAccountTest} = require('@commonTests/FO/createAccount');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Importing FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const pricesDropPage = require('@pages/FO/pricesDrop');
const newProductsPage = require('@pages/FO/newProducts');
const bestSalesPage = require('@pages/FO/bestSales');
const deliveryPage = require('@pages/FO/delivery');
const legalNoticePage = require('@pages/FO/legalNotice');
const termsAndConditionsOfUsePage = require('@pages/FO/termsAndConditionsOfUse');
const aboutUsPage = require('@pages/FO/aboutUs');
const securePaymentPage = require('@pages/FO/securePayment');
const contactUsPage = require('@pages/FO/contactUs');
const siteMapPage = require('@pages/FO/siteMap');
const storesPage = require('@pages/FO/stores');
const personalInfoPage = require('@pages/FO/myAccount/identity');
const ordersPage = require('@pages/FO/myAccount/orderHistory');
const creditSlipsPage = require('@pages/FO/myAccount/creditSlips');
const addressesPage = require('@pages/FO/myAccount/addresses');
const addAddressPage = require('@pages/FO/myAccount/addAddress');
const createAccountPage = require('@pages/FO/myAccount/add');
const guestOrderTrackingPage = require('@pages/FO/orderTracking/guestOrderTracking');
const myWishlistPage = require('@pages/FO/myAccount/myWishlists');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');

// Import faker data
const CustomerFaker = require('@data/faker/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_checkLinksInFooter';

let browserContext;
let page;

const today = new Date();
const currentYear = today.getFullYear().toString();

const createCustomerData = new CustomerFaker();

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
      {linkSelector: 'Best sales', pageTitle: bestSalesPage.pageTitle},
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

        let pageTitle;
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

        let pageTitle = '';
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

        let pageTitle = '';
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
